<?php
/**
 * Created by PhpStorm.
 * User: chengyuanlong
 * Date: 2018-12-07
 * Time: 16:29
 */

namespace App\Http\Transformer;


use App\Models\MiniProgram;
use League\Fractal\TransformerAbstract;

class TemplateMiniProgramListTransformer extends TransformerAbstract
{
    public function transform(MiniProgram $item)
    {
        return [
            "mini_program_id" => $item->mini_program_id,
            "inner_name" => $item->inner_name,
            "nick_name" => $item->nick_name,
            "tag" => $item->tag,
            "online_version" => $item->onlineVersion ? $item->onlineVersion->user_version : '',
        ];
    }
}