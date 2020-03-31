<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'Dashboard', 'prefix' => 'project'], function () {
    Route::post('/push', 'AjaxController@pushFileProjectToServer')->name('project.api.push');
    Route::get('/detail/{key}', 'AjaxController@getDetail')->name('project.api.detail');
    Route::get('/list', 'AjaxController@getList')->name('project.api.list');
    Route::post('/delete', 'AjaxController@deleteProject')->name('project.api.delete');
    Route::get('/history/list', 'AjaxController@getListHistory')->name('project.api.history.list');

    Route::get('/server', 'AjaxController@connectServer')->name('project.api.server');
    Route::get('/server/list', 'AjaxController@getListServer')->name('project.api.server.list');
    Route::get('/server/logs', 'AjaxController@getLogsFile')->name('project.api.server.logs');
    Route::post('/server/command', 'AjaxController@command')->name('project.api.server.command');
    Route::post('/server/add-server', 'AjaxController@addNewServer')->name('project.api.server.add');
});
