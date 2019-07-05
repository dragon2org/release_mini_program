<?php


namespace App\Http\Transformer;


use App\Models\ComponentTemplate;
use App\Models\Template;
use League\Fractal\TransformerAbstract;

class ReleaseTemplateListTransformer extends TransformerAbstract
{
    public function transform(ComponentTemplate $item)
    {
        return [
            "create_time" => $item->create_time,
            "template_id" => $item->template_id,
            "user_version" => $item->user_version,
            "tag" => $item->tag,
            "created_at" => (string) $item->created_at,
            'developer' => $item->developer,
        ];
    }
}