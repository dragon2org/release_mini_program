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
        $this->proccess($this, function (Application $app) {

            $setted = $app->code->getSupportVersion();
            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "pull support_version", $setted);

            $supportVersion = $this->config->supportVersion;
            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "push support_version", [$supportVersion]);

            $response = $app->code->setSupportVersion($supportVersion);
            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "push support_version response", $response);

            ReleaseItem::createReleaseLog($this->release, ReleaseItem::CONFIG_KEY_SUPPORT_VERSION, [
                'online_config' => $setted,
                'original_config'=> $supportVersion,
                'push_config' => $supportVersion,
                'response' => $response
            ]);
        });
    }
}
