<?php

namespace App\Listeners;

use App\Events\EmailNoticeEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class EmailNoticeEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  EmailNoticeEvent  $event
     * @return void
     */
    public function handle(EmailNoticeEvent $event)
    {
        $payload = $event->payload;
        Mail::raw(json_encode($payload, JSON_UNESCAPED_UNICODE), function($message) use($payload){
            $message->to('chengyuanlong@rsung.com', '成元龙');
            $message->subject($payload['title'] ?? '小程序发版系统通知');
            return $message;
        });
    }
}
