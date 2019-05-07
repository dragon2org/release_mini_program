<?php


namespace App\Http\Controllers\Api\V1;


use App\Http\Requests\ToolsCodeCommit;

class ToolsController extends Controller
{
    public function buildCodeCommitParams(ToolsCodeCommit $request)
    {
        return response()->json([
            'template_id' => $request->template_id,
            'user_version' => $request->user_version,
            'user_desc' => $request->user_desc,
            'ext_json' => json_encode($request->ext_json)
        ]);
    }
}