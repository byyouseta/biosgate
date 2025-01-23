<?php

namespace App\Exports;

use App\TarifRanap;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class TarifRanapExport implements FromCollection, WithHeadings, WithStrictNullComparison
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // return TarifRanap::all();

        return DB::connection('mysqlkhanzadummy')->table('jns_perawatan_inap')
            ->leftJoin('jns_perawatan_inap_detail', 'jns_perawatan_inap_detail.kd_jenis_prw', '=', 'jns_perawatan_inap.kd_jenis_prw')
            ->select(
                'jns_perawatan_inap.kd_jenis_prw as Kode Tindakan',
                'jns_perawatan_inap.nm_perawatan as Nama Tindakan',
                'jns_perawatan_inap.kd_kategori as Kategori',
                'jns_perawatan_inap.material as Jasa RS',
                'jns_perawatan_inap.bhp as BHP/Paket Obat',
                'jns_perawatan_inap.tarif_tindakandr as Js Medis Dr',
                'jns_perawatan_inap.tarif_tindakanpr as Js Medis Pr',
                'jns_perawatan_inap.kso as KSO',
                'jns_perawatan_inap.menejemen as Menejemen',
                'jns_perawatan_inap.total_byrdr as Ttl Biaya Dr',
                'jns_perawatan_inap.total_byrpr as Ttl Biaya Pr',
                'jns_perawatan_inap.total_byrdrpr as Ttl Biaya Dr & Pr',
                'jns_perawatan_inap.kd_pj as Jenis Bayar',
                'jns_perawatan_inap.kd_bangsal as Kamar',
                'jns_perawatan_inap.status as Status Aktif',
                'jns_perawatan_inap.kelas as Kelas',
                'jns_perawatan_inap_detail.kptl as KPTL'
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
            "Kamar",
            "Status Aktif",
            "Kelas",
            "KPTL"
        ];
    }
}
