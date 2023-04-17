<?php

namespace App\Imports;

use App\Pengeluaran;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PengeluaranImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $left = substr($row['kd_akun'], 0, 1);
        // dd($left);
        if ($left == '5') {
            return new Pengeluaran([
                'tgl_transaksi' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tgl_transaksi']),
                'kd_akun'     => $row['kd_akun'],
                'jumlah'    => $row['jumlah'],
                'status'    => 0,
            ]);
        } else {
            return false;
        }
    }
}
