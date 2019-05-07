<?php
/**
 * Created by PhpStorm.
 * User: chengyuanlong
 * Date: 2018-12-10
 * Time: 19:09
 */

namespace App\Http\Controllers;


use App\Logs\ReleaseAuditLog;

class MiniProgramController extends Controller
{
    public function serve()
    {
        ReleaseAuditLog::info('小程序消息与事件: '. request()->getContent(false), []);
        return app('dhb.component.core')->miniProgramServe();
    }
}