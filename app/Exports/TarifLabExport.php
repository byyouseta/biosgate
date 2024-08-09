<?php

namespace App\Exports;

use App\TarifLab;
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
        return TarifLab::all();
    }

    public function headings(): array
    {
        return [
            "Kode Periksa", "Nama Pemeriksaan", "Jasa RS",
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
            "Kategori"
        ];
    }
}
