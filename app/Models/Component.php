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

    public function extend()
    {
        return $this->hasOne(ComponentExt::class, 'component_id', 'component_id')->withDefault(function(){
            return json_encode([
                'tests' => '',
                'domain' => '',
                'web_view_domain' => '',
                'visit_status' => '',
                'support_version' => '',
            ], JSON_UNESCAPED_UNICODE);
        });
    }

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

}
