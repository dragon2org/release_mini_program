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
 *     basePath="/api",
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
 *         ref="$/definitions/LayoutRespone",
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
 *                 @SWG\Property(
 *                     property="code",
 *                     type="integer",
 *                     description="错误码"
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
 * @SWG\Definition(
 *   definition="LayoutRespone",
 *   @SWG\Property(
 *      property="status",
 *      type="string",
 *      description="接口返回状态",
 *      enum={"T", "F"},
 *      default="T"
 *   )
 * )
 *
 * @SWG\Response(
 *      response="200",
 *      description="the basic response",
 *      @SWG\Schema(
 *          ref="$/definitions/LayoutRespone",
 *          @SWG\Property(
 *              property="data",
 *              type="object",
 *          )
 *      )
 * )
 *
 */


/**
 * @SWG\Definition(
 *   definition="ExceptionReponse",
 *   type="object"
 * )
 */
class ExceptionReponse
{
    /**
     * @SWG\Property(type="string", description="状态, 值为F")
     */
    public $status;

    /**
     * @SWG\Property(
     *     type="object",
     *     @SWG\Property(
     *         property="message",
     *         type="string",
     *         description="错误描述"
     *     ),
     * )
     */
    public $error;
}

/**
 * @SWG\Definition(definition="ApiResponse")
 */
class ApiResponse
{

    /**
     * @SWG\Property(type="integer", description="错误码", default="0")
     * @var int
     */
    public $err_code;

    /**
     * @SWG\Property(type="object", description="返回数据")
     * @var string
     */
    public $data;

    /**
     * @SWG\Property(type="string", description="错误信息", default="")
     * @var string
     */
    public $err_msg;
}

