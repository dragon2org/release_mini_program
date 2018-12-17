<?php
/**
 * Created by PhpStorm.
 * User: chengyuanlong
 * Date: 2018-12-17
 * Time: 10:15
 */
namespace App\Component;

use EasyWeChat\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

class Component
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    protected $config;

    /**
     * @var \EasyWeChat\OpenPlatform\Application
     */
    protected $proxy;

    protected $appId;

    /**
     * Create a new manager instance.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->appId  = 'wx302844b3c020c900';

        $openPlatform = Factory::openPlatform($this->getConfig());
        $this->proxy = $openPlatform;
    }

    public function setAppId($appId)
    {
        $this->appId = $appId;
    }

    public function getConfig()
    {
        if(in_array(env('APP_ENV'), ['local'])){
            return $this->getRemoteConfig();
        }
        return Cache::remember($this->getCacheKey(), 6000, function() use($appId){
            $component = Component::where('app_id', $appId)->first();
            $config = [
                'component_id' => $component->component_id,
                'app_id' => $component->app_id,
                'secret' => $component->app_secret,
                'token' => $component->verify_token,
                'aes_key' => $component->aes_key,
                'component_verify_ticket' => $component->verify_ticket,
            ];
            return $config;
        });
    }

    public function getRemoteConfig()
    {
        $component = Component::where('app_id', $this->appId)->first();
        $config = [
            'component_id' => $component->component_id,
            'app_id' => $component->app_id,
            'secret' => $component->app_secret,
            'token' => $component->verify_token,
            'aes_key' => $component->aes_key,
            'component_verify_ticket' => $component->verify_ticket,
        ];

        try {
            $uri = route('getComponentVerifyTicket', ['componentAppId' => $component->app_id], false) . '?remote=1';
            $res = file_get_contents(env('WECAHT_RECEIVE_MSG_GATEWAY_HOST') . $uri);
            $res = json_decode($res);
            if(isset($res->data)){
                $config['component_verify_ticket'] = $res->data->component_verify_ticket;
                $openPlatform = Factory::openPlatform($config);
                $openPlatform['verify_ticket']->setTicket($config['component_verify_ticket']);
            }
            return $config;
        } catch (\Exception $e) {
            throw new \App\Exceptions\InternalException('拉取网关component_verify_ticket失败');
        }
    }

    public function getCacheKey()
    {
        return 'dhb.mini-program.release.component' . $this->appId;
    }
}