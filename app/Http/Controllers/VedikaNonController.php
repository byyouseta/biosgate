<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class VedikaNonController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function rajal(Request $request)
    {
        session()->put('ibu', 'Vedika Non BPJS');
        session()->put('anak', 'Pasien Rajal');
        session()->forget('cucu');
        set_time_limit(0);

        if (isset($request->tanggalMulai)) {
            $tanggalMulai = $request->tanggalMulai;
            $tanggalSelesai = $request->tanggalSelesai;
        } else {
            $tanggalMulai = Carbon::now()->format('Y-m-d');
            $tanggalSelesai = Carbon::now()->format('Y-m-d');
        }

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
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
                'dokter.nm_dokter',
                'penjab.png_jawab'
                // 'diagnosa_pasien.kd_penyakit',
                // 'diagnosa_pasien.prioritas',
                // 'diagnosa_pasien.status',
                // 'penyakit.nm_penyakit'
            )
            ->where('reg_periksa.kd_pj', '!=', 'BPJ')
            ->where('reg_periksa.status_lanjut', '=', 'Ralan')
            ->where('reg_periksa.tgl_registrasi', '>=', $tanggalMulai)
            ->where('reg_periksa.tgl_registrasi', '<=', $tanggalSelesai)
            ->where('reg_periksa.stts', '!=', 'Batal')
            // ->where('diagnosa_pasien.prioritas', '=', 1)
            // ->where('diagnosa_pasien.status', '=', 'Ralan')
            ->get();

        // dd($data);

        return view('vedika_non.pasien_rajal', compact('data'));
    }

    public function detailRajal($id)
    {
        session()->put('ibu', 'Vedika Non BPJS');
        session()->put('anak', 'Pasien Rajal');
        session()->put('cucu', 'Detail');

        $id = Crypt::decrypt($id);

        $pasien = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.status_lanjut',
                'reg_periksa.kd_pj',
                'reg_periksa.stts',
                'reg_periksa.kd_dokter',
                'poliklinik.nm_poli',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pasien.alamat',
                'pasien.no_peserta',
                'dokter.nm_dokter',
                'penjab.png_jawab'
            )
            ->where('reg_periksa.kd_pj', '!=', 'BPJ')
            ->where('reg_periksa.status_lanjut', '=', 'Ralan')
            ->where('reg_periksa.no_rawat', '=', $id)
            ->first();

        // dd($pasien);
        if ($pasien) {
            //Ambil data billing
            $billing = VedikaController::billingRajal($pasien->no_rawat);
            //Ambil data untuk Bukti Pelayanan
            $buktiPelayanan = VedikaController::buktiPelayanan($pasien->no_rawat);
            $diagnosa = $buktiPelayanan[0];
            $prosedur = $buktiPelayanan[1];
            //Ambil data Radiologi
            $radiologi = VedikaController::radioRajal($pasien->no_rawat);
            // dd($radiologi);
            $dataRadiologiRajal = $radiologi[0];
            $dokterRadiologiRajal = $radiologi[1];
            $hasilRadiologiRajal = $radiologi[2];

            //Ambil Berkas tambahan
            $berkas = VedikaController::berkas($pasien->no_rawat);
            $dataBerkas = $berkas[0];
            $masterBerkas = $berkas[1];
            $pathBerkas = $berkas[2];
            //Data Lab
            $lab = VedikaController::lab($pasien->no_rawat);
            $permintaanLab = $lab[0];
            $hasilLab = $lab[1];
            $kesanLab = $lab[2];
            //Data Obat
            $obat = VedikaController::obat($pasien->no_rawat);
            $resepObat = $obat[0];
            $obatJadi = $obat[1];
            $obatRacik = $obat[2];
            $bbPasien = $obat[3];

            return view('vedika_non.detailRajal', compact(
                'pasien',
                'billing',
                'diagnosa',
                'prosedur',
                'permintaanLab',
                'hasilLab',
                'kesanLab',
                'dataRadiologiRajal',
                'dokterRadiologiRajal',
                'hasilRadiologiRajal',
                'resepObat',
                'obatJadi',
                'obatRacik',
                'bbPasien',
                'dataBerkas',
                'masterBerkas',
                'pathBerkas'
            ));
        } else {
            Session::flash('error', 'Data Pasien tidak ditemukan');
            return redirect()->back();
        }
    }

    public function detailRajalPdf($id)
    {
        $id = Crypt::decrypt($id);
        // mengambil data id rapat
        $pasien = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.status_lanjut',
                'reg_periksa.kd_pj',
                'reg_periksa.stts',
                'reg_periksa.kd_dokter',
                'poliklinik.nm_poli',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pasien.alamat',
                'pasien.no_peserta',
                'dokter.nm_dokter',
                'penjab.png_jawab'
            )
            ->where('reg_periksa.kd_pj', '!=', 'BPJ')
            ->where('reg_periksa.status_lanjut', '=', 'Ralan')
            ->where('reg_periksa.no_rawat', '=', $id)
            ->first();

        //Ambil data billing
        $billing = VedikaController::billingRajal($pasien->no_rawat);
        //Ambil data untuk Bukti Pelayanan
        $buktiPelayanan = VedikaController::buktiPelayanan($pasien->no_rawat);
        $diagnosa = $buktiPelayanan[0];
        $prosedur = $buktiPelayanan[1];
        //Ambil data Radiologi
        $radiologi = VedikaController::radioRajal($pasien->no_rawat);
        $dataRadiologiRajal = $radiologi[0];
        $dokterRadiologiRajal = $radiologi[1];
        $hasilRadiologiRajal = $radiologi[2];
        //Ambil Berkas tambahan
        $berkas = VedikaController::berkas($pasien->no_rawat);
        $dataBerkas = $berkas[0];
        $masterBerkas = $berkas[1];
        $pathBerkas = $berkas[2];
        //Data Lab
        $lab = VedikaController::lab($pasien->no_rawat);
        $permintaanLab = $lab[0];
        $hasilLab = $lab[1];
        //Data Obat
        $obat = VedikaController::obat($pasien->no_rawat);
        $resepObat = $obat[0];
        $obatJadi = $obat[1];
        $obatRacik = $obat[2];
        $bbPasien = $obat[3];


        $pdf = Pdf::loadView('vedika_non.detailRajal_pdf', [
            'pasien' => $pasien,
            'billing' => $billing,
            'diagnosa' => $diagnosa,
            'prosedur' => $prosedur,
            'permintaanLab' => $permintaanLab,
            'hasilLab' => $hasilLab,
            'dataRadiologiRajal' => $dataRadiologiRajal,
            'dokterRadiologiRajal' => $dokterRadiologiRajal,
            'hasilRadiologiRajal' => $hasilRadiologiRajal,
            'resepObat' => $resepObat,
            'obatJadi' => $obatJadi,
            'obatRacik' => $obatRacik,
            'bbPasien' => $bbPasien
        ]);

        // (Optional) Setup the paper size and orientation
        $pdf->setPaper('A4', 'potraid');

        return $pdf->stream();
    }

    public function ranap(Request $request)
    {
        session()->put('ibu', 'Vedika Non BPJS');
        session()->put('anak', 'Pasien Ranap');
        session()->forget('cucu');

        if (isset($request->tanggalMulai)) {
            $tanggalMulai = $request->tanggalMulai;
            $tanggalSelesai = $request->tanggalSelesai;
        } else {
            $tanggalMulai = Carbon::now()->format('Y-m-d');
            $tanggalSelesai = Carbon::now()->format('Y-m-d');
        }

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('dpjp_ranap', 'dpjp_ranap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('dokter', 'dokter.kd_dokter', '=', 'dpjp_ranap.kd_dokter')
            // ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            // ->leftJoin('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
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
                'poliklinik.nm_poli',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.no_peserta',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pasien.alamat',
                'dokter.nm_dokter'
                // 'diagnosa_pasien.kd_penyakit',
                // 'diagnosa_pasien.prioritas',
                // 'diagnosa_pasien.status',
                // 'penyakit.nm_penyakit'
            )
            ->where('reg_periksa.kd_pj', '!=', 'BPJ')
            ->where('reg_periksa.status_lanjut', '=', 'Ranap')
            ->where('reg_periksa.tgl_registrasi', '>=', $tanggalMulai)
            ->where('reg_periksa.tgl_registrasi', '<=', $tanggalSelesai)
            // ->where('diagnosa_pasien.status', '=', 'Ranap')
            // ->where('diagnosa_pasien.prioritas', '=', 1)
            ->get();

        return view('vedika_non.pasien_ranap', compact('data'));
    }

    public function detailRanap($id)
    {
        session()->put('ibu', 'Vedika Non BPJS');
        session()->put('anak', 'Pasien Ranap');
        session()->put('cucu', 'Detail');

        $id = Crypt::decrypt($id);

        $pasien = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
            ->join('dpjp_ranap', 'dpjp_ranap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->leftJoin('dokter', 'dokter.kd_dokter', '=', 'dpjp_ranap.kd_dokter')
            ->leftJoin('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
            ->leftJoin('bangsal', 'bangsal.kd_bangsal', '=', 'kamar.kd_bangsal')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.status_lanjut',
                'reg_periksa.kd_pj',
                'reg_periksa.stts',
                'reg_periksa.kd_dokter',
                'poliklinik.nm_poli',
                'kamar.kd_kamar',
                'kamar_inap.tgl_masuk',
                'kamar_inap.jam_masuk',
                'bangsal.nm_bangsal',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pasien.alamat',
                'pasien.no_peserta',
                'dokter.nm_dokter',
                'penjab.png_jawab',
                DB::raw("CONCAT(kamar_inap.tgl_masuk,' ',kamar_inap.jam_masuk) AS waktu_masuk_ranap")
            )
            ->where('reg_periksa.status_lanjut', '=', 'Ranap')
            ->where('reg_periksa.no_rawat', '=', $id)
            ->orderBy('waktu_masuk_ranap', 'DESC')
            ->first();

        // dd($pasien);
        if ($pasien) {
            //Ambil data billing
            $billing = VedikaController::billingRanap($pasien->no_rawat);
            //Ambil data untuk Bukti Pelayanan
            $buktiPelayanan = VedikaController::buktiPelayanan($pasien->no_rawat);
            $diagnosa = $buktiPelayanan[0];
            $prosedur = $buktiPelayanan[1];
            //Ambil data Radiologi
            $dataRadiologi = VedikaController::radioRanap($pasien->no_rawat);
            $dataRadiologiRanap = $dataRadiologi[0];
            $dokterRadiologiRanap = $dataRadiologi[1];
            $hasilRadiologiRanap = $dataRadiologi[2];
            $radiologi = VedikaController::radioRajal($pasien->no_rawat);
            $dataRadiologiRajal = $radiologi[0];
            $dokterRadiologiRajal = $radiologi[1];
            $hasilRadiologiRajal = $radiologi[2];
            // dd($pasien, $dataRadiologiRanap, $dokterRadiologiRanap, $hasilRadiologiRanap);

            //Ambil Berkas tambahan
            $berkas = VedikaController::berkas($pasien->no_rawat);
            // dd($berkas);

            $dataBerkas = $berkas[0];
            $masterBerkas = $berkas[1];
            $pathBerkas = $berkas[2];
            //Data Lab
            $lab = VedikaController::lab($pasien->no_rawat);
            $permintaanLab = $lab[0];
            $hasilLab = $lab[1];
            $kesanLab = $lab[2];

            //Resume Ranap
            $resumeRanap = VedikaController::getResumeRanap($pasien->no_rawat);
            if ($resumeRanap) {
                $resumeRanap1 = $resumeRanap[0] ? $resumeRanap[0] : null;
                $resumeRanap2 = $resumeRanap[1] ? $resumeRanap[1] : null;
                $resumeRanap3 = $resumeRanap[2] ? $resumeRanap[2] : null;
                $resumeRanap4 = $resumeRanap[3] ? $resumeRanap[3] : null;
            } else {
                $resumeRanap1 = null;
                $resumeRanap2 = null;
                $resumeRanap3 = null;
                $resumeRanap4 = null;
            }


            return view('vedika_non.detailRanap', compact(
                'pasien',
                'billing',
                'diagnosa',
                'prosedur',
                'permintaanLab',
                'hasilLab',
                'kesanLab',
                'dataRadiologiRanap',
                'dokterRadiologiRanap',
                'hasilRadiologiRanap',
                'dataRadiologiRajal',
                'dokterRadiologiRajal',
                'hasilRadiologiRajal',
                'dataBerkas',
                'masterBerkas',
                // 'spri',
                'resumeRanap1',
                'resumeRanap2',
                'resumeRanap3',
                'resumeRanap4',
                'pathBerkas'
            ));
        } else {
            Session::flash('error', 'Data Pasien tidak ditemukan');
            return redirect()->back();
        }
    }

    public function detailRanapPdf($id)
    {
        $id = Crypt::decrypt($id);
        // mengambil data id rapat
        $pasien = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
            ->join('dpjp_ranap', 'dpjp_ranap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->leftJoin('dokter', 'dokter.kd_dokter', '=', 'dpjp_ranap.kd_dokter')
            ->leftJoin('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
            ->leftJoin('bangsal', 'bangsal.kd_bangsal', '=', 'kamar.kd_bangsal')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.status_lanjut',
                'reg_periksa.kd_pj',
                'reg_periksa.stts',
                'reg_periksa.kd_dokter',
                'poliklinik.nm_poli',
                'kamar.kd_kamar',
                'kamar_inap.tgl_masuk',
                'kamar_inap.jam_masuk',
                'bangsal.nm_bangsal',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pasien.alamat',
                'pasien.no_peserta',
                'dokter.nm_dokter',
                'penjab.png_jawab',
                DB::raw("CONCAT(kamar_inap.tgl_masuk,' ',kamar_inap.jam_masuk) AS waktu_masuk_ranap")
            )
            ->where('reg_periksa.status_lanjut', '=', 'Ranap')
            ->where('reg_periksa.no_rawat', '=', $id)
            ->orderBy('waktu_masuk_ranap', 'DESC')
            ->first();

        // dd($pasien);
        //Ambil data billing
        $billing = VedikaController::billingRanap($pasien->no_rawat);
        //Ambil data untuk Bukti Pelayanan
        $buktiPelayanan = VedikaController::buktiPelayanan($pasien->no_rawat);
        $diagnosa = $buktiPelayanan[0];
        $prosedur = $buktiPelayanan[1];
        //Ambil data Radiologi
        $dataRadiologi = VedikaController::radioRanap($pasien->no_rawat);
        $dataRadiologiRanap = $dataRadiologi[0];
        $dokterRadiologiRanap = $dataRadiologi[1];
        $hasilRadiologiRanap = $dataRadiologi[2];
        $radiologi = VedikaController::radioRajal($pasien->no_rawat);
        $dataRadiologiRajal = $radiologi[0];
        $dokterRadiologiRajal = $radiologi[1];
        $hasilRadiologiRajal = $radiologi[2];



        //Data Lab
        $lab = VedikaController::lab($pasien->no_rawat);
        $permintaanLab = $lab[0];
        $hasilLab = $lab[1];
        $kesanLab = $lab[2];


        //Resume Ranap
        $resumeRanap = VedikaController::getResumeRanap($pasien->no_rawat);
        if ($resumeRanap) {
            $resumeRanap1 = $resumeRanap[0];
            $resumeRanap2 = $resumeRanap[1];
            $resumeRanap3 = $resumeRanap[2];
            $resumeRanap4 = $resumeRanap[3];
        } else {
            $resumeRanap1 = null;
            $resumeRanap2 = null;
            $resumeRanap3 = null;
            $resumeRanap4 = null;
        }

        $pdf = Pdf::loadView('vedika_non.detailRanap_pdf', [
            'pasien' => $pasien,
            'billing' => $billing,
            'diagnosa' => $diagnosa,
            'prosedur' => $prosedur,
            'permintaanLab' => $permintaanLab,
            'hasilLab' => $hasilLab,
            'kesanLab' => $kesanLab,
            'dataRadiologiRajal' => $dataRadiologiRajal,
            'dokterRadiologiRajal' => $dokterRadiologiRajal,
            'hasilRadiologiRajal' => $hasilRadiologiRajal,
            'dataRadiologiRanap' => $dataRadiologiRanap,
            'dokterRadiologiRanap' => $dokterRadiologiRanap,
            'hasilRadiologiRanap' => $hasilRadiologiRanap,
            // 'spri' => $spri,
            'resumeRanap1' => $resumeRanap1,
            'resumeRanap2' => $resumeRanap2,
            'resumeRanap3' => $resumeRanap3,
            'resumeRanap4' => $resumeRanap4
        ]);

        // (Optional) Setup the paper size and orientation
        $pdf->setPaper('A4', 'potraid');

        return $pdf->stream();
    }
}
