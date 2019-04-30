<?php


namespace App\Http\Transformer;


use App\Models\ReleaseItem;
use League\Fractal\TransformerAbstract;

class ReleaseDetailItemsTransformer extends TransformerAbstract
{
    public function transform(ReleaseItem $item)
    {
        return [
            "release_item_id" => $item->release_item_id,
            "release_id" => $item->release_id,
            'name' => $item->name,
            "original_config" => $item->original_config,
            "online_config" => $item->online_config,
            "push_config" => $item->push_config,
            "response" => $item->response,
            "status" => $item->status,
            "status_trans" => $item->status === 1 ? 'success' : 'failed',
            "audit_id" => $item->audit_id,
            "trade_no" => $item->trade_no,
            'created_at' => (string) $item->created_at,
            'updated_at' => (string) $item->updated_at,
        ];
    }
}