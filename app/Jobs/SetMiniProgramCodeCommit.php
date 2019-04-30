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
use \EasyWeChat\OpenPlatform\Application as ComponentApplication;


class SetMiniProgramCodeCommit extends BaseReleaseJobWithLog implements ShouldQueue
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
        $this->proccess($this, function(Application $app, ComponentApplication $componentApp){
            $templateId = intval($this->release->template_id);
            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "pull component code templateId", [$templateId]);

            $template = $componentApp->code_template->list();
            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "pull component code template", $template);

            $remoteTemplates  = $template['template_list'] ?? [];

            $templateInfo = null;
            foreach($remoteTemplates as $template){
                if($template['template_id'] === $templateId){
                    $templateInfo = $template;
                }
            }
            if(is_null($templateInfo)){
                ReleaseCommonQueueLogQueueLog::error($this->miniProgram, "pull component code template info not found", $template);
                return false;
            }

            $this->release->user_desc = $templateInfo['user_desc'];
            $this->release->user_version = $templateInfo['user_desc'];
            $this->release->save();

            $extJson = $this->config->extJson;
            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "push miniProgram code commit ext_json origin", [$extJson]);

            $extJson = str_replace('$APP_ID$', $this->miniProgram->app_id,  $extJson);
            $extJson = str_replace('$COMPANY_ID$', $this->miniProgram->company_id,  $extJson);

            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "push miniProgram code commit ext_json result", [$extJson]);

            $params = [
                'template_id' => $this->release->template_id,
                'user_desc' => $templateInfo['user_desc'],
                'user_version' => $templateInfo['user_version'],
                'ext_json' => $extJson,
            ];

            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "push code commit", $params);

            $response = $app->code->commit($templateId, $extJson, $params['user_version'], $params['user_desc']);
            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "push code commit response", $response);


            ReleaseItem::createReleaseLog($this->release, ReleaseItem::CONFIG_KEY_DOMAIN, [
                'online_config' => '',
                'original_config'=> $this->config->extJson,
                'push_config' => $extJson,
                'response' => $response
            ]);
            return true;
        });
    }
}
