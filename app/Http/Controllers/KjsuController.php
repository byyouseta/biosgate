<?php

namespace App\Http\Controllers;

use App\EpisodeOfCare;
use App\IcdKjsu;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class KjsuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Monitoring KJSU');
        session()->forget('cucu');

        if (empty($request->get('tanggal'))) {
            $tanggal = Carbon::now();
        } else {
            $tanggal = new Carbon($request->get('tanggal'));
        }

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('diagnosa_pasien', 'diagnosa_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.status_lanjut',
                'reg_periksa.stts',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'diagnosa_pasien.kd_penyakit',
                'diagnosa_pasien.status',
                'diagnosa_pasien.prioritas'
            )
            ->where('reg_periksa.tgl_registrasi', $tanggal)
            ->orderBy('reg_periksa.no_rkm_medis', 'ASC')
            ->get();

        $icd_kjsu_data = IcdKjsu::all();
        $icd_kjsu_map = $icd_kjsu_data->pluck('jenis', 'kode_icd')->toArray();

        $dataFilter = [];

        // Proses data dengan loop
        foreach ($data as $listData) {
            $listData = (array) $listData; // Ubah ke array jika objek

            if (isset($icd_kjsu_map[$listData['kd_penyakit']])) {
                $listData['jenis'] = $icd_kjsu_map[$listData['kd_penyakit']];

                // 2. Check data no_rawat
                if ($listData['status'] === 'Ralan') {
                    $encounter = DB::table('response_satu_sehats')
                        ->where('noRawat', $listData['no_rawat'])
                        ->value('encounter_id'); // Ambil encounter_id jika ada
                } elseif ($listData['status'] === 'Ranap') {
                    $encounter = DB::table('response_ranap_satu_sehats')
                        ->where('noRawat', $listData['no_rawat'])
                        ->value('encounter_id'); // Ambil encounter_id jika ada
                } else {
                    $encounter = null; // Default jika tidak ditemukan
                }

                $listData['encounter_id'] = $encounter;
                $dataFilter[] = $listData;
            }
        }

        $dataFilter = json_decode(json_encode($dataFilter));
        // dd($data, $icd_kjsu_map, $dataFilter);

        return view('satu_sehat.summaryKjsu', compact('dataFilter'));
    }

    public function detail($id)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Monitoring KJSU');
        session()->forget('cucu');

        $id = explode('_', Crypt::decrypt($id));

        $dataPasien = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('diagnosa_pasien', 'diagnosa_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.status_lanjut',
                'reg_periksa.stts',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'diagnosa_pasien.kd_penyakit',
                'diagnosa_pasien.status',
                'diagnosa_pasien.prioritas'
            )
            ->where('reg_periksa.no_rawat', $id[0])
            ->first();
        //LogDataCare
        $dataCare = EpisodeOfCare::where('no_rm', $dataPasien->no_rkm_medis)
            ->get();
        //
        //Jenis Kategori
        $dataJenis = IcdKjsu::where('kode_icd', $id[1])
            ->first();
        // dd($data);

        return view('satu_sehat.detailKjsu', compact('dataPasien', 'dataCare', 'dataJenis'));
    }

    public function kirimEoc(Request $request)
    {
        // dd($request);
        $idRS = env('IDRS');

        $simpan = new EpisodeOfCare();
        $simpan->no_rawat = $request->no_rawat;
        $simpan->no_rm = $request->no_rm;
        $simpan->periode = $request->periode;
        $simpan->status = $request->status;
        $simpan->waktu_mulai = $request->tanggal_mulai;
        $simpan->waktu_selesai = $request->tanggal_selesai;
        $simpan->jenis = $request->jenis;
        $simpan->save();

        $eoc_id = EpisodeOfCare::select('no_rm')
            ->groupBy('no_rm')->get();

        dd($eoc_id);

        $json_eoc = [
            "resourceType" => "EpisodeOfCare",
            "identifier" => [
                (object) [
                    "system" => "http://sys-ids.kemkes.go.id/episode-of-care/{{Org_id}}",
                    "value" => "EOC{{EOC_ID}}"
                ]
            ],
            "status" => "waitlist",
            "statusHistory" => [
                (object) [
                    "status" => "waitlist",
                    "period" => (object) [
                        "start" => "2023-11-06T11:00:00+00:00"
                    ]
                ]
            ],
            "type" => [
                (object) [
                    "coding" => [
                        (object) [
                            "system" => "http://terminology.kemkes.go.id/CodeSystem/episodeofcare-type",
                            "code" => "CNC",
                            "display" => "Cancer Management Care "
                        ]
                    ]
                ]
            ],
            "patient" => (object) [
                "reference" => "Patient/{{Patient_Id}}",
                "display" => "{{Patient_Name}}"
            ],
            "managingOrganization" => (object) [
                "reference" => "Organization/{{Org_id}}"
            ],
            "period" => (object) [
                "start" => "2023-11-06T11:00:00+00:00"
            ]
        ];
    }
}
