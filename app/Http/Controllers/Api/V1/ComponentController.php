<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\UnprocessableEntityHttpException;
use App\Exceptions\WechatGatewayException;
use App\Http\ApiResponse;
use App\Http\Requests\RegisterComponent;
use App\Http\Requests\UpdateComponent;
use App\Http\Requests\UpdateReleaseConfigSupportVersion;
use App\Http\Requests\UpdateReleaseConfigTester;
use App\Http\Requests\UpdateReleaseConfigVisitStatus;
use App\Http\Requests\UpdateReleaseConfigWebViewDomain;
use App\Http\Requests\UpdateReleaseExtJson;
use App\Http\Requests\UpdateReleaseConfigDomain;
use App\Http\Requests\UpdateReleaseConfigWebDomain;
use App\Http\Requests\UploadValidateFile;
use App\Http\Transformer\ComponentDetailTransformer;
use App\Http\Transformer\ComponentTransformer;
use App\Models\Component;
use App\Models\ValidateFile;
use App\Services\ComponentService;
use Illuminate\Support\Str;

class ComponentController extends Controller
{
    /**
     * @var \App\Services\ComponentService
     */
    protected $service;

    /**
     * ComponentController constructor.
     * @param ApiResponse $response
     * @param ComponentService $service
     */
    public function __construct(ApiResponse $response, ComponentService $service)
    {
        parent::__construct($response);
        $this->service = $service;
    }

    /**
     * @SWG\Post(
     *     path="/component/create_before",
     *     summary="设置平台验证文件，并获取白名单ip等",
     *     tags={"三方平台管理"},
     *     description="设置平台验证文件，并获取白名单ip. 测试使用接口",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="data",
     *         in="body",
     *         description="注册表单数据",
     *         required=true,
     *         type="object",
     *         @SWG\Schema(ref="#/definitions/ComponentCreateBefore")
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
     *                 @SWG\Property(property="authorization_launch_page_domain", type="string", description="三方平台填写信息/业务域名/服务域名"),
     *                 @SWG\Property(property="authorization_event_notify_url", type="string", description="三方平台填写信息:授权事件接收URL。临时填写信息。在开放平台创建成功之后，再在平台填写补充信息，获取正确的信息"),
     *                 @SWG\Property(property="msg_event_nofify_url", type="string", description="三方平台填写信息:授权事件接收URL。临时填写信息。在开放平台创建成功之后，再在平台填写补充信息，获取正确的信息"),
     *                 @SWG\Property(property="white_list_ip", type="string", description="三方平台填写信息:白名单IP地址列表"),
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
    public function createBefore(UploadValidateFile $request)
    {
        throw new WechatGatewayException('版本输入错误', 85015);
        $filename = request()->input('validate.filename');
        $content = request()->input('validate.content');

        ValidateFile::firstOrCreate(['filename' => $filename], ['content' => $content]);

        $component = (new Component());
        //生成token和ase-key;
        $component->verify_token = base64_encode($filename);
        $component->aes_key =  Str::random(43);

        return $this->response->withArray([
            'data' => [
                'white_list_ip' => $_SERVER['SERVER_ADDR'],
                'aes_key' => $component->aes_key,
                'verify_token' => $component->verify_token,
                "authorization_launch_page_domain" => $component->getAuthorizationLaunchPageDomain(),
                "authorization_event_notify_url" => $component->getAuthorizationEventNotifyUrl(),
                "msg_event_notify_url" => $component->getMsgEventNotifyUrl(),
            ],
        ]);
    }

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
     *                 @SWG\Property(property="authorization_event_notify_url", type="string", description="三方平台填写信息:授权事件接收URL"),
     *                 @SWG\Property(property="msg_event_nofify_url", type="string", description="三方平台填写信息:消息与事件接收URL"),
     *                 @SWG\Property(property="white_list_ip", type="string", description="三方平台填写信息:白名单IP地址列表"),
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

    public function create(RegisterComponent $request)
    {
        $component = $this->service->register(request()->all());

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
    public function show($componentAppId)
    {
        $component = (new Component())->where('app_id', $componentAppId)->first();
        if(!isset($component)){
            throw new UnprocessableEntityHttpException(trans('平台不存在'));
        }

        return $this->response->withArray(['data' => $this->response->transformatItem($component, new ComponentDetailTransformer($component))]);
    }

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
    public function update(UpdateComponent $request, $componentAppId)
    {
        $input = $request->all();
        $input['app_id'] = $componentAppId;

        $component = $this->service->updateComponent($input);

        return $this->response->withArray(
            ['data' => $this->response->transformatItem($component, new ComponentDetailTransformer($component))]
        );
    }

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
     *             @SWG\Property(property="action", type="string", description="set覆盖,当前版本仅支持set模式"),
     *             @SWG\Property(property="requestdomain", type="array", @SWG\Items(), description="request合法域名"),
     *             @SWG\Property(property="wsrequestdomain", type="array", @SWG\Items(),  description="socke合法域名"),
     *             @SWG\Property(property="uploaddomain", type="array", @SWG\Items(),  description="uploadFile合法域名"),
     *             @SWG\Property(property="downloaddomain", type="array", @SWG\Items(),  description="downloadFile合法域名"),
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
     * @param UpdateReleaseConfigDomain $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse
     */
    public function domain(UpdateReleaseConfigDomain $request)
    {
        $config = $this->service->updateDomain(request()->all());
        $config['ext_json'] = json_encode($config['ext_json']);

        return $this->response->withArray(['data' => $config ]);
    }
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
     *             @SWG\Property(property="action", type="string", description="set覆盖,仅支持set"),
     *             @SWG\Property(property="webviewdomain", type="array", @SWG\Items(), description="webviewdomain合法域名"),
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
    public function webViewDomain(UpdateReleaseConfigWebViewDomain $request)
    {
        $config = $this->service->updateWebViewDomain(request()->all());
        $config['ext_json'] = json_encode($config['ext_json']);

        return $this->response->withArray(['data' => $config ]);
    }
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
    public function tester(UpdateReleaseConfigTester $request)
    {
        $config = $this->service->updateTester(request()->all());
        $config['ext_json'] = json_encode($config['ext_json']);

        return $this->response->withArray(['data' => $config ]);
    }
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
    public function visitStatus(UpdateReleaseConfigVisitStatus $request)
    {
        $config = $this->service->updateVisitStatus(request()->all());
        $config['ext_json'] = json_encode($config['ext_json']);

        return $this->response->withArray(['data' => $config ]);
    }
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
    public function supportVersion(UpdateReleaseConfigSupportVersion $request)
    {
        $config = $this->service->updateReleaseConfig(request()->all());
        $config['ext_json'] = json_encode($config['ext_json']);

        return $this->response->withArray(['data' => $config ]);
    }

    /**
     * @SWG\Put(
     *     path="/component/{componentAppId}/config/ext_json",
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
    public function extJson(UpdateReleaseExtJson $request, $componentAppId)
    {
        $config = $this->service->updateExtJson(request()->input('ext_json'));
        $config['ext_json'] = json_encode($config['ext_json']);

        return $this->response->withArray(['data' => $config ]);
    }

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

    public function configSync()
    {
        app('dhb.component.core')->configSync();

        return $this->response->withArray();
    }

    /**
     * @SWG\get(
     *     path="/component/{componentAppId}/config",
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
    public function config()
    {
        $config = $this->service->getReleaseConfig();
        $config['ext_json'] = json_encode($config['ext_json']);

        return $this->response->withArray(['data' => $config ]);
    }

    /**
     * @SWG\Get(
     *     path="/component/{componentAppId}/component_verify_ticket",
     *     summary="获取三方平台 component_verify_ticket",
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
        try {
            return $this->response->withArray(['data' => [
                    'component_verify_ticket' => app('dhb.component.core')->component->verify_ticket
                ]]
            );
        } catch (UnprocessableEntityHttpException $e) {
            return $this->response->withArray([
                'status' => 'F',
                'message' => $e->getMessage()
            ]);
        }

    }

    /**
     * @SWG\Get(
     *     path="/component/{componentAppId}/component_access_token",
     *     summary="获取三方平台 component_access_token",
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
     *                 @SWG\Property(property="expires_in", type="integer", description="有效期"),
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
    public function componentAccessToken()
    {
        try {
            return $this->response->withArray([
                    'data' =>app('dhb.component.core')->openPlatform->access_token->getToken()
                ]
            );
        } catch (\EasyWeChat\Kernel\Exceptions\HttpException $e) {
            return $this->response->withArray([
                'status' => 'F',
                'message' => $e->getMessage()
            ]);
        } catch (UnprocessableEntityHttpException $e) {
            return $this->response->withArray([
                'status' => 'F',
                'message' => $e->getMessage()
            ]);
        }

    }


}
