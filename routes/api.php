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
    Route::get('/component/{componentAppId}/component_verify_ticket', 'ComponentController@componentVerifyTicket')->name('getComponentVerifyTicket');
    Route::get('/component/{componentAppId}/component_access_token', 'ComponentController@componentAccessToken');
    Route::put('/component/{componentAppId}/ext_json', 'ComponentController@extJson');
    Route::get('/component/{componentAppId}/config', 'ComponentController@config');
    Route::put('/component/{componentAppId}/domain', 'ComponentController@domain');
    Route::put('/component/{componentAppId}/web_view_domain', 'ComponentController@webViewDomain');
    Route::put('/component/{componentAppId}/tester', 'ComponentController@tester');
    Route::put('/component/{componentAppId}/support_version', 'ComponentController@supportVersion');
    Route::put('/component/{componentAppId}/visit_status', 'ComponentController@visitStatus');
    Route::post('/component/{componentAppId}/config_sync', 'ComponentController@configSync');
    Route::post('/component/{componentAppId}/config_sync', 'ComponentController@configSync');

    /**
     * 小程序管理
     */
    Route::get('/component/{componentAppId}/bind_url', 'MiniProgramController@bindUrl');
    Route::get('/component/{componentAppId}/bind/callback', 'MiniProgramController@bindCallback')->name('MiniProgramBindCallback');
    Route::get('/component/{componentAppId}/mini_program', 'MiniProgramController@index');
    Route::get('/component/{componentAppId}/mini_program/{miniProgramAppId}', 'MiniProgramController@show');
    Route::put('/component/{componentAppId}/mini_program/{miniProgramAppId}', 'MiniProgramController@update');
    Route::get('/component/{componentAppId}/mini_program/{miniProgramAppId}/access_token', 'MiniProgramController@accessToken');
    Route::post('/component/{componentAppId}/mini_program/{miniProgramAppId}/commit', 'CodeController@commit');
    Route::get('/component/{componentAppId}/mini_program/{miniProgramAppId}/qrcode', 'CodeController@qrcode');
    Route::get('/component/{componentAppId}/mini_program/{miniProgramAppId}/category', 'CodeController@category');
    Route::get('/component/{componentAppId}/mini_program/{miniProgramAppId}/page', 'CodeController@page');
    Route::post('/component/{componentAppId}/mini_program/{miniProgramAppId}/audit', 'CodeController@audit');
    Route::get('/component/{componentAppId}/mini_program/{miniProgramAppId}/audit/{audit}', 'CodeController@auditStatus');
    Route::get('/component/{componentAppId}/mini_program/{miniProgramAppId}/last_audit', 'CodeController@lastAuditStatus');
    Route::post('/component/{componentAppId}/mini_program/{miniProgramAppId}/release', 'CodeController@release');
    Route::post('/component/{componentAppId}/mini_program/{miniProgramAppId}/revert_code_release', 'CodeController@revertCodeRelease');

    Route::get('/component/{componentAppId}/mini_program/{miniProgramAppId}/tester', 'MiniProgramController@tester');
    Route::post('/component/{componentAppId}/mini_program/{miniProgramAppId}/tester', 'MiniProgramController@bindTester');
    Route::delete('/component/{componentAppId}/mini_program/{miniProgramAppId}/tester', 'MiniProgramController@unbindTester');


    /**
     * 模板管理
     */
    Route::get('/component/{componentAppId}/draft', 'TemplateController@draft');
    Route::get('/component/{componentAppId}/template', 'TemplateController@index');
    Route::delete('/component/{componentAppId}/template/{templateId}');
    Route::post('/component/{componentAppId}/template');
    Route::post('/component/{componentAppId}/template/{templateId}/release');
});






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


