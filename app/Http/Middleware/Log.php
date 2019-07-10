<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log as LogFacade;
use Symfony\Component\Stopwatch\Stopwatch;

class Log
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $request->stopwatch = new Stopwatch();
        $request->stopwatch->start('api-log');

        return $next($request);
    }

    public function terminate($request, $response){
        $this->end = microtime(true);
        $this->stopwatchEvent = $request->stopwatch->start('api-log');;
        $this->log($request, $response);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param $response
     */
    protected function log($request, $response){
        $duration = $this->stopwatchEvent->getDuration();
        $path = $request->path();
        $method = $request->getMethod();
        $ip = $request->getClientIp();

        $status = $response->getStatusCode();
        $content = $response->getContent();

        $log = "{$ip}: [{$status}] {$method}@{$path} - {$duration}ms";

        LogFacade::channel('apilog')->info($log);
        if($request->getContentType() === 'xml'){
            $body = $request->getContent();
        }else{
            $body =  json_encode($request->all());
        }
        LogFacade::channel('apilog')->info('REQUEST:' . $body);
        LogFacade::channel('apilog')->info('HEADER:' . json_encode($request->header()));
        LogFacade::channel('apilog')->info('RESPONSE:' . $content);
    }

}
