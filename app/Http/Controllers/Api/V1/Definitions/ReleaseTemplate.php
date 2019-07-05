<?php

namespace Http\Controllers\Api\V1\Definitions;

/**
 * @SWG\Definition(definition="ReleaseTemplate", type="object")
 */
class ReleaseTemplate
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
     * @SWG\Property(type="string", description="开发者")
     */
    public $developer;

    /**
     * @SWG\Property(type="string", description="分支")
     */
    public $tag;
}
