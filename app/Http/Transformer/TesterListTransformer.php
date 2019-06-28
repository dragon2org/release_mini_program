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

class TesterListTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'userstr' => $item['userstr'],
            'wechat_id' => $item['wechat_id'],
            'created_at' => (string) $item['created_at'],
        ];
    }
}