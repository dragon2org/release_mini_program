<?php
/**
 * Created by PhpStorm.
 * User: harlen-mac
 * Date: 2018/12/4
 * Time: 上午1:39
 */

namespace App\Http\Controllers\Api\V1;


use App\Exceptions\UnprocessableEntityHttpException;
use App\Facades\ReleaseFacade;
use App\Helpers\Utils;
use App\Http\Requests\BindMiniProgramTester;
use App\Http\Requests\GetBindMiniProgramUri;
use App\Http\Requests\GetMiniProgramSessionKey;
use App\Http\Requests\MiniProgramDecrypt;
use App\Http\Requests\PutExtJson;
use App\Http\Requests\PutMiniProgramInfo;
use App\Http\Requests\UnbindMiniProgramTester;
use App\Http\Requests\UpdateMiniProgramTag;
use App\Http\Transformer\MiniProgramListTransformer;
use App\Http\Transformer\TesterListTransformer;
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
     *     path="/v1/component/{componentAppId}/bind_url",
     *     summary="添加(绑定)小程序",
     *     tags={"小程序管理"},
     *     description="管理三方平台. 直接跳转这个地址",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="Content-Type",
     *         in="header",
     *         required=true,
     *         type="string",
     *         enum={"application/json"}
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         type="object",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="body",
     *                 type="object",
     *                 required={"redirect_uri", "inner_name", "company_id", "type"},
     *                 @SWG\Property(property="redirect_uri", type="string", description="授权成功的跳转地址"),
     *                 @SWG\Property(property="inner_name", type="string", description="别名"),
     *                 @SWG\Property(property="company_id", type="integer", description="公司id"),
     *                 @SWG\Property(property="biz_appid", type="string", description="指定要绑定的app_id"),
     *                 @SWG\Property(property="type", type="string", description="生成类型:移动微信端: mobile；电脑端:pc"),
     *             ),
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="",
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
     *                 @SWG\Property(property="uri", type="string", description="授权链接")
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
        $uri = ReleaseFacade::service()->getBindUri();

//        return response('', 302, [
//            'Location' => $uri,
//            'referer' => $uri
//        ]);
        return view('authorize_boot_page', ['uri' => urldecode($uri)]);
    }

    public function bindCallback()
    {
        return ReleaseFacade::service()->bindCallback();
    }

    /**
     * @SWG\Get(
     *     path="/v1/component/{componentAppId}/mini_program",
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
     *                 @SWG\Items(ref="#/definitions/MiniProgramList")
     *             ),
     *         )
     *     )
     * )
     */
    public function index($componentId)
    {
        $componentId = ReleaseFacade::service()->component->component_id;
        $items = MiniProgram::where(['component_id'=> $componentId])->paginate(Utils::pageSize());

        return $this->response->withCollection($items, new MiniProgramListTransformer());
    }


    /**
     * @SWG\Get(
     *     path="/v1/component/{componentAppId}/mini_program/{miniProgramAppId}",
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
        $item = MiniProgram::where('app_id', $miniProgramAppId)->firstOrFail();
        return $this->response->withItem($item, new MiniProgramTransformer());
    }


    /**
     * @SWG\Put(
     *     path="/v1/component/{componentAppId}/mini_program/{miniProgramAppId}",
     *     summary="更新小程序信息",
     *     tags={"小程序管理"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="Content-Type",
     *         in="header",
     *         required=true,
     *         type="string",
     *         enum={"application/json"}
     *     ),
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
     *         name="body",
     *         in="body",
     *         required=true,
     *         type="object",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="body",
     *                 type="object",
     *                 required={"inner_name", "inner_desc"},
     *                 @SWG\Property(property="inner_name", type="string", description="内部名称"),
     *                 @SWG\Property(property="inner_desc", type="string", description="内部描述"),
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
        $miniProgram = MiniProgram::where('component_id', ReleaseFacade::service()->component->component_id)
            ->where('app_id', $miniProgramAppId)
            ->firstOrFail();

        if($request->inner_name) $miniProgram->inner_name = $request->inner_name;
        if($request->inner_desc) $miniProgram->inner_desc = $request->inner_desc;
        $miniProgram->save();

        return $this->response->withItem($miniProgram, new MiniProgramTransformer());
    }

    /**
     * @SWG\Delete(
     *     path="/v1/component/{componentAppId}/mini_program/{miniProgramAppId}",
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
        $miniProgram = MiniProgram::where('component_id', ReleaseFacade::service()->component->component_id)
            ->where('app_id', $miniProgramAppId)
            ->firstOrFail();

        $miniProgram->delete();

        return $this->response->withArray();
    }

    /**
     * @SWG\Get(
     *     path="/v1/component/{componentAppId}/mini_program/{miniProgramAppId}/tester",
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
     *                 @SWG\Items(ref="#/definitions/Tester")
     *             ),
     *         )
     *     )
     * )
     */
    public function tester()
    {
        $response = ReleaseFacade::service()->getTester();

        return $this->response->withCollection($response, new TesterListTransformer());
    }

    /**
     * @SWG\Post(
     *     path="/v1/component/{componentAppId}/mini_program/{miniProgram}/tester",
     *     summary="绑定体验者",
     *     tags={"小程序管理-成员管理"},
     *     description="绑定体验者",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="Content-Type",
     *         in="header",
     *         required=true,
     *         type="string",
     *         enum={"application/json"}
     *     ),
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
     *         name="body",
     *         in="body",
     *         required=true,
     *         type="object",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="body",
     *                 type="object",
     *                 required={"wechat_id"},
     *                 @SWG\Property(property="wechat_id", type="string", description="微信id"),
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
        $response = ReleaseFacade::service()->bindTester(request()->input('wechat_id'));

        return $this->response->withArray(['data' => $response]);
    }

    /**
     * @SWG\Delete(
     *     path="/v1/component/{componentAppId}/mini_program/{miniProgram}/tester",
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
     *         name="body",
     *         in="body",
     *         required=true,
     *         type="object",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="body",
     *                 type="object",
     *                 required={"wechat_id"},
     *                 @SWG\Property(property="wechat_id", type="string", description="微信id/userstr"),
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
    public function unbindTester(UnbindMiniProgramTester $request)
    {
        $response = ReleaseFacade::service()->unbindTester(request()->input('wechat_id'));

        return $this->response->withArray();
    }

    /**
     * @SWG\Post(
     *     path="/v1/component/{componentAppId}/mini_program/{miniProgramAppId}/session_key",
     *     summary="code换小程序session_key",
     *     tags={"小程序管理"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="Content-Type",
     *         in="header",
     *         required=true,
     *         type="string",
     *         enum={"application/json"}
     *     ),
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
     *         name="body",
     *         in="body",
     *         required=true,
     *         type="object",
     *         @SWG\Schema(
     *             required={"code"},
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
        $response = ReleaseFacade::service()->sessionKey(request()->input('code'));
        return $this->response->withArray($response);
    }

    /**
     * @SWG\Post(
     *     path="/v1/component/{componentAppId}/mini_program/{miniProgramAppId}/decrypt",
     *     summary="小程序数据解密",
     *     tags={"小程序管理"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="Content-Type",
     *         in="header",
     *         required=true,
     *         type="string",
     *         enum={"application/json"}
     *     ),
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
     *         name="body",
     *         in="body",
     *         required=true,
     *         type="object",
     *         @SWG\Schema(
     *             required={"jscode", "encryptedData", "iv"},
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
        $response = ReleaseFacade::service()->decryptData(request()->input('jscode'), request()->input('iv'), request()->input('encryptedData'));

        return $this->response->withArray(['data'=> $response]);
    }
    /**
     * @SWG\Get(
     *     path="/v1/component/{componentAppId}/mini_program/{miniProgramAppId}/access_token",
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
        return $this->response->withArray(['data' => ReleaseFacade::service()->getAccessToken()]);
    }

    /**
     * @SWG\Put(
     *     path="/v1/component/{componentAppId}/mini_program/{miniProgramAppId}/tag",
     *     summary="更新小程序绑定的模板的tag(绑定到指定模板的内部二级版本)",
     *     tags={"小程序管理"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="Content-Type",
     *         in="header",
     *         required=true,
     *         type="string",
     *         enum={"application/json"}
     *     ),
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
     *         name="body",
     *         in="body",
     *         required=true,
     *         type="object",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="body",
     *                 type="object",
     *                 required={"tag"},
     *                 @SWG\Property(property="tag", type="string", description="模板内部版本; 英文字母开头", minLength=1, maxLength=45, pattern="^[a-zA-Z]+[a-zA-Z0-9\-\_]{1,45}$"),
     *             ),
     *         )
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
     *                 @SWG\Property(property="tag", type="string", description="模板内部版本"),
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
    public function tag($componentAppId, $miniProgramAppId, UpdateMiniProgramTag $request)
    {
        $miniProgram = ReleaseFacade::service()->miniProgram;
        $miniProgram->tag = $request->tag;
        $miniProgram->save();

        return $this->response->withArray([
            'data' => $miniProgram->tag
        ]);
    }
    /**
     * @SWG\Get(
     *     path="/v1/component/{componentAppId}/mini_program/{miniProgramAppId}/config/ext_json",
     *     summary="更新小程序最高优先级的-ext_json",
     *     tags={"三方平台管理"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="Content-Type",
     *         in="header",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="componentAppId",
     *         in="path",
     *         required=true,
     *         type="string",
     *         description="微信三方平台appId",
     *     ),
     *     @SWG\Parameter(
     *         name="miniProgramAppId",
     *         in="path",
     *         required=true,
     *         type="string",
     *         description="小程序appiD",
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         type="object",
     *         @SWG\Schema(
     *             required={"ext_json"},
     *             @SWG\Property(
     *                 property="ext_json",
     *                 type="string",
     *                 description="json字符串",
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="成功返回",
     *         ref="$/responses/200",
     *         @SWG\Schema(
     *             required={"ext_json"},
     *             @SWG\Property(
     *                 property="ext_json",
     *                 type="string",
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
    public function getExtJson()
    {
        return $this->response->withArray([
            'data' => [
                'ext_json' => json_encode(ReleaseFacade::service()->miniProgram->getExtJson())
            ]
        ]);

    }
    /**
     * @SWG\Put(
     *     path="/v1/component/{componentAppId}/mini_program/{miniProgramAppId}/config/ext_json",
     *     summary="更新小程序最高优先级的-ext_json",
     *     tags={"三方平台管理"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="Content-Type",
     *         in="header",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="componentAppId",
     *         in="path",
     *         required=true,
     *         type="string",
     *         description="微信三方平台appId",
     *     ),
     *     @SWG\Parameter(
     *         name="miniProgramAppId",
     *         in="path",
     *         required=true,
     *         type="string",
     *         description="小程序appiD",
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         type="object",
     *         @SWG\Schema(
     *             required={"ext_json"},
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
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="处理失败的返回",
     *         ref="$/responses/422",
     *     ),
     * )
     */
    public function putExtJson(PutExtJson $request)
    {
        return $this->response->withArray([
            'data' => [
                'ext_json' => ReleaseFacade::service()->miniProgram->updateExtJson(request()->input('ext_json'))
            ]
        ]);
    }

}
