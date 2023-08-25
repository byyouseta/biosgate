<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Vedika extends Model
{
    public static function cekBilling($id)
    {
        $cek = DB::connection('mysqlkhanza')->table('billing')
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
            ->count();

        return $cek;
    }

    public static function cekLab($id)
    {
        $cek = DB::connection('mysqlkhanza')->table('detail_periksa_lab')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'detail_periksa_lab.no_rawat')
            ->leftJoin('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('permintaan_lab', 'permintaan_lab.no_rawat', '=', 'detail_periksa_lab.no_rawat')
            ->join('periksa_lab', 'periksa_lab.no_rawat', '=', 'detail_periksa_lab.no_rawat')
            ->select(
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.jk',
                'reg_periksa.almt_pj',
                'reg_periksa.umurdaftar',
                'reg_periksa.sttsumur',
                'reg_periksa.no_rawat',
                'reg_periksa.kd_poli',
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
            ->count();

        return $cek;
    }

    public static function cekRad($id)
    {
        $cek = DB::connection('mysqlkhanza')->table('permintaan_radiologi')
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
            // ->where('reg_periksa.kd_pj', '=', 'BPJ')
            ->where('reg_periksa.no_rawat', '=', $id)
            ->count();

        return $cek;
    }

    public static function cekObat($id)
    {
        $cek = DB::connection('mysqlkhanza')->table('detail_pemberian_obat')
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
            // ->where('detail_pemberian_obat.status', '=', 'Ralan')
            ->count();

        return $cek;
    }

    public static function cekResume($id)
    {
        $cek = DB::connection('mysqlkhanza')->table('resume_pasien')
            ->select(
                'resume_pasien.no_rawat',
                'resume_pasien.kondisi_pulang',
                'resume_pasien.obat_pulang',
                'resume_pasien.tindak_lanjut',
                'resume_pasien.edukasi',
                'resume_pasien.tgl_selesai'
            )
            ->where('resume_pasien.no_rawat', '=', $id)
            ->count();

        return $cek;
    }

    public static function getPetugas($idd, $idt)
    {
        $cari = DB::connection('mysqlkhanza')->table('periksa_lab')
            ->select(
                'periksa_lab.nip',
                'periksa_lab.dokter_perujuk',
                'periksa_lab.kd_dokter'
            )
            ->where('periksa_lab.tgl_periksa', '=', $idd)
            ->where('periksa_lab.jam', '=', $idt)
            ->first();

        $data = DB::connection('mysqlkhanza')->table('petugas')
            ->select(
                'petugas.nip',
                'petugas.nama'
            )
            ->where('petugas.nip', '=', $cari->nip)
            ->first();

        return $data;
    }

    public static function getDokter($idd, $idt)
    {
        $cari = DB::connection('mysqlkhanza')->table('periksa_lab')
            ->select(
                'periksa_lab.nip',
                'periksa_lab.dokter_perujuk',
                'periksa_lab.kd_dokter'
            )
            ->where('periksa_lab.tgl_periksa', '=', $idd)
            ->where('periksa_lab.jam', '=', $idt)
            ->first();

        $data = DB::connection('mysqlkhanza')->table('dokter')
            ->select(
                'dokter.kd_dokter',
                'dokter.nm_dokter'
            )
            ->where('dokter.kd_dokter', '=', $cari->kd_dokter)
            ->first();

        return $data;
    }

    public static function getSep($norawat)
    {
        $cari = DB::connection('mysqlkhanza')->table('bridging_sep')
            ->select(
                'bridging_sep.no_sep',
                'bridging_sep.no_rawat'
            )
            ->where('bridging_sep.no_rawat', '=', $norawat)
            ->first();

        if (empty($cari)) {
            $cari = sepManual::where('noRawat', $norawat)
                ->first();
        }

        return $cari;
    }

    public static function getTtd($norawat)
    {
        $cari = sepManual::where('noRawat', $norawat)
            ->first();

        // dd($cari);

        return $cari;
    }

    public static function getDiagnosa($norawat, $status)
    {
        $cari = DB::connection('mysqlkhanza')->table('diagnosa_pasien')
            ->join('penyakit', 'penyakit.kd_penyakit', '=', 'diagnosa_pasien.kd_penyakit')
            ->select(
                'diagnosa_pasien.kd_penyakit',
                'diagnosa_pasien.status',
                'diagnosa_pasien.no_rawat',
                'penyakit.nm_penyakit'
            )
            ->where('diagnosa_pasien.no_rawat', '=', $norawat)
            ->where('diagnosa_pasien.status', '=', $status)
            ->first();

        return $cari;
    }

    public static function aturanObatJadi($norawat, $kdObat)
    {
        $cek = DB::connection('mysqlkhanza')->table('aturan_pakai')
            ->select(
                'aturan_pakai.no_rawat',
                'aturan_pakai.kode_brng',
                'aturan_pakai.aturan'
            )
            ->where('aturan_pakai.no_rawat', $norawat)
            ->where('kode_brng', $kdObat)
            ->first();

        if ($cek != null) {
            return $cek;
        } else {
            return null;
        }
    }

    public static function getRacikan($norawat, $noracik)
    {
        $cek = DB::connection('mysqlkhanza')->table('detail_obat_racikan')
            ->join('databarang', 'databarang.kode_brng', '=', 'detail_obat_racikan.kode_brng')
            ->select(
                'detail_obat_racikan.no_rawat',
                'detail_obat_racikan.no_racik',
                'detail_obat_racikan.kode_brng',
                'databarang.nama_brng'
            )
            ->where('detail_obat_racikan.no_rawat', $norawat)
            ->where('detail_obat_racikan.no_racik', $noracik)
            ->orderBy('detail_obat_racikan.kode_brng', 'DESC')
            ->get();

        // dd($cek);

        return $cek;
    }

    public static function getJmlRacikan($norawat, $kdObat)
    {
        $cek = DB::connection('mysqlkhanza')->table('detail_pemberian_obat')
            ->select(
                'detail_pemberian_obat.no_rawat',
                'detail_pemberian_obat.kode_brng',
                'detail_pemberian_obat.jml'
            )
            ->where('detail_pemberian_obat.no_rawat', $norawat)
            ->where('detail_pemberian_obat.kode_brng', $kdObat)
            ->first();

        if (empty($cek)) {
            $cek = [
                'no_rawat' => $norawat,
                'kdObat' => $kdObat,
                'jml' => null
            ];

            $cek = (object) $cek;
        }

        return $cek;
    }
}
