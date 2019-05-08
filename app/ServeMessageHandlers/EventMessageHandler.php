<?php


namespace App\ServeMessageHandlers;

use App\Events\AuditEvent;
use App\Events\EmailNoticeEvent;
use EasyWeChat\Kernel\Contracts\EventHandlerInterface;

class EventMessageHandler implements EventHandlerInterface
{
    public function handle($payload = null)
    {
        switch ($payload['Event']){
            case 'weapp_audit_success':
                AuditEvent::dispatch($payload);
                return true;
            case 'weapp_audit_fail':
                AuditEvent::dispatch($payload);
                EmailNoticeEvent::dispatch($payload);
                return true;
        }

    }
}