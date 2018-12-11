<?php

use Illuminate\Http\Request;

Route::pattern('validateFilename', '[A-Za-z0-9_]+\.txt$');
Route::get('/{validateFilename}', 'ComponentController@hostValidate');


Route::any('/component/{componentAppId}/mini_program/{miniProgram}/serve', 'MiniProgramController@serve')->name('componentMiniProgramServe');
Route::post('/component/{componentAppId}/serve', 'ComponentController@serve')->name('componentServe');;


Route::group([
    'prefix' => 'api/v1',
    'namespace' => 'Api\V1',
],  function(){
    /**
     * 三方平台管理
     */
    Route::post('/component', 'ComponentController@create');
    Route::get('/component');
    Route::get('/component/{componentAppId}');
    Route::put('/component/{componentAppId}');
    Route::delete('/component/{componentAppId}');

    /**
     * 小程序管理
     */
    Route::get('/component/{componentAppId}/bind_component', 'MiniProgramController@bind_component');
    Route::get('/component/{componentAppId}/mini_program');
    Route::post('/component/{componentAppId}/domain');
    Route::post('/component/{componentAppId}/web_view_domain');
    Route::put('/component/{componentAppId}/mini_program/{miniProgramAppId}');
});

/**
 * 模板管理
 */
Route::get('/component/{componentAppId}/draft');
Route::get('/component/{componentAppId}/template');
Route::delete('/component/{componentAppId}/template/{templateId}');
Route::post('/component/{componentAppId}/template');
Route::post('/component/{componentAppId}/template/{templateId}/release');




/**
 * 代码管理
 */
Route::post('/component/{componentAppId}/mini_program/{miniProgram}/commit');
Route::get('/component/{componentAppId}/mini_program/{miniProgram}/qrcode');
Route::get('/component/{componentAppId}/mini_program/{miniProgram}/category');
Route::get('/component/{componentAppId}/mini_program/{miniProgram}/page');
Route::post('/component/{componentAppId}/mini_program/{miniProgram}/audit');
Route::get('/component/{componentAppId}/mini_program/{miniProgram}/audit/{audit}');
Route::get('/component/{componentAppId}/mini_program/{miniProgram}/last_audit');
Route::post('/component/{componentAppId}/mini_program/{miniProgram}/release');
Route::post('/component/{componentAppId}/mini_program/{miniProgram}/visit_status');
Route::get('/component/{componentAppId}/mini_program/{miniProgram}/revert_code_release');
Route::post('/component/{componentAppId}/mini_program/{miniProgram}/support_version');

/**
 * 微信登录和解密. 上报js_code
 */
Route::get('/component/{componentAppId}/mini_program/{miniProgram}/');

/**
 * 成员管理
 */
Route::get('/component/{componentAppId}/mini_program/{miniProgram}/tester');
Route::post('/component/{componentAppId}/mini_program/{miniProgram}/tester');
Route::delete('/component/{componentAppId}/mini_program/{miniProgram}/tester/{wechatid}');


