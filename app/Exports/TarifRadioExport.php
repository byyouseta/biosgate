<?php

namespace App\Exports;

use App\TarifRadiologi;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class TarifRadioExport implements FromCollection, WithHeadings, WithStrictNullComparison
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        //
        // return TarifRadiologi::all();
        return DB::connection('mysqlkhanzadummy')->table('jns_perawatan_radiologi')
            ->leftJoin('jns_perawatan_radiologi_detail', 'jns_perawatan_radiologi.kd_jenis_prw', '=', 'jns_perawatan_radiologi_detail.kd_jenis_prw')
            ->select(
                'jns_perawatan_radiologi.kd_jenis_prw as Kode Periksa',
                'jns_perawatan_radiologi.nm_perawatan as Nama Pemeriksaan',
                'jns_perawatan_radiologi.bagian_rs as Jasa RS',
                'jns_perawatan_radiologi.bhp as Paket BHP',
                'jns_perawatan_radiologi.tarif_perujuk as J.M Perujuk',
                'jns_perawatan_radiologi.tarif_tindakan_dokter as J.M Dokter',
                'jns_perawatan_radiologi.tarif_tindakan_petugas as J.M Petugas',
                'jns_perawatan_radiologi.kso as KSO',
                'jns_perawatan_radiologi.menejemen as Menejemen',
                'jns_perawatan_radiologi.total_byr as Ttl Tarif',
                'jns_perawatan_radiologi.kd_pj as Jenis Bayar',
                'jns_perawatan_radiologi.status as Status Aktif',
                'jns_perawatan_radiologi.kelas as Kelas',
                'jns_perawatan_radiologi_detail.kptl as KPTL', // Tambahkan kolom kptl
                'jns_perawatan_radiologi_detail.keterangan as Keterangan'
            )
            ->get();
    }

    public function headings(): array
    {
        return [
            "Kode Periksa",
            "Nama Pemeriksaan",
            "Jasa RS",
            "Paket BHP",
            "J.M Perujuk",
            "J.M Dokter",
            "J.M Petugas",
            "KSO",
            "Menejemen",
            "Ttl Tarif",
            "Jenis Bayar",
            "Status Aktif",
            "Kelas",
            "KPTL",
            "Keterangan"
        ];
    }
}
