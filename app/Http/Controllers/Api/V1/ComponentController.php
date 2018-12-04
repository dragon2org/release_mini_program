<?php

namespace App\Http\Controllers\V1;

class ComponentController
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
     *         description="操作成功",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Items(ref="#/definitions/Component")
     *         ),
     *     )
     * )
     */

    public function create()
    {

    }

    /**
     * @SWG\Get(
     *     path="/component/{componentAppId}",
     *     summary="获取平台信息",
     *     tags={"三方平台管理"},
     *     description="管理三方平台",
     *     produces={"application/json"},
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
     *                 type="Object",
     *                 ref="#/definitions/Component"
     *             )
     *         )
     *     )
     * )
     */


    /**
     * @SWG\Put(
     *     path="/component/{componentAppId}",
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
     *                 ref="#/definitions/Component"
     *             )
     *         )
     *     )
     * )
     */

    /**
     * @SWG\Get(
     *     path="/component",
     *     summary="获取平台列表",
     *     tags={"三方平台管理"},
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
     *                 @SWG\Items(ref="#/definitions/Component")
     *             ),
     *         )
     *     )
     * )
     */

    /**
     * @SWG\Delete(
     *     path="/component/{componentAppId}",
     *     summary="删除平台",
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
     *     path="/component/{componentAppId}/template/{templateId}/release",
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
}
