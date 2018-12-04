<?php
/**
 * Created by PhpStorm.
 * User: chengyuanlong
 * Date: 2018/12/4
 * Time: 上午11:47
 */

namespace App\Http\Controllers\Api\V1\Definitions;

/**
 * @SWG\Definition(definition="Commit", type="object")
 */
class Commit
{
    /**
     * @SWG\Property(type="string", description="代码库中的代码模版ID")
     */
    public $template_id;

    /**
     * @SWG\Property(type="object", description="小程序内自定义配置")
     */
    public $ext_json;

    /**
     * @SWG\Property(type="string", description="代码版本号")
     */
    public $user_version;

    /**
     * @SWG\Property(type="string", description="代码描述")
     */
    public $user_desc;
}