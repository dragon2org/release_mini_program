<?php


namespace App\Http\Controllers\Api\V1;

use App\Exceptions\UnprocessableEntityHttpException;
use App\Http\ApiResponse;
use App\Http\Requests\RetryRelease;
use App\Http\Transformer\ReleaseDetailItemsTransformer;
use App\Http\Transformer\ReleaseItemsTransformer;
use App\Jobs\SetMiniProgramWebViewDomain;
use App\Logs\ReleaseInQueueLog;
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
        $model = Release::where('component_id', app('dhb.component.core')->component->component_id)
            ->orderBy('release_id', 'desc');

        if(request()->input('status')){
            $model->where('status', request()->input('status'));
        }

        $items = $model->paginate();

        return $this->response->withCollection($items, new ReleaseItemsTransformer($items));
    }

    public function statistical($componentAppId, $releaseId)
    {
        $data = ReleaseItem::statistical($releaseId);

        return $this->response->withArray(['data' => $data]);
    }

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

    public function retry($componentAppId, $releaseId, RetryRelease $request)
    {
        (new ReleaseItem())->retry(request()->input('release_item_id'), request()->input('config'));

        return $this->response->withArray();
    }
}