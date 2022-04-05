<?php

namespace App\Http\Controllers;

use App\PelaporanCovid;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Session;

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
            ->where(function ($q) {
                $q->where('kamar_inap.diagnosa_awal', 'like', '%covid%')
                    ->orWhere('kamar_inap.diagnosa_awal', 'like', '%U07.1%')
                    ->orWhere('kamar_inap.diagnosa_awal', 'like', '%U07.2%')
                    ->orWhere('kamar_inap.diagnosa_akhir', 'like', '%covid%');
                // ->orWhere('kamar_inap.diagnosa_akhir', 'like', '%U07.1%')
                // ->orWhere('kamar_inap.diagnosa_akhir', 'like', '%U07.2%');
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
            'datakelompok'
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

            // if($response)
            // Session::flash('sukses', 'Berhasil');

            // return redirect()->back();
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

        $data = PelaporanCovid::orderBy('created_at', 'DESC')->get();

        return view('data_rsonline.pasien_terlapor', compact('data'));
    }

    public function editlap($id)
    {

        $lapId = Crypt::decrypt($id);

        RsClientController::tokenrs();

        $access_token = session('tokenrs');
        $client = new \GuzzleHttp\Client(['base_uri' => session('base_url')]);
        $response = $client->request('GET', "laporancovid19versi3/$lapId", [
            'headers' => [
                'Authorization' => "Bearer {$access_token}"
            ]
        ]);

        $data = json_decode($response->getBody());
        $data = $data->data;

        dd($data);
        return view('data_rsonline.pasien_online_edit', compact('data'));
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
}
