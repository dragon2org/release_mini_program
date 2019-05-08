<?php
/**
 * Created by PhpStorm.
 * User: harlen-mac
 * Date: 2018/12/4
 * Time: 上午1:50
 */

namespace App\Http\Controllers\Api\V1;


use App\Http\ApiResponse;
use App\Http\Requests\CodeAudit;
use App\Http\Requests\CodeCommit;
use App\Http\Requests\GetTestQrcode;
use App\Http\Requests\SetSupportVersion;
use App\Models\Component;
use App\Models\ComponentExt;
use App\Models\MiniProgram;
use App\Services\MiniProgramService;
use EasyWeChat\Factory;

class CodeController extends Controller
{
    public function __construct(ApiResponse $response)
    {
        parent::__construct($response);
    }

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
     *             ),
     *             @SWG\Property(
     *                 property="ext_json",
     *                 type="string",
     *                 description="自定义ext_json; json字符串",
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
    public function commit(CodeCommit $request)
    {
        $response = app('dhb.component.core')->commit(
            request()->input('template_id'),
            request()->input('user_version'),
            request()->input('ext_json'));

        return $this->response->withArray(['data' => $response]);
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
     *                 description=""
     *             )
     *             @SWG\Property(
     *                 property="data",
     *                 type="object",
     *                 @SWG\Property(property="qrcode", type="string", description="base64编码的"),
     *             )
     *         )
     *     )
     * )
     */
    public function qrcode(GetTestQrcode $request)
    {
        $response = app('dhb.component.core')->getQrCode(request()->input('path'));

        return $this->response->withArray([
            'data' => [
                'qrcode' => base64_encode($response)
            ]
        ]);
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
    public function category()
    {
        $response = app('dhb.component.core')->getCategory();

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
    public function page()
    {
        $response = app('dhb.component.core')->getPage();

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
    public function audit(CodeAudit $request)
    {
        $response = app('dhb.component.core')->audit(request()->input('item_list'));

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
        $response = app('dhb.component.core')->getAuditStatus($audit);

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
    public function lastAuditStatus()
    {
        $response = app('dhb.component.core')->getLatestAuditStatus();

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

    public function release()
    {
        $response = app('dhb.component.core')->release();

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
    public function visitStatus()
    {
        $response = app('dhb.component.core')->setVisitStatus(request()->input('action', 'close'));

        return $this->response->withArray(['data'=> $response]);
    }

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
    public function revertCodeRelease()
    {
        $response = app('dhb.component.core')->revertCodeRelease();

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
    public function SetSupportVersion(SetSupportVersion $request)
    {
        $response = app('dhb.component.core')->setSupportVersion(
            request()->input('version')
        );

        return $this->response->withArray(['data' => $response]);
    }

    public function supportVersion()
    {
        $response = app('dhb.component.core')->getSupportVersion(
            request()->input('version')
        );

        return $this->response->withArray(['data' => $response]);
    }
}
