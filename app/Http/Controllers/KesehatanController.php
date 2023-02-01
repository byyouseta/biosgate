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
        session()->put('ibu', 'BIOS facelift');
        session()->put('anak', 'Data Layanan Kesehatan');
        session()->forget('cucu');

        $tanggal = Carbon::now()->format('Y-m-d');

        //data Inap Tanggal sesuai tanggal
        $inap = KesehatanController::ranap($tanggal);
        $igd = KesehatanController::igd($tanggal);
        $operasi = KesehatanController::operasi($tanggal);
        $radiologi = KesehatanController::radiologi($tanggal);
        $rajal = KesehatanController::rajal($tanggal);
        $rajalpoli = KesehatanController::rajalpoli($tanggal);
        $bpjs = KesehatanController::bpjs($tanggal);
        $nonbpjs = KesehatanController::nonbpjs($tanggal);
        $labsample = KesehatanController::labsample($tanggal);
        $labparameter = KesehatanController::labparameter($tanggal);
        $farmasi = KesehatanController::farmasi($tanggal);
        // dd($operasi);

        return view('bios.layanan_kesehatan', compact(
            'inap',
            'igd',
            'operasi',
            'radiologi',
            'rajal',
            'rajalpoli',
            'bpjs',
            'nonbpjs',
            'farmasi',
            'labsample',
            'labparameter'
        ));
    }

    public function cari(Request $request)
    {
        session()->put('ibu', 'BIOS facelift');
        session()->put('anak', 'Data Layanan Kesehatan');
        session()->forget('cucu');

        $tanggal = $request->get('tanggal');
        //data Inap Tanggal sesuai tanggal
        $inap = KesehatanController::ranap($tanggal);
        $igd = KesehatanController::igd($tanggal);
        $operasi = KesehatanController::operasi($tanggal);
        $radiologi = KesehatanController::radiologi($tanggal);
        $rajal = KesehatanController::rajal($tanggal);
        $rajalpoli = KesehatanController::rajalpoli($tanggal);
        $bpjs = KesehatanController::bpjs($tanggal);
        $nonbpjs = KesehatanController::nonbpjs($tanggal);
        $labsample = KesehatanController::labsample($tanggal);
        $labparameter = KesehatanController::labparameter($tanggal);
        $farmasi = KesehatanController::farmasi($tanggal);
        // dd($operasi);

        return view('bios.layanan_kesehatan', compact(
            'inap',
            'igd',
            'operasi',
            'radiologi',
            'rajal',
            'rajalpoli',
            'bpjs',
            'nonbpjs',
            'farmasi',
            'labsample',
            'labparameter'
        ));
    }

    public static function ranap($tanggal)
    {
        // $tanggal = Carbon::now()->format('Y-m-d');

        $data = DB::connection('mysqlkhanza')->table('kamar_inap')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
            ->join('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
            ->select('kamar_inap.no_rawat', 'kamar_inap.tgl_masuk', 'kamar_inap.tgl_keluar', 'kamar.kelas')
            ->whereDate('kamar_inap.tgl_masuk', '<=', $tanggal)
            ->whereDate('kamar_inap.tgl_keluar', '>=', $tanggal)
            ->orWhereDate('kamar_inap.tgl_keluar', '=', '0000-00-00')
            ->get();

        // dd($data);
        $lamakelas1 = $lamakelas2 = $lamakelas3 = $nonkelas = 0;
        $pasien1 = $pasien2 = $pasien3 = $pasienVip = 0;

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
                // $nonkelas = $nonkelas + $data->lama;
                $pasienVip++;
            }
        }

        // dd($lamakelas1, $pasien1, $lamakelas2, $pasien2, $lamakelas3, $pasien3);

        $datainap = [
            [
                'kode_kelas' => '01',
                // 'jml_hari' => $lamakelas1,
                'jumlah' => $pasienVip,
                'tgl_transaksi' => $tanggal
            ],
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

    public static function igd($tanggal)
    {
        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->select('reg_periksa.no_rawat', 'reg_periksa.kd_poli', 'reg_periksa.tgl_registrasi')
            ->whereDate('reg_periksa.tgl_registrasi', '=', $tanggal)
            ->where('reg_periksa.kd_poli', '=', 'IGDK')
            ->count();
        // dd($data);


        $arrayigd = [
            'jumlah' => $data,
            'tgl_transaksi' => $tanggal
        ];

        $data = json_decode(json_encode($arrayigd));



        return $data;
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
            $jumlahperawatan = DB::connection('mysqlkhanza')->table('periksa_lab')
                ->join('jns_perawatan_lab', 'jns_perawatan_lab.kd_jenis_prw', '=', 'periksa_lab.kd_jenis_prw')
                ->select('periksa_lab.no_rawat', 'periksa_lab.tgl_periksa', 'jns_perawatan_lab.nm_perawatan')
                // ->where('reg_periksa.status_lanjut', '=', 'Ralan')
                ->where('jns_perawatan_lab.nm_perawatan', '=', $data->nm_perawatan)
                ->whereDate('periksa_lab.tgl_periksa', '=', $tanggal)
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
            ->join('paket_operasi', 'paket_operasi.kode_paket', '=', 'operasi.kode_paket')
            ->select(
                'operasi.no_rawat',
                'operasi.tgl_operasi',
                'operasi.kategori',
                'operasi.kode_paket',
                'paket_operasi.nm_perawatan',
                'paket_operasi.operator1',
                'paket_operasi.kelas'
            )
            ->whereDate('operasi.tgl_operasi', '=', $tanggal)
            ->get();

        // dd($data);
        $bedahKecil = $bedahSedang1 = $bedahSedang2 = $bedahBesar1 = $bedahBesar2 = $bedahKhusus1 = $bedahKhusus2 = 0;

        foreach ($data as $listData) {
            if ($listData->kelas == 'Kelas 3') {
                if (($listData->operator1 > 0) and ($listData->operator1 <= 886500)) {
                    $bedahKecil++;
                } elseif (($listData->operator1 > 886500) and ($listData->operator1 <= 1813500)) {
                    $bedahSedang1++;
                } elseif (($listData->operator1 > 1813500) and ($listData->operator1 <= 2695500)) {
                    $bedahSedang2++;
                } elseif (($listData->operator1 > 2695500) and ($listData->operator1 <= 3586500)) {
                    $bedahBesar1++;
                } elseif (($listData->operator1 > 3586500) and ($listData->operator1 <= 4468500)) {
                    $bedahBesar2++;
                } elseif (($listData->operator1 > 4468500) and ($listData->operator1 <= 5589000)) {
                    $bedahKhusus1++;
                } elseif (($listData->operator1 > 5589000) and ($listData->operator1 <= 6705000)) {
                    $bedahKhusus2++;
                }
            } elseif (($listData->kelas == 'Kelas 2') or ($listData->kelas == 'Rawat Jalan')) {
                if (($listData->operator1 > 0) and ($listData->operator1 <= 985000)) {
                    $bedahKecil++;
                } elseif (($listData->operator1 > 985000) and ($listData->operator1 <= 2015000)) {
                    $bedahSedang1++;
                } elseif (($listData->operator1 > 2015000) and ($listData->operator1 <= 2995000)) {
                    $bedahSedang2++;
                } elseif (($listData->operator1 > 2995000) and ($listData->operator1 <= 3985000)) {
                    $bedahBesar1++;
                } elseif (($listData->operator1 > 3985000) and ($listData->operator1 <= 4965000)) {
                    $bedahBesar2++;
                } elseif (($listData->operator1 > 4965000) and ($listData->operator1 <= 6210000)) {
                    $bedahKhusus1++;
                } elseif (($listData->operator1 > 6210000) and ($listData->operator1 <= 7450000)) {
                    $bedahKhusus2++;
                }
            } elseif ($listData->kelas == 'Kelas 1') {
                if (($listData->operator1 > 0) and ($listData->operator1 <= 1085000)) {
                    $bedahKecil++;
                } elseif (($listData->operator1 > 1085000) and ($listData->operator1 <= 2220000)) {
                    $bedahSedang1++;
                } elseif (($listData->operator1 > 2220000) and ($listData->operator1 <= 3295000)) {
                    $bedahSedang2++;
                } elseif (($listData->operator1 > 3295000) and ($listData->operator1 <= 4385000)) {
                    $bedahBesar1++;
                } elseif (($listData->operator1 > 4385000) and ($listData->operator1 <= 5465000)) {
                    $bedahBesar2++;
                } elseif (($listData->operator1 > 5465000) and ($listData->operator1 <= 6835000)) {
                    $bedahKhusus1++;
                } elseif (($listData->operator1 > 6835000) and ($listData->operator1 <= 8195000)) {
                    $bedahKhusus2++;
                }
            } elseif ($listData->kelas == 'VIP') {
                if (($listData->operator1 > 0) and ($listData->operator1 <= 1190000)) {
                    $bedahKecil++;
                } elseif (($listData->operator1 > 1190000) and ($listData->operator1 <= 2420000)) {
                    $bedahSedang1++;
                } elseif (($listData->operator1 > 2420000) and ($listData->operator1 <= 3600000)) {
                    $bedahSedang2++;
                } elseif (($listData->operator1 > 3600000) and ($listData->operator1 <= 4790000)) {
                    $bedahBesar1++;
                } elseif (($listData->operator1 > 4790000) and ($listData->operator1 <= 5960000)) {
                    $bedahBesar2++;
                } elseif (($listData->operator1 > 5960000) and ($listData->operator1 <= 7460000)) {
                    $bedahKhusus1++;
                } elseif (($listData->operator1 > 7460000) and ($listData->operator1 <= 8940000)) {
                    $bedahKhusus2++;
                }
            }
        }

        // dd($data, $bedahKecil, $bedahSedang1, $bedahSedang2, $bedahBesar1, $bedahBesar2, $bedahKhusus1, $bedahKhusus2);

        $arrayoperasi = [
            [
                'jumlah' => $bedahKecil,
                'klasifikasi_operasi' => 'Tindakan Bedah Kecil',
                'tgl_transaksi' => $tanggal
            ],
            [
                'jumlah' => $bedahSedang1,
                'klasifikasi_operasi' => 'Tindakan Bedah Sedang 1',
                'tgl_transaksi' => $tanggal
            ],
            [
                'jumlah' => $bedahSedang2,
                'klasifikasi_operasi' => 'Tindakan Bedah Sedang 2',
                'tgl_transaksi' => $tanggal
            ],
            [
                'jumlah' => $bedahBesar1,
                'klasifikasi_operasi' => 'Tindakan Bedah Besar 1',
                'tgl_transaksi' => $tanggal
            ],
            [
                'jumlah' => $bedahBesar2,
                'klasifikasi_operasi' => 'Tindakan Bedah Besar 2',
                'tgl_transaksi' => $tanggal
            ],
            [
                'jumlah' => $bedahKhusus1,
                'klasifikasi_operasi' => 'Tindakan Bedah Khusus 1',
                'tgl_transaksi' => $tanggal
            ],
            [
                'jumlah' => $bedahKhusus2,
                'klasifikasi_operasi' => 'Tindakan Bedah Khusus 2',
                'tgl_transaksi' => $tanggal
            ]
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
            ->select('reg_periksa.no_rawat', 'reg_periksa.tgl_registrasi', 'reg_periksa.kd_poli', 'reg_periksa.status_lanjut', 'reg_periksa.stts')
            ->where('reg_periksa.status_lanjut', '=', 'Ralan')
            ->where('reg_periksa.kd_poli', '!=', 'IGDK')
            ->where('reg_periksa.stts', '=', 'Sudah')
            ->whereDate('reg_periksa.tgl_registrasi', '=', $tanggal)
            ->count();

        // dd($data);

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
            ->where('poliklinik.kd_poli', '!=', 'IGDK')
            ->where('poliklinik.kd_poli', '!=', '-')
            ->orderBy('poliklinik.kd_poli', 'asc')
            ->get();

        // dd($data);

        $i = 0;

        foreach ($data as $poli) {
            $jumlahpoli = DB::connection('mysqlkhanza')->table('reg_periksa')
                ->select('reg_periksa.no_rawat', 'reg_periksa.tgl_registrasi', 'reg_periksa.kd_poli', 'reg_periksa.status_lanjut', 'reg_periksa.stts')
                ->where('reg_periksa.status_lanjut', '=', 'Ralan')
                ->where('reg_periksa.stts', '=', 'Sudah')
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
            ->select('reg_periksa.no_rawat', 'reg_periksa.tgl_registrasi', 'reg_periksa.kd_poli', 'reg_periksa.kd_pj', 'reg_periksa.stts')
            ->where('reg_periksa.kd_pj', '=', 'BPJ')
            ->where('reg_periksa.stts', '=', 'Sudah')
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
            ->select('reg_periksa.no_rawat', 'reg_periksa.tgl_registrasi', 'reg_periksa.kd_poli', 'reg_periksa.kd_pj', 'reg_periksa.stts')
            ->where('reg_periksa.kd_pj', '<>', 'BPJ')
            ->where('reg_periksa.stts', '=', 'Sudah')
            ->whereDate('reg_periksa.tgl_registrasi', '=', $tanggal)
            ->count();

        $array = [
            'jumlah' => $data,
            'tgl_transaksi' => $tanggal
        ];
        $nonbpjs = json_decode(json_encode($array));

        return $nonbpjs;
    }

    public static function farmasi($tanggal)
    {
        $data = DB::connection('mysqlkhanza')->table('resep_obat')
            ->select('resep_obat.no_rawat', 'resep_obat.no_resep', 'resep_obat.tgl_peresepan', 'resep_obat.status')
            ->whereDate('resep_obat.tgl_peresepan', '=', $tanggal)
            ->count();

        $array = [
            'jumlah' => $data,
            'tgl_transaksi' => $tanggal
        ];
        $resep = json_decode(json_encode($array));

        return $resep;
    }
}
