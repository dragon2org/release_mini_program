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
     * @SWG\Property(
     *     type="object",
     *     description="服务域名配置信息",
     *     @SWG\Property(property="action", type="string", description="set覆盖"),
     *     @SWG\Property(property="requestdomain", type="array", description="请求域名;最多20个",  @SWG\Items()),
     *     @SWG\Property(property="wsrequestdomain", type="array", description="wss域名;最多20个",  @SWG\Items()),
     *     @SWG\Property(property="uploaddomain", type="array", description="上传域名;最多20个",  @SWG\Items()),
     *     @SWG\Property(property="downloaddomain", type="array", description="下载域名;最多20个",  @SWG\Items()),
     * )
     */
    public $domain;

    /**
     * @SWG\Property(
     *     type="object",
     *     description="业务域名",
     *     @SWG\Property(property="action", type="string", description="set覆盖"),
     *     @SWG\Property(property="webviewdomain", type="array", description="业务域名;最多20个",  @SWG\Items()),
     * )
     */
    public $web_view_domain;

    /**
     * @SWG\Property(
     *     type="array",
     *     description="体验者配置; Array[integer]",
     *     @SWG\Items(),
     * )
     */
    public $tester;

    /**
     * @SWG\Property(type="string", description="json_string;小程序ext_json配置: 替换变量, 小程序app_id {$app_id}, 公司id {$company_id}")
     */
    public $ext_json;

    /**
     * @SWG\Property(
     *     type="array",
     *     description="体验者配置; Array[integer]",
     *     @SWG\Items(),
     * )
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