<?php

namespace App\Listeners;

use App\Event\TemplateReleaseTaskEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class TemplateReleaseTaskEventListener
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
     * @param  TemplateReleaseTaskEvent  $event
     * @return void
     */
    public function handle(TemplateReleaseTaskEvent $event)
    {
        $task = $event->task;


    }
}
