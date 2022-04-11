<?php

namespace App\Http\Controllers;

use App\DiagnosaLap;
use App\KabKota;
use App\Kecamatan;
use App\KomorbidLap;
use App\PelaporanCovid;
use App\PemeriksaanLab;
use App\Provinsi;
use App\Pulang;
use App\TerapiLap;
use App\VaksinasiLap;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Session;
use phpDocumentor\Reflection\PseudoTypes\True_;

use function GuzzleHttp\Promise\all;

class PasienOnlineController extends Controller
{
    public function index()
    {
        session()->put('ibu', 'RS Online');
        session()->put('anak', 'Data Pasien');
        session()->put('cucu', 'Pasien Baru');

        $tanggal = Carbon::now()->format('Y-m-d');

        // dd($tanggal);

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
            // ->join('diagnosa_pasien', 'diagnosa_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
            // ->join('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
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
                // 'diagnosa_pasien.kd_penyakit',
                // 'diagnosa_pasien.prioritas',
                // 'penyakit.nm_penyakit'
            )
            ->where(function ($q) {
                $q->where('kamar_inap.diagnosa_awal', 'like', '%covid%')
                    ->orWhere('kamar_inap.diagnosa_awal', 'like', '%U07.1%')
                    ->orWhere('kamar_inap.diagnosa_awal', 'like', '%U07.2%')
                    ->orWhere('kamar_inap.diagnosa_akhir', 'like', '%covid%')
                    ->orWhere('penjab.png_jawab', 'like', '%covid%');
                // ->orWhere('kamar_inap.diagnosa_akhir', 'like', '%U07.1%')
                // ->orWhere('kamar_inap.diagnosa_akhir', 'like', '%U07.2%');
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

        return view('data_rsonline.pasien_online', compact('data'));
    }

    public function add($id)
    {
        $id = Crypt::decrypt($id);

        // dd($id);

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            // ->join('diagnosa_pasien', 'diagnosa_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
            // ->join('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'kamar_inap.tgl_masuk',
                'kamar_inap.tgl_keluar',
                'kamar_inap.diagnosa_awal',
                'kamar_inap.diagnosa_akhir',
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
                // 'diagnosa_pasien.kd_penyakit',
                // 'diagnosa_pasien.prioritas',
                // 'penyakit.nm_penyakit'
            )
            ->where('reg_periksa.no_rawat', $id)
            ->first();

        $provinsi = Provinsi::all();
        // $kabkota = KabKota::all();

        $inisial = PasienOnlineController::Inisial($data->nm_pasien);
        $kewarganegaraan = RsClientController::kewarganegaraan();
        $dataasal = RsClientController::asalpasien();
        $datapekerjaan = RsClientController::pekerjaan();
        $datajenis = RsClientController::jenispasien();
        $datavarian = RsClientController::variancovid();
        $statuspasien = RsClientController::statuspasien();
        $statusrawat = RsClientController::statusrawat();
        $alatoksigen = RsClientController::alatoksigen();
        $datakelompok = RsClientController::kelompokgejala();
        // dd($dataasal);

        return view('data_rsonline.pasien_online_add', compact(
            'data',
            'inisial',
            'kewarganegaraan',
            'dataasal',
            'datapekerjaan',
            'datajenis',
            'datavarian',
            'statuspasien',
            'statusrawat',
            'alatoksigen',
            'datakelompok',
            'provinsi'
        ));
    }

    public function store(Request $request)
    {
        // dd($request);

        RsClientController::tokenrs();

        $access_token = session('tokenrs');
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        try {
            $response = $client->request('POST', 'laporancovid19versi3', [
                'headers' => [
                    'Authorization' => "Bearer {$access_token}"
                ],
                'json' => [
                    'kewarganegaraanId' => $request->kewarganegaraan,
                    'nik' => $request->nik,
                    'noPassport' => $request->noPassport,
                    'asalPasienId' => $request->asal,
                    'noRM' => $request->noRM,
                    'namaLengkapPasien' => $request->namaPasien,
                    'namaInisialPasien' => $request->inisial,
                    'tanggalLahir' => $request->tgl_lahir,
                    'email' => $request->email,
                    'noTelp' => $request->nohp,
                    'jenisKelaminId' => $request->jk,
                    'domisiliKecamatanId' => $request->kecamatan,
                    'domisiliKabKotaId' => $request->kabKota,
                    'domisiliProvinsiId' => $request->provinsi,
                    'pekerjaanId' => $request->pekerjaan,
                    'tanggalMasuk' => $request->tgl_masuk,
                    'jenisPasienId' => $request->jenis_pasien,
                    'varianCovidId' => $request->varian_covid,
                    'statusPasienId' => $request->status_pasien,
                    'statusCoInsidenId' => $request->status_coinsiden,
                    'statusRawatId' => $request->status_rawat,
                    'alatOksigenId' => $request->alat_oksigen,
                    'penyintasId' => $request->penyintas,
                    'tanggalOnsetGejala' => $request->tgl_gejala,
                    'kelompokGejalaId' => $request->kelompok_gejala,
                    'gejala' => [
                        'demamId' => $request->demam,
                        'batukId' => $request->batuk,
                        'pilekId' => $request->pilek,
                        'sakitTenggorokanId' => $request->sakit_tenggorokan,
                        'sesakNapasId' => $request->sesak_napas,
                        'lemasId' => $request->lemas,
                        'nyeriOtotId' => $request->nyeri_otot,
                        'mualMuntahId' => $request->mual_muntah,
                        'diareId' => $request->diare,
                        'anosmiaId' => $request->anosmia,
                        'napasCepatId' => $request->napas_cepat,
                        'frekNapas30KaliPerMenitId' => $request->frek_napas,
                        'distresPernapasanBeratId' => $request->distres_pernapasan,
                        'lainnyaId' => $request->lainnya,
                    ]
                ]
            ]);
        } catch (ClientException $e) {
            // echo $e->getRequest();
            // echo $e->getResponse();
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode((string) $response->getBody());
                // dd($test->message);
            }
            // $data = $e->getResponse();
            // $responseBodyAsString = $data->getBody()->getContents('message');
            // $data = json_decode($data);
            // dd($responseBodyAsString);

            $id = Crypt::encrypt($request->noRawat);
            Session::flash('error', $test->message);

            return redirect("/rsonline/pasienbaru/add/$id")->withInput();
        }

        // dd($response);

        $data = json_decode($response->getBody());
        // $data =  $response->json();
        // $data = $data->data;

        // dd($data);
        if ($data->status == "true") {
            $berhasil = new PelaporanCovid();

            $berhasil->lapId = $data->data->id;
            $berhasil->noRawat = $request->noRawat;
            $berhasil->noRm = $request->noRM;
            $berhasil->namaPasien = $request->namaPasien;
            $berhasil->tgl_lahir = $request->tgl_lahir;
            $berhasil->kewarganegaraan = $request->kewarganegaraan;
            $berhasil->nik = $request->nik;
            $berhasil->noPassport = $request->noPassport;
            $berhasil->asalPasien = $request->asal;
            $berhasil->asalPasien = $request->asal;
            $berhasil->inisial = $request->inisial;
            $berhasil->email = $request->email;
            $berhasil->nohp = $request->nohp;
            $berhasil->jk = $request->jk;
            $berhasil->provinsi = $request->provinsi;
            $berhasil->kabKota = $request->kabKota;
            $berhasil->kecamatan = $request->kecamatan;
            $berhasil->tgl_masuk = $request->tgl_masuk;
            $berhasil->pekerjaan = $request->pekerjaan;
            $berhasil->jenis_pasien = $request->jenis_pasien;
            $berhasil->varian_covid = $request->varian_covid;
            $berhasil->status_pasien = $request->status_pasien;
            $berhasil->status_coinsiden = $request->status_coinsiden;
            $berhasil->status_rawat = $request->status_rawat;
            $berhasil->alat_oksigen = $request->alat_oksigen;
            $berhasil->penyintas = $request->penyintas;
            $berhasil->tgl_gejala = $request->tgl_gejala;
            $berhasil->kelompok_gejala = $request->kelompok_gejala;
            $berhasil->demam = $request->demam;
            $berhasil->batuk = $request->batuk;
            $berhasil->pilek = $request->pilek;
            $berhasil->sakit_tenggorokan = $request->sakit_tenggorokan;
            $berhasil->sesak_napas = $request->sesak_napas;
            $berhasil->lemas = $request->lemas;
            $berhasil->nyeri_otot = $request->nyeri_otot;
            $berhasil->mual_muntah = $request->mual_muntah;
            $berhasil->diare = $request->diare;
            $berhasil->anosmia = $request->anosmia;
            $berhasil->napas_cepat = $request->napas_cepat;
            $berhasil->distres_pernapasan = $request->distres_pernapasan;
            $berhasil->frek_napas = $request->frek_napas;
            $berhasil->lainnya = $request->lainnya;
            $berhasil->status_pulang = false;
            $berhasil->save();
        }

        Session::flash('sukses', $data->message . ', No Id Pelaporan : ' . $data->data->id);
        return redirect('/rsonline/pasienbaru');
    }

    public function terlapor()
    {
        session()->put('ibu', 'RS Online');
        session()->put('anak', 'Data Pasien');
        session()->put('cucu', 'Pasien Terlapor');

        $data = PelaporanCovid::where('status_pulang', false)
            ->orderBy('created_at', 'DESC')->get();

        return view('data_rsonline.pasien_terlapor', compact('data'));
    }

    public function keluar()
    {
        session()->put('ibu', 'RS Online');
        session()->put('anak', 'Data Pasien');
        session()->put('cucu', 'Pasien Keluar');

        $data = PelaporanCovid::where('status_pulang', true)
            ->orderBy('created_at', 'DESC')->get();

        // dd($data);

        return view('data_rsonline.pasien_pulang', compact('data'));
    }

    public function editlap($id)
    {
        $lapId = Crypt::decrypt($id);

        // dd($lapId);

        $data = PelaporanCovid::where('lapId', $lapId)->first();
        $kewarganegaraan = RsClientController::kewarganegaraan();
        $dataasal = RsClientController::asalpasien();
        $datapekerjaan = RsClientController::pekerjaan();
        $datajenis = RsClientController::jenispasien();
        $datavarian = RsClientController::variancovid();
        $statuspasien = RsClientController::statuspasien();
        $statusrawat = RsClientController::statusrawat();
        $alatoksigen = RsClientController::alatoksigen();
        $datakelompok = RsClientController::kelompokgejala();
        $provinsi = Provinsi::all();
        // dd($data);

        return view('data_rsonline.pasien_online_edit', compact(
            'data',
            'kewarganegaraan',
            'dataasal',
            'datapekerjaan',
            'datajenis',
            'datavarian',
            'statuspasien',
            'statusrawat',
            'alatoksigen',
            'datakelompok',
            'provinsi'
        ));
    }

    public function updatelap($id, Request $request)
    {
        RsClientController::tokenrs();

        $access_token = session('tokenrs');
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        try {
            $response = $client->request('PATCH', "laporancovid19versi3/$id", [
                'headers' => [
                    'Authorization' => "Bearer {$access_token}"
                ],
                'json' => [
                    'kewarganegaraanId' => $request->kewarganegaraan,
                    'nik' => $request->nik,
                    'noPassport' => $request->noPassport,
                    'asalPasienId' => $request->asal,
                    'noRM' => $request->noRM,
                    'namaLengkapPasien' => $request->namaPasien,
                    'namaInisialPasien' => $request->inisial,
                    'tanggalLahir' => $request->tgl_lahir,
                    'email' => $request->email,
                    'noTelp' => $request->nohp,
                    'jenisKelaminId' => $request->jk,
                    'domisiliKecamatanId' => $request->kecamatan,
                    'domisiliKabKotaId' => $request->kabKota,
                    'domisiliProvinsiId' => $request->provinsi,
                    'pekerjaanId' => $request->pekerjaan,
                    'tanggalMasuk' => $request->tgl_masuk,
                    // 'statusVaksinasiId' => "1",
                    'jenisPasienId' => $request->jenis_pasien,
                    'varianCovidId' => $request->varian_covid,
                    // 'severityLevelId' => "1",
                    // 'statusKomorbidId'=> "0",
                    'statusPasienId' => $request->status_pasien,
                    'statusCoInsidenId' => $request->status_coinsiden,
                    'statusRawatId' => $request->status_rawat,
                    // 'saturasiOksigen'=> "97",
                    'alatOksigenId' => $request->alat_oksigen,
                    'penyintasId' => $request->penyintas,
                    'tanggalOnsetGejala' => $request->tgl_gejala,
                    'kelompokGejalaId' => $request->kelompok_gejala,
                    'gejala' => [
                        'demamId' => $request->demam,
                        'batukId' => $request->batuk,
                        'pilekId' => $request->pilek,
                        'sakitTenggorokanId' => $request->sakit_tenggorokan,
                        'sesakNapasId' => $request->sesak_napas,
                        'lemasId' => $request->lemas,
                        'nyeriOtotId' => $request->nyeri_otot,
                        'mualMuntahId' => $request->mual_muntah,
                        'diareId' => $request->diare,
                        'anosmiaId' => $request->anosmia,
                        'napasCepatId' => $request->napas_cepat,
                        'frekNapas30KaliPerMenitId' => $request->frek_napas,
                        'distresPernapasanBeratId' => $request->distres_pernapasan,
                        'lainnyaId' => $request->lainnya,
                    ]
                ]
            ]);
        } catch (ClientException $e) {

            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode((string) $response->getBody());
            }

            $id = Crypt::encrypt($id);
            Session::flash('error', $test->message);

            return redirect("/rsonline/pasienterlapor/editlap/$id")->withInput();
        }

        $data = json_decode($response->getBody());

        if ($data->status == "true") {
            $berhasil = PelaporanCovid::where('lapId', $id)->first();

            $berhasil->lapId = $data->data->id;
            $berhasil->noRawat = $request->noRawat;
            $berhasil->noRm = $request->noRM;
            $berhasil->namaPasien = $request->namaPasien;
            $berhasil->tgl_lahir = $request->tgl_lahir;
            $berhasil->kewarganegaraan = $request->kewarganegaraan;
            $berhasil->nik = $request->nik;
            $berhasil->noPassport = $request->noPassport;
            $berhasil->asalPasien = $request->asal;
            $berhasil->asalPasien = $request->asal;
            $berhasil->inisial = $request->inisial;
            $berhasil->email = $request->email;
            $berhasil->nohp = $request->nohp;
            $berhasil->jk = $request->jk;
            $berhasil->provinsi = $request->provinsi;
            $berhasil->kabKota = $request->kabKota;
            $berhasil->kecamatan = $request->kecamatan;
            $berhasil->tgl_masuk = $request->tgl_masuk;
            $berhasil->pekerjaan = $request->pekerjaan;
            $berhasil->jenis_pasien = $request->jenis_pasien;
            $berhasil->varian_covid = $request->varian_covid;
            $berhasil->status_pasien = $request->status_pasien;
            $berhasil->status_coinsiden = $request->status_coinsiden;
            $berhasil->status_rawat = $request->status_rawat;
            $berhasil->alat_oksigen = $request->alat_oksigen;
            $berhasil->penyintas = $request->penyintas;
            $berhasil->tgl_gejala = $request->tgl_gejala;
            $berhasil->kelompok_gejala = $request->kelompok_gejala;
            $berhasil->demam = $request->demam;
            $berhasil->batuk = $request->batuk;
            $berhasil->pilek = $request->pilek;
            $berhasil->sakit_tenggorokan = $request->sakit_tenggorokan;
            $berhasil->sesak_napas = $request->sesak_napas;
            $berhasil->lemas = $request->lemas;
            $berhasil->nyeri_otot = $request->nyeri_otot;
            $berhasil->mual_muntah = $request->mual_muntah;
            $berhasil->diare = $request->diare;
            $berhasil->anosmia = $request->anosmia;
            $berhasil->napas_cepat = $request->napas_cepat;
            $berhasil->distres_pernapasan = $request->distres_pernapasan;
            $berhasil->frek_napas = $request->frek_napas;
            $berhasil->lainnya = $request->lainnya;
            $berhasil->status_pulang = false;
            $berhasil->save();
        }

        Session::flash('sukses', $data->message . ', No Id Pelaporan : ' . $data->data->id);
        return redirect('/rsonline/pasienterlapor');
    }

    public function lapTambahan($id)
    {
        $id = Crypt::decrypt($id);

        // dd($id);

        // $data = KomorbidLap::where('lapId', $id)->first();
        $pasien = PelaporanCovid::where('lapId', $id)->first();

        $diagnosa = PasienOnlineController::getDiagnosa($pasien->noRawat);
        $komorbid = RsClientController::komorbid();
        $terapi = RsClientController::terapi();
        $dosisvaksin = RsClientController::dosisvaksin();
        $jenisvaksin = RsClientController::jenisvaksin();
        $lab = RsClientController::jenispemeriksaanlab();

        // dd($diagnosa);

        return view('data_rsonline.laporan_tambahan', compact(
            'komorbid',
            'pasien',
            'terapi',
            'dosisvaksin',
            'jenisvaksin',
            'lab',
            'diagnosa'
        ));
    }

    public function diagnosaUpdate($id, Request $request)
    {
        RsClientController::tokenrs();
        $access_token = session('tokenrs');

        // $cek = KomorbidLap::where('lapId', $id)->first();
        // $split = explode('-', $request->diagnosa);
        try {
            $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
            $response = $client->request('POST', "laporancovid19versi3diagnosa", [
                'headers' => [
                    'Authorization' => "Bearer {$access_token}"
                ],
                'json' => [
                    'laporanCovid19Versi3Id' => $id,
                    'diagnosaLevelId' => $request->levelDiagnosa,
                    'diagnosaId' => $request->kd_diagnosa,
                ]
            ]);
        } catch (ClientException $e) {

            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode((string) $response->getBody());
            }

            $id = Crypt::encrypt($id);
            Session::flash('error', $test->message);

            return redirect("/rsonline/pasienterlapor/laptambahan/$id")->withInput();
        }

        $data = json_decode($response->getBody());

        if ($data->message == 'data inserted successfully') {
            $baru = new DiagnosaLap();
            $baru->lapId = $id;
            $baru->lapDiagnosaId = $data->data->id;
            $baru->diagnosaLevelId = $request->levelDiagnosa;
            $baru->diagnosaId = $request->kd_diagnosa;
            $baru->namaDiagnosa = $request->nama_diagnosa;
            $baru->save();

            Session::flash('sukses', $data->message);

            $id = Crypt::encrypt($id);

            return redirect("/rsonline/pasienterlapor/laptambahan/$id");
        } else {
            Session::flash('error', $data->message);

            return redirect()->back()->withInput();
        }
    }

    public function komorbidUpdate($id, Request $request)
    {
        RsClientController::tokenrs();
        $access_token = session('tokenrs');

        // $cek = KomorbidLap::where('lapId', $id)->first();
        $split = explode('-', $request->komorbid);

        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('POST', "laporancovid19versi3komorbid", [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ],
            'json' => [
                'laporanCovid19Versi3Id' => $id,
                'komorbidId' => $split[0],
            ]
        ]);

        $data = json_decode($response->getBody());

        if ($data->message == 'data inserted successfully') {
            $baru = new KomorbidLap();
            $baru->lapId = $id;
            $baru->lapKomorbidId = $data->data->id;
            $baru->komorbidId = $split[0];
            $baru->desc = $split[1];
            $baru->save();

            Session::flash('sukses', $data->message);

            $id = Crypt::encrypt($id);

            return redirect("/rsonline/pasienterlapor/laptambahan/$id");
        } else {
            Session::flash('error', $data->message);

            return redirect()->back()->withInput();
        }
    }

    public function komorbidEdit($id)
    {
        $id = Crypt::decrypt($id);

        $data = KomorbidLap::where('lapKomorbidId', $id)->first();
        $komorbid = RsClientController::komorbid();

        return view('data_rsonline.komorbid_edit', compact('data', 'komorbid'));
    }

    public function komorbidPatch($id, Request $request)
    {
        // $id = Crypt::decrypt($id);

        // dd($id);

        RsClientController::tokenrs();
        $access_token = session('tokenrs');

        $komorbid = explode('-', $request->komorbid);

        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('PATCH', "laporancovid19versi3komorbid/$id", [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ],
            'json' => [
                'komorbidId' => $komorbid[0],
            ]
        ]);

        $data = json_decode($response->getBody());

        if ($data->message == 'data updated successfully') {
            // dd($dosis, $jenis);
            $update = KomorbidLap::where('lapKomorbidId', $id)->first();

            $update->komorbidId = $komorbid[0];
            $update->desc = $komorbid[1];
            $update->save();

            Session::flash('sukses', $data->message);

            $id = Crypt::encrypt($update->lapId);

            return redirect("/rsonline/pasienterlapor/laptambahan/$id");
        } else {
            Session::flash('error', $data->message);

            return redirect()->back()->withInput();
        }
    }

    public function terapiUpdate($id, Request $request)
    {
        RsClientController::tokenrs();
        $access_token = session('tokenrs');

        // $cek = TerapiLap::where('lapId', $id)->first();
        $split = explode('-', $request->terapi);

        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('POST', "laporancovid19versi3terapi", [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ],
            'json' => [
                'laporanCovid19Versi3Id' => $id,
                'terapiId' => $split[0],
                'jumlahTerapi' => $request->jumlah,
            ]
        ]);

        $data = json_decode($response->getBody());

        if ($data->message == 'data inserted successfully') {
            $baru = new TerapiLap();
            $baru->lapId = $id;
            $baru->lapTerapiId = $data->data->id;
            $baru->terapiId = $split[0];
            $baru->desc = $split[1];
            $baru->jumlah = $request->jumlah;
            $baru->save();

            Session::flash('sukses', $data->message);

            $id = Crypt::encrypt($id);

            return redirect("/rsonline/pasienterlapor/laptambahan/$id");
        } else {
            Session::flash('error', $data->message);

            return redirect()->back()->withInput();
        }
    }

    public function terapiEdit($id)
    {
        $id = Crypt::decrypt($id);

        $data = TerapiLap::where('lapTerapiId', $id)->first();
        $terapi = RsClientController::terapi();

        return view('data_rsonline.terapi_edit', compact(
            'data',
            'terapi',
        ));
    }

    public function terapiPatch($id, Request $request)
    {
        // $id = Crypt::decrypt($id);

        // dd($id);

        RsClientController::tokenrs();
        $access_token = session('tokenrs');

        $terapi = explode('-', $request->terapi);

        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('PATCH', "laporancovid19versi3terapi/$id", [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ],
            'json' => [
                'terapiId' => $terapi[0],
                'jumlahTerapi' => $request->jumlah,
            ]
        ]);

        $data = json_decode($response->getBody());

        if ($data->message == 'data updated successfully') {
            // dd($dosis, $jenis);
            $update = TerapiLap::where('lapTerapiId', $id)->first();
            $update->terapiId = $terapi[0];
            $update->desc = $terapi[1];
            $update->jumlah = $request->jumlah;
            $update->save();

            Session::flash('sukses', $data->message);

            $id = Crypt::encrypt($update->lapId);

            return redirect("/rsonline/pasienterlapor/laptambahan/$id");
        } else {
            Session::flash('error', $data->message);

            return redirect()->back()->withInput();
        }
    }

    public function vaksinasiUpdate($id, Request $request)
    {
        RsClientController::tokenrs();
        $access_token = session('tokenrs');

        $cek = VaksinasiLap::where('lapId', $id)->first();
        $dosis = explode('_', $request->dosisVaksin);
        $jenis = explode('_', $request->jenisVaksin);

        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('POST', "laporancovid19versi3vaksinasi", [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ],
            'json' => [
                'laporanCovid19Versi3Id' => $id,
                'dosisVaksinId' => $dosis[0],
                'jenisVaksinId' => $jenis[0],
            ]
        ]);

        $data = json_decode($response->getBody());

        if ($data->message == 'data inserted successfully') {
            $baru = new VaksinasiLap();
            $baru->lapId = $id;
            $baru->lapVaksinId = $data->data->id;
            $baru->dosisVaksinId = $dosis[0];
            $baru->namaDosis = $dosis[1];
            $baru->jenisVaksinId = $jenis[0];
            $baru->namaVaksin = $jenis[1];
            $baru->save();

            Session::flash('sukses', $data->message);

            $id = Crypt::encrypt($id);

            return redirect("/rsonline/pasienterlapor/laptambahan/$id");
        } else {
            Session::flash('error', $data->message);

            return redirect()->back()->withInput();
        }
    }

    public function vaksinEdit($id)
    {
        $id = Crypt::decrypt($id);

        $data = VaksinasiLap::where('lapVaksinId', $id)->first();
        $dosisvaksin = RsClientController::dosisvaksin();
        $jenisvaksin = RsClientController::jenisvaksin();

        return view('data_rsonline.vaksin_edit', compact(
            'data',
            'dosisvaksin',
            'jenisvaksin'
        ));
    }

    public function vaksinPatch($id, Request $request)
    {
        // $id = Crypt::decrypt($id);

        // dd($id);

        RsClientController::tokenrs();
        $access_token = session('tokenrs');

        $dosis = explode('_', $request->dosisVaksin);
        $jenis = explode('_', $request->jenisVaksin);



        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('PATCH', "laporancovid19versi3vaksinasi/$id", [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ],
            'json' => [
                'dosisVaksinId' => $dosis[0],
                'jenisVaksinId' => $jenis[0],
            ]
        ]);

        $data = json_decode($response->getBody());

        if ($data->message == 'data updated successfully') {
            // dd($dosis, $jenis);
            $update = VaksinasiLap::where('lapVaksinId', $id)->first();

            $update->dosisVaksinId = $dosis[0];
            $update->namaDosis = $dosis[1];
            $update->jenisVaksinId = $jenis[0];
            $update->namaVaksin = $jenis[1];
            $update->save();

            Session::flash('sukses', $data->message);

            $id = Crypt::encrypt($update->lapId);

            return redirect("/rsonline/pasienterlapor/laptambahan/$id");
        } else {
            Session::flash('error', $data->message);

            return redirect()->back()->withInput();
        }
    }

    public function labUpdate($id, Request $request)
    {
        RsClientController::tokenrs();
        $access_token = session('tokenrs');

        $cek = PemeriksaanLab::where('lapId', $id)->first();

        $jenis = explode('-', $request->jenisPemeriksaan);

        // dd($cek);

        // if (empty($cek)) {
        //add data
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('POST', "laporancovid19versi3pemeriksaanlab", [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ],
            'json' => [
                'laporanCovid19Versi3Id' => $id,
                'jenisPemeriksaanLabId' => $jenis[0],
                'hasilPemeriksaanLabId' => $request->hasilpemeriksaan,
                'tanggalHasilPemeriksaanLab' => $request->tgl_hasil,
            ]
        ]);

        $data = json_decode($response->getBody());

        if ($data->message == 'data inserted successfully') {
            $baru = new PemeriksaanLab();
            $baru->lapId = $id;
            $baru->lapPemeriksaanId = $data->data->id;
            $baru->jenisPemeriksaanId = $jenis[0];
            $baru->namapemeriksaan = $jenis[1];
            $baru->hasilPemeriksaanId = $request->hasilpemeriksaan;
            $baru->tgl_hasil = $request->tgl_hasil;
            $baru->save();

            Session::flash('sukses', $data->message);

            $id = Crypt::encrypt($id);

            return redirect("/rsonline/pasienterlapor/laptambahan/$id");
        } else {
            Session::flash('error', $data->message);

            return redirect()->back()->withInput();
        }
    }

    public function pulang($id)
    {
        $id = Crypt::decrypt($id);

        $data = PelaporanCovid::where('lapId', $id)->first();

        $datakeluar = RsClientController::statuskeluar();
        $datapenyebabkematian = RsClientController::penyebabkematian();
        $datapenyebabkematianlangsung = RsClientController::penyebabkematianlangsung();
        $datapasiensaatmeninggal = RsClientController::statuspasiensaatmeninggal();
        $datakomorbidcoinsiden = RsClientController::komorbidcoinsiden();

        // dd($datakeluar, $datapenyebabkematian, $datapenyebabkematianlangsung, $datapasiensaatmeninggal, $datakomorbidcoinsiden);
        // dd($data);

        return view('data_rsonline.pulang', compact(
            'data',
            'datakeluar',
            'datapenyebabkematian',
            'datapenyebabkematianlangsung',
            'datapasiensaatmeninggal',
            'datakomorbidcoinsiden',
        ));
    }

    public function pulangupdate($id, Request $request)
    {
        RsClientController::tokenrs();

        $statusKeluar = explode('_', $request->statusKeluar);
        $penyebabKematian = explode('_', $request->penyebabKematian);
        $penyebabKematianLangsung = explode('_', $request->penyebabKematianLangsung);
        $statusPasienMeninggal = explode('_', $request->statusPasienMeninggal);
        $komorbidCoinsiden = explode('_', $request->komorbidCoinsiden);

        $access_token = session('tokenrs');
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        try {
            $response = $client->request('POST', "laporancovid19versi3statuskeluar", [
                'headers' => [
                    'Authorization' => "Bearer {$access_token}"
                ],
                'json' => [
                    'laporanCovid19Versi3Id' => $id,
                    'tanggalKeluar' => $request->tgl_keluar,
                    'statusKeluarId' => $statusKeluar[0],
                    'penyebabKematianId' => $penyebabKematian[0],
                    'penyebabKematianLangsungId' => $penyebabKematianLangsung[0],
                    'statusPasienSaatMeninggalId' => $statusPasienMeninggal[0],
                    'komorbidCoInsidenId' => $komorbidCoinsiden[0],
                ]
            ]);
        } catch (ClientException $e) {

            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode((string) $response->getBody());
            }

            $id = Crypt::encrypt($id);
            Session::flash('error', $test->message);

            return redirect("/rsonline/pasienterlapor/pulang/$id")->withInput();
        }

        $data = json_decode($response->getBody());
        // $data =  $response->json();
        // $data = $data->data;

        // dd($data);
        if ($data->status == "true") {
            $berhasil = PelaporanCovid::where('lapId', $id)->first();
            $berhasil->status_pulang = True;
            $berhasil->save();

            $tambah = new Pulang();
            $tambah->lapId = $id;
            $tambah->lapPulangId = $data->data->id;
            $tambah->statusPulangId = $statusKeluar[0];
            $tambah->statusPulang = $statusKeluar[1];
            $tambah->penyebabKematianId = $penyebabKematian[0];
            if (!empty($penyebabKematian[1])) {
                $tambah->penyebabKematian = $penyebabKematian[1];
            }
            $tambah->penyebabKematianLangsungId = $penyebabKematianLangsung[0];
            if (!empty($penyebabKematianLangsung[1])) {
                $tambah->penyebabKematianLangsung = $penyebabKematianLangsung[1];
            }
            $tambah->statusPasienMeninggalId = $statusPasienMeninggal[0];
            if (!empty($statusPasienMeninggal[1])) {
                $tambah->statusPasienMeninggal = $statusPasienMeninggal[1];
            }
            $tambah->komorbidCoinsidenId = $komorbidCoinsiden[0];
            if (!empty($komorbidCoinsiden[1])) {
                $tambah->komorbidCoinsiden = $komorbidCoinsiden[1];
            }
            $tambah->tgl_pulang = $request->tgl_keluar;
            $tambah->save();
        }

        Session::flash('sukses', $data->message . ', No Id Pelaporan : ' . $data->data->id);
        return redirect('/rsonline/pasienterlapor');
    }

    public static function Inisial($nama)
    {
        $split_nama = explode(' ', $nama);
        $singkatan = '';
        foreach ($split_nama as $kata) {
            $singkatan .= substr($kata, 0, 1);
        }

        return $singkatan;
    }

    // Fetch records axios
    public function getKabKota(Request $request)
    {

        // Fetch Employees by Departmentid
        $KabKota = KabKota::where('provinsi_id', $request->get('id'))
            ->pluck('nama', 'id');

        return response()->json($KabKota);
    }

    public function getKecamatan(Request $request)
    {

        // Fetch Employees by Departmentid
        $Kecamatan = Kecamatan::where('kab_kota_id', $request->get('id'))
            ->pluck('nama', 'id');

        return response()->json($Kecamatan);
    }

    public function getDiagnosa($id)
    {

        $data = DB::connection('mysqlkhanza')->table('diagnosa_pasien')
            ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'diagnosa_pasien.no_rawat')
            ->join('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
            ->select(
                // 'kamar_inap.tgl_masuk',
                // 'kamar_inap.tgl_keluar',
                // 'kamar_inap.diagnosa_awal',
                // 'kamar_inap.diagnosa_akhir',
                // 'kamar_inap.stts_pulang',
                'kamar_inap.no_rawat',
                'diagnosa_pasien.kd_penyakit',
                'diagnosa_pasien.prioritas',
                'penyakit.nm_penyakit'
            )
            ->where('kamar_inap.no_rawat', $id)
            ->get();

        return $data;
    }
}
