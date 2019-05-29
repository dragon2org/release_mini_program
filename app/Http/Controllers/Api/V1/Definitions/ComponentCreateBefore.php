<?php

namespace Http\Controllers\Api\V1\Definitions;

/**
 * @SWG\Definition(
 *     definition="ComponentCreateBefore",
 *     type="object",
 * )
 */
class ComponentCreateBefore
{
    /**
     * @SWG\Property(
     *     type="object",
     *     description="域名验证信息",
     *     required={"filename", "content"},
     *     @SWG\Property(property="filename", type="string", description="域名信息验证文件名。必须用.txt结尾"),
     *     @SWG\Property(property="content", type="string", description="域名信息验证文件内容"),
     * )
     */
    public $validate;
}
