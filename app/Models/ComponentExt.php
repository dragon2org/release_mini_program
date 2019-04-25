<?php

namespace App\Models;

use App\Exceptions\UnprocessableEntityHttpException;
use EasyWeChat\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ComponentExt extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'component_ext';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'component_ext_id';


    public function getReleaseConfig()
    {
        if($config = json_decode($this->config, true)){
            return $config;
        }

        return json_encode([
            'tester' => '',
            'domain' => '',
            'web_view_domain' => '',
            'visit_status' => '',
            'support_version' => '',
            'ext_json' => '',
        ], JSON_UNESCAPED_UNICODE);
    }
}
