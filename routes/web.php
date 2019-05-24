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

use Illuminate\Support\Facades\Route;

Route::pattern('validateFilename', '[A-Za-z0-9_]+\.txt$');
Route::get('/{validateFilename}', 'ComponentController@hostValidate');

Route::group([
    'middleware' => ['force-json', 'component-injection']
], function(){
    Route::any('/component/{componentAppId}/mini_program/{miniProgramAppId}/serve', 'MiniProgramController@serve')->name('componentMiniProgramServe');
    Route::post('/component/{componentAppId}/serve', 'ComponentController@serve')->name('componentServe');

});

Route::get('/debug', 'ComponentController@debug');
