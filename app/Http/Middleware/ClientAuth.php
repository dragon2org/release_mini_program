<?php

namespace App\Http\Middleware;

use App\Logs\IllegalRequestLog;
use Closure;

class ClientAuth
{
    /**
     * 外部服务部允许请求api接口
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $whitelist = env('SERVICE_WHITE_LIST');
        if(env('APP_ENV') === 'prod' && strpos('/api',$request->route()->getPrefix()) === 0 && !in_array($request->ip(), explode(',', $whitelist))){
            IllegalRequestLog::info($request->ip());
            abort(404);
        }
        return $next($request);
    }
}
