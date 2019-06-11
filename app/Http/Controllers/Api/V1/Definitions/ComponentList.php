<?php

namespace Http\Controllers\Api\V1\Definitions;

/**
 * @SWG\Definition(
 *     definition="ComponentList",
 *     type="object",
 * )
 */
class ComponentList
{
    /**
     * @SWG\Property(type="string", description="三方平台名称")
     */
    public $name;

    /**
     * @SWG\Property(type="string", description="三方平台描述")
     */
    public $desc;

    /**
     * @SWG\Property(type="string", description="三方平台AppID")
     */
    public $app_id;

    /**
     * @SWG\Property(type="integer", description="小程序数量")
     */
    public $mini_program_number;

    /**
     * @SWG\Property(type="integer", description="模板数量")
     */
    public $template_number;

    /**
     * @SWG\Property(type="string", description="添加时间")
     */
    public $created_at;
}
