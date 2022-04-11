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

//BIOS V.2
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/saldo', 'SaldoAwalController@index')->name('saldo.index');
Route::get('/saldo/lihat', 'SaldoAwalController@set')->name('saldo.set');
Route::get('/saldo/client', 'ClientController@saldoawal')->name('client.saldoawal');
Route::get('/layanan/kesehatan/client', 'ClientController@kesehatan')->name('client.kesehatan');

Route::get('/penerimaan', 'PemasukanController@index')->name('pemasukan.index');
Route::post('/penerimaan/store', 'PemasukanController@store')->name('pemasukan.store');
Route::get('/penerimaan/edit/{id}', 'PemasukanController@edit')->name('pemasukan.edit');
Route::post('/penerimaan/update/{id}', 'PemasukanController@update')->name('pemasukan.update');
Route::get('/penerimaan/delete/{id}', 'PemasukanController@delete')->name('pemasukan.delete');
Route::get('/penerimaan/lihat', 'PemasukanController@cari')->name('pemasukan.cari');
Route::get('/penerimaan/client', 'PemasukanController@client')->name('pemasukan.client');

Route::get('/pengeluaran', 'PengeluaranController@index')->name('pengeluaran.index');
Route::post('/pengeluaran/store', 'PengeluaranController@store')->name('pengeluaran.store');
Route::get('/pengeluaran/edit/{id}', 'PengeluaranController@edit')->name('pengeluaran.edit');
Route::post('/pengeluaran/update/{id}', 'PengeluaranController@update')->name('pengeluaran.update');
Route::get('/pengeluaran/delete/{id}', 'PengeluaranController@delete')->name('pengeluaran.delete');
Route::get('/pengeluaran/lihat', 'PengeluaranController@cari')->name('pengeluaran.cari');
Route::get('/pengeluaran/client', 'PengeluaranController@client')->name('pengeluaran.client');

Route::get('/saldokeuangan', 'SaldoKeuanganController@index')->name('saldokeuangan.index');
Route::post('/saldokeuangan/store', 'SaldoKeuanganController@store')->name('saldokeuangan.store');
Route::get('/saldokeuangan/edit/{id}', 'SaldoKeuanganController@edit')->name('saldokeuangan.edit');
Route::post('/saldokeuangan/update/{id}', 'SaldoKeuanganController@update')->name('saldokeuangan.update');
Route::get('/saldokeuangan/delete/{id}', 'SaldoKeuanganController@delete')->name('saldokeuangan.delete');
Route::get('/saldokeuangan/lihat', 'SaldoKeuanganController@cari')->name('saldokeuangan.cari');
Route::get('/saldokeuangan/client', 'SaldoKeuanganController@client')->name('saldokeuangan.client');


Route::get('/pengeluaran', 'PengeluaranController@index')->name('pengeluaran.index');

Route::get('/layanan/kesehatan', 'KesehatanController@index')->name('kesehatan.index');
Route::get('/layanan/kesehatan/lihat', 'KesehatanController@cari')->name('kesehatan.cari');

Route::get('/layanan/bor', 'BorController@index')->name('bor.index');
Route::get('/layanan/bor/lihat', 'BorController@cari')->name('bor.cari');

Route::get('/layanan/visit', 'VisitController@index')->name('visit.index');
Route::get('/layanan/visit/lihat', 'VisitController@cari')->name('visit.cari');

Route::get('/layanan/sdm', 'SDMController@index')->name('sdm.index');

//RSonline
Route::get('/rsonline/geografi', 'RsClientController@geografi')->name('rsclient.geografi');
Route::get('/rsonline/vaksin', 'RsClientController@vaksin')->name('rsclient.vaksin');
Route::get('/rsonline/statuspasien', 'RsClientController@status')->name('rsclient.status');

Route::get('/rsonline/pasienbaru', 'PasienOnlineController@index')->name('pasienonline.index');
Route::get('/rsonline/pasienbaru/add/{id}', 'PasienOnlineController@add')->name('pasienonline.add');
Route::post('/rsonline/pasienbaru/store', 'PasienOnlineController@store')->name('pasienonline.store');

Route::get('/rsonline/pasienterlapor', 'PasienOnlineController@terlapor')->name('pasienonline.terlapor');
Route::get('/rsonline/pasienterlapor/editlap/{id}', 'PasienOnlineController@editlap')->name('pasienonline.editlap');
Route::post('/rsonline/pasienterlapor/updatelap/{id}', 'PasienOnlineController@updatelap')->name('pasienonline.updatelap');
Route::get('/rsonline/pasienterlapor/laptambahan/{id}', 'PasienOnlineController@lapTambahan')->name('pasienonline.lapTambahan');
//Laporan Komorbid
Route::post('/rsonline/pasienterlapor/komorbid/{id}', 'PasienOnlineController@komorbidupdate')->name('pasienonline.komorbidupdate');
Route::get('/rsonline/pasienterlapor/editkomorbid/{id}', 'PasienOnlineController@komorbidedit')->name('pasienonline.komorbidedit');
Route::post('/rsonline/pasienterlapor/patchkomorbid/{id}', 'PasienOnlineController@komorbidpatch')->name('pasienonline.komorbidpatch');
//Laporan Terapi
Route::post('/rsonline/pasienterlapor/terapi/{id}', 'PasienOnlineController@terapiupdate')->name('pasienonline.terapiupdate');
Route::get('/rsonline/pasienterlapor/editterapi/{id}', 'PasienOnlineController@terapiedit')->name('pasienonline.terapiedit');
Route::post('/rsonline/pasienterlapor/patchterapi/{id}', 'PasienOnlineController@terapipatch')->name('pasienonline.terapipatch');

//Laporan Vaksinasi
Route::post('/rsonline/pasienterlapor/vaksinasi/{id}', 'PasienOnlineController@vaksinasiupdate')->name('pasienonline.vaksinasiupdate');
Route::get('/rsonline/pasienterlapor/editvaksinasi/{id}', 'PasienOnlineController@vaksinedit')->name('pasienonline.vaksinedit');
Route::post('/rsonline/pasienterlapor/patchvaksin/{id}', 'PasienOnlineController@vaksinpatch')->name('pasienonline.vaksinpatch');

Route::post('/rsonline/pasienterlapor/lab/{id}', 'PasienOnlineController@labupdate')->name('pasienonline.labupdate');
Route::post('/rsonline/pasienterlapor/diagnosa/{id}', 'PasienOnlineController@diagnosaupdate')->name('pasienonline.diagnosaupdate');

//Laporan Pulang
Route::get('/rsonline/pasienpulang', 'PasienOnlineController@keluar')->name('pasienonline.keluar');
Route::get('/rsonline/pasienterlapor/pulang/{id}', 'PasienOnlineController@pulang')->name('pasienonline.pulang');
Route::post('/rsonline/pasienterlapor/pulang/{id}', 'PasienOnlineController@pulangupdate')->name('pasienonline.pulangupdate');

//axios
Route::post('/getKabKota', 'PasienOnlineController@getKabKota')->name('getKabKota');
Route::post('/getKecamatan', 'PasienOnlineController@getKecamatan')->name('getKecamatan');

Route::get('/setting', 'SettingController@index')->name('setting.index');
Route::post('/setting/store', 'SettingController@store')->name('setting.store');
Route::get('/setting/edit/{id}', 'SettingController@edit')->name('setting.edit');
Route::post('/setting/update/{id}', 'SettingController@update')->name('setting.update');
Route::get('/setting/delete/{id}', 'SettingController@delete')->name('setting.delete');

Route::get('/schedule', 'ScheduleController@index')->name('schedule.index');
Route::post('/schedule/store', 'ScheduleController@store')->name('schedule.store');
Route::get('/schedule/delete/{id}', 'ScheduleController@delete')->name('schedule.delete');

Route::get('/master/user', 'UserController@index')->name('user.index');
Route::post('/master/user/store', 'UserController@store')->name('user.store');
Route::get('/master/user/edit/{id}', 'UserController@edit')->name('user.edit');
Route::post('/master/user/update/{id}', 'UserController@update')->name('user.update');
Route::get('/master/user/delete/{id}', 'UserController@delete')->name('user.delete');

Route::get('/master/bank', 'BankController@index')->name('bank.index');
Route::post('/master/bank/store', 'BankController@store')->name('bank.store');
Route::get('/master/bank/edit/{id}', 'BankController@edit')->name('bank.edit');
Route::post('/master/bank/update/{id}', 'BankController@update')->name('bank.update');
Route::get('/master/bank/delete/{id}', 'BankController@delete')->name('bank.delete');

Route::get('/master/dummygeo', 'DummyController@index')->name('dummy.index');
Route::get('/master/dummygeo/provinsi', 'DummyController@provinsi')->name('dummy.provinsi');
Route::get('/master/dummygeo/kabkota', 'DummyController@kabkota')->name('dummy.kabkota');
Route::post('/master/dummygeo/kecamatan', 'DummyController@kecamatan')->name('dummy.kecamatan');


Route::get('/permission', 'PermissionController@index')->name('permission.index');
Route::post('/permission/store', 'PermissionController@store')->name('permission.store');
Route::get('/permission/edit/{id}', 'PermissionController@edit')->name('permission.edit');
Route::post('/permission/update/{id}', 'PermissionController@update')->name('permission.update');
Route::get('/permission/delete/{id}', 'PermissionController@delete')->name('permission.delete');

Route::resource('roles', 'RoleController');

Route::get('/profil', 'UserController@profile')->name('user.profile');
Route::post('/profil/update', 'UserController@profileupdate')->name('user.profileupdate');
Route::post('/profil/password', 'UserController@password')->name('user.password');
