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
    // return view('home');
    return redirect('/home');
});

Auth::routes([
    'register' => false, // Registration Routes...
    'reset' => false, // Password Reset Routes...
    'verify' => false, // Email Verification Routes...
]);

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/saldo', 'SaldoAwalController@index')->name('saldo.index');
Route::get('/saldo/lihat', 'SaldoAwalController@set')->name('saldo.set');
Route::get('/saldo/client', 'ClientController@saldoawal')->name('client.saldoawal');
Route::get('/layanan/kesehatan/client', 'ClientController@kesehatan')->name('client.kesehatan');


Route::get('/layanan/kesehatan', 'KesehatanController@index')->name('kesehatan.index');
Route::get('/layanan/kesehatan/lihat', 'KesehatanController@cari')->name('kesehatan.cari');

Route::get('/layanan/bor', 'BorController@index')->name('bor.index');
Route::get('/layanan/bor/lihat', 'BorController@cari')->name('bor.cari');

Route::get('/layanan/visit', 'VisitController@index')->name('visit.index');
Route::get('/layanan/visit/lihat', 'VisitController@cari')->name('visit.cari');

Route::get('/layanan/sdm', 'SDMController@index')->name('sdm.index');

Route::get('/setting', 'SettingController@index')->name('setting.index');
Route::post('/setting/store', 'SettingController@store')->name('setting.store');
Route::post('/setting/update', 'SettingController@update')->name('setting.update');
Route::get('/setting/delete/{id}', 'SettingController@delete')->name('setting.delete');

Route::get('/schedule', 'ScheduleController@index')->name('schedule.index');
Route::post('/schedule/store', 'ScheduleController@store')->name('schedule.store');
Route::get('/schedule/delete/{id}', 'ScheduleController@delete')->name('schedule.delete');

Route::get('/master/user', 'UserController@index')->name('user.index');
Route::post('/master/user/store', 'UserController@store')->name('user.store');
Route::get('/master/user/edit/{id}', 'UserController@edit')->name('user.edit');
Route::post('/master/user/update/{id}', 'UserController@update')->name('user.update');
Route::get('/master/user/delete/{id}', 'UserController@delete')->name('user.delete');

Route::get('/permission', 'PermissionController@index')->name('permission.index');
Route::post('/permission/store', 'PermissionController@store')->name('permission.store');
Route::get('/permission/edit/{id}', 'PermissionController@edit')->name('permission.edit');
Route::post('/permission/update/{id}', 'PermissionController@update')->name('permission.update');
Route::get('/permission/delete/{id}', 'PermissionController@delete')->name('permission.delete');

Route::resource('roles', 'RoleController');


Route::get('/profil', 'UserController@profile')->name('user.profile');
Route::post('/profil/update', 'UserController@profileupdate')->name('user.profileupdate');
Route::post('/profil/password', 'UserController@password')->name('user.password');
