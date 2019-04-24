<?php
/**
 * Created by PhpStorm.
 * User: harlen
 * Date: 2019/4/24
 * Time: 11:33 PM
 */

namespace App\Services;


use App\Models\Component;
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
}