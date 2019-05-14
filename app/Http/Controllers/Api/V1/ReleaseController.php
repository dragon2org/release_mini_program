<?php


namespace App\Http\Controllers\Api\V1;

use App\Exceptions\UnprocessableEntityHttpException;
use App\Http\ApiResponse;
use App\Http\Requests\RetryRelease;
use App\Http\Transformer\ReleaseDetailItemsTransformer;
use App\Http\Transformer\ReleaseItemsTransformer;
use App\Models\Release;
use App\Models\ReleaseItem;

class ReleaseController extends Controller
{

    public function __construct(ApiResponse $response)
    {
        parent::__construct($response);
    }

    /**
     * @SWG\Get(
     *     path="/component/{componentAppId}/release_task",
     *     summary="获取构建列表",
     *     tags={"三方平台管理-模板管理"},
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
     *         name="status",
     *         in="query",
     *         description="状态",
     *         required=false,
     *         type="integer"
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
     *                 @SWG\Items(ref="#/definitions/Release")
     *             ),
     *         )
     *     )
     * )
     */
    public function index()
    {
        $model = Release::where('component_id', app('dhb.component.core')->component->component_id)
            ->orderBy('release_id', 'desc');

        if(request()->input('status')){
            $model->where('status', request()->input('status'));
        }

        $items = $model->paginate();

        return $this->response->withCollection($items, new ReleaseItemsTransformer($items));
    }
    /**
     * @SWG\Get(
     *     path="/component/{componentAppId}/release_task/{releaseId}",
     *     summary="获取构建详情任务列表",
     *     tags={"三方平台管理-模板管理"},
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
     *         name="releaseId",
     *         in="path",
     *         description="构建id",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="status",
     *         in="query",
     *         description="状态：准备中:0;进行中:1;成功:2;失败3",
     *         required=false,
     *         type="integer"
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
     *                 @SWG\Items(ref="#/definitions/ReleaseItem")
     *             ),
     *         )
     *     )
     * )
     */
    public function detail($componentAppId, $releaseId)
    {
        $model = ReleaseItem::where('release_id', $releaseId)
            ->orderBy('release_item_id', 'desc');

        if(request()->input('status')){
            $model->where('status', request()->input('status'));
        }

        $items = $model->paginate();

        return $this->response->withCollection($items, new ReleaseDetailItemsTransformer($items));
    }

    /**
     * @SWG\Get(
     *     path="/component/{componentAppId}/release_task/{releaseId}/statistical",
     *     summary="模板任务统计",
     *     tags={"三方平台管理-模板管理"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     deprecated=true,
     *     @SWG\Parameter(
     *         name="componentAppId",
     *         in="path",
     *         description="三方平台AppID",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="releaseId",
     *         in="path",
     *         description="构建id",
     *         required=true,
     *         type="integer"
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
     *                 @SWG\Property(property="prepare", type="integer", description="等待中的任务数"),
     *                 @SWG\Property(property="processing", type="integer", description="进行中的任务数"),
     *                 @SWG\Property(property="success", type="integer", description="构建成功的任务数"),
     *                 @SWG\Property(property="failed", type="integer", description="构建失败的任务数"),
     *             )
     *         )
     *     )
     * )
     */
    public function statistical($componentAppId, $releaseId)
    {
        $data = ReleaseItem::statistical($releaseId);

        return $this->response->withArray(['data' => $data]);
    }

    /**
     * @SWG\Post(
     *     path="/component/{componentAppId}/release_task/{releaseId}/retry",
     *     summary="重试任务",
     *     tags={"三方平台管理-模板管理"},
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
     *         name="releaseId",
     *         in="path",
     *         description="构建id",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="data",
     *         in="body",
     *         description="表单数据",
     *         required=true,
     *         type="object",
     *         @SWG\Schema(
     *             @SWG\Property(property="config", type="object", description="重试配置.支持平台发版配置所有的配置项。格式一样"),
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
    public function retry($componentAppId, $releaseId, RetryRelease $request)
    {
        $release = (new Release())->where('release_id', $releaseId)->first();
        if(!isset($release)){
            throw new UnprocessableEntityHttpException(trans('任务不存在'));
        }

        $result  = $release->retry(request()->input('config'));

        return $this->response->withArray([
            'data' => $result
        ]);
    }
}