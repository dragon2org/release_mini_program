<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::pattern('validateFilename', '[A-Za-z0-9_]+\.txt$');
Route::get('/{validateFilename}', '\App\Http\Controllers\Api\V1\ComponentController@hostValidate');


//通过该URL接收公众号或小程序消息和事件推送
Route::any('/component/{componentAppId}/mini_program/{miniProgram}/serve', 'ComponentController@serve');
//授权时间和component_verify_ticket推送地址
Route::post('/component/{componentAppId}/serve', 'MiniProgramController@serve');