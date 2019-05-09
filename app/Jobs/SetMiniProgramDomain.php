<?php

namespace App\Jobs;

use App\Logs\ReleaseCommonQueueLogQueueLog;
use App\Models\MiniProgram;
use App\Models\Release;
use App\Models\ReleaseItem;
use App\ReleaseConfigurator;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use \EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Application;

class SetMiniProgramDomain extends BaseReleaseJobWithLog implements ShouldQueue
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
        $this->proccess($this, function(Application $app){

            $setted = $app->domain->modify(['action' => 'get']);
            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "pull domain", $setted);

            $domain = $this->config;
            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "push domain", $domain);

            $response = $app->domain->modify($domain);
            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "push domain response", $response);

            $this->task->building($domain, $response, ReleaseItem::STATUS_SUCCESS, '');

            return $response;
        });
    }

}
