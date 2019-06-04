<?php
/**
 * Created by PhpStorm.
 * User: harlen
 * Date: 2019/4/24
 * Time: 10:41 PM
 */

namespace App\Http\Middleware;


use App\Exceptions\UnprocessableEntityHttpException;
use App\Models\Component;
use App\Services\ReleaseService;
use Closure;

class ComponentInjection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $params = $request->route()->parametersWithoutNulls();
        if(!isset($params['componentAppId'])){
            throw new UnprocessableEntityHttpException(trans('注入component配置信息失败'));
        }
        $this->injection($params['componentAppId']);
        
        if(isset($params['miniProgramAppId'])){
            app('dhb.component.core')->setMiniProgramByAppId($params['miniProgramAppId']);
        }
        return $next($request);
    }

    protected function injection($componentAppId)
    {
        app()->singleton('dhb.component.core', function() use($componentAppId) {
            $component = (new Component())->getComponent($componentAppId);
            $service = (new ReleaseService($component));
            $service->setOpenPlatform();
            return $service;
        });
    }
}