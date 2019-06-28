<?php
/**
 * Created by PhpStorm.
 * User: chengyuanlong
 * Date: 2018-12-07
 * Time: 16:29
 */

namespace App\Http\Transformer;


use App\Models\Component;
use App\Models\MiniProgram;
use League\Fractal\TransformerAbstract;

class MiniProgramTransformer extends TransformerAbstract
{
    public function transform(MiniProgram $item)
    {
        return [
            "mini_program_id" => $item->mini_program_id,
            "inner_name" => $item->inner_name,
            "inner_desc" => $item->inner_desc,
            "nick_name" => $item->nick_name,
            "head_img" => $item->head_img,
            "app_id" => $item->app_id,
            "user_name" => $item->user_name,
            "principal_name" => $item->principal_name,
            "qrcode_url" => $item->qrcode_url,
            "desc" => $item->desc,
            "authorization_status" => $item->authorization_status === 10 ? 'authorized' : 'unauthorized',
            //TODO::补充发布状态
            "release_status" => 21,
        ];
    }
}