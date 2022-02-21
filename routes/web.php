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

Auth::routes([
    'register' => false, // Registration Routes...
    'reset' => false, // Password Reset Routes...
    'verify' => false, // Email Verification Routes...
]);

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/saldo', 'SaldoAwalController@index')->name('saldo.index');
Route::get('/layanan/kesehatan', 'KesehatanController@index')->name('kesehatan.index');
Route::get('/layanan/kesehatan/lihat', 'KesehatanController@cari')->name('kesehatan.cari');

Route::get('/layanan/bor', 'BorController@index')->name('bor.index');
Route::get('/layanan/bor/lihat', 'BorController@cari')->name('bor.cari');

Route::get('/layanan/visit', 'VisitController@index')->name('visit.index');

Route::get('/layanan/sdm', 'SDMController@index')->name('sdm.index');
