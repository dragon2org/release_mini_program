<?php

use Illuminate\Support\Facades\Route;

Route::pattern('validateFilename', '[A-Za-z0-9_]+\.txt$');
Route::get('/{validateFilename}', 'ComponentController@hostValidate');

Route::group([
    'middleware' => ['force-json', 'component-injection']
], function(){
    Route::any('/component/{componentAppId}/mini_program/{miniProgram}/serve', 'MiniProgramController@serve')->name('componentMiniProgramServe');
    Route::post('/component/{componentAppId}/serve', 'ComponentController@serve')->name('componentServe');;

});



Route::group([
    'middleware' => 'force-json',
    'prefix' => 'api/v1',
    'namespace' => 'Api\V1',
], function () {
    /**
     * 三方平台管理
     */
    Route::post('/component/create_before', 'ComponentController@createBefore');
    Route::post('/component', 'ComponentController@create');
    Route::get('/component', 'ComponentController@list');
    Route::get('/component/{componentAppId}', 'ComponentController@show');
    Route::put('/component/{componentAppId}', 'ComponentController@update');
    Route::delete('/component/{componentAppId}');
});

Route::group([
    'middleware' => ['force-json', 'component-injection'],
    'prefix' => 'api/v1',
    'namespace' => 'Api\V1',
], function () {
    /**
     * 三方平台配置
     */
    Route::get('/component/{componentAppId}/component_verify_ticket', 'ComponentController@componentVerifyTicket')->name('getComponentVerifyTicket');
    Route::get('/component/{componentAppId}/component_access_token', 'ComponentController@componentAccessToken');
    Route::put('/component/{componentAppId}/config/ext_json', 'ComponentController@extJson');
    Route::get('/component/{componentAppId}/config', 'ComponentController@config');
    Route::put('/component/{componentAppId}/config/domain', 'ComponentController@domain');
    Route::put('/component/{componentAppId}/config/web_view_domain', 'ComponentController@webViewDomain');
    Route::put('/component/{componentAppId}/config/tester', 'ComponentController@tester');
    Route::put('/component/{componentAppId}/config/support_version', 'ComponentController@supportVersion');
    Route::put('/component/{componentAppId}/config/visit_status', 'ComponentController@visitStatus');
    Route::post('/component/{componentAppId}/config/sync', 'ComponentController@configSync');

    /**
     * 小程序管理
     */
    Route::get('/component/{componentAppId}/bind_url', 'MiniProgramController@bindUrl');
    Route::get('/component/{componentAppId}/bind/callback', 'MiniProgramController@bindCallback')->name('MiniProgramBindCallback');
    Route::get('/component/{componentAppId}/mini_program', 'MiniProgramController@index');
    Route::get('/component/{componentAppId}/mini_program/{miniProgramAppId}', 'MiniProgramController@show');
    Route::put('/component/{componentAppId}/mini_program/{miniProgramAppId}', 'MiniProgramController@update');
    Route::delete('/component/{componentAppId}/mini_program/{miniProgramAppId}', 'MiniProgramController@delete');
    Route::get('/component/{componentAppId}/mini_program/{miniProgramAppId}/access_token', 'MiniProgramController@accessToken');
    Route::get('/component/{componentAppId}/mini_program/{miniProgramAppId}/session_key', 'MiniProgramController@sessionKey');
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
    Route::get('/component/{componentAppId}/mini_program/{miniProgramAppId}/support_version', 'CodeController@supportVersion');
    Route::post('/component/{componentAppId}/mini_program/{miniProgramAppId}/support_version', 'CodeController@SetSupportVersion');

    /**
     * 模板管理
     */
    Route::get('/component/{componentAppId}/draft', 'TemplateController@draft');
    Route::get('/component/{componentAppId}/template', 'TemplateController@index');
    Route::delete('/component/{componentAppId}/template/{templateId}', 'TemplateController@delete');
    Route::post('/component/{componentAppId}/template', 'TemplateController@draftToTemplate');
    Route::post('/component/{componentAppId}/template/sync', 'TemplateController@templateSync');
    Route::post('/component/{componentAppId}/template/{templateId}/release', 'TemplateController@release');

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

    /*
     * 构建管理
     */
    Route::get('/component/{componentAppId}/release_task', 'ReleaseController@index');
    Route::get('/component/{componentAppId}/release_task/{releaseId}', 'ReleaseController@detail');
    Route::get('/component/{componentAppId}/release_task/{releaseId}/retry', 'ReleaseController@retry');
});