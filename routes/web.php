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

Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

Route::prefix('')->middleware('demo')->group(function () {

    Route::middleware('limits')
        ->get('tasks/run/{token}', 'TaskExecutionController@execute')
        ->name('task.execute');
    Route::get('scheduler/run', 'ScheduleController@run');

    Route::prefix('/')->middleware('auth')->group(function () {
        Route::get('', 'AppController@index')->name('backend');
        Route::get('backups/{backup}/download', 'BackupController@download');
        Route::get('backups/{backup}/manifest', 'BackupController@getManifest');
        Route::middleware('limits')
            ->post('backups/{backup}/restore', 'BackupController@restore');
        Route::post('backups/{backup}/transfer', 'BackupController@transfer');
        Route::post('backups/import', 'BackupController@import');
        Route::post('backups/delete', 'BackupController@bulkDeleteAction');
        Route::post('backups/{backup}/delete', 'BackupController@delete');
        Route::get('tasks/{task}/overview', 'TaskController@overview');
        Route::get('tasks/{task}/logs', 'TaskController@getLogs');
        Route::put('tasks/{task}/token', 'TaskController@getNewToken');
        Route::post('tasks/delete', 'TaskController@bulkDelete');
        Route::get('databases/{database}/connect', 'DatabaseController@checkConnection');
        Route::post('databases/delete', 'DatabaseController@bulkDelete');
        Route::get('databases/list', 'DatabaseController@listDatabases');
        Route::post('destinations/delete', 'DestinationController@bulkDelete');
        Route::get('destinations/list', 'DestinationController@listDestinations');
        Route::post('users/delete', 'UserController@bulkDelete');
        Route::get('settings', 'SettingController@index');
        Route::post('settings', 'SettingController@update');
        Route::get('users/{user}/profile', 'UserController@profile');
        Route::get('activities', 'ActivityController@index');

        Route::apiResources([
            'backups' => 'BackupController',
            'tasks' => 'TaskController',
            'databases' => 'DatabaseController',
            'destinations' => 'DestinationController',
            'users' => 'UserController',
        ]);

        Route::get('update', 'UpgradeController@getUpgradePage')->name('upgrade');
        Route::post('update', 'UpgradeController@upgrade');
    });
});