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
use App\Jobs\SetMiniProgramAudit;
use App\Jobs\SetMiniProgramCodeCommit;
use App\Jobs\SetMiniProgramDomain;
use App\Jobs\SetMiniProgramSupportVersion;
use App\Jobs\SetMiniProgramTester;
use App\Jobs\SetMiniProgramVisitStatus;
use App\Jobs\SetMiniProgramWebViewDomain;
use App\Logs\ReleaseInQueueLog;
use App\Models\Component;
use App\Models\MiniProgram;
use App\Models\MiniProgramExt;
use App\Models\Release;
use App\Models\Tester;
use App\ReleaseConfigurator;
use App\ServeMessageHandlers\EventMessageHandler;
use App\ServeMessageHandlers\MiniProgramUnauthorizedEventMessageHandler;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Messages\Message;
use EasyWeChat\OpenPlatform\Server\Guard;
use Illuminate\Support\Arr;
use Log;

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
        $openPlatform = Factory::openPlatform([
            'app_id' => $this->component->app_id,
            'secret' => $this->component->app_secret,
            'token' => $this->component->verify_token,
            'aes_key' => $this->component->aes_key,
        ]);

        $openPlatform['verify_ticket']->setTicket($this->component->verify_ticket);

        return $this->openPlatform = $openPlatform;
    }

    /**
     * @param $appId
     * @return \EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Application
     * @throws UnprocessableEntityHttpException
     */
    public function setMiniProgram($appId)
    {
        $miniProgram = (new MiniProgram())
            ->where('app_id', $appId)
            ->where('component_id', $this->component->component_id)
            ->first();

        if (!isset($miniProgram)) {
            throw new UnprocessableEntityHttpException(trans('小程序未绑定'));
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

        $miniProgramAppId = $authorization['authorization_info']['authorizer_appid'];
        $refreshToken = $authorization['authorization_info']['authorizer_refresh_token'];

        //TODO::判断function_info
        $miniProgram = (new MiniProgram())->firstOrNew(['app_id' => $miniProgramAppId]);
        $miniProgram->component_id = $this->component->component_id;
        $miniProgram->company_id = request()->query('company_id', 0);
        $miniProgram->inner_name = request()->query('inner_name', '');
        $miniProgram->inner_desc = request()->query('inner_desc', '');
        $miniProgram->authorizer_refresh_token = $refreshToken;
        $miniProgram->save();

        //拉取基础信息
        $miniProgramAuthorizer = $this->openPlatform->getAuthorizer($miniProgramAppId);
        $info = $miniProgramAuthorizer['authorizer_info'];

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
        return view('authorize_success');
    }

    /**
     * @param array $data
     * @return array
     * @throws UnprocessableEntityHttpException
     */
    protected function parseResponse(array $data)
    {
        if ($data['errcode'] === 0) {
            unset($data['errmsg']);
            unset($data['errcode']);
            return $data;
        }
        if($data['errcode'] === -1) $data['errmsg'] = '微信网关繁忙';
        throw new WechatGatewayException($data['errmsg'], $data['errcode']);
    }

    public function getDrafts()
    {
        $data = $this->parseResponse(
            $this->openPlatform->code_template->getDrafts()
        );

        return $data['draft_list'] ?? [];
    }

    public function draftToTemplate($draftId)
    {
        return $this->parseResponse(
            $this->openPlatform->code_template->createFromDraft($draftId)
        );
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
        return $this->parseResponse(
            $this->openPlatform->code_template->delete($templateId)
        );
    }

    /**
     * get binding tester
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
     * @param $wechatId
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
     * @param $userStr
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

        if(isset($response['errcode'])){
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

    public function commit($templateId, $userVersion, $extJson = '{}')
    {

        MiniProgramExt::updateOrCreate([
            'component_id' => $this->miniProgram->component_id,
            'template_id' => $templateId,
            'mini_program_id' => $this->miniProgram->mini_program_id,
            'company_id' => $this->miniProgram->company_id
        ], ['config' => $extJson]);

        $extJson = $this->miniProgram->assign($extJson);

        $response = $this->parseResponse(
            $this->miniProgramApp->code->commit($templateId, $extJson, $userVersion, $userVersion)
        );

        return $response;
    }

    public function getQrCode($path = null)
    {
        //TODO::尝试转换成地址
        $response = $this->miniProgramApp->code->getQrCode($path);
        return $response;
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
        //TODO::做详细的验证参数
        $response = $this->parseResponse(
            $response = $this->miniProgramApp->code->submitAudit($itemList)
        );
        return $response;
    }

    public function getAuditStatus($audit)
    {
        $response = $this->parseResponse(
            $this->miniProgramApp->code->getAuditStatus($audit)
        );
        return $response;
    }

    public function getLatestAuditStatus()
    {
        $response = $this->parseResponse(
            $this->miniProgramApp->code->getLatestAuditStatus()
        );
        return $response;
    }

    public function release()
    {
        $response = $this->parseResponse(
            $this->miniProgramApp->code->release()
        );
        return $response;
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
        $response = $this->parseResponse(
            $this->miniProgramApp->code->changeVisitStatus($status)
        );
        return $response;
    }

    public function templateRelease($templateId)
    {
        try {
            //step 1. 获取授权小程序列表
            $miniProgramList = (new MiniProgram())->getComponentMiniProgramList($this->component->component_id);
            if (count($miniProgramList) == 0) {
                throw new UnprocessableEntityHttpException(trans('暂无已绑定的小程序可发版'));
            }

            $templatelist = Arr::pluck($this->templateList(), 'template_id');
            if(!in_array($templateId, $templatelist)){
                throw new UnprocessableEntityHttpException(trans('模板不存在'));
            }

            //step 2. 获取配置文件
            $config = $this->component->extend->getReleaseConfig();

            $configurator = new ReleaseConfigurator($config);
            foreach ($miniProgramList as $miniProgram) {
                $release = (new Release());
                $release = $release->createReleaseTrans($miniProgram, $templateId, $config);
                $tradeNo = $release->trade_no;
                SetMiniProgramDomain::dispatch($miniProgram, $release);
                ReleaseInQueueLog::info($tradeNo, $miniProgram, $config, $templateId, SetMiniProgramDomain::class, SetMiniProgramDomain::VERSION);

                SetMiniProgramWebViewDomain::dispatch($miniProgram, $release);
                ReleaseInQueueLog::info($tradeNo, $miniProgram, $config, $templateId, SetMiniProgramWebViewDomain::class, SetMiniProgramWebViewDomain::VERSION);

                SetMiniProgramTester::dispatch($miniProgram, $release);
                ReleaseInQueueLog::info($tradeNo, $miniProgram, $config, $templateId, SetMiniProgramTester::class, SetMiniProgramTester::VERSION);

                SetMiniProgramSupportVersion::dispatch($miniProgram, $release);
                ReleaseInQueueLog::info($tradeNo, $miniProgram, $config, $templateId, SetMiniProgramSupportVersion::class, SetMiniProgramSupportVersion::VERSION);

                SetMiniProgramVisitStatus::dispatch($miniProgram, $release);
                ReleaseInQueueLog::info($tradeNo, $miniProgram, $config, $templateId, SetMiniProgramVisitStatus::class, SetMiniProgramVisitStatus::VERSION);

                SetMiniProgramCodeCommit::dispatch($miniProgram, $release);
                ReleaseInQueueLog::info($tradeNo, $miniProgram, $config, $templateId, SetMiniProgramCodeCommit::class, SetMiniProgramCodeCommit::VERSION);

                SetMiniProgramAudit::dispatch($miniProgram, $release);
                ReleaseInQueueLog::info($tradeNo, $miniProgram, $config, $templateId, SetMiniProgramAudit::class, SetMiniProgramAudit::VERSION);
            }
            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
