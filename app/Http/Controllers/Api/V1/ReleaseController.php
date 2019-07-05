<?php


namespace App\Http\Controllers\Api\V1;

use App\Exceptions\UnprocessableEntityHttpException;
use App\Facades\ReleaseFacade;
use App\Helpers\Utils;
use App\Http\ApiResponse;
use App\Http\Requests\RetryRelease;
use App\Http\Transformer\AuditListTransformer;
use App\Http\Transformer\ReleaseDetailItemsTransformer;
use App\Http\Transformer\ReleaseItemsTransformer;
use App\Models\Release;
use App\Models\ReleaseAudit;
use App\Models\ReleaseItem;
use Illuminate\Support\Collection;

class ReleaseController extends Controller
{

    public function __construct(ApiResponse $response)
    {
        parent::__construct($response);
    }

    /**
     * @SWG\Get(
     *     path="/v1/component/{componentAppId}/release_task",
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
        $model = Release::with('miniProgram')->where('component_id', ReleaseFacade::service()->component->component_id)
            ->orderBy('release_id', 'desc');

        if(request()->input('status')){
            $model->where('status', request()->input('status'));
        }

        $items = $model->paginate(Utils::pageSize());

        return $this->response->withCollection($items, new ReleaseItemsTransformer());
    }
    /**
     * @SWG\Get(
     *     path="/v1/component/{componentAppId}/release_task/{releaseId}",
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
        $model = ReleaseItem::with('miniProgram')->where('release_id', $releaseId)
            ->orderBy('release_item_id', 'desc');

        if(request()->input('status')){
            $model->where('status', request()->input('status'));
        }

        $items = $model->paginate(Utils::pageSize());

        return $this->response->withCollection($items, new ReleaseDetailItemsTransformer());
    }

    /**
     * @SWG\Get(
     *     path="/v1/component/{componentAppId}/release/{releaseId}/audit",
     *     summary="获取审核列表",
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
     *                 @SWG\Items(ref="#/definitions/ReleaseAuditList")
     *             ),
     *         )
     *     )
     * )
     */
    public function auditList($componentAppId, $releaseId)
    {
        $items = ReleaseAudit::where('release_id',  $releaseId)->orderBy('release_audit_id', 'desc')->get();

        $items->map(function($item){
            if($item->screenshot){
                $item->screenshot = ReleaseFacade::service()->componentGetMaterial($item->mini_program_id, $item->screenshot);
            }
        });

        return $this->response->withCollection($items, new AuditListTransformer());
    }
    /**
     * @SWG\Get(
     *     path="/v1/component/{componentAppId}/release_task/{releaseId}/statistical",
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

    public function retry($componentAppId, $releaseId, RetryRelease $request)
    {
        $release = (new Release())->where('release_id', $releaseId)->firstOrFail();

        $result  = $release->retry(request()->input('config'));

        return $this->response->withArray([
            'data' => $result
        ]);
    }

    /**
     * @SWG\Post(
     *     path="/v1/component/{componentAppId}/release_task/{release_item_id}/retry",
     *     summary="重试任务",
     *     tags={"三方平台管理-模板管理"},
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
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="releaseId",
     *         in="path",
     *         description="构建id",
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
    public function retryItem($componentAppId, $releaseItemId, RetryRelease $request)
    {
        $release = (new ReleaseItem())->where('release_item_id', $releaseItemId)->firstOrFail();

        $result  = $release->retry(request()->input('config'));

        return $this->response->withArray([
            'data' => $result
        ]);
    }
}