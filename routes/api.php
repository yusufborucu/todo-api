<?php

Route::post('todo', 'TodoController@store');
Route::get('todo', 'TodoController@index');
Route::get('todo/{id}', 'TodoController@show');
Route::put('todo', 'TodoController@update');
Route::delete('todo/{id}', 'TodoController@destroy');
Route::get('category', 'TodoController@category');