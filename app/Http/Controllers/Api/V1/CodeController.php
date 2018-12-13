<?php
/**
 * Created by PhpStorm.
 * User: harlen-mac
 * Date: 2018/12/4
 * Time: 上午1:50
 */

namespace App\Http\Controllers\Api\V1;


use App\Models\Component;
use App\Models\ComponentExt;
use App\Models\MiniProgram;
use EasyWeChat\Factory;

class CodeController extends Controller
{
    /**
     * @SWG\Post(
     *     path="/component/{componentAppId}/mini_program/{miniProgramAppId}/commit",
     *     summary="上传代码",
     *     tags={"小程序管理"},
     *     description="上传代码到微信服务器",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="componentAppId",
     *         in="path",
     *         description="三方平台AppID",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="miniProgramAppId",
     *         in="path",
     *         description="小程序AppId",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="data",
     *         in="body",
     *         description="表单数据",
     *         required=true,
     *         type="object",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="template_id",
     *                 type="string",
     *                 description="模板id",
     *             ),
     *             @SWG\Property(
     *                 property="user_version",
     *                 type="string",
     *                 description="用户自定义版本",
     *             ),
     *             @SWG\Property(
     *                 property="user_desc",
     *                 type="string",
     *                 description="用户自定义描述",
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="成功返回",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="status",
     *                 type="string",
     *                 default="T",
     *                 description="接口返回状态['T'->成功; 'F'->失败]"
     *             )
     *         )
     *     )
     * )
     */
    public function commit($componentAppId, $miniProgramAppId)
    {
        $config = Component::getConfig($componentAppId);
        $openPlatform = Factory::openPlatform($config);
        $miniProgram = MiniProgram::where('app_id', $miniProgramAppId)->first();
        $miniProgramApp = $openPlatform->miniProgram($miniProgramAppId, $miniProgram->authorizer_refresh_token);

        $componentConfig = ComponentExt::where('component_id',$config['component_id'])->value('config');
        $componentConfig = json_decode($componentConfig, true);
        //TODO::替换模板变量

        $result = $miniProgramApp->code->commit(
            (integer) request()->input('template_id'),
            json_encode($componentConfig['ext_json'] ?? [], JSON_UNESCAPED_UNICODE),
            request()->input('user_version'),
            request()->input('user_desc')
        );
        //TODO::小程序配置设置

        return $this->response->withArray(['data' => $result]);
    }

    /**
     * @SWG\Get(
     *     path="/component/{componentAppId}/mini_program/{miniProgramAppId}/qrcode",
     *     summary="获取小程序体验二维码",
     *     tags={"小程序管理"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="componentAppId",
     *         in="path",
     *         description="三方平台AppID",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="miniProgramAppId",
     *         in="path",
     *         description="小程序AppId",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="path",
     *         in="query",
     *         description="跳转的path",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="成功返回",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="status",
     *                 type="string",
     *                 default="T",
     *                 description="直接返回图片"
     *             )
     *         )
     *     )
     * )
     */
    public function qrcode($componentAppId, $miniProgramAppId)
    {
        $config = Component::getConfig($componentAppId);
        $openPlatform = Factory::openPlatform($config);
        $miniProgram = MiniProgram::where('app_id', $miniProgramAppId)->first();
        $miniProgramApp = $openPlatform->miniProgram($miniProgramAppId, $miniProgram->authorizer_refresh_token);

        $response = $miniProgramApp->code->getQrCode();

        return $response;
    }
    /**
     * @SWG\Get(
     *     path="/component/{componentAppId}/mini_program/{miniProgramAppId}/category",
     *     summary="获取小程序可选类目",
     *     tags={"小程序管理"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="componentAppId",
     *         in="path",
     *         description="三方平台AppID",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="miniProgramAppId",
     *         in="path",
     *         description="小程序AppId",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="成功返回",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="status",
     *                 type="string",
     *                 default="T",
     *                 description="接口返回状态['T'->成功; 'F'->失败]"
     *             ),
     *             @SWG\Property(
     *                 property="data",
     *                 type="array",
     *                 @SWG\Items(ref="#/definitions/Category")
     *             )
     *         )
     *     )
     * )
     */
    public function category($componentAppId, $miniProgramAppId)
    {
        $config = Component::getConfig($componentAppId);
        $openPlatform = Factory::openPlatform($config);
        $miniProgram = MiniProgram::where('app_id', $miniProgramAppId)->first();
        $miniProgramApp = $openPlatform->miniProgram($miniProgramAppId, $miniProgram->authorizer_refresh_token);

        $response = $miniProgramApp->code->getCategory();

        return $this->response->withArray(['data'=> $response]);
    }
    /**
     * @SWG\Get(
     *     path="/component/{componentAppId}/mini_program/{miniProgramAppId}/page",
     *     summary="获取小程序提交代码的页面配置",
     *     tags={"小程序管理"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="componentAppId",
     *         in="path",
     *         description="三方平台AppID",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="miniProgramAppId",
     *         in="path",
     *         description="小程序AppId",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="成功返回",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="status",
     *                 type="string",
     *                 default="T",
     *                 description="接口返回状态['T'->成功; 'F'->失败]"
     *             ),
     *             @SWG\Property(
     *                 property="data",
     *                 type="array",
     *                 @SWG\Items()
     *             )
     *         )
     *     )
     * )
     */
    public function page($componentAppId, $miniProgramAppId)
    {
        $config = Component::getConfig($componentAppId);
        $openPlatform = Factory::openPlatform($config);
        $miniProgram = MiniProgram::where('app_id', $miniProgramAppId)->first();
        $miniProgramApp = $openPlatform->miniProgram($miniProgramAppId, $miniProgram->authorizer_refresh_token);

        $response = $miniProgramApp->code->getPage();

        return $this->response->withArray(['data'=> $response]);
    }
    /**
     * @SWG\Post(
     *     path="/component/{componentAppId}/mini_program/{miniProgramAppId}/audit",
     *     summary="提交审核",
     *     tags={"小程序管理"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="componentAppId",
     *         in="path",
     *         description="三方平台AppID",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="miniProgramAppId",
     *         in="path",
     *         description="小程序AppId",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="data",
     *         in="body",
     *         description="提交审核项的一个列表（至少填写1项，至多填写5项）",
     *         required=true,
     *         type="array",
     *         @SWG\Schema(ref="#/definitions/Audit")
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="成功返回",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="status",
     *                 type="string",
     *                 default="T",
     *                 description="接口返回状态['T'->成功; 'F'->失败]"
     *             ),
     *             @SWG\Property(
     *                 property="data",
     *                 type="object",
     *                 @SWG\Property(
     *                     property="auditid",
     *                     type="integer",
     *                     description="审核ID"
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function audit($componentAppId, $miniProgramAppId)
    {
        $config = Component::getConfig($componentAppId);
        $openPlatform = Factory::openPlatform($config);
        $miniProgram = MiniProgram::where('app_id', $miniProgramAppId)->first();
        $miniProgramApp = $openPlatform->miniProgram($miniProgramAppId, $miniProgram->authorizer_refresh_token);

        $items = request()->input('item_list');
        //457853319
        $response = $miniProgramApp->code->submitAudit($items);

        return $this->response->withArray(['data'=> $response]);
    }
    /**
     * @SWG\Get(
     *     path="/component/{componentAppId}/mini_program/{miniProgramAppId}/audit/{audit}",
     *     summary="获取提交代码的审核结果",
     *     tags={"小程序管理"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="componentAppId",
     *         in="path",
     *         description="三方平台AppID",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="miniProgramAppId",
     *         in="path",
     *         description="小程序AppId",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="成功返回",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="status",
     *                 type="string",
     *                 default="T",
     *                 description="接口返回状态['T'->成功; 'F'->失败]"
     *             ),
     *             @SWG\Property(
     *                 property="data",
     *                 type="object",
     *                 @SWG\Property(
     *                     property="reason",
     *                     type="string",
     *                     description="当status=1，审核被拒绝时，返回的拒绝原因"
     *                 ),
     *                 @SWG\Property(
     *                     property="status",
     *                     type="integer",
     *                     description="审核状态，其中0为审核成功，1为审核失败，2为审核中"
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function auditStatus($componentAppId, $miniProgramAppId, $audit)
    {
        $config = Component::getConfig($componentAppId);
        $openPlatform = Factory::openPlatform($config);
        $miniProgram = MiniProgram::where('app_id', $miniProgramAppId)->first();
        $miniProgramApp = $openPlatform->miniProgram($miniProgramAppId, $miniProgram->authorizer_refresh_token);

        $response = $miniProgramApp->code->getAuditStatus($audit);

        return $this->response->withArray(['data'=> $response]);
    }

    /**
     * @SWG\Get(
     *     path="/component/{componentAppId}/mini_program/{miniProgramAppId}/last_audit",
     *     summary="查询最新一次提交的审核状态",
     *     tags={"小程序管理"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="componentAppId",
     *         in="path",
     *         description="三方平台AppID",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="miniProgramAppId",
     *         in="path",
     *         description="小程序AppId",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="auditid",
     *         in="formData",
     *         description="审核ID",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="成功返回",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="status",
     *                 type="string",
     *                 default="T",
     *                 description="接口返回状态['T'->成功; 'F'->失败]"
     *             ),
     *             @SWG\Property(
     *                 property="data",
     *                 type="object",
     *                 @SWG\Property(
     *                     property="reason",
     *                     type="string",
     *                     description="当status=1，审核被拒绝时，返回的拒绝原因"
     *                 ),
     *                 @SWG\Property(
     *                     property="status",
     *                     type="integer",
     *                     description="审核状态，其中0为审核成功，1为审核失败，2为审核中"
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function lastAuditStatus($componentAppId, $miniProgramAppId)
    {
        $config = Component::getConfig($componentAppId);
        $openPlatform = Factory::openPlatform($config);
        $miniProgram = MiniProgram::where('app_id', $miniProgramAppId)->first();
        $miniProgramApp = $openPlatform->miniProgram($miniProgramAppId, $miniProgram->authorizer_refresh_token);

        $response = $miniProgramApp->code->getLatestAuditStatus();

        return $this->response->withArray(['data'=> $response]);
    }

    /**
     * @SWG\Post(
     *     path="/component/{componentAppId}/mini_program/{miniProgramAppId}/release",
     *     summary="发布已通过审核的小程序",
     *     tags={"小程序管理-代码管理"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="componentAppId",
     *         in="path",
     *         description="三方平台AppID",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="miniProgramAppId",
     *         in="path",
     *         description="小程序AppId",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="成功返回",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="status",
     *                 type="string",
     *                 default="T",
     *                 description="接口返回状态['T'->成功; 'F'->失败]"
     *             )
     *         )
     *     )
     * )
     */

    public function release($componentAppId, $miniProgramAppId)
    {
        $config = Component::getConfig($componentAppId);
        $openPlatform = Factory::openPlatform($config);
        $miniProgram = MiniProgram::where('app_id', $miniProgramAppId)->first();
        $miniProgramApp = $openPlatform->miniProgram($miniProgramAppId, $miniProgram->authorizer_refresh_token);

        $response = $miniProgramApp->code->release();

        return $this->response->withArray(['data'=> $response]);
    }
    /**
     * @SWG\Post(
     *     path="/component/{componentAppId}/mini_program/{miniProgramAppId}/visit_status",
     *     summary="修改小程序线上代码的可见状态",
     *     tags={"小程序管理-代码管理"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="componentAppId",
     *         in="path",
     *         description="三方平台AppID",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="miniProgramAppId",
     *         in="path",
     *         description="小程序AppId",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="data",
     *         in="body",
     *         description="请求json数据",
     *         required=true,
     *         type="object",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="action",
     *                 type="string",
     *                 description="设置可访问状态，发布后默认可访问，close为不可见，open为可见"
     *             ),
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="成功返回",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="status",
     *                 type="string",
     *                 default="T",
     *                 description="接口返回状态['T'->成功; 'F'->失败]"
     *             )
     *         )
     *     )
     * )
     */

    /**
     * @SWG\Post(
     *     path="/component/{componentAppId}/mini_program/{miniProgramAppId}/revert_code_release",
     *     summary="小程序版本回退",
     *     tags={"小程序管理-代码管理"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="componentAppId",
     *         in="path",
     *         description="三方平台AppID",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="miniProgramAppId",
     *         in="path",
     *         description="小程序AppId",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="成功返回",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="status",
     *                 type="string",
     *                 default="T",
     *                 description="接口返回状态['T'->成功; 'F'->失败]"
     *             )
     *         )
     *     )
     * )
     */
    public function revertCodeRelease($componentAppId, $miniProgramAppId)
    {
        $config = Component::getConfig($componentAppId);
        $openPlatform = Factory::openPlatform($config);
        $miniProgram = MiniProgram::where('app_id', $miniProgramAppId)->first();
        $miniProgramApp = $openPlatform->miniProgram($miniProgramAppId, $miniProgram->authorizer_refresh_token);

        $response = $miniProgramApp->code->rollbackRelease();

        return $this->response->withArray(['data'=> $response]);
    }
    /**
     * @SWG\Post(
     *     path="/component/{componentAppId}/mini_program/{miniProgramAppId}/support_version",
     *     summary="设置最低基础库版本",
     *     tags={"小程序管理-代码管理"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="componentAppId",
     *         in="path",
     *         description="三方平台AppID",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="miniProgramAppId",
     *         in="path",
     *         description="小程序AppId",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="data",
     *         in="body",
     *         description="请求json数据",
     *         required=true,
     *         type="object",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="version",
     *                 type="string",
     *                 description="版本.如1.0.0。可在开发者工具中获取"
     *             ),
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="成功返回",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="status",
     *                 type="string",
     *                 default="T",
     *                 description="接口返回状态['T'->成功; 'F'->失败]"
     *             )
     *         )
     *     )
     * )
     */
    public function SetSupportVersion($componentAppId, $miniProgramAppId)
    {
        $config = Component::getConfig($componentAppId);
        $openPlatform = Factory::openPlatform($config);
        $miniProgram = MiniProgram::where('app_id', $miniProgramAppId)->first();
        $miniProgramApp = $openPlatform->miniProgram($miniProgramAppId, $miniProgram->authorizer_refresh_token);
        $response = $miniProgramApp->code->setSupportVersion(
            request()->input('version')
        );

        return $this->response->withArray(['data' => $response]);
    }

    public function supportVersion($componentAppId, $miniProgramAppId)
    {
        $config = Component::getConfig($componentAppId);
        $openPlatform = Factory::openPlatform($config);
        $miniProgram = MiniProgram::where('app_id', $miniProgramAppId)->first();
        $miniProgramApp = $openPlatform->miniProgram($miniProgramAppId, $miniProgram->authorizer_refresh_token);
        $response = $miniProgramApp->code->getSupportVersion();

        return $this->response->withArray(['data' => $response]);
    }
}
