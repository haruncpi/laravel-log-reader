<?php

Route::view(config('laravel-log-reader.view_route_path'), 'LaravelLogReader::index');
Route::post(config('laravel-log-reader.view_route_path'), '\Haruncpi\LaravelLogReader\Controllers\LogReaderController@postDelete');
Route::get(config('laravel-log-reader.api_route_path'),'\Haruncpi\LaravelLogReader\Controllers\LogReaderController@getLogs');