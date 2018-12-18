<?php
/**
 * Created by PhpStorm.
 * User: chengyuanlong
 * Date: 2018-12-17
 * Time: 10:15
 */

namespace App\Services;

use App\Exceptions\UnprocessableEntityHttpException;
use EasyWeChat\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use App\Models\Component;
use EasyWeChat\OpenPlatform\Server\Guard;
use Illuminate\Support\Facades\Log;

class ComponentService
{
    protected $config;

    public $appId;

    /**
     * @var \EasyWeChat\OpenPlatform\Application
     */
    public $app;

    public function __construct()
    {
        $this->appId = 'wx302844b3c020c900';

        $this->app = Factory::openPlatform($this->getConfig());
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
        return $data;
        throw new UnprocessableEntityHttpException($data['errmsg'], $data['errcode']);
    }

    public function setAppId($appId)
    {
        $this->appId = $appId;
    }

    public function getParent()
    {
        return $this;
    }


    public function getConfig()
    {
        if (in_array(env('APP_ENV'), ['local'])) {
            return $this->getRemoteConfig();
        }
        return Cache::remember($this->getCacheKey(), 6000, function () {
            $component = Component::where('app_id', $this->appId)->first();
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

    protected function getRemoteConfig()
    {
        $component = $this->getComponent();

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
            if (isset($res->data)) {
                $app = Factory::openPlatform($config);
                $app['verify_ticket']->setTicket($res->data->component_verify_ticket);
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

    /**
     * @param array $input
     * @return Component
     * @throws UnprocessableEntityHttpException
     */
    public function register(array $input)
    {
        $component = new Component();
        $component->fill($input);
        $validateFile = Arr::get($input, 'validate');
        $component->validate_filename = $validateFile['filename'];
        $component->validate_content = $validateFile['content'];
        $component->save();
        return $component;
    }

    public function getComponent()
    {
        $component = Component::where(['app_Id' => $this->appId])->first();
        if (!isset($component)) {
            throw new UnprocessableEntityHttpException('Component is exists');
        }
        return $component;
    }

    public function updateComponent($input)
    {
        $component = $this->getComponent();
        $component->fill($input);
        $component->save();

        Cache::forget($this->getCacheKey());
        return $component;
    }

    public function updateReleaseConfig($config)
    {
        $oldConfig = $this->getReleaseConfig();
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

    public function getReleaseConfig()
    {
        $config = $this->getComponent()->getConfig();
        return json_decode($config, true);
    }

    public function configSync()
    {

    }

    public function server()
    {
        $server = $this->app->server;

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

    public function setTicket($ticket)
    {
        $this->app['verify_ticket']->setTicket($ticket);
        Cache::forget($this->getCacheKey());
    }

    public function getDrafts()
    {
        return $this->parseResponse(
            $this->app->code_template->getDrafts()
        );
    }

    public function draftToTemplate($templateId)
    {
        return $this->parseResponse(
            $this->app->code_template->createFromDraft($templateId)
        );
    }

    public function deleteTemplate($templateId)
    {
        return $this->parseResponse(
            $this->app->code_template->delete($templateId)
        );
    }

    public function templateList()
    {
        return $this->parseResponse(
            $this->app->code_template->list()
        );
    }
}