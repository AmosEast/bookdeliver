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

    //用户管理模块
    Route::match(['post', 'get'], 'usersmanage/index', 'UsersManageController@index') ->name('usersmanage.index');
    Route::get('usersmanage/userroles/{roleId}', 'UsersManageController@userRoles') ->name('usersmanage.userroles');
    Route::get('usersmanage/removerole/{userId}/{roleId}', 'UsersManageController@removeRole') ->name('usersmanage.removerole');
    Route::post('usersmanage/giverole/{userId}', 'UsersManageController@giveRole') ->name('usersmanage.giverole');
    Route::get('usersmanage/addauserview', 'UsersManageController@addAUserView') ->name('usersmanage.addauserview');
    Route::post('usersmanage/addauser', 'UsersManageController@addAUser') ->name('usersmanage.addauser');
    Route::get('usersmanage/downloadexcelexample', 'UsersManageController@downloadExcelExample') ->name('usersmanage.downloadexcelexample');
    Route::get('usersmanage/addmanyusersview', 'UsersManageController@addManyUsersView') ->name('usersmanage.addmanyusersview');
    Route::post('usersmanage/confirmusersinfo', 'UsersManageController@confirmUsersInfo') ->name('usersmanage.confirmusersinfo');
    Route::get('usersmanage/saveusersfromsession', 'UsersManageController@saveUsersFromSession') ->name('usersmanage.saveusersfromsession');
    Route::get('usersmanage/resetpassword/{userId}', 'UsersManageController@resetPassword') ->name('usersmanage.resetpassword');
});

//教务管理模块
Route::group(['prefix' =>'educationmanage', 'namespace' =>'EducationManage'], function (){
    //学院管理模块
    Route::get('academiesmanage/index', 'AcademiesManageController@index') ->name('academiesmanage.index');
    Route::post('academiesmanage/addacademy', 'AcademiesManageController@addAcademy') ->name('academiesmanage.addacademy');
    Route::get('academiesmanage/editacademyview/{academyId}', 'AcademiesManageController@editAcademyView') ->name('academiesmanage.editacademyview');
    Route::post('academiesmanage/updateacademyinfo/{academyId}', 'AcademiesManageController@updateAcademyInfo') ->name('academiesmanage.updateacademyinfo');

    //专业管理模块
    Route::get('majorsmanage/index', 'MajorsManageController@index') ->name('majorsmanage.index');
    Route::post('majorsmanage/addmajor', 'MajorsManageController@addmajor') ->name('majorsmanage.addmajor');
    Route::get('majorsmanage/editmajorview/{majorId}', 'MajorsManageController@editMajorView') ->name('majorsmanage.editmajorview');
    Route::post('majorsmanage/updatemajorinfo/{majorId}', 'MajorsManageController@updateMajorInfo') ->name('majorsmanage.updatemajorinfo');

    //班级管理模块
    Route::get('classesmanage/index', 'ClassesManageController@index') ->name('classesmanage.index');
    Route::post('classesmanage/addclass', 'ClassesManageController@addclass') ->name('classesmanage.addclass');
    Route::get('classesmanage/editclassview/{classId}', 'ClassesManageController@editClassView') ->name('classesmanage.editclassview');
    Route::post('classesmanage/updateclassinfo/{classId}', 'ClassesManageController@updateClassInfo') ->name('classesmanage.updateclassinfo');
    Route::get('classesmanage/downloadClassExcel', 'ClassesManageController@downloadExcelExample') ->name('classesmanage.downloadexcelexample');
    Route::post('classesmanage/uploadclasses', 'ClassesManageController@uploadClasses') ->name('classesmanage.uploadclasses');
    Route::get('classesmanage/addClassesFromSession', 'ClassesManageController@addClassesFromSession') ->name('classesmanage.addclassesfromsession');

    //课程管理模块
    Route::get('coursesmanage/index', 'CoursesManageController@index') ->name('coursesmanage.index');
    Route::post('coursesmanage/addcourse', 'CoursesManageController@addCourse') ->name('coursesmanage.addcourse');
    Route::get('coursesmanage/getmajors/{courseId}', 'CoursesManageController@getMajors') ->name('coursesmanage.getmajors');
    Route::get('coursesmanage/editcourseview/{courseId}', 'coursesManageController@editCourseView') ->name('coursesmanage.editcourseview');
    Route::post('coursesmanage/updatecourse/{courseId}', 'coursesManageController@updateCourse') ->name('coursesmanage.updatecourse');

});

