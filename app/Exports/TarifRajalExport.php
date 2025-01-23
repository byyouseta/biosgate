<?php

namespace App\Exports;

use App\TarifRajal;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class TarifRajalExport implements FromCollection, WithHeadings, WithStrictNullComparison
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // return TarifRajal::all();
        return DB::connection('mysqlkhanzadummy')->table('jns_perawatan')
            ->leftJoin('jns_perawatan_detail', 'jns_perawatan_detail.kd_jenis_prw', '=', 'jns_perawatan.kd_jenis_prw')
            ->select(
                'jns_perawatan.kd_jenis_prw as Kode Tindakan',
                'jns_perawatan.nm_perawatan as Nama Tindakan',
                'jns_perawatan.kd_kategori as Kategori',
                'jns_perawatan.material as Jasa RS',
                'jns_perawatan.bhp as BHP/Paket Obat',
                'jns_perawatan.tarif_tindakandr as Js Medis Dr',
                'jns_perawatan.tarif_tindakanpr as Js Medis Pr',
                'jns_perawatan.kso as KSO',
                'jns_perawatan.menejemen as Menejemen',
                'jns_perawatan.total_byrdr as Ttl Biaya Dr',
                'jns_perawatan.total_byrpr as Ttl Biaya Pr',
                'jns_perawatan.total_byrdrpr as Ttl Biaya Dr & Pr',
                'jns_perawatan.kd_pj as Jenis Bayar',
                'jns_perawatan.kd_poli as Unit/Poli',
                'jns_perawatan.status as Status Aktif',
                'jns_perawatan_detail.kptl as KPTL',
                'jns_perawatan_detail.keterangan as Keterangan'
            )
            ->get();
    }

    public function headings(): array
    {
        return [
            "Kode Tindakan",
            "Nama Tindakan",
            "Kategori",
            "Jasa RS",
            "BHP/Paket Obat",
            "Js Medis Dr",
            "Js Medis Pr",
            "KSO",
            "Menejemen",
            "Ttl Biaya Dr",
            "Ttl Biaya Pr",
            "Ttl Biaya Dr & Pr",
            "Jenis Bayar",
            "Unit/Poli",
            "Status Aktif",
            "KPTL",
            "Keterangan"
        ];
    }
}
