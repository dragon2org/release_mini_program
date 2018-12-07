<?php
/**
 * Created by PhpStorm.
 * User: chengyuanlong
 * Date: 2018/12/4
 * Time: 上午11:12
 */

namespace App\Http\Controllers\Api\V1;


class TesterController
{
    /**
     * @SWG\Get(
     *     path="/component/{componentAppId}/mini_program/{miniProgramAppId}/tester",
     *     summary="获取体验者列表",
     *     tags={"成员管理"},
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

    /**
     * @SWG\Post(
     *     path="/component/{componentAppId}/mini_program/{miniProgram}/tester",
     *     summary="绑定体验者",
     *     tags={"成员管理"},
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
     *         in="formData",
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

    /**
     * @SWG\Delete(
     *     path="/component/{componentAppId}/mini_program/{miniProgram}/tester/{wechatid}",
     *     summary="解绑体验者",
     *     tags={"成员管理"},
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
}