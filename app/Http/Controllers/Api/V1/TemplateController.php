<?php

namespace App\Http\Controllers\Api\V1;


use App\Exceptions\UnprocessableEntityHttpException;
use App\Facades\ReleaseFacade;
use App\Http\ApiResponse;
use App\Http\Requests\DeleteTemplate;
use App\Http\Requests\DraftToTemplate;
use App\Http\Transformer\TemplateListTransformer;
use App\Http\Transformer\TemplateMiniProgramListTransformer;
use App\Models\Component;
use App\Models\ComponentTemplate;
use App\Models\MiniProgram;
use App\Models\Template;

class TemplateController extends Controller
{
    protected $service;

    public function __construct(ApiResponse $response)
    {
        parent::__construct($response);
    }

    /**
     * @SWG\Get(
     *     path="/v1/component/{componentAppId}/draft",
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

        $items = ReleaseFacade::service()->getDrafts();

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
     *     path="/v1/component/{componentAppId}/template",
     *     summary="保存草稿箱到模板",
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
     *         name="body",
     *         in="body",
     *         required=true,
     *         type="object",
     *         @SWG\Schema(
     *             required={"draft_id"},
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
     *         )
     *     )
     * )
     */
    public function draftToTemplate(DraftToTemplate $request)
    {
        $response = ReleaseFacade::service()->draftToTemplate(request()->input('draft_id'));
        return $this->response->withArray([]);
    }

    /**
     * @SWG\Delete(
     *     path="/v1/component/{componentAppId}/template/{templateId}",
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
        $response = ReleaseFacade::service()->deleteTemplate($templateId);

        return $this->response->withArray([
            'data' => $response
        ]);
    }

    /**
     * @SWG\Get(
     *     path="/v1/component/{componentAppId}/template",
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
        $items = ComponentTemplate::where('component_id', ReleaseFacade::service()->component->component_id)
            ->orderBy('component_template_id', 'desc')
            ->paginate();

        return $this->response->withCollection($items, new TemplateListTransformer());
    }

    /**
     * @SWG\Post(
     *     path="/v1/component/:componentAppId/template/:templateId/release",
     *     summary="批量发布",
     *     tags={"三方平台管理"},
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
        $response = ReleaseFacade::service()->templateRelease($templateId);

        return $this->response->withArray([
            'data' => $response
        ]);
    }

    /**
     * @SWG\Get(
     *     path="/v1/component/{componentAppId}/template/{templateId}/statistical",
     *     summary="模板统计",
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
     *                 type="object",
     *                 @SWG\Property(property="unset", type="integer", description="待设置"),
     *                 @SWG\Property(property="setting", type="integer", description="设置中"),
     *                 @SWG\Property(property="setted", type="integer", description="已设置"),
     *                 @SWG\Property(property="uncommitted", type="integer", description="待提交代码"),
     *                 @SWG\Property(property="committed", type="integer", description="已提交代码"),
     *                 @SWG\Property(property="auditing", type="integer", description="已提审"),
     *                 @SWG\Property(property="audit_failed", type="integer", description="审核失败"),
     *                 @SWG\Property(property="audit_success", type="integer", description="审核成功"),
     *                 @SWG\Property(property="audit_reverted", type="integer", description="撤回审核"),
     *                 @SWG\Property(property="released", type="integer", description="已发布"),
     *             )
     *         )
     *     )
     * )
     */
    public function statistical($componentAppId, $templateId)
    {
        $response = ReleaseFacade::service()->templateStatistical($templateId);

        return $this->response->withArray([
            'data' => $response
        ]);
    }

    public function miniProgramList($componentAppId, $templateId)
    {
        $component =  Component::where('app_id', $componentAppId)->first();

        $template = $component->template()->where('template_id', $templateId)->first();

        if(!isset($template->tag)){
            throw new UnprocessableEntityHttpException(trans('模板不存在'));
        }

        if(empty($template->tag)){
            throw new UnprocessableEntityHttpException(trans('内部版本错误'));
        }

        $items = MiniProgram::with('onlineVersion')->where('tag', $template->tag)->select(['nick_name', 'mini_program_id', 'inner_name', 'tag'])->get();

        return $this->response->withCollection($items, new TemplateMiniProgramListTransformer());
    }
}
