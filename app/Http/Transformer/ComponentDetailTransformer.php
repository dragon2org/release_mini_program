<?php
/**
 * Created by PhpStorm.
 * User: harlen
 * Date: 2019/4/24
 * Time: 10:04 PM
 */

namespace App\Http\Transformer;

use App\Models\Component;
use League\Fractal\TransformerAbstract;

class ComponentDetailTransformer extends TransformerAbstract
{
    public function transform(Component $component)
    {
        return [
            "inner_name" => $component->inner_name,
            "inner_desc" => $component->inner_desc,
            "name" => $component->name,
            "desc" => $component->desc,
            "app_id" => $component->app_id,
            "app_secret" => $component->app_secret,
            "verify_token" => $component->verify_token,
            "aes_key" => $component->aes_key,
            "validate" => [
                'filename' => $component->filename,
                'content' => $component->filename
            ],
        ];
    }
}