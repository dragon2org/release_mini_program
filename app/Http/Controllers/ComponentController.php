<?php
/**
 * Created by PhpStorm.
 * User: chengyuanlong
 * Date: 2018-12-10
 * Time: 19:08
 */

namespace App\Http\Controllers;


use App\Models\Component;
use App\Services\ComponentService;
use EasyWeChat\Factory;
use EasyWeChat\OpenPlatform\Server\Guard;
use Illuminate\Support\Facades\Cache;
use Log;

class ComponentController extends Controller
{
    public function serve()
    {
        return (new ComponentService())->server();
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