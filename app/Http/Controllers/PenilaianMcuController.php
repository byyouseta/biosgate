<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PenilaianMcuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($id)
    {
        session()->put('ibu', 'Berkas RM');
        session()->put('anak', 'Rawat Jalan/IGD');
        session()->put('cucu', 'Penilaian Awal Keperawatan Umum');

        $noRawat = Crypt::decrypt($id);

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->leftJoin('bahasa_pasien', 'bahasa_pasien.id', '=', 'pasien.bahasa_pasien')
            ->leftJoin('cacat_fisik', 'cacat_fisik.id', '=', 'pasien.cacat_fisik')
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.kd_dokter',
                'reg_periksa.kd_poli',
                'dokter.nm_dokter',
                'pasien.nm_pasien',
                'pasien.jk',
                'pasien.tgl_lahir',
                'pasien.bahasa_pasien',
                'pasien.cacat_fisik',
                'pasien.agama',
                'bahasa_pasien.nama_bahasa',
                'cacat_fisik.nama_cacat'
            )
            ->where('reg_periksa.no_rawat', $noRawat)
            ->first();

        $data_masalah = DB::connection('mysqlkhanza')->table('master_masalah_keperawatan')
            ->get();

        $data_update = DB::connection('mysqlkhanza')->table('penilaian_awal_keperawatan_ralan')
            ->leftJoin('penilaian_awal_keperawatan_ralan_tambahan', 'penilaian_awal_keperawatan_ralan_tambahan.no_rawat', '=', 'penilaian_awal_keperawatan_ralan.no_rawat')
            ->where('penilaian_awal_keperawatan_ralan.no_rawat', '=', $noRawat)
            ->first();

        $data_resiko = DB::connection('mysqlkhanza')->table('master_intervensi_resikojatuh')
            ->get();

        $data_petugas = DB::connection('mysqlkhanza')->table('petugas')
            ->select(
                'nip',
                'nama'
            )
            ->get();
        $data_masalahList = DB::connection('mysqlkhanza')->table('penilaian_awal_keperawatan_ralan_masalah')
            ->where('no_rawat', $noRawat)
            ->get();
        $data_resikoPlan = DB::connection('mysqlkhanza')->table('penilaian_awal_keperawatan_ralan_resikojatuh')
            ->where('no_rawat', $noRawat)
            ->get();
        $data_resikoImplementasi = DB::connection('mysqlkhanza')->table('penilaian_awal_keperawatan_ralan_resikojatuh_implementasi')
            ->where('no_rawat', $noRawat)
            ->get();

        // dd($data, $data_masalah, $data_update);

        return view('berkas_rm.penilaian_mcu', compact(
            'data',
            'data_masalah',
            'data_update',
            'data_resiko',
            'data_petugas',
            'data_masalahList',
            'data_resikoPlan',
            'data_resikoImplementasi'
        ));
    }

    public function store(Request $request)
    {
        // dd($request);
        $cek =  DB::connection('mysqlkhanza')->table('penilaian_awal_keperawatan_ralan')->where('no_rawat', $request->noRawat)->first();
        if (empty($cek)) {
            $data['no_rawat'] = $request->noRawat;
            $data['tanggal'] = new Carbon($request->tanggal);
            $data['informasi'] = $request->informasi;
            $data['td'] = $request->td != null ? $request->td : '';
            $data['nadi'] = $request->nadi != null ? $request->nadi : '';
            $data['rr'] = $request->rr != null ? $request->rr : '';
            $data['suhu'] = $request->suhu != null ? $request->suhu : '';
            $data['gcs'] = $request->gcs != null ? $request->gcs : '';
            $data['bb'] = $request->bb != null ? $request->bb : '';
            $data['tb'] = $request->tb != null ? $request->tb : '';
            $data['bmi'] = $request->bmi != null ? $request->bmi : '';
            $data['keluhan_utama'] = $request->keluhan_utama != null ? $request->keluhan_utama : '';
            $data['rpd'] = $request->rpd != null ? $request->rpd : '';
            $data['rpk'] = $request->rpk != null ? $request->rpk : '';
            $data['rpo'] = $request->rpo != null ? $request->rpo : '';
            $data['alergi'] = $request->alergi != null ? $request->alergi : '';
            $data['alat_bantu'] = $request->alat_bantu != null ? $request->alat_bantu : '';
            $data['ket_bantu'] = $request->ket_bantu != null ? $request->ket_bantu : '';
            $data['prothesa'] = $request->prothesa != null ? $request->prothesa : '';
            $data['ket_pro'] = $request->ket_pro != null ? $request->ket_pro : '';
            $data['adl'] = $request->adl != null ? $request->adl : '';
            $data['status_psiko'] = $request->status_psiko != null ? $request->status_psiko : '';
            $data['ket_psiko'] = $request->ket_psico != null ? $request->ket_psico : '';
            $data['hub_keluarga'] = $request->hub_keluarga != null ? $request->hub_keluarga : '';
            $data['tinggal_dengan'] = $request->tinggal_dengan != null ? $request->tinggal_dengan : '';
            $data['ket_tinggal'] = $request->ket_tinggal != null ? $request->ket_tinggal : '';
            $data['ekonomi'] = $request->ekonomi != null ? $request->ekonomi : '';
            $data['budaya'] = $request->budaya != null ? $request->budaya : '';
            $data['ket_budaya'] = $request->ket_budaya != null ? $request->ket_budaya : '';
            $data['edukasi'] = $request->edukasi != null ? $request->edukasi : '';
            $data['ket_edukasi'] = $request->ket_edukasi != null ? $request->ket_edukasi : '';
            $data['berjalan_a'] = $request->berjalan_a != '-' ? $request->berjalan_a : 'Tidak';
            $data['berjalan_b'] = $request->berjalan_b != '-' ? $request->berjalan_b : 'Tidak';
            $data['berjalan_c'] = $request->berjalan_c != '-' ? $request->berjalan_c : 'Tidak';
            $data['hasil'] = $request->hasil != '-' ? $request->hasil : 'Tidak beresiko (tidak ditemukan a dan b)';
            $data['lapor'] = $request->lapor != '-' ? $request->lapor : 'Tidak';
            $data['ket_lapor'] = $request->ket_lapor != null ? $request->ket_lapor : '';
            $data['sg1'] = $request->sg1 != null ? $request->sg1 : '';
            $data['nilai1'] = $request->nilai1 != null ? $request->nilai1 : 0;
            $data['sg2'] = $request->sg2 != null ? $request->sg2 : '';
            $data['nilai2'] = $request->nilai2 != null ? $request->nilai2 : 0;
            $data['total_hasil'] = $request->total_hasil != null ? $request->total_hasil : 0;
            $data['nyeri'] = $request->nyeri != null ? $request->nyeri : '';
            $data['provokes'] = $request->provokes != null ? $request->provokes : '';
            $data['ket_provokes'] = $request->ket_provokes != null ? $request->ket_provokes : '';
            $data['quality'] = $request->quality != null ? $request->quality : '';
            $data['ket_quality'] = $request->ket_quality != null ? $request->ket_quality : '';
            $data['lokasi'] = $request->lokasi != null ? $request->lokasi : '';
            $data['menyebar'] = $request->menyebar != null ? $request->menyebar : '';
            $data['skala_nyeri'] = $request->skala_nyeri != null ? $request->skala_nyeri : '';
            $data['durasi'] = $request->durasi != null ? $request->durasi : '';
            $data['nyeri_hilang'] = $request->nyeri_hilang != null ? $request->nyeri_hilang : '';
            $data['ket_nyeri'] = $request->ket_nyeri != null ? $request->ket_nyeri : '';
            $data['pada_dokter'] = $request->pada_dokter != null ? $request->pada_dokter : '';
            $data['ket_dokter'] = $request->ket_dokter != null ? $request->ket_dokter : '';
            $data['rencana'] = $request->rencana != null ? $request->rencana : '';
            $data['nip'] = $request->petugas != null ? $request->petugas : '';
            $data['spo2'] = $request->spo2 != null ? $request->spo2 : '';
            $data['waktu_tunggu'] = $request->waktu_tunggu != null ? $request->waktu_tunggu : '';

            DB::connection('mysqlkhanza')->table('penilaian_awal_keperawatan_ralan')->insert($data);

            $dataTambahan['no_rawat'] = $request->noRawat;
            $dataTambahan['lingkar_perut'] = $request->lingkar_perut != null ? $request->lingkar_perut : '';

            DB::connection('mysqlkhanza')->table('penilaian_awal_keperawatan_ralan_tambahan')->insert($dataTambahan);
        }


        //Data resiko jatuh Plan
        if (!empty($request->resikojatuhPlan)) {
            foreach ($request->resikojatuhPlan as $data_resikojatuh) {
                // dd($data_resikojatuh);
                $data1['no_rawat'] = $request->noRawat;
                $data1['kode_intervensi'] = $data_resikojatuh;

                DB::connection('mysqlkhanza')->table('penilaian_awal_keperawatan_ralan_resikojatuh')->insert($data1);
            }
        }

        //Resiko jatuh implementasi
        if (!empty($request->resikojatuhImplementasi)) {
            foreach ($request->resikojatuhImplementasi as $data_resikojatuh) {
                // dd($data_resikojatuh);
                $data2['no_rawat'] = $request->noRawat;
                $data2['kode_implementasi'] = $data_resikojatuh;

                DB::connection('mysqlkhanza')->table('penilaian_awal_keperawatan_ralan_resikojatuh_implementasi')->insert($data2);
            }
        }

        if (!empty($request->masalah)) {
            foreach ($request->masalah as $data_masalah) {
                // dd($data_resikojatuh);
                $data3['no_rawat'] = $request->noRawat;
                $data3['kode_masalah'] = $data_masalah;

                DB::connection('mysqlkhanza')->table('penilaian_awal_keperawatan_ralan_masalah')->insert($data3);
            }
        }

        Session::flash('sukses', 'Data berhasil disimpan');
        return redirect()->back();
    }

    public function update(Request $request)
    {
        // dd($request);

        $UpdateDetails = DB::connection('mysqlkhanza')->table('penilaian_awal_keperawatan_ralan')
            ->where('no_rawat', '=', $request->noRawat)
            ->update(array(
                // 'no_rawat' => $request->noRawat,
                'tanggal' => new Carbon($request->tanggal),
                'informasi' => $request->informasi,
                'td' => $request->td != null ? $request->td : '',
                'nadi' => $request->nadi != null ? $request->nadi : '',
                'rr' => $request->rr != null ? $request->rr : '',
                'suhu' => $request->suhu != null ? $request->suhu : '',
                'gcs' => $request->gcs != null ? $request->gcs : '',
                'bb' => $request->bb != null ? $request->bb : '',
                'tb' => $request->tb != null ? $request->tb : '',
                'bmi' => $request->bmi != null ? $request->bmi : '',
                'keluhan_utama' => $request->keluhan_utama != null ? $request->keluhan_utama : '',
                'rpd' => $request->rpd != null ? $request->rpd : '',
                'rpk' => $request->rpk != null ? $request->rpk : '',
                'rpo' => $request->rpo != null ? $request->rpo : '',
                'alergi' => $request->alergi != null ? $request->alergi : '',
                'alat_bantu' => $request->alat_bantu != null ? $request->alat_bantu : '',
                'ket_bantu' => $request->ket_bantu != null ? $request->ket_bantu : '',
                'prothesa' => $request->prothesa != null ? $request->prothesa : '',
                'ket_pro' => $request->ket_pro != null ? $request->ket_pro : '',
                'adl' => $request->adl != null ? $request->adl : '',
                'status_psiko' => $request->status_psiko != null ? $request->status_psiko : '',
                'ket_psiko' => $request->ket_psico != null ? $request->ket_psico : '',
                'hub_keluarga' => $request->hub_keluarga != null ? $request->hub_keluarga : '',
                'tinggal_dengan' => $request->tinggal_dengan != null ? $request->tinggal_dengan : '',
                'ket_tinggal' => $request->ket_tinggal != null ? $request->ket_tinggal : '',
                'ekonomi' => $request->ekonomi != null ? $request->ekonomi : '',
                'budaya' => $request->budaya != null ? $request->budaya : '',
                'ket_budaya' => $request->ket_budaya != null ? $request->ket_budaya : '',
                'edukasi' => $request->edukasi != null ? $request->edukasi : '',
                'ket_edukasi' => $request->ket_edukasi != null ? $request->ket_edukasi : '',
                'berjalan_a' => $request->berjalan_a != '-' ? $request->berjalan_a : 'Tidak',
                'berjalan_b' => $request->berjalan_b != '-' ? $request->berjalan_b : 'Tidak',
                'berjalan_c' => $request->berjalan_c != '-' ? $request->berjalan_c : 'Tidak',
                'hasil' => $request->hasil != '-' ? $request->hasil : 'Tidak beresiko (tidak ditemukan a dan b)',
                'lapor' => $request->lapor != '-' ? $request->lapor : 'Tidak',
                'ket_lapor' => $request->ket_lapor != null ? $request->ket_lapor : '',
                'sg1' => $request->sg1 != null ? $request->sg1 : '',
                'nilai1' => $request->nilai1 != null ? $request->nilai1 : 0,
                'sg2' => $request->sg2 != null ? $request->sg2 : '',
                'nilai2' => $request->nilai2 != null ? $request->nilai2 : 0,
                'total_hasil' => $request->total_hasil != null ? $request->total_hasil : 0,
                'nyeri' => $request->nyeri != null ? $request->nyeri : '',
                'provokes' => $request->provokes != null ? $request->provokes : '',
                'ket_provokes' => $request->ket_provokes != null ? $request->ket_provokes : '',
                'quality' => $request->quality != null ? $request->quality : '',
                'ket_quality' => $request->ket_quality != null ? $request->ket_quality : '',
                'lokasi' => $request->lokasi != null ? $request->lokasi : '',
                'menyebar' => $request->menyebar != null ? $request->menyebar : '',
                'skala_nyeri' => $request->skala_nyeri != null ? $request->skala_nyeri : '',
                'durasi' => $request->durasi != null ? $request->durasi : '',
                'nyeri_hilang' => $request->nyeri_hilang != null ? $request->nyeri_hilang : '',
                'ket_nyeri' => $request->ket_nyeri != null ? $request->ket_nyeri : '',
                'pada_dokter' => $request->pada_dokter != null ? $request->pada_dokter : '',
                'ket_dokter' => $request->ket_dokter != null ? $request->ket_dokter : '',
                'rencana' => $request->rencana != null ? $request->rencana : '',
                'nip' => $request->petugas != null ? $request->petugas : '',
                'spo2' => $request->spo2 != null ? $request->spo2 : '',
                'waktu_tunggu' => $request->waktu_tunggu != null ? $request->waktu_tunggu : ''
            ));

        $updateTambahan = DB::connection('mysqlkhanza')->table('penilaian_awal_keperawatan_ralan_tambahan')
            ->where('no_rawat', '=', $request->noRawat)
            ->update(array('lingkar_perut' => $request->lingkar_perut != null ? $request->lingkar_perut : ''));

        //data resiko jatuh Plan
        if (!empty($request->resikojatuhPlan)) {
            $delete = DB::connection('mysqlkhanza')->table('penilaian_awal_keperawatan_ralan_resikojatuh')
                ->where('no_rawat', $request->noRawat)
                ->delete();

            foreach ($request->resikojatuhPlan as $data_resikojatuh) {
                // dd($data_resikojatuh);
                $data1['no_rawat'] = $request->noRawat;
                $data1['kode_intervensi'] = $data_resikojatuh;

                DB::connection('mysqlkhanza')->table('penilaian_awal_keperawatan_ralan_resikojatuh')->insert($data1);
            }
        }

        if (!empty($request->resikojatuhImplementasi)) {
            //data resiko jatuh Implementasi
            $delete = DB::connection('mysqlkhanza')->table('penilaian_awal_keperawatan_ralan_resikojatuh_implementasi')
                ->where('no_rawat', $request->noRawat)
                ->delete();

            foreach ($request->resikojatuhImplementasi as $data_resikojatuh) {
                // dd($data_resikojatuh);
                $data2['no_rawat'] = $request->noRawat;
                $data2['kode_implementasi'] = $data_resikojatuh;

                DB::connection('mysqlkhanza')->table('penilaian_awal_keperawatan_ralan_resikojatuh_implementasi')->insert($data2);
            }
        }

        if (!empty($request->masalah)) {
            $delete = DB::connection('mysqlkhanza')->table('penilaian_awal_keperawatan_ralan_masalah')
                ->where('no_rawat', $request->noRawat)
                ->delete();

            foreach ($request->masalah as $data_masalah) {
                // dd($data_resikojatuh);
                $data3['no_rawat'] = $request->noRawat;
                $data3['kode_masalah'] = $data_masalah;

                DB::connection('mysqlkhanza')->table('penilaian_awal_keperawatan_ralan_masalah')->insert($data3);
            }
        }

        Session::flash('sukses', 'Data berhasil diperbaharui');
        return redirect()->back();
    }

    public function soapie($id)
    {
        session()->put('ibu', 'Berkas RM');
        session()->put('anak', 'Rawat Jalan/IGD');
        session()->put('cucu', 'SOAPIE');

        $noRawat = Crypt::decrypt($id);

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->leftJoin('bahasa_pasien', 'bahasa_pasien.id', '=', 'pasien.bahasa_pasien')
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.kd_dokter',
                'reg_periksa.kd_poli',
                'dokter.nm_dokter',
                'pasien.nm_pasien',
                'pasien.jk',
                'pasien.tgl_lahir',
                'pasien.bahasa_pasien',
                'pasien.cacat_fisik',
                'pasien.agama',
                'bahasa_pasien.nama_bahasa'
            )
            ->where('reg_periksa.no_rawat', $noRawat)
            ->first();

        $data_pegawai = DB::connection('mysqlkhanza')->table('pegawai')
            ->select(
                'nik',
                'nama',
                'jbtn'
            )
            ->get();

        $data_soapie = DB::connection('mysqlkhanza')->table('pemeriksaan_ralan')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'pemeriksaan_ralan.no_rawat')
            ->leftJoin('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'pemeriksaan_ralan.nip')
            ->select(
                'pemeriksaan_ralan.no_rawat',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pemeriksaan_ralan.tgl_perawatan',
                'pemeriksaan_ralan.jam_rawat',
                'pemeriksaan_ralan.suhu_tubuh',
                'pemeriksaan_ralan.tensi',
                'pemeriksaan_ralan.nadi',
                'pemeriksaan_ralan.respirasi',
                'pemeriksaan_ralan.tinggi',
                'pemeriksaan_ralan.berat',
                'pemeriksaan_ralan.spo2',
                'pemeriksaan_ralan.gcs',
                'pemeriksaan_ralan.kesadaran',
                'pemeriksaan_ralan.keluhan',
                'pemeriksaan_ralan.pemeriksaan',
                'pemeriksaan_ralan.alergi',
                'pemeriksaan_ralan.imun_ke',
                'pemeriksaan_ralan.rtl',
                'pemeriksaan_ralan.penilaian',
                'pemeriksaan_ralan.instruksi',
                'pemeriksaan_ralan.evaluasi',
                'pemeriksaan_ralan.nip',
                'pegawai.nama as nm_dokter',
                'pegawai.jbtn'
            )
            ->orderBy('pemeriksaan_ralan.tgl_perawatan', 'DESC')
            ->where('pemeriksaan_ralan.no_rawat', $noRawat)
            ->get();

        $data_inputRalan = DB::connection('mysqlkhanza')->table('penilaian_awal_keperawatan_ralan')
            ->orderBy('tanggal', 'DESC')
            ->where('no_rawat', $noRawat)
            ->first();

        // dd($data_soapie, $data_inputRalan);

        return view('berkas_rm.soapie', compact('data', 'data_pegawai', 'data_soapie', 'data_inputRalan'));
    }

    public function soapieStore(Request $request)
    {
        // dd($request);
        // $cek =  DB::connection('mysqlkhanza')->table('pemeriksaan_ralan')->where('no_rawat', $request->noRawat)->first();
        // if (empty($cek)) {
        $data['no_rawat'] = $request->noRawat;
        $data['tgl_perawatan'] = Carbon::parse($request->tanggal)->format('Y-m-d');
        $data['jam_rawat'] = Carbon::parse($request->tanggal)->format('H:i:s');
        $data['suhu_tubuh'] = $request->suhu_tubuh != null ? $request->suhu_tubuh : '';
        $data['tensi'] = $request->tensi != null ? $request->tensi : '';
        $data['nadi'] = $request->nadi != null ? $request->nadi : '';
        $data['respirasi'] = $request->respirasi != null ? $request->respirasi : '';
        $data['tinggi'] = $request->tinggi != null ? $request->tinggi : '';
        $data['berat'] = $request->berat != null ? $request->berat : '';
        $data['spo2'] = $request->spo2 != null ? $request->spo2 : '';
        $data['gcs'] = $request->gcs != null ? $request->gcs : '';
        $data['kesadaran'] = $request->kesadaran != null ? $request->kesadaran : '';
        $data['keluhan'] = $request->keluhan != null ? $request->keluhan : '';
        $data['pemeriksaan'] = $request->pemeriksaan != null ? $request->pemeriksaan : '';
        $data['alergi'] = $request->alergi != null ? $request->alergi : '';
        $data['imun_ke'] = $request->imun_ke != null ? $request->imun_ke : '';
        $data['rtl'] = $request->rtl != null ? $request->rtl : '';
        $data['penilaian'] = $request->penilaian != null ? $request->penilaian : '';
        $data['instruksi'] = $request->instruksi != null ? $request->instruksi : '';
        $data['evaluasi'] = $request->evaluasi != null ? $request->evaluasi : '';
        $data['nip'] = $request->petugas != null ? $request->petugas : '';

        DB::connection('mysqlkhanza')->table('pemeriksaan_ralan')->insert($data);
        // }

        // update data reg periksa menjadi Sudah
        $update =  DB::connection('mysqlkhanza')->table('reg_periksa')->where('no_rawat', $request->noRawat)->update([
            'stts' => 'Sudah'
        ]);

        Session::flash('sukses', 'Data berhasil disimpan');
        return redirect()->back();
    }

    public function soapieEdit($id)
    {
        session()->put('ibu', 'Berkas RM');
        session()->put('anak', 'Rawat Jalan/IGD');
        session()->put('cucu', 'SOAPIE');

        $data_enkripsi = Crypt::decrypt($id);
        $data_pecah = explode('-', $data_enkripsi);
        $noRawat = $data_pecah[0];
        $jam_pelayanan = $data_pecah[1];

        $data = DB::connection('mysqlkhanza')->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->leftJoin('bahasa_pasien', 'bahasa_pasien.id', '=', 'pasien.bahasa_pasien')
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.kd_dokter',
                'reg_periksa.kd_poli',
                'dokter.nm_dokter',
                'pasien.nm_pasien',
                'pasien.jk',
                'pasien.tgl_lahir',
                'pasien.bahasa_pasien',
                'pasien.cacat_fisik',
                'pasien.agama',
                'bahasa_pasien.nama_bahasa'
            )
            ->where('reg_periksa.no_rawat', $noRawat)
            ->first();

        $data_pegawai = DB::connection('mysqlkhanza')->table('pegawai')
            ->select(
                'nik',
                'nama',
                'jbtn'
            )
            ->get();

        $data_soapie = DB::connection('mysqlkhanza')->table('pemeriksaan_ralan')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'pemeriksaan_ralan.no_rawat')
            ->leftJoin('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pegawai', 'pegawai.nik', '=', 'pemeriksaan_ralan.nip')
            ->select(
                'pemeriksaan_ralan.no_rawat',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pemeriksaan_ralan.tgl_perawatan',
                'pemeriksaan_ralan.jam_rawat',
                'pemeriksaan_ralan.suhu_tubuh',
                'pemeriksaan_ralan.tensi',
                'pemeriksaan_ralan.nadi',
                'pemeriksaan_ralan.respirasi',
                'pemeriksaan_ralan.tinggi',
                'pemeriksaan_ralan.berat',
                'pemeriksaan_ralan.spo2',
                'pemeriksaan_ralan.gcs',
                'pemeriksaan_ralan.kesadaran',
                'pemeriksaan_ralan.keluhan',
                'pemeriksaan_ralan.pemeriksaan',
                'pemeriksaan_ralan.alergi',
                'pemeriksaan_ralan.imun_ke',
                'pemeriksaan_ralan.rtl',
                'pemeriksaan_ralan.penilaian',
                'pemeriksaan_ralan.instruksi',
                'pemeriksaan_ralan.evaluasi',
                'pemeriksaan_ralan.nip',
                'pegawai.nama as nm_dokter',
                'pegawai.jbtn'
            )
            ->orderBy('pemeriksaan_ralan.tgl_perawatan', 'DESC')
            ->where('pemeriksaan_ralan.no_rawat', $noRawat)
            ->get();

        $data_inputRalan = DB::connection('mysqlkhanza')->table('pemeriksaan_ralan')
            ->where('no_rawat', $noRawat)
            ->where('jam_rawat', $jam_pelayanan)
            ->first();

        // dd($data_soapie, $data_inputRalan);

        return view('berkas_rm.soapie_edit', compact('data', 'data_pegawai', 'data_soapie', 'data_inputRalan'));
    }

    public function soapieUpdate(Request $request)
    {
        $UpdateDetails = DB::connection('mysqlkhanza')->table('pemeriksaan_ralan')
            ->where('no_rawat', '=', $request->noRawat)
            ->where('jam_rawat', '=', $request->tglPerawatan)
            ->update(array(
                'no_rawat' => $request->noRawat,
                'tgl_perawatan' => Carbon::parse($request->tanggal)->format('Y-m-d'),
                'jam_rawat' => Carbon::parse($request->tanggal)->format('H:i:s'),
                'suhu_tubuh' => $request->suhu_tubuh != null ? $request->suhu_tubuh : '',
                'tensi' => $request->tensi != null ? $request->tensi : '',
                'nadi' => $request->nadi != null ? $request->nadi : '',
                'respirasi' => $request->respirasi != null ? $request->respirasi : '',
                'tinggi' => $request->tinggi != null ? $request->tinggi : '',
                'berat' => $request->berat != null ? $request->berat : '',
                'spo2' => $request->spo2 != null ? $request->spo2 : '',
                'gcs' => $request->gcs != null ? $request->gcs : '',
                'kesadaran' => $request->kesadaran != null ? $request->kesadaran : '',
                'keluhan' => $request->keluhan != null ? $request->keluhan : '',
                'pemeriksaan' => $request->pemeriksaan != null ? $request->pemeriksaan : '',
                'alergi' => $request->alergi != null ? $request->alergi : '',
                'imun_ke' => $request->imun_ke != null ? $request->imun_ke : '',
                'rtl' => $request->rtl != null ? $request->rtl : '',
                'penilaian' => $request->penilaian != null ? $request->penilaian : '',
                'instruksi' => $request->instruksi != null ? $request->instruksi : '',
                'evaluasi' => $request->evaluasi != null ? $request->evaluasi : '',
                'nip' => $request->petugas != null ? $request->petugas : '',
            ));

        Session::flash('sukses', 'Data berhasil disimpan');
        return redirect()->route('berkasrm.soapie', Crypt::encrypt($request->noRawat));
    }

    public function soapieDelete($id)
    {
        $data_enkripsi = Crypt::decrypt($id);
        $data_pecah = explode('-', $data_enkripsi);
        $noRawat = $data_pecah[0];
        $jam_pelayanan = $data_pecah[1];

        $UpdateDetails = DB::connection('mysqlkhanza')->table('pemeriksaan_ralan')
            ->where('no_rawat', '=', $noRawat)
            ->where('jam_rawat', '=', $jam_pelayanan)
            ->delete();

        Session::flash('sukses', 'Data berhasil didelete');
        return redirect()->route('berkasrm.soapie', Crypt::encrypt($noRawat));
    }
}
