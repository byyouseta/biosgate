<?php

namespace App\Http\Controllers;

use App\Imports\KlaimCairImport;
use App\KlaimCair;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class KlaimCompareController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Klaim Compare');
        session()->forget('cucu');

        if (isset($request->tanggal)) {
            $tanggal = $request->tanggal;
        } else {
            $tanggal = Carbon::now()->format('Y-m-d');
        }

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
            ->leftJoin('bridging_sep', 'bridging_sep.no_rawat', '=', 'reg_periksa.no_rawat')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.almt_pj',
                'reg_periksa.status_lanjut',
                'reg_periksa.kd_pj',
                'reg_periksa.umurdaftar',
                'reg_periksa.sttsumur',
                'reg_periksa.stts',
                'poliklinik.nm_poli',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.no_peserta',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pasien.alamat',
                'bridging_sep.no_sep',
                'dokter.nm_dokter'
            )
            ->where('reg_periksa.kd_pj', '=', 'BPJ')
            ->whereDate('reg_periksa.tgl_registrasi', '=', $tanggal)
            ->where('reg_periksa.stts', '!=', 'Batal')
            ->get();

        // dd($data);

        return view('vedika.compare', compact('data'));
    }

    public function import_excel(Request $request)
    {
        set_time_limit(0);
        // validasi
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);

        // menangkap file excel
        $file = $request->file('file');

        // membuat nama file unik
        $nama_file = rand() . $file->getClientOriginalName();

        // upload ke folder file_siswa di dalam folder public
        $file->move('klaim_cair', $nama_file);

        // // import data
        // Excel::import(new DataEklaimImport, public_path('/eklaim/' . $nama_file));

        // // notifikasi dengan session
        try {
            $data = Excel::toArray(new KlaimCairImport(), public_path('/klaim_cair/' . $nama_file));

            foreach ($data[0] as $dataEklaim) {
                $cek = KlaimCair::where('no_sep', $dataEklaim['no_sep'])
                    ->get();


                if ($cek->count() == '0') {

                    $simpan = new KlaimCair();
                    $simpan->tgl_verif = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dataEklaim['tgl_verif']);
                    $simpan->no_sep = $dataEklaim['no_sep'];
                    $simpan->riil = $dataEklaim['riil'];
                    $simpan->diajukan = $dataEklaim['diajukan'];
                    $simpan->disetujui = $dataEklaim['disetujui'];
                    $simpan->jenis_rawat = $dataEklaim['jenis_rawat'];
                    $simpan->save();
                }
            }


            // notifikasi dengan session
            Session::flash('sukses', 'Data Berhasil Diimport!');
        } catch (\Throwable $th) {

            // notifikasi dengan session
            Session::flash('error', 'Cek kembali data file Anda!');
        }

        // alihkan halaman kembali
        return redirect('/vedika/klaimcompare');
    }

    public function template()
    {
        //PDF file is stored under project/public/download/info.pdf
        $file = public_path() . "/klaim_cair/template_klaim_cair.xlsx";

        $headers = [
            'Content-Type' => 'application/xlsx',
        ];

        return response()->download($file, 'template_klaim_cair.xlsx', $headers);
    }
}
