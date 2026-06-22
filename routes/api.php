<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/pesan/webhook', 'RedirectController@handle')->name('wa.handle');
Route::middleware('verify.payroll')->post('/send-otp', 'RedirectController@sendOtp')->name('wa.kirimOtp');
Route::middleware('verify.payroll')->post('/send-message', 'RedirectController@sendPesan')->name('wa.kirimPesan');

Route::middleware(['api.token', 'throttle:60,1'])->prefix('bridging')
    ->group(function () {
        Route::get('/doctor-schedules', 'Api\DoctorScheduleController@index')->name('api.doctor-schedules');
        Route::get('/doctors', 'Api\DoctorController@index')->name('api.doctors');
        Route::get('/doctors/{kd_dokter}', 'Api\DoctorController@show')->name('api.doctors.show');
        Route::get('/doctors/{kd_dokter}/schedules', 'Api\DoctorController@schedules')->name('api.doctors.schedules');
        Route::get('/polyclinics', 'Api\PolyclinicController@index')->name('api.polyclinics');
        Route::get('/polyclinics/{kd_poli}/doctors', 'Api\PolyclinicController@doctors')->name('api.polyclinics.doctors');
        Route::get('/polyclinics/{kd_poli}/schedules', 'Api\PolyclinicController@schedules')->name('api.polyclinics.schedules');
    });
