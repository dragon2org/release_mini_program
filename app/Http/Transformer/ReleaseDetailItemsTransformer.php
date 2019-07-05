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
            "original_config" => $item->original_config ? json_decode($item->original_config, true) : (object) [],
            "online_config" => $item->online_config? json_decode($item->online_config, true) : (object) [],
            "push_config" => $item->push_config ? json_decode($item->push_config, true) : (object) [],
            "response" => $item->response ? json_decode($item->response, true) : (object) [],
            "status" => $item->status,
            "status_trans" => $item->getStatusTrans(),
            'created_at' => (string) $item->created_at,
            'updated_at' => (string) $item->updated_at,
            'app_id' => $item->miniProgram->app_id,
        ];
    }
}