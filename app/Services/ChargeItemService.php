<?php

namespace App\Services;

use App\ChargeItemSatuSehat;
use App\Http\Controllers\SatuSehatController;
use App\PasienSehat;
use App\ResponseIgdSatuSehat;
use App\ResponseRanapSatuSehat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ChargeItemService
{
    public function sync($no_rawat)
    {
        $result = [
            'administrasi' => 0,
            'tindakan' => 0,
            'lab' => 0,
            'radiologi' => 0,
            'obat' => 0,
            'kamar_inap' => 0,
        ];

        $context = $this->getChargeContext($no_rawat);

        if (!$context) {
            return 0;
        }

        // 1. Tindakan
        $result['tindakan'] = $this->syncTindakan($no_rawat, $context);

        // 2. Laboratorium
        $result['lab'] = $this->syncLab($no_rawat, $context);

        // 3. Radiologi
        $result['radiologi'] = $this->syncRadiologi($no_rawat, $context);

        // 4. Obat
        $result['obat'] = $this->syncObat($no_rawat, $context);

        //5. Administrasi
        $result['administrasi'] = $this->syncAdministrasi($no_rawat, $context);

        if ($context['status_lanjut'] ==  'Ranap') {
            $result['kamar_inap'] = $this->syncKamarInap($no_rawat, $context);
            $result['obat_pulang'] = $this->syncObatPulang($no_rawat, $context);
        }


        return $result;
    }

    private function getChargeContext($no_rawat)
    {
        // cari Patient dan Encounter sekali saja
        $getKtp = DB::connection('mysqlkhanza')
            ->table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->where('no_rawat', $no_rawat)
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.status_lanjut',
                'pasien.no_ktp',
                'poliklinik.nm_poli'
            )
            ->first();

        $noKtp = trim((string) $getKtp->no_ktp);

        if ($noKtp !== '' && !in_array($noKtp, ['-', '0'], true)) {
            $idSehat = SatuSehatController::patientSehat($noKtp);
        } else {
            $idSehat = null;
        }

        if ($getKtp->status_lanjut == 'Ralan') {
            $isIgd = stripos($getKtp->nm_poli, 'igd') !== false;
            if ($isIgd) {
                $dataEncounter = ResponseIgdSatuSehat::where('noRawat', $no_rawat)->first() ?? null;
            } else {
                $dataEncounter = SatuSehatController::getEncounterId($no_rawat) ?? null;
            }
        } else {
            $dataEncounter = ResponseRanapSatuSehat::where('noRawat', $no_rawat)->first() ?? null;
        }

        return [
            'patient_id' => $idSehat,
            'encounter_id' => $dataEncounter ? $dataEncounter->encounter_id : null,
            'status_lanjut' => $getKtp->status_lanjut,
        ];
    }

    public function syncTindakan($no_rawat, $context)
    {
        /*
     * Tentukan jenis rawat berdasarkan data reg_periksa
     */
        $regPeriksa = DB::connection('mysqlkhanza')
            ->table('reg_periksa')
            ->where('no_rawat', $no_rawat)
            ->first();

        if (!$regPeriksa) {
            return [
                'success' => false,
                'message' => 'Data reg_periksa tidak ditemukan.',
            ];
        }

        /*
     * Status rawat:
     *
     * Ralan  = poliklinik
     * Ranap  = rawat inap
     * IGD    = IGD
     *
     * Sesuaikan kode poliklinik IGD dengan database Khanza Anda.
     */
        $jenisRawat = 'ralan';

        if ($regPeriksa->status_lanjut == 'Ranap') {
            $jenisRawat = 'ranap';
        } elseif ($regPeriksa->status_lanjut == 'Ralan') {

            // Sesuaikan jika kode/bagian IGD di Khanza Anda berbeda
            $poliklinik = DB::connection('mysqlkhanza')
                ->table('poliklinik')
                ->where('kd_poli', $regPeriksa->kd_poli)
                ->first();

            if ($poliklinik && stripos($poliklinik->nm_poli, 'IGD') !== false) {
                $jenisRawat = 'igd';
            }
        }

        /*
     * Untuk sementara kita panggil seluruh sumber tindakan.
     */
        $total = 0;

        if ($jenisRawat == 'ralan' || $jenisRawat == 'igd') {

            $total += $this->syncRawatJlDr(
                $no_rawat,
                $context,
                $jenisRawat
            );

            $total += $this->syncRawatJlPr(
                $no_rawat,
                $context,
                $jenisRawat
            );

            $total += $this->syncRawatJlDrPr(
                $no_rawat,
                $context,
                $jenisRawat
            );
        }

        if ($jenisRawat == 'ranap') {

            $total += $this->syncRawatInapDr(
                $no_rawat,
                $context
            );

            $total += $this->syncRawatInapPr(
                $no_rawat,
                $context
            );

            $total += $this->syncRawatInapDrPr(
                $no_rawat,
                $context
            );
        }

        return [
            'success' => true,
            'no_rawat' => $no_rawat,
            'jenis_rawat' => $jenisRawat,
            'total' => $total,
        ];
    }

    private function syncRawatJlDr($no_rawat, $context, $jenisRawat = 'ralan')
    {
        $rows = DB::connection('mysqlkhanza')
            ->table('rawat_jl_dr as r')
            ->leftJoin('jns_perawatan as j', 'j.kd_jenis_prw', '=', 'r.kd_jenis_prw')
            ->leftjoin('jns_perawatan_detail as jd', 'jd.kd_jenis_prw', '=', 'j.kd_jenis_prw')
            ->join('pegawai', 'pegawai.nik', '=', 'r.kd_dokter')
            ->where('r.no_rawat', $no_rawat)
            ->select(
                'r.*',
                'j.nm_perawatan',
                'j.total_byrdr',
                'jd.kptl',
                'pegawai.no_ktp as ktp_dokter'
            )
            ->get();

        $total = 0;

        foreach ($rows as $row) {

            $idPractition = SatuSehatController::practitioner($row->ktp_dokter);
            /*
         * Tarif tindakan.
         *
         * Khanza biasanya menyimpan komponen tarif:
         * tarif_tindakandr
         * material
         * bhp
         * kso
         * menejemen
         *
         * Untuk ChargeItem kita gunakan total biaya
         * tindakan.
         */
            // $harga = (float) (
            //     $row->material +
            //     $row->bhp +
            //     $row->tarif_tindakandr +
            //     $row->kso +
            //     $row->menejemen
            // );
            $harga = (float) $row->total_byrdr;

            /*
         * Identifikasi record lokal.
         *
         * Karena tabel Khanza tidak mempunyai ID,
         * kita gunakan kombinasi:
         *
         * no_rawat
         * source_table
         * kode tindakan
         * tanggal
         * jam
         * nip
         *
         * sebagai pencarian.
         */
            $chargeItem = ChargeItemSatuSehat::firstOrNew([
                'no_rawat' => $row->no_rawat,
                'source_table' => 'rawat_jl_dr',
                'jenis_item' => 'tindakan',
                'kode_item' => $row->kd_jenis_prw,
                'no_transaksi' => null,
            ]);

            $chargeItem->jenis_rawat = $jenisRawat;

            $chargeItem->nama_item = $row->nm_perawatan;

            $chargeItem->jumlah = 1;
            $chargeItem->harga_satuan = $harga;
            $chargeItem->total_harga = $harga;
            $chargeItem->kptl_code = $row->kptl;
            $chargeItem->kptl_system = 'http://terminology.kemkes.go.id/CodeSystem/kptl';
            $chargeItem->performer_id = $idPractition;
            $chargeItem->occurrence_start = Carbon::parse($row->tgl_perawatan . ' ' . $row->jam_rawat)->format('Y-m-d H:i:s');
            $chargeItem->occurrence_end = Carbon::parse($row->tgl_perawatan . ' ' . $row->jam_rawat)->format('Y-m-d H:i:s');

            $chargeItem->patient_id = $context['patient_id'];
            $chargeItem->encounter_id = $context['encounter_id'];
            /*
         * Jangan mengubah status menjadi pending
         * jika sebelumnya sudah berhasil dikirim.
         */
            if (!$chargeItem->exists) {
                $chargeItem->status = 'pending';
            }

            $chargeItem->save();

            $total++;
        }

        return $total;
    }

    private function syncRawatJlPr($no_rawat, $context, $jenisRawat = 'ralan')
    {
        $rows = DB::connection('mysqlkhanza')
            ->table('rawat_jl_pr as r')
            ->leftJoin('jns_perawatan as j', 'j.kd_jenis_prw', '=', 'r.kd_jenis_prw')
            ->leftjoin('jns_perawatan_detail as jd', 'jd.kd_jenis_prw', '=', 'j.kd_jenis_prw')
            ->join('pegawai', 'pegawai.nik', '=', 'r.nip')
            ->where('r.no_rawat', $no_rawat)
            ->select(
                'r.*',
                'j.nm_perawatan',
                'j.total_byrdr',
                'j.total_byrdrpr',
                'j.total_byrpr',
                'jd.kptl',
                'pegawai.no_ktp as ktp_perawat'
            )
            ->get();

        $total = 0;

        foreach ($rows as $row) {
            $idPractition = SatuSehatController::practitioner($row->ktp_perawat);

            $harga = (float) $row->total_byrpr;

            $chargeItem = ChargeItemSatuSehat::firstOrNew([
                'no_rawat' => $row->no_rawat,
                'source_table' => 'rawat_jl_pr',
                'jenis_item' => 'tindakan',
                'kode_item' => $row->kd_jenis_prw,
                'no_transaksi' => null,
            ]);

            $chargeItem->jenis_rawat = $jenisRawat;
            $chargeItem->jenis_item = 'tindakan';

            $chargeItem->nama_item = $row->nm_perawatan;

            $chargeItem->jumlah = 1;
            $chargeItem->harga_satuan = $harga;
            $chargeItem->total_harga = $harga;
            $chargeItem->kptl_code = $row->kptl;
            $chargeItem->kptl_system = 'http://terminology.kemkes.go.id/CodeSystem/kptl';
            $chargeItem->performer_id = $idPractition;
            $chargeItem->occurrence_start = Carbon::parse($row->tgl_perawatan . ' ' . $row->jam_rawat)->format('Y-m-d H:i:s');
            $chargeItem->occurrence_end = Carbon::parse($row->tgl_perawatan . ' ' . $row->jam_rawat)->format('Y-m-d H:i:s');

            $chargeItem->patient_id = $context['patient_id'];
            $chargeItem->encounter_id = $context['encounter_id'];

            if (!$chargeItem->exists) {
                $chargeItem->status = 'pending';
            }

            $chargeItem->save();

            $total++;
        }

        return $total;
    }

    private function syncRawatJlDrPr($no_rawat, $context, $jenisRawat = 'ralan')
    {
        $rows = DB::connection('mysqlkhanza')
            ->table('rawat_jl_drpr as r')
            ->leftJoin('jns_perawatan as j', 'j.kd_jenis_prw', '=', 'r.kd_jenis_prw')
            ->leftjoin('jns_perawatan_detail as jd', 'jd.kd_jenis_prw', '=', 'j.kd_jenis_prw')
            ->join('pegawai', 'pegawai.nik', '=', 'r.kd_dokter')
            ->where('r.no_rawat', $no_rawat)
            ->select(
                'r.*',
                'j.nm_perawatan',
                'j.total_byrdrpr',
                'jd.kptl',
                'pegawai.no_ktp as ktp_dokter'
            )
            ->get();

        $total = 0;

        foreach ($rows as $row) {
            $idPractition = SatuSehatController::practitioner($row->ktp_dokter);

            $harga = (float) $row->total_byrdrpr;

            $chargeItem = ChargeItemSatuSehat::firstOrNew([
                'no_rawat' => $row->no_rawat,
                'source_table' => 'rawat_jl_drpr',
                'jenis_item' => 'tindakan',
                'kode_item' => $row->kd_jenis_prw,
                'no_transaksi' => null,
            ]);

            $chargeItem->jenis_rawat = $jenisRawat;

            $chargeItem->nama_item = $row->nm_perawatan;

            $chargeItem->jumlah = 1;
            $chargeItem->harga_satuan = $harga;
            $chargeItem->total_harga = $harga;
            $chargeItem->kptl_code = $row->kptl;
            $chargeItem->kptl_system = 'http://terminology.kemkes.go.id/CodeSystem/kptl';
            $chargeItem->performer_id = $idPractition;
            $chargeItem->occurrence_start = Carbon::parse($row->tgl_perawatan . ' ' . $row->jam_rawat)->format('Y-m-d H:i:s');
            $chargeItem->occurrence_end = Carbon::parse($row->tgl_perawatan . ' ' . $row->jam_rawat)->format('Y-m-d H:i:s');

            $chargeItem->patient_id = $context['patient_id'];
            $chargeItem->encounter_id = $context['encounter_id'];

            if (!$chargeItem->exists) {
                $chargeItem->status = 'pending';
            }

            $chargeItem->save();

            $total++;
        }

        return $total;
    }

    private function syncRawatInapDr($no_rawat, $context, $jenisRawat = 'ralan')
    {
        $rows = DB::connection('mysqlkhanza')
            ->table('rawat_inap_dr as r')
            ->leftJoin('jns_perawatan_inap as j', 'j.kd_jenis_prw', '=', 'r.kd_jenis_prw')
            ->leftjoin('jns_perawatan_inap_detail as jd', 'jd.kd_jenis_prw', '=', 'j.kd_jenis_prw')
            ->join('pegawai', 'pegawai.nik', '=', 'r.kd_dokter')
            ->where('r.no_rawat', $no_rawat)
            ->select(
                'r.*',
                'j.nm_perawatan',
                'j.total_byrdr',
                'jd.kptl',
                'pegawai.no_ktp as ktp_dokter'
            )
            ->get();

        $total = 0;

        foreach ($rows as $row) {
            $idPractition = SatuSehatController::practitioner($row->ktp_dokter);

            $harga = (float) $row->total_byrdr;

            /*
         *
         * sebagai pencarian.
         */
            $chargeItem = ChargeItemSatuSehat::firstOrNew([
                'no_rawat' => $row->no_rawat,
                'source_table' => 'rawat_inap_dr',
                'jenis_item' => 'tindakan',
                'kode_item' => $row->kd_jenis_prw,
                'no_transaksi' => null,
            ]);

            $chargeItem->jenis_rawat = $jenisRawat;

            $chargeItem->nama_item = $row->nm_perawatan;

            $chargeItem->jumlah = 1;
            $chargeItem->harga_satuan = $harga;
            $chargeItem->total_harga = $harga;
            $chargeItem->kptl_code = $row->kptl;
            $chargeItem->kptl_system = 'http://terminology.kemkes.go.id/CodeSystem/kptl';
            $chargeItem->performer_id = $idPractition;
            $chargeItem->occurrence_start = Carbon::parse($row->tgl_perawatan . ' ' . $row->jam_rawat)->format('Y-m-d H:i:s');
            $chargeItem->occurrence_end = Carbon::parse($row->tgl_perawatan . ' ' . $row->jam_rawat)->format('Y-m-d H:i:s');

            $chargeItem->patient_id = $context['patient_id'];
            $chargeItem->encounter_id = $context['encounter_id'];
            /*
         * Jangan mengubah status menjadi pending
         * jika sebelumnya sudah berhasil dikirim.
         */
            if (!$chargeItem->exists) {
                $chargeItem->status = 'pending';
            }

            $chargeItem->save();

            $total++;
        }

        return $total;
    }

    private function syncRawatInapPr($no_rawat, $context, $jenisRawat = 'ralan')
    {
        $rows = DB::connection('mysqlkhanza')
            ->table('rawat_inap_pr as r')
            ->leftJoin('jns_perawatan_inap as j', 'j.kd_jenis_prw', '=', 'r.kd_jenis_prw')
            ->leftjoin('jns_perawatan_inap_detail as jd', 'jd.kd_jenis_prw', '=', 'j.kd_jenis_prw')
            ->join('pegawai', 'pegawai.nik', '=', 'r.nip')
            ->where('r.no_rawat', $no_rawat)
            ->select(
                'r.*',
                'j.nm_perawatan',
                'j.total_byrpr',
                'jd.kptl',
                'pegawai.no_ktp as ktp_perawat'
            )
            ->get();

        $total = 0;

        foreach ($rows as $row) {
            $idPractition = SatuSehatController::practitioner($row->ktp_perawat);

            $harga = (float) $row->total_byrpr;

            $chargeItem = ChargeItemSatuSehat::firstOrNew([
                'no_rawat' => $row->no_rawat,
                'source_table' => 'rawat_inap_pr',
                'jenis_item' => 'tindakan',
                'kode_item' => $row->kd_jenis_prw,
                'no_transaksi' => null,
            ]);

            $chargeItem->jenis_rawat = $jenisRawat;

            $chargeItem->nama_item = $row->nm_perawatan;

            $chargeItem->jumlah = 1;
            $chargeItem->harga_satuan = $harga;
            $chargeItem->total_harga = $harga;
            $chargeItem->kptl_code = $row->kptl;
            $chargeItem->kptl_system = 'http://terminology.kemkes.go.id/CodeSystem/kptl';
            $chargeItem->performer_id = $idPractition;
            $chargeItem->occurrence_start = Carbon::parse($row->tgl_perawatan . ' ' . $row->jam_rawat)->format('Y-m-d H:i:s');
            $chargeItem->occurrence_end = Carbon::parse($row->tgl_perawatan . ' ' . $row->jam_rawat)->format('Y-m-d H:i:s');

            $chargeItem->patient_id = $context['patient_id'];
            $chargeItem->encounter_id = $context['encounter_id'];

            if (!$chargeItem->exists) {
                $chargeItem->status = 'pending';
            }

            $chargeItem->save();

            $total++;
        }

        return $total;
    }

    private function syncRawatInapDrPr($no_rawat, $context, $jenisRawat = 'ralan')
    {
        $rows = DB::connection('mysqlkhanza')
            ->table('rawat_inap_drpr as r')
            ->leftJoin('jns_perawatan_inap as j', 'j.kd_jenis_prw', '=', 'r.kd_jenis_prw')
            ->leftjoin('jns_perawatan_inap_detail as jd', 'jd.kd_jenis_prw', '=', 'j.kd_jenis_prw')
            ->join('pegawai', 'pegawai.nik', '=', 'r.kd_dokter')
            ->where('r.no_rawat', $no_rawat)
            ->select(
                'r.*',
                'j.nm_perawatan',
                'j.total_byrdrpr',
                'jd.kptl',
                'pegawai.no_ktp as ktp_dokter'
            )
            ->get();

        $total = 0;

        foreach ($rows as $row) {
            $idPractition = SatuSehatController::practitioner($row->ktp_dokter);

            $harga = (float) $row->total_byrdrpr;

            $chargeItem = ChargeItemSatuSehat::firstOrNew([
                'no_rawat' => $row->no_rawat,
                'source_table' => 'rawat_inap_drpr',
                'jenis_item' => 'tindakan',
                'kode_item' => $row->kd_jenis_prw,
                'no_transaksi' => null,
            ]);

            $chargeItem->jenis_rawat = $jenisRawat;

            $chargeItem->nama_item = $row->nm_perawatan;

            $chargeItem->jumlah = 1;
            $chargeItem->harga_satuan = $harga;
            $chargeItem->total_harga = $harga;
            $chargeItem->kptl_code = $row->kptl;
            $chargeItem->kptl_system = 'http://terminology.kemkes.go.id/CodeSystem/kptl';
            $chargeItem->performer_id = $idPractition;
            $chargeItem->occurrence_start = Carbon::parse($row->tgl_perawatan . ' ' . $row->jam_rawat)->format('Y-m-d H:i:s');
            $chargeItem->occurrence_end = Carbon::parse($row->tgl_perawatan . ' ' . $row->jam_rawat)->format('Y-m-d H:i:s');

            $chargeItem->patient_id = $context['patient_id'];
            $chargeItem->encounter_id = $context['encounter_id'];

            if (!$chargeItem->exists) {
                $chargeItem->status = 'pending';
            }

            $chargeItem->save();

            $total++;
        }

        return $total;
    }

    private function syncLab($no_rawat, $context)
    {
        $rows = DB::connection('mysqlkhanza')
            ->table('permintaan_lab as pl')

            ->join(
                'detail_periksa_lab as dpl',
                'dpl.no_rawat',
                '=',
                'pl.no_rawat'
            )
            ->leftJoin(
                'jns_perawatan_lab as jpl',
                'jpl.kd_jenis_prw',
                '=',
                'dpl.kd_jenis_prw'
            )
            ->leftJoin(
                'jns_perawatan_lab_detail as jpld',
                'dpl.kd_jenis_prw',
                '=',
                'jpld.kd_jenis_prw'
            )
            ->leftJoin('periksa_lab as prl', function ($join) {
                $join->on('prl.no_rawat', '=', 'pl.no_rawat')
                    ->on('prl.kd_jenis_prw', '=', 'dpl.kd_jenis_prw')
                    ->on('prl.tgl_periksa', '=', 'dpl.tgl_periksa')
                    ->on('prl.jam', '=', 'dpl.jam');
            })
            ->leftJoin('pegawai', 'pegawai.nik', '=', 'prl.kd_dokter')
            ->where('pl.no_rawat', $no_rawat)
            ->select(
                'pl.noorder',
                'pl.no_rawat',
                'pl.tgl_permintaan',
                'pl.jam_permintaan',
                'dpl.*',
                'jpl.nm_perawatan',
                'jpl.total_byr',
                'jpld.kptl',
                'pegawai.no_ktp as ktp_dokter'
            )
            ->get();

        $total = 0;

        foreach ($rows as $row) {
            $idPractition = SatuSehatController::practitioner($row->ktp_dokter);

            $chargeItem = ChargeItemSatuSehat::firstOrNew([
                'no_rawat'     => $no_rawat,
                'source_table' => 'detail_periksa_lab',
                'jenis_item' => 'lab',
                'no_transaksi'      => $row->noorder,
                'kode_item'    => $row->kd_jenis_prw,
            ]);

            // Sesuaikan dengan nama kolom nama pemeriksaan
            $chargeItem->nama_item = $row->nm_perawatan ?? null;

            $chargeItem->jumlah = 1;

            // Sesuaikan dengan kolom total tarif lab
            $harga = (float) ($row->total_byr ?? 0);

            $chargeItem->harga_satuan = $harga;
            $chargeItem->total_harga = $harga;
            $chargeItem->kptl_code = $row->kptl;
            $chargeItem->kptl_system = 'http://terminology.kemkes.go.id/CodeSystem/kptl';
            $chargeItem->performer_id = $idPractition;
            $chargeItem->occurrence_start = Carbon::parse($row->tgl_permintaan . ' ' . $row->jam_permintaan)->format('Y-m-d H:i:s');
            $chargeItem->occurrence_end = Carbon::parse($row->tgl_periksa . ' ' . $row->jam)->format('Y-m-d H:i:s');

            $chargeItem->patient_id = $context['patient_id'];
            $chargeItem->encounter_id = $context['encounter_id'];

            $chargeItem->status = $chargeItem->exists
                ? $chargeItem->status
                : 'pending';

            $chargeItem->save();

            $total++;
        }

        return $total;
    }

    private function syncRadiologi($no_rawat, $context)
    {
        $rows = DB::connection('mysqlkhanza')
            ->table('permintaan_radiologi as pr')
            ->join(
                'periksa_radiologi as per',
                'per.no_rawat',
                '=',
                'pr.no_rawat'
            )
            ->leftJoin(
                'jns_perawatan_radiologi as perw',
                'perw.kd_jenis_prw',
                '=',
                'per.kd_jenis_prw'
            )
            ->leftJoin(
                'jns_perawatan_radiologi_detail as perw_d',
                'perw_d.kd_jenis_prw',
                '=',
                'per.kd_jenis_prw'
            )
            ->leftJoin('pegawai', 'pegawai.nik', '=', 'per.kd_dokter')
            ->select(
                'pr.noorder',
                'pr.no_rawat',
                'pr.tgl_permintaan',
                'pr.jam_permintaan',
                'per.tgl_periksa',
                'per.jam',
                'per.status',
                'per.kd_jenis_prw',
                'perw.nm_perawatan',
                'per.biaya',
                'pegawai.no_ktp as ktp_dokter',
                'perw_d.kptl'
            )
            ->where('pr.no_rawat', $no_rawat)
            ->get();

        $total = 0;

        foreach ($rows as $row) {
            $idPractition = SatuSehatController::practitioner($row->ktp_dokter);

            $chargeItem = ChargeItemSatuSehat::firstOrNew([
                'no_rawat'     => $no_rawat,
                'source_table' => 'permintaan_radiologi',
                'jenis_item' => 'radiologi',
                'no_transaksi'      => $row->noorder,
                'kode_item'    => $row->kd_jenis_prw,
            ]);

            $chargeItem->nama_item = $row->nm_perawatan ?? null;
            $chargeItem->jumlah = 1;

            $harga = (float) ($row->biaya ?? 0);

            $chargeItem->harga_satuan = $harga;
            $chargeItem->total_harga = $harga;
            $chargeItem->kptl_code = $row->kptl;
            $chargeItem->kptl_system = 'http://terminology.kemkes.go.id/CodeSystem/kptl';
            $chargeItem->performer_id = $idPractition;
            $chargeItem->occurrence_start = Carbon::parse($row->tgl_permintaan . ' ' . $row->jam_permintaan)->format('Y-m-d H:i:s');
            $chargeItem->occurrence_end = Carbon::parse($row->tgl_periksa . ' ' . $row->jam)->format('Y-m-d H:i:s');

            $chargeItem->patient_id = $context['patient_id'];
            $chargeItem->encounter_id = $context['encounter_id'];

            $chargeItem->status = $chargeItem->exists
                ? $chargeItem->status
                : 'pending';

            $chargeItem->save();

            $total++;
        }

        return $total;
    }

    private function syncObatPulang($no_rawat, $context)
    {
        // Query obat berdasarkan no_rawat
        $rows = DB::connection('mysqlkhanza')
            ->table('resep_pulang as rp')
            ->leftJoin(
                'databarang as db',
                'db.kode_brng',
                '=',
                'rp.kode_brng'
            )
            ->leftJoin(
                'fhir_farmasi as ff',
                'ff.kode_brng',
                '=',
                'db.kode_brng'
            )
            ->where('rp.no_rawat', $no_rawat)
            ->select(
                'rp.no_rawat',
                'rp.tanggal',
                'rp.jam',
                'rp.harga',
                'rp.jml_barang',
                'rp.total',
                'rp.kode_brng',
                'db.nama_brng',
                'ff.id_ihs as kode_kfa'
            )
            ->orderBy('rp.tanggal')
            ->orderBy('rp.jam')
            ->get();

        $total = 0;

        foreach ($rows as $row) {
            $noTransaksi = Carbon::parse(
                $row->tanggal . ' ' . $row->jam
            )->format('YmdHis');

            $apoteker = "10007445367";

            $chargeItem = ChargeItemSatuSehat::firstOrNew([
                'no_rawat'     => $no_rawat,
                'source_table' => 'resep_pulang',
                'jenis_item' => 'obat',
                'no_transaksi' => $noTransaksi,
                'kode_item'    => $row->kode_brng,
            ]);

            $chargeItem->nama_item = $row->nama_brng ?? null;
            $chargeItem->jenis_rawat = 'ranap';
            $jumlah = (float) ($row->jml_barang ?? 1);
            $harga = (float) ($row->total ?? 0);

            $chargeItem->jumlah = $jumlah;
            $chargeItem->total_harga = $harga;
            $chargeItem->kptl_code = $row->kode_kfa;
            $chargeItem->kptl_system = 'http://sys-ids.kemkes.go.id/kfa';
            $chargeItem->performer_id = $apoteker;
            $chargeItem->occurrence_start = Carbon::parse($row->tanggal . ' ' . $row->jam)->format('Y-m-d H:i:s');
            $chargeItem->occurrence_end = Carbon::parse($row->tanggal . ' ' . $row->jam)->format('Y-m-d H:i:s');

            $chargeItem->patient_id = $context['patient_id'];
            $chargeItem->encounter_id = $context['encounter_id'];

            if ($jumlah > 0) {
                $chargeItem->harga_satuan = $harga / $jumlah;
            } else {
                $chargeItem->harga_satuan = 0;
            }

            $chargeItem->status = $chargeItem->exists
                ? $chargeItem->status
                : 'pending';

            $chargeItem->save();

            $total++;
        }

        return $total;
    }

    private function syncObat($no_rawat, $context)
    {
        /*
     * 1. Ambil seluruh obat yang benar-benar diberikan
     */
        $rows = DB::connection('mysqlkhanza')
            ->table('detail_pemberian_obat as dpo')
            ->leftJoin(
                'databarang as db',
                'db.kode_brng',
                '=',
                'dpo.kode_brng'
            )
            ->leftJoin('fhir_farmasi as ff', 'ff.kode_brng', '=', 'db.kode_brng')
            ->where('dpo.no_rawat', $no_rawat)
            ->select(
                'dpo.no_rawat',
                'dpo.tgl_perawatan',
                'dpo.jam',
                'dpo.status',
                'dpo.jml',
                'dpo.total',
                'dpo.kode_brng',
                'db.nama_brng',
                'ff.id_ihs as kode_kfa'
            )
            ->orderBy('dpo.tgl_perawatan')
            ->orderBy('dpo.jam')
            ->get();


        /*
     * 2. Ambil seluruh retur obat untuk kunjungan tersebut
     */
        $retur = DB::connection('mysqlkhanza')
            ->table('detreturjual')
            ->where('no_retur_jual', 'like', $no_rawat . '%')
            ->select(
                'no_retur_jual',
                'kode_brng',
                'jml_retur'
            )
            ->get()
            ->groupBy('kode_brng');


        $total = 0;


        /*
     * 3. Proses obat yang diberikan
     */
        foreach ($rows as $row) {
            $apoteker = "10007445367";
            /*
         * Jumlah obat yang diberikan
         */
            $jumlahDiberikan = (float) ($row->jml ?? 0);


            /*
         * Jumlah yang diretur
         */
            $jumlahRetur = $retur
                ->get($row->kode_brng, collect())
                ->sum(function ($item) {
                    return (float) ($item->jml_retur ?? 0);
                });


            /*
         * Jumlah obat yang benar-benar dibebankan
         */
            $jumlahNetto = $jumlahDiberikan - $jumlahRetur;


            /*
         * Kalau seluruh obat diretur,
         * tidak perlu membuat ChargeItem.
         */
            if ($jumlahNetto <= 0) {
                continue;
            }


            /*
         * Harga total pemberian.
         */
            $totalHarga = (float) ($row->total ?? 0);

            /*
         * Harga satuan.
         */
            $hargaSatuan = $jumlahDiberikan > 0
                ? $totalHarga / $jumlahDiberikan
                : 0;


            /*
         * Karena retur mengurangi jumlah,
         * hitung ulang total berdasarkan jumlah netto.
         */
            $totalHargaNetto = $hargaSatuan * $jumlahNetto;


            /*
         * Natural key pemberian obat.
         */
            $noTransaksi = Carbon::parse(
                $row->tgl_perawatan . ' ' . $row->jam
            )->format('YmdHis');


            $chargeItem = ChargeItemSatuSehat::firstOrNew([
                'no_rawat'     => $row->no_rawat,
                'source_table' => 'detail_pemberian_obat',
                'jenis_item'   => 'obat',
                'no_transaksi' => $noTransaksi,
                'kode_item'    => $row->kode_brng,
            ]);


            $chargeItem->nama_item = $row->nama_brng ?? null;

            $chargeItem->jenis_rawat =
                $row->status == 'Ranap'
                ? 'ranap'
                : 'ralan';

            $chargeItem->jumlah = $jumlahNetto;

            $chargeItem->harga_satuan = $hargaSatuan;

            $chargeItem->total_harga = $totalHargaNetto;

            $chargeItem->kptl_code = $row->kode_kfa;
            $chargeItem->kptl_system = 'http://sys-ids.kemkes.go.id/kfa';
            $chargeItem->performer_id = $apoteker;
            $chargeItem->occurrence_start = Carbon::parse($row->tgl_perawatan . ' ' . $row->jam)->format('Y-m-d H:i:s');
            $chargeItem->occurrence_end = Carbon::parse($row->tgl_perawatan . ' ' . $row->jam)->format('Y-m-d H:i:s');

            $chargeItem->patient_id = $context['patient_id'];
            $chargeItem->encounter_id = $context['encounter_id'];

            if (!$chargeItem->exists) {
                $chargeItem->status = 'pending';
            }


            $chargeItem->save();

            $total++;
        }


        return $total;
    }

    public function syncAdministrasi($no_rawat, $context)
    {
        $row = DB::connection('mysqlkhanza')
            ->table('reg_periksa')
            ->where('no_rawat', $no_rawat)
            ->first();

        if (!$row) {
            return 0;
        }

        $petugas = '10013166591'; //Mas Amar

        $harga = (float) $row->biaya_reg;

        if ($harga <= 0) {
            return 0;
        }

        $chargeItem = ChargeItemSatuSehat::firstOrNew([
            'no_rawat' => $row->no_rawat,
            'source_table' => 'reg_periksa',
            'kode_item' => 'BIAYA_REG',
            'jenis_item' => 'administrasi',

            'no_transaksi' => null,
        ]);

        $chargeItem->jenis_rawat = $row->status_lanjut;
        $chargeItem->nama_item = 'Biaya Pendaftaran';
        $chargeItem->jumlah = 1;
        $chargeItem->harga_satuan = $harga;
        $chargeItem->total_harga = $harga;

        if ($row->stts_daftar == 'Baru') {
            $chargeItem->kptl_code = '15229.PS001';
            $chargeItem->kptl_system = 'http://terminology.kemkes.go.id/CodeSystem/kptl';
        } else if ($row->stts_daftar == 'Lama') {
            $chargeItem->kptl_code = '15229.PS002';
            $chargeItem->kptl_system = 'http://terminology.kemkes.go.id/CodeSystem/kptl';
        }

        $chargeItem->performer_id = $petugas;
        $chargeItem->occurrence_start = Carbon::parse($row->tgl_registrasi . ' ' . $row->jam_reg)->format('Y-m-d H:i:s');
        $chargeItem->occurrence_end = Carbon::parse($row->tgl_registrasi . ' ' . $row->jam_reg)->format('Y-m-d H:i:s');

        $chargeItem->patient_id = $context['patient_id'];
        $chargeItem->encounter_id = $context['encounter_id'];

        if (!$chargeItem->exists) {
            $chargeItem->status = 'pending';
        }

        $chargeItem->save();

        return 1;
    }

    private function syncKamarInap($no_rawat, $context)
    {
        $rows = DB::connection('mysqlkhanza')
            ->table('kamar_inap')
            ->join('kamar', 'kamar.kd_kamar', '=', 'kamar_inap,kd_kamar')
            ->where('no_rawat', $no_rawat)
            ->select(
                'kamar_inap.kd_kamar',
                'kamar_inap.trf_kamar',
                'kamar_inap.tgl_masuk',
                'kamar_inap.jam_masuk',
                'kamar_inap.tgl_keluar',
                'kamar_inap.jam_keluar',
                'kamar_inap.lama',
                'kamar_inap.ttl_biaya',
                'kamar.kode_kptl'
            )
            ->get();

        $total = 0;

        foreach ($rows as $row) {

            /*
         * Satu episode kamar diidentifikasi berdasarkan
         * no_rawat + tanggal/jam masuk + kode kamar
         */
            $noTransaksi = Carbon::parse(
                $row->tgl_masuk . ' ' . $row->jam_masuk
            )->format('YmdHis');

            $chargeItem = ChargeItemSatuSehat::firstOrNew([
                'no_rawat'     => $row->no_rawat,
                'source_table' => 'kamar_inap',
                'jenis_item'   => 'akomodasi',
                'no_transaksi' => $noTransaksi,
                'kode_item'    => $row->kd_kamar,
            ]);

            $chargeItem->nama_item = 'Biaya Kamar ' . $row->kd_kamar;
            $chargeItem->jenis_rawat = 'ranap';

            $jumlah = (float) ($row->lama ?? 0);
            $harga = (float) ($row->trf_kamar ?? 0);

            $chargeItem->jumlah = $jumlah;
            $chargeItem->harga_satuan = $harga;
            $chargeItem->total_harga = $harga * $jumlah;

            $chargeItem->kptl_code = $row->kode_kptl;
            $chargeItem->kptl_system = 'http://terminology.kemkes.go.id/CodeSystem/kptl';
            // $chargeItem->performer_id = $apoteker;
            $chargeItem->occurrence_start = Carbon::parse($row->tgl_masuk . ' ' . $row->jam_masuk)->format('Y-m-d H:i:s');
            $chargeItem->occurrence_end = Carbon::parse($row->tgl_keluar . ' ' . $row->jam_keluar)->format('Y-m-d H:i:s');

            $chargeItem->patient_id = $context['patient_id'];
            $chargeItem->encounter_id = $context['encounter_id'];

            if (!$chargeItem->exists) {
                $chargeItem->status = 'pending';
            }

            $chargeItem->save();

            $total++;
        }

        return $total;
    }
}
