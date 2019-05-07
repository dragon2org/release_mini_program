<?php

namespace App\Listeners;

use App\Events\AuditEvent;
use App\Models\MiniProgram;
use App\Models\Release;
use App\Releaser;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AuditEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  AuditEvent  $event
     * @return void
     */
    public function handle(AuditEvent $event)
    {
        $payload = $event->payload;

        $miniProgram = (new MiniProgram())
            ->where('user_name', $payload['ToUserName'])
            ->first();

        $release = (new Release())
            ->where('mini_program_id', $miniProgram->mini_program_id)
            ->whereIn('status', [Release::RELEASE_STATUS_AUDITING, Release::RELEASE_STATUS_AUDIT_FAILED, Release::RELEASE_STATUS_AUDIT_REVERTED])
            ->orderBy('release_id', 'desc')
            ->first();

        if(!isset($release) || !isset($release->audit_id)){
            return null;
        }

        //获取审核结果
        $service = Releaser::build($miniProgram->component->app_id);
        $miniProgramApp = $service->setMiniProgram($miniProgram->app_id);

        return $release->callback($miniProgramApp);
    }
}
