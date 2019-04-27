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
//        $core = app('dhb.component.core');
//        dd($core);
//        $this->component = $core->component;
//        $this->app = $this->component->app->miniProgram($core->miniProgramAppId, $this->getRefreshToken());
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