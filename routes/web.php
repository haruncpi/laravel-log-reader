<?php
Route::group(['namespace' => 'Haruncpi\LaravelLogReader\Controllers', 'middleware' => ['web','auth']], function () {

    Route::get(config('laravel-log-reader.view_route_path'), 'LogReaderController@getIndex');
    Route::post(config('laravel-log-reader.view_route_path'), 'LogReaderController@postDelete');
    Route::get(config('laravel-log-reader.api_route_path'), 'LogReaderController@getLogs');

});
