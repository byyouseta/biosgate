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
        // dd($request);

        $simpan = new HakKewajibanPasien();
        $simpan->noRawat = $request->noRawat;
        $simpan->hak1 = $request->hak1;
        $simpan->hak2 = $request->hak2;
        $simpan->hak3 = $request->hak3;
        $simpan->hak4 = $request->hak4;
        $simpan->hak5 = $request->hak5;
        $simpan->hak6 = $request->hak6;
        $simpan->hak7 = $request->hak7;
        $simpan->hak8 = $request->hak8;
        $simpan->hak9 = $request->hak9;
        $simpan->hak10 = $request->hak10;
        $simpan->hak11 = $request->hak11;
        $simpan->hak12 = $request->hak12;
        $simpan->hak13 = $request->hak13;
        $simpan->hak14 = $request->hak14;
        $simpan->hak15 = $request->hak15;
        $simpan->hak16 = $request->hak16;
        $simpan->hak17 = $request->hak17;
        $simpan->hak18 = $request->hak18;
        $simpan->kewajiban1 = $request->kewajiban1;
        $simpan->kewajiban2 = $request->kewajiban2;
        $simpan->kewajiban3 = $request->kewajiban3;
        $simpan->kewajiban4 = $request->kewajiban4;
        $simpan->kewajiban5 = $request->kewajiban5;
        $simpan->kewajiban6 = $request->kewajiban6;
        $simpan->kewajiban7 = $request->kewajiban7;
        $simpan->kewajiban8 = $request->kewajiban8;
        $simpan->statusPj = $request->statusPj;
        $simpan->namaPj = $request->namaPj;
        $simpan->tandaTangan = $request->signed;
        $simpan->user_id = Auth::user()->id;
        $simpan->save();

        Session::flash('sukses', 'Data Berhasil disimpan!');

        return redirect()->back();
    }

    public function hakkewajibanEdit(Request $request)
    {
        // dd($request);
        $edit = HakKewajibanPasien::where('noRawat', $request->noRawat)->first();

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

        Session::flash('sukses', 'Data Berhasil diperbaharui!');

        return redirect()->back();
    }

    public function delete($id)
    {
        $id = Crypt::decrypt($id);

        $delete = HakKewajibanPasien::where('noRawat', $id)->delete();

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

        $id = Crypt::encrypt($request->noRawat);
        Session::flash('sukses', 'Data Berhasil disimpan!');

        return redirect("/berkasrm/berkas/$id/generalconsent");
    }

    public function generalEdit(Request $request)
    {
        // dd($request);

        $edit = GeneralConsent::where('noRawat', $request->noRawat)->first();
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

        $id = Crypt::encrypt($request->noRawat);
        Session::flash('sukses', 'Data Berhasil disimpan!');

        return redirect("/berkasrm/berkas/$id/generalconsent");
    }

    public function generalDelete($id)
    {
        $id = Crypt::decrypt($id);

        GeneralConsent::where('noRawat', $id)->delete();

        Session::flash('sukses', 'Data Berhasil disimpan!');

        return redirect()->back();
    }
}
