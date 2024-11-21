<?php

namespace App\Http\Controllers;

use App\Dokter;
use App\GeneralConsent;
use App\HakKewajibanPasien;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

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
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'poliklinik.nm_poli'
            )
            ->where('reg_periksa.status_lanjut', 'Ralan')
            // ->where('reg_periksa.stts', 'Sudah')
            ->whereDate('reg_periksa.tgl_registrasi', $tanggal)
            ->get();

        // dd($data);

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
                'pegawai.no_ktp as ktp_dokter',
                'pegawai.nama as nama_dokter',
                'poliklinik.nm_poli'
            )
            ->where('reg_periksa.status_lanjut', 'Ranap')
            // ->where('reg_periksa.stts', 'Sudah')
            ->whereDate('reg_periksa.tgl_registrasi', $tanggal)
            ->get();

        // dd($data);

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

        // dd($berkas);
        if (empty($berkas)) {
            return view('berkas_rm.check_kewajiban', compact('data', 'berkas'));
        } else {
            return view('berkas_rm.edit_kewajiban', compact('data', 'berkas'));
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
        $cek2 = DB::connection('mysqlkhanza')->table('hak_kewajiban_pasiens')
            ->where('noRawat', $request->noRawat)->first();

        if (empty($cek2)) {
            DB::connection('mysqlkhanza')->table('hak_kewajiban_pasiens')->insert([
                'noRawat' => $request->noRawat,
                'hak1' => $request->hak1 ? $request->hak1 : false,
                'hak2' => $request->hak2 ? $request->hak1 : false,
                'hak3' => $request->hak3 ? $request->hak1 : false,
                'hak4' => $request->hak4 ? $request->hak1 : false,
                'hak5' => $request->hak5 ? $request->hak1 : false,
                'hak6' => $request->hak6 ? $request->hak1 : false,
                'hak7' => $request->hak7 ? $request->hak1 : false,
                'hak8' => $request->hak8 ? $request->hak1 : false,
                'hak9' => $request->hak9 ? $request->hak1 : false,
                'hak10' => $request->hak10 ? $request->hak1 : false,
                'hak11' => $request->hak11 ? $request->hak1 : false,
                'hak12' => $request->hak12 ? $request->hak1 : false,
                'hak13' => $request->hak13 ? $request->hak1 : false,
                'hak14' => $request->hak14 ? $request->hak1 : false,
                'hak15' => $request->hak15 ? $request->hak1 : false,
                'hak16' => $request->hak16 ? $request->hak1 : false,
                'hak17' => $request->hak17 ? $request->hak1 : false,
                'hak18' => $request->hak18 ? $request->hak1 : false,
                'kewajiban1' => $request->kewajiban1 ? $request->kewajiban1 : false,
                'kewajiban2' => $request->kewajiban2 ? $request->kewajiban2 : false,
                'kewajiban3' => $request->kewajiban3 ? $request->kewajiban3 : false,
                'kewajiban4' => $request->kewajiban4 ? $request->kewajiban4 : false,
                'kewajiban5' => $request->kewajiban5 ? $request->kewajiban5 : false,
                'kewajiban6' => $request->kewajiban6 ? $request->kewajiban6 : false,
                'kewajiban7' => $request->kewajiban7 ? $request->kewajiban7 : false,
                'kewajiban8' => $request->kewajiban8 ? $request->kewajiban8 : false,
                'statusPj' => $request->statusPj,
                'namaPj' => $request->namaPj,
                'tandaTangan' => $request->signed,
                'user_id' => Auth::user()->username
            ]);
        }

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
        $cek2 = DB::connection('mysqlkhanza')->table('hak_kewajiban_pasiens')
            ->where('noRawat', $request->noRawat)->first();
        if ($cek2) {
            DB::connection('mysqlkhanza')->table('hak_kewajiban_pasiens')
                ->where('noRawat', $request->noRawat)
                ->update([
                    'hak1' => $request->hak1,
                    'hak2' => $request->hak2,
                    'hak3' => $request->hak3,
                    'hak4' => $request->hak4,
                    'hak5' => $request->hak5,
                    'hak6' => $request->hak6,
                    'hak7' => $request->hak7,
                    'hak8' => $request->hak8,
                    'hak9' => $request->hak9,
                    'hak10' => $request->hak10,
                    'hak11' => $request->hak11,
                    'hak12' => $request->hak12,
                    'hak13' => $request->hak13,
                    'hak14' => $request->hak14,
                    'hak15' => $request->hak15,
                    'hak16' => $request->hak16,
                    'hak17' => $request->hak17,
                    'hak18' => $request->hak18,
                    'kewajiban1' => $request->kewajiban1,
                    'kewajiban2' => $request->kewajiban2,
                    'kewajiban3' => $request->kewajiban3,
                    'kewajiban4' => $request->kewajiban4,
                    'kewajiban5' => $request->kewajiban5,
                    'kewajiban6' => $request->kewajiban6,
                    'kewajiban7' => $request->kewajiban7,
                    'kewajiban8' => $request->kewajiban8,
                    'statusPj' => $request->statusPj,
                    'namaPj' => $request->namaPj,
                    'user_id' => Auth::user()->username,
                    'tandaTangan' => !empty($request->signed) ? $request->signed : DB::raw('tandaTangan'), // retain original value if empty
                ]);
        }

        Session::flash('sukses', 'Data Berhasil diperbaharui!');

        return redirect()->back();
    }

    public function delete($id)
    {
        $id = Crypt::decrypt($id);

        //dblokal
        $delete = HakKewajibanPasien::where('noRawat', $id)->first();
        if ($delete) {
            $delete->delete();
        }
        //dbkhanza
        $hapus = DB::connection('mysqlkhanza')->table('hak_kewajiban_pasiens')
            ->where('noRawat', $id)->first();
        if ($hapus) {
            DB::connection('mysqlkhanza')->table('hak_kewajiban_pasiens')
                ->where('noRawat', $id)
                ->delete();
        }

        Session::flash('sukses', 'Data Berhasil dihapus!');

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

        // dd($berkas, $dokter);
        if (empty($berkas)) {
            return view('berkas_rm.check_gc', compact('data', 'berkas', 'dokter'));
        } else {
            return view('berkas_rm.edit_gc', compact('data', 'berkas', 'dokter'));
        }
    }

    public function generalStore(Request $request)
    {
        // dd($request);
        $cek = GeneralConsent::where('noRawat', $request->noRawat)->first();

        if (empty($cek)) {
            $simpan = new GeneralConsent();
            $simpan->noRawat = $request->noRawat;
            $simpan->keyakinan1 = $request->keyakinan1;
            $simpan->keyakinan2 = $request->keyakinan2;
            $simpan->keyakinan3 = $request->keyakinan3;
            $simpan->keyakinan4 = $request->keyakinan4;
            $simpan->privasi1 = $request->privasi1;
            $simpan->privasi2 = $request->privasi2;
            $simpan->privasi3 = $request->privasi3;
            $simpan->tglLahirPj = $request->tanggalLahirPj;
            $simpan->namaPj = $request->namaPj;
            $simpan->umurPj = $request->umurPj;
            $simpan->alamatPj = $request->alamatPj;
            $simpan->dpjp = $request->dpjp;
            $simpan->tandaTangan = $request->signed;
            $simpan->user_id = Auth::user()->id;
            $simpan->save();
        }

        $cek2 = DB::connection('mysqlkhanza')->table('general_consents')
            ->where('noRawat', $request->noRawat)->first();

        if (empty($cek2)) {
            DB::connection('mysqlkhanza')->table('general_consents')->insert([
                'noRawat' => $request->noRawat,
                'keyakinan1' => $request->keyakinan1 ? $request->keyakinan1 : false,
                'keyakinan2' => $request->keyakinan2 ? $request->keyakinan2 : false,
                'keyakinan3' => $request->keyakinan3 ? $request->keyakinan3 : false,
                'keyakinan4' => $request->keyakinan4 ? $request->keyakinan4 : false,
                'privasi1' => $request->privasi1 ? $request->privasi1 : false,
                'privasi2' => $request->privasi2 ? $request->privasi2 : false,
                'privasi3' => $request->privasi3 ? $request->privasi3 : false,
                'tglLahirPj' => $request->tanggalLahirPj,
                'namaPj' => $request->namaPj,
                'umurPj' => $request->umurPj,
                'alamatPj' => $request->alamatPj,
                'dpjp' => $request->dpjp,
                'tandaTangan' => $request->signed,
                'user_id' => Auth::user()->username
            ]);
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
            $edit->dpjp = $request->dpjp;
            if (!empty($request->signed)) {
                $edit->tandaTangan = $request->signed;
            }
            $edit->user_id = Auth::user()->id;
            $edit->save();
        }

        //Edit Data dikhanza
        $cek2 = DB::connection('mysqlkhanza')->table('general_consents')
            ->where('noRawat', $request->noRawat)->first();
        if ($cek2) {
            DB::connection('mysqlkhanza')->table('general_consents')
                ->where('noRawat', $request->noRawat)
                ->update([
                    'keyakinan1' => $request->keyakinan1,
                    'keyakinan2' => $request->keyakinan2,
                    'keyakinan3' => $request->keyakinan3,
                    'keyakinan4' => $request->keyakinan4,
                    'privasi1' => $request->privasi1,
                    'privasi2' => $request->privasi2,
                    'privasi3' => $request->privasi3,
                    'tglLahirPj' => $request->tanggalLahirPj,
                    'namaPj' => $request->namaPj,
                    'umurPj' => $request->umurPj,
                    'alamatPj' => $request->alamatPj,
                    'dpjp' => $request->dpjp,
                    'user_id' => Auth::user()->username,
                    'tandaTangan' => !empty($request->signed) ? $request->signed : DB::raw('tandaTangan') // retain original value if empty
                ]);
        }

        $id = Crypt::encrypt($request->noRawat);
        Session::flash('sukses', 'Data Berhasil disimpan!');

        return redirect("/berkasrm/berkas/$id/generalconsent");
    }

    public function generalDelete($id)
    {
        $id = Crypt::decrypt($id);


        GeneralConsent::where('noRawat', $id)->delete();
        DB::connection('mysqlkhanza')->table('general_consents')
            ->where('noRawat', $id)
            ->delete();

        Session::flash('sukses', 'Data Berhasil dihapus!');

        return redirect()->back();
    }
}
