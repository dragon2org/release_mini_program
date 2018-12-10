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

class ComponentTransformer extends TransformerAbstract
{
    public function transform(Component $component)
    {
        return [
            "authorization_launch_page_domain" => $component->getAuthorizationLaunchPageDomain(),
            "authorization_event_notify_url" => $component->getAuthorizationEventNotifyUrl(),
            "msg_event_notify_url" => $component->getMsgEventNotifyUrl(),
        ];
    }
}