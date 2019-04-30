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
        $class = get_class($job);
        try {
            ReleaseCommonQueueLogQueueLog::info( $this->miniProgram, "release queue job begin", ['class' => $class]);
            $service = Releaser::build($this->miniProgram()->component->app_id);
            $miniProgramApp = $service->setMiniProgram($this->miniProgram()->app_id);
            call_user_func($callback, $miniProgramApp, $service->openPlatform);
            ReleaseCommonQueueLogQueueLog::info( $this->miniProgram, "release queue job end", ['class' => $class]);
        } catch (\Exception $e) {
            ReleaseCommonQueueLogQueueLog::error( $this->miniProgram, "release queue job failed", ['class' => $class, 'message'=> $e->getMessage()]);
            throw $e;
        }

        return true;
    }

    public function miniProgram()
    {
        return $this->miniProgram;
    }
}
