<?php

namespace App\Models;

use EasyWeChat\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Component extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'component';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'component_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'inner_name',
        'inner_desc',
        'inner_key',
        'name',
        'desc',
        'app_id',
        'app_secret',
        'verify_token',
        'aes_key',
    ];

    public function getAuthorizationLaunchPageDomain()
    {
        return url('');
    }

    public function getAuthorizationEventNotifyUrl()
    {
        return route('componentServe', ['componentAppId' => $this->app_id]);
    }

    public function getMsgEventNotifyUrl()
    {
        $route = route('componentMiniProgramServe', [
            'componentAppId' => $this->app_id,
            'miniProgram' => 'AAAAA'
            ]);
        return str_replace('AAAAA', '$APPID$', $route);
    }

    public static function getConfig($appId)
    {
        if(in_array(env('APP_ENV'), ['local'])){
            $component = Component::where('app_id', $appId)->first();
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

        return Cache::remember(self::getCacheKey($appId), 6000, function() use ($appId){
            $component = Component::where('app_id', $appId)->first();
            $config = [
                'component_id' => $component->component_id,
                'app_id' => $component->app_id,
                'secret' => $component->app_secret,
                'token' => $component->verify_token,
                'aes_key' => $component->aes_key,
                'component_verify_ticket' => $component->verify_ticket,
            ];
            $openPlatform = Factory::openPlatform($config);
            $openPlatform['verify_ticket']->setTicket($config['component_verify_ticket']);
            return $config;
        });
    }

    public static function getCacheKey($app_id) :string
    {
        return "component_{$app_id}_config";
    }
}
