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

            if($this->isModify($setted, $domain)){
                $response = $app->domain->modify($domain);
            }else{
                $response['un_modify'] = 1;
            }

            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "push domain response", $response);

            $this->task->building($domain, $response, ReleaseItem::STATUS_SUCCESS, $setted);

            return $response;
        });
    }

    protected function isModify($old, $new)
    {
        foreach(['requestdomain', 'wsrequestdomain', 'uploaddomain', 'downloaddomain'] as $key){
            $attribute1 = $new[$key];
            $attribute2 = $old[$key];
            if(count(array_diff($attribute1, $attribute2)) !== 0 || count(array_diff($attribute2, $attribute1)) !== 0){
                return true;
            }
        }
        return false;
    }

    protected function isResponseOk($response)
    {
        if(parent::isResponseOk($response)){
            foreach(['invalid_requestdomain', 'invalid_wsrequestdomain', 'invalid_uploaddomain', 'invalid_downloaddomain'] as $key){
                if(!isset($response['un_modify']) && count($response[$key])> 0){
                    return false;
                }
            }
        }
        return false;
    }
}
