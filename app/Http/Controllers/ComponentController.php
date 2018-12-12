<?php
/**
 * Created by PhpStorm.
 * User: chengyuanlong
 * Date: 2018-12-10
 * Time: 19:08
 */

namespace App\Http\Controllers;


use App\Models\Component;
use EasyWeChat\Factory;
use EasyWeChat\OpenPlatform\Server\Guard;
use Illuminate\Support\Facades\Cache;
use Log;

class ComponentController extends Controller
{
    public function serve($componentAppId)
    {
        $component = Component::where('app_id', $componentAppId)->first();
        $config = Component::getConfig($componentAppId);
        $openPlatform = Factory::openPlatform($config);
        $server = $openPlatform->server;

        // 处理授权成功事件
        $server->push(function ($message) {

        }, Guard::EVENT_AUTHORIZED);

        // 处理授权更新事件
        $server->push(function ($message) {

        }, Guard::EVENT_UPDATE_AUTHORIZED);

        // 处理授权取消事件
        $server->push(function ($message) {

        }, Guard::EVENT_UNAUTHORIZED);

        // 处理VERIFY_TICKET
        $server->push(function ($message) use($component) {
            Log::info('ComponentVerifyTicket:', $message);
            $component->verify_ticket = $message['ComponentVerifyTicket'];
            $component->save();
            Cache::forget(Component::getCacheKey($component->app_id));
        }, Guard::EVENT_COMPONENT_VERIFY_TICKET);

        return $server->serve();
    }

    public function hostValidate($validateFilename)
    {
        $content = Component::where('validate_filename', $validateFilename)->value('validate_content');

        if (empty($content)) {
            abort(404, 'not found pages');
        }

        return response($content, 200, [
            "Content-type" => "application/octet-stream",
            "Accept-Ranges" => "bytes",
            "Accept-Length" => strlen($content),
            "Content-Disposition" => "attachment; filename={$validateFilename}"
        ]);
    }
}