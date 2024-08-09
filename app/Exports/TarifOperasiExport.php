<?php

namespace App\Exports;

use App\TarifOperasi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class TarifOperasiExport implements FromCollection, WithHeadings, WithStrictNullComparison
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return TarifOperasi::all();
    }

    public function headings(): array
    {
        return [
            "Kode Paket", "Nama Operasi", "Kategori",
            'Operator 1',
            'Operator 2',
            'Operator 3',
            'Asisten Op 1',
            'Asisten Op 2',
            'Asisten Op 3',
            'Instrumen',
            'dr Anestesi',
            'Asisten Anes 1',
            'Asisten Anes 2',
            'dr Anak',
            'Perawat Resus',
            'Bidan 1',
            'Bidan 2',
            'Bidan 3',
            'Perawat Luar',
            'Alat',
            'Sewa OK/VK',
            'Akomodasi',
            'N.M.S',
            'Onloop 1',
            'Onloop 2',
            'Onloop 3',
            'Onloop 4',
            'Onloop 5',
            'Sarpras',
            'dr PJ Anak',
            'dr Umum',
            'Kode PJ',
            'Status',
            'Kelas'
        ];
    }
}
