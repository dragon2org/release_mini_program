<?php


namespace App\Http\Transformer;


use App\Models\ComponentTemplate;
use App\Models\Template;
use League\Fractal\TransformerAbstract;

class TemplateListTransformer extends TransformerAbstract
{
    public function transform(ComponentTemplate $item)
    {
        return [
            "create_time" => $item->create_time,
            "template_id" => $item->template_id,
            "user_version" => $item->user_version,
            "user_desc" => $item->user_desc,
            "tag" => $item->tag,
            "created_at" => (string) $item->created_at,
            'developer' => $item->developer,
            'source_miniprogram' => $item->source_miniprogram,
            'source_miniprogram_appid' => $item->source_miniprogram_appid,
            'commit_time' => date('Y-m-d H:i:s'),
            'uncommitted' => 0,
            'committed' => 0,
            'auditing' => 0,
            'audit_failed' => 0,
            'released' => 0,
        ];
    }
}