<?php
/**
 * Created by PhpStorm.
 * User: chengyuanlong
 * Date: 2018-12-07
 * Time: 16:26
 */

namespace App\Http\Controllers\Api\V1;

use App\Http\ApiResponse;
use App\Http\Controllers\Controller as BaseController;

class Controller extends BaseController
{
    protected $response;

    public function __construct(ApiResponse $response)
    {
        $this->response = $response;
    }
}