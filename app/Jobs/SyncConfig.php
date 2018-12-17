<?php

namespace App\Jobs;

use App\Models\Component;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SyncConfig implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $component;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Component $component)
    {
        $this->component = $component;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->component->inner_name ++;
        $this->component->save();
    }
}
