<?php

namespace App\Http\Controllers;

use App\Exports\TarifLabExport;
use App\Exports\TarifOperasiExport;
use App\Exports\TarifRadioExport;
use App\Exports\TarifRajalExport;
use App\Exports\TarifRanapExport;
use App\Imports\TarifLabImport;
use App\Imports\TarifRadioImport;
use App\Imports\TarifRajalImport;
use App\Imports\TarifRanapImport;
use App\TarifLab;
use App\TarifOperasi;
use App\TarifRadiologi;
use App\TarifRajal;
use App\TarifRanap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class TarifSimController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function rajal()
    {
        session()->put('ibu', 'Tarif SIMRS');
        session()->put('anak', 'Tarif Rajal');
        session()->forget('cucu');

        $data = DB::connection('mysqlkhanzadummy')->table('jns_perawatan')
            ->join('kategori_perawatan', 'kategori_perawatan.kd_kategori', '=', 'jns_perawatan.kd_kategori')
            ->join('penjab', 'penjab.kd_pj', '=', 'jns_perawatan.kd_pj')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'jns_perawatan.kd_poli')
            ->select(
                'jns_perawatan.kd_jenis_prw',
                'jns_perawatan.nm_perawatan',
                'jns_perawatan.kd_kategori',
                'jns_perawatan.material',
                'jns_perawatan.bhp',
                'jns_perawatan.tarif_tindakandr',
                'jns_perawatan.tarif_tindakanpr',
                'jns_perawatan.kso',
                'jns_perawatan.menejemen',
                'jns_perawatan.total_byrdr',
                'jns_perawatan.total_byrpr',
                'jns_perawatan.total_byrdrpr',
                'jns_perawatan.kd_pj',
                'jns_perawatan.kd_poli',
                'jns_perawatan.status',
                'kategori_perawatan.nm_kategori',
                'penjab.png_jawab',
                'poliklinik.nm_poli'
            )
            ->where('jns_perawatan.status', '=', '1')
            ->get();

        // dd($data);
        return view('tarifsim.rajal', compact('data'));
    }

    public function exportRajal()
    {
        return Excel::download(new TarifRajalExport, 'tarif_rajal.xlsx');
    }

    public function importRajal(Request $request)
    {
        // validasi
        $this->validate($request, [
            'file' => 'required|mimes:xls,xlsx'
        ]);
        set_time_limit(0);

        // menangkap file excel
        $file = $request->file('file');

        // dd($file);

        // membuat nama file unik
        $nama_file = rand() . $file->getClientOriginalName();

        // upload ke folder file_siswa di dalam folder public
        $file->move('tarif/rajal', $nama_file);

        try {
            $data = Excel::toArray(new TarifRajalImport(), public_path('/tarif/rajal/' . $nama_file));

            foreach ($data[0] as $listData) {
                // dd($listData);
                $cek = TarifRajal::where('kd_jenis_prw', $listData['kode_jenis_perawatan'])
                    ->first();


                if (empty($cek)) {
                    // dd('baru';

                    $simpan = new TarifRajal();
                    $simpan->kd_jenis_prw = $listData['kode_jenis_perawatan'];
                    $simpan->nm_perawatan = $listData['nama_perawatan'];
                    $simpan->kd_kategori = $listData['kode_kategori'];
                    $simpan->material = $listData['jasa_rs'] ? $listData['jasa_rs'] : 0;
                    $simpan->bhp = $listData['bhp'] ? $listData['bhp'] : 0;
                    $simpan->tarif_tindakandr = $listData['tarif_dokter'] ? $listData['tarif_dokter'] : 0;
                    $simpan->tarif_tindakanpr = $listData['tarif_perawat'] ? $listData['tarif_perawat'] : 0;
                    $simpan->kso = $listData['kso'] ? $listData['kso'] : 0;
                    $simpan->menejemen = $listData['managemen'] ? $listData['managemen'] : 0;
                    $simpan->total_byrdr = $listData['total_bayar_dokter'] ? $listData['total_bayar_dokter'] : 0;
                    $simpan->total_byrpr = $listData['total_bayar_perawat'] ? $listData['total_bayar_perawat'] : 0;
                    $simpan->total_byrdrpr = $listData['total_bayar_dokter_perawat'] ? $listData['total_bayar_dokter_perawat'] : 0;
                    $simpan->kd_pj = $listData['kode_penjamin'];
                    $simpan->kd_poli = $listData['kode_poliklinik'];
                    $simpan->status = $listData['status'] == 1 ? '1' : '0';
                    $simpan->save();
                } else {
                    $cek = TarifRajal::where('kd_jenis_prw', $listData['kode_jenis_perawatan'])->update([
                        'kd_jenis_prw' => $listData['kode_jenis_perawatan'],
                        'nm_perawatan' => $listData['nama_perawatan'],
                        'kd_kategori' => $listData['kode_kategori'],
                        'material' => $listData['jasa_rs'] ? $listData['jasa_rs'] : 0,
                        'bhp' => $listData['bhp'] ? $listData['bhp'] : 0,
                        'tarif_tindakandr' => $listData['tarif_dokter'] ? $listData['tarif_dokter'] : 0,
                        'tarif_tindakanpr' => $listData['tarif_perawat'] ? $listData['tarif_perawat'] : 0,
                        'kso' => $listData['kso'] ? $listData['kso'] : 0,
                        'menejemen' => $listData['managemen'] ? $listData['managemen'] : 0,
                        'total_byrdr' => $listData['total_bayar_dokter'] ? $listData['total_bayar_dokter'] : 0,
                        'total_byrpr' => $listData['total_bayar_perawat'] ? $listData['total_bayar_perawat'] : 0,
                        'total_byrdrpr' => $listData['total_bayar_dokter_perawat'] ? $listData['total_bayar_dokter_perawat'] : 0,
                        'kd_pj' => $listData['kode_penjamin'],
                        'kd_poli' => $listData['kode_poliklinik'],
                        'status' => $listData['status'] == 1 ? '1' : '0'
                    ]);
                }
            }


            // notifikasi dengan session
            Session::flash('sukses', 'Data Berhasil Diimport!');
        } catch (\Exception $e) {
            // $error = getBod
            $response = $e->getMessage();
            // $test = json_decode($response->getBody());
            // dd($response);
            // notifikasi dengan session
            Session::flash('error', $response);
        }

        // alihkan halaman kembali
        return redirect()->back();
    }

    public function templateImportRajal()
    {
        //PDF file is stored under project/public/download/info.pdf
        $file = public_path() . "/tarif/rajal/template_import_tarifrajal.xlsx";

        $headers = [
            'Content-Type' => 'application/xlsx',
        ];

        return response()->download($file, 'template_import_tarifrajal.xlsx', $headers);
    }

    public function ranap()
    {
        session()->put('ibu', 'Tarif SIMRS');
        session()->put('anak', 'Tarif Ranap');
        session()->forget('cucu');

        $data = DB::connection('mysqlkhanzadummy')->table('jns_perawatan_inap')
            ->join('kategori_perawatan', 'kategori_perawatan.kd_kategori', '=', 'jns_perawatan_inap.kd_kategori')
            ->join('penjab', 'penjab.kd_pj', '=', 'jns_perawatan_inap.kd_pj')
            ->join('bangsal', 'bangsal.kd_bangsal', '=', 'jns_perawatan_inap.kd_bangsal')
            ->select(
                'jns_perawatan_inap.kd_jenis_prw',
                'jns_perawatan_inap.nm_perawatan',
                'jns_perawatan_inap.kd_kategori',
                'jns_perawatan_inap.material',
                'jns_perawatan_inap.bhp',
                'jns_perawatan_inap.tarif_tindakandr',
                'jns_perawatan_inap.tarif_tindakanpr',
                'jns_perawatan_inap.kso',
                'jns_perawatan_inap.menejemen',
                'jns_perawatan_inap.total_byrdr',
                'jns_perawatan_inap.total_byrpr',
                'jns_perawatan_inap.total_byrdrpr',
                'jns_perawatan_inap.kd_pj',
                'jns_perawatan_inap.kd_bangsal',
                'jns_perawatan_inap.status',
                'jns_perawatan_inap.kelas',
                'kategori_perawatan.nm_kategori',
                'penjab.png_jawab',
                'bangsal.nm_bangsal'
            )
            ->where('jns_perawatan_inap.status', '=', '1')
            ->get();

        // dd($data);
        return view('tarifsim.ranap', compact('data'));
    }

    public function exportRanap()
    {
        return Excel::download(new TarifRanapExport, 'tarif_ranap.xlsx');
    }

    public function importRanap(Request $request)
    {
        // validasi
        $this->validate($request, [
            'file' => 'required|mimes:xls,xlsx'
        ]);
        set_time_limit(0);

        // menangkap file excel
        $file = $request->file('file');

        // dd($file);

        // membuat nama file unik
        $nama_file = rand() . $file->getClientOriginalName();

        // upload ke folder file_siswa di dalam folder public
        $file->move('tarif/ranap', $nama_file);

        try {
            $data = Excel::toArray(new TarifRanapImport(), public_path('/tarif/ranap/' . $nama_file));

            foreach ($data[0] as $listData) {
                // dd($listData);
                $cek = TarifRanap::where('kd_jenis_prw', $listData['kode_jenis_perawatan'])
                    ->first();


                if (empty($cek)) {
                    // dd('baru';

                    $simpan = new TarifRanap();
                    $simpan->kd_jenis_prw = $listData['kode_jenis_perawatan'];
                    $simpan->nm_perawatan = $listData['nama_perawatan'];
                    $simpan->kd_kategori = $listData['kode_kategori'];
                    $simpan->material = $listData['jasa_rs'] ? $listData['jasa_rs'] : 0;
                    $simpan->bhp = $listData['bhp'] ? $listData['bhp'] : 0;
                    $simpan->tarif_tindakandr = $listData['tarif_dokter'] ? $listData['tarif_dokter'] : 0;
                    $simpan->tarif_tindakanpr = $listData['tarif_perawat'] ? $listData['tarif_perawat'] : 0;
                    $simpan->kso = $listData['kso'] ? $listData['kso'] : 0;
                    $simpan->menejemen = $listData['managemen'] ? $listData['managemen'] : 0;
                    $simpan->total_byrdr = $listData['total_bayar_dokter'] ? $listData['total_bayar_dokter'] : 0;
                    $simpan->total_byrpr = $listData['total_bayar_perawat'] ? $listData['total_bayar_perawat'] : 0;
                    $simpan->total_byrdrpr = $listData['total_bayar_dokter_perawat'] ? $listData['total_bayar_dokter_perawat'] : 0;
                    $simpan->kd_pj = $listData['kode_penjamin'];
                    $simpan->kd_bangsal = $listData['kode_bangsal'];
                    $simpan->status = $listData['status'] == 1 ? '1' : '0';
                    $simpan->kelas = $listData['kelas'];
                    $simpan->save();
                } else {
                    $cek = TarifRanap::where('kd_jenis_prw', $listData['kode_jenis_perawatan'])->update([
                        'kd_jenis_prw' => $listData['kode_jenis_perawatan'],
                        'nm_perawatan' => $listData['nama_perawatan'],
                        'kd_kategori' => $listData['kode_kategori'],
                        'material' => $listData['jasa_rs'] ? $listData['jasa_rs'] : 0,
                        'bhp' => $listData['bhp'] ? $listData['bhp'] : 0,
                        'tarif_tindakandr' => $listData['tarif_dokter'] ? $listData['tarif_dokter'] : 0,
                        'tarif_tindakanpr' => $listData['tarif_perawat'] ? $listData['tarif_perawat'] : 0,
                        'kso' => $listData['kso'] ? $listData['kso'] : 0,
                        'menejemen' => $listData['managemen'] ? $listData['managemen'] : 0,
                        'total_byrdr' => $listData['total_bayar_dokter'] ? $listData['total_bayar_dokter'] : 0,
                        'total_byrpr' => $listData['total_bayar_perawat'] ? $listData['total_bayar_perawat'] : 0,
                        'total_byrdrpr' => $listData['total_bayar_dokter_perawat'] ? $listData['total_bayar_dokter_perawat'] : 0,
                        'kd_pj' => $listData['kode_penjamin'],
                        'kd_bangsal' => $listData['kode_bangsal'],
                        'status' => $listData['status'] == 1 ? '1' : '0',
                        'kelas' => $listData['kelas']
                    ]);
                }
            }


            // notifikasi dengan session
            Session::flash('sukses', 'Data Berhasil Diimport!');
        } catch (\Exception $e) {
            // $error = getBod
            $response = $e->getMessage();
            // $test = json_decode($response->getBody());
            // dd($response);
            // notifikasi dengan session
            Session::flash('error', $response);
        }

        // alihkan halaman kembali
        return redirect()->back();
    }

    public function templateImportRanap()
    {
        //PDF file is stored under project/public/download/info.pdf
        $file = public_path() . "/tarif/ranap/template_import_tarifranap.xlsx";

        $headers = [
            'Content-Type' => 'application/xlsx',
        ];

        return response()->download($file, 'template_import_tarifrajal.xlsx', $headers);
    }

    public function lab()
    {
        session()->put('ibu', 'Tarif SIMRS');
        session()->put('anak', 'Tarif Lab');
        session()->forget('cucu');

        $data = DB::connection('mysqlkhanzadummy')->table('jns_perawatan_lab')
            ->join('penjab', 'penjab.kd_pj', '=', 'jns_perawatan_lab.kd_pj')
            ->select(
                'jns_perawatan_lab.kd_jenis_prw',
                'jns_perawatan_lab.nm_perawatan',
                'jns_perawatan_lab.bagian_rs',
                'jns_perawatan_lab.bhp',
                'jns_perawatan_lab.tarif_perujuk',
                'jns_perawatan_lab.tarif_tindakan_dokter',
                'jns_perawatan_lab.tarif_tindakan_petugas',
                'jns_perawatan_lab.kso',
                'jns_perawatan_lab.menejemen',
                'jns_perawatan_lab.total_byr',
                'jns_perawatan_lab.kd_pj',
                'jns_perawatan_lab.status',
                'jns_perawatan_lab.kelas',
                'jns_perawatan_lab.kategori',
                'penjab.png_jawab'
            )
            ->where('jns_perawatan_lab.status', '=', '1')
            ->get();

        // dd($data);
        return view('tarifsim.lab', compact('data'));
    }

    public function exportLab()
    {
        return Excel::download(new TarifLabExport, 'tarif_lab.xlsx');
    }

    public function importLab(Request $request)
    {
        // validasi
        $this->validate($request, [
            'file' => 'required|mimes:xls,xlsx'
        ]);
        set_time_limit(0);

        // menangkap file excel
        $file = $request->file('file');

        // dd($file);

        // membuat nama file unik
        $nama_file = rand() . $file->getClientOriginalName();

        // upload ke folder file_siswa di dalam folder public
        $file->move('tarif/lab', $nama_file);

        try {
            $data = Excel::toArray(new TarifLabImport(), public_path('/tarif/lab/' . $nama_file));

            foreach ($data[0] as $listData) {
                // dd($listData);
                $cek = TarifLab::where('kd_jenis_prw', $listData['kode_periksa'])
                    ->first();


                if (empty($cek)) {
                    // dd('baru';

                    $simpan = new TarifLab();
                    $simpan->kd_jenis_prw = $listData['kode_periksa'];
                    $simpan->nm_perawatan = $listData['nama_pemeriksaan'];
                    $simpan->bagian_rs = $listData['jasa_rs'] ? $listData['jasa_rs'] : 0;
                    $simpan->bhp = $listData['paket_bhp'] ? $listData['paket_bhp'] : 0;
                    $simpan->tarif_perujuk = $listData['jm_perujuk'] ? $listData['jm_perujuk'] : 0;
                    $simpan->tarif_tindakan_dokter = $listData['jm_dokter'] ? $listData['jm_dokter'] : 0;
                    $simpan->tarif_tindakan_petugas = $listData['jm_petugas'] ? $listData['jm_petugas'] : 0;
                    $simpan->kso = $listData['kso'] ? $listData['kso'] : 0;
                    $simpan->menejemen = $listData['menejemen'] ? $listData['menejemen'] : 0;
                    $simpan->total_byr = $listData['ttl_tarif'] ? $listData['ttl_tarif'] : 0;
                    $simpan->kd_pj = $listData['jenis_bayar'];
                    $simpan->status = $listData['status_aktif'] == 1 ? '1' : '0';
                    $simpan->kelas = $listData['kelas'];
                    $simpan->kategori = $listData['kategori'];
                    $simpan->save();
                } else {
                    $cek = TarifLab::where('kd_jenis_prw', $listData['kode_periksa'])->update([
                        'kd_jenis_prw' => $listData['kode_periksa'],
                        'nm_perawatan' => $listData['nama_pemeriksaan'],
                        'bagian_rs' => $listData['jasa_rs'] ? $listData['jasa_rs'] : 0,
                        'bhp' => $listData['paket_bhp'] ? $listData['paket_bhp'] : 0,
                        'tarif_perujuk' => $listData['jm_perujuk'] ? $listData['jm_perujuk'] : 0,
                        'tarif_tindakan_dokter' => $listData['jm_dokter'] ? $listData['jm_dokter'] : 0,
                        'tarif_tindakan_petugas' => $listData['jm_petugas'] ? $listData['jm_petugas'] : 0,
                        'kso' => $listData['kso'] ? $listData['kso'] : 0,
                        'menejemen' => $listData['menejemen'] ? $listData['menejemen'] : 0,
                        'total_byr' => $listData['ttl_tarif'] ? $listData['ttl_tarif'] : 0,
                        'kd_pj' => $listData['jenis_bayar'],
                        'status' => $listData['status_aktif'] == 1 ? '1' : '0',
                        'kelas' => $listData['kelas'],
                        'kategori' => $listData['kategori']
                    ]);
                }
            }


            // notifikasi dengan session
            Session::flash('sukses', 'Data Berhasil Diimport!');
        } catch (\Exception $e) {
            // $error = getBod
            $response = $e->getMessage();
            // $test = json_decode($response->getBody());
            // dd($response);
            // notifikasi dengan session
            Session::flash('error', $response);
        }

        // alihkan halaman kembali
        return redirect()->back();
    }

    public function templateImportLab()
    {
        //PDF file is stored under project/public/download/info.pdf
        $file = public_path() . "/tarif/lab/template_import_tariflab.xlsx";

        $headers = [
            'Content-Type' => 'application/xlsx',
        ];

        return response()->download($file, 'template_import_tariflab.xlsx', $headers);
    }

    public function radiologi()
    {
        session()->put('ibu', 'Tarif SIMRS');
        session()->put('anak', 'Tarif Radiologi');
        session()->forget('cucu');

        $data = DB::connection('mysqlkhanzadummy')->table('jns_perawatan_radiologi')
            ->join('penjab', 'penjab.kd_pj', '=', 'jns_perawatan_radiologi.kd_pj')
            ->select(
                'jns_perawatan_radiologi.kd_jenis_prw',
                'jns_perawatan_radiologi.nm_perawatan',
                'jns_perawatan_radiologi.bagian_rs',
                'jns_perawatan_radiologi.bhp',
                'jns_perawatan_radiologi.tarif_perujuk',
                'jns_perawatan_radiologi.tarif_tindakan_dokter',
                'jns_perawatan_radiologi.tarif_tindakan_petugas',
                'jns_perawatan_radiologi.kso',
                'jns_perawatan_radiologi.menejemen',
                'jns_perawatan_radiologi.total_byr',
                'jns_perawatan_radiologi.kd_pj',
                'jns_perawatan_radiologi.status',
                'jns_perawatan_radiologi.kelas',
                'penjab.png_jawab'
            )
            ->where('jns_perawatan_radiologi.status', '=', '1')
            ->get();

        // dd($data);
        return view('tarifsim.radio', compact('data'));
    }

    public function exportRadiologi()
    {
        return Excel::download(new TarifRadioExport, 'tarif_radiologi.xlsx');
    }

    public function importRadiologi(Request $request)
    {
        // validasi
        $this->validate($request, [
            'file' => 'required|mimes:xls,xlsx'
        ]);
        set_time_limit(0);

        // menangkap file excel
        $file = $request->file('file');

        // dd($file);

        // membuat nama file unik
        $nama_file = rand() . $file->getClientOriginalName();

        // upload ke folder file_siswa di dalam folder public
        $file->move('tarif/radiologi', $nama_file);

        try {
            $data = Excel::toArray(new TarifRadioImport(), public_path('/tarif/radiologi/' . $nama_file));

            foreach ($data[0] as $listData) {
                // dd($listData);
                $cek = TarifRadiologi::where('kd_jenis_prw', $listData['kode_periksa'])
                    ->first();

                if (empty($cek)) {
                    // dd('baru';
                    $simpan = new TarifRadiologi();
                    $simpan->kd_jenis_prw = $listData['kode_periksa'];
                    $simpan->nm_perawatan = $listData['nama_pemeriksaan'];
                    $simpan->bagian_rs = $listData['jasa_rs'] ? $listData['jasa_rs'] : 0;
                    $simpan->bhp = $listData['paket_bhp'] ? $listData['paket_bhp'] : 0;
                    $simpan->tarif_perujuk = $listData['jm_perujuk'] ? $listData['jm_perujuk'] : 0;
                    $simpan->tarif_tindakan_dokter = $listData['jm_dokter'] ? $listData['jm_dokter'] : 0;
                    $simpan->tarif_tindakan_petugas = $listData['jm_petugas'] ? $listData['jm_petugas'] : 0;
                    $simpan->kso = $listData['kso'] ? $listData['kso'] : 0;
                    $simpan->menejemen = $listData['menejemen'] ? $listData['menejemen'] : 0;
                    $simpan->total_byr = $listData['ttl_tarif'] ? $listData['ttl_tarif'] : 0;
                    $simpan->kd_pj = $listData['jenis_bayar'];
                    $simpan->status = $listData['status_aktif'] == 1 ? '1' : '0';
                    $simpan->kelas = $listData['kelas'];
                    $simpan->save();
                } else {
                    $cek = TarifRadiologi::where('kd_jenis_prw', $listData['kode_periksa'])->update([
                        'kd_jenis_prw' => $listData['kode_periksa'],
                        'nm_perawatan' => $listData['nama_pemeriksaan'],
                        'bagian_rs' => $listData['jasa_rs'] ? $listData['jasa_rs'] : 0,
                        'bhp' => $listData['paket_bhp'] ? $listData['paket_bhp'] : 0,
                        'tarif_perujuk' => $listData['jm_perujuk'] ? $listData['jm_perujuk'] : 0,
                        'tarif_tindakan_dokter' => $listData['jm_dokter'] ? $listData['jm_dokter'] : 0,
                        'tarif_tindakan_petugas' => $listData['jm_petugas'] ? $listData['jm_petugas'] : 0,
                        'kso' => $listData['kso'] ? $listData['kso'] : 0,
                        'menejemen' => $listData['menejemen'] ? $listData['menejemen'] : 0,
                        'total_byr' => $listData['ttl_tarif'] ? $listData['ttl_tarif'] : 0,
                        'kd_pj' => $listData['jenis_bayar'],
                        'status' => $listData['status_aktif'] == 1 ? '1' : '0',
                        'kelas' => $listData['kelas']
                    ]);
                }
            }
            // notifikasi dengan session
            Session::flash('sukses', 'Data Berhasil Diimport!');
        } catch (\Exception $e) {
            // $error = getBod
            $response = $e->getMessage();
            // $test = json_decode($response->getBody());
            // dd($response);
            // notifikasi dengan session
            Session::flash('error', $response);
        }

        // alihkan halaman kembali
        return redirect()->back();
    }

    public function templateImportRadiologi()
    {
        //PDF file is stored under project/public/download/info.pdf
        $file = public_path() . "/tarif/radiologi/template_import_tarifradiologi.xlsx";

        $headers = [
            'Content-Type' => 'application/xlsx',
        ];

        return response()->download($file, 'template_import_tarifradiologi.xlsx', $headers);
    }

    public function operasi()
    {
        session()->put('ibu', 'Tarif SIMRS');
        session()->put('anak', 'Tarif Operasi');
        session()->forget('cucu');

        $data = DB::connection('mysqlkhanzadummy')->table('paket_operasi')
            ->join('penjab', 'penjab.kd_pj', '=', 'paket_operasi.kd_pj')
            ->select(
                'paket_operasi.kode_paket',
                'paket_operasi.nm_perawatan',
                'paket_operasi.kategori',
                'paket_operasi.operator1',
                'paket_operasi.operator2',
                'paket_operasi.operator3',
                'paket_operasi.asisten_operator1',
                'paket_operasi.asisten_operator2',
                'paket_operasi.asisten_operator3',
                'paket_operasi.instrumen',
                'paket_operasi.dokter_anak',
                'paket_operasi.perawaat_resusitas',
                'paket_operasi.dokter_anestesi',
                'paket_operasi.asisten_anestesi',
                'paket_operasi.asisten_anestesi2',
                'paket_operasi.bidan',
                'paket_operasi.bidan2',
                'paket_operasi.bidan3',
                'paket_operasi.perawat_luar',
                'paket_operasi.sewa_ok',
                'paket_operasi.alat',
                'paket_operasi.akomodasi',
                'paket_operasi.bagian_rs',
                'paket_operasi.omloop',
                'paket_operasi.omloop2',
                'paket_operasi.omloop3',
                'paket_operasi.omloop4',
                'paket_operasi.omloop5',
                'paket_operasi.sarpras',
                'paket_operasi.dokter_pjanak',
                'paket_operasi.dokter_umum',
                'paket_operasi.kd_pj',
                'paket_operasi.status',
                'paket_operasi.kelas',
                'penjab.png_jawab',
                DB::raw("operator1 + operator2 + operator3 + asisten_operator1 + asisten_operator2 + asisten_operator3 +
                 instrumen + dokter_anak + perawaat_resusitas + dokter_anestesi + asisten_anestesi + asisten_anestesi2
                 + bidan + bidan2 + bidan3 + perawat_luar + sewa_ok + alat + akomodasi + bagian_rs + omloop +
                 omloop2 + omloop3 + omloop4 +omloop5 + sarpras + dokter_pjanak + dokter_umum AS jml_tarif")
            )
            ->where('paket_operasi.status', '=', '1')
            ->get();

        // dd($data);
        return view('tarifsim.operasi', compact('data'));
    }

    public function exportOperasi()
    {
        return Excel::download(new TarifOperasiExport, 'tarif_operasi.xlsx');
    }

    public function importOperasi(Request $request)
    {
        // validasi
        $this->validate($request, [
            'file' => 'required|mimes:xls,xlsx'
        ]);
        set_time_limit(0);

        // menangkap file excel
        $file = $request->file('file');

        // dd($file);

        // membuat nama file unik
        $nama_file = rand() . $file->getClientOriginalName();

        // upload ke folder file_siswa di dalam folder public
        $file->move('tarif/operasi', $nama_file);

        try {
            $data = Excel::toArray(new TarifRadioImport(), public_path('/tarif/operasi/' . $nama_file));

            foreach ($data[0] as $listData) {
                // dd($listData);
                $cek = TarifOperasi::where('kode_paket', $listData['kode_paket'])
                    ->first();

                if (empty($cek)) {
                    // dd('baru';
                    $simpan = new TarifOperasi();
                    $simpan->kode_paket = $listData['kode_paket'];
                    $simpan->nm_perawatan = $listData['nama_operasi'];
                    $simpan->kategori = $listData['kategori'];
                    $simpan->operator1 = $listData['operator_1'] ? $listData['operator_1'] : 0;
                    $simpan->operator2 = $listData['operator_2'] ? $listData['operator_2'] : 0;
                    $simpan->operator3 = $listData['operator_3'] ? $listData['operator_3'] : 0;
                    $simpan->asisten_operator1 = $listData['asisten_op_1'] ? $listData['asisten_op_1'] : 0;
                    $simpan->asisten_operator2 = $listData['asisten_op_2'] ? $listData['asisten_op_2'] : 0;
                    $simpan->asisten_operator3 = $listData['asisten_op_3'] ? $listData['asisten_op_3'] : 0;
                    $simpan->instrumen = $listData['instrumen'] ? $listData['instrumen'] : 0;
                    $simpan->dokter_anak = $listData['dr_anak'] ? $listData['dr_anak'] : 0;
                    $simpan->perawaat_resusitas = $listData['perawat_resus'] ? $listData['perawat_resus'] : 0;
                    $simpan->dokter_anestesi = $listData['dr_anestesi'] ? $listData['dr_anestesi'] : 0;
                    $simpan->asisten_anestesi = $listData['asisten_anes_1'] ? $listData['asisten_anes_1'] : 0;
                    $simpan->asisten_anestesi2 = $listData['asisten_anes_2'] ? $listData['asisten_anes_2'] : 0;
                    $simpan->bidan = $listData['bidan_1'] ? $listData['bidan_1'] : 0;
                    $simpan->bidan2 = $listData['bidan_2'] ? $listData['bidan_2'] : 0;
                    $simpan->bidan3 = $listData['bidan_3'] ? $listData['bidan_3'] : 0;
                    $simpan->perawat_luar = $listData['perawat_luar'] ? $listData['perawat_luar'] : 0;
                    $simpan->sewa_ok = $listData['sewa_okvk'] ? $listData['sewa_okvk'] : 0;
                    $simpan->alat = $listData['alat'] ? $listData['alat'] : 0;
                    $simpan->akomodasi = $listData['akomodasi'] ? $listData['akomodasi'] : 0;
                    $simpan->bagian_rs = $listData['nms'] ? $listData['nms'] : 0;
                    $simpan->omloop = $listData['onloop_1'] ? $listData['onloop_1'] : 0;
                    $simpan->omloop2 = $listData['onloop_2'] ? $listData['onloop_2'] : 0;
                    $simpan->omloop3 = $listData['onloop_3'] ? $listData['onloop_3'] : 0;
                    $simpan->omloop4 = $listData['onloop_4'] ? $listData['onloop_4'] : 0;
                    $simpan->omloop5 = $listData['onloop_5'] ? $listData['onloop_5'] : 0;
                    $simpan->sarpras = $listData['sarpras'] ? $listData['sarpras'] : 0;
                    $simpan->dokter_pjanak = $listData['dr_pj_anak'] ? $listData['dr_pj_anak'] : 0;
                    $simpan->dokter_umum = $listData['dr_umum'] ? $listData['dr_umum'] : 0;
                    $simpan->status = $listData['status'] == 1 ? '1' : '0';
                    $simpan->kd_pj = $listData['kode_pj'];
                    $simpan->kelas = $listData['kelas'];
                    $simpan->save();
                } else {
                    $cek = TarifOperasi::where('kode_paket', $listData['kode_paket'])->update([
                        'nm_perawatan' => $listData['nama_operasi'],
                        'kategori' => $listData['kategori'],
                        'operator1' => $listData['operator_1'],
                        'operator2' => $listData['operator_2'],
                        'operator3' => $listData['operator_3'],
                        'asisten_operator1' => $listData['asisten_op_1'],
                        'asisten_operator2' => $listData['asisten_op_2'],
                        'asisten_operator3' => $listData['asisten_op_3'],
                        'instrumen' => $listData['instrumen'],
                        'dokter_anak' => $listData['dr_anak'],
                        'perawaat_resusitas' => $listData['perawat_resus'],
                        'dokter_anestesi' => $listData['dr_anestesi'],
                        'asisten_anestesi' => $listData['asisten_anes_1'],
                        'asisten_anestesi2' => $listData['asisten_anes_2'],
                        'bidan' => $listData['bidan_1'],
                        'bidan2' => $listData['bidan_2'],
                        'bidan3' => $listData['bidan_3'],
                        'perawat_luar' => $listData['perawat_luar'],
                        'sewa_ok' => $listData['sewa_okvk'],
                        'alat' => $listData['alat'],
                        'akomodasi' => $listData['akomodasi'],
                        'bagian_rs' => $listData['nms'],
                        'omloop' => $listData['onloop_1'],
                        'omloop2' => $listData['onloop_2'],
                        'omloop3' => $listData['onloop_3'],
                        'omloop4' => $listData['onloop_4'],
                        'omloop5' => $listData['onloop_5'],
                        'sarpras' => $listData['sarpras'],
                        'dokter_pjanak' => $listData['dr_pj_anak'],
                        'dokter_umum' => $listData['dr_umum'],
                        'status' => $listData['status'] == 1 ? '1' : '0',
                        'kd_pj' => $listData['kode_pj'],
                        'kelas' => $listData['kelas']
                    ]);
                }
            }
            // notifikasi dengan session
            Session::flash('sukses', 'Data Berhasil Diimport!');
        } catch (\Exception $e) {
            // $error = getBod
            $response = $e->getMessage();
            // $test = json_decode($response->getBody());
            // dd($response);
            // notifikasi dengan session
            Session::flash('error', $response);
        }

        // alihkan halaman kembali
        return redirect()->back();
    }

    public function templateImportOperasi()
    {
        //PDF file is stored under project/public/download/info.pdf
        $file = public_path() . "/tarif/operasi/template_import_tarifoperasi.xlsx";

        $headers = [
            'Content-Type' => 'application/xlsx',
        ];

        return response()->download($file, 'template_import_tarifoperasi.xlsx', $headers);
    }
}
