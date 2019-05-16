<?php
/**
 * Created by PhpStorm.
 * User: chengyuanlong
 * Date: 2018-12-10
 * Time: 19:08
 */

namespace App\Http\Controllers;

use App\Jobs\SetMiniProgramCodeCommit;
use App\Jobs\TestJob;
use App\Models\ReleaseItem;
use App\Models\ValidateFile;

class ComponentController extends Controller
{
    public function serve()
    {
        return app('dhb.component.core')->server();
    }

    public function hostValidate($validateFilename)
    {
        $file = ValidateFile::where('filename', $validateFilename)->first();

        if(!isset($file->content)){
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
        if(env('APP_ENV') !== 'local'){
            abort(404);
        }
       TestJob::dispatch(['aaa'=> 123])->onConnection('kafka');
       echo 'success';
    }
}