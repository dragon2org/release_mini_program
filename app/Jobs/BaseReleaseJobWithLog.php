<?php
/**
 * Created by PhpStorm.
 * User: harlen
 * Date: 2019/4/29
 * Time: 9:18 PM
 */

namespace App\Jobs;


use App\Logs\ReleaseCommonQueueLogQueueLog;
use App\Releaser;
use Closure;
use Illuminate\Contracts\Queue\ShouldQueue;


class BaseReleaseJobWithLog
{
    protected $miniProgram;

    public function proccess(ShouldQueue $job, Closure $callback)
    {
        try {
            ReleaseCommonQueueLogQueueLog::info( $this->miniProgram, "队列任务执行 begin", ['class' => get_class($job)]);
            $service = Releaser::build($this->miniProgram()->component->app_id);
            $app = $service->setMiniProgram($this->miniProgram()->app_id);
            call_user_func($callback, $app);
            ReleaseCommonQueueLogQueueLog::info( $this->miniProgram, "队列任务执行 end", ['class' => get_class($job)]);
        } catch (\Exception $e) {
            ReleaseCommonQueueLogQueueLog::error( $this->miniProgram, "队列任务执行 failed", ['class' => get_class($job)]);
            throw $e;
        }
    }

    public function miniProgram()
    {
        return $this->miniProgram;
    }
}
