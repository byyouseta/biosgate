<?php

namespace App\Exports;

use App\TarifRanap;
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
        return TarifRanap::all();
    }

    public function headings(): array
    {
        return [
            "Kode Tindakan", "Nama Tindakan", "Kategori", "Jasa RS",
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
            "Kelas"
        ];
    }
}
