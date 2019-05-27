<?php
/**
 * Created by PhpStorm.
 * User: chengyuanlong
 * Date: 2018-12-17
 * Time: 10:15
 */

namespace App\Services;

use App\Exceptions\UnprocessableEntityHttpException;
use App\Facades\ReleaseFacade;
use App\Models\ComponentExt;
use App\Models\ValidateFile;
use EasyWeChat\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use App\Models\Component;
use EasyWeChat\OpenPlatform\Server\Guard;
use Illuminate\Support\Facades\Log;

class ComponentService
{
    /**
     * @param array $input
     * @return Component
     * @throws UnprocessableEntityHttpException
     */
    public function register(array $input)
    {
        $component =  (new Component())->where('app_id', $input['app_id'])->first();
        if(isset($component)){
            throw new UnprocessableEntityHttpException(trans('平台已存在'));
        }

        if(!isset($component)){
            $component = new Component();
        }
        $component->fill($input);
        $component->save();
        $file = Arr::get($input, 'validate');
        ValidateFile::updateOrCreate(['filename' => $file['filename']], [
            'content'=> $file['content'],
            'component_id' => $component->component_id
        ]);

        return $component;
    }

    public function updateComponent($input)
    {
        $component = (new Component())->where('app_id', $input['app_id'])->first();
        if(!isset($component)){
            throw new UnprocessableEntityHttpException(trans('App 不存在'));
        }
        $component->fill($input);
        $component->save();
        $file = Arr::get($input, 'validate');
        ValidateFile::updateOrCreate(['filename' => $file['filename']], [
            'content'=> $file['content'],
            'component_id' => $component->component_id
        ]);

        return $component;
    }

    public function updateDomain($input)
    {
        $data =  [
            'action' => $input['action'],
            'requestdomain' => $input['requestdomain'] ?? [],
            'wsrequestdomain' => $input['wsrequestdomain'] ?? [],
            'uploaddomain' => $input['uploaddomain'] ?? [],
            'downloaddomain' => $input['downloaddomain'] ?? [],
        ];
        return $this->updateReleaseConfig(['domain'=> $data]);
    }

    public function updateWebViewDomain($input)
    {
        $data =  [
            'action' => $input['action'],
            'webviewdomain' => $input['webviewdomain'] ?? [],
        ];
        return $this->updateReleaseConfig(['web_view_domain'=> $data]);
    }

    public function updateTester($input)
    {
        $data =  [
            'tester' => $input['tester'] ?? [],
        ];
        return $this->updateReleaseConfig($data);
    }

    public function updateVisitStatus($input)
    {
        $data =  [
            'visit_status' => $input['visit_status'] ?? 'close',
        ];
        return $this->updateReleaseConfig($data);
    }

    public function updateSupportVersion($input)
    {
        $data =  [
            'support_version' => $input['support_version'] ?? '1.0.1',
        ];
        return $this->updateReleaseConfig($data);
    }

    public function updateExtJson($input)
    {
        $data =  [
            'ext_json' => $input ? json_decode($input, true, JSON_UNESCAPED_UNICODE) : [],
        ];
        return $this->updateReleaseConfig($data);
    }

    public function updateReleaseConfig($input)
    {
        $config = $this->getReleaseConfig();
        foreach (['tester', 'domain', 'web_view_domain', 'visit_status', 'support_version', 'ext_json'] as $key) {
            if (isset($input[$key])) {
                $config[$key] = $input[$key];
            }
        }

        $extend = ReleaseFacade::service()->component->extend;
        if(!isset($extend)){
            $extend = new ComponentExt();
            $extend->component_id = ReleaseFacade::service()->component->component_id;
        }
        $extend->config = json_encode($config, JSON_UNESCAPED_UNICODE);
        $extend->config_version = sha1($extend->config);
        $extend->save();

        return $config;
    }

    public function getReleaseConfig()
    {
        $extend = ReleaseFacade::service()->component->extend;
        if(isset($extend)){
            $config = $extend->config;
            $config = json_decode($config, true);
        }else{
            $config = (new ComponentExt())->getReleaseConfig();
        }

        return $config;
    }
}