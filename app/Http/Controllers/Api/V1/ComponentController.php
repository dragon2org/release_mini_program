<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\ComponentRequest;
use App\Http\Transformer\ComponentTransformer;
use App\Models\Component;

class ComponentController extends Controller
{
    /**
     * @SWG\Post(
     *     path="/component",
     *     summary="平台注册",
     *     tags={"三方平台管理"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="data",
     *         in="body",
     *         description="注册表单数据",
     *         required=true,
     *         type="object",
     *         @SWG\Schema(ref="#/definitions/Component")
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="处理成功返回",
     *         ref="$/responses/200",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 type="object",
     *                 description="返回数据包",
     *                 @SWG\Property(property="authorization_launch_page_domain", type="string", description="三方平台填写信息:登录授权的发起页域名"),
     *                 @SWG\Property(property="authorization_event_nofify_url", type="string", description="三方平台填写信息:授权事件接收URL"),
     *                 @SWG\Property(property="msg_event_nofify_url", type="string", description="三方平台填写信息:消息与事件接收URL"),
     *             ),
     *         )
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="处理失败的返回",
     *         ref="$/responses/422",
     *     ),
     * )
     */

    public function create(ComponentRequest $request, Component $component)
    {
        $component->fill($request->all());
        $validateFile = $request->input('validate');
        $component->validate_filename = $validateFile['filename'];
        $component->validate_content = $validateFile['content'];
        $component->save();

        return $this->response->withArray(
            ['data' => $this->response->transformatItem($component, new ComponentTransformer($component))]
        );
    }

    /**
     * @SWG\Get(
     *     path="/component/:componentAppId",
     *     summary="获取平台信息",
     *     tags={"三方平台管理"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response=200,
     *         description="成功返回",
     *         ref="$/responses/200",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 type="Object",
     *                 ref="#/definitions/Component"
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="处理失败的返回",
     *         ref="$/responses/422",
     *     ),
     * )
     */


    /**
     * @SWG\Put(
     *     path="/component/:componentAppId",
     *     summary="更新平台信息",
     *     tags={"三方平台管理"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="data",
     *         in="body",
     *         description="表单数据",
     *         required=true,
     *         type="object",
     *         @SWG\Schema(ref="#/definitions/Component")
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="成功返回",
     *         ref="$/responses/200",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 type="Object",
     *                 ref="#/definitions/Component"
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="处理失败的返回",
     *         ref="$/responses/422",
     *     ),
     * )
     */

    /**
     * @SWG\Put(
     *     path="/component/:componentAppId/config/domain",
     *     summary="更新平台发版配置-变更服务域名",
     *     tags={"三方平台管理"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="data",
     *         in="body",
     *         description="表单数据",
     *         required=true,
     *         type="object",
     *         @SWG\Schema(
     *             @SWG\Property(property="action", type="string", description="add添加, delete删除, set覆盖, get获取。当参数是get时不需要填四个域名字段"),
     *             @SWG\Property(property="requestdomain", type="array", @SWG\Items(), description="request合法域名，当action参数是get时不需要此字段"),
     *             @SWG\Property(property="wsrequestdomain", type="array", @SWG\Items(),  description="socke合法域名，当action参数是get时不需要此字段"),
     *             @SWG\Property(property="uploaddomain", type="array", @SWG\Items(),  description="uploadFile合法域名，当action参数是get时不需要此字段"),
     *             @SWG\Property(property="downloaddomain", type="array", @SWG\Items(),  description="downloadFile合法域名，当action参数是get时不需要此字段"),
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="成功返回",
     *         ref="$/responses/200",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 type="Object",
     *                 ref="#/definitions/MiniProgramConfig"
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="处理失败的返回",
     *         ref="$/responses/422",
     *     ),
     * )
     */

    /**
     * @SWG\Put(
     *     path="/component/:componentAppId/config/web_view_domain",
     *     summary="更新平台发版配置-变更业务域名",
     *     tags={"三方平台管理"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="data",
     *         in="body",
     *         description="表单数据",
     *         required=true,
     *         type="object",
     *         @SWG\Schema(
     *             @SWG\Property(property="action", type="string", description="add添加, delete删除, set覆盖, get获取。当参数是get时不需要填四个域名字段"),
     *             @SWG\Property(property="webviewdomain", type="array", @SWG\Items(), description="webviewdomain合法域名，当action参数是get时不需要此字段"),
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="成功返回",
     *         ref="$/responses/200",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 type="Object",
     *                 ref="#/definitions/MiniProgramConfig"
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="处理失败的返回",
     *         ref="$/responses/422",
     *     ),
     * )
     */

    /**
     * @SWG\Put(
     *     path="/component/:componentAppId/config/tester",
     *     summary="更新平台发版配置-体验者",
     *     tags={"三方平台管理"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="data",
     *         in="body",
     *         description="表单数据",
     *         required=true,
     *         type="object",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="wechatId",
     *                 type="array",
     *                 @SWG\Items()
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="成功返回",
     *         ref="$/responses/200",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 type="Object",
     *                 ref="#/definitions/MiniProgramConfig"
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="处理失败的返回",
     *         ref="$/responses/422",
     *     ),
     * )
     */

    /**
     * @SWG\Put(
     *     path="/component/:componentAppId/config/visit_status",
     *     summary="更新平台发版配置-设置代码可见状态",
     *     tags={"三方平台管理"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="data",
     *         in="body",
     *         description="表单数据",
     *         required=true,
     *         type="object",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="visit_status",
     *                 type="string",
     *                 description="设置可访问状态，发布后默认可访问，close为不可见，open为可见",
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="成功返回",
     *         ref="$/responses/200",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 type="Object",
     *                 ref="#/definitions/MiniProgramConfig"
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="处理失败的返回",
     *         ref="$/responses/422",
     *     ),
     * )
     */

    /**
     * @SWG\Put(
     *     path="/component/:componentAppId/config/support_version",
     *     summary="更新平台发版配置-最低基础库版本",
     *     tags={"三方平台管理"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="data",
     *         in="body",
     *         description="表单数据",
     *         required=true,
     *         type="object",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="version",
     *                 type="string",
     *                 description="版本.如:1.0.0",
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="成功返回",
     *         ref="$/responses/200",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 type="Object",
     *                 ref="#/definitions/MiniProgramConfig"
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="处理失败的返回",
     *         ref="$/responses/422",
     *     ),
     * )
     */

    /**
     * @SWG\Put(
     *     path="/component/:componentAppId/config/sync",
     *     summary="批量推送平台配置到微信小程序",
     *     tags={"三方平台管理"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="data",
     *         in="body",
     *         description="表单数据",
     *         required=true,
     *         type="object",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="category",
     *                 type="string",
     *                 description="推送配置类型: domain, web_view_domain, tester, visit_status, support_version; all全部推送",
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="成功返回",
     *         ref="$/responses/200",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 type="Object",
     *                 ref="#/definitions/MiniProgramConfig"
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="处理失败的返回",
     *         ref="$/responses/422",
     *     ),
     * )
     */

    /**
     * @SWG\Put(
     *     path="/component/:componentAppId/page_list",
     *     summary="更新平台发版配置-ext_json",
     *     tags={"三方平台管理"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="data",
     *         in="body",
     *         description="表单数据",
     *         required=true,
     *         type="object",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="ext_json",
     *                 type="string",
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="成功返回",
     *         ref="$/responses/200",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 type="Object",
     *                 ref="#/definitions/MiniProgramConfig"
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="处理失败的返回",
     *         ref="$/responses/422",
     *     ),
     * )
     */


    /**
     * @SWG\get(
     *     path="/component/:componentAppId/config",
     *     summary="获取平台发版配置",
     *     tags={"三方平台管理"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response=200,
     *         description="成功返回",
     *         ref="$/responses/200",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 type="Object",
     *                 ref="#/definitions/MiniProgramConfig"
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="处理失败的返回",
     *         ref="$/responses/422",
     *     ),
     * )
     */


    /**
     * @SWG\Post(
     *     path="/component/:componentAppId/template/:templateId/release",
     *     summary="批量发布",
     *     tags={"三方平台管理"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="componentAppId",
     *         in="path",
     *         description="三方平台AppID",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="templateId",
     *         in="path",
     *         description="模板id",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="data",
     *         in="body",
     *         description="注册表单数据",
     *         required=true,
     *         type="object",
     *         @SWG\Schema(ref="#/definitions/MiniProgramConfig")
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="成功返回",
     *         ref="$/responses/200",
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="处理失败的返回",
     *         ref="$/responses/422",
     *     ),
     * )
     */

    /**
     * @SWG\Get(
     *     path="/component/{componentAppId}/component_verify_ticket",
     *     summary="获取三方平台 component_verify_ticket",
     *     tags={"测试接口"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="componentAppId",
     *         in="path",
     *         description="三方平台AppID",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="处理成功返回",
     *         ref="$/responses/200",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 type="object",
     *                 description="返回数据包",
     *                 @SWG\Property(property="component_verify_ticket", type="string", description="用于获取第三方平台接口调用凭据"),
     *             ),
     *         )
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="处理失败的返回",
     *         ref="$/responses/422",
     *     ),
     * )
     */

    public function componentVerifyTicket($componentAppId)
    {
        return $this->response->withArray(['data' => [
                'component_verify_ticket' => Component::getConfig($componentAppId)['component_verify_ticket']
            ]]
        );
    }

    /**
     * @SWG\Get(
     *     path="/component/{componentAppId}/component_access_token",
     *     summary="获取三方平台 component_access_token",
     *     tags={"测试接口"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="componentAppId",
     *         in="path",
     *         description="三方平台AppID",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="处理成功返回",
     *         ref="$/responses/200",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 type="object",
     *                 description="返回数据包",
     *                 @SWG\Property(property="component_access_token", type="string", description="三方平台自己的接口调用凭据"),
     *             ),
     *         )
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="处理失败的返回",
     *         ref="$/responses/422",
     *     ),
     * )
     */

    /**
     * @SWG\Get(
     *     path="/component/:componentAppId}/mini_program/:miniProgramAppId/access_token",
     *     summary="获取小程序的access_token",
     *     tags={"测试接口"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="componentAppId",
     *         in="path",
     *         description="三方平台AppID",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="miniProgramAppId",
     *         in="path",
     *         description="小程序AppId",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="处理成功返回",
     *         ref="$/responses/200",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 type="object",
     *                 description="返回数据包",
     *                 @SWG\Property(property="access_token", type="string", description="三方平台的小程序的access_token"),
     *             ),
     *         )
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="处理失败的返回",
     *         ref="$/responses/422",
     *     ),
     * )
     */
}
