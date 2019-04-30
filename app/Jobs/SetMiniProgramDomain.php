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

            $setted = $app->domain->modify(['action' => 'get']);
            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "pull domain", $setted);

            $domain = $this->config->getDomain();
            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "push domain", $domain);

            $response = $app->domain->modify($domain);
            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "push domain response", $response);

            ReleaseItem::createReleaseLog($this->release, ReleaseItem::CONFIG_KEY_DOMAIN, [
                'online_config' => $setted,
                'original_config'=> $domain,
                'push_config' => $domain,
                'response' => $response
            ]);
            return true;
        });
    }

}
