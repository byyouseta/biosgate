<?php

namespace App\Http\Controllers;

use App\Dokter;
use App\GeneralConsent;
use App\HakKewajibanPasien;
use App\PersetujuanRawatInap;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class BerkasRmController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function rajal(Request $request)
    {
        session()->put('ibu', 'Berkas RM');
        session()->put('anak', 'Rawat Jalan/IGD');
        session()->forget('cucu');

        if (empty($request->get('tanggal'))) {
            $tanggal = Carbon::now();
        } else {
            $tanggal = new Carbon($request->get('tanggal'));
        }

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_dokter',
                'reg_periksa.status_lanjut',
                'reg_periksa.stts',
                'reg_periksa.kd_poli',
                'reg_periksa.kd_pj',
                'penjab.png_jawab as cara_bayar',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'poliklinik.nm_poli'
            )
            ->where('reg_periksa.status_lanjut', 'Ralan')
            ->whereDate('reg_periksa.tgl_registrasi', $tanggal)
            ->get();

        return view('berkas_rm.rajal', compact('data'));
    }

    public function ranap(Request $request)
    {
        session()->put('ibu', 'Berkas RM');
        session()->put('anak', 'Rawat Inap');
        session()->forget('cucu');

        if (empty($request->get('tanggal'))) {
            $tanggal = Carbon::now();
        } else {
            $tanggal = new Carbon($request->get('tanggal'));
        }

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_dokter',
                'reg_periksa.status_lanjut',
                'reg_periksa.stts',
                'reg_periksa.kd_poli',
                'reg_periksa.kd_pj',
                'penjab.png_jawab as cara_bayar',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'poliklinik.nm_poli'
            )
            ->where('reg_periksa.status_lanjut', 'Ranap')
            // ->where('reg_periksa.stts', 'Sudah')
            ->whereDate('reg_periksa.tgl_registrasi', $tanggal)
            ->get();

        return view('berkas_rm.rajal', compact('data'));
    }

    public function kewajiban($id)
    {
        $id = Crypt::decrypt($id);

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_dokter',
                'reg_periksa.status_lanjut',
                'reg_periksa.stts',
                'reg_periksa.kd_poli',
                'reg_periksa.kd_pj',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pasien.alamat',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'poliklinik.nm_poli'
            )
            // ->where('reg_periksa.status_lanjut', 'Ralan')
            ->where('reg_periksa.no_rawat', $id)
            ->first();

        $berkas = HakKewajibanPasien::where('noRawat', $id)
            ->first();

        $fileSftp =  DB::connection('mysqlkhanza')->table('berkas_digital_perawatan')
            ->where('kode', '057')
            ->where('no_rawat', $id)
            ->first();

        if (empty($berkas)) {
            return view('berkas_rm.check_kewajiban', compact('data', 'berkas', 'fileSftp'));
        } else {
            return view('berkas_rm.edit_kewajiban', compact('data', 'berkas', 'fileSftp'));
        }
    }

    public function hakKewajibanPdf($id)
    {
        $id = Crypt::decrypt($id);

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_dokter',
                'reg_periksa.status_lanjut',
                'reg_periksa.stts',
                'reg_periksa.kd_poli',
                'reg_periksa.kd_pj',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pasien.alamat',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'poliklinik.nm_poli'
            )
            // ->where('reg_periksa.status_lanjut', 'Ralan')
            ->where('reg_periksa.no_rawat', $id)
            ->first();

        $berkas = HakKewajibanPasien::where('noRawat', $id)
            ->first();

        $pdf = Pdf::loadView('berkas_rm.hakkewajiban_pdf', [
            'data' => $data,
            'berkas' => $berkas
        ]);

        // (Optional) Setup the paper size and orientation
        $pdf->setPaper('A4', 'potraid');

        // $pdf->setOptions(['isRemoteEnabled' => true]);

        // Render the HTML as PDF
        //$pdf->render();
        //Watermark
        // $pdf->setPaper('L');
        // $pdf->output();
        // $canvas = $pdf->getDomPDF()->getCanvas();

        // $height = $canvas->get_height();
        // $width = $canvas->get_width();

        // $canvas->set_opacity(.2, "Multiply");

        // $canvas->set_opacity(.2);

        // $canvas->page_text(
        //     $width / 5,
        //     $height / 2,
        //     'VedikaRSUPGate',
        //     null,
        //     55,
        //     array(0, 0, 0),
        //     2,
        //     2,
        //     -30
        // );

        return $pdf->stream();
    }

    public function hakKewajibanSend($id)
    {
        $id = Crypt::decrypt($id);

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_dokter',
                'reg_periksa.status_lanjut',
                'reg_periksa.stts',
                'reg_periksa.kd_poli',
                'reg_periksa.kd_pj',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pasien.alamat',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'poliklinik.nm_poli'
            )
            // ->where('reg_periksa.status_lanjut', 'Ralan')
            ->where('reg_periksa.no_rawat', $id)
            ->first();

        $berkas = HakKewajibanPasien::where('noRawat', $id)
            ->first();

        $pdf = Pdf::loadView('berkas_rm.hakkewajiban_pdf', [
            'data' => $data,
            'berkas' => $berkas
        ]);

        // (Optional) Setup the paper size and orientation
        $pdf->setPaper('A4', 'potraid');

        // $folder = storage_path('app/temp');

        // if (!file_exists($folder)) {
        //     mkdir($folder, 0755, true);
        // }

        // $namaFile = $data->no_rawat . '_hak_kewajiban.pdf';

        // $pathFile = $folder . '/' . $namaFile;

        // $pdf->save($pathFile);

        $master =  DB::connection('mysqlkhanza')->table('master_berkas_digital')
            ->where('kode', '057')
            ->first();

        $nama_master = preg_replace('/[\/\\\\\?\*\:\|\"\<\>\s]/', '_', $master->nama);

        //'Hak_Kewajiban_Pasien';

        $tgl_registrasi = Carbon::parse($data->tgl_registrasi)->format('Ymd');
        $waktu_upload = Carbon::now()->format('His');

        $nama_file = $tgl_registrasi . '_' .
            $waktu_upload . '_' .
            substr($data->no_rawat, -6) . '_' .
            $nama_master . '.pdf';

        //============================
        // Simpan ke storage lokal
        //============================
        $localPath = 'temp/' . $nama_file;

        Storage::disk('local')->put(
            $localPath,
            $pdf->output()
        );

        //============================
        // Upload ke SFTP
        //============================
        $file = Storage::disk('local')->get($localPath);

        $move = Storage::disk('sftp')->put(
            'pages/upload/' . $nama_file,
            $file
        );

        if ($move) {
            Session::flash('sukses', 'File berhasil diupload ke server!');

            //============================
            // Hapus file lokal
            //============================
            Storage::disk('local')->delete($localPath);

            //============================
            // Simpan ke database
            //============================
            $dataInsert = [];

            $dataInsert['no_rawat'] = $data->no_rawat;
            $dataInsert['kode'] = '057'; // atau kode master berkas
            $dataInsert['lokasi_file'] = 'pages/upload/' . $nama_file;

            DB::connection('mysqlkhanza')
                ->table('berkas_digital_perawatan')
                ->insert($dataInsert);
        } else {
            Session::flash('gagal', 'File gagal diupload ke server!');
        }

        return redirect()->back();
    }

    public function viewHakKewajiban($id)
    {
        $noRawat = Crypt::decrypt($id);

        $berkas = DB::connection('mysqlkhanza')
            ->table('berkas_digital_perawatan')
            ->where('no_rawat', $noRawat)
            ->where('kode', '057') // sesuaikan kode master
            ->first();

        if (!$berkas) {
            abort(404, 'File tidak ditemukan');
        }

        if (!Storage::disk('sftp')->exists($berkas->lokasi_file)) {
            abort(404, 'File tidak ditemukan di SFTP');
        }

        $file = Storage::disk('sftp')->get($berkas->lokasi_file);

        return response($file, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . basename($berkas->lokasi_file) . '"');
    }

    public function generalPdf($id)
    {
        $id = Crypt::decrypt($id);

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_dokter',
                'reg_periksa.status_lanjut',
                'reg_periksa.stts',
                'reg_periksa.kd_poli',
                'reg_periksa.kd_pj',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pasien.alamat',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'poliklinik.nm_poli'
            )
            // ->where('reg_periksa.status_lanjut', 'Ralan')
            ->where('reg_periksa.no_rawat', $id)
            ->first();

        $berkas = GeneralConsent::where('noRawat', $id)
            ->first();
        $dokter = Dokter::where('nm_dokter', 'like', '%dr%')
            ->where('status', '=', '1')
            ->orderBy('nm_dokter')
            ->get();

        $pdf = Pdf::loadView('berkas_rm.general_pdf', [
            'data' => $data,
            'berkas' => $berkas,
            'dokter' => $dokter
        ]);

        // (Optional) Setup the paper size and orientation
        $pdf->setPaper('A4', 'potraid');

        // $pdf->setOptions(['isRemoteEnabled' => true]);

        // Render the HTML as PDF
        //$pdf->render();
        //Watermark
        // $pdf->setPaper('L');
        // $pdf->output();
        // $canvas = $pdf->getDomPDF()->getCanvas();

        // $height = $canvas->get_height();
        // $width = $canvas->get_width();

        // $canvas->set_opacity(.2, "Multiply");

        // $canvas->set_opacity(.2);

        // $canvas->page_text(
        //     $width / 5,
        //     $height / 2,
        //     'VedikaRSUPGate',
        //     null,
        //     55,
        //     array(0, 0, 0),
        //     2,
        //     2,
        //     -30
        // );

        return $pdf->stream();
    }

    public function hakkewajibanStore(Request $request)
    {
        $validatedData = $request->validate([
            'namaPj' => 'required|string|max:255',
            'signed' => 'required'
        ]);

        $cek = HakKewajibanPasien::where('noRawat', $request->noRawat)->first();

        if (empty($cek)) {
            $simpan = new HakKewajibanPasien();
            $simpan->noRawat = $request->noRawat;
            $simpan->hak1 = $request->hak1 ? $request->hak1 : false;
            $simpan->hak2 = $request->hak2 ? $request->hak2 : false;
            $simpan->hak3 = $request->hak3 ? $request->hak3 : false;
            $simpan->hak4 = $request->hak4 ? $request->hak4 : false;
            $simpan->hak5 = $request->hak5 ? $request->hak5 : false;
            $simpan->hak6 = $request->hak6 ? $request->hak6 : false;
            $simpan->hak7 = $request->hak7 ? $request->hak7 : false;
            $simpan->hak8 = $request->hak8 ? $request->hak8 : false;
            $simpan->hak9 = $request->hak9 ? $request->hak9 : false;
            $simpan->hak10 = $request->hak10 ? $request->hak10 : false;
            $simpan->hak11 = $request->hak11 ? $request->hak11 : false;
            $simpan->hak12 = $request->hak12 ? $request->hak12 : false;
            $simpan->hak13 = $request->hak13 ? $request->hak13 : false;
            $simpan->hak14 = $request->hak14 ? $request->hak14 : false;
            $simpan->hak15 = $request->hak15 ? $request->hak15 : false;
            $simpan->hak16 = $request->hak16 ? $request->hak16 : false;
            $simpan->hak17 = $request->hak17 ? $request->hak17 : false;
            $simpan->hak18 = $request->hak18 ? $request->hak18 : false;
            $simpan->kewajiban1 = $request->kewajiban1 ? $request->kewajiban1 : false;
            $simpan->kewajiban2 = $request->kewajiban2 ? $request->kewajiban2 : false;
            $simpan->kewajiban3 = $request->kewajiban3 ? $request->kewajiban3 : false;
            $simpan->kewajiban4 = $request->kewajiban4 ? $request->kewajiban4 : false;
            $simpan->kewajiban5 = $request->kewajiban5 ? $request->kewajiban5 : false;
            $simpan->kewajiban6 = $request->kewajiban6 ? $request->kewajiban6 : false;
            $simpan->kewajiban7 = $request->kewajiban7 ? $request->kewajiban7 : false;
            $simpan->kewajiban8 = $request->kewajiban8 ? $request->kewajiban8 : false;
            $simpan->statusPj = $request->statusPj;
            $simpan->namaPj = $request->namaPj;
            $simpan->tandaTangan = $request->signed;
            $simpan->user_id = Auth::user()->id;
            $simpan->save();
        }

        //simpaan di dbkhanza
        // $cek2 = DB::connection('mysqlkhanza')->table('hak_kewajiban_pasiens')
        //     ->where('noRawat', $request->noRawat)->first();

        // if (empty($cek2)) {
        //     DB::connection('mysqlkhanza')->table('hak_kewajiban_pasiens')->insert([
        //         'noRawat' => $request->noRawat,
        //         'hak1' => $request->hak1 ? $request->hak1 : false,
        //         'hak2' => $request->hak2 ? $request->hak1 : false,
        //         'hak3' => $request->hak3 ? $request->hak1 : false,
        //         'hak4' => $request->hak4 ? $request->hak1 : false,
        //         'hak5' => $request->hak5 ? $request->hak1 : false,
        //         'hak6' => $request->hak6 ? $request->hak1 : false,
        //         'hak7' => $request->hak7 ? $request->hak1 : false,
        //         'hak8' => $request->hak8 ? $request->hak1 : false,
        //         'hak9' => $request->hak9 ? $request->hak1 : false,
        //         'hak10' => $request->hak10 ? $request->hak1 : false,
        //         'hak11' => $request->hak11 ? $request->hak1 : false,
        //         'hak12' => $request->hak12 ? $request->hak1 : false,
        //         'hak13' => $request->hak13 ? $request->hak1 : false,
        //         'hak14' => $request->hak14 ? $request->hak1 : false,
        //         'hak15' => $request->hak15 ? $request->hak1 : false,
        //         'hak16' => $request->hak16 ? $request->hak1 : false,
        //         'hak17' => $request->hak17 ? $request->hak1 : false,
        //         'hak18' => $request->hak18 ? $request->hak1 : false,
        //         'kewajiban1' => $request->kewajiban1 ? $request->kewajiban1 : false,
        //         'kewajiban2' => $request->kewajiban2 ? $request->kewajiban2 : false,
        //         'kewajiban3' => $request->kewajiban3 ? $request->kewajiban3 : false,
        //         'kewajiban4' => $request->kewajiban4 ? $request->kewajiban4 : false,
        //         'kewajiban5' => $request->kewajiban5 ? $request->kewajiban5 : false,
        //         'kewajiban6' => $request->kewajiban6 ? $request->kewajiban6 : false,
        //         'kewajiban7' => $request->kewajiban7 ? $request->kewajiban7 : false,
        //         'kewajiban8' => $request->kewajiban8 ? $request->kewajiban8 : false,
        //         'statusPj' => $request->statusPj,
        //         'namaPj' => $request->namaPj,
        //         'tandaTangan' => $request->signed,
        //         'user_id' => Auth::user()->username
        //     ]);
        // }

        Session::flash('sukses', 'Data Berhasil disimpan!');

        return redirect()->back();
    }

    public function hakkewajibanEdit(Request $request)
    {
        $validatedData = $request->validate([
            'namaPj' => 'required|string|max:255',
            'statusPj' => 'required',
        ]);

        $edit = HakKewajibanPasien::where('noRawat', $request->noRawat)->first();

        if ($edit) {
            $edit->hak1 = $request->hak1;
            $edit->hak2 = $request->hak2;
            $edit->hak3 = $request->hak3;
            $edit->hak4 = $request->hak4;
            $edit->hak5 = $request->hak5;
            $edit->hak6 = $request->hak6;
            $edit->hak7 = $request->hak7;
            $edit->hak8 = $request->hak8;
            $edit->hak9 = $request->hak9;
            $edit->hak10 = $request->hak10;
            $edit->hak11 = $request->hak11;
            $edit->hak12 = $request->hak12;
            $edit->hak13 = $request->hak13;
            $edit->hak14 = $request->hak14;
            $edit->hak15 = $request->hak15;
            $edit->hak16 = $request->hak16;
            $edit->hak17 = $request->hak17;
            $edit->hak18 = $request->hak18;
            $edit->kewajiban1 = $request->kewajiban1;
            $edit->kewajiban2 = $request->kewajiban2;
            $edit->kewajiban3 = $request->kewajiban3;
            $edit->kewajiban4 = $request->kewajiban4;
            $edit->kewajiban5 = $request->kewajiban5;
            $edit->kewajiban6 = $request->kewajiban6;
            $edit->kewajiban7 = $request->kewajiban7;
            $edit->kewajiban8 = $request->kewajiban8;
            $edit->statusPj = $request->statusPj;
            $edit->namaPj = $request->namaPj;
            if (!empty($request->signed)) {
                $edit->tandaTangan = $request->signed;
            }
            $edit->user_id = Auth::user()->id;
            $edit->save();
        }

        //edit di dbkhanza
        // $cek2 = DB::connection('mysqlkhanza')->table('hak_kewajiban_pasiens')
        //     ->where('noRawat', $request->noRawat)->first();
        // if ($cek2) {
        //     DB::connection('mysqlkhanza')->table('hak_kewajiban_pasiens')
        //         ->where('noRawat', $request->noRawat)
        //         ->update([
        //             'hak1' => $request->hak1,
        //             'hak2' => $request->hak2,
        //             'hak3' => $request->hak3,
        //             'hak4' => $request->hak4,
        //             'hak5' => $request->hak5,
        //             'hak6' => $request->hak6,
        //             'hak7' => $request->hak7,
        //             'hak8' => $request->hak8,
        //             'hak9' => $request->hak9,
        //             'hak10' => $request->hak10,
        //             'hak11' => $request->hak11,
        //             'hak12' => $request->hak12,
        //             'hak13' => $request->hak13,
        //             'hak14' => $request->hak14,
        //             'hak15' => $request->hak15,
        //             'hak16' => $request->hak16,
        //             'hak17' => $request->hak17,
        //             'hak18' => $request->hak18,
        //             'kewajiban1' => $request->kewajiban1,
        //             'kewajiban2' => $request->kewajiban2,
        //             'kewajiban3' => $request->kewajiban3,
        //             'kewajiban4' => $request->kewajiban4,
        //             'kewajiban5' => $request->kewajiban5,
        //             'kewajiban6' => $request->kewajiban6,
        //             'kewajiban7' => $request->kewajiban7,
        //             'kewajiban8' => $request->kewajiban8,
        //             'statusPj' => $request->statusPj,
        //             'namaPj' => $request->namaPj,
        //             'user_id' => Auth::user()->username,
        //             'tandaTangan' => !empty($request->signed) ? $request->signed : DB::raw('tandaTangan'), // retain original value if empty
        //         ]);
        // }

        Session::flash('sukses', 'Data Berhasil diperbaharui!');

        return redirect()->back();
    }

    public function delete($id)
    {
        $id = Crypt::decrypt($id);

        DB::beginTransaction();
        try {
            // Hapus file di SFTP jika ada
            $berkasDigital = DB::connection('mysqlkhanza')
                ->table('berkas_digital_perawatan')
                ->where('no_rawat', $id)
                ->where('kode', '057') // sesuaikan dengan kode master berkas
                ->first();
            if ($berkasDigital) {
                if (Storage::disk('sftp')->exists($berkasDigital->lokasi_file)) {
                    Storage::disk('sftp')->delete($berkasDigital->lokasi_file);
                }
                DB::connection('mysqlkhanza')
                    ->table('berkas_digital_perawatan')
                    ->where('no_rawat', $id)
                    ->where('kode', '057')
                    ->delete();
            }

            // Hapus data lokal
            $delete = HakKewajibanPasien::where('noRawat', $id)->first();

            if ($delete) {
                $delete->delete();
            }

            DB::commit();

            Session::flash('sukses', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('error', 'Data gagal dihapus. ' . $e->getMessage());
        }

        return redirect()->back();
    }

    public function generalConsent($id)
    {
        $id = Crypt::decrypt($id);

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_dokter',
                'reg_periksa.status_lanjut',
                'reg_periksa.stts',
                'reg_periksa.kd_poli',
                'reg_periksa.kd_pj',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pasien.alamat',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'poliklinik.nm_poli'
            )
            // ->where('reg_periksa.status_lanjut', 'Ralan')
            ->where('reg_periksa.no_rawat', $id)
            ->first();

        $berkas = GeneralConsent::where('noRawat', $id)
            ->first();

        $dokter = Dokter::where('nm_dokter', 'like', '%dr%')
            ->where('status', '=', '1')
            ->orderBy('nm_dokter')
            ->get();

        $fileSftp =  DB::connection('mysqlkhanza')->table('berkas_digital_perawatan')
            ->where('kode', '058')
            ->where('no_rawat', $id)
            ->first();

        if (empty($berkas)) {
            return view('berkas_rm.check_gc', compact('data', 'berkas', 'dokter', 'fileSftp'));
        } else {
            return view('berkas_rm.edit_gc', compact('data', 'berkas', 'dokter', 'fileSftp'));
        }
    }

    public function generalStore(Request $request)
    {
        $cek = GeneralConsent::where('noRawat', $request->noRawat)->first();

        if (empty($cek)) {
            $simpan = new GeneralConsent();
            $simpan->noRawat = $request->noRawat;
            $simpan->keyakinan1 = $request->keyakinan1 ?? null;
            $simpan->keyakinan2 = $request->keyakinan2 ?? null;
            $simpan->keyakinan3 = $request->keyakinan3 ?? null;
            $simpan->keyakinan4 = $request->keyakinan4 ?? null;
            $simpan->privasi1 = $request->privasi1 ?? null;
            $simpan->privasi2 = $request->privasi2 ?? null;
            $simpan->privasi3 = $request->privasi3 ?? null;
            $simpan->tglLahirPj = $request->tanggalLahirPj ?? null;
            $simpan->namaPj = $request->namaPj ?? null;
            $simpan->umurPj = $request->umurPj ?? null;
            $simpan->alamatPj = $request->alamatPj ?? null;
            $simpan->dpjp = $request->dpjp ?? null;
            $simpan->tandaTangan = $request->signed;
            $simpan->user_id = Auth::user()->id;
            $simpan->save();
        }

        $id = Crypt::encrypt($request->noRawat);
        Session::flash('sukses', 'Data Berhasil disimpan!');

        return redirect("/berkasrm/berkas/$id/generalconsent");
    }

    public function generalEdit(Request $request)
    {
        // dd($request);

        $edit = GeneralConsent::where('noRawat', $request->noRawat)->first();
        if ($edit) {
            $edit->keyakinan1 = $request->keyakinan1;
            $edit->keyakinan2 = $request->keyakinan2;
            $edit->keyakinan3 = $request->keyakinan3;
            $edit->keyakinan4 = $request->keyakinan4;
            $edit->privasi1 = $request->privasi1;
            $edit->privasi2 = $request->privasi2;
            $edit->privasi3 = $request->privasi3;
            $edit->tglLahirPj = $request->tanggalLahirPj;
            $edit->namaPj = $request->namaPj;
            $edit->umurPj = $request->umurPj;
            $edit->alamatPj = $request->alamatPj;
            $edit->dpjp = $request->dpjp ?? null;
            if (!empty($request->signed)) {
                $edit->tandaTangan = $request->signed;
            }
            $edit->user_id = Auth::user()->id;
            $edit->save();
        }

        $id = Crypt::encrypt($request->noRawat);
        Session::flash('sukses', 'Data Berhasil disimpan!');

        return redirect("/berkasrm/berkas/$id/generalconsent");
    }

    public function generalSend($id)
    {
        $id = Crypt::decrypt($id);

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_dokter',
                'reg_periksa.status_lanjut',
                'reg_periksa.stts',
                'reg_periksa.kd_poli',
                'reg_periksa.kd_pj',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pasien.alamat',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'poliklinik.nm_poli'
            )
            ->where('reg_periksa.no_rawat', $id)
            ->first();

        $berkas = GeneralConsent::where('noRawat', $id)
            ->first();
        $dokter = Dokter::where('nm_dokter', 'like', '%dr%')
            ->where('status', '=', '1')
            ->orderBy('nm_dokter')
            ->get();

        $pdf = Pdf::loadView('berkas_rm.general_pdf', [
            'data' => $data,
            'berkas' => $berkas,
            'dokter' => $dokter
        ]);

        // (Optional) Setup the paper size and orientation
        $pdf->setPaper('A4', 'potraid');

        $master =  DB::connection('mysqlkhanza')->table('master_berkas_digital')
            ->where('kode', '058')
            ->first();

        $nama_master = preg_replace('/[\/\\\\\?\*\:\|\"\<\>\s]/', '_', $master->nama);

        $tgl_registrasi = Carbon::parse($data->tgl_registrasi)->format('Ymd');
        $waktu_upload = Carbon::now()->format('His');

        $nama_file = $tgl_registrasi . '_' .
            $waktu_upload . '_' .
            substr($data->no_rawat, -6) . '_' .
            $nama_master . '.pdf';

        //============================
        // Simpan ke storage lokal
        //============================
        $localPath = 'temp/' . $nama_file;

        Storage::disk('local')->put(
            $localPath,
            $pdf->output()
        );

        //============================
        // Upload ke SFTP
        //============================
        $file = Storage::disk('local')->get($localPath);

        $move = Storage::disk('sftp')->put(
            'pages/upload/' . $nama_file,
            $file
        );

        if ($move) {
            Session::flash('sukses', 'File berhasil diupload ke server!');

            //============================
            // Hapus file lokal
            //============================
            Storage::disk('local')->delete($localPath);

            //============================
            // Simpan ke database
            //============================
            $dataInsert = [];

            $dataInsert['no_rawat'] = $data->no_rawat;
            $dataInsert['kode'] = '058'; // atau kode master berkas
            $dataInsert['lokasi_file'] = 'pages/upload/' . $nama_file;

            DB::connection('mysqlkhanza')
                ->table('berkas_digital_perawatan')
                ->insert($dataInsert);
        } else {
            Session::flash('error', 'File gagal diupload ke server!');
        }

        return redirect()->back();
    }

    public function viewGeneral($id)
    {
        $noRawat = Crypt::decrypt($id);

        $berkas = DB::connection('mysqlkhanza')
            ->table('berkas_digital_perawatan')
            ->where('no_rawat', $noRawat)
            ->where('kode', '058') // sesuaikan kode master
            ->first();

        if (!$berkas) {
            abort(404, 'File tidak ditemukan');
        }

        if (!Storage::disk('sftp')->exists($berkas->lokasi_file)) {
            abort(404, 'File tidak ditemukan di SFTP');
        }

        $file = Storage::disk('sftp')->get($berkas->lokasi_file);

        return response($file, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . basename($berkas->lokasi_file) . '"');
    }

    public function generalDelete($id)
    {
        $id = Crypt::decrypt($id);

        DB::beginTransaction();
        try {
            // Hapus file di SFTP jika ada
            $berkasDigital = DB::connection('mysqlkhanza')
                ->table('berkas_digital_perawatan')
                ->where('no_rawat', $id)
                ->where('kode', '058') // sesuaikan dengan kode master berkas
                ->first();
            if ($berkasDigital) {
                if (Storage::disk('sftp')->exists($berkasDigital->lokasi_file)) {
                    Storage::disk('sftp')->delete($berkasDigital->lokasi_file);
                }
                DB::connection('mysqlkhanza')
                    ->table('berkas_digital_perawatan')
                    ->where('no_rawat', $id)
                    ->where('kode', '058')
                    ->delete();
            }

            // Hapus data lokal
            $delete = GeneralConsent::where('noRawat', $id)->first();

            if ($delete) {
                $delete->delete();
            }

            DB::commit();

            Session::flash('sukses', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('error', 'Data gagal dihapus. ' . $e->getMessage());
        }

        return redirect()->back();
    }

    public function persetujuanRI($id)
    {
        $id = Crypt::decrypt($id);

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
            ->join('kelurahan', 'kelurahan.kd_kel', '=', 'pasien.kd_kel')
            ->join('kecamatan', 'kecamatan.kd_kec', '=', 'pasien.kd_kec')
            ->join('kabupaten', 'kabupaten.kd_kab', '=', 'pasien.kd_kab')
            ->join('propinsi', 'propinsi.kd_prop', '=', 'pasien.kd_prop')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_dokter',
                'reg_periksa.status_lanjut',
                'reg_periksa.stts',
                'reg_periksa.kd_poli',
                'reg_periksa.kd_pj',
                'penjab.png_jawab as cara_bayar',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pasien.alamat',
                'kelurahan.nm_kel',
                'kecamatan.nm_kec',
                'kabupaten.nm_kab',
                'propinsi.nm_prop',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'poliklinik.nm_poli'
            )
            ->where('reg_periksa.no_rawat', $id)
            ->first();

        $berkas = PersetujuanRawatInap::where('no_rawat', $id)
            ->first();

        $fileSftp =  DB::connection('mysqlkhanza')->table('berkas_digital_perawatan')
            ->where('kode', '059')
            ->where('no_rawat', $id)
            ->first();

        return view('berkas_rm.persetujuan_ri', compact('data', 'berkas', 'fileSftp'));
    }

    public function persetujuanRIStore(Request $request)
    {
        $validatedData = $request->validate([
            'hak_kelas_rawat' => 'required|string|max:255',
            'signed' => 'required'
        ], [
            'hak_kelas_rawat.required' => 'Hak kelas rawat harus diisi.',
            'signed.required' => 'Tanda tangan harus diisi.'
        ]);

        $cek = PersetujuanRawatInap::where('no_rawat', $request->no_rawat)->first();



        if (empty($cek)) {
            $simpan = new PersetujuanRawatInap();
            $simpan->nama_pj = $request->nama_pj;
            $simpan->tempat_lahir_pj = $request->tempat_lahir_pj;
            $simpan->tanggal_lahir_pj = $request->tgl_lahir_pj;
            $simpan->jenis_kelamin_pj = $request->jk_pj;
            $simpan->alamat_pj = $request->alamat_pj;
            $simpan->pekerjaan_pj = $request->pekerjaan_pj;
            $simpan->no_ktp_pj = $request->no_ktp_pj;
            $simpan->no_telepon_pj = $request->no_telp_pj;
            $simpan->hubungan_pj = $request->hubungan_pj;
            $simpan->nama_pasien = $request->nama_pasien;
            $simpan->tempat_lahir_pasien = $request->tempat_lahir_pasien;
            $simpan->tanggal_lahir_pasien = $request->tgl_lahir_pasien;
            $simpan->jenis_kelamin_pasien = $request->jk_pasien;
            $simpan->alamat_pasien = $request->alamat_pasien;
            $simpan->no_rm = $request->nomor_rekam_medis;
            $simpan->no_rawat = $request->no_rawat;
            $simpan->cara_bayar = $request->cara_bayar;
            $simpan->kelas_rawat = $request->hak_kelas_rawat;
            $simpan->pindah_kelas_rawat = $request->pindah_kelas_rawat;

            $simpan->tanda_tangan = $request->signed;
            $simpan->petugas_id = Auth::user()->id;
            $simpan->save();
        }

        Session::flash('sukses', 'Data Berhasil disimpan!');

        return redirect()->back();
    }

    public function persetujuanRIUpdate(Request $request, $id)
    {
        $validatedData = $request->validate([
            'hak_kelas_rawat' => 'required|string|max:255',
            'signed' => 'required'
        ], [
            'hak_kelas_rawat.required' => 'Hak kelas rawat harus diisi.',
            'signed.required' => 'Tanda tangan harus diisi.'
        ]);

        $cek = PersetujuanRawatInap::where('no_rawat', $request->no_rawat)->first();

        if (!empty($cek)) {
            $cek->nama_pj = $request->nama_pj;
            $cek->tempat_lahir_pj = $request->tempat_lahir_pj;
            $cek->tanggal_lahir_pj = $request->tgl_lahir_pj;
            $cek->jenis_kelamin_pj = $request->jk_pj;
            $cek->alamat_pj = $request->alamat_pj;
            $cek->pekerjaan_pj = $request->pekerjaan_pj;
            $cek->no_ktp_pj = $request->no_ktp_pj;
            $cek->no_telepon_pj = $request->no_telp_pj;
            $cek->hubungan_pj = $request->hubungan_pj;
            $cek->nama_pasien = $request->nama_pasien;
            $cek->tempat_lahir_pasien = $request->tempat_lahir_pasien;
            $cek->tanggal_lahir_pasien = $request->tgl_lahir_pasien;
            $cek->jenis_kelamin_pasien = $request->jk_pasien;
            $cek->alamat_pasien = $request->alamat_pasien;
            $cek->no_rm = $request->nomor_rekam_medis;
            $cek->no_rawat = $request->no_rawat;
            $cek->cara_bayar = $request->cara_bayar;
            $cek->kelas_rawat = $request->hak_kelas_rawat;
            $cek->pindah_kelas_rawat = $request->pindah_kelas_rawat;

            $cek->tanda_tangan = $request->signed;
            $cek->petugas_id = Auth::user()->id;
            $cek->save();
        } else {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        Session::flash('sukses', 'Data Berhasil disimpan!');

        return redirect()->back();
    }

    public function persetujuanRIDelete($id)
    {
        $id = Crypt::decrypt($id);

        DB::beginTransaction();
        try {
            // Hapus file di SFTP jika ada
            $berkasDigital = DB::connection('mysqlkhanza')
                ->table('berkas_digital_perawatan')
                ->where('no_rawat', $id)
                ->where('kode', '059') // sesuaikan dengan kode master berkas
                ->first();
            if ($berkasDigital) {
                if (Storage::disk('sftp')->exists($berkasDigital->lokasi_file)) {
                    Storage::disk('sftp')->delete($berkasDigital->lokasi_file);
                }
                DB::connection('mysqlkhanza')
                    ->table('berkas_digital_perawatan')
                    ->where('no_rawat', $id)
                    ->where('kode', '059')
                    ->delete();
            }

            // Hapus data lokal
            $delete = PersetujuanRawatInap::where('no_rawat', $id)->first();

            if ($delete) {
                $delete->delete();
            } else {
                return redirect()->back()->with('error', 'Data tidak ditemukan.');
            }

            DB::commit();

            Session::flash('sukses', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('error', 'Data gagal dihapus. ' . $e->getMessage());
        }

        return redirect()->back();
    }

    public function persetujuanRIPdf($id)
    {
        $id = Crypt::decrypt($id);

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
            ->join('kelurahan', 'kelurahan.kd_kel', '=', 'pasien.kd_kel')
            ->join('kecamatan', 'kecamatan.kd_kec', '=', 'pasien.kd_kec')
            ->join('kabupaten', 'kabupaten.kd_kab', '=', 'pasien.kd_kab')
            ->join('propinsi', 'propinsi.kd_prop', '=', 'pasien.kd_prop')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_dokter',
                'reg_periksa.status_lanjut',
                'reg_periksa.stts',
                'reg_periksa.kd_poli',
                'reg_periksa.kd_pj',
                'penjab.png_jawab as cara_bayar',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pasien.alamat',
                'kelurahan.nm_kel',
                'kecamatan.nm_kec',
                'kabupaten.nm_kab',
                'propinsi.nm_prop',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'poliklinik.nm_poli'
            )
            ->where('reg_periksa.no_rawat', $id)
            ->first();

        $berkas = PersetujuanRawatInap::where('no_rawat', $id)
            ->first();

        $pdf = Pdf::loadView('berkas_rm.persetujuan_ri_pdf', [
            'data' => $data,
            'berkas' => $berkas
        ]);

        // (Optional) Setup the paper size and orientation
        $pdf->setPaper('A4', 'portrait');
        //berikan nama file sesuai dengan format yang diinginkan
        $nama_file = 'Persetujuan Rawat Inap ' . $data->no_rawat . '.pdf';
        // $pdf->setOption('filename', $nama_file);
        return $pdf->stream($nama_file);
    }

    public function persetujuanRISend($id)
    {
        $id = Crypt::decrypt($id);

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
            ->join('kelurahan', 'kelurahan.kd_kel', '=', 'pasien.kd_kel')
            ->join('kecamatan', 'kecamatan.kd_kec', '=', 'pasien.kd_kec')
            ->join('kabupaten', 'kabupaten.kd_kab', '=', 'pasien.kd_kab')
            ->join('propinsi', 'propinsi.kd_prop', '=', 'pasien.kd_prop')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_dokter',
                'reg_periksa.status_lanjut',
                'reg_periksa.stts',
                'reg_periksa.kd_poli',
                'reg_periksa.kd_pj',
                'penjab.png_jawab as cara_bayar',
                'pasien.nm_pasien',
                'pasien.no_ktp as ktp_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'pasien.alamat',
                'kelurahan.nm_kel',
                'kecamatan.nm_kec',
                'kabupaten.nm_kab',
                'propinsi.nm_prop',
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'poliklinik.nm_poli'
            )
            ->where('reg_periksa.no_rawat', $id)
            ->first();

        $berkas = PersetujuanRawatInap::where('no_rawat', $id)
            ->first();

        $pdf = Pdf::loadView('berkas_rm.persetujuan_ri_pdf', [
            'data' => $data,
            'berkas' => $berkas
        ]);

        // (Optional) Setup the paper size and orientation
        $pdf->setPaper('A4', 'portrait');

        $master =  DB::connection('mysqlkhanza')->table('master_berkas_digital')
            ->where('kode', '059')
            ->first();

        $nama_master = preg_replace('/[\/\\\\\?\*\:\|\"\<\>\s]/', '_', $master->nama);

        $tgl_registrasi = Carbon::parse($data->tgl_registrasi)->format('Ymd');
        $waktu_upload = Carbon::now()->format('His');

        $nama_file = $tgl_registrasi . '_' .
            $waktu_upload . '_' .
            substr($data->no_rawat, -6) . '_' .
            $nama_master . '.pdf';

        //============================
        // Simpan ke storage lokal
        //============================
        $localPath = 'temp/' . $nama_file;

        Storage::disk('local')->put(
            $localPath,
            $pdf->output()
        );

        //============================
        // Upload ke SFTP
        //============================
        $file = Storage::disk('local')->get($localPath);

        try {
            $move = Storage::disk('sftp')->put(
                'pages/upload/' . $nama_file,
                $file
            );
        } catch (\Exception $e) {
            Session::flash('error', 'File gagal diupload ke server! ' . $e->getMessage());
            return redirect()->back();
        }

        if ($move) {
            Session::flash('sukses', 'File berhasil diupload ke server!');

            //============================
            // Hapus file lokal
            //============================
            Storage::disk('local')->delete($localPath);

            //============================
            // Simpan ke database
            //============================
            $dataInsert = [];

            $dataInsert['no_rawat'] = $data->no_rawat;
            $dataInsert['kode'] = '059'; // atau kode master berkas
            $dataInsert['lokasi_file'] = 'pages/upload/' . $nama_file;

            DB::connection('mysqlkhanza')
                ->table('berkas_digital_perawatan')
                ->insert($dataInsert);
        } else {
            Session::flash('error', 'File gagal diupload ke server!');
        }

        return redirect()->back();
    }
}
