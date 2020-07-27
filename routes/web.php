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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['prefix' => '/crud', 'middleware' => 'auth'], function () {
    $cruds = [
        '/users' => 'UserController',
        '/departments' => 'DepartmentController',
        '/roles' => 'RoleController',
        '/permissions' => 'PermissionController',
    ];
    collect($cruds)->each(function ($controller, $prefix) {
        Route::group(['prefix' => $prefix], function () use ($controller) {
            Route::get('/', $controller . '@index');
            Route::post('/', $controller . '@create');
            Route::put('/', $controller . '@update');
            Route::delete('/{id}', $controller . '@destroy')->where('id', '[0-9]+');
            Route::get('/options', $controller . '@options');
        });
    });
});
