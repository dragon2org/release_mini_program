<?php

namespace Http\Controllers\Api\V1\Definitions;

/**
 * @SWG\Definition(definition="Draft", type="object")
 */
class Draft
{
    /**
     * @SWG\Property(type="string", description="草稿id")
     */
    public $draft_id;

    /**
     * @SWG\Property(type="string", description="模版版本号")
     */
    public $user_version;

    /**
     * @SWG\Property(type="string", description="模版描述")
     */
    public $user_desc;

    /**
     * @SWG\Property(type="string", description="开发者上传草稿时间")
     */
    public $create_time;

    /**
     * @SWG\Property(type="string", description="开源小程序")
     */
    public $source_miniprogram_appid;

    /**
     * @SWG\Property(type="string", description="开源小程序名称")
     */
    public $source_miniprogram;

    /**
     * @SWG\Property(type="string", description="开发者")
     */
    public $developer;
}
