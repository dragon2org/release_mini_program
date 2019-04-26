<?php
/**
 * Created by PhpStorm.
 * User: harlen
 * Date: 2019/4/24
 * Time: 11:33 PM
 */

namespace App\Services;


use App\Models\Component;
use App\Models\MiniProgram;
use EasyWeChat\Factory;
use EasyWeChat\OpenPlatform\Server\Guard;
use Log;

class ReleaseService
{
    protected $component;

    /**
     * @var \EasyWeChat\OpenPlatform\Application
     */
    protected $openPlatform;

    protected $miniProgramAppId;

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

    public function setMiniProgram($appId)
    {
        $this->miniProgramAppId = $appId;
    }

    public function __get($name)
    {
        return $this->{$name};
    }

    public function server()
    {
        $server = $this->openPlatform->server;

        // 处理授权成功事件
        $server->push(function ($message) {

        }, Guard::EVENT_AUTHORIZED);

        // 处理授权更新事件
        $server->push(function ($message) {

        }, Guard::EVENT_UPDATE_AUTHORIZED);

        // 处理授权取消事件
        $server->push(function ($message) {

        }, Guard::EVENT_UNAUTHORIZED);

        // 处理VERIFY_TICKET
        $server->push(function ($message) {
            Log::info('ComponentVerifyTicket:', $message);
            $this->setTicket($message['ComponentVerifyTicket']);
        }, Guard::EVENT_COMPONENT_VERIFY_TICKET);

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
        $miniProgram = new MiniProgram();
        $miniProgram->component_id = $this->component->component_id;
        $miniProgram->app_id = $miniProgramAppId;
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
}