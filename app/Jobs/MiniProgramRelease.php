<?php

namespace App\Jobs;

use App\Logs\ReleaseCommonQueueLogQueueLog;
use App\Models\Release;
use App\Models\ReleaseItem;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use \EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Application;


class MiniProgramRelease extends BaseReleaseJobWithLog implements ShouldQueue
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
    public function __construct(ReleaseItem $task)
    {
        $this->task = $task;
        $this->miniProgram = $task->miniProgram;
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
            $response = $app->code->release();
            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "push domain response", $response);
            return $response;
        });
    }

    protected function isResponseOk($response)
    {
        if(parent::isResponseOk($response)){
            ReleaseItem::create([
                'release_id' => $this->release->release_id,
                'name' => ReleaseItem::CONFIG_KEY_RELEASE,
                'original_config' => $this->release->config,
                'response' => json_encode($response),
                'status' => ReleaseItem::STATUS_SUCCESS,
                'mini_program_id' => $this->miniProgram->mini_program_id,
            ]);
            $this->release->status = Release::RELEASE_STATUS_RELEASED;
            $this->release->save();

            return true;
        }
        return false;
    }

}
