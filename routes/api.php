<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['force-json'],
    'prefix' => 'api',
    'namespace' => 'Api\V1',
], function () {
    /**
     * 三方平台管理
     */
    Route::post('/v1/component/create_before', 'ComponentController@createBefore');
    Route::post('/v1/component', 'ComponentController@create');
    Route::get('/v1/component', 'ComponentController@index');
    Route::get('/v1/component/{componentAppId}', 'ComponentController@show');
    Route::put('/v1/component/{componentAppId}', 'ComponentController@update');
    Route::delete('/v1/component/{componentAppId}', 'ComponentController@delete');
});

Route::group([
    'middleware' => ['force-json', 'component-injection'],
    'prefix' => 'api',
    'namespace' => 'Api\V1',
], function () {
    /**
     * 三方平台配置
     */
    Route::get('/v1/component/{componentAppId}/component_verify_ticket', 'ComponentController@componentVerifyTicket')->name('getComponentVerifyTicket');
    Route::get('/v1/component/{componentAppId}/component_access_token', 'ComponentController@componentAccessToken');
    Route::put('/v1/component/{componentAppId}/config/ext_json', 'ComponentController@extJson');
    Route::get('/v1/component/{componentAppId}/config', 'ComponentController@config');
    Route::put('/v1/component/{componentAppId}/config/domain', 'ComponentController@domain');
    Route::put('/v1/component/{componentAppId}/config/web_view_domain', 'ComponentController@webViewDomain');
    Route::put('/v1/component/{componentAppId}/config/tester', 'ComponentController@tester');
    Route::put('/v1/component/{componentAppId}/config/support_version', 'ComponentController@supportVersion');
    Route::put('/v1/component/{componentAppId}/config/visit_status', 'ComponentController@visitStatus');
    Route::post('/v1/component/{componentAppId}/config/sync', 'ComponentController@configSync');

    /**
     * 小程序管理
     */
    Route::post('/v1/component/{componentAppId}/bind_url', 'MiniProgramController@bindUrl');
    Route::get('/v1/component/{componentAppId}/bind', 'MiniProgramController@bind')->name('MiniProgramBind');
    Route::get('/v1/component/{componentAppId}/bind/callback', 'MiniProgramController@bindCallback')->name('MiniProgramBindCallback');
    Route::get('/v1/component/{componentAppId}/mini_program', 'MiniProgramController@index');
    Route::get('/v1/component/{componentAppId}/mini_program/{miniProgramAppId}', 'MiniProgramController@show');
    Route::put('/v1/component/{componentAppId}/mini_program/{miniProgramAppId}', 'MiniProgramController@update');
    Route::delete('/v1/component/{componentAppId}/mini_program/{miniProgramAppId}', 'MiniProgramController@delete');
    Route::get('/v1/component/{componentAppId}/mini_program/{miniProgramAppId}/access_token', 'MiniProgramController@accessToken');
    Route::get('/v1/component/{componentAppId}/mini_program/{miniProgramAppId}/session_key', 'MiniProgramController@sessionKey');
    Route::post('/v1/component/{componentAppId}/mini_program/{miniProgramAppId}/commit', 'CodeController@commit');
    Route::get('/v1/component/{componentAppId}/mini_program/{miniProgramAppId}/qrcode', 'CodeController@qrcode');
    Route::get('/v1/component/{componentAppId}/mini_program/{miniProgramAppId}/category', 'CodeController@category');
    Route::get('/v1/component/{componentAppId}/mini_program/{miniProgramAppId}/page', 'CodeController@page');
    Route::post('/v1/component/{componentAppId}/mini_program/{miniProgramAppId}/audit', 'CodeController@audit');
    Route::get('/v1/component/{componentAppId}/mini_program/{miniProgramAppId}/audit/{audit}', 'CodeController@auditStatus');
    Route::get('/v1/component/{componentAppId}/mini_program/{miniProgramAppId}/last_audit', 'CodeController@lastAuditStatus');
    Route::post('/v1/component/{componentAppId}/mini_program/{miniProgramAppId}/withdraw_audit', 'CodeController@withdrawAudit');
    Route::post('/v1/component/{componentAppId}/mini_program/{miniProgramAppId}/release', 'CodeController@release');
    Route::post('/v1/component/{componentAppId}/mini_program/{miniProgramAppId}/revert_code_release', 'CodeController@revertCodeRelease');

    Route::get('/v1/component/{componentAppId}/mini_program/{miniProgramAppId}/tester', 'MiniProgramController@tester');
    Route::post('/v1/component/{componentAppId}/mini_program/{miniProgramAppId}/tester', 'MiniProgramController@bindTester');
    Route::delete('/v1/component/{componentAppId}/mini_program/{miniProgramAppId}/tester', 'MiniProgramController@unbindTester');
    Route::get('/v1/component/{componentAppId}/mini_program/{miniProgramAppId}/support_version', 'CodeController@supportVersion');
    Route::post('/v1/component/{componentAppId}/mini_program/{miniProgramAppId}/support_version', 'CodeController@SetSupportVersion');
    Route::post('/v1/component/{componentAppId}/mini_program/{miniProgramAppId}/visit_status', 'CodeController@visitStatus');
    Route::put('/v1/component/{componentAppId}/mini_program/{miniProgramAppId}/tag', 'MiniProgramController@tag');
    Route::get('/v1/component/{componentAppId}/mini_program/{miniProgramAppId}/config/ext_json', 'MiniProgramController@getExtJson');
    Route::put('/v1/component/{componentAppId}/mini_program/{miniProgramAppId}/config/ext_json', 'MiniProgramController@putExtJson');
    /**
     * 模板管理
     */
    Route::get('/v1/component/{componentAppId}/draft', 'TemplateController@draft');
    Route::get('/v1/component/{componentAppId}/template', 'TemplateController@index');
    Route::delete('/v1/component/{componentAppId}/template/{templateId}', 'TemplateController@delete');
    Route::post('/v1/component/{componentAppId}/template', 'TemplateController@draftToTemplate');
    Route::post('/v1/component/{componentAppId}/template/sync', 'TemplateController@sync');
    Route::post('/v1/component/{componentAppId}/template/{templateId}/release', 'TemplateController@release');
    Route::get('/v1/component/{componentAppId}/template/{templateId}/statistical', 'TemplateController@statistical');
    Route::get('/v1/component/{componentAppId}/template/{templateId}/mini_program', 'TemplateController@miniProgramList');

    /*
     * 构建管理
     */
    Route::get('/v1/component/{componentAppId}/release/{releaseId}/audit', 'ReleaseController@auditList');
    Route::get('/v1/component/{componentAppId}/release_task', 'ReleaseController@index');
    Route::get('/v1/component/{componentAppId}/release_task/{releaseId}', 'ReleaseController@detail');
    Route::post('/v1/component/{componentAppId}/release_task/item/{releaseItemId}/retry', 'ReleaseController@retryItem');
    Route::get('/v1/component/{componentAppId}/release_task/{releaseId}/statistical', 'ReleaseController@statistical');
});

Route::group([
    'middleware' => 'force-json',
    'prefix' => 'api/v1',
    'namespace' => 'Api\V1',
], function () {
    /**
     *  工具类方法
     */
    Route::post('/tools/buildCodeCommitParams', 'ToolsController@buildCodeCommitParams');
});