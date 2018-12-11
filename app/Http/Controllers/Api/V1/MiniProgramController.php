<?php
/**
 * Created by PhpStorm.
 * User: harlen-mac
 * Date: 2018/12/4
 * Time: 上午1:39
 */

namespace App\Http\Controllers\Api\V1;


use App\Models\Component;
use App\Models\MiniProgram;
use EasyWeChat\Factory;

class MiniProgramController extends Controller
{
    /**
     * @SWG\Get(
     *     path="/component/{componentAppId}/bind_url",
     *     summary="获取小程序授权地址",
     *     tags={"小程序管理"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="redirect_url",
     *         type="string",
     *         required=false,
     *         in="query",
     *         description="授权成功的通知地址",
     *     ),
     *     @SWG\Parameter(
     *         name="type",
     *         type="string",
     *         required=false,
     *         in="query",
     *         description="生成类型:移动端: mobile；电脑端:pc",
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
    public function bindUrl($componentAppId)
    {
        $config = Component::getConfig($componentAppId);
        $openPlatform = Factory::openPlatform($config);
        $callbackUrl = Route('MiniProgramBindCallback', [
            'componentAppId'=> $componentAppId,
            'inner_name' => '',
            'inner_desc' => '',
            'inner_key' => '',
        ]);
        $uri = request()->query('type') === 'mobile' ? $openPlatform->getPreAuthorizationUrl($callbackUrl) : $openPlatform->getMobilePreAuthorizationUrl($callbackUrl);

        return $this->response->withArray(['data' => [
                'uri' => $uri
            ]]
        );
    }

    public function bindCallback($componentAppId)
    {
        $config = Component::getConfig($componentAppId);
        $openPlatform = Factory::openPlatform($config);
        $res = $openPlatform->handleAuthorize();

        //TODO::判断function_info
        $miniProgram = new MiniProgram();
        $miniProgram->component_app_id = $componentAppId;
        $miniProgram->company_id = request()->query('company_id');
        $miniProgram->inner_name = request()->query('company_id');
        $miniProgram->inner_desc = request()->query('company_id');
        $miniProgram->inner_key = request()->query('inner_key') ??  $res['authorizer_appid'];
        $miniProgram->authorizer_refresh_token = $res['authorizer_refresh_token'];
        $miniProgram->save();

        //拉取基础信息
        $info = $openPlatform->getAuthorizer($res['authorizer_appid']);
        $miniProgram->nick_name = $info['nick_name'];
        $miniProgram->head_img = $info['head_img'];
        $miniProgram->user_name = $info['user_name'];
        $miniProgram->principal_name = $info['principal_name'];
        $miniProgram->qrcode_url = $info['qrcode_url'];
        $miniProgram->save();

        echo 'success';
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


/**
 * @SWG\get(
 *     path="/component/{componentAppId}/mini_program/{miniProgramAppId}/session_key",
 *     summary="获取小程序session_key",
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
 *                     property="session_key",
 *                     type="string",
 *                     description="小程序seesion_key"
 *                 ),
 *             )
 *         )
 *     )
 * )
 */


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
 *         name="iv",
 *         in="formData",
 *         description="加密数据",
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
 *                 description="解密返回的数据"
 *             )
 *         )
 *     )
 * )
 */


}
