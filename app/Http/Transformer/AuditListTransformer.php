<?php
/**
 * Created by PhpStorm.
 * User: chengyuanlong
 * Date: 2018-12-07
 * Time: 16:29
 */

namespace App\Http\Transformer;


use App\Models\ReleaseAudit;
use League\Fractal\TransformerAbstract;

class AuditListTransformer extends TransformerAbstract
{
    public function transform(ReleaseAudit $item)
    {
        return [
            'status' => $item->status,
            'reason' => $item->reason,
            'screenshot' => $item->screenshot,
            'created_at' => (string) $item->created_at,
        ];
    }
}