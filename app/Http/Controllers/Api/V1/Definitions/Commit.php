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
}