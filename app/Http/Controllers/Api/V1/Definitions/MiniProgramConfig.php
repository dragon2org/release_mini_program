<?php
/**
 * Created by PhpStorm.
 * User: chengyuanlong
 * Date: 2018/12/4
 * Time: 上午10:04
 */

namespace App\Http\Controllers\Api\V1\Definitions;

/**
 * @SWG\Definition(definition="MiniProgramConfig", type="object")
 */
class MiniProgramConfig
{
    /**
     * @SWG\Property(type="string", description="服务域名配置信息")
     */
    public $domain;

    /**
     * @SWG\Property(type="string", description="业务域名配置信息")
     */
    public $web_view_domain;

    /**
     * @SWG\Property(type="string", description="体验者配置")
     */
    public $tester;

    /**
     * @SWG\Property(type="string", description="小程序ext_json配置")
     */
    public $ext_json;

    /**
     * @SWG\Property(type="string", description="提交审核页面")
     */
    public $page_list;

    /**
     * @SWG\Property(type="string", description="可见状态")
     */
    public $visit_status;

    /**
     * @SWG\Property(type="string", description="基础库版本")
     */
    public $support_version;
}