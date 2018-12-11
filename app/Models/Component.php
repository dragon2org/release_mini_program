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
        return env('APP_URL');
    }

    public function getAuthorizationEventNotifyUrl()
    {
        return env('APP_URL') .
            "/component/{$this->app_id}/serve";
    }

    public function getMsgEventNotifyUrl()
    {
        return env('APP_URL') .
            "component/{$this->app_id}" . '/mini_program/$APPID$/serve';
    }
}
