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

use Illuminate\Support\Facades\File;

Route::get('/', function () {
    // return view('home');
    return redirect('/login');
});

// Survei
Route::get('/survei', 'SurveiController@index')->name('survei');
Route::get('/survei/pengaduan', 'SurveiController@pengaduan')->name('survei.pengaduan');
Route::post('/survei/pengaduan/store', 'SurveiController@store')->name('survei.store');
Route::get('/survei/{id}/tiket', 'SurveiController@showTicket')->name('survei.showcTiket');
Route::get('/survei/pengaduan/periksa', 'SurveiController@periksa')->name('survei.periksa');
Route::post('/survei/pengaduan/periksa', 'SurveiController@periksaTiket')->name('survei.periksaTiket');
Route::get('/survei/kepuasan', 'SurveiController@kepuasan')->name('survei.kepuasan');
Route::post('/survei/kepuasan/store', 'SurveiController@simpan')->name('survei.simpan');

//viewerRadiologi
Route::get('/viewer/{id}', 'RedirectController@viewerRadiologi')->name('viewerRadiologi');

Auth::routes([
    'register' => false, // Registration Routes...
    'reset' => false, // Password Reset Routes...
    'verify' => false, // Email Verification Routes...
]);
// Survei data
Route::get('/survei/datapengaduan', 'DataSurveiController@pengaduan')->name('datasurvei.pengaduan');
Route::get('/survei/datapengaduan/exportExcel', 'DataSurveiController@exportPengaduan')->name('datasurvei.pengaduan_export');
Route::get('/survei/datapengaduan/{id}/delete', 'DataSurveiController@deletePengaduan')->name('datasurvei.deletePengaduan');
Route::get('/survei/datapengaduan/{id}/detail', 'DataSurveiController@detailPengaduan')->name('datasurvei.detailPengaduan');
Route::post('/survei/datapengaduan/{id}/status', 'DataSurveiController@statusPengaduan')->name('datasurvei.statusPengaduan');
Route::get('/survei/datakepuasan', 'DataSurveiController@kepuasan')->name('datasurvei.kepuasan');
Route::get('/survei/datakepuasan/{id}/exportExcel', 'DataSurveiController@exportKepuasan')->name('datasurvei.kepuasan_export');
Route::get('/survei/datakepuasan/{id}/detail', 'DataSurveiController@detailKepuasan')->name('datasurvei.detailKepuasan');

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
Route::post('/penerimaan/import', 'PemasukanController@import')->name('pemasukan.import');
Route::get('/penerimaan/template', 'PemasukanController@template')->name('pemasukan.template');
// Route::get('/penerimaan/chart', 'PemasukanController@chart')->name('pemasukan.chart');

Route::get('/pengeluaran', 'PengeluaranController@index')->name('pengeluaran.index');
Route::post('/pengeluaran/store', 'PengeluaranController@store')->name('pengeluaran.store');
Route::get('/pengeluaran/edit/{id}', 'PengeluaranController@edit')->name('pengeluaran.edit');
Route::post('/pengeluaran/update/{id}', 'PengeluaranController@update')->name('pengeluaran.update');
Route::get('/pengeluaran/delete/{id}', 'PengeluaranController@delete')->name('pengeluaran.delete');
Route::get('/pengeluaran/lihat', 'PengeluaranController@cari')->name('pengeluaran.cari');
Route::get('/pengeluaran/client', 'PengeluaranController@client')->name('pengeluaran.client');
Route::post('/pengeluaran/import', 'PengeluaranController@import')->name('pengeluaran.import');

Route::get('/saldo/operasional', 'SaldoKeuanganController@index')->name('saldooperasional.index');
Route::post('/saldo/operasional/store', 'SaldoKeuanganController@store')->name('saldooperasional.store');
Route::get('/saldo/operasional/{id}/edit', 'SaldoKeuanganController@edit')->name('saldooperasional.edit');
Route::post('/saldo/operasional/{id}/update', 'SaldoKeuanganController@update')->name('saldooperasional.update');
Route::get('/saldo/operasional/{id}/delete', 'SaldoKeuanganController@delete')->name('saldooperasional.delete');
Route::get('/saldo/operasional/lihat', 'SaldoKeuanganController@cari')->name('saldokeuangan.cari');
Route::get('/saldokeuangan/client', 'SaldoKeuanganController@client')->name('saldokeuangan.client');

Route::get('/saldo/pengelolaankas', 'SaldoKeuanganController@indexPengelolaan')->name('saldopengelolaan.indexPengelolaan');
Route::post('/saldo/pengelolaankas/store', 'SaldoKeuanganController@storePengelolaan')->name('saldopengelolaan.storePengelolaan');
Route::get('/saldo/pengelolaankas/{id}/edit', 'SaldoKeuanganController@editPengelolaan')->name('saldopengelolaan.editPengelolaan');
Route::post('/saldo/pengelolaankas/{id}/update', 'SaldoKeuanganController@updatePengelolaan')->name('saldopengelolaan.updatePengelolaan');
Route::get('/saldo/pengelolaankas/{id}/delete', 'SaldoKeuanganController@deletePengelolaan')->name('saldopengelolaan.deletePengelolaan');
Route::get('/saldo/pengelolaankas/lihat', 'SaldoKeuanganController@cariPengelolaan')->name('saldopengelolaan.cariPengelolaan');

Route::get('/saldo/kelolaan', 'SaldoKeuanganController@indexKelolaan')->name('saldokelolaan.indexKelolaan');
Route::post('/saldo/kelolaan/store', 'SaldoKeuanganController@storeKelolaan')->name('saldokelolaan.storeKelolaan');
Route::get('/saldo/kelolaan/{id}/edit', 'SaldoKeuanganController@editKelolaan')->name('saldokelolaan.editKelolaan');
Route::post('/saldo/kelolaan/{id}/update', 'SaldoKeuanganController@updateKelolaan')->name('saldokelolaan.updateKelolaan');
Route::get('/saldo/kelolaan/{id}/delete', 'SaldoKeuanganController@deleteKelolaan')->name('saldokelolaan.deleteKelolaan');
Route::get('/saldo/kelolaan/lihat', 'SaldoKeuanganController@cariKelolaan')->name('saldokelolaan.cariKelolaan');

Route::get('/saldo/laporan', 'PemasukanController@chart')->name('saldo.chart');

Route::get('/layanan/kesehatan', 'KesehatanController@index')->name('kesehatan.index');
Route::get('/layanan/kesehatan/lihat', 'KesehatanController@cari')->name('kesehatan.cari');

Route::get('/layanan/bor', 'BorController@index')->name('bor.index');
Route::get('/layanan/bor/lihat', 'BorController@cari')->name('bor.cari');
Route::get('/layanan/bor/client', 'ClientController@statistik')->name('client.statistik');

Route::get('/layanan/visit', 'VisitController@index')->name('visit.index');
Route::get('/layanan/visit/lihat', 'VisitController@cari')->name('visit.cari');

Route::get('/layanan/sdm', 'SDMController@index')->name('sdm.index');
Route::get('/layanan/sdm/client', 'ClientController@sdm')->name('client.sdm');

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

//RSOnline antrian
Route::get('/rsonline/antrian', 'RsoAntrianController@index')->name('antrian.index');
Route::get('/rsonline/antrian/clientadd', 'RsoAntrianController@clientAdd')->name('antrian.clientAdd');


//axios
Route::post('/getKabKota', 'PasienOnlineController@getKabKota')->name('getKabKota');
Route::post('/getKecamatan', 'PasienOnlineController@getKecamatan')->name('getKecamatan');
Route::post('/getKelurahan', 'PasienOnlineController@getKelurahan')->name('getKelurahan');
Route::post('/getSubinstalasi', 'KankerController@getSubinstalasi')->name('getSubinstalasi');

//ajax
Route::get('/geticd10', 'KankerController@geticd10')->name('geticd10');
Route::get('/testicd', 'KankerController@test')->name('kanker.test');

//Kanker
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

//Berkas RM
Route::get('/berkasrm/rajal', 'BerkasRmController@rajal')->name('berkasrm.rajal');
Route::get('/berkasrm/ranap', 'BerkasRmController@ranap')->name('berkasrm.ranap');
Route::get('/berkasrm/berkas/{id}/kewajiban', 'BerkasRmController@kewajiban')->name('berkasrm.berkaskewajiban');
Route::post('/berkasrm/hakkewajiban/store', 'BerkasRmController@hakkewajibanStore')->name('berkasrm.hakkewajibanStore');
Route::post('/berkasrm/hakkewajiban/edit', 'BerkasRmController@hakkewajibanEdit')->name('berkasrm.hakkewajibanEdit');
Route::get('/berkasrm/hakkewajiban/{id}/delete', 'BerkasRmController@delete')->name('berkasrm.hakKewajibanDelete');
Route::get('/berkasrm/hakkewajiban/{id}/print', 'BerkasRmController@hakKewajibanPdf')->name('berkasrm.hakKewajibanPdf');

//MCU
Route::get('/berkasrm/{id}/penilaianralan', 'PenilaianMcuController@index')->name('berkasrm.penilaianralan');
Route::get('/berkasrm/{id}/soapie', 'PenilaianMcuController@soapie')->name('berkasrm.soapie');
Route::post('/berkasrm/soapie', 'PenilaianMcuController@soapieStore')->name('berkasrm.soapieStore');
Route::get('/berkasrm/soapie/{id}/edit', 'PenilaianMcuController@soapieEdit')->name('berkasrm.soapieEdit');
Route::get('/berkasrm/soapie/{id}/delete', 'PenilaianMcuController@soapieDelete')->name('berkasrm.soapieDelete');
Route::post('/berkasrm/soapie/update', 'PenilaianMcuController@soapieUpdate')->name('berkasrm.soapieUpdate');
Route::post('/berkasrm/penilaianralan', 'PenilaianMcuController@store')->name('berkasrm.penilaianralanStore');
Route::post('/berkasrm/penilaianralan/update', 'PenilaianMcuController@update')->name('berkasrm.penilaianralanUpdate');

Route::get('/berkasrm/berkas/{id}/generalconsent', 'BerkasRmController@generalConsent')->name('berkasrm.generalConsent');
Route::post('/berkasrm/generalconsent/store', 'BerkasRmController@generalStore')->name('berkasrm.generalStore');
Route::post('/berkasrm/generalconsent/edit', 'BerkasRmController@generalEdit')->name('berkasrm.generalEdit');
Route::get('/berkasrm/generalconsent/{id}/delete', 'BerkasRmController@generalDelete')->name('berkasrm.generalDelete');
Route::get('/berkasrm/generalconsent/{id}/print', 'BerkasRmController@generalPdf')->name('berkasrm.generalPdf');
//VEDIKA
Route::get('/vedika/rajal', 'VedikaController@rajal')->name('vedika.rajal');
Route::get('/vedika/rajal/{id}/detail', 'VedikaController@detailRajal')->name('vedika.detailRajal');
Route::get('/vedika/rajal/{id}/detailpdf', 'VedikaController@detailRajalPdf')->name('vedika.detailRajalPdf');
Route::get('/vedika/rajal/{id}/downloadpdf', 'VedikaController@downloadRajalPdf')->name('vedika.downloadRajalPdf');
Route::get('/vedika/rajal/{id}/cronispdf', 'VedikaController@cronisRajalPdf')->name('vedika.cronisRajalPdf');
// Route::get('/vedika/rajal/{id}/billing', 'VedikaController@billingRajal')->name('vedika.billingRajal');
// Route::get('/vedika/rajal/{id}/lab', 'VedikaController@labRajal')->name('vedika.labRajal');
// Route::get('/vedika/rajal/{id}/radiologi', 'VedikaController@radioRajal')->name('vedika.radioRajal');
// Route::get('/vedika/rajal/{id}/obat', 'VedikaController@obatRajal')->name('vedika.obatRajal');
// Route::get('/vedika/rajal/{id}/triase', 'VedikaController@triase')->name('vedika.triase');
// Route::get('/vedika/rajal/{id}/ringkasanIgd', 'VedikaController@ringkasanIgd')->name('vedika.ringkasanIgd');
// Route::get('/vedika/rajal/{id}/buktiPelayanan', 'VedikaController@buktiPelayanan')->name('vedika.buktiPelayanan');
Route::get('/vedika/rajal/{id}/berkas', 'VedikaController@berkas')->name('vedika.berkas');

Route::get('/vedika/rajal/{id}/sepmanual', 'VedikaController@sepManual')->name('vedika.sepManual');
Route::get('/vedika/rajal/{id}/hapusSep', 'VedikaController@hapusSepManual')->name('vedika.hapusSepManual');
Route::post('/vedika/rajal/simpansep', 'VedikaController@simpanSep')->name('vedika.simpanSep');

Route::get('/vedika/ranap', 'VedikaController@ranap')->name('vedika.ranap');
Route::get('/vedika/ranap/{id}/detail', 'VedikaController@detailRanap')->name('vedika.detailRanap');
Route::get('/vedika/ranap/{id}/detailpdf', 'VedikaController@detailRanapPdf')->name('vedika.detailRanapPdf');
Route::get('/vedika/ranap/{id}/downloadpdf', 'VedikaController@downloadRanapPdf')->name('vedika.downloadRanapPdf');
Route::get('/vedika/ranap/{id}/viewgabungpdf', 'VedikaController@viewGabungPdf')->name('vedika.viewGabungPdf');
Route::get('/vedika/ranap/{id}/deletepdf', 'VedikaController@deletePdf')->name('vedika.deletePdf');
Route::get('/vedika/ranap/{id}/billing', 'VedikaController@billingRanap')->name('vedika.billingRanap');
Route::get('/vedika/ranap/{id}/lab', 'VedikaController@labRanap')->name('vedika.labRanap');
Route::get('/vedika/ranap/{id}/radiologi', 'VedikaController@radioRanap')->name('vedika.radioRanap');
Route::get('/vedika/ranap/{id}/obat', 'VedikaController@obatRanap')->name('vedika.obatRanap');
Route::get('/vedika/ranap/{id}/berkas', 'VedikaController@berkasRanap')->name('vedika.berkasRanap');

Route::get('/vedika/{id}/tambahradiologi', 'VedikaController@tambahRadiologi')->name('vedika.tambahRadiologi');
Route::get('/vedika/{id}/deleteradiologi', 'VedikaController@deleteRadiologi')->name('vedika.deleteRadiologi');

Route::get('/vedika/obatkronis', 'VedikaController@pasienkronis')->name('vedika.pasienkronis');
Route::get('/vedika/obatkronis/{id}/detail', 'VedikaController@detailCronis')->name('vedika.detailCronis');

Route::get('/vedika/obatkronis/{id}/gabungpdf', 'VedikaController@gabungKronisPdf')->name('vedika.gabungKronis');
Route::get('/vedika/obatkronis/{id}/viewgabungpdf', 'VedikaController@lihatKronisPdf')->name('vedika.lihatKronisPdf');

Route::post('/vedika/verifikasi', 'VedikaController@simpanVerif')->name('vedika.simpanVerif');
Route::post('/vedika/{id}/verifikasi', 'VedikaController@updateVerif')->name('vedika.updateVerif');
Route::post('/vedika/berkas/store', 'VedikaController@berkasUpload')->name('vedika.berkasUpload');
// Route::post('/vedika/berkas/store', 'VedikaController@berkasStore')->name('vedika.berkasStore');
Route::get('/vedika/berkas/{id}/delete', 'VedikaController@berkasDelete')->name('vedika.berkasDelete');
Route::get('/vedika/berkas/{id}/view', 'VedikaController@berkasShow')->name('vedika.berkasShow');

Route::post('/vedika/pengajuan', 'KlaimController@pengajuan')->name('vedika.pengajuan');
Route::post('/vedika/pengajuanpending', 'KlaimController@pengajuanUlang')->name('vedika.pengajuanUlang');
Route::post('/vedika/pengajuankronis', 'KlaimController@pengajuanKronis')->name('vedika.pengajuanKronis');
Route::post('/vedika/pengajuan/{id}/update', 'KlaimController@updatePengajuan')->name('vedika.updatePengajuan');
Route::post('/vedika/pengajuankronis/{id}/update', 'KlaimController@updatePengajuanKronis')->name('vedika.updatePengajuanKronis');
Route::get('/vedika/pengajuan/{id}/delete', 'KlaimController@deletePengajuan')->name('vedika.deletePengajuan');
Route::get('/vedika/pengajuankronis/{id}/delete', 'KlaimController@deletePengajuanKronis')->name('vedika.deletePengajuanKronis');
Route::get('/vedika/pengajuanpending/{id}/delete', 'KlaimController@deletePengajuanUlang')->name('vedika.deletePengajuanUlang');
Route::get('/vedika/pengajuan/rajal', 'KlaimController@daftarRajal')->name('vedika.daftarRajal');
Route::get('/vedika/pengajuan/kronis', 'KlaimController@daftarRajalKronis')->name('vedika.daftarRajalKronis');
Route::get('/vedika/pengajuan/ranap', 'KlaimController@daftarRanap')->name('vedika.daftarRanap');
Route::get('/vedika/pengajuan/ulang', 'KlaimController@daftarUlang')->name('vedika.daftarUlang');

Route::get('/vedika/pengajuan/{periode}/gabungberkasall', 'VedikaController@gabungBerkasAll')->name('vedika.gabungberkasall');
Route::get('/vedika/pengajuan/{periode}/makezipranap', 'VedikaController@generateZipRanap')->name('vedika.makezipranap');
Route::get('/vedika/pengajuan/{periode}/makeziprajal', 'VedikaController@generateZipRajal')->name('vedika.makeziprajal');
Route::get('/vedika/pengajuan/{jenis}/{periode}/downloadzip', 'VedikaController@downloadZip')->name('vedika.downloadzip');
Route::get('/vedika/pengajuanpending/{periode}/makezipranap', 'VedikaController@generateZipRanapPending')->name('vedika.makezipranap_pending');
Route::get('/vedika/pengajuanpending/{periode}/makeziprajal', 'VedikaController@generateZipRajalPending')->name('vedika.makeziprajal_pending');
Route::get('/vedika/pengajuanpending/{jenis}/{periode}/downloadzip', 'VedikaController@downloadZipPending')->name('vedika.downloadzip_pending');
Route::get('/vedika/pengajuankronis/{periode}/makezip', 'VedikaController@generateZipKronis')->name('vedika.makezipkronis');
Route::get('/vedika/pengajuankronis/{periode}/downloadzip', 'VedikaController@downloadZipKronis')->name('vedika.downloadzipkronis');

Route::get('/vedika/fraud/rajal', 'FraudController@rajal')->name('vedika.fraudRajal');
Route::get('/vedika/fraud/ranap', 'FraudController@ranap')->name('vedika.fraudRanap');
Route::get('/vedika/fraud/{id}/{idd}/store', 'FraudController@store')->name('vedika.fraudStore');
Route::get('/vedika/fraud/{id}/{idd}/storeranap', 'FraudController@storeranap')->name('vedika.fraudStoreRanap');
Route::get('/vedika/fraud/{id}/delete', 'FraudController@delete')->name('vedika.fraudDelete');
Route::get('/vedika/fraud/{id}/deleteranap', 'FraudController@deleteranap')->name('vedika.fraudDeleteRanap');
Route::get('/vedika/fraud/{id}/detail', 'FraudController@detailRajal')->name('vedika.fraudDetailRajal');
Route::get('/vedika/fraud/{id}/detailranap', 'FraudController@detailRanap')->name('vedika.fraudDetailRanap');
Route::post('/vedika/fraud/{id}/store', 'FraudController@storeRajal')->name('vedika.fraudStoreRajal');
Route::post('/vedika/fraud/{id}/storeranap', 'FraudController@storeDetailRanap')->name('vedika.fraudStoreDetailRanap');
Route::get('/vedika/fraud/{id}/export', 'FraudController@exportRajal')->name('vedika.exportRajal');
Route::get('/vedika/fraud/{id}/exportranap', 'FraudController@exportRanap')->name('vedika.exportRanap');
Route::get('/vedika/klaimcompare', 'KlaimCompareController@index')->name('vedika.klaimcompare');
Route::post('/vedika/klaimcompare/import', 'KlaimCompareController@import_excel')->name('vedika.klaimcompare.import');
Route::get('/vedika/klaimcompare/template', 'KlaimCompareController@template')->name('vedika.klaimcompare.template');
Route::post('/vedika/klaimcompare/ambilresponevklaim', 'KlaimCompareController@ambilResponeVklaim')->name('vedika.klaimcompare.ambilRespone');

Route::get('/vedika/eklaim/{id}/printout', 'EklaimController@getStatus')->name('eklaim.status');

Route::get('/vedika/pendingdpjp', 'KlaimCompareController@lihatDpjpPending')->name('vedika.pendingDpjp');
Route::get('/vedika/tidaklayak', 'KlaimCompareController@lihatDpjpGagal')->name('vedika.tidakLayak');
Route::post('/vedika/tidaklayak/import', 'KlaimCompareController@importTidakLayak')->name('vedika.importTidakLayak');
Route::get('/vedika/tidaklayak/template', 'KlaimCompareController@templateTidakLayak')->name('vedika.tidaklayak.template');

Route::get('/sep', 'SepController@getSep')->name('sep.getSep');
// Route::get('/sep2', 'SepController@getJmlSep')->name('sep.getJmlSep');
// Route::get('/coba', 'EklaimController@getDetail')->name('sep.coba');

// Route::get('/show-log', function () {
//     $logFile = storage_path('logs/laravel.log');
//     if (File::exists($logFile)) {
//         $logs = File::get($logFile);
//         return nl2br($logs); // Menampilkan log dengan format yang rapi di browser
//     }
//     return "Log file tidak ditemukan.";
// });

// Route::get('/phpinfo', function () {
//     if (!in_array(request()->ip(), ['192.168.1.208', 'your-allowed-ip'])) {
//         abort(403, 'Unauthorized access');
//     }
//     phpinfo();
// });

//IBS OPERASI
Route::get('/operasi/booking', 'OperasiController@index')->name('operasi.index');
Route::post('/operasi/booking', 'OperasiController@simpan')->name('operasi.index');
Route::get('/operasi/{id}/booking', 'OperasiController@booking')->name('operasi.index');
Route::get('/operasi/jadwal', 'OperasiController@jadwal')->name('operasi.jadwal');

//COBA WHATSAPP API BAILEY
Route::get('/pesan', 'WaController@index')->name('wa.index');
Route::get('/pesan/createsession', 'WaController@createSession')->name('wa.createSession');
Route::get('/pesan/deletesession', 'WaController@deleteSession')->name('wa.deleteSession');
Route::get('/pesan/kirim', 'WaController@kirimPesan')->name('wa.kirimPesan');
Route::post('/pesan/kirim', 'WaController@kirim')->name('wa.kirim');

//Tarif SIMRS
Route::get('/tarifsimrs/rajal', 'TarifSimController@rajal')->name('tarifsim.rajal');
Route::get('/tarifsimrs/rajal/exportexcel', 'TarifSimController@exportRajal')->name('tarifsim.exportRajal');
Route::get('/tarifsimrs/rajal/template', 'TarifSimController@templateImportRajal')->name('tarifsim.templateImportRajal');
Route::post('/tarifsimrs/rajal/importexcel', 'TarifSimController@importRajal')->name('tarifsim.importRajal');

Route::get('/tarifsimrs/ranap', 'TarifSimController@ranap')->name('tarifsim.ranap');
Route::get('/tarifsimrs/ranap/exportexcel', 'TarifSimController@exportRanap')->name('tarifsim.exportRanap');
Route::get('/tarifsimrs/ranap/template', 'TarifSimController@templateImportRanap')->name('tarifsim.templateImportRanap');
Route::post('/tarifsimrs/ranap/importexcel', 'TarifSimController@importRanap')->name('tarifsim.importRanap');

Route::get('/tarifsimrs/lab', 'TarifSimController@lab')->name('tarifsim.lab');
Route::get('/tarifsimrs/lab/exportexcel', 'TarifSimController@exportLab')->name('tarifsim.exportLab');
Route::get('/tarifsimrs/lab/template', 'TarifSimController@templateImportLab')->name('tarifsim.templateImportLab');
Route::post('/tarifsimrs/lab/importexcel', 'TarifSimController@importLab')->name('tarifsim.importLab');

Route::get('/tarifsimrs/radiologi', 'TarifSimController@radiologi')->name('tarifsim.radio');
Route::get('/tarifsimrs/radiologi/exportexcel', 'TarifSimController@exportRadiologi')->name('tarifsim.exportRadio');
Route::get('/tarifsimrs/radiologi/template', 'TarifSimController@templateImportRadiologi')->name('tarifsim.templateImportRadio');
Route::post('/tarifsimrs/radiologi/importexcel', 'TarifSimController@importRadiologi')->name('tarifsim.importRadio');

Route::get('/tarifsimrs/operasi', 'TarifSimController@operasi')->name('tarifsim.operasi');
Route::get('/tarifsimrs/operasi/exportexcel', 'TarifSimController@exportOperasi')->name('tarifsim.exportOperasi');
Route::get('/tarifsimrs/operasi/template', 'TarifSimController@templateImportOperasi')->name('tarifsim.templateImportOperasi');
Route::post('/tarifsimrs/operasi/importexcel', 'TarifSimController@importOperasi')->name('tarifsim.importOperasi');

//SATU SEHAT
Route::get('/satusehat', 'SatuSehatController@summary')->name('satuSehat.summary');
Route::get('/satusehat/bundle', 'SatuSehatController@bundleData')->name('satuSehat.bundleData');
Route::get('/satusehat/{id}/encounter', 'SatuSehatController@sendSingleEncounter')->name('satuSehat.singleEncounter');
Route::get('/satusehat/composition', 'SatuSehatController@sendComposition')->name('satuSehat.composition');
Route::get('/satusehat/medication', 'SatuSehatController@sendMedication')->name('satuSehat.medication');
Route::get('/satusehat/lab', 'SatuSehatController@sendLab')->name('satuSehat.sendLab');
Route::get('/satusehat/labbundle', 'SatuSehatController@bundleLab')->name('satuSehat.bundleLab');
Route::get('/satusehat/radiologi', 'RadiologiController@index')->name('satuSehat.radiologi');
Route::get('/satusehat/cek', 'SatuSehatController@checkRajal')->name('satuSehat.checkRajal');
Route::get('/satusehat/cek/{id}/detail', 'SatuSehatController@checkRajalDetail')->name('satuSehat.checkRajalDetail');
Route::get('/satusehat/cek/{id}/send', 'SatuSehatController@sendSingleBundle')->name('satuSehat.checkRajalSend');

Route::get('/satusehat/igd', 'IgdSehatController@index')->name('satuSehatIgd.index');
Route::get('/satusehat/igd/kirimencounter', 'IgdSehatController@sendEncounter')->name('satuSehatIgd.sendEncounter');
Route::get('/satusehat/igd/encounterupdate', 'IgdSehatController@closeEncounter')->name('satuSehatIgd.closeEncounter');

Route::get('/satusehat/ranap', 'RanapSehatController@index')->name('satuSehatRanap.index');
Route::get('/satusehat/ranap/kirimencounter', 'RanapSehatController@sendEncounter')->name('satuSehatRanap.sendEncounter');
Route::get('/satusehat/ranap/encounterupdate', 'RanapSehatController@closeEncounter')->name('satuSehatRanap.closeEncounter');

Route::get('/satusehat/kfa', 'KfaController@cari')->name('satuSehat.kfa');

Route::get('/satusehat/kjsu', 'KjsuController@index')->name('satuSehat.kjsu');
Route::get('/satusehat/kjsu/{id}/detail', 'KjsuController@detail')->name('satuSehat.kjsuDetail');
Route::post('/satusehat/kjsu/kirimeoc', 'KjsuController@kirimEoc')->name('satuSehat.kjsuKirimEoc');

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
// Route::get('/master/bank/refBank', 'BankController@refBank')->name('bank.refBank');

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

Route::get('/master/vedika/klaim', 'KlaimController@index')->name('vedika.klaim');
Route::post('/master/vedika/klaim', 'KlaimController@store')->name('vedika.klaimstore');
Route::get('/master/vedika/klaim/{id}/delete', 'KlaimController@delete')->name('vedika.klaimdelete');
Route::get('/master/vedika/klaim/{id}/edit', 'KlaimController@edit')->name('vedika.klaimedit');
Route::post('/master/vedika/klaim/{id}/update', 'KlaimController@update')->name('vedika.klaimupdate');

Route::get('/master/vedika/klaimpending', 'KlaimController@periodePending')->name('vedika.klaimpending');
Route::post('/master/vedika/klaimpending', 'KlaimController@storePending')->name('vedika.klaimpendingstore');
Route::get('/master/vedika/klaimpending/{id}/delete', 'KlaimController@deletePending')->name('vedika.klaimpendingdelete');
Route::get('/master/vedika/klaimpending/{id}/edit', 'KlaimController@editPending')->name('vedika.klaimpendingedit');
Route::post('/master/vedika/klaimpending/{id}/update', 'KlaimController@updatePending')->name('vedika.klaimpendingupdate');

Route::get('/permission', 'PermissionController@index')->name('permission.index');
Route::post('/permission/store', 'PermissionController@store')->name('permission.store');
Route::get('/permission/edit/{id}', 'PermissionController@edit')->name('permission.edit');
Route::post('/permission/update/{id}', 'PermissionController@update')->name('permission.update');
Route::get('/permission/delete/{id}', 'PermissionController@delete')->name('permission.delete');

Route::resource('roles', 'RoleController');
Route::get('/logs', 'LogController@index')->name('logs.index');

Route::get('/profil', 'UserController@profile')->name('user.profile');
Route::post('/profil/update', 'UserController@profileupdate')->name('user.profileupdate');
Route::post('/profil/password', 'UserController@password')->name('user.password');

//SSO
Route::get('sso/redirect', 'SSOController@redirectToSSOServer')->name('sso.redirect');
Route::get('sso/callback', 'SSOController@handleSSOCallback')->name('sso.callback');

// Route::get('/clear-cache', function () {
//     $exitCode = Artisan::call('optimize:clear');
//     return redirect('/login');
// });

// //Clear Config cache:
// Route::get('/config-cache', function () {
//     $exitCode = Artisan::call('config:cache');
//     return '<h1>Clear Config cleared</h1>';
// });
