<?php

namespace App\Jobs;

use App\Logs\ReleaseCommonQueueLogQueueLog;
use App\Models\ReleaseItem;
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

            $extJson = isset($this->config['ext_json']) ? json_encode($this->config['ext_json']) : '{}';
            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "push miniProgram code commit ext_json origin", json_decode($extJson, true));
            $miniProgramTemplateConfig = $this->miniProgram->getTemplateConfig($templateId);
            if($miniProgramTemplateConfig){
                $config = json_decode($miniProgramTemplateConfig->config, true);
                if(isset($config['ext_json'])){
                    $extJson = json_encode($config['ext_json']);
                    ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "push miniProgram code commit ext_json independent origin", json_decode($extJson, true));
                }

            }

            $extJson = $this->miniProgram->assign($extJson);

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

            $this->task->building($extJson, $response, ReleaseItem::STATUS_SUCCESS, '');

            return $response;
        });
    }

    protected function isResponseOk($response)
    {
        if(parent::isResponseOk($response)){
            if($this->release->shouldAudit()){
                ReleaseItem::createAuditTask($this->release, $this->miniProgram,  $this->config);
            }
            return true;
        }
        return false;
    }
}
