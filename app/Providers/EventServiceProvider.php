<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\AuditEvent' => [
            'App\Listeners\AuditEventListener',
        ],
        'App\Events\EmailNoticeEvent' => [
            'App\Listeners\EmailNoticeEventListener',
        ],
        'App\Event\TemplateReleaseTaskEvent' => [
            'App\Listeners\TemplateReleaseTaskEventListener',
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
