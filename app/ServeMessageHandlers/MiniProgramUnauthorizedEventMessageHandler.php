<?php


namespace App\ServeMessageHandlers;

use App\Events\AuditEvent;
use App\Events\EmailNoticeEvent;
use App\Models\MiniProgram;
use EasyWeChat\Kernel\Contracts\EventHandlerInterface;

class MiniProgramUnauthorizedEventMessageHandler implements EventHandlerInterface
{
    public function handle($payload = null)
    {
        $miniProgram = MiniProgram::withTrashed()
            ->whereHas('component', function ($query) use ($payload) {
                $query->withTrashed()->where('app_id', $payload['AppId']);
            })
            ->where('app_id', $payload['AuthorizerAppid'])
            ->firstOrFail();

        $miniProgram->authorization_status = MiniProgram::AUTHORIZATION_STATUS_UNAUTHORIZED;
        $miniProgram->save();

        return true;
    }
}