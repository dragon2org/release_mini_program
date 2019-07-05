<?php


namespace App\Http\Controllers\Api\V1\Definitions;

/**
 * @SWG\Definition(definition="TemplateTag", type="object")
 */
class TemplateTag
{
    /**
     * @SWG\Property(type="string", description="tag名称")
     */
    public $tag;
}