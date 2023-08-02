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

Route::middleware('api')->group(function() {
    Route::post('task/create', ['as' => 'task.create','uses' => 'TaskController@create']);

    Route::get('task/delete/{id}', ['as' => 'task.delete','uses' => 'TaskController@delete']);

    Route::get('task/complete/{id}', ['as' => 'task.complete','uses' => 'TaskController@complete']);

    Route::get('task/list', ['as' => 'task.list','uses' => 'TaskController@list']);

    Route::post('task/search', ['as' => 'task.search','uses' => 'TaskController@search']);

    Route::get('task/deleteSoftDeletes', ['as' => 'task.deleteAll','uses' => 'TaskController@deleteAll']);// cron job scheduler task
});
