<?php

namespace Http\Controllers\Api\V1\Definitions;

/**
 * @SWG\Definition(
 *     definition="Component",
 *     type="object",
 *     required={"name", "desc", "app_id", "app_secret", "verify_token", "aes_key", "validate"}
 * )
 */
class Component
{
    /**
     * @SWG\Property(type="string", description="业务方内部名称")
     */
    public $inner_name;

    /**
     * @SWG\Property(type="string", description="业务方内部描述")
     */
    public $inner_desc;
    

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
     * @SWG\Property(type="string", description="三方平台AppSecret")
     */
    public $app_secret;

    /**
     * @SWG\Property(type="string", description="三方平台消息验证token")
     */
    public $verify_token;


    /**
     * @SWG\Property(type="string", description="三方平台消息解密解密Key")
     */
    public $aes_key;

    /**
     * @SWG\Property(
     *     type="object",
     *     description="域名验证信息",
     *     required={"filename", "content"},
     *     @SWG\Property(property="filename", type="string", description="域名信息验证文件名"),
     *     @SWG\Property(property="content", type="string", description="域名信息验证文件内容"),
     * )
     */
    public $validate;
}
