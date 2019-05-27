<?php

namespace App\Jobs;

use App\Logs\ReleaseCommonQueueLogQueueLog;
use App\Models\ReleaseItem;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Tester;
use \EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Application;
use Illuminate\Support\Arr;


class SetMiniProgramTester extends BaseReleaseJobWithLog implements ShouldQueue
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
        $this->proccess($this, function (Application $app) {
            $setted = $app->tester->list();
            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "pull tester", $setted);

            ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "push tester", $this->config);
            $pushConfig = [];
            $result = [];

            $tempSetted = $setted['members'] ?? [];
            $tempSetted = Arr::pluck($tempSetted, 'userstr');
            $tempSetted = Tester::whereIn('userstr', $tempSetted)->pluck('wechat_id')->toArray();
            foreach($this->config as $tester){
                if(!in_array($tester, $tempSetted)){
                    $response = $app->tester->bind($tester);
                    ReleaseCommonQueueLogQueueLog::info($this->miniProgram, "push tester: {$tester} response", $response);
                    $pushConfig[] = $tester;
                    $result[] = [
                        'response' => $response,
                        'tester' => $tester
                    ];
                }else{
                    $pushConfig[] = '';
                    $result[] = [
                        'response' => [
                            'un_modify' => 1
                        ],
                        'tester' => $tester
                    ];
                }
            }
            $this->task->building($pushConfig, $result, ReleaseItem::STATUS_SUCCESS, $setted);

            return $result;
        });
    }

    protected function isResponseOk($response)
    {
        $status = true;
        foreach($response as $res){
            if(false === parent::isResponseOk($res['response'])){
                $status =  false;
                continue;
            }

            Tester::firstOrCreate([
                'userstr' => $res['response']['userstr'],
            ], [
                'wechat_id' => $res['tester'],
                'mini_program_id' => $this->miniProgram->mini_program_id
            ]);
        }

        return $status;
    }
}
