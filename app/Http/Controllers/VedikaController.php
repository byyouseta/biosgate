<?php

namespace App\Http\Controllers;

use App\BerkasVedika;
use App\MasterBerkasVedika;
use App\sepManual;
use App\Setting;
use App\Vedika;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class VedikaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('permission:vedika-list', ['only' => ['rajal', 'billingRajal', 'labRajal', 'radioRajal']]);
    }

    public function rajal(Request $request)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Pasien Rajal');
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
                'dokter.nm_dokter'
                // 'diagnosa_pasien.kd_penyakit',
                // 'diagnosa_pasien.prioritas',
                // 'diagnosa_pasien.status',
                // 'penyakit.nm_penyakit'
            )
            ->where('reg_periksa.kd_pj', '=', 'BPJ')
            ->where('reg_periksa.status_lanjut', '=', 'Ralan')
            ->where('reg_periksa.tgl_registrasi', '>=', $tanggalMulai)
            ->where('reg_periksa.tgl_registrasi', '<=', $tanggalSelesai)
            ->where('reg_periksa.stts', '!=', 'Batal')
            // ->where('diagnosa_pasien.prioritas', '=', 1)
            // ->where('diagnosa_pasien.status', '=', 'Ralan')
            ->get();

        // dd($data);

        return view('vedika.pasien_rajal', compact('data'));
    }

    public function ranap(Request $request)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Pasien Ranap');
        session()->forget('cucu');

        if (isset($request->tanggal)) {
            $tanggal = $request->tanggal;
        } else {
            $tanggal = Carbon::now()->format('Y-m-d');
        }

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('dpjp_ranap', 'dpjp_ranap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('dokter', 'dokter.kd_dokter', '=', 'dpjp_ranap.kd_dokter')
            // ->join('diagnosa_pasien', 'diagnosa_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
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
                'dokter.nm_dokter'
                // 'diagnosa_pasien.kd_penyakit',
                // 'diagnosa_pasien.prioritas',
                // 'diagnosa_pasien.status',
                // 'penyakit.nm_penyakit'
            )
            ->where('reg_periksa.kd_pj', '=', 'BPJ')
            ->where('reg_periksa.status_lanjut', '=', 'Ranap')
            ->where('reg_periksa.tgl_registrasi', '=', $tanggal)
            // ->where('diagnosa_pasien.status', '=', 'Ranap')
            // ->where('diagnosa_pasien.prioritas', '=', 1)
            ->get();

        // dd($data);

        return view('vedika.pasien_ranap', compact('data'));
    }

    public function detailRajal($id)
    {
        session()->put('ibu', 'Vedika');
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
            ->where('reg_periksa.kd_pj', '=', 'BPJ')
            ->where('reg_periksa.status_lanjut', '=', 'Ralan')
            ->where('reg_periksa.no_rawat', '=', $id)
            ->first();

        // dd($pasien);

        //Ambil data billing
        $billing = VedikaController::billingRajal($pasien->no_rawat);
        //Ambil data untuk Bukti Pelayanan
        $buktiPelayanan = VedikaController::buktiPelayanan($pasien->no_rawat);
        $diagnosa = $buktiPelayanan[0];
        $prosedur = $buktiPelayanan[1];
        //Ambil data Radiologi
        $radiologi = VedikaController::radioRajal($pasien->no_rawat);
        $dataRadiologi = $radiologi[0];
        $dokterRadiologi = $radiologi[1];
        //Ambil data Triase dan Ringkasan IGD
        if ($pasien->nm_poli == "IGD") {
            $triase = VedikaController::triase($pasien->no_rawat);
            $dataTriase = $triase[0];
            $primer = $triase[1];
            $sekunder = $triase[2];
            $skala = $triase[3];

            $ringkasan = VedikaController::ringkasanIgd($pasien->no_rawat);
            $dataRingkasan = $ringkasan[0];
            $resumeIgd = $ringkasan[1];
        } else {
            $dataTriase = $primer = $sekunder = $skala = $dataRingkasan = $resumeIgd = null;
        }
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
        // dd($pasien, $billing, $dataTriase);

        return view('vedika.detailRajal', compact(
            'pasien',
            'billing',
            'diagnosa',
            'prosedur',
            'permintaanLab',
            'hasilLab',
            'dataRadiologi',
            'dokterRadiologi',
            'dataTriase',
            'primer',
            'sekunder',
            'skala',
            'dataRingkasan',
            'resumeIgd',
            'resepObat',
            'obatJadi',
            'obatRacik',
            'dataBerkas',
            'masterBerkas',
            'pathBerkas'
        ));
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
            ->where('reg_periksa.kd_pj', '=', 'BPJ')
            ->where('reg_periksa.status_lanjut', '=', 'Ralan')
            ->where('reg_periksa.no_rawat', '=', $id)
            ->first();

        // dd($pasien);

        //Ambil data billing
        $billing = VedikaController::billingRajal($pasien->no_rawat);
        //Ambil data untuk Bukti Pelayanan
        $buktiPelayanan = VedikaController::buktiPelayanan($pasien->no_rawat);
        $diagnosa = $buktiPelayanan[0];
        $prosedur = $buktiPelayanan[1];
        //Ambil data Radiologi
        $radiologi = VedikaController::radioRajal($pasien->no_rawat);
        $dataRadiologi = $radiologi[0];
        $dokterRadiologi = $radiologi[1];
        //Ambil data Triase dan Ringkasan IGD
        if ($pasien->nm_poli == "IGD") {
            $triase = VedikaController::triase($pasien->no_rawat);
            $dataTriase = $triase[0];
            $primer = $triase[1];
            $sekunder = $triase[2];
            $skala = $triase[3];

            $ringkasan = VedikaController::ringkasanIgd($pasien->no_rawat);
            $dataRingkasan = $ringkasan[0];
            $resumeIgd = $ringkasan[1];
        } else {
            $dataTriase = $primer = $sekunder = $skala = $dataRingkasan = $resumeIgd = null;
        }
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

        // dd($pasien, $billing);

        $pdf = Pdf::loadView('vedika.detailRajal_pdf', [
            'pasien' => $pasien,
            'billing' => $billing,
            'diagnosa' => $diagnosa,
            'prosedur' => $prosedur,
            'permintaanLab' => $permintaanLab,
            'hasilLab' => $hasilLab,
            'dataRadiologi' => $dataRadiologi,
            'dokterRadiologi' => $dokterRadiologi,
            'dataTriase' => $dataTriase,
            'primer' => $primer,
            'sekunder' => $sekunder,
            'skala' => $skala,
            'dataRingkasan' => $dataRingkasan,
            'resumeIgd' => $resumeIgd,
            'resepObat' => $resepObat,
            'obatJadi' => $obatJadi,
            'obatRacik' => $obatRacik,
            'dataBerkas' => $dataBerkas,
            'masterBerkas' => $masterBerkas,
            'pathBerkas' => $pathBerkas
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

    public function sepManual($id)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Input SEP Manual');
        session()->forget('cucu');

        $id = Crypt::decrypt($id);

        $cek = sepManual::where('noRawat', $id)->count();

        if (empty($cek)) {
            $data = DB::connection('mysqlkhanza')->table('reg_periksa')
                ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
                // ->join('bridging_sep', 'bridging_sep.no_rawat', '=', 'reg_periksa.no_rawat')
                ->select(
                    'reg_periksa.no_rawat',
                    'reg_periksa.kd_poli',
                    'pasien.nm_pasien',
                    'pasien.jk',
                    'pasien.tmp_lahir',
                    'pasien.tgl_lahir',
                    'pasien.alamat'
                    // 'bridging_sep.no_sep'
                )
                ->where('reg_periksa.no_rawat', '=', $id)
                ->first();
        } else {
            Session::flash('error', 'Data SEP sudah ada!');

            return redirect('/vedika/rajal');
        }

        // dd($data);

        return view('vedika.add_noSep', compact('data'));
    }

    public function simpanSep(Request $request)
    {
        $this->validate($request, [
            'signed' => 'required',
        ], [
            'signed.required' => 'Tanda tangan pasien kosong'
        ]);

        // dd($request);
        $data = new sepManual();
        $data->noRawat = $request->noRawat;
        $data->no_sep = $request->no_sep;
        $data->tandaTangan = $request->signed;
        $data->save();

        return redirect('/vedika/rajal');
    }

    public function billingRajal($id)
    {
        // session()->put('ibu', 'Vedika');
        // session()->put('anak', 'Pasien Rajal');
        // session()->put('cucu', 'Billing');

        // $id = Crypt::decrypt($id);

        $data = DB::connection('mysqlkhanza')->table('billing')
            ->select(
                'billing.no as no_status',
                'billing.nm_perawatan',
                'billing.pemisah',
                'billing.biaya',
                'billing.jumlah',
                'billing.tambahan',
                'billing.totalbiaya',
                'billing.status',
                'billing.tgl_byr'
            )
            ->where('billing.no_rawat', '=', $id)
            ->get();

        return $data;

        // return view('vedika.billing', compact('data'));
    }

    public function obat($id)
    {
        $pasien = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('resep_obat', 'resep_obat.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('penilaian_awal_keperawatan_ralan', 'penilaian_awal_keperawatan_ralan.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('kelurahan', 'kelurahan.kd_kel', '=', 'pasien.kd_kel')
            ->leftJoin('kecamatan', 'kecamatan.kd_kec', '=', 'pasien.kd_kec')
            ->leftJoin('kabupaten', 'kabupaten.kd_kab', '=', 'pasien.kd_kab')
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
                'pasien.no_peserta',
                'pasien.alamat',
                'pasien.tgl_lahir',
                'kelurahan.nm_kel',
                'kecamatan.nm_kec',
                'kabupaten.nm_kab',
                'resep_obat.no_resep',
                'resep_obat.tgl_perawatan',
                'resep_obat.jam',
                'resep_obat.tgl_peresepan',
                'resep_obat.jam_peresepan',
                'penilaian_awal_keperawatan_ralan.bb',
                'dokter.nm_dokter'
            )
            ->where('reg_periksa.kd_pj', '=', 'BPJ')
            ->where('reg_periksa.status_lanjut', '=', 'Ralan')
            ->where('reg_periksa.no_rawat', '=', $id)
            ->first();

        $data = DB::connection('mysqlkhanza')->table('detail_pemberian_obat')
            ->join('resep_obat', 'resep_obat.no_rawat', '=', 'detail_pemberian_obat.no_rawat')
            ->join('databarang', 'databarang.kode_brng', '=', 'detail_pemberian_obat.kode_brng')
            ->leftJoin('kodesatuan', 'kodesatuan.kode_sat', '=', 'databarang.kode_sat')
            ->select(
                'detail_pemberian_obat.tgl_perawatan',
                'detail_pemberian_obat.jam',
                'detail_pemberian_obat.no_rawat',
                'detail_pemberian_obat.kode_brng',
                'detail_pemberian_obat.biaya_obat',
                'detail_pemberian_obat.jml',
                'detail_pemberian_obat.total',
                'detail_pemberian_obat.status',
                'resep_obat.kd_dokter',
                'resep_obat.tgl_perawatan',
                'resep_obat.jam',
                'resep_obat.tgl_peresepan',
                'resep_obat.jam_peresepan',
                'databarang.nama_brng',
                'kodesatuan.satuan'
            )
            ->where('detail_pemberian_obat.no_rawat', '=', $id)
            ->where('detail_pemberian_obat.status', '=', 'Ralan')
            ->get();

        $racikan = DB::connection('mysqlkhanza')->table('obat_racikan')
            ->join('metode_racik', 'metode_racik.kd_racik', '=', 'obat_racikan.kd_racik')
            ->select(
                'obat_racikan.tgl_perawatan',
                'obat_racikan.jam',
                'obat_racikan.no_rawat',
                'obat_racikan.no_racik',
                'obat_racikan.nama_racik',
                'metode_racik.nm_racik',
                'obat_racikan.jml_dr',
                'obat_racikan.aturan_pakai',
                'obat_racikan.keterangan'
            )
            ->where('obat_racikan.no_rawat', $id)
            ->get();

        // dd($pasien, $data, $racikan);

        return array($pasien, $data, $racikan);

        // return view('vedika.obat', compact('pasien', 'data'));
    }

    public function obatRajal($id)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Pasien Rajal');
        session()->put('cucu', 'Obat');

        $id = Crypt::decrypt($id);

        $pasien = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('resep_obat', 'resep_obat.no_rawat', '=', 'reg_periksa.no_rawat')
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
                'pasien.no_peserta',
                'resep_obat.no_resep',
                'resep_obat.tgl_perawatan',
                'dokter.nm_dokter'
            )
            ->where('reg_periksa.kd_pj', '=', 'BPJ')
            ->where('reg_periksa.status_lanjut', '=', 'Ralan')
            ->where('reg_periksa.no_rawat', '=', $id)
            ->first();

        // if ($pasien->nm_poli == 'IGD') {
        // dd($pasien);
        // }

        $data = DB::connection('mysqlkhanza')->table('detail_pemberian_obat')
            ->join('resep_obat', 'resep_obat.no_rawat', '=', 'detail_pemberian_obat.no_rawat')
            ->join('databarang', 'databarang.kode_brng', '=', 'detail_pemberian_obat.kode_brng')
            ->select(
                'detail_pemberian_obat.tgl_perawatan',
                'detail_pemberian_obat.jam',
                'detail_pemberian_obat.no_rawat',
                'detail_pemberian_obat.kode_brng',
                'detail_pemberian_obat.biaya_obat',
                'detail_pemberian_obat.jml',
                'detail_pemberian_obat.total',
                'detail_pemberian_obat.status',
                'resep_obat.kd_dokter',
                'databarang.nama_brng',
                'databarang.kode_sat'
            )
            ->where('detail_pemberian_obat.no_rawat', '=', $id)
            ->where('detail_pemberian_obat.status', '=', 'Ralan')
            ->get();

        // dd($pasien, $data);

        return view('vedika.obat', compact('pasien', 'data'));
    }

    public function obatRanap($id)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Pasien Ranap');
        session()->put('cucu', 'Obat');

        $id = Crypt::decrypt($id);

        $pasien = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('resep_obat', 'resep_obat.no_rawat', '=', 'reg_periksa.no_rawat')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                // 'reg_periksa.tgl_registrasi',
                // 'reg_periksa.jam_reg',
                'reg_periksa.status_lanjut',
                'reg_periksa.kd_pj',
                'reg_periksa.stts',
                'reg_periksa.kd_dokter',
                'poliklinik.nm_poli',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.no_peserta',
                'resep_obat.no_resep',
                'resep_obat.tgl_perawatan',
                'resep_obat.jam',
                'dokter.nm_dokter'
            )
            ->where('reg_periksa.kd_pj', '=', 'BPJ')
            ->where('reg_periksa.status_lanjut', '=', 'Ranap')
            ->where('reg_periksa.no_rawat', '=', $id)
            ->get();

        $data = DB::connection('mysqlkhanza')->table('detail_pemberian_obat')
            // ->leftJoin('resep_obat', 'resep_obat.no_rawat', '=', 'detail_pemberian_obat.no_rawat')
            ->join('databarang', 'databarang.kode_brng', '=', 'detail_pemberian_obat.kode_brng')
            ->select(
                'detail_pemberian_obat.tgl_perawatan',
                'detail_pemberian_obat.jam',
                'detail_pemberian_obat.no_rawat',
                'detail_pemberian_obat.kode_brng',
                'detail_pemberian_obat.biaya_obat',
                'detail_pemberian_obat.jml',
                'detail_pemberian_obat.total',
                'detail_pemberian_obat.status',
                // 'resep_obat.kd_dokter',
                'databarang.nama_brng',
                'databarang.kode_sat'
            )
            ->where('detail_pemberian_obat.no_rawat', '=', $id)
            ->where('detail_pemberian_obat.status', '=', 'Ranap')
            ->get();

        // dd($pasien, $data);

        return view('vedika.obat_ranap', compact('pasien', 'data'));
    }

    public function billingRanap($id)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Pasien Ranap');
        session()->put('cucu', 'Billing');

        $id = Crypt::decrypt($id);

        $data = DB::connection('mysqlkhanza')->table('billing')
            ->select(
                'billing.no as no_status',
                'billing.nm_perawatan',
                'billing.pemisah',
                'billing.biaya',
                'billing.jumlah',
                'billing.tambahan',
                'billing.totalbiaya',
                'billing.status',
                'billing.tgl_byr'
            )
            ->where('billing.no_rawat', '=', $id)
            ->get();

        // dd($data);

        return view('vedika.billing', compact('data'));
    }

    public function lab($id)
    {
        $cek = DB::connection('mysqlkhanza')->table('permintaan_lab')
            ->join('dokter', 'dokter.kd_dokter', '=', 'permintaan_lab.dokter_perujuk')
            ->select(
                'permintaan_lab.noorder',
                'permintaan_lab.no_rawat',
                'permintaan_lab.tgl_permintaan',
                'permintaan_lab.jam_permintaan',
                'permintaan_lab.tgl_hasil',
                'permintaan_lab.jam_hasil',
                'permintaan_lab.status',
                'dokter.nm_dokter'
            )
            // ->where('permintaan_lab.status', '=', 'ralan')
            ->where('permintaan_lab.no_rawat', '=', $id)
            ->get();

        // $data = DB::connection('mysqlkhanza')->table('detail_periksa_lab')
        //     ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'detail_periksa_lab.no_rawat')
        //     ->leftJoin('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
        //     ->leftJoin('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
        //     ->join('permintaan_lab', 'permintaan_lab.no_rawat', '=', 'detail_periksa_lab.no_rawat')
        //     ->join('periksa_lab', 'periksa_lab.no_rawat', '=', 'detail_periksa_lab.no_rawat')
        //     ->select(
        //         'pasien.no_rkm_medis',
        //         'pasien.nm_pasien',
        //         'pasien.jk',
        //         'pasien.tgl_lahir',
        //         'reg_periksa.almt_pj',
        //         'reg_periksa.umurdaftar',
        //         'reg_periksa.sttsumur',
        //         'reg_periksa.no_rawat',
        //         'reg_periksa.kd_poli',
        //         'poliklinik.nm_poli',
        //         'permintaan_lab.dokter_perujuk',
        //         'permintaan_lab.noorder',
        //         'permintaan_lab.tgl_permintaan',
        //         'permintaan_lab.jam_permintaan',
        //         'permintaan_lab.tgl_hasil',
        //         'permintaan_lab.jam_hasil',
        //         'periksa_lab.nip as petugas',
        //         'periksa_lab.kd_dokter as dokter_lab'
        //     )
        //     ->where('reg_periksa.no_rawat', '=', $id)
        //     ->first();

        if ($cek->count() > 0) {
            // $petugas =  DB::connection('mysqlkhanza')->table('petugas')
            //     ->select('petugas.nip', 'petugas.nama')
            //     ->where('petugas.nip', $data->petugas)
            //     ->first();
            // $dokterLab =  DB::connection('mysqlkhanza')->table('dokter')
            //     ->select('dokter.kd_dokter', 'dokter.nm_dokter')
            //     ->where('dokter.kd_dokter', $data->dokter_lab)
            //     ->first();
            // $dokterPerujuk =  DB::connection('mysqlkhanza')->table('dokter')
            //     ->select('dokter.kd_dokter', 'dokter.nm_dokter')
            //     ->where('dokter.kd_dokter', $data->dokter_perujuk)
            //     ->first();
            $hasil_periksa = DB::connection('mysqlkhanza')->table('detail_periksa_lab')
                ->join('periksa_lab', 'periksa_lab.no_rawat', '=', 'detail_periksa_lab.no_rawat')
                ->leftJoin('template_laboratorium', 'template_laboratorium.id_template', '=', 'detail_periksa_lab.id_template')
                ->select(
                    // 'jns_perawatan_lab.nm_perawatan',
                    'detail_periksa_lab.no_rawat',
                    'detail_periksa_lab.tgl_periksa',
                    'detail_periksa_lab.jam',
                    'detail_periksa_lab.kd_jenis_prw',
                    'detail_periksa_lab.nilai',
                    'detail_periksa_lab.nilai_rujukan',
                    'detail_periksa_lab.keterangan',
                    'periksa_lab.nip as petugas',
                    'periksa_lab.dokter_perujuk',
                    'periksa_lab.kd_dokter as dokter_lab',
                    'template_laboratorium.Pemeriksaan',
                    'template_laboratorium.id_template',
                    'template_laboratorium.satuan'
                )
                ->where('detail_periksa_lab.no_rawat', $id)
                ->groupBy('template_laboratorium.id_template', 'detail_periksa_lab.kd_jenis_prw')
                ->get();
        } else {
            $hasil_periksa = null;
        }

        // dd($cek, $hasil_periksa);
        return array($cek, $hasil_periksa);
        // return view('vedika.lab', compact(
        //     'data',
        //     'petugas',
        //     'dokterLab',
        //     'dokterPerujuk',
        //     'hasil_periksa'
        // ));
    }

    public function labRajal($id)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Pasien Rajal');
        session()->put('cucu', 'Lab');

        $id = Crypt::decrypt($id);

        $cek = DB::connection('mysqlkhanza')->table('permintaan_lab')
            ->join('dokter', 'dokter.kd_dokter', '=', 'permintaan_lab.dokter_perujuk')
            ->select(
                'permintaan_lab.noorder',
                'permintaan_lab.no_rawat',
                'permintaan_lab.tgl_permintaan',
                'permintaan_lab.jam_permintaan',
                'permintaan_lab.tgl_hasil',
                'permintaan_lab.jam_hasil',
                'permintaan_lab.status',
                'dokter.nm_dokter'
            )
            // ->where('permintaan_lab.status', '=', 'ralan')
            ->where('permintaan_lab.no_rawat', '=', $id)
            ->get();

        // dd($cek);

        if ($cek->count() == 1) {
            $data = DB::connection('mysqlkhanza')->table('detail_periksa_lab')
                ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'detail_periksa_lab.no_rawat')
                ->leftJoin('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
                ->leftJoin('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
                ->join('permintaan_lab', 'permintaan_lab.no_rawat', '=', 'detail_periksa_lab.no_rawat')
                ->join('periksa_lab', 'periksa_lab.no_rawat', '=', 'detail_periksa_lab.no_rawat')
                ->select(
                    'pasien.no_rkm_medis',
                    'pasien.nm_pasien',
                    'pasien.jk',
                    'pasien.tgl_lahir',
                    'reg_periksa.almt_pj',
                    'reg_periksa.umurdaftar',
                    'reg_periksa.sttsumur',
                    'reg_periksa.no_rawat',
                    'reg_periksa.kd_poli',
                    'poliklinik.nm_poli',
                    'permintaan_lab.dokter_perujuk',
                    'permintaan_lab.noorder',
                    'permintaan_lab.tgl_permintaan',
                    'permintaan_lab.jam_permintaan',
                    'permintaan_lab.tgl_hasil',
                    'permintaan_lab.jam_hasil',
                    'periksa_lab.nip as petugas',
                    'periksa_lab.kd_dokter as dokter_lab'
                )
                ->where('reg_periksa.no_rawat', '=', $id)
                ->first();

            if (!empty($data)) {
                $petugas =  DB::connection('mysqlkhanza')->table('petugas')
                    ->select('petugas.nip', 'petugas.nama')
                    ->where('petugas.nip', $data->petugas)
                    ->first();
                $dokterLab =  DB::connection('mysqlkhanza')->table('dokter')
                    ->select('dokter.kd_dokter', 'dokter.nm_dokter')
                    ->where('dokter.kd_dokter', $data->dokter_lab)
                    ->first();
                $dokterPerujuk =  DB::connection('mysqlkhanza')->table('dokter')
                    ->select('dokter.kd_dokter', 'dokter.nm_dokter')
                    ->where('dokter.kd_dokter', $data->dokter_perujuk)
                    ->first();
                $hasil_periksa = DB::connection('mysqlkhanza')->table('detail_periksa_lab')
                    ->join('jns_perawatan_lab', 'jns_perawatan_lab.kd_jenis_prw', '=', 'detail_periksa_lab.kd_jenis_prw')
                    ->join('template_laboratorium', 'template_laboratorium.id_template', '=', 'detail_periksa_lab.id_template')
                    ->select(
                        'jns_perawatan_lab.nm_perawatan',
                        'detail_periksa_lab.no_rawat',
                        'detail_periksa_lab.jam',
                        'detail_periksa_lab.nilai',
                        'detail_periksa_lab.nilai_rujukan',
                        'detail_periksa_lab.keterangan',
                        'template_laboratorium.Pemeriksaan',
                        'template_laboratorium.satuan'
                    )
                    ->where('detail_periksa_lab.no_rawat', $id)
                    ->get();
            }

            return view('vedika.lab', compact(
                'data',
                'petugas',
                'dokterLab',
                'dokterPerujuk',
                'hasil_periksa'
            ));
        } else {
            // dd($cek, 'multi permintaan lab');
            $pasien = DB::connection('mysqlkhanza')->table('reg_periksa')
                ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
                ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
                ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
                ->select(
                    'pasien.no_rkm_medis',
                    'pasien.nm_pasien',
                    'pasien.jk',
                    'pasien.tgl_lahir',
                    'reg_periksa.almt_pj',
                    'reg_periksa.umurdaftar',
                    'reg_periksa.sttsumur',
                    'reg_periksa.no_rawat',
                    'reg_periksa.kd_poli',
                    'dokter.nm_dokter',
                    'poliklinik.nm_poli'
                )
                ->where('reg_periksa.no_rawat', '=', $id)
                ->first();
            // dd($cek);
            foreach ($cek as $index => $loop) {
                $data[$index++] = DB::connection('mysqlkhanza')->table('detail_periksa_lab')
                    ->join('jns_perawatan_lab', 'jns_perawatan_lab.kd_jenis_prw', '=', 'detail_periksa_lab.kd_jenis_prw')
                    ->join('periksa_lab', 'periksa_lab.no_rawat', '=', 'detail_periksa_lab.no_rawat')
                    ->join('template_laboratorium', 'template_laboratorium.kd_jenis_prw', '=', 'detail_periksa_lab.kd_jenis_prw')
                    ->select(
                        'detail_periksa_lab.no_rawat',
                        'detail_periksa_lab.tgl_periksa',
                        'detail_periksa_lab.jam',
                        'detail_periksa_lab.id_template',
                        'detail_periksa_lab.nilai',
                        'detail_periksa_lab.nilai_rujukan',
                        'detail_periksa_lab.keterangan',
                        'jns_perawatan_lab.nm_perawatan',
                        'periksa_lab.nip as petugas',
                        'periksa_lab.dokter_perujuk',
                        'periksa_lab.kd_dokter as dokter_lab',
                        'template_laboratorium.satuan',
                        'template_laboratorium.Pemeriksaan'
                    )
                    ->where('detail_periksa_lab.jam', '=', $loop->jam_hasil)
                    // ->where('detail_periksa_lab.tgl_periksa', '=', $loop->tgl_hasil)
                    ->where('detail_periksa_lab.no_rawat', '=', $loop->no_rawat)
                    ->orderBy('detail_periksa_lab.id_template', 'ASC')
                    ->groupBy('template_laboratorium.Pemeriksaan')
                    ->get();
            }

            return view('vedika.multi_lab', compact(
                'data',
                // 'petugas',
                // 'dokterLab',
                // 'dokterPerujuk',
                'pasien',
                'cek'
            ));
        }
    }

    public function labRanap($id)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Pasien Ranap');
        session()->put('cucu', 'Lab');

        $id = Crypt::decrypt($id);

        $cek = DB::connection('mysqlkhanza')->table('permintaan_lab')
            ->join('dokter', 'dokter.kd_dokter', '=', 'permintaan_lab.dokter_perujuk')

            ->select(
                'permintaan_lab.noorder',
                'permintaan_lab.no_rawat',
                'permintaan_lab.tgl_permintaan',
                'permintaan_lab.jam_permintaan',
                'permintaan_lab.tgl_hasil',
                'permintaan_lab.jam_hasil',
                'permintaan_lab.status',
                'dokter.nm_dokter'
            )
            // ->where('permintaan_lab.status', '=', 'ralan')
            ->where('permintaan_lab.no_rawat', '=', $id)
            ->get();

        // dd($cek, 'multi permintaan lab');
        $pasien = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->select(
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.jk',
                'pasien.tgl_lahir',
                'reg_periksa.almt_pj',
                'reg_periksa.no_rawat',
                'kamar_inap.kd_kamar'
            )
            ->where('reg_periksa.no_rawat', '=', $id)
            ->first();

        $kamar = DB::connection('mysqlkhanza')->table('kamar')
            ->join('bangsal', 'bangsal.kd_bangsal', '=', 'kamar.kd_bangsal')
            ->select(
                'kamar.kd_kamar',
                'bangsal.nm_bangsal'
            )
            ->where('kamar.kd_kamar', '=', $pasien->kd_kamar)
            ->first();

        // dd($cek, $pasien, $kamar);
        foreach ($cek as $index => $loop) {
            $data[$index++] = DB::connection('mysqlkhanza')->table('detail_periksa_lab')
                ->join('jns_perawatan_lab', 'jns_perawatan_lab.kd_jenis_prw', '=', 'detail_periksa_lab.kd_jenis_prw')
                // ->join('periksa_lab', 'periksa_lab.no_rawat', '=', 'detail_periksa_lab.no_rawat')
                ->join('template_laboratorium', 'template_laboratorium.id_template', '=', 'detail_periksa_lab.id_template')
                ->select(
                    'detail_periksa_lab.no_rawat',
                    'detail_periksa_lab.tgl_periksa',
                    'detail_periksa_lab.jam',
                    'detail_periksa_lab.id_template',
                    'detail_periksa_lab.nilai',
                    'detail_periksa_lab.nilai_rujukan',
                    'detail_periksa_lab.keterangan',
                    'jns_perawatan_lab.nm_perawatan',
                    // 'periksa_lab.nip as petugas',
                    // 'periksa_lab.dokter_perujuk',
                    // 'periksa_lab.kd_dokter as dokter_lab',
                    'template_laboratorium.satuan',
                    'template_laboratorium.Pemeriksaan'
                )
                ->where('detail_periksa_lab.jam', '=', $loop->jam_hasil)
                ->where('detail_periksa_lab.tgl_periksa', '=', $loop->tgl_hasil)
                ->where('detail_periksa_lab.no_rawat', '=', $loop->no_rawat)
                // ->orderBy('detail_periksa_lab.id_template', 'ASC')
                // ->groupBy(
                //     'detail_periksa_lab.id_template',
                //     'detail_periksa_lab.nilai',
                //     'template_laboratorium.Pemeriksaan'
                // )
                ->get();
        }

        // dd($cek, $pasien, $kamar, $data);

        return view('vedika.multi_lab_ranap', compact(
            'data',
            'kamar',
            // 'dokterLab',
            // 'dokterPerujuk',
            'pasien',
            'cek'
        ));
    }

    public function radioRajal($id)
    {
        // session()->put('ibu', 'Vedika');
        // session()->put('anak', 'Pasien Rajal');
        // session()->put('cucu', 'Radiologi');

        // $id = Crypt::decrypt($id);

        $data = DB::connection('mysqlkhanza')->table('permintaan_radiologi')
            ->join('hasil_radiologi', 'hasil_radiologi.no_rawat', '=', 'permintaan_radiologi.no_rawat')
            ->join('permintaan_pemeriksaan_radiologi', 'permintaan_pemeriksaan_radiologi.noorder', '=', 'permintaan_radiologi.noorder')
            ->join('dokter', 'dokter.kd_dokter', '=', 'permintaan_radiologi.dokter_perujuk')
            ->leftJoin('jns_perawatan_radiologi', 'jns_perawatan_radiologi.kd_jenis_prw', '=', 'permintaan_pemeriksaan_radiologi.kd_jenis_prw')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'permintaan_radiologi.no_rawat')
            ->leftJoin('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->leftJoin('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->select(
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.jk',
                'pasien.tgl_lahir',
                'reg_periksa.almt_pj',
                'reg_periksa.umurdaftar',
                'reg_periksa.sttsumur',
                'reg_periksa.no_rawat',
                'reg_periksa.kd_poli',
                'poliklinik.nm_poli',
                'dokter.nm_dokter',
                'permintaan_radiologi.dokter_perujuk',
                'permintaan_radiologi.noorder',
                'permintaan_radiologi.tgl_permintaan',
                'permintaan_radiologi.jam_permintaan',
                'jns_perawatan_radiologi.nm_perawatan',
                'hasil_radiologi.tgl_periksa',
                'hasil_radiologi.jam',
                'hasil_radiologi.hasil'
            )
            ->where('reg_periksa.no_rawat', '=', $id)
            ->first();

        if (!empty($data)) {
            $dokterRad =  DB::connection('mysqlkhanza')->table('set_pjlab')
                ->join('dokter', 'dokter.kd_dokter', '=', 'set_pjlab.kd_dokterrad')
                ->select('dokter.kd_dokter', 'dokter.nm_dokter')
                ->first();
        } else {
            $dokterRad = null;
        }

        // dd($data);
        return array($data, $dokterRad);

        // return view('vedika.radiologi', compact(
        //     'data',
        //     'dokterRad'
        // ));
    }

    public function radioRanap($id)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Pasien Ranap');
        session()->put('cucu', 'Radiologi');

        $id = Crypt::decrypt($id);

        $data = DB::connection('mysqlkhanza')->table('permintaan_radiologi')
            ->join('hasil_radiologi', 'hasil_radiologi.no_rawat', '=', 'permintaan_radiologi.no_rawat')
            ->join('permintaan_pemeriksaan_radiologi', 'permintaan_pemeriksaan_radiologi.noorder', '=', 'permintaan_radiologi.noorder')
            ->join('dokter', 'dokter.kd_dokter', '=', 'permintaan_radiologi.dokter_perujuk')
            ->leftJoin('jns_perawatan_radiologi', 'jns_perawatan_radiologi.kd_jenis_prw', '=', 'permintaan_pemeriksaan_radiologi.kd_jenis_prw')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'permintaan_radiologi.no_rawat')
            ->leftJoin('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->leftJoin('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->select(
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.jk',
                'pasien.tgl_lahir',
                'reg_periksa.almt_pj',
                'reg_periksa.umurdaftar',
                'reg_periksa.sttsumur',
                'reg_periksa.no_rawat',
                'reg_periksa.kd_poli',
                'poliklinik.nm_poli',
                'dokter.nm_dokter',
                'permintaan_radiologi.dokter_perujuk',
                'permintaan_radiologi.noorder',
                'permintaan_radiologi.tgl_permintaan',
                'permintaan_radiologi.jam_permintaan',
                'jns_perawatan_radiologi.nm_perawatan',
                'hasil_radiologi.tgl_periksa',
                'hasil_radiologi.jam',
                'hasil_radiologi.hasil'
            )
            ->where('reg_periksa.no_rawat', '=', $id)
            ->first();

        if (!empty($data)) {
            $dokterRad =  DB::connection('mysqlkhanza')->table('set_pjlab')
                ->join('dokter', 'dokter.kd_dokter', '=', 'set_pjlab.kd_dokterrad')
                ->select('dokter.kd_dokter', 'dokter.nm_dokter')
                ->first();
        }

        // dd($data, $dokterRad);

        return view('vedika.radiologi', compact(
            'data',
            'dokterRad'
        ));
    }

    public function triase($id)
    {
        // session()->put('ibu', 'Vedika');
        // session()->put('anak', 'Pasien Rajal');
        // session()->put('cucu', 'Triase');

        // $id = Crypt::decrypt($id);

        $data = DB::connection('mysqlkhanza')->table('data_triase_igd')
            ->join('master_triase_macam_kasus', 'master_triase_macam_kasus.kode_kasus', '=', 'data_triase_igd.kode_kasus')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'data_triase_igd.no_rawat')
            ->leftJoin('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->select(
                'pasien.no_rkm_medis',
                'pasien.no_ktp',
                'pasien.nm_pasien',
                'pasien.jk',
                'pasien.tgl_lahir',
                'pasien.alamat',
                'data_triase_igd.tgl_kunjungan',
                'data_triase_igd.cara_masuk',
                'master_triase_macam_kasus.macam_kasus',
                'data_triase_igd.tekanan_darah',
                'data_triase_igd.nadi',
                'data_triase_igd.pernapasan',
                'data_triase_igd.suhu',
                'data_triase_igd.saturasi_o2',
                'data_triase_igd.nyeri'
            )
            ->where('data_triase_igd.no_rawat', '=', $id)
            ->first();

        // dd($data);
        $primer = DB::connection('mysqlkhanza')->table('data_triase_igdprimer')
            ->join('petugas', 'petugas.nip', '=', 'data_triase_igdprimer.nik')
            ->select(
                'data_triase_igdprimer.no_rawat',
                'data_triase_igdprimer.keluhan_utama',
                'data_triase_igdprimer.kebutuhan_khusus',
                'data_triase_igdprimer.catatan',
                'data_triase_igdprimer.plan',
                'data_triase_igdprimer.tanggaltriase',
                'petugas.nama',
                'petugas.nip'
            )
            ->where('data_triase_igdprimer.no_rawat', '=', $id)
            ->first();

        $sekunder = DB::connection('mysqlkhanza')->table('data_triase_igdsekunder')
            ->join('petugas', 'petugas.nip', '=', 'data_triase_igdsekunder.nik')
            ->select(
                'data_triase_igdsekunder.no_rawat',
                'data_triase_igdsekunder.anamnesa_singkat',
                'data_triase_igdsekunder.catatan',
                'data_triase_igdsekunder.plan',
                'data_triase_igdsekunder.tanggaltriase',
                'petugas.nama',
                'petugas.nip'
            )
            ->where('data_triase_igdsekunder.no_rawat', '=', $id)
            ->first();

        $skala[1] =  DB::connection('mysqlkhanza')->table('master_triase_skala1')
            ->join('data_triase_igddetail_skala1', 'data_triase_igddetail_skala1.kode_skala1', '=', 'master_triase_skala1.kode_skala1')
            ->join('master_triase_pemeriksaan', 'master_triase_pemeriksaan.kode_pemeriksaan', '=', 'master_triase_skala1.kode_pemeriksaan')
            ->select(
                'data_triase_igddetail_skala1.no_rawat',
                'master_triase_pemeriksaan.nama_pemeriksaan',
                'master_triase_skala1.pengkajian_skala1 as pengkajian_skala'
            )
            ->where('data_triase_igddetail_skala1.no_rawat', '=', $id)
            ->get();

        $skala[2] =  DB::connection('mysqlkhanza')->table('master_triase_skala2')
            ->join('data_triase_igddetail_skala2', 'data_triase_igddetail_skala2.kode_skala2', '=', 'master_triase_skala2.kode_skala2')
            ->join('master_triase_pemeriksaan', 'master_triase_pemeriksaan.kode_pemeriksaan', '=', 'master_triase_skala2.kode_pemeriksaan')
            ->select(
                'data_triase_igddetail_skala2.no_rawat',
                'master_triase_pemeriksaan.nama_pemeriksaan',
                'master_triase_skala2.pengkajian_skala2 as pengkajian_skala'
            )
            ->where('data_triase_igddetail_skala2.no_rawat', '=', $id)
            ->get();

        $skala[3] =  DB::connection('mysqlkhanza')->table('master_triase_skala3')
            ->join('data_triase_igddetail_skala3', 'data_triase_igddetail_skala3.kode_skala3', '=', 'master_triase_skala3.kode_skala3')
            ->join('master_triase_pemeriksaan', 'master_triase_pemeriksaan.kode_pemeriksaan', '=', 'master_triase_skala3.kode_pemeriksaan')
            ->select(
                'data_triase_igddetail_skala3.no_rawat',
                'master_triase_pemeriksaan.nama_pemeriksaan',
                'master_triase_skala3.pengkajian_skala3 as pengkajian_skala'
            )
            ->where('data_triase_igddetail_skala3.no_rawat', '=', $id)
            ->get();

        $skala[4] =  DB::connection('mysqlkhanza')->table('master_triase_skala4')
            ->join('data_triase_igddetail_skala4', 'data_triase_igddetail_skala4.kode_skala4', '=', 'master_triase_skala4.kode_skala4')
            ->join('master_triase_pemeriksaan', 'master_triase_pemeriksaan.kode_pemeriksaan', '=', 'master_triase_skala4.kode_pemeriksaan')
            ->select(
                'data_triase_igddetail_skala4.no_rawat',
                'master_triase_pemeriksaan.nama_pemeriksaan',
                'master_triase_skala4.pengkajian_skala4 as pengkajian_skala'
            )
            ->where('data_triase_igddetail_skala4.no_rawat', '=', $id)
            ->get();

        $skala[5] =  DB::connection('mysqlkhanza')->table('master_triase_skala5')
            ->join('data_triase_igddetail_skala5', 'data_triase_igddetail_skala5.kode_skala5', '=', 'master_triase_skala5.kode_skala5')
            ->join('master_triase_pemeriksaan', 'master_triase_pemeriksaan.kode_pemeriksaan', '=', 'master_triase_skala5.kode_pemeriksaan')
            ->select(
                'data_triase_igddetail_skala5.no_rawat',
                'master_triase_pemeriksaan.nama_pemeriksaan',
                'master_triase_skala5.pengkajian_skala5 as pengkajian_skala'
            )
            ->where('data_triase_igddetail_skala5.no_rawat', '=', $id)
            ->get();

        // dd($data, $primer, $sekunder, $skala1, $skala2, $skala3, $skala4, $skala5);
        return array($data, $primer, $sekunder, $skala);

        // return view('vedika.triase', compact(
        //     'data',
        //     'primer',
        //     'sekunder',
        //     'skala'
        // ));
    }

    public function buktiPelayanan($id)
    {
        // session()->put('ibu', 'Vedika');
        // session()->put('anak', 'Pasien Rajal');
        // session()->put('cucu', 'Surat Bukti Pelayanan Kesehatan');

        // $id = Crypt::decrypt($id);

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->select(
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.jk',
                'pasien.tgl_lahir',
                'pasien.alamat',
                'reg_periksa.almt_pj',
                'reg_periksa.umurdaftar',
                'reg_periksa.sttsumur',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.stts',
                'reg_periksa.no_rawat',
                'reg_periksa.kd_poli',
                'poliklinik.nm_poli',
                'dokter.nm_dokter',
                'dokter.kd_dokter',
                'penjab.png_jawab'
            )
            ->where('reg_periksa.no_rawat', '=', $id)
            ->first();

        $diagnosa = DB::connection('mysqlkhanza')->table('diagnosa_pasien')
            ->join('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
            ->select(
                'diagnosa_pasien.no_rawat',
                'diagnosa_pasien.kd_penyakit',
                'diagnosa_pasien.status',
                'penyakit.nm_penyakit'
            )
            ->where('diagnosa_pasien.status', '=', 'Ralan')
            ->where('diagnosa_pasien.no_rawat', '=', $id)
            ->get();

        $prosedur = DB::connection('mysqlkhanza')->table('prosedur_pasien')
            ->join('icd9', 'icd9.kode', '=', 'prosedur_pasien.kode')
            ->select(
                'prosedur_pasien.no_rawat',
                'prosedur_pasien.kode',
                'prosedur_pasien.status',
                'icd9.deskripsi_panjang'
            )
            ->where('prosedur_pasien.status', '=', 'Ralan')
            ->where('prosedur_pasien.no_rawat', '=', $id)
            ->get();
        // dd($id, $data, $diagnosa, $prosedur);

        return array($diagnosa, $prosedur);
        // return view('vedika.sbpk', compact(
        //     'data',
        //     'diagnosa',
        //     'prosedur'
        // ));
    }

    public function ringkasanIgd($id)
    {
        // session()->put('ibu', 'Vedika');
        // session()->put('anak', 'Pasien Rajal');
        // session()->put('cucu', 'Ringkasan Pasien Gawat Darurat');

        // $id = Crypt::decrypt($id);

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('penilaian_medis_igd', 'penilaian_medis_igd.no_rawat', '=', 'reg_periksa.no_rawat')
            // ->join('resume_pasien', 'resume_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->select(
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.jk',
                'pasien.tgl_lahir',
                'pasien.alamat',
                'pasien.no_ktp',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.stts',
                'reg_periksa.no_rawat',
                'reg_periksa.kd_poli',
                'dokter.nm_dokter',
                'dokter.kd_dokter',
                'penilaian_medis_igd.diagnosis'
            )
            ->where('reg_periksa.no_rawat', '=', $id)
            ->first();

        $resume = DB::connection('mysqlkhanza')->table('resume_pasien')
            ->select(
                'resume_pasien.no_rawat',
                'resume_pasien.kondisi_pulang',
                'resume_pasien.obat_pulang',
                'resume_pasien.tindak_lanjut',
                'resume_pasien.edukasi',
                'resume_pasien.tgl_selesai'
            )
            ->where('resume_pasien.no_rawat', '=', $id)
            ->first();

        // dd($id, $data, $resume);
        return array($data, $resume);

        // return view('vedika.resume_igd', compact(
        //     'data',
        //     'resume'
        // ));
    }

    public function berkas($id)
    {
        // session()->put('ibu', 'Vedika');
        // session()->put('anak', 'Pasien Rajal');
        // session()->put('cucu', 'Berkas');

        // $id = Crypt::decrypt($id);

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('diagnosa_pasien', 'diagnosa_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
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
                'dokter.nm_dokter',
                'diagnosa_pasien.kd_penyakit',
                'diagnosa_pasien.prioritas',
                'penyakit.nm_penyakit'
            )
            ->where('reg_periksa.no_rawat', '=', $id)
            ->where('diagnosa_pasien.prioritas', '=', 1)
            ->first();

        // $berkas = BerkasVedika::where('no_rawat', $id)
        //     ->get();
        // $master = MasterBerkasVedika::all();
        $berkas = DB::connection('mysqlkhanza')->table('berkas_digital_perawatan')
            ->join('master_berkas_digital', 'master_berkas_digital.kode', '=', 'berkas_digital_perawatan.kode')
            ->select(
                'master_berkas_digital.nama',
                'berkas_digital_perawatan.lokasi_file',
                'berkas_digital_perawatan.no_rawat'
            )
            ->where('berkas_digital_perawatan.no_rawat', '=', $id)
            ->get();

        $master =  DB::connection('mysqlkhanza')->table('master_berkas_digital')
            ->get();
        $path = Setting::where('nama', 'webappz_berkasrawat')->first();

        // dd($id, $data, $berkas, $path);
        return array($berkas, $master, $path);
        // if (empty($data)) {
        //     Session::flash('error', 'Data Berkas masih kosong!');

        //     return redirect()->back();
        // } else {
        //     return view('vedika.berkas', compact('data', 'berkas', 'master', 'path'));
        // }
    }

    public function berkasRanap($id)
    {
        session()->put('ibu', 'Vedika');
        session()->put('anak', 'Pasien Ranap');
        session()->put('cucu', 'Berkas');

        $id = Crypt::decrypt($id);

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('diagnosa_pasien', 'diagnosa_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
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
                'dokter.nm_dokter',
                'diagnosa_pasien.kd_penyakit',
                'diagnosa_pasien.prioritas',
                'penyakit.nm_penyakit'
            )
            ->where('reg_periksa.no_rawat', '=', $id)
            ->where('diagnosa_pasien.prioritas', '=', 1)
            ->first();

        // $berkas = BerkasVedika::where('no_rawat', $id)
        //     ->get();
        // $master = MasterBerkasVedika::all();

        $berkas = DB::connection('mysqlkhanza')->table('berkas_digital_perawatan')
            ->join('master_berkas_digital', 'master_berkas_digital.kode', '=', 'berkas_digital_perawatan.kode')
            ->select(
                'master_berkas_digital.nama',
                'berkas_digital_perawatan.lokasi_file',
                'berkas_digital_perawatan.no_rawat'
            )
            ->where('berkas_digital_perawatan.no_rawat', '=', $id)
            ->get();

        $master =  DB::connection('mysqlkhanza')->table('master_berkas_digital')
            ->get();

        $path = Setting::where('nama', 'webappz_berkasrawat')
            ->first();
        // dd($data, $berkas);

        return view('vedika.berkas', compact('data', 'berkas', 'master', 'path'));
    }

    public function berkasStore(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:pdf,jpg,jpeg|max:2048',
        ], [
            'file.mimes' => 'File yang diperbolehkan adalah file PDF, JPG/JPEG!',
            'file.max' => 'Ukuran file maksimal 2MB!'
        ]);


        $str = $request->master_berkas;
        $split = explode("-", $str);

        $data = new BerkasVedika();
        $data->no_rawat = $request->no_rawat;
        $data->master_berkas_vedika_id = $split[0];
        // $data->file = $request->file;

        //aksi file
        $file = $request->file('file');
        $tgl_registrasi = Carbon::parse($request->tgl_registrasi)->format('Ymd');
        $waktu_upload = Carbon::now()->format('YmdHis');
        $extension = $file->getClientOriginalExtension();
        $nama_file = substr($request->no_rawat, -6) . "_" . $split[1] . "_" . $waktu_upload . '.' . $extension;
        // isi dengan nama folder tempat kemana file diupload
        $tujuan_upload = "berkas_vedika/" . $tgl_registrasi;

        // dd($nama_file);
        $file->move($tujuan_upload, $nama_file);

        $data->file = $nama_file;
        $data->lokasi_berkas = $tujuan_upload;

        // dd($data);

        $data->save();

        $id = Crypt::encrypt($request->no_rawat);

        Session::flash('sukses', 'Data Berhasil ditambahkan!');

        return redirect("/vedika/rajal/$id/berkas");
    }

    public function berkasUpload(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:pdf,jpg,jpeg|max:2048',
        ], [
            'file.mimes' => 'File yang diperbolehkan adalah file PDF, JPG/JPEG!',
            'file.max' => 'Ukuran file maksimal 2MB!'
        ]);

        // dd($request);
        $str = $request->master_berkas;
        $split = explode("-", $str);

        $data['no_rawat'] = $request->no_rawat;
        $data['kode'] = $split[0];
        // $data->file = $request->file;

        //aksi file
        $file = $request->file('file');
        $tgl_registrasi = Carbon::parse($request->tgl_registrasi)->format('Ymd');
        $waktu_upload = Carbon::now()->format('YmdHis');
        $extension = $file->getClientOriginalExtension();
        $nama_file = $tgl_registrasi . '_' . substr($request->no_rawat, -6) . "_" . $split[1] . '.' . $extension;

        // isi dengan nama folder tempat kemana file diupload
        $path = Setting::where('nama', 'webappz_berkasrawat')
            ->first();
        $tujuan_upload = $path->base_url . "pages/upload";

        // dd($nama_file);
        // $file->move($tujuan_upload, $nama_file);

        // Storage::disk('sftp')->putFileAs($file, new File('pages/upload/'), $nama_file);
        // Storage::put($nama_file, $file);
        $pindah = $request->file('file')->storeAs(
            'pages/upload',
            $nama_file,
            'sftp'
        );

        // Storage::disk('sftp')->setVisibility('pages/upload' . $nama_file, 'public');


        $data['lokasi_file'] = 'pages/upload/' . $nama_file;
        // dd($data, $pindah);

        DB::connection('mysqlkhanza')->table('berkas_digital_perawatan')->insert($data);

        $id = Crypt::encrypt($request->no_rawat);

        Session::flash('sukses', 'Data Berhasil ditambahkan!');

        // dd(Session(''))

        if (Session('anak') == 'Pasien Rajal')
            return redirect("/vedika/rajal/$id/berkas");
        else
            return redirect("/vedika/ranap/$id/berkas");
    }

    public function berkasShow($id)
    {
        $id = Crypt::decrypt($id);

        // dd($id);

        $file = Storage::disk('sftp')->download($id);
        // dd($id, $file);

        // $file = '//10.10.28.10/webappz/berkasrawat/' . $id;

        // $file = BerkasVedika::find($id);
        // Force download of the file
        // $this->file_to_download   = $file->lokasi_berkas . '/' . $file->file;
        //return response()->streamDownload(function () {
        //    echo file_get_contents($this->file_to_download);
        //}, $file.'.pdf');
        // return response()->file($this->file_to_download);
        // return response()->file($file);
        return $file;
    }

    public function berkasDelete($id)
    {
        $id = Crypt::decrypt($id);
        // $delete = BerkasVedika::find($id);

        // $file = public_path($delete->lokasi_berkas . '/' . $delete->file);

        // if (File::exists($file)) {
        //     File::delete($file);
        // } else {
        //     dd('file tidak eksis', $file);
        // }
        $berkas = DB::connection('mysqlkhanza')->table('berkas_digital_perawatan')
            ->select(
                'berkas_digital_perawatan.lokasi_file',
                'berkas_digital_perawatan.no_rawat'
            )
            ->where('berkas_digital_perawatan.lokasi_file', '=', $id)
            ->first();
        if (Storage::disk('sftp')->exists($berkas->lokasi_file)) {
            // dd('eksis');
            Storage::disk('sftp')->delete($berkas->lokasi_file);
            $berkas = DB::connection('mysqlkhanza')->table('berkas_digital_perawatan')
                ->select(
                    'berkas_digital_perawatan.lokasi_file',
                    'berkas_digital_perawatan.no_rawat'
                )
                ->where('berkas_digital_perawatan.lokasi_file', '=', $id)
                ->delete();

            Session::flash('sukses', 'Data Berhasil dihapus!');
        } else {
            // dd('tidak eksis');
            Session::flash('error', 'Data tidak ditemukan');
        }

        // dd($berkas);

        return redirect()->back();
    }

    //Master vedika ndak dipake karena langsung ambil dikhanza aja sinkron dengan data simrs berkas digital
    public function index()
    {
        session()->put('ibu', 'Master Data');
        session()->put('anak', 'Berkas Vedika');
        session()->forget('cucu');

        $data = MasterBerkasVedika::all();

        return view('masters.vedika_berkas', compact('data'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'nama' => 'required|unique:master_berkas_vedikas,nama',
            // 'keterangan' => 'required',
        ], [
            'nama.unique' => 'Nama Berkas sudah terdaftar!',
        ]);

        $data = new MasterBerkasVedika();
        $data->nama = $request->nama;
        $data->keterangan = $request->keterangan;
        $data->save();

        Session::flash('sukses', 'Data Berhasil ditambahkan!');

        return redirect('/master/vedika');
    }

    public function edit($id)
    {
        $id = Crypt::decrypt($id);

        $data = MasterBerkasVedika::find($id);

        return view('masters.vedika_berkas_edit', compact('data'));
    }

    public function update($id, Request $request)
    {
        $data = MasterBerkasVedika::find($id);
        $data->nama = $request->nama;
        $data->keterangan = $request->keterangan;
        $data->save();

        Session::flash('sukses', 'Data Berhasil diupdate!');

        return redirect('/master/vedika');
    }

    public function delete($id)
    {
        $id = Crypt::decrypt($id);
        $delete = MasterBerkasVedika::find($id);
        $delete->delete();

        Session::flash('sukses', 'Data Berhasil dihapus!');

        return redirect()->back();
    }
}
