<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\ApiResponse;
use App\Models\Component;
use App\Services\ComponentService;
use EasyWeChat\Factory;

class TemplateController extends Controller
{
    protected $service;

    public function __construct(ApiResponse $response)
    {
        parent::__construct($response);
        $this->service = new ComponentService();
    }

    /**
     * @SWG\Get(
     *     path="/component/{componentAppId}/draft",
     *     summary="获取草稿箱列表",
     *     tags={"三方平台管理-模板管理"},
     *     description="三方平台三方平台管理-模板管理",
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
     *                 @SWG\Items(ref="#/definitions/Draft")
     *             ),
     *         )
     *     )
     * )
     */
    public function draft()
    {
        $response = $this->service->getDrafts();
        return $this->response->withArray([
            'data' => $response
        ]);
    }

    /**
     * @SWG\Post(
     *     path="/component/{componentAppId}/template",
     *     summary="保存草稿箱到模板",
     *     tags={"三方平台管理-模板管理"},
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
    public function draftToTemplate($componentAppId, $templateId)
    {
        $response = $this->service->draftToTemplate($templateId);
        return $this->response->withArray([
            'data' => $response
        ]);
    }


    /**
     * @SWG\Delete(
     *     path="/component/{componentAppId}/template/{templateId}",
     *     summary="删除模板",
     *     tags={"三方平台管理-模板管理"},
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
     *         name="templateId",
     *         in="path",
     *         description="模板ID",
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
     *                 type="Object",
     *                 ref="#/definitions/Component"
     *             )
     *         )
     *     )
     * )
     */

    public function delete($componentAppId, $templateId)
    {
        $response = $this->service->deleteTemplate($templateId);
        return $this->response->withArray([
            'data' => $response
        ]);
    }

    /**
     * @SWG\Get(
     *     path="/component/{componentAppId}/template",
     *     summary="获取模板列表",
     *     tags={"三方平台管理-模板管理"},
     *     description="三方平台三方平台管理-模板管理",
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
     *                 property="status",
     *                 type="string",
     *                 default="T",
     *                 description="接口返回状态['T'->成功; 'F'->失败]"
     *             ),
     *             @SWG\Property(
     *                 property="data",
     *                 type="array",
     *                 @SWG\Items(ref="#/definitions/Template")
     *             ),
     *         )
     *     )
     * )
     */

    public function index()
    {
        $response = $this->service->templateList();
        return $this->response->withArray([
            'data' => $response
        ]);
    }

}
