<?php
/**
 * Created by PhpStorm.
 * User: harlen
 * Date: 2019/4/29
 * Time: 9:18 PM
 */

namespace App\Jobs;


use App\Logs\ReleaseCommonQueueLogQueueLog;
use App\Models\ReleaseItem;
use App\Releaser;
use Closure;
use Illuminate\Contracts\Queue\ShouldQueue;


class BaseReleaseJobWithLog
{
    /**
     * @var ReleaseItem
     */
    protected $task;

    protected $miniProgram;

    public function proccess(ShouldQueue $job, Closure $callback)
    {
        $class = get_class($job);
        try {
            ReleaseCommonQueueLogQueueLog::info( $this->miniProgram, "release queue job begin", ['class' => $class]);
            $service = Releaser::build($this->miniProgram()->component->app_id);
            $miniProgramApp = $service->setMiniProgramByAppId($this->miniProgram()->app_id);
            $this->task->applyBuilding();
            $response = call_user_func($callback, $miniProgramApp, $service->openPlatform);
            ReleaseCommonQueueLogQueueLog::info( $this->miniProgram, "release queue job end", ['class' => $class]);
            if($this->isResponseOk($response)){
                $this->task->applyBuildSuccess();
            }else{
                $this->task->applyBuildFailed();
            }
        } catch (\Exception $e) {
            $this->task->applyBuildFailed();
            ReleaseCommonQueueLogQueueLog::error( $this->miniProgram, "release queue job failed", ['class' => $class, 'message'=> $e->getMessage()]);
            throw $e;
        }

        return true;
    }

    public function miniProgram()
    {
        return $this->miniProgram;
    }

    protected function isResponseOk($response)
    {
        if( (isset($response['errcode']) && $response['errcode'] === 0) ||
            (isset($response['un_modify']) && $response['un_modify'] === 1)
        ){
            return true;
        }
        return false;
    }

}
