<?php
/**
 * Created by PhpStorm.
 * User: harlen-mac
 * Date: 2018/12/4
 * Time: 上午1:42
 */

namespace App\Http\Controllers\Api\V1\Definitions;

/**
 * @SWG\Definition(
 *     definition="MiniProgramList",
 *     type="object"
 * )
 */
class MiniProgramList
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
     * @SWG\Property(type="string", description="小程序名称")
     */
    public $nick_name;

    /**
     * @SWG\Property(type="string", description="小程序头像")
     */
    public $head_img;

    /**
     * @SWG\Property(type="string", description="小程序AppID")
     */
    public $app_id;

    /**
     * @SWG\Property(type="string", description="原始ID")
     */
    public $user_name;

    /**
     * @SWG\Property(type="string", description="小程序主体名称")
     */
    public $principal_name;


    /**
     * @SWG\Property(type="string", description="二维码图片的URL")
     */
    public $qrcode_url;

    /**
     * @SWG\Property(type="string", description="小程序平台描述")
     */
    public $desc;

    /**
     * @SWG\Property(type="string", description="微信基础库当前版本")
     */
    public $user_version;

    /**
     * @SWG\Property(type="string", description="线上版本")
     */
    public $online_version;

    /**
     * @SWG\Property(type="string", description="构建中版本")
     */
    public $build_version;

    /**
     * @SWG\Property(type="string", description="模板内部版本")
     */
    public $tag;

    /**
     * @SWG\Property(type="integer", description="授权状态; 10正常；20授权时效")
     */
    public $authorization_status;

    /**
     * @SWG\Property(type="integer", description="发布状态;待审核:11; 审核中:20;审核失败:22;审核成功:21;已发布:30; //可发布状态: 21")
     */
    public $release_status;
}
