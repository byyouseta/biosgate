<?php

namespace App\Http\Controllers;

use App\Pengaduan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Lunaweb\RecaptchaV3\Facades\RecaptchaV3;

class SurveiController extends Controller
{
    public function index()
    {
        session()->put('ibu', 'Survei');
        session()->forget('anak');
        session()->forget('cucu');

        return view('survei.main');
    }

    public function pengaduan()
    {
        session()->put('ibu', 'Survei');
        session()->put('anak', 'Pengaduan');
        session()->forget('cucu');

        return view('survei.pengaduan');
    }

    public function showTicket($id)
    {
        $no_tiket = Crypt::decrypt($id);
        $data = Pengaduan::where('no_tiket', $no_tiket)->first();

        if (empty(session('cucu'))) {
            Session::flash('sukses', 'Data Berhasil disimpan!');
            // session()->forget('anak');

        } elseif (session('cucu') == 'Periksa') {
            Session::flash('sukses', 'Data ditemukan');
        }

        return view('survei.tiket', compact('data'));
    }

    public function periksa()
    {
        session()->put('ibu', 'Survei');
        session()->put('anak', 'Pengaduan');
        session()->put('cucu', 'Periksa');

        return view('survei.periksa');
    }

    public function periksaTiket(Request $request)
    {
        session()->put('ibu', 'Survei');
        session()->put('anak', 'Pengaduan');
        session()->put('cucu', 'Periksa');

        $this->validate($request, [
            'no_tiket' => 'required|digits_between:0,12',
            'g-recaptcha-response' => 'required|recaptchav3:register,0.5'
        ], [
            'no_tiket.required' => 'Kolom No Tiket belum diisi',
            'no_tiket.digits_between' => 'Tiket harus berbentuk urutan angka 12 digits',
        ]);


        $cek = Pengaduan::where('no_tiket', $request->no_tiket)->first();

        if (!empty($cek)) {
            $no_tiket = $cek->no_tiket;
            $id = Crypt::encrypt($no_tiket);
            return redirect("/survei/$id/tiket");
        } else {
            Session::flash('error', 'Tiket tidak ditemukan');

            return redirect()->back()->withInput();
        }
    }

    public function store(Request $request)
    {

        $this->validate($request, [
            'nama' => 'required',
            'no_hp' => 'required',
            'penerima_keluhan' => 'required',
            'punya_norm' => 'required',
            'no_rm' => 'required_if:punya_norm,1|digits_between:0,8',
            'nama_pasien' => 'required_if:punya_norm,1',
            'lahir_pasien' => 'required_if:punya_norm,1',
            'pembiayaan' => 'required',
            'tgl_kejadian' => 'required',
            'jam_kejadian' => 'required',
            'deskripsi' => 'required',
            'g-recaptcha-response' => 'required|recaptchav3:register,0.5'
        ], [
            'nama.required' => 'Kolom Nama Pelapor belum diisi',
            'no_hp.required' => 'Kolom No HP/ Whatsapp belum diisi',
            'penerima_keluhan.required' => 'Kolom Penerima Keluhan belum diisi',
            'punya_norm.required' => 'Silahkan check list kepemilikan No Rekam Medis',
            'no_rm.required_if' => 'Kolom No Rekam Medis belum diisi',
            'nama_pasien.required_if' => 'Kolom Nama Pasien belum diisi',
            'nama_pasien.string' => 'Periksa kembali Kolom Nama Pasien',
            'lahir_pasien.required_if' => 'Kolom Tanggal Lahir Pasien belum diisi',
            'tgl_kejadian.required' => 'Kolom Tanggal Kejadian Pasien belum diisi',
            'jam_kejadian.required' => 'Kolom Jam Kejadian Pasien belum diisi',
            'pembiayaan.required' => 'Data Pembiayaan belum dipilih',
            'deskripsi.required' => 'Kolom Detail Kejadian belum diisi',
        ]);

        $hari_ini = Carbon::now();
        $prefiks = $hari_ini->format('Ymd');
        $cek = Pengaduan::where('no_tiket', 'like', "$prefiks%")
            ->count();
        $urutan = $cek + 1;

        $no_tiket = $prefiks . str_pad($urutan, 4, "0", STR_PAD_LEFT);
        // dd($cek, $urutan, $no_tiket);

        $waktu_kejadian = "$request->tgl_kejadian $request->jam_kejadian";
        // dd($request, $waktu_kejadian);

        $simpan = new Pengaduan();
        $simpan->nama = $request->nama;
        $simpan->no_hp = $request->no_hp;
        $simpan->email = $request->email;
        $simpan->penerima = $request->penerima_keluhan;
        $simpan->punya_rm = $request->punya_norm;
        $simpan->no_rm = $request->no_rm;
        $simpan->nama_pasien = $request->nama_pasien;
        $simpan->lahir_pasien = $request->lahir_pasien;
        $simpan->pembiayaan = $request->pembiayaan;
        $simpan->tempat_kejadian = $request->tempat_kejadian;
        $simpan->waktu_kejadian = $waktu_kejadian;
        $simpan->pendaftaran_online = $request->pendaftaran_online != null ? $request->pendaftaran_online : '0';
        $simpan->pendaftaran_rajal = $request->pendaftaran_rajal != null ? $request->pendaftaran_rajal : '0';
        $simpan->pendaftaran_ranap = $request->pendaftaran_ranap != null ? $request->pendaftaran_ranap : '0';
        $simpan->pendaftaran_igd = $request->pendaftaran_igd != null ? $request->pendaftaran_igd : '0';
        $simpan->admin_bpjs = $request->admin_bpjs != null ? $request->admin_bpjs : '0';
        $simpan->petugas_dr_sp = $request->petugas_dr_sp != null ? $request->petugas_dr_sp : '0';
        $simpan->petugas_dr_umum = $request->petugas_dr_umum != null ? $request->petugas_dr_umum : '0';
        $simpan->petugas_dr_gigi = $request->petugas_dr_gigi != null ? $request->petugas_dr_gigi : '0';
        $simpan->petugas_perawat = $request->petugas_perawat != null ? $request->petugas_perawat : '0';
        $simpan->petugas_bidan = $request->petugas_bidan != null ? $request->petugas_bidan : '0';
        $simpan->petugas_psikolog = $request->petugas_psikolog != null ? $request->petugas_psikolog : '0';
        $simpan->petugas_apoteker = $request->petugas_apoteker != null ? $request->petugas_apoteker : '0';
        $simpan->petugas_radiografer = $request->petugas_radiografer != null ? $request->petugas_radiografer : '0';
        $simpan->petugas_fisioterapi = $request->petugas_fisioterapi != null ? $request->petugas_fisioterapi : '0';
        $simpan->petugas_konselor = $request->petugas_konselor != null ? $request->petugas_konselor : '0';
        $simpan->petugas_ahli_gizi = $request->petugas_ahli_gizi != null ? $request->petugas_ahli_gizi : '0';
        $simpan->petugas_administrasi = $request->petugas_administrasi != null ? $request->petugas_administrasi : '0';
        $simpan->petugas_kebersihan = $request->petugas_kebersihan != null ? $request->petugas_kebersihan : '0';
        $simpan->petugas_parkir = $request->petugas_parkir != null ? $request->petugas_parkir : '0';
        $simpan->petugas_lainnya = $request->petugas_lainnya != null ? $request->petugas_lainnya : '0';
        $simpan->petugas_satpam = $request->petugas_satpam != null ? $request->petugas_satpam : '0';
        $simpan->petugas_kasir = $request->petugas_kasir != null ? $request->petugas_kasir : '0';
        $simpan->petugas_rohaniawan = $request->petugas_rohaniawan != null ? $request->petugas_rohaniawan : '0';
        $simpan->layanan_poli_reg = $request->layanan_poli_reg != null ? $request->layanan_poli_reg : '0';
        $simpan->layanan_poli_eks = $request->layanan_poli_eks != null ? $request->layanan_poli_eks : '0';
        $simpan->layanan_ranap = $request->layanan_ranap != null ? $request->layanan_ranap : '0';
        $simpan->layanan_igd = $request->layanan_igd != null ? $request->layanan_igd : '0';
        $simpan->layanan_icu = $request->layanan_icu != null ? $request->layanan_icu : '0';
        $simpan->layanan_farmasi = $request->layanan_farmasi != null ? $request->layanan_farmasi : '0';
        $simpan->layanan_jenazah = $request->layanan_jenazah != null ? $request->layanan_jenazah : '0';
        $simpan->layanan_lab = $request->layanan_lab != null ? $request->layanan_lab : '0';
        $simpan->layanan_mcu = $request->layanan_mcu != null ? $request->layanan_mcu : '0';
        $simpan->layanan_hemodialisa = $request->layanan_hemodialisa != null ? $request->layanan_hemodialisa : '0';
        $simpan->layanan_fisioterapi = $request->layanan_fisioterapi != null ? $request->layanan_fisioterapi : '0';
        $simpan->layanan_radioterapi = $request->layanan_radioterapi != null ? $request->layanan_radioterapi : '0';
        $simpan->layanan_radiologi = $request->layanan_radiologi != null ? $request->layanan_radiologi : '0';
        $simpan->layanan_lainnya = $request->layanan_lainnya != null ? $request->layanan_lainnya : '0';
        $simpan->fasilitas_parkir = $request->fasilitas_parkir != null ? $request->fasilitas_parkir : '0';
        $simpan->fasilitas_taman = $request->fasilitas_taman != null ? $request->fasilitas_taman : '0';
        $simpan->fasilitas_ambulan = $request->fasilitas_ambulan != null ? $request->fasilitas_ambulan : '0';
        $simpan->fasilitas_toilet = $request->fasilitas_toilet != null ? $request->fasilitas_toilet : '0';
        $simpan->fasilitas_tunggu = $request->fasilitas_tunggu != null ? $request->fasilitas_tunggu : '0';
        $simpan->fasilitas_lainnya = $request->fasilitas_lainnya != null ? $request->fasilitas_lainnya : '0';
        $simpan->deskripsi = $request->deskripsi;
        $simpan->nilai_gangguan = $request->nilai_gangguan;
        $simpan->no_tiket = $no_tiket;
        $simpan->status_keluhan_id = 0;
        $simpan->save();

        $id = Crypt::encrypt($no_tiket);

        return redirect("survei/$id/tiket");
    }
}
