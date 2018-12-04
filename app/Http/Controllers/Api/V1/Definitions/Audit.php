<?php
/**
 * Created by PhpStorm.
 * User: chengyuanlong
 * Date: 2018/12/4
 * Time: 下午1:59
 */

namespace App\Http\Controllers\Api\V1\Definitions;

/**
 * @SWG\Definition(definition="Audit", type="object")
 */
class Audit
{
    /**
     * @SWG\Property(type="string", description="小程序的页面，可通过“获取小程序的第三方提交代码的页面配置”接口获得")
     */
    public $address;

    /**
     * @SWG\Property(type="string", description="小程序的标签，多个标签用空格分隔，标签不能多于10个，标签长度不超过20")
     */
    public $tag;

    /**
     * @SWG\Property(type="string", description="一级类目名称，可通过“获取授权小程序帐号的可选类目”接口获得")
     */
    public $first_class;

    /**
     * @SWG\Property(type="string", description="二级类目(同上)")
     */
    public $second_class;

    /**
     * @SWG\Property(type="string", description="三级类目(同上)")
     */
    public $third_class;

    /**
     * @SWG\Property(type="integer", description="一级类目的ID编号，可通过“获取授权小程序帐号的可选类目”接口获得")
     */
    public $first_id;

    /**
     * @SWG\Property(type="integer", description="二级类目的ID(同上)")
     */
    public $second_id;

    /**
     * @SWG\Property(type="integer", description="三级类目的ID(同上)")
     */
    public $third_id;

    /**
     * @SWG\Property(type="string", description="小程序页面的标题,标题长度不超过32")
     */
    public $title;
}