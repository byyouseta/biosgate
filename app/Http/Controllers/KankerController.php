<?php

namespace App\Http\Controllers;

use App\InstalasiKanker;
use App\PelaporanKanker;
use App\Provinsi;
use Illuminate\Http\Request;
use App\Setting;
use App\SubinstalasiKanker;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class KankerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('permission:kanker-ranap-list|kanker-ranap-create', ['only' => ['pasien']]);
        $this->middleware('permission:kanker-ranap-create', ['only' => ['addRanap', 'store']]);
        $this->middleware('permission:kanker-rajal-list', ['only' => ['rajal']]);
        $this->middleware('permission:kanker-rajal-create', ['only' => ['addRajal', 'store']]);
        $this->middleware('permission:kanker-terlapor-list', ['only' => ['terlapor']]);
        $this->middleware('permission:kanker-terlapor-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:kanker-terlapor-delete', ['only' => ['delete']]);
    }

    public static function tokenkanker()
    {
        $setting = Setting::where('nama', 'datakanker')->first();
        // dd($setting);
        session()->put('base_url', $setting->base_url);
        try {
            $client = new \GuzzleHttp\Client(['base_uri' => $setting->base_url]);
            $response = $client->request('POST', 'user/request_token', [
                'headers' => [
                    'X-Api-Key' => "sirskemkes"
                ],
                'json' => [
                    'username' => $setting->satker,
                    'password' => $setting->key,
                ]
            ]);
        } catch (ClientException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode((string) $response->getBody());
            }

            // $id = Crypt::encrypt($id);
            Session::flash('error', $test->message);

            return redirect()->back()->withInput();
        }

        $data = json_decode($response->getBody());

        // dd($data);

        if ($data->message == "Token generated") {
            session()->put('tokenkanker', $data->data->token);
        }
        // dd(session('tokenrs'));
    }

    public function pasien()
    {
        session()->put('ibu', 'Data Kanker');
        session()->put('anak', 'Pasien Ranap');
        session()->forget('cucu');

        $tanggal = Carbon::now()->format('Y-m-d');

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
            ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
            ->leftJoin('bangsal', 'bangsal.kd_bangsal', '=', 'kamar.kd_bangsal')
            ->join('diagnosa_pasien', 'diagnosa_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'kamar_inap.tgl_masuk',
                'kamar_inap.tgl_keluar',
                'kamar_inap.diagnosa_awal',
                'kamar_inap.diagnosa_akhir',
                'kamar_inap.stts_pulang',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pasien.pekerjaan',
                'pasien.no_tlp',
                'pasien.email',
                'pasien.kd_kel',
                'pasien.kd_kec',
                'pasien.kd_kab',
                'pasien.kd_prop',
                'penjab.png_jawab',
                'bangsal.nm_bangsal',
                'kamar.kd_kamar',
                'diagnosa_pasien.kd_penyakit',
                // 'diagnosa_pasien.prioritas',
                'penyakit.nm_penyakit'
            )
            ->where(function ($q) {
                $q->where('penyakit.nm_penyakit', 'like', '%malignant%')
                    ->orWhere('penyakit.nm_penyakit', 'like', '%anaemia%')
                    ->orWhere('penyakit.nm_penyakit', 'like', '%leukaemia%')
                    ->orWhere('penyakit.nm_penyakit', 'like', '%neoplasm%');
            })
            ->where(function ($pulang) {
                $pulang->where('kamar_inap.stts_pulang', '=', '-')
                    ->orWhere('kamar_inap.stts_pulang', '=', 'Pindah Kamar');
            })
            ->where(function ($query) use ($tanggal) {
                $query->whereDate('kamar_inap.tgl_masuk', '<=', $tanggal)
                    ->whereDate('kamar_inap.tgl_keluar', '>=', $tanggal)
                    ->orWhereDate('kamar_inap.tgl_keluar', '=', '0000-00-00');
            })

            ->get();

        // dd($data);

        return view('data_kanker.pasien_ranap', compact('data'));
    }

    public function addRanap($id)
    {
        $id = Crypt::decrypt($id);

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
            ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
            ->leftJoin('bangsal', 'bangsal.kd_bangsal', '=', 'kamar.kd_bangsal')
            ->join('diagnosa_pasien', 'diagnosa_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.status_lanjut',
                'reg_periksa.kd_poli',
                'reg_periksa.kd_pj',
                'kamar_inap.tgl_masuk',
                'kamar_inap.tgl_keluar',
                'kamar_inap.diagnosa_awal',
                'kamar_inap.diagnosa_akhir',
                'kamar_inap.stts_pulang',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pasien.pekerjaan',
                'pasien.no_tlp',
                'pasien.no_peserta',
                'pasien.alamat',
                'pasien.kd_kel',
                'pasien.kd_kec',
                'pasien.kd_kab',
                'pasien.kd_prop',
                'penjab.png_jawab',
                'bangsal.nm_bangsal',
                'kamar.kd_kamar',
                'diagnosa_pasien.kd_penyakit',
                // 'diagnosa_pasien.prioritas',
                'penyakit.nm_penyakit'
            )
            ->where('reg_periksa.no_rawat', $id)
            ->first();

        // dd($data);

        $provinsi = Provinsi::all();
        $caraMasuk = KankerController::CaraMasuk();
        $asalRujukan = KankerController::AsalRujukan();
        $instalasi = InstalasiKanker::all();
        $caraKeluar = KankerController::CaraKeluar();
        $keadaanKeluar = KankerController::KeadaanKeluar();
        // $icd10 = KankerController::geticd10(null);
        $caraBayar = KankerController::CaraBayar();

        return view('data_kanker.lapor_kanker', compact(
            'data',
            'provinsi',
            'caraMasuk',
            'asalRujukan',
            'instalasi',
            'caraKeluar',
            'keadaanKeluar',
            // 'icd10',
            'caraBayar'
        ));
    }

    public function rajal(Request $request)
    {
        session()->put('ibu', 'Data Kanker');
        session()->put('anak', 'Pasien Rajal/IGD');
        session()->forget('cucu');

        if (!empty($request->get('tanggal'))) {
            $tanggal = $request->get('tanggal');
        } else {
            $tanggal = Carbon::now()->format('Y-m-d');
        }

        // dd($tanggal);

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            // ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('diagnosa_pasien', 'diagnosa_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.status_lanjut',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.tgl_lahir',
                'pasien.pekerjaan',
                'diagnosa_pasien.kd_penyakit',
                'diagnosa_pasien.prioritas',
                'penyakit.nm_penyakit',
                'poliklinik.nm_poli'
            )
            ->where('reg_periksa.status_lanjut', '=', 'Ralan')
            ->where(function ($q) {
                $q->where('penyakit.nm_penyakit', 'like', '%malignant%')
                    ->orWhere('penyakit.nm_penyakit', 'like', '%anaemia%')
                    ->orWhere('penyakit.nm_penyakit', 'like', '%leukaemia%')
                    ->orWhere('penyakit.nm_penyakit', 'like', '%neoplasm%');
            })
            ->whereDate('reg_periksa.tgl_registrasi', $tanggal)
            ->get();

        // dd($data);

        return view('data_kanker.pasien_rajal', compact('data'));
    }

    public function addRajal($id)
    {
        $id = Crypt::decrypt($id);

        // dd($id);

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            // ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('diagnosa_pasien', 'diagnosa_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi as tgl_masuk',
                'reg_periksa.status_lanjut',
                'reg_periksa.kd_poli',
                'reg_periksa.kd_pj',
                // 'kamar_inap.tgl_keluar',
                // 'kamar_inap.diagnosa_awal',
                // 'kamar_inap.diagnosa_akhir',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.no_peserta',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pasien.alamat',
                'pasien.no_tlp',
                'pasien.email',
                'pasien.kd_kel',
                'pasien.kd_kec',
                'pasien.kd_kab',
                'pasien.kd_prop',
                'diagnosa_pasien.kd_penyakit',
                'diagnosa_pasien.prioritas',
                'penyakit.nm_penyakit'
            )
            ->where('reg_periksa.no_rawat', $id)
            ->first();

        // dd($data);

        $provinsi = Provinsi::all();
        $caraMasuk = KankerController::CaraMasuk();
        $asalRujukan = KankerController::AsalRujukan();
        $instalasi = InstalasiKanker::all();
        $caraKeluar = KankerController::CaraKeluar();
        $keadaanKeluar = KankerController::KeadaanKeluar();
        // $icd10 = KankerController::geticd10(null);
        $caraBayar = KankerController::CaraBayar();


        // dd($icd10);

        return view('data_kanker.lapor_kanker', compact(
            'data',
            'provinsi',
            'caraMasuk',
            'asalRujukan',
            'instalasi',
            'caraKeluar',
            'keadaanKeluar',
            // 'icd10',
            'caraBayar'
        ));
    }

    public function store(Request $request)
    {
        // dd($request);

        if ($request->AlamatTinggalSama != "on") {
            $provinsiTinggal = $request->provinsiTinggal;
            $kabKotaTinggal = $request->kabKotaTinggal;
            $kecamatanTinggal = $request->kecamatanTinggal;
            $kelurahanTinggal = $request->kelurahanTinggal;
        } else {
            $provinsiTinggal = $request->provinsi;
            $kabKotaTinggal = $request->kabKota;
            $kecamatanTinggal = $request->kecamatan;
            $kelurahanTinggal = $request->kelurahan;
        }

        //asalRujukan
        if (!empty($request->asalRujukan)) {
            $asalRujukan = $request->asalRujukan;
        } else {
            $asalRujukan = null;
        }

        //Nama Fasyankes Lainnya
        if (!empty($request->fasyankesLain)) {
            $fasyankesLain = $request->fasyankesLain;
        } else {
            $fasyankesLain = null;
        }

        $DiagnosisMasuk = explode('_', $request->DiagnosisMasuk);

        // dd($DiagnosisMasuk[0]);

        KankerController::tokenkanker();

        $access_token = session('tokenkanker');
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        try {
            $response = $client->request('POST', 't_register_penyakit/add', [
                'headers' => [
                    'X-Api-Key' => "sirskemkes",
                    'X-Token' => $access_token
                ],
                'json' => [
                    'id_jenis_pelaporan' => '2',
                    'id_jenis_kasus' => '2.5',
                    'nik' => $request->nik,
                    'nama_pasien' => $request->namaPasien,
                    'id_jenis_kelamin' => $request->jk,
                    'tanggal_lahir' => $request->tgl_lahir,
                    'alamat' => $request->alamat,
                    'id_kelurahan' => $request->kelurahan,
                    'id_kecamatan' => $request->kecamatan,
                    'id_kab_kota' => $request->kabKota,
                    'id_provinsi' => $request->provinsi,
                    'alamat_tinggal' => $request->alamat,
                    'id_kelurahan_tinggal' => $kelurahanTinggal,
                    'id_kecamatan_tinggal' => $kecamatanTinggal,
                    'id_kab_kota_tinggal' => $kabKotaTinggal,
                    'id_provinsi_tinggal' => $provinsiTinggal,
                    'kontak_pasien' => $request->nohp,
                    'tanggal_masuk' => $request->tgl_masuk,
                    'id_cara_masuk_pasien' => $request->caraMasuk,
                    'id_asal_rujukan_pasien' => $asalRujukan,
                    'asal_rujukan_pasien_fasyankes_lainnya' => $fasyankesLain,
                    'id_diagnosa_masuk' => $DiagnosisMasuk[0], //belum ki
                    'id_sub_instalasi_unit' => $request->subinstalasi,
                    'id_diagnosa_utama' => $request->diagnosaUtama,
                    'id_diagnosa_sekunder1' => $request->sekunder1,
                    'id_diagnosa_sekunder2' => $request->sekunder2,
                    'id_diagnosa_sekunder3' => $request->sekunder3,
                    'tanggal_diagnosa' => $request->tglDiagnosis,
                    'tanggal_keluar' => $request->tglKeluar,
                    'id_cara_keluar' => $request->caraKeluar,
                    'id_keadaan_keluar' => $request->keadaanKeluar,
                    'id_sebab_kematian_langsung_1a' => $request->kematian1a,
                    'id_sebab_kematian_antara_1b' => $request->kematian1b,
                    'id_sebab_kematian_antara_1c' => $request->kematian1c,
                    'id_sebab_kematian_dasar_1d' => $request->kematian1d,
                    'id_kondisi_yg_berkontribusi_thdp_kematian' => $request->kondisi,
                    'sebab_dasar_kematian' => $request->sebabDasar,
                    'id_cara_bayar' => $request->caraBayar,
                    'nomor_bpjs' => $request->noBpjs
                ]
            ]);
        } catch (ClientException $e) {
            // echo $e->getRequest();
            // echo $e->getResponse();
            if ($e->hasResponse()) {
                $response = $e->getResponse();

                // dd($response);
                $test = json_decode($response->getBody());
                // dd($test);
            }

            $message = "";
            for ($i = 0; $i < count($test->message); $i++) {
                if (empty($message)) {
                    $message = $test->message[$i];
                } else {
                    $message = $message + ", " + $test->message[$i];
                }
            }

            Session::flash('error', $message);

            return redirect()->back()->withInput();
        }

        // dd($response);

        $data = json_decode($response->getBody());
        // $data =  $response->json();
        // $data = $data->data;

        // $response = $response->getBody()->__toString();
        // $response =  stripslashes($response);
        // $data = json_decode($response, true);

        // dd($data);
        if ($data->status == "true") {
            $berhasil = new PelaporanKanker();

            $berhasil->idReg = $data->id_simpan;
            $berhasil->noRawat = $request->noRawat;
            $berhasil->nik = $request->nik;
            $berhasil->nama_pasien = $request->namaPasien;
            $berhasil->id_jenis_kelamin = $request->jk;
            $berhasil->tanggal_lahir = $request->tgl_lahir;
            $berhasil->alamat = $request->alamat;
            $berhasil->id_provinsi = $request->provinsi;
            $berhasil->id_kab_kota = $request->kabKota;
            $berhasil->id_kecamatan = $request->kecamatan;
            $berhasil->id_kelurahan = $request->kelurahan;
            $berhasil->alamat_tinggal = $request->alamatTinggal;
            $berhasil->id_provinsi_tinggal = $provinsiTinggal;
            $berhasil->id_kab_kota_tinggal = $kabKotaTinggal;
            $berhasil->id_kecamatan_tinggal = $kecamatanTinggal;
            $berhasil->id_kelurahan_tinggal = $kelurahanTinggal;
            $berhasil->kontak_pasien = $request->nohp;
            $berhasil->tanggal_masuk = $request->tgl_masuk;
            $berhasil->id_cara_masuk_pasien = $request->caraMasuk;
            $berhasil->id_asal_rujukan_pasien = $asalRujukan;
            $berhasil->asal_rujukan_pasien_fasyankes_lainnya = $fasyankesLain;
            $berhasil->id_diagnosa_masuk = $DiagnosisMasuk[0];
            $berhasil->id_sub_instalasi_unit = $request->subinstalasi;
            $berhasil->id_diagnosa_utama = $request->diagnosaUtama;
            $berhasil->id_diagnosa_sekunder1 = $request->sekunder1;
            $berhasil->id_diagnosa_sekunder2 = $request->sekunder2;
            $berhasil->id_diagnosa_sekunder3 = $request->sekunder3;
            $berhasil->tanggal_diagnosa = $request->tglDiagnosis;
            $berhasil->tanggal_keluar = $request->tglKeluar;
            $berhasil->id_cara_keluar = $request->caraKeluar;
            $berhasil->id_keadaan_keluar = $request->keadaanKeluar;
            $berhasil->id_sebab_kematian_langsung_1a = $request->kematian1a;
            $berhasil->id_sebab_kematian_antara_1b = $request->kematian1b;
            $berhasil->id_sebab_kematian_antara_1c = $request->kematian1c;
            $berhasil->id_sebab_kematian_dasar_1d = $request->kematian1d;
            $berhasil->id_kondisi_yg_berkontribusi_thdp_kematian = $request->kondisi;
            $berhasil->sebab_dasar_kematian = $request->sebabDasar;
            $berhasil->id_cara_bayar = $request->caraBayar;
            $berhasil->nomor_bpjs = $request->noBpjs;
            $berhasil->save();
        }

        Session::flash('sukses', $data->message . ', No Id Pelaporan : ' . $data->id_simpan);
        return redirect('/kanker/terlapor');
    }

    public function terlapor()
    {
        session()->put('ibu', 'Data Kanker');
        session()->put('anak', 'Pasien Terlapor');
        session()->forget('cucu');

        $data = PelaporanKanker::all();

        // dd($caraKeluar);

        return view('data_kanker.pasien_terlapor', compact(
            'data'
        ));
    }

    public function edit($id)
    {
        $id = Crypt::decrypt($id);
        $data = PelaporanKanker::where('idReg', $id)->first();
        $provinsi = Provinsi::all();
        $caraMasuk = KankerController::CaraMasuk();
        $asalRujukan = KankerController::AsalRujukan();
        $instalasi = InstalasiKanker::all();
        $caraKeluar = KankerController::CaraKeluar();
        $keadaanKeluar = KankerController::KeadaanKeluar();
        // $icd10 = KankerController::geticd10(null);
        $caraBayar = KankerController::CaraBayar();

        // dd($data);

        return view('data_kanker.edit_lapor_kanker', compact(
            'data',
            'provinsi',
            'caraMasuk',
            'asalRujukan',
            'instalasi',
            'caraKeluar',
            'keadaanKeluar',
            'caraBayar'
        ));
    }

    public function update($id, Request $request)
    {
        // dd($request, $id);
        if ($request->provinsi == null) {
            $dataProv = explode('-', $request->dataProvinsi);
            $provinsi = $dataProv[0];
            $dataKab = explode('-', $request->dataKabKota);
            $kabKota = $dataKab[0];
            $dataKec = explode('-', $request->dataKecamatan);
            $kecamatan = $dataKec[0];
            $dataKel = explode('-', $request->dataKelurahan);
            $kelurahan = $dataKel[0];
        }

        if ($request->AlamatTinggalSama != "on") {
            if ($request->provinsiTinggal == null) {
                $dataProv = explode('-', $request->dataProvinsiTinggal);
                $provinsiTinggal = $dataProv[0];
                $dataKab = explode('-', $request->dataKabKotaTinggal);
                $kabKotaTinggal = $dataKab[0];
                $dataKec = explode('-', $request->dataKecamatanTinggal);
                $kecamatanTinggal = $dataKec[0];
                $dataKel = explode('-', $request->dataKelurahanTinggal);
                $kelurahanTinggal = $dataKel[0];
            } else {
                $provinsiTinggal = $request->provinsiTinggal;
                $kabKotaTinggal = $request->kabKotaTinggal;
                $kecamatanTinggal = $request->kecamatanTinggal;
                $kelurahanTinggal = $request->kelurahanTinggal;
            }
        } else {
            $provinsiTinggal = $provinsi;
            $kabKotaTinggal = $kabKota;
            $kecamatanTinggal = $kecamatan;
            $kelurahanTinggal = $kelurahan;
        }

        //asalRujukan
        if (!empty($request->asalRujukan)) {
            $asalRujukan = $request->asalRujukan;
        } else {
            $asalRujukan = null;
        }

        //Nama Fasyankes Lainnya
        if (!empty($request->fasyankesLain)) {
            $fasyankesLain = $request->fasyankesLain;
        } else {
            $fasyankesLain = null;
        }

        // $DiagnosisMasuk = explode('_', $request->DiagnosisMasuk);

        if ($request->subinstalasi == null) {
            $subInstalasi = explode('_', $request->dataSubinstalasi);
            // dd($request, $subInstalasi);
            $subInstalasi = $subInstalasi[0];
        }



        if ($request->diagnosaUtama == null) {
            $diagnosaUtama = $request->dataDiagnosaUtama;
        }

        if ($request->sekunder1 == null) {
            $sekunder1 = $request->dataSekunder1;
        }

        if ($request->sekunder2 == null) {
            $sekunder2 = $request->dataSekunder2;
        }

        if ($request->sekunder3 == null) {
            $sekunder3 = $request->dataSekunder3;
        }

        if ($request->kematian1a == null) {
            $kematian1a = $request->dataKematian1a;
        }

        if ($request->kematian1b == null) {
            $kematian1b = $request->dataKematian1b;
        }

        if ($request->kematian1c == null) {
            $kematian1c = $request->dataKematian1c;
        }

        if ($request->kematian1d == null) {
            $kematian1d = $request->dataKematian1d;
        }

        KankerController::tokenkanker();

        $access_token = session('tokenkanker');
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        try {
            $response = $client->request('POST', 't_register_penyakit/update', [
                'headers' => [
                    'X-Api-Key' => "sirskemkes",
                    'X-Token' => $access_token
                ],
                'json' => [
                    'id' => $id,
                    'id_jenis_pelaporan' => '2',
                    'id_jenis_kasus' => '2.5',
                    'nik' => $request->nik,
                    'nama_pasien' => $request->namaPasien,
                    'id_jenis_kelamin' => $request->jk,
                    'tanggal_lahir' => $request->tgl_lahir,
                    'alamat' => $request->alamat,
                    'id_kelurahan' => $kelurahan,
                    'id_kecamatan' => $kecamatan,
                    'id_kab_kota' => $kabKota,
                    'id_provinsi' => $provinsi,
                    'alamat_tinggal' => $request->alamat,
                    'id_kelurahan_tinggal' => $kelurahanTinggal,
                    'id_kecamatan_tinggal' => $kecamatanTinggal,
                    'id_kab_kota_tinggal' => $kabKotaTinggal,
                    'id_provinsi_tinggal' => $provinsiTinggal,
                    'kontak_pasien' => $request->nohp,
                    'tanggal_masuk' => $request->tgl_masuk,
                    'id_cara_masuk_pasien' => $request->caraMasuk,
                    'id_asal_rujukan_pasien' => $asalRujukan,
                    'asal_rujukan_pasien_fasyankes_lainnya' => $fasyankesLain,
                    'id_diagnosa_masuk' => $request->DiagnosisMasuk, //belum ki
                    'id_sub_instalasi_unit' => $subInstalasi,
                    'id_diagnosa_utama' => $diagnosaUtama,
                    'id_diagnosa_sekunder1' => $sekunder1,
                    'id_diagnosa_sekunder2' => $sekunder2,
                    'id_diagnosa_sekunder3' => $sekunder3,
                    'tanggal_diagnosa' => $request->tglDiagnosis,
                    'tanggal_keluar' => $request->tglKeluar,
                    'id_cara_keluar' => $request->caraKeluar,
                    'id_keadaan_keluar' => $request->keadaanKeluar,
                    'id_sebab_kematian_langsung_1a' => $kematian1a,
                    'id_sebab_kematian_antara_1b' => $kematian1b,
                    'id_sebab_kematian_antara_1c' => $kematian1c,
                    'id_sebab_kematian_dasar_1d' => $kematian1d,
                    'id_kondisi_yg_berkontribusi_thdp_kematian' => $request->kondisi,
                    'sebab_dasar_kematian' => $request->sebabDasar,
                    'id_cara_bayar' => $request->caraBayar,
                    'nomor_bpjs' => $request->noBpjs
                ]
            ]);
        } catch (ClientException $e) {
            // echo $e->getRequest();
            // echo $e->getResponse();
            if ($e->hasResponse()) {
                $response = $e->getResponse();

                // dd($response);
                $test = json_decode($response->getBody());
                // dd($test);
            }

            $message = $test->message;

            // if (count($test->message) == 0) {
            //     $banyak = 1;
            // } else {
            //     $banyak = count($test->message);
            // }

            // for ($i = 0; $i < count($test->message); $i++) {
            //     if (empty($message)) {
            //         $message = $test->message[$i];
            //     } else {
            //         $message = $message + ", " + $test->message[$i];
            //     }
            // }

            Session::flash('error', $message);

            return redirect()->back()->withInput();
        }

        // dd($response);

        $data = json_decode($response->getBody());


        // dd($data);
        if ($data->status == "true") {
            $berhasil = PelaporanKanker::where('idReg', $id)->first();

            // $berhasil->idReg = $data->id_simpan;
            $berhasil->noRawat = $request->noRawat;
            $berhasil->nik = $request->nik;
            $berhasil->nama_pasien = $request->namaPasien;
            $berhasil->id_jenis_kelamin = $request->jk;
            $berhasil->tanggal_lahir = $request->tgl_lahir;
            $berhasil->alamat = $request->alamat;
            $berhasil->id_provinsi = $provinsi;
            $berhasil->id_kab_kota = $kabKota;
            $berhasil->id_kecamatan = $kecamatan;
            $berhasil->id_kelurahan = $kelurahan;
            $berhasil->alamat_tinggal = $request->alamatTinggal;
            $berhasil->id_provinsi_tinggal = $provinsiTinggal;
            $berhasil->id_kab_kota_tinggal = $kabKotaTinggal;
            $berhasil->id_kecamatan_tinggal = $kecamatanTinggal;
            $berhasil->id_kelurahan_tinggal = $kelurahanTinggal;
            $berhasil->kontak_pasien = $request->nohp;
            $berhasil->tanggal_masuk = $request->tgl_masuk;
            $berhasil->id_cara_masuk_pasien = $request->caraMasuk;
            $berhasil->id_asal_rujukan_pasien = $asalRujukan;
            $berhasil->asal_rujukan_pasien_fasyankes_lainnya = $fasyankesLain;
            $berhasil->id_diagnosa_masuk = $request->DiagnosisMasuk;
            $berhasil->id_sub_instalasi_unit = $subInstalasi;
            $berhasil->id_diagnosa_utama = $diagnosaUtama;
            $berhasil->id_diagnosa_sekunder1 = $sekunder1;
            $berhasil->id_diagnosa_sekunder2 = $sekunder2;
            $berhasil->id_diagnosa_sekunder3 = $sekunder3;
            $berhasil->tanggal_diagnosa = $request->tglDiagnosis;
            $berhasil->tanggal_keluar = $request->tglKeluar;
            $berhasil->id_cara_keluar = $request->caraKeluar;
            $berhasil->id_keadaan_keluar = $request->keadaanKeluar;
            $berhasil->id_sebab_kematian_langsung_1a = $kematian1a;
            $berhasil->id_sebab_kematian_antara_1b = $kematian1b;
            $berhasil->id_sebab_kematian_antara_1c = $kematian1c;
            $berhasil->id_sebab_kematian_dasar_1d = $kematian1d;
            $berhasil->id_kondisi_yg_berkontribusi_thdp_kematian = $request->kondisi;
            $berhasil->sebab_dasar_kematian = $request->sebabDasar;
            $berhasil->id_cara_bayar = $request->caraBayar;
            $berhasil->nomor_bpjs = $request->noBpjs;
            $berhasil->save();
        }

        Session::flash('sukses', $data->message);
        return redirect('/kanker/terlapor');
    }

    public function delete($id)
    {
        $id = Crypt::decrypt($id);

        KankerController::tokenkanker();

        $access_token = session('tokenkanker');
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        try {
            $response = $client->request('POST', 't_register_penyakit/delete', [
                'headers' => [
                    'X-Api-Key' => "sirskemkes",
                    'X-Token' => $access_token
                ],
                'json' => [
                    'id' => $id
                ]
            ]);
        } catch (ClientException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());
            }

            Session::flash('error', $test->message);

            return redirect()->back()->withInput();
        }

        // dd($response);

        $data = json_decode($response->getBody());
        if ($data->status == "true") {
            $delete = PelaporanKanker::where('idReg', $id)->first();
            $delete->delete();
        }

        Session::flash('sukses', $data->message);

        return redirect()->back();
    }

    public function referensi()
    {
        session()->put('ibu', 'Data Kanker');
        session()->put('anak', 'Referensi Data');
        session()->forget('cucu');

        $caraMasuk = KankerController::CaraMasuk();
        $asalRujukan = KankerController::AsalRujukan();
        $instalasi = InstalasiKanker::all();
        $subinstalasi = SubinstalasiKanker::all();
        $caraKeluar = KankerController::CaraKeluar();
        $keadaanKeluar = KankerController::KeadaanKeluar();
        $caraBayar = KankerController::CaraBayar();

        // dd($caraKeluar);

        return view('data_kanker.referensi', compact(
            'caraMasuk',
            'asalRujukan',
            'instalasi',
            'subinstalasi',
            'caraKeluar',
            'keadaanKeluar',
            'caraBayar'
        ));
    }

    public function CaraMasuk()
    {
        KankerController::tokenkanker();

        $access_token = session('tokenkanker');
        try {
            $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
            $response = $client->request('GET', 'm_register_penyakit_cara_masuk_pasien/all', [
                'headers' => [
                    'X-Api-Key' => "sirskemkes",
                    'X-Token' => $access_token
                ]
            ]);
        } catch (ClientException $e) {

            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode((string) $response->getBody());
            }

            // $id = Crypt::encrypt($id);
            Session::flash('error', $test->message);

            return redirect()->back();
        }

        $data = json_decode($response->getBody());
        $data = $data->data->m_register_penyakit_cara_masuk_pasien;

        // dd($data);

        return $data;
    }

    public function AsalRujukan()
    {
        KankerController::tokenkanker();

        $access_token = session('tokenkanker');
        try {
            $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
            $response = $client->request('GET', 'm_register_penyakit_asal_rujukan_pasien/all', [
                'headers' => [
                    'X-Api-Key' => "sirskemkes",
                    'X-Token' => $access_token
                ]
            ]);
        } catch (ClientException $e) {

            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode((string) $response->getBody());
            }

            // $id = Crypt::encrypt($id);
            Session::flash('error', $test->message);

            return redirect()->back();
        }

        $data = json_decode($response->getBody());
        $data = $data->data->m_register_penyakit_asal_rujukan_pasien;

        // dd($data);

        return $data;
    }

    public function Instalasi()
    {
        KankerController::tokenkanker();

        $access_token = session('tokenkanker');
        try {
            $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
            $response = $client->request('GET', 'm_register_penyakit_instalasi_unit/all', [
                'headers' => [
                    'X-Api-Key' => "sirskemkes",
                    'X-Token' => $access_token
                ]
            ]);
        } catch (ClientException $e) {

            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode((string) $response->getBody());
            }

            // $id = Crypt::encrypt($id);
            Session::flash('error', $test->message);

            return redirect()->back();
        }

        $data = json_decode($response->getBody());
        $data = $data->data->m_register_penyakit_instalasi_unit;

        // dd($data);

        return $data;
    }

    public function CaraKeluar()
    {
        KankerController::tokenkanker();

        $access_token = session('tokenkanker');
        try {
            $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
            $response = $client->request('GET', 'm_register_penyakit_cara_keluar/all', [
                'headers' => [
                    'X-Api-Key' => "sirskemkes",
                    'X-Token' => $access_token
                ]
            ]);
        } catch (ClientException $e) {

            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode((string) $response->getBody());
            }

            // $id = Crypt::encrypt($id);
            Session::flash('error', $test->message);

            return redirect()->back();
        }

        $data = json_decode($response->getBody());
        $data = $data->data->m_register_penyakit_cara_keluar;

        // dd($data);

        return $data;
    }

    public function KeadaanKeluar()
    {
        KankerController::tokenkanker();

        $access_token = session('tokenkanker');
        try {
            $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
            $response = $client->request('GET', 'm_register_penyakit_keadaan_keluar/all', [
                'headers' => [
                    'X-Api-Key' => "sirskemkes",
                    'X-Token' => $access_token
                ]
            ]);
        } catch (ClientException $e) {

            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode((string) $response->getBody());
            }

            // $id = Crypt::encrypt($id);
            Session::flash('error', $test->message);

            return redirect()->back();
        }

        $data = json_decode($response->getBody());
        $data = $data->data->m_register_penyakit_keadaan_keluar;

        // dd($data);

        return $data;
    }

    public function CaraBayar()
    {
        KankerController::tokenkanker();

        $access_token = session('tokenkanker');
        try {
            $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
            $response = $client->request('GET', 'm_register_penyakit_cara_bayar/all', [
                'headers' => [
                    'X-Api-Key' => "sirskemkes",
                    'X-Token' => $access_token
                ]
            ]);
        } catch (ClientException $e) {

            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode((string) $response->getBody());
            }

            // $id = Crypt::encrypt($id);
            Session::flash('error', $test->message);

            return redirect()->back();
        }

        $data = json_decode($response->getBody());
        $data = $data->data->m_register_penyakit_cara_bayar;

        // dd($data);

        return $data;
    }

    public function icd10()
    {

        $data = DB::connection('mysqlkhanza')->table('penyakit')
            ->select('penyakit.kd_penyakit', 'penyakit.nm_penyakit')
            ->get();

        return $data;
    }

    public function geticd10(Request $request)
    {
        $data = [];

        if ($request->has('q')) {
            $search = $request->q;
            $data = DB::connection('mysqlkhanza')->table('penyakit')
                ->select('penyakit.kd_penyakit', 'penyakit.nm_penyakit', DB::raw("CONCAT(penyakit.kd_penyakit,' ',penyakit.nm_penyakit) AS nama_penyakit"))
                ->where('penyakit.kd_penyakit', 'LIKE', "%$search%")
                ->orWhere('penyakit.nm_penyakit', 'LIKE', "%$search%")
                ->get();
        }

        return response()->json($data);
    }

    public function test()
    {
        return view('data_kanker.test_ajax');
    }

    public function getSubinstalasi(Request $request)
    {
        $sub = SubinstalasiKanker::where('kode_instalasi_unit', $request->get('id'))
            ->pluck('sub_instalasi_unit', 'kode_gabung_sub_instalasi_unit');

        return response()->json($sub);
    }
}
