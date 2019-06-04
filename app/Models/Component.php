<?php

namespace App\Models;

use App\Exceptions\UnprocessableEntityHttpException;
use Illuminate\Database\Eloquent\Model;

class Component extends Model
{
    use SoftDeletes;

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

    public function template()
    {
        return $this->hasMany(ComponentTemplate::class, 'component_id', 'component_id');
    }

    public function validateFile()
    {
        return $this->hasOne(ValidateFile::class, 'component_id', 'component_id');
    }

    public function getConfig()
    {
        if($config = json_decode($this->config, true)){
            return $config;
        }

        return json_encode([
            'tests' => '',
            'domain' => '',
            'web_view_domain' => '',
            'visit_status' => '',
            'support_version' => '',
        ], JSON_UNESCAPED_UNICODE);
    }

    public function getAuthorizationLaunchPageDomain()
    {
        $url = url('');

        return preg_replace("/http[s]*\:\/\//", '', $url);
    }

    public function getAuthorizationEventNotifyUrl()
    {
        $route = route('componentServe', ['componentAppId' => $this->getAppId()]);
        return str_replace('ZZZZZ', '$componentAppId$', $route);
    }

    public function getMsgEventNotifyUrl()
    {
        $route = route('componentMiniProgramServe', [
            'componentAppId' => $this->getAppId(),
            'miniProgram' => 'AAAAA'
            ]);

        $route = str_replace('ZZZZZ', '$componentAppId$', $route);
        return str_replace('AAAAA', '$APPID$', $route);
    }

    private function getAppId()
    {
        $appId = $this->app_id ? $this->app_id : 'ZZZZZ';
        return $appId;
    }

    public function getComponent($componentAppId)
    {
        $componentApp = (new self())
            ->where('app_id', $componentAppId)
            ->first();

        if(!isset($componentApp)){
            throw new UnprocessableEntityHttpException(trans('微信三方平台未注册: '. $componentAppId));
        }

        return $componentApp;
    }

    public function extend()
    {
        return $this->hasOne(ComponentExt::class, 'component_id', 'component_id');
    }

    public function validateUnique($appId)
    {
        if($this->app_id === $appId){
            return true;
        }

        if(((new self())->where('app_id', $appId)->count()) == 0){
            return true;
        }
        return false;
    }

    public function updateVerifyTicket($verifyTicket)
    {
        $this->verify_ticket = $verifyTicket;
        $this->save();
    }
}
