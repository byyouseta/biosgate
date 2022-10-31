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

use App\Http\Controllers\SepController;

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

Route::get('/rsonline/pasienrajal', 'PasienOnlineController@rajal')->name('pasienonline.rajal');

Route::get('/rsonline/pasienbaru/add/{id}', 'PasienOnlineController@add')->name('pasienonline.add');
Route::get('/rsonline/pasienbaru/addrajal/{id}', 'PasienOnlineController@addrajal')->name('pasienonline.addrajal');
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
Route::get('/rsonline/pasienterlapor/lapterapi/{id}', 'PasienOnlineController@lapTerapi')->name('pasienonline.lapTerapi');
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
Route::post('/getKelurahan', 'PasienOnlineController@getKelurahan')->name('getKelurahan');
Route::post('/getSubinstalasi', 'KankerController@getSubinstalasi')->name('getSubinstalasi');

//ajax
Route::get('/geticd10', 'KankerController@geticd10')->name('geticd10');
Route::get('/testicd', 'KankerController@test')->name('kanker.test');

Route::get('/kanker/ranap', 'KankerController@pasien')->name('kanker.pasien');
Route::get('/kanker/rajal', 'KankerController@rajal')->name('kanker.rajal');
Route::get('/kanker/addranap/{id}', 'KankerController@addranap')->name('kanker.addranap');
Route::get('/kanker/addrajal/{id}', 'KankerController@addrajal')->name('kanker.addrajal');
Route::post('/kanker/addpasien', 'KankerController@store')->name('kanker.store');
Route::get('/kanker/{id}/edit', 'KankerController@edit')->name('kanker.edit');
Route::post('/kanker/{id}/update', 'KankerController@update')->name('kanker.update');
Route::get('/kanker/{id}/delete', 'KankerController@delete')->name('kanker.delete');
Route::get('/kanker/terlapor', 'KankerController@terlapor')->name('kanker.terlapor');
Route::get('/kanker/referensi', 'KankerController@referensi')->name('kanker.referensi');

//VEDIKA
Route::get('/vedika/rajal', 'VedikaController@rajal')->name('vedika.rajal');
Route::get('/vedika/rajal/{id}/billing', 'VedikaController@billingRajal')->name('vedika.billingRajal');
Route::get('/vedika/rajal/{id}/lab', 'VedikaController@labRajal')->name('vedika.labRajal');
Route::get('/vedika/rajal/{id}/radiologi', 'VedikaController@radioRajal')->name('vedika.radioRajal');
Route::get('/vedika/rajal/{id}/triase', 'VedikaController@triase')->name('vedika.triase');
Route::get('/vedika/rajal/{id}/ringkasanIgd', 'VedikaController@ringkasanIgd')->name('vedika.ringkasanIgd');
Route::get('/vedika/rajal/{id}/buktiPelayanan', 'VedikaController@buktiPelayanan')->name('vedika.buktiPelayanan');
Route::get('/vedika/rajal/{id}/berkas', 'VedikaController@berkas')->name('vedika.berkas');

Route::get('/vedika/ranap', 'VedikaController@ranap')->name('vedika.ranap');
Route::get('/vedika/ranap/{id}/billing', 'VedikaController@billingRanap')->name('vedika.billingRanap');
Route::get('/vedika/ranap/{id}/lab', 'VedikaController@labRanap')->name('vedika.labRanap');
Route::get('/vedika/ranap/{id}/radiologi', 'VedikaController@radioRanap')->name('vedika.radioRanap');
Route::get('/vedika/ranap/{id}/berkas', 'VedikaController@berkasRanap')->name('vedika.berkasRanap');

Route::post('/vedika/berkas/store', 'VedikaController@berkasUpload')->name('vedika.berkasUpload');
// Route::post('/vedika/berkas/store', 'VedikaController@berkasStore')->name('vedika.berkasStore');
Route::get('/vedika/berkas/{id}/delete', 'VedikaController@berkasDelete')->name('vedika.berkasDelete');
Route::get('/vedika/berkas/{id}/view', 'VedikaController@berkasShow')->name('vedika.berkasShow');

Route::get('/sep', 'SepController@index')->name('sep.index');

Route::get('/satusehat', 'SatuSehatController@summary')->name('satuSehat.summary');
Route::get('/satusehat/bundle', 'SatuSehatController@bundleData')->name('satuSehat.bundleData');
Route::get('/satusehat/encounter', 'SatuSehatController@sendEncounter')->name('satuSehat.encounter');
Route::get('/satusehat/composition', 'SatuSehatController@sendComposition')->name('satuSehat.composition');
Route::get('/satusehat/medication', 'SatuSehatController@sendMedication')->name('satuSehat.medication');
Route::get('/satusehat/lab', 'SatuSehatController@sendLab')->name('satuSehat.lab');

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
Route::post('/master/dummygeo/kelurahan', 'DummyController@kelurahan')->name('dummy.kelurahan');

Route::get('/master/dummy/instalasi', 'DummyController@instalasi')->name('dummy.instalasi');
Route::get('/master/dummy/subinstalasi', 'DummyController@subinstalasi')->name('dummy.subinstalasi');


Route::get('/master/vedika', 'VedikaController@index')->name('vedika.index');
Route::post('/master/vedika/store', 'VedikaController@store')->name('vedika.store');
Route::get('/master/vedika/edit/{id}', 'VedikaController@edit')->name('vedika.edit');
Route::post('/master/vedika/update/{id}', 'VedikaController@update')->name('vedika.update');
Route::get('/master/vedika/delete/{id}', 'VedikaController@delete')->name('vedika.delete');

Route::get('/permission', 'PermissionController@index')->name('permission.index');
Route::post('/permission/store', 'PermissionController@store')->name('permission.store');
Route::get('/permission/edit/{id}', 'PermissionController@edit')->name('permission.edit');
Route::post('/permission/update/{id}', 'PermissionController@update')->name('permission.update');
Route::get('/permission/delete/{id}', 'PermissionController@delete')->name('permission.delete');

Route::resource('roles', 'RoleController');

Route::get('/profil', 'UserController@profile')->name('user.profile');
Route::post('/profil/update', 'UserController@profileupdate')->name('user.profileupdate');
Route::post('/profil/password', 'UserController@password')->name('user.password');
