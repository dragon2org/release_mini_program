<?php

namespace App\Jobs;

use App\Models\MiniProgram;
use App\ReleaseConfigurator;
use App\Releaser;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SetMiniProgramWebViewDomain implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $miniProgram;

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
//        if(!isset($this->config->webViewDomain)){
//            return ;
//        }

        $service = Releaser::build($this->miniProgram->component->app_id);
        $app = $service->setMiniProgram($this->miniProgram->app_id);
        //step 1. 获取已经设置的业务域名

        $domain = $this->config->webViewDomain;
        $result = $app->domain->setWebviewDomain($domain['webviewdomain'], 'set');
        dd($result);
    }
}
