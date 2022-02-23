<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KesehatanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        session()->put('ibu', 'Data Transaksi');
        session()->put('anak', 'Layanan Kesehatan');

        //data Inap Tanggal sesuai tanggal
        $inap = KesehatanController::ranap('2022-01-03');
        $operasi = KesehatanController::operasi('2022-01-03');
        $radiologi = KesehatanController::radiologi('2022-01-03');
        $rajal = KesehatanController::rajal('2022-01-03');
        $rajalpoli = KesehatanController::rajalpoli('2022-01-03');
        $bpjs = KesehatanController::bpjs('2022-01-03');
        $nonbpjs = KesehatanController::nonbpjs('2022-01-03');
        $labsample = KesehatanController::labsample('2022-01-03');
        $labparameter = KesehatanController::labparameter('2022-01-03');
        // dd($operasi);

        return view('layanan_kesehatan', compact('inap', 'operasi', 'radiologi', 'rajal', 'rajalpoli', 'bpjs', 'nonbpjs', 'labsample', 'labparameter'));
    }

    public function cari(Request $request)
    {
        $tanggal = $request->get('tanggal');
        //data Inap Tanggal sesuai tanggal
        $inap = KesehatanController::ranap($tanggal);
        $operasi = KesehatanController::operasi($tanggal);
        $radiologi = KesehatanController::radiologi($tanggal);
        $rajal = KesehatanController::rajal($tanggal);
        $rajalpoli = KesehatanController::rajalpoli($tanggal);
        $bpjs = KesehatanController::bpjs($tanggal);
        $nonbpjs = KesehatanController::nonbpjs($tanggal);
        $labsample = KesehatanController::labsample($tanggal);
        $labparameter = KesehatanController::labparameter($tanggal);
        // dd($operasi);

        return view('layanan_kesehatan', compact('inap', 'operasi', 'radiologi', 'rajal', 'rajalpoli', 'bpjs', 'nonbpjs', 'labsample', 'labparameter'));
    }

    public static function ranap($tanggal)
    {
        // $tanggal = Carbon::now()->format('Y-m-d');

        $data = DB::connection('mysqlkhanza')->table('kamar_inap')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
            ->join('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
            ->select('kamar_inap.no_rawat', 'kamar_inap.tgl_masuk', 'kamar_inap.tgl_keluar', 'kamar.kelas')
            ->whereDate('kamar_inap.tgl_keluar', '>=', $tanggal) //Nanti Tanggal dibikin dinamis sesuai tanggal hari ini
            ->whereDate('kamar_inap.tgl_masuk', '<=', $tanggal)
            ->get();

        // dd($data);
        $lamakelas1 = $lamakelas2 = $lamakelas3 = $nonkelas = 0;
        $pasien1 = $pasien2 = $pasien3 = 0;

        foreach ($data as $data) {
            if ($data->kelas == 'Kelas 1') {
                // $lamakelas1 = $lamakelas1 + $data->lama;
                $pasien1++;
            } elseif ($data->kelas == 'Kelas 2') {
                // $lamakelas2 = $lamakelas2 + $data->lama;
                $pasien2++;
            } elseif ($data->kelas == 'Kelas 3') {
                // $lamakelas3 = $lamakelas3 + $data->lama;
                $pasien3++;
            } else {
                $nonkelas = $nonkelas + $data->lama;
            }
        }

        // dd($lamakelas1, $pasien1, $lamakelas2, $pasien2, $lamakelas3, $pasien3);

        $datainap = [
            [
                'kode_kelas' => '02',
                // 'jml_hari' => $lamakelas1,
                'jumlah' => $pasien1,
                'tgl_transaksi' => $tanggal
            ],
            [
                'kode_kelas' => '03',
                // 'jml_hari' => $lamakelas2,
                'jumlah' => $pasien2,
                'tgl_transaksi' => $tanggal
            ],
            [
                'kode_kelas' => '04',
                // 'jml_hari' => $lamakelas3,
                'jumlah' => $pasien3,
                'tgl_transaksi' => $tanggal
            ]
        ];
        $inap = json_decode(json_encode($datainap));

        return $inap;
    }

    public static function labsample($tanggal)
    {
        $data = DB::connection('mysqlkhanza')->table('permintaan_lab')
            ->select('permintaan_lab.no_rawat', 'permintaan_lab.no_order', 'permintaan_lab.tgl_sampel')
            ->whereDate('permintaan_lab.tgl_sampel', '=', $tanggal)
            ->count();

        // dd($data);

        $arrayoperasi = [
            'jumlah' => $data,
            'tgl_transaksi' => $tanggal
        ];

        $lab = json_decode(json_encode($arrayoperasi));

        return $lab;
    }

    public static function labparameter($tanggal)
    {
        $data = DB::connection('mysqlkhanza')->table('jns_perawatan_lab')
            ->select('jns_perawatan_lab.nm_perawatan')
            ->groupBy('jns_perawatan_lab.nm_perawatan')
            ->orderBy('jns_perawatan_lab.nm_perawatan', 'asc')
            ->get();

        // dd($data);

        $i = 0;
        $nama_pelayanan = '';

        foreach ($data as $data) {
            $jumlahperawatan = DB::connection('mysqlkhanza')->table('detail_periksa_lab')
                ->join('jns_perawatan_lab', 'jns_perawatan_lab.kd_jenis_prw', '=', 'detail_periksa_lab.kd_jenis_prw')
                ->select('detail_periksa_lab.no_rawat', 'detail_periksa_lab.tgl_periksa', 'jns_perawatan_lab.nm_perawatan')
                // ->where('reg_periksa.status_lanjut', '=', 'Ralan')
                ->where('jns_perawatan_lab.nm_perawatan', '=', $data->nm_perawatan)
                ->whereDate('detail_periksa_lab.tgl_periksa', '=', $tanggal)
                ->orderBy('jns_perawatan_lab.nm_perawatan', 'asc')
                ->count();
            // dd($jumlahperawatan);

            if (($jumlahperawatan > 0) and ($nama_pelayanan == $data->nm_perawatan)) {
                $array[$i] = [
                    'jumlah' => $jumlahperawatan,
                    'tgl_transaksi' => $tanggal,
                    'nama_layanan' => $data->nm_perawatan,
                ];

                $nama_pelayanan = $data->nm_perawatan;
            } elseif (($jumlahperawatan > 0) and ($nama_pelayanan != $data->nm_perawatan)) {
                $array[$i] = [
                    'jumlah' => $jumlahperawatan,
                    'tgl_transaksi' => $tanggal,
                    'nama_layanan' => $data->nm_perawatan,
                ];

                $i++;
            }
        }

        if (!empty($array)) {
            $lab = json_decode(json_encode($array));
        } else {
            $lab = null;
        }


        // dd($lab);

        return $lab;
    }

    public static function operasi($tanggal)
    {
        $data = DB::connection('mysqlkhanza')->table('operasi')
            ->select('operasi.no_rawat', 'operasi.tgl_operasi', 'operasi.jenis_anasthesi')
            ->whereDate('operasi.tgl_operasi', '=', $tanggal)
            ->count();

        // dd($data);

        $arrayoperasi = [
            'jumlah' => $data,
            'tgl_transaksi' => $tanggal
        ];
        $operasi = json_decode(json_encode($arrayoperasi));

        // dd($operasi);

        return $operasi;
    }

    public static function radiologi($tanggal)
    {
        $data = DB::connection('mysqlkhanza')->table('periksa_radiologi')
            ->select('periksa_radiologi.no_rawat', 'periksa_radiologi.tgl_periksa', 'periksa_radiologi.status')
            ->whereDate('periksa_radiologi.tgl_periksa', '=', $tanggal)
            ->count();

        // dd($data);

        $array = [
            'jumlah' => $data,
            'tgl_transaksi' => $tanggal
        ];
        $radiologi = json_decode(json_encode($array));

        // dd($operasi);

        return $radiologi;
    }

    public static function rajal($tanggal)
    {
        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            // ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'operasi.no_rawat')
            // ->join('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
            ->select('reg_periksa.no_rawat', 'reg_periksa.tgl_registrasi', 'reg_periksa.kd_poli', 'reg_periksa.status_lanjut')
            ->where('reg_periksa.status_lanjut', '=', 'Ralan')
            ->whereDate('reg_periksa.tgl_registrasi', '=', $tanggal)
            ->count();

        $array = [
            'jumlah' => $data,
            'tgl_transaksi' => $tanggal
        ];
        $rajal = json_decode(json_encode($array));

        return $rajal;
    }

    public static function rajalpoli($tanggal)
    {
        $data = DB::connection('mysqlkhanza')->table('poliklinik')
            ->select('poliklinik.kd_poli', 'poliklinik.nm_poli', 'poliklinik.status')
            ->where('poliklinik.status', '=', '1')
            ->orderBy('poliklinik.kd_poli', 'asc')
            ->get();

        // dd($data);

        $i = 0;

        foreach ($data as $poli) {
            $jumlahpoli = DB::connection('mysqlkhanza')->table('reg_periksa')
                ->select('reg_periksa.no_rawat', 'reg_periksa.tgl_registrasi', 'reg_periksa.kd_poli', 'reg_periksa.status_lanjut')
                ->where('reg_periksa.status_lanjut', '=', 'Ralan')
                ->where('reg_periksa.kd_poli', '=', $poli->kd_poli)
                ->whereDate('reg_periksa.tgl_registrasi', '=', $tanggal)
                ->orderBy('reg_periksa.kd_poli', 'asc')
                ->count();

            $array[$i] = [
                'jumlah' => $jumlahpoli,
                'tgl_transaksi' => $tanggal,
                'nama_poli' => $poli->nm_poli,
            ];

            $i++;
        }

        $rajalpoli = json_decode(json_encode($array));

        // dd($rajal);

        return $rajalpoli;
    }

    public static function bpjs($tanggal)
    {
        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            // ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'operasi.no_rawat')
            // ->join('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
            ->select('reg_periksa.no_rawat', 'reg_periksa.tgl_registrasi', 'reg_periksa.kd_poli', 'reg_periksa.kd_pj')
            ->where('reg_periksa.kd_pj', '=', 'BPJ')
            ->whereDate('reg_periksa.tgl_registrasi', '=', $tanggal)
            ->count();

        $array = [
            'jumlah' => $data,
            'tgl_transaksi' => $tanggal
        ];
        $bpjs = json_decode(json_encode($array));

        return $bpjs;
    }

    public static function nonbpjs($tanggal)
    {
        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            // ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'operasi.no_rawat')
            // ->join('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
            ->select('reg_periksa.no_rawat', 'reg_periksa.tgl_registrasi', 'reg_periksa.kd_poli', 'reg_periksa.kd_pj')
            ->where('reg_periksa.kd_pj', '<>', 'BPJ')
            ->whereDate('reg_periksa.tgl_registrasi', '=', $tanggal)
            ->count();

        $array = [
            'jumlah' => $data,
            'tgl_transaksi' => $tanggal
        ];
        $nonbpjs = json_decode(json_encode($array));

        return $nonbpjs;
    }
}
