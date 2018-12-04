<?php

namespace Http\Controllers\Api\V1\Definitions;

/**
 * @SWG\Definition(definition="Component", type="object")
 */
class Component
{
    /**
     * @SWG\Property(type="string", description="内部名称")
     */
    public $inner_name;

    /**
     * @SWG\Property(type="string", description="内部名称")
     */
    public $inner_desc;

    /**
     * @SWG\Property(type="string", description="内部名称")
     */
    public $inner_key;

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
}
