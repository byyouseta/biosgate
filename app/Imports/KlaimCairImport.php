<?php

namespace App\Imports;

use App\KlaimCair;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KlaimCairImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new KlaimCair([
            'no_sep' => $row['no_sep'],
            'tgl_verif' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tgl_verif']),
            'riil' => $row['riil'],
            'diajukan' => $row['diajukan'],
            'disetujui' => $row['disetujui'],
            'jenis_rawat' => $row['jenis_rawat']
        ]);
    }
}
