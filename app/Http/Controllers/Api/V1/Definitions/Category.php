<?php
/**
 * Created by PhpStorm.
 * User: chengyuanlong
 * Date: 2018/12/4
 * Time: 上午11:53
 */

namespace App\Http\Controllers\Api\V1\Definitions;

/**
 * @SWG\Definition(definition="Category", type="object")
 */
class Category
{
    /**
     * @SWG\Property(type="string", description="一级类目名称")
     */
    public $first_class;

    /**
     * @SWG\Property(type="string", description="二级类目名称")
     */
    public $second_class;

    /**
     * @SWG\Property(type="string", description="三级类目名称")
     */
    public $third_class;

    /**
     * @SWG\Property(type="string", description="一级类目的ID编号")
     */
    public $first_id;

    /**
     * @SWG\Property(type="string", description="二级类目的ID编号")
     */
    public $second_id;

    /**
     * @SWG\Property(type="string", description="三级类目的ID编号")
     */
    public $third_id;
}