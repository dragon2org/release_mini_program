<?php

namespace App\Jobs;

use App\Logs\ReleaseCommonQueueLogQueueLog;
use App\Models\MiniProgram;
use App\Models\Release;
use App\Models\ReleaseItem;
use App\ReleaseConfigurator;
use App\Releaser;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use \EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Application;


class SetMiniProgramSupportVersion extends BaseReleaseJobWithLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $miniProgram;

    protected $config;

    protected $release;

    protected $task;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ReleaseItem $task)
    {
        $this->task = $task;
        $this->miniProgram = $task->miniProgram;
        $this->config = json_decode($task->original_config, true);
        $this->release = $task->release;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->proccess($this, function (Application $app) {

            $setted = $app->code->getSupportVersion();
            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "pull support_version", $setted);

            $supportVersion = $this->config;
            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "push support_version", [$supportVersion]);

            $response = $app->code->setSupportVersion($supportVersion);
            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "push support_version response", $response);

            $this->task->building($supportVersion, $response, ReleaseItem::STATUS_SUCCESS, $setted);
        });
    }
}
