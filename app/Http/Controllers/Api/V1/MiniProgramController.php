<?php
/**
 * Created by PhpStorm.
 * User: harlen-mac
 * Date: 2018/12/4
 * Time: 上午1:39
 */

namespace App\Http\Controllers\Api\V1;


use App\Exceptions\UnprocessableEntityHttpException;
use App\Http\Requests\BindMiniProgramTester;
use App\Http\Requests\GetBindMiniProgramUri;
use App\Http\Requests\GetMiniProgramSessionKey;
use App\Http\Requests\MiniProgramDecrypt;
use App\Http\Requests\PutMiniProgramInfo;
use App\Http\Requests\UnbindMiniProgramTester;
use App\Http\Transformer\MiniProgramListTransformer;
use App\Services\MiniProgramService;
use App\Http\ApiResponse;
use App\Http\Transformer\MiniProgramTransformer;
use App\Models\MiniProgram;

class MiniProgramController extends Controller
{
    protected $service;

    public function __construct(ApiResponse $response)
    {
        parent::__construct($response);
        $this->service = new MiniProgramService();
    }

    /**
     * @SWG\Post(
     *     path="/component/{componentAppId}/bind_url",
     *     summary="添加(绑定)小程序",
     *     tags={"小程序管理"},
     *     description="管理三方平台. 直接跳转这个地址",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="redirect_uri",
     *         type="string",
     *         required=false,
     *         in="query",
     *         description="授权成功的跳转地址",
     *     ),
     *     @SWG\Parameter(
     *         name="inner_name",
     *         type="string",
     *         required=false,
     *         in="query",
     *         description="内部名称",
     *     ),
     *     @SWG\Parameter(
     *         name="inner_desc",
     *         type="string",
     *         required=false,
     *         in="query",
     *         description="内部描述",
     *     ),
     *     @SWG\Parameter(
     *         name="company_id",
     *         type="integer",
     *         required=true,
     *         in="query",
     *         description="公司id",
     *     ),
     *     @SWG\Parameter(
     *         name="biz_appid",
     *         type="string",
     *         required=false,
     *         in="query",
     *         description="指定要绑定的app_id",
     *     ),
     *     @SWG\Parameter(
     *         name="type",
     *         type="string",
     *         required=true,
     *         default="pc",
     *         in="query",
     *         description="生成类型:移动微信端: mobile；电脑端:pc",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="成功返回。type为qrcode直接返回图片",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="status",
     *                 type="string",
     *                 default="T",
     *                 description="接口返回状态['T'->成功; 'F'->失败]"
     *             ),
     *             @SWG\Property(
     *                 property="data",
     *                 type="Object",
     *                 @SWG\Property(property="uri", description="授权链接")
     *             )
     *         )
     *     )
     * )
     */
    public function bindUrl(GetBindMiniProgramUri $request, $componentAppId)
    {
        $uri = route('MiniProgramBind', [
            'componentAppId' => $componentAppId,
            'redirect_uri' => $request->input('redirect_uri'),
            'inner_name' => $request->input('inner_name'),
            'inner_desc' => $request->input('inner_desc'),
            'company_id' => $request->input('company_id'),
        ], true);

        return $this->response->withArray([
            'data' => [
                'uri' => $uri
            ]
        ]);
    }

    public function bind()
    {
        $uri = app('dhb.component.core')->getBindUri();

//        return response('', 302, [
//            'Location' => $uri,
//            'referer' => $uri
//        ]);
        return view('authorize_boot_page', ['uri' => urldecode($uri)]);
    }

    public function bindCallback()
    {
        return app('dhb.component.core')->bindCallback();
    }

    /**
     * @SWG\Get(
     *     path="/component/{componentAppId}/mini_program",
     *     summary="获取已经授权的小程序列表",
     *     tags={"小程序管理"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="第几页，默认第一页",
     *         in="query",
     *         name="page",
     *         required=false,
     *         type="integer",
     *         format="int64",
     *         default="1"
     *     ),
     *     @SWG\Parameter(
     *         description="每页数量，默认为15",
     *         in="query",
     *         name="pageSize",
     *         required=false,
     *         type="integer",
     *         format="int64",
     *         default="5"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="成功返回",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="pagination",
     *                 type="object",
     *                 @SWG\Property(
     *                     property="total",
     *                     type="integer",
     *                     description="总的数据条数 "
     *                 ),
     *                 @SWG\Property(
     *                     property="per_page",
     *                     type="integer",
     *                     description="每页的数据条数"
     *                 ),
     *                 @SWG\Property(
     *                     property="current_page",
     *                     type="integer",
     *                     description="当前是第几页"
     *                 ),
     *                 @SWG\Property(
     *                     property="last_page",
     *                     type="integer",
     *                     description="最大页数"
     *                 ),
     *             ),
     *             @SWG\Property(
     *                 property="status",
     *                 type="string",
     *                 default="T",
     *                 description="接口返回状态['T'->成功; 'F'->失败]"
     *             ),
     *             @SWG\Property(
     *                 property="data",
     *                 type="array",
     *                 @SWG\Items(ref="#/definitions/MiniProgram")
     *             ),
     *         )
     *     )
     * )
     */
    public function index($componentId)
    {
        $componentId = app('dhb.component.core')->component->component_id;
        $items = MiniProgram::where(['component_id'=> $componentId])->paginate();

        return $this->response->withCollection($items, new MiniProgramListTransformer($items));
    }


    /**
     * @SWG\Get(
     *     path="/component/{componentAppId}/mini_program/{miniProgramAppId}",
     *     summary="获取小程序信息",
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
     *                     property="info",
     *                     type="Object",
     *                     ref="#/definitions/MiniProgram"
     *                 ),
     *             )
     *         )
     *     )
     * )
     */
    public function show($componentAppId, $miniProgramAppId)
    {
        $item = (new MiniProgram())->where('app_id', $miniProgramAppId)->first();
        return $this->response->withItem($item, new MiniProgramTransformer($item));
    }


    /**
     * @SWG\Put(
     *     path="/component/{componentAppId}/mini_program/{miniProgramAppId}",
     *     summary="更新小程序信息",
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
     *         description="注册表单数据",
     *         required=true,
     *         type="object",
     *         @SWG\Schema(ref="#/definitions/MiniProgram")
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
     *                     property="info",
     *                     type="Object",
     *                     ref="#/definitions/MiniProgram"
     *                 ),
     *             )
     *         )
     *     )
     * )
     */
    public function update(PutMiniProgramInfo $request, $componentAppId, $miniProgramAppId)
    {
        $miniProgram = (new MiniProgram())
            ->where('component_id', app('dhb.component.core')->component->component_id)
            ->where('app_id', $miniProgramAppId)
            ->first();

        if(!isset($miniProgram)){
            throw new UnprocessableEntityHttpException(trans('小程序不存在'));
        }

        $miniProgram->inner_name = $request->inner_name;
        $miniProgram->inner_desc = $request->inner_desc;
        $miniProgram->save();

        return $this->response->withItem($miniProgram, new MiniProgramTransformer($miniProgram));
    }

    /**
     * @SWG\Delete(
     *     path="/component/{componentAppId}/mini_program/{miniProgramAppId}",
     *     summary="删除小程序",
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
     *             )
     *         )
     *     )
     * )
     */
    public function delete($componentAppId, $miniProgramAppId)
    {
        $miniProgram = (new MiniProgram())
            ->where('component_id', app('dhb.component.core')->component->component_id)
            ->where('app_id', $miniProgramAppId)
            ->where('is_deleted', 0)
            ->first();

        if(!isset($miniProgram)){
            throw new UnprocessableEntityHttpException(trans('小程序不存在'));
        }

        $miniProgram->is_deleted = 1;
        $miniProgram->save();

        return $this->response->withArray();
    }

    /**
     * @SWG\Get(
     *     path="/component/{componentAppId}/mini_program/{miniProgramAppId}/tester",
     *     summary="获取体验者列表",
     *     tags={"小程序管理-成员管理"},
     *     description="获取已经设置了的体验者列表",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="三方平台appId",
     *         in="path",
     *         name="componentAppId",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="小程序appId",
     *         in="path",
     *         name="miniProgramAppId",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="第几页，默认第一页",
     *         in="query",
     *         name="page",
     *         required=false,
     *         type="integer",
     *         format="int64",
     *         default="1"
     *     ),
     *     @SWG\Parameter(
     *         description="每页数量，默认为15",
     *         in="query",
     *         name="pageSize",
     *         required=false,
     *         type="integer",
     *         format="int64",
     *         default="5"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="成功返回",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="pagination",
     *                 type="object",
     *                 @SWG\Property(
     *                     property="total",
     *                     type="integer",
     *                     description="总的数据条数 "
     *                 ),
     *                 @SWG\Property(
     *                     property="per_page",
     *                     type="integer",
     *                     description="每页的数据条数"
     *                 ),
     *                 @SWG\Property(
     *                     property="current_page",
     *                     type="integer",
     *                     description="当前是第几页"
     *                 ),
     *                 @SWG\Property(
     *                     property="last_page",
     *                     type="integer",
     *                     description="最大页数"
     *                 ),
     *             ),
     *             @SWG\Property(
     *                 property="status",
     *                 type="string",
     *                 default="T",
     *                 description="接口返回状态['T'->成功; 'F'->失败]"
     *             ),
     *             @SWG\Property(
     *                 property="data",
     *                 type="array",
     *                 @SWG\Items(ref="#/definitions/Tester")
     *             ),
     *         )
     *     )
     * )
     */
    public function tester()
    {
        $response = app('dhb.component.core')->getTester();

        return $this->response->withArray(['data' => $response]);
    }

    /**
     * @SWG\Post(
     *     path="/component/{componentAppId}/mini_program/{miniProgram}/tester",
     *     summary="绑定体验者",
     *     tags={"小程序管理-成员管理"},
     *     description="绑定体验者",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="三方平台appId",
     *         in="path",
     *         name="componentAppId",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="小程序appId",
     *         in="path",
     *         name="miniProgramAppId",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="data",
     *         in="body",
     *         description="注册表单数据",
     *         required=true,
     *         type="object",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="wechat_id",
     *                 type="string",
     *                 description="wechat_id",
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
     *             ),
     *         )
     *     )
     * )
     */
    public function bindTester(BindMiniProgramTester $request)
    {
        $response = app('dhb.component.core')->bindTester(request()->input('wechat_id'));

        return $this->response->withArray(['data' => $response]);
    }

    /**
     * @SWG\Delete(
     *     path="/component/{componentAppId}/mini_program/{miniProgram}/tester/{wechatid}",
     *     summary="解绑体验者",
     *     tags={"小程序管理-成员管理"},
     *     description="绑定体验者",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="三方平台appId",
     *         in="path",
     *         name="componentAppId",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="小程序appId",
     *         in="path",
     *         name="miniProgramAppId",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="微信号",
     *         in="path",
     *         name="wechatid",
     *         required=true,
     *         type="string"
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
     *         )
     *     )
     * )
     */
    public function unbindTester(UnbindMiniProgramTester $request)
    {
        $response = app('dhb.component.core')->unbindTester(request()->input('wechat_id'));

        return $this->response->withArray();
    }

    /**
     * @SWG\Post(
     *     path="/component/{componentAppId}/mini_program/{miniProgramAppId}/session_key",
     *     summary="code换小程序session_key",
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
     *         description="注册表单数据",
     *         required=true,
     *         type="object",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="code",
     *                 type="string",
     *                 description="jscode"
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
     *             ),
     *             @SWG\Property(
     *                 property="data",
     *                 type="object",
     *                 @SWG\Property(
     *                     property="session_key",
     *                     type="string",
     *                     description="小程序seesion_key"
     *                 ),
     *             )
     *         )
     *     )
     * )
     */
    public function sessionKey(GetMiniProgramSessionKey $request)
    {
        $response = app('dhb.component.core')->sessionKey(request()->input('code'));
        return $this->response->withArray($response);
    }

    /**
     * @SWG\Post(
     *     path="/component/{componentAppId}/mini_program/{miniProgramAppId}/decrypt",
     *     summary="小程序数据解密",
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
     *         description="注册表单数据",
     *         required=true,
     *         type="object",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="jscode",
     *                 type="string",
     *                 description="jscode"
     *             ),
     *             @SWG\Property(
     *                 property="encryptedData",
     *                 type="string",
     *                 description="加密数据"
     *             ),
     *             @SWG\Property(
     *                 property="iv",
     *                 type="string",
     *                 description="加密向量"
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
     *             ),
     *             @SWG\Property(
     *                 property="data",
     *                 type="object",
     *                 description="解密返回的数据"
     *             )
     *         )
     *     )
     * )
     */
    public function decrypt(MiniProgramDecrypt $request)
    {
        $response = app('dhb.component.core')->decryptData(request()->input('jscode'), request()->input('iv'), request()->input('encryptedData'));

        return $this->response->withArray(['data'=> $response]);
    }
    /**
     * @SWG\Get(
     *     path="/component/{componentAppId}/mini_program/{miniProgramAppId}/access_token",
     *     summary="获取小程序的access_token",
     *     tags={"小程序管理"},
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
     *                 @SWG\Property(property="authorizer_access_token", type="string", description="授权方令牌"),
     *                 @SWG\Property(property="expires_in", type="string", description="有效期，为2小时"),
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
    public function accessToken()
    {
        return $this->response->withArray(['data' => app('dhb.component.core')->getAccessToken()]);
    }
}
