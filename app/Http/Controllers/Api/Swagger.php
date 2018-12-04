<?php
/**
 * Created by PhpStorm.
 * User: harlen-mac
 * Date: 2018/12/4
 * Time: 上午12:22
 */

namespace App\Http\Controllers\Api;


/**
 * @SWG\Swagger(
 *     schemes={"http", "https"},
 *     host="http://release.min-program.com",
 *     basePath="/v1",
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="微信小程序发版系统API",
 *         description="",
 *         termsOfService="",
 *         @SWG\Contact(
 *             email="maofei@rsung.com"
 *         )
 *     ),
 *     @SWG\SecurityScheme(
 *         securityDefinition="oauth2", type="oauth2", description="OAuth2授权", flow="password",
 *         tokenUrl="/oauth/token",
 *         scopes={"scope": "暂时不支持scope"}
 *     ),
 *     @SWG\Response(
 *         response="400",
 *         description="INVALID REQUEST - [POST/PUT/PATCH]：用户发出的请求有错误，服务器没有进行新建或修改数据的操作，该操作是幂等的。",
 *         @SWG\Schema(ref="#/definitions/ExceptionReponse")
 *     ),
 *     @SWG\Response(
 *         response="401",
 *         description="Unauthorized - [*]：表示用户没有权限（令牌、用户名、密码错误）。",
 *         @SWG\Schema(ref="#/definitions/ExceptionReponse")
 *     ),
 *     @SWG\Response(
 *         response="404",
 *         description="NOT FOUND - [*]：用户发出的请求针对的是不存在的记录，服务器没有进行操作，该操作是幂等的。",
 *         @SWG\Schema(ref="#/definitions/ExceptionReponse")
 *     ),
 *     @SWG\Response(
 *         response="405",
 *         description="Method Not Allowed - [*] : 请求行中指定的请求方法不能被用于请求相应的资源。",
 *         @SWG\Schema(ref="#/definitions/ExceptionReponse")
 *     ),
 *     @SWG\Response(
 *         response="422",
 *         description="Unprocesable entity - [POST/PUT/PATCH] 当创建一个对象时，发生一个验证错误。",
 *         @SWG\Schema(
 *             @SWG\Property(
 *                 property="status",
 *                 type="string",
 *                 description="状态, 值为F"
 *             ),
 *             @SWG\Property(
 *                 property="error",
 *                 type="object",
 *                 description="如果是字段错误，则返回每个字段对应的错误信息，否则就是message",
 *                 @SWG\Property(
 *                     property="message",
 *                     type="string",
 *                     description="错误描述"
 *                 ),
 *             ),
 *         ),
 *     ),
 *     @SWG\Response(
 *         response="429",
 *         description="Too Many Requests - [*] : 用户在给定的时间内发送了太多的请求",
 *         @SWG\Schema(ref="#/definitions/ExceptionReponse")
 *     ),
 *     @SWG\Response(
 *         response="500",
 *         description="INTERNAL SERVER ERROR - [*]：服务器发生错误，用户将无法判断发出的请求是否成功。",
 *     ),
 * )
 *
 * @SWG\Post(
 *     path="/oauth/token",
 *     summary="获取令牌及刷新令牌",
 *     tags={"OAuth2"},
 *     description="获取token",
 *     produces={"application/json"},
 *     consumes={"multipart/form-data"},
 *     @SWG\Parameter(
 *         name="grant_type",
 *         type="string",
 *         required=true,
 *         in="formData",
 *         enum={"password", "weixin_mini_program", "refresh_token", "dhb_skey"},
 *         default="password",
 *         description="模式"
 *     ),
 *     @SWG\Parameter(
 *         name="client_id",
 *         type="integer",
 *         required=true,
 *         in="formData",
 *         default="4",
 *         description="client_id, 由服务端分配"
 *     ),
 *     @SWG\Parameter(
 *         name="client_secret",
 *         type="string",
 *         required=true,
 *         in="formData",
 *         default="8b27d21deab5ba7573205b9900468397fed11033",
 *         description="client_secret, 由服务端分配"
 *     ),
 *     @SWG\Parameter(
 *         name="username",
 *         type="string",
 *         required=false,
 *         in="formData",
 *         default="13880960819",
 *         description="用户名（模式为password必填）"
 *     ),
 *     @SWG\Parameter(
 *         name="password",
 *         type="string",
 *         required=false,
 *         in="formData",
 *         default="dhb168",
 *         description="用户密码（模式为password必填）"
 *     ),
 *     @SWG\Parameter(
 *         name="code",
 *         type="string",
 *         required=false,
 *         in="formData",
 *         description="微信小程序通过wx.login得到的code（模式为weixin_mini_program必填）"
 *     ),
 *     @SWG\Parameter(
 *         name="store_id",
 *         type="string",
 *         required=false,
 *         in="formData",
 *         description="用户访问的店铺ID（模式为weixin_mini_program必填）"
 *     ),
 *     @SWG\Parameter(
 *         name="invite_member_id",
 *         type="string",
 *         required=false,
 *         in="formData",
 *         description="邀请用户的ID（模式为weixin_mini_program时的参数, 不必填）"
 *     ),
 *     @SWG\Parameter(
 *         name="phone_number_encryptedData",
 *         type="string",
 *         required=false,
 *         in="formData",
 *         description="小程序获取到的电话号码加密数据（模式为weixin_mini_program时的参数, 不必填）"
 *     ),
 *     @SWG\Parameter(
 *         name="phone_number_iv",
 *         type="string",
 *         required=false,
 *         in="formData",
 *         description="小程序获取到的电话号码加密数据的偏移量（模式为weixin_mini_program时的参数, 不必填）"
 *     ),
 *     @SWG\Parameter(
 *         name="encryptedData",
 *         type="string",
 *         required=false,
 *         in="formData",
 *         description="小程序获取到的电话号码加密数据（模式为weixin_mini_program时的参数, 不必填）"
 *     ),
 *     @SWG\Parameter(
 *         name="iv",
 *         type="string",
 *         required=false,
 *         in="formData",
 *         description="小程序获取到的用户信息加密数据的偏移量（模式为weixin_mini_program时的参数, 不必填）"
 *     ),
 *     @SWG\Parameter(
 *         name="refresh_token",
 *         type="string",
 *         required=false,
 *         in="formData",
 *         description="更新令牌，用来获取下一次的访问令牌（模式为refresh_token必填）"
 *     ),
 *     @SWG\Parameter(
 *         name="skey",
 *         type="string",
 *         required=false,
 *         in="formData",
 *         description="APP传递的skey（模式为dhb_skey必填）"
 *     ),
 *     @SWG\Parameter(
 *         name="scope",
 *         type="string",
 *         required=false,
 *         in="formData",
 *         default="",
 *         description="权限范围（现在未使用，设置为空）"
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="成功返回",
 *         @SWG\Schema(
 *             @SWG\Property(
 *                 property="access_token",
 *                 type="string",
 *                 description="访问令牌"
 *             ),
 *             @SWG\Property(
 *                 property="token_type",
 *                 type="string",
 *                 default="Bearer",
 *                 description="令牌类型"
 *             ),
 *             @SWG\Property(
 *                 property="expires_in",
 *                 type="integer"
 * ,
 *                 default="3600",
 *                 description="表示过期时间，单位为秒"
 *             ),
 *             @SWG\Property(
 *                 property="refresh_token",
 *                 type="string",
 *                 description="更新令牌，用来获取下一次的访问令牌"
 *             ),
 *             @SWG\Property(
 *                 property="company_id",
 *                 type="integer",
 *                 description="公司ID"
 *             ),
 *         ),
 *     ),
 *     @SWG\Response(response=400, ref="#/responses/400"),
 *     @SWG\Response(response=401, ref="#/responses/401"),
 *     @SWG\Response(response=404, ref="#/responses/404"),
 *     @SWG\Response(response=405, ref="#/responses/405"),
 *     @SWG\Response(response=422, ref="#/responses/422"),
 *     @SWG\Response(response=429, ref="#/responses/429"),
 *     @SWG\Response(response=500, ref="#/responses/500"),
 * )
 */

/**
 * @SWG\Definition(type="object")
 */
class ApiResponse
{

    /**
     * @SWG\Property(format="int32")
     * @var int
     */
    public $code;

    /**
     * @SWG\Property
     * @var string
     */
    public $data;

    /**
     * @SWG\Property
     * @var string
     */
    public $message;
}

