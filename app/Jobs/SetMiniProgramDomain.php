<?php

namespace App\Jobs;

use App\Logs\ReleaseCommonQueueLogQueueLog;
use App\Logs\ReleaseOutQueueLog;
use App\Models\MiniProgram;
use App\ReleaseConfigurator;
use App\Releaser;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SetMiniProgramDomain implements ShouldQueue
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
//        if(!isset($this->config->domain)){
//            return ;
//        }

        $service = Releaser::build($this->miniProgram->component->app_id);
        $app = $service->setMiniProgram($this->miniProgram->app_id);
        //step 1. 获取已经设置的业务域名
        $setted = $app->domain->modify(['action' => 'get']);
        ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "拉取服务器域名", $setted);

        $domain = $this->config->getDomain();
        ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "推送服务器域名", $domain);
        $response = $app->domain->modify($domain);
        ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "推送服务器域名响应", $response);

        //TODO::改成匿名函数处理
        //ReleaseOutQueueLog::info($this->miniProgram, $this->config, $this->templateId, self::class, self::VERSION);
        //ReleaseOutQueueLog::info($this->miniProgram, $this->config, $this->templateId, self::class, self::VERSION);
    }

    public function toArray()
    {
        return [];
    }
}
