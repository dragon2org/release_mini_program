<?php

namespace App\Http\Controllers\Api\V1;


use App\Exceptions\UnprocessableEntityHttpException;
use App\Http\ApiResponse;
use App\Http\Requests\DeleteTemplate;
use App\Http\Requests\DraftToTemplate;
use App\Http\Transformer\MiniProgramListTransformer;
use App\Http\Transformer\TemplateListTransformer;
use App\Models\Component;
use App\Models\ComponentTemplate;
use App\Models\Template;
use App\Services\ComponentService;
use EasyWeChat\Factory;
use Illuminate\Support\Arr;

class TemplateController extends Controller
{
    protected $service;

    public function __construct(ApiResponse $response)
    {
        parent::__construct($response);
    }

    /**
     * @SWG\Get(
     *     path="/component/{componentAppId}/draft",
     *     summary="获取草稿箱列表",
     *     tags={"三方平台管理-模板管理"},
     *     description="三方平台三方平台管理-模板管理",
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
     *                 type="array",
     *                 @SWG\Items(ref="#/definitions/Draft")
     *             ),
     *         )
     *     )
     * )
     */
    public function draft()
    {

        $items = app('dhb.component.core')->getDrafts();

        if(isset($items)){
            foreach($items as &$item){
                $item['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
            }
        }
        return $this->response->withArray([
            'data' => $items
        ]);
    }

    /**
     * @SWG\Post(
     *     path="/component/{componentAppId}/template",
     *     summary="保存草稿箱到模板",
     *     tags={"三方平台管理-模板管理"},
     *     description="管理三方平台",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="data",
     *         in="body",
     *         description="表单数据",
     *         required=true,
     *         type="object",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="draft_id",
     *                 type="string",
     *                 description="模板ID"
     *             )
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
     *                 type="Object",
     *                 ref="#/definitions/Component"
     *             )
     *         )
     *     )
     * )
     */
    public function draftToTemplate(DraftToTemplate $request)
    {
        $response = app('dhb.component.core')->draftToTemplate(request()->input('draft_id'));
        return $this->response->withArray([
            'data' => $response
        ]);
    }

    /**
     * @SWG\Post(
     *     path="/component/{componentAppId}/template/sync",
     *     summary="同步微信三方平台模板到平台",
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
     *                 @SWG\Property(property="count", type="integer", description="模板总数"),
     *                 @SWG\Property(property="synced", type="integer", description="已同步"),
     *                 @SWG\Property(property="not_change", type="integer", description="未变化"),
     *             )
     *         )
     *     )
     * )
     */
    public function templateSync()
    {
        $remoteTemplateList = app('dhb.component.core')->templateList();
        $remote = [];
        foreach($remoteTemplateList as $item){
            $remote[] = $item['template_id'];
        }

        $noItems = (new ComponentTemplate())
            ->whereIn('template_id', $remote)
            ->select('template_id')
            ->get();
        $no = [];
        foreach($noItems as $item){
            $no[] = $item->template_id;
        }

        $handleNum = 0;
        foreach($remoteTemplateList as $item){
            if(in_array($item['template_id'],  $no)){
                continue;
            }
            //添加数据
            $model = new ComponentTemplate();
            $model->component_id = app('dhb.component.core')->component->component_id;
            $model->template_id = $item['template_id'];
            $model->user_version = $item['user_version'];
            $model->user_desc = $item['user_desc'];
            $model->create_time = date('Y-m-d H:i:s', $item['create_time']);
            $model->branch = $item['user_desc'];
            $model->save();

            $handleNum++;
        }

        return $this->response->withArray([
            'data' => [
                'count' => count($remoteTemplateList),
                'synced' => $handleNum,
                'not_change' => count($no),
            ]
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
     *             )
     *         )
     *     )
     * )
     */

    public function delete($componentAppId, $templateId)
    {
        $template = (new ComponentTemplate())
            ->where('template_id', $templateId)
            ->where('is_deleted', 0)
            ->first();

        if(!isset($template)){
            throw new UnprocessableEntityHttpException(trans('模板不存在'));
        }

        $response = app('dhb.component.core')->deleteTemplate($templateId);
        $template->is_delete=1;
        $template->save();

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
        $items = (new ComponentTemplate())
            ->where('component_id', app('dhb.component.core')->component->component_id)
            ->where('is_deleted', 0)
            ->paginate();

        return $this->response->withCollection($items, new TemplateListTransformer($items));
    }

    /**
     * @SWG\Post(
     *     path="/component/:componentAppId/template/:templateId/release",
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
    public function release($componentAppId, $templateId)
    {
        $response = app('dhb.component.core')->templateRelease($templateId);

        return $this->response->withArray([
            'data' => $response
        ]);
    }
}
