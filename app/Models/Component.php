<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function getConfig()
    {
        return [
            'app_id' => $this->app_id,
            'secret' => $this->app_secret,
            'token' => $this->verify_token,
            'aes_key' => $this->aes_key,
        ];
    }
}
