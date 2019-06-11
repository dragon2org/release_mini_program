<?php
/**
 * Created by PhpStorm.
 * User: harlen
 * Date: 2019/4/24
 * Time: 11:33 PM
 */

namespace App\Services;


use App\Exceptions\UnprocessableEntityHttpException;
use App\Exceptions\WechatGatewayException;
use App\Helper\CustomLogger;
use App\Logs\MiniProgramAuthCallbackLog;
use App\Models\Component;
use App\Models\ComponentTemplate;
use App\Models\MiniProgram;
use App\Models\MiniProgramExt;
use App\Models\Release;
use App\Models\ReleaseItem;
use App\Models\Tester;
use App\ServeMessageHandlers\EventMessageHandler;
use App\ServeMessageHandlers\MiniProgramUnauthorizedEventMessageHandler;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Http\StreamResponse;
use EasyWeChat\Kernel\Messages\Message;
use EasyWeChat\OpenPlatform\Server\Guard;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Log;
use RuntimeException;

class ReleaseService
{

    /**
     * @var \App\Models\Component
     */
    protected $component;

    /**
     * @var \EasyWeChat\OpenPlatform\Application
     */
    protected $openPlatform;

    /**
     * @var \App\Models\MiniProgram
     */
    protected $miniProgram;

    /**
     * @var \EasyWeChat\MiniProgram\Application
     */
    protected $miniProgramApp;

    public function __construct(Component $component)
    {
        $this->component = $component;
        $this->setOpenPlatform();
    }

    /**
     * @return  \EasyWeChat\OpenPlatform\Application
     */
    public function setOpenPlatform()
    {
        $componentConfig = [
            'app_id' => $this->component->app_id,
            'secret' => $this->component->app_secret,
            'token' => $this->component->verify_token,
            'aes_key' => $this->component->aes_key,
        ];
        $config = array_merge(config('wechat.defaults'), $componentConfig);
        $openPlatform = Factory::openPlatform($config);

        $openPlatform['verify_ticket']->setTicket($this->component->verify_ticket);

        return $this->openPlatform = $openPlatform;
    }

    public function setMiniProgramById(int $id)
    {
        return $this->setMiniProgram(
            $miniProgram = (new MiniProgram())
                ->where('mini_program_id', $id)
                ->where('component_id', $this->component->component_id)
                ->firstOrFail()
        );
    }

    public function setMiniProgramByAppId(string $appId)
    {
        return $this->setMiniProgram(
            $miniProgram = (new MiniProgram())
                ->where('app_id', $appId)
                ->where('component_id', $this->component->component_id)
                ->firstOrFail()
        );
    }

    /**
     * @param $appId
     *
     * @return \EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Application
     * @throws UnprocessableEntityHttpException
     */
    public function setMiniProgram(MiniProgram $miniProgram)
    {
        if ($miniProgram->authorization_status !== MiniProgram::AUTHORIZATION_STATUS_AUTHORIZED) {
            throw new UnprocessableEntityHttpException(trans('小程序授权已取消'));
        }

        $this->miniProgram = $miniProgram;

        return $this->miniProgramApp = $this->openPlatform->miniProgram(
            $this->miniProgram->app_id,
            $this->miniProgram->authorizer_refresh_token);
    }


    public function __get($name)
    {
        return $this->{$name};
    }

    public function server()
    {
        $server = $this->openPlatform->server;

        $server->push(function ($message) {

        }, Guard::EVENT_AUTHORIZED);

        $server->push(function ($message) {

        }, Guard::EVENT_UPDATE_AUTHORIZED);

        $server->push(MiniProgramUnauthorizedEventMessageHandler::class, Guard::EVENT_UNAUTHORIZED);

        // 处理VERIFY_TICKET
        $server->push(function ($message) {
            Log::info('ComponentVerifyTicket:', $message);
            $this->component->updateVerifyTicket($message['ComponentVerifyTicket']);
        }, Guard::EVENT_COMPONENT_VERIFY_TICKET);

        return $server->serve();
    }

    public function miniProgramServe()
    {
        $server = $this->miniProgramApp->server;

        $server->push(EventMessageHandler::class, Message::EVENT);

        return $server->serve();
    }

    public function updateReleaseConfig($input)
    {
        $config = $this->component->extend->getReleaseConfig();

        foreach (['tests', 'domain', 'web_view_domain', 'visit_status', 'support_version'] as $key) {
            if (isset($config[$key])) {
                $oldConfig[$key] = $config[$key];
            }
        }

        $extend = $this->getComponent()->getConfig();
        $extend->config = json_encode($oldConfig, JSON_UNESCAPED_UNICODE);
        $extend->save();

        return $oldConfig;
    }

    public function getBindUri()
    {
        $callbackUrl = Route('MiniProgramBindCallback', [
            'componentAppId' => $this->component->app_id,
        ]);

        $params = [
            'inner_name' => request()->query('inner_name'),
            'inner_desc' => request()->query('inner_desc'),
            'company_id' => request()->query('company_id'),
            'redirect_uri' => request()->query('redirect_uri'),
        ];

        $callbackUrl .= '?' . http_build_query($params);

        $uri = request()->query('type') === 'mobile' ? $this->openPlatform->getMobilePreAuthorizationUrl($callbackUrl) : $this->openPlatform->getPreAuthorizationUrl($callbackUrl);
        return $uri;
    }

    public function bindCallback()
    {
        $authorization = $this->openPlatform->handleAuthorize();
        MiniProgramAuthCallbackLog::info($this->component, $authorization);

        $miniProgramAppId = $authorization['authorization_info']['authorizer_appid'];
        $authorizer = $this->openPlatform->getAuthorizer($miniProgramAppId);
        $info = $authorizer['authorizer_info'];
        if(!isset($info['MiniProgramInfo'])){
            return view('authorize_success',  ['message' => '暂不支持非小程序授权.']);
        }

        $refreshToken = $authorization['authorization_info']['authorizer_refresh_token'];

        $miniProgram = MiniProgram::firstOrNew(['app_id' => $miniProgramAppId]);
        $miniProgram->component_id = $this->component->component_id;
        $miniProgram->company_id = request()->query('company_id', 0);
        $miniProgram->inner_name = request()->query('inner_name', '');
        $miniProgram->inner_desc = request()->query('inner_desc', '');
        $miniProgram->authorizer_refresh_token = $refreshToken;

        $miniProgram->nick_name = $info['nick_name'];
        $miniProgram->head_img = $info['head_img'];
        $miniProgram->user_name = $info['user_name'];
        $miniProgram->principal_name = $info['principal_name'];
        $miniProgram->qrcode_url = $info['qrcode_url'];
        $miniProgram->desc = $info['signature'];
        $miniProgram->save();

        if ($redirectUri = request()->query('redirect_uri')) {
            return response()->redirectTo($redirectUri);
        }
        return view('authorize_success',  ['message' => '授权成功.']);
    }

    /**
     * 解析微信处理
     * @param array $data
     * @param bool $isRaw
     * @param array $allowCode
     *
     * @return array
     */
    protected function parseResponse(array $data, $isRaw = false, $allowCode = [])
    {
        if ($data['errcode'] === 0 || in_array($data['errcode'], $allowCode)) {
            if ($isRaw) return $data;
            unset($data['errmsg']);
            unset($data['errcode']);
            return $data;
        }
        if ($data['errcode'] === -1) $data['errmsg'] = '微信网关繁忙';
        throw new WechatGatewayException($data['errmsg'], $data['errcode']);
    }

    public function getDrafts()
    {
        $data = $this->parseResponse(
            $this->openPlatform->code_template->getDrafts()
        );

        $local = ComponentTemplate::withTrashed()
            ->where('component_id', $this->component->component_id)
            ->pluck('draft_id');

        return Collection::wrap($data['draft_list'] ?? [])
            ->whereNotIn('draft_id', $local)
            ->sortByDesc('create_time')
            ->values();
    }

    public function draftToTemplate($draftId)
    {
        $draftList = $this->openPlatform->code_template->getDrafts($draftId);

        $draftList = $draftList['draft_list'] ?? [];

        $draftInfo = null;
        foreach ($draftList as $item) {
            if ($item['draft_id'] === $draftId) {
                $draftInfo = $item;
                break;
            }
        }
        if (is_null($draftInfo)) {
            throw new UnprocessableEntityHttpException(trans('草稿不存在'));
        }

        if (false === strpos($draftInfo['user_desc'], '|')) {
            throw new UnprocessableEntityHttpException(trans('user_desc format \'内部版本号|内部版本描述\''));
        }

        list($version, $desc) = explode('|', $draftInfo['user_desc']);

        if (preg_match('/^[a-zA-Z]+[a-zA-Z0-9\-\_]{1,45}/', $version) === 0) {
            throw new UnprocessableEntityHttpException(trans('内部版本格式错误, 只运行字母开头，1-45位字符串(英文字母_-); $version: '. $version));
        }

        $template = ComponentTemplate::where('component_id', $this->component->component_id)
            ->where('tag', $version)->first();

        if (isset($template)) {
            throw new UnprocessableEntityHttpException(trans('内部版本已存在'));
        }

        $this->openPlatform->code_template->createFromDraft($draftId);

        $this->syncTemplate();
    }

    public function syncTemplate()
    {
        $localTemplate = ComponentTemplate::withTrashed()
            ->where('component_id', $this->component->component_id)
            ->pluck('template_id');

        collect($this->templateList())
            ->whereNotIn('template_id', $localTemplate)
            ->reject(function ($item, $key){
                if(strpos($item['user_desc'], '|') === false) return true;
            })->each(function ($item, $key){
                list($version, $desc) = explode('|', $item['user_desc']);
                $template = (new ComponentTemplate());
                $template->component_id = $this->component->component_id;
                $template->template_id = $item['template_id'];
                $template->user_version = $item['user_version'];
                $template->user_desc = $desc;
                $template->create_time = date('Y-m-d H:i:s', $item['create_time']);
                $template->tag = $version;
                $template->source_miniprogram = $item['source_miniprogram'];
                $template->source_miniprogram_appid = $item['source_miniprogram_appid'];
                $template->developer = $item['developer'];
                $template->draft_id = $item['draft_id'];
                $template->save();
            });


    }

    public function templateList()
    {
        $data = $this->parseResponse(
            $this->openPlatform->code_template->list()
        );

        return $data['template_list'] ?? [];
    }

    public function deleteTemplate($templateId)
    {
        $template = ComponentTemplate::where('template_id', $templateId)->firstOrFail();

        $this->parseResponse(
            $this->openPlatform->code_template->delete($templateId)
            , false, [85064]);

        $template->delete();

        return true;
    }

    /**
     * get binding tester
     *
     * @return array
     * @throws UnprocessableEntityHttpException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getTester()
    {
        $remoteData = $this->miniProgramApp->tester->list();
        $remote = $this->parseResponse($remoteData)['members'] ?? [];

        $items = $this->miniProgram->tester()->select(['wechat_id', 'userstr'])->get()->toArray();

        $localUserStr = Arr::pluck($items, 'userstr');
        $remoteUserStr = Arr::pluck($remote, 'userstr');

        //微信服务器已经绑定，本地没有数据的
        $diff = array_diff($remoteUserStr, $localUserStr);

        foreach ($diff as $item) {
            $items[] = [
                'userstr' => $item,
                'wechat_id' => '',
            ];
        }

        return $items;
    }

    /**
     * bind tester
     *
     * @param $wechatId
     *
     * @return array
     * @throws UnprocessableEntityHttpException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function bindTester($wechatId)
    {
        $response = $this->parseResponse(
            $this->miniProgramApp->tester->bind($wechatId)
        );

        $fill = [
            'wechat_id' => $wechatId,
            'userstr' => $response['userstr'],
        ];
        $tester = new Tester();
        $tester->fill($fill);
        $tester->mini_program_id = $this->miniProgram->mini_program_id;
        $tester->save();

        return $fill;
    }

    /**
     * unbind tester
     *
     * @param $userStr
     *
     * @return bool
     * @throws UnprocessableEntityHttpException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function unbindTester($userStr)
    {
        $response = $this->parseResponse(
            $this->miniProgramApp->tester->unbind($userStr)
        );

        $tester = Tester::where(['mini_program_id' => $this->miniProgram->mini_program_id])->where(function ($query) use ($userStr) {
            $query->orWhere('userstr', $userStr);
            $query->orWhere('wechat_id', $userStr);
            return $query;
        })->first();

        if ($tester) $tester->delete();

        return $response;
    }

    public function sessionKey(string $code)
    {
        $response = $this->miniProgramApp->auth->session($code);

        if (isset($response['errcode'])) {
            return $this->parseResponse($response);
        }
        return $response;
    }

    public function decryptData(string $jscode, string $iv, string $encryptedData)
    {
        $session = $this->sessionKey($jscode);
        return $this->miniProgramApp->encryptor->decryptData($session['session_key'], $iv, $encryptedData);
    }

    public function getAccessToken()
    {
        return $this->miniProgramApp->access_token->getToken();
    }

    public function commit($templateId, $extJson = null)
    {
        if (is_null($extJson)) {
            if (!isset($this->component->extend)) {
                throw new UnprocessableEntityHttpException(trans('平台为设置发版配置'));
            }

            $config = json_decode($this->component->extend->config, true);
            $extJson = $config['ext_json'] ? json_encode($config['ext_json']) : '{}';
        } else {
            MiniProgramExt::updateOrCreate([
                'component_id' => $this->miniProgram->component_id,
                'template_id' => $templateId,
                'mini_program_id' => $this->miniProgram->mini_program_id,
                'company_id' => $this->miniProgram->company_id
            ], ['config' => json_encode(['ext_json' => json_decode($extJson, true)])]);
        }

        $extJson = $this->miniProgram->assign($extJson);

        $template = ComponentTemplate::where('template_id', $templateId)
            ->where('component_id', $this->component->component_id)
            ->orderBy('component_template_id', 'desc')->firstOrFail();

        $response = $this->parseResponse(
            $this->miniProgramApp->code->commit($templateId, $extJson, $template->user_version, $template->user_desc)
            , true);

        $extJson = json_decode($extJson, true);
        $config = json_encode(['ext_json' => $extJson]);
        $release = (new Release())->syncMake($this->miniProgram, $templateId, $config, $response);

        return $release;
    }

    public function getQrCode($path = null)
    {
        $response = $this->miniProgramApp->code->getQrCode($path);

        return $this->stream2base64($response);
    }

    /**
     * @param $response StreamResponse
     *
     * @return array|string
     */
    protected function stream2base64($response)
    {
        if(false === stripos($response->getHeaderLine('Content-disposition'), 'attachment')){
            return $this->parseResponse($response);
        }

        $response->getBody()->rewind();
        $contents = $response->getBody()->getContents();

        if (empty($contents) || '{' === $contents[0]) {
            throw new RuntimeException('Invalid media response content.');
        }
        $filename = Str::random(64) . '.jpeg';
        $filePath = storage_path() . '/framework/cache/' . $filename;
        file_put_contents($filePath, $contents);

        $base64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($filePath));

        unlink($filePath);

        return $base64;
    }

    public function getCategory()
    {
        $response = $this->parseResponse(
            $response = $this->miniProgramApp->code->getCategory()
        );
        return $response['category_list'] ?? [];
    }

    public function getPage()
    {
        $response = $this->parseResponse(
            $response = $this->miniProgramApp->code->getPage()
        );
        return $response['page_list'] ?? [];
    }

    public function audit($itemList)
    {
        $release = Release::where('mini_program_id', $this->miniProgram->mini_program_id)
            ->where('status', Release::RELEASE_STATUS_COMMITTED)
            ->orderBy('release_id', 'desc')
            ->firstOrFail();

        $response = $this->parseResponse(
            $response = $this->miniProgramApp->code->submitAudit($itemList)
            , true);

        ReleaseItem::create([
            'release_id' => $release->release_id,
            'name' => ReleaseItem::CONFIG_KEY_AUDIT,
            'original_config' => $release->config,
            'push_config' => $itemList,
            'response' => json_encode($response),
            'status' => ReleaseItem::STATUS_SUCCESS,
            'mini_program_id' => $release->mini_program_id,
        ]);

        $release->audit_id = $response['auditid'];
        $release->save();

        return [
            'audit_id' => $release->audit_id
        ];
    }

    public function getAuditStatus($audit)
    {
        $response = $this->parseResponse(
            $this->miniProgramApp->code->getAuditStatus($audit)
        );

        if(isset($response['screenshot'])){
            $response['screenshot'] = $this->getMaterial($response['screenshot']);
        }

        return $response;
    }

    public function getLatestAuditStatus()
    {
        $response = $this->parseResponse(
            $this->miniProgramApp->code->getLatestAuditStatus()
        );
        return $response;
    }

    public function withdrawAudit()
    {
        $release = Release::where('mini_program_id', $this->miniProgram->mini_program_id)
            ->where('status', Release::RELEASE_STATUS_AUDITING)
            ->orderBy('release_id', 'desc')->firstOrFail();

        $response = $this->parseResponse(
            $this->miniProgramApp->code->withdrawAudit()
            , true);

        $release->status = Release::RELEASE_STATUS_AUDIT_REVERTED;
        $release->save();

        ReleaseItem::create([
            'release_id' => $release->release_id,
            'name' => ReleaseItem::NAME_REVERT_AUDIT,
            'original_config' => $release->config,
            'response' => json_encode($response),
            'status' => ReleaseItem::STATUS_SUCCESS,
            'mini_program_id' => $release->mini_program_id,
        ]);

        return true;
    }

    public function release($tradeNo)
    {
        $release = Release::where('mini_program_id', $this->miniProgram->mini_program_id)
            ->where('status', Release::RELEASE_STATUS_AUDIT_SUCCESS)
            ->orderBy('release_id', 'desc')->firstOrFail();

        $response = $this->parseResponse(
            $this->miniProgramApp->code->release()
            , true);

        $release->status = Release::RELEASE_STATUS_RELEASED;
        $release->save();

        ReleaseItem::create([
            'release_id' => $release->release_id,
            'name' => ReleaseItem::CONFIG_KEY_RELEASE,
            'original_config' => $release->config,
            'response' => json_encode($response),
            'status' => ReleaseItem::STATUS_SUCCESS,
            'mini_program_id' => $release->mini_program_id,
        ]);

        return [];
    }

    public function revertCodeRelease()
    {
        $response = $this->parseResponse(
            $this->miniProgramApp->code->rollbackRelease()
        );
        return $response;
    }

    public function SetSupportVersion($version)
    {
        $response = $this->parseResponse(
            $this->miniProgramApp->code->setSupportVersion($version)
        );
        return $response;
    }

    public function getSupportVersion()
    {
        $response = $this->parseResponse(
            $this->miniProgramApp->code->getSupportVersion()
        );
        return $response;
    }

    public function setVisitStatus($status)
    {
        try {
            $this->parseResponse(
                $this->miniProgramApp->code->changeVisitStatus($status)
            );
            return [];
        } catch (WechatGatewayException $exception) {
            if ($exception->getCode() !== 85021) {
                throw $exception;
            }
        }

        return [];
    }

    public function templateRelease($templateId)
    {
        try {
            //step 1. 获取授权小程序列表
            $miniProgramList = (new MiniProgram())->getComponentMiniProgramList($this->component->component_id);
            if (count($miniProgramList) == 0) {
                throw new UnprocessableEntityHttpException(trans('暂无已绑定的小程序可发版'));
            }

            $templateList = Arr::pluck($this->templateList(), 'template_id');
            if (!in_array($templateId, $templateList)) {
                throw new UnprocessableEntityHttpException(trans('模板不存在'));
            }

            //step 2. 获取配置文件
            if (!isset($this->component->extend)) {
                throw new UnprocessableEntityHttpException(trans('批量发版配置不存在'));
            }
            $config = $this->component->extend->getReleaseConfig();

            $data = [];
            foreach ($miniProgramList as $miniProgram) {
                $data[] = (new Release())->make($miniProgram, $templateId, $config);
            }
            return $data;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function templateStatistical($templateId)
    {
        return Release::statistical($this->component->component_id, $templateId);
    }

    public function configSync()
    {
        try {
            //step 1. 获取授权小程序列表
            $miniProgramList = (new MiniProgram())->getComponentMiniProgramList($this->component->component_id);
            if (count($miniProgramList) == 0) {
                throw new UnprocessableEntityHttpException(trans('暂无已绑定的小程序可发版'));
            }

            //step 2. 获取配置文件
            if (!isset($this->component->extend)) {
                throw new UnprocessableEntityHttpException(trans('批量发版配置不存在'));
            }
            $config = $this->component->extend->getReleaseConfig();

            $data = [];
            foreach ($miniProgramList as $miniProgram) {
                $data[] = (new Release())->syncConfig($miniProgram, $config);
            }
            return $data;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function componentGetMaterial($miniProgramId, $mediaId)
    {
        $this->setMiniProgramById($miniProgramId);
        return $this->getMaterial($mediaId);
    }

    public function getMaterial($mediaId)
    {
        if(strpos($mediaId, '|')) $mediaId = explode('|', $mediaId);

        $collect = Collection::wrap($mediaId)->map(function($mediaId){
            return $this->stream2base64(
                $this->miniProgramApp->material->get($mediaId)
            );
        });

        return $collect;
    }
}
