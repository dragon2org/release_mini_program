<?php

namespace Http\Controllers\Api\V1\Definitions;

/**
 * @SWG\Definition(definition="Template", type="object")
 */
class Template
{
    /**
     * @SWG\Property(type="integer", description="创建时间")
     */
    public $create_time;

    /**
     * @SWG\Property(type="string", description="模板id")
     */
    public $template_id;

    /**
     * @SWG\Property(type="string", description="模版版本号")
     */
    public $user_version;

    /**
     * @SWG\Property(type="string", description="模版描述")
     */
    public $user_desc;

    /**
     * @SWG\Property(type="string", description="来源小程序appid")
     */
    public $source_miniprogram_appid;

    /**
     * @SWG\Property(type="string", description="来源小程序名字")
     */
    public $source_miniprogram;

    /**
     * @SWG\Property(type="string", description="开发者")
     */
    public $developer;
}
