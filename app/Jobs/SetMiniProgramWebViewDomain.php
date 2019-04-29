<?php

namespace App\Jobs;

use App\Logs\ReleaseCommonQueueLogQueueLog;
use App\Models\MiniProgram;
use App\ReleaseConfigurator;
use App\Releaser;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use \EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Application;

class SetMiniProgramWebViewDomain extends BaseReleaseJobWithLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $miniProgram;

    protected $config;

    protected $templateId;

    const VERSION = '1.0.0';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(MiniProgram $miniProgram, ReleaseConfigurator $config, $templateId)
    {
        $this->miniProgram = $miniProgram;
        $this->config = $config;
        $this->templateId = $templateId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->proccess($this, function(Application $app){
            $setted = $app->domain->setWebviewDomain([], 'get');
            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "拉取业务服务器域名", $setted);

            $domain = $this->config->webViewDomain;
            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "推送业务服务器域名", $domain);

            $response = $app->domain->setWebviewDomain($domain, $domain['action']);
            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "推送业务服务器域名响应", $response);
        });
    }
}
