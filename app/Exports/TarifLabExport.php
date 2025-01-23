<?php

namespace App\Exports;

use App\TarifLab;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class TarifLabExport implements FromCollection, WithHeadings, WithStrictNullComparison
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        //
        // return TarifLab::all();
        // Lakukan join dengan tabel jns_perawatan_lab_detail
        return DB::connection('mysqlkhanzadummy')->table('jns_perawatan_lab')
            ->leftJoin('jns_perawatan_lab_detail', 'jns_perawatan_lab.kd_jenis_prw', '=', 'jns_perawatan_lab_detail.kd_jenis_prw')
            ->select(
                'jns_perawatan_lab.kd_jenis_prw as Kode Periksa',
                'jns_perawatan_lab.nm_perawatan as Nama Pemeriksaan',
                'jns_perawatan_lab.bagian_rs as Jasa RS',
                'jns_perawatan_lab.bhp as Paket BHP',
                'jns_perawatan_lab.tarif_perujuk as J.M Perujuk',
                'jns_perawatan_lab.tarif_tindakan_dokter as J.M Dokter',
                'jns_perawatan_lab.tarif_tindakan_petugas as J.M Petugas',
                'jns_perawatan_lab.kso as KSO',
                'jns_perawatan_lab.menejemen as Menejemen',
                'jns_perawatan_lab.total_byr as Ttl Tarif',
                'jns_perawatan_lab.kd_pj as Jenis Bayar',
                'jns_perawatan_lab.status as Status Aktif',
                'jns_perawatan_lab.kelas as Kelas',
                'jns_perawatan_lab.kategori as Kategori',
                'jns_perawatan_lab_detail.kptl as KPTL', // Tambahkan kolom kptl
                'jns_perawatan_lab_detail.keterangan as Keterangan'
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
            "Kategori",
            "KPTL",
            "Keterangan"
        ];
    }
}
