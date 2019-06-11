<?php
/**
 * Created by PhpStorm.
 * User: chengyuanlong
 * Date: 2018-12-07
 * Time: 16:29
 */

namespace App\Http\Transformer;


use App\Models\Component;
use League\Fractal\TransformerAbstract;

class ComponentListTransformer extends TransformerAbstract
{
    public function transform(Component $component)
    {
        return [
            "name" => $component->name,
            "desc" => $component->desc,
            "app_id" => $component->app_id,
            "mini_program_number" => 0,
            'template_number' => 0,
            "created_at" => (string) $component->created_at,
        ];
    }
}