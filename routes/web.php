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

//系统管理模块
Route::group(['prefix' =>'systemmanage', 'namespace' =>'SystemManage'], function (){
    //角色管理模块
    Route::get('rolesmanage/index', 'RolesManageController@index') ->name('rolesmanage.index');
    Route::post('rolesmanage/addrole', 'RolesManageController@addRole') ->name('rolesmanage.addrole');
    Route::get('rolesmanage/disablerole/{id}', 'RolesManageController@disableRole') ->name('rolesmanage.disablerole');
    Route::get('rolesmanage/startrole/{id}', 'RolesManageController@startRole') ->name('rolesmanage.startrole');
    Route::get('rolesmanage/rolepermissions/{id}', 'RolesManageController@rolePermissions') ->name('rolesmanage.rolepermissions');
    Route::get('rolesmanage/removepermission/{roleId}/{permissionId}', 'RolesManageController@removePermission') ->name('rolesmanage.removepermission');
    Route::post('rolesmanage/givePermission/{roleId}', 'RolesManageController@givePermission') ->name('rolesmanage.givepermission');

    //权限管理模块
    Route::get('permissionsmanage/index', 'PermissionsManageController@index') ->name('permissionsmanage.index');
    Route::post('permissionsmanage/addpermission', 'PermissionsManageController@addPermission') ->name('permissionsmanage.addpermission');
    Route::get('permissionsmanage/editpermissionview/{permissionId}', 'PermissionsManageController@editPermissionView') ->name('permissionsmanage.editpermissionview');
    Route::post('permissionsmanage/updatepermissioninfo/{permissionId}', 'PermissionsManageController@updatePermissionInfo') ->name('permissionsmanage.updatepermissioninfo');
});

