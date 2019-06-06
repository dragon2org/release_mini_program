<?php


namespace App\Http\Transformer;


use App\Models\Release;
use League\Fractal\TransformerAbstract;

class ReleaseItemsTransformer extends TransformerAbstract
{
    public function transform(Release $item)
    {
        return [
            "release_id" => $item->release_id,
            "template_id" => $item->template_id,
            "user_version" => $item->user_version,
            "user_desc" => $item->user_desc,
            "config" => $item->config ? json_decode($item->config, true) : (object) [],
            "desc" => $item->desc,
            'status' => $item->status,
            'status_trans' => $item->getStatusTrans(),
            "audit_id" => intval($item->audit_id),
            "trade_no" => $item->trade_no,
            'created_at' => (string) $item->created_at,
            'updated_at' => (string) $item->updated_at,
        ];
    }
}