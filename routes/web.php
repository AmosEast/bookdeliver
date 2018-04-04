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

Route::get('/', 'Auth\LoginController@showLoginForm') ->name('login');
Route::get('/practice/index', "PracticeController@index");

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

//Route::get('rolesmanage/index', 'SystemManage/RolesManageController@index') ->name('rolesmanage.index');

//Route::prefix('/systemmanage') ->group(function (){
//    Route::get('rolesmanage/index', 'RolesManageController@index') ->name('rolesmanage.index');
//});
Route::group(['prefix' =>'systemmanage', 'namespace' =>'SystemManage'], function (){
    Route::get('rolesmanage/index', 'RolesManageController@index') ->name('rolesmanage.index');
});

