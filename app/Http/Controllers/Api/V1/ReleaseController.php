<?php


namespace App\Http\Controllers\Api\V1;

use App\Http\ApiResponse;
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

    public function index()
    {
        $items = Release::where('component_id', app('dhb.component.core')->component->component_id)
            ->orderBy('release_id', 'desc')
            ->paginate();


        return $this->response->withArray(
            ['data' => $this->response->transformatCollection($items, new ReleaseItemsTransformer($items))]
        );
    }

    public function detail($componentAppId, $releaseId)
    {
        $items = ReleaseItem::where('release_id', $releaseId)
            ->orderBy('release_item_id', 'desc')
            ->paginate();


        return $this->response->withArray(
            ['data' => $this->response->transformatCollection($items, new ReleaseDetailItemsTransformer($items))]
        );
    }

    public function retry($componentAppId, $releaseId)
    {
        //TODO::重试构建任务
    }
}