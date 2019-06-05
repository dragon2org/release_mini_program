<?php
/**
 * Created by PhpStorm.
 * User: chengyuanlong
 * Date: 2018-12-10
 * Time: 19:08
 */

namespace App\Http\Controllers;

use App\Facades\ReleaseFacade;
use App\Jobs\SetMiniProgramCodeCommit;
use App\Jobs\TestJob;
use App\Models\Component;
use App\Models\ReleaseItem;
use App\Models\ValidateFile;
use App\Services\ReleaseService;
use Illuminate\Support\Facades\Log;

class ComponentController extends Controller
{
    public function serve()
    {
        return ReleaseFacade::service()->server();
    }

    public function hostValidate($validateFilename)
    {
        $file = ValidateFile::where('filename', $validateFilename)->first();

        if (!isset($file->content)) {
            abort(404, 'not found pages');
        }

        return response($file->content, 200, [
            "Content-type" => "application/octet-stream",
            "Accept-Ranges" => "bytes",
            "Accept-Length" => strlen($file->content),
            "Content-Disposition" => "attachment; filename={$validateFilename}"
        ]);
    }

    public function debug()
    {

    }
}