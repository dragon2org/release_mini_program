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

    public function defaultConfig()
    {
        return [
            'tester' => [],
            'domain' => [
                'requestdomain' => [],
                'wsrequestdomain' => [],
                'uploaddomain' => [],
                'downloaddomain' => [],
            ],
            'web_view_domain' => [
                'webviewdomain' => [],
            ],
            'visit_status' => 'close',
            'support_version' => '1.0.1',
            'ext_json' => [],
        ];
    }

    public function getReleaseConfig()
    {
        if($config = json_decode($this->config, true)){
            return $config;
        }

        return $this->defaultConfig();
    }

}
