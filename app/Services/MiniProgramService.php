<?php
/**
 * Created by PhpStorm.
 * User: chengyuanlong
 * Date: 2018-12-17
 * Time: 10:15
 */

namespace App\Services;

use App\Exceptions\UnprocessableEntityHttpException;
use App\Models\MiniProgram;
use App\Models\Tester;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class MiniProgramService
{

    public $appId;

    public $app;

    public $component;

    /**
     * Create a new manager instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->appId = 'wxb21f306e9ef1af2a';

        $this->component = new ComponentService();
        //$this->app = $this->component->app->miniProgram($this->appId, $this->getRefreshToken());
    }

    /**
     * @param array $data
     * @return array
     * @throws UnprocessableEntityHttpException
     */
    protected function parseResponse(array $data)
    {
        if($data['errcode'] === 0){
            unset($data['errmsg']);
            unset($data['errcode']);
            return $data;
        }
        return $data;
        throw new UnprocessableEntityHttpException($data['errmsg'], $data['errcode']);
    }

    public function getCacheKey()
    {
        return 'dhb.mini-program.release.mini-program' . $this->appId;
    }

    public function getRefreshToken()
    {
        return Cache::remember($this->getCacheKey(), 6000, function () {
            return MiniProgram::where('app_id', $this->appId)->value('authorizer_refresh_token');
        });
    }

    public function getBindUri()
    {
        $callbackUrl = Route('MiniProgramBindCallback', [
            'componentAppId' => $this->component->appId,
        ]);

        $params = [
            'inner_name' => request()->query('inner_name'),
            'inner_desc' => request()->query('inner_desc'),
            'company_id' => request()->query('company_id'),
            'redirect_uri' => request()->query('redirect_uri'),
        ];

        $callbackUrl .= '?' . http_build_query($params);

        $uri = request()->query('type') === 'mobile' ? $this->component->app->getMobilePreAuthorizationUrl($callbackUrl) : $this->component->app->getPreAuthorizationUrl($callbackUrl);
        return $uri;
    }

    public function bindCallback()
    {
        $authorization = $this->component->app->handleAuthorize();

        $miniProgramAppId = $authorization['authorization_info']['authorizer_appid'];
        $refreshToken = $authorization['authorization_info']['authorizer_refresh_token'];
        //TODO::判断function_info
        $miniProgram = new MiniProgram();
        $miniProgram->component_id = $this->component->getConfig['component_id'];
        $miniProgram->app_id = $miniProgramAppId;
        $miniProgram->company_id = request()->query('company_id', 0);
        $miniProgram->inner_name = request()->query('inner_name', '');
        $miniProgram->inner_desc = request()->query('inner_desc', '');
        $miniProgram->authorizer_refresh_token = $refreshToken;
        $miniProgram->save();

        //拉取基础信息
        $miniProgramAuthorizer = $this->component->app->getAuthorizer($miniProgramAppId);
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

    public function getMiniProgram()
    {
        $componentId = $this->component->getConfig()['component_id'];

        $miniProgramAppId = $this->appId;
        $item = MiniProgram::where([
            'component_id' => $componentId,
            'app_id' => $miniProgramAppId,
            'deleted' => 0
        ])->first();
        return $item;
    }

    public function updateMiniProgram($input)
    {
        $item = $this->getMiniProgram();
        $item->inner_name = $input['inner_name'] ?? '';
        $item->inner_desc = $input['inner_desc'] ?? '';
        return $item->save();
    }

    public function deleteMiniProgram()
    {
        $item = $this->getMiniProgram();
        $item->deleted = 1;
        $item->save();
    }

    public function sessionKey(string $code)
    {
        return $this->app->auth->session($code);
    }

    public function decryptData(string $iv, string $encryptedData)
    {
        $sessionKey = 1;
        return $this->app->encryptor->decryptData($sessionKey, $iv, $encryptedData);
    }

    public function getAccessToken()
    {
        return $this->app->access_token->getToken();
    }

    /**
     * get binding tester
     * @return array
     * @throws UnprocessableEntityHttpException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getTester()
    {
        $remote = $this->parseResponse($this->app->tester->list())['members'];

        $items = $this->getMiniProgram()->tester()->select(['wechat_id', 'userstr'])->get()->toArray();

        $localUserStr = Arr::pluck($items, 'userstr');
        $remoteUserStr = Arr::pluck($remote, 'userstr');

        //微信服务器已经绑定，本地没有数据的
        $diff = array_diff($remoteUserStr, $localUserStr);

        foreach($diff as $item){
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
        $response = $this->app->tester->bind($wechatId);
        $response = $this->parseResponse($response);

        $fill = [
            'wechat_id' => $wechatId,
            'userstr' => $response['userstr'],
        ];
        $tester = new Tester();
        $tester->fill($fill);
        $tester->mini_program_id = $this->getMiniProgram()->mini_program_id;
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
        $response = $this->app->tester->bind($userStr);
        $response = $this->parseResponse($response);

        $miniProgramId = $this->getMiniProgram()->mini_program_id;
        $tester = Tester::where(['mini_program_id' => $miniProgramId])->where(function($query) use($userStr) {
            $query->orWhere('userstr', $userStr);
            $query->orWhere('wechat_id', $userStr);
            return $query;
        })->first();
        if($tester){
            $tester->delete();
        }

        return true;
    }


    public function commit($templateId, $userVersion, $userDesc)
    {
        $response = $this->app->code->commit($templateId, '', $userVersion, $userVersion);
        $response = $this->parseResponse($response);


        return $response;
    }

    public function getQrCode($path = null)
    {
        $response = $this->app->code->getQrCode($path);
        return $response;
    }

    public function getCategory()
    {
        $response = $this->app->code->getCategory();
        return $response;
    }

    public function getPage()
    {
        $response = $this->app->code->getPage();
        return $response;
    }

    public function audit($itemList)
    {
        $response = $this->app->code->submitAudit($itemList);
        return $response;
    }

    public function getAuditStatus($audit)
    {
        $response = $this->app->code->getAuditStatus($audit);
        return $response;
    }

    public function getLatestAuditStatus()
    {
        $response = $this->app->code->getLatestAuditStatus();
        return $response;
    }

    public function release()
    {
        $response = $this->app->code->release();
        return $response;
    }

    public function revertCodeRelease()
    {
        $response = $this->app->code->rollbackRelease();
        return $response;
    }

    public function SetSupportVersion($version)
    {
        $response = $this->app->code->setSupportVersion($version);
        return $response;
    }

    public function getSupportVersion()
    {
        $response = $this->app->code->getSupportVersion();
        return $response;
    }

    public function setVisitStatus($status)
    {
        $response = $this->app->code->changeVisitStatus($status);
        return $response;
    }

}