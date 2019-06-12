<?php

namespace Http\Controllers\Api\V1\Definitions;

/**
 * @SWG\Definition(definition="Template", type="object")
 */
class Template
{
    /**
     * @SWG\Property(type="integer", description="提交时间")
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

    /**
     * @SWG\Property(type="string", description="分支")
     */
    public $tag;

    /**
     * @SWG\Property(type="integer", description="待提交")
     */
    public $uncommitted_count;

    /**
     * @SWG\Property(type="integer", description="待审核")
     */
    public $committed_count;

    /**
     * @SWG\Property(type="integer", description="审核中")
     */
    public $auditing_count;

    /**
     * @SWG\Property(type="integer", description="审核失败")
     */
    public $audit_failed_count;

    /**
     * @SWG\Property(type="integer", description="已发布")
     */
    public $released_count;
}
