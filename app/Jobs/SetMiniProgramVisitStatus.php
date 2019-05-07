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


class SetMiniProgramVisitStatus extends BaseReleaseJobWithLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $miniProgram;

    protected $config;

    protected $release;

    const VERSION = '1.0.0';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(MiniProgram $miniProgram, Release $release)
    {
        $this->miniProgram = $miniProgram;
        $this->config = $release->getReleaseConfigurator();
        $this->release = $release;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->proccess($this, function(Application $app){

            $visitStatus = $this->config->visitStatus;
            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "push visit_status", [$visitStatus]);

            $response = $app->code->changeVisitStatus($visitStatus);
            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "push visit_status response", $response);

            ReleaseItem::createReleaseLog($this->release, ReleaseItem::CONFIG_KEY_VISIT_STATUS, [
                'online_config' => '',
                'original_config'=> $visitStatus,
                'push_config' => $visitStatus,
                'response' => $response
            ]);
        });
    }
}
