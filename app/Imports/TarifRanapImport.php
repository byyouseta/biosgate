<?php

namespace App\Imports;

use App\TarifRanap;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TarifRanapImport implements ToModel, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function model(array $row)
    {
        return new TarifRanap([
            'kd_jenis_prw' => $row['kd_jenis_prw'],
            'nm_perawatan' => $row['nm_perawatan'],
            'kd_kategori' => $row['kd_kategori'],
            'material' => $row['material'],
            'bhp' => $row['bhp'],
            'tarif_tindakandr' => $row['tarif_tindakandr'],
            'tarif_tindakanpr' => $row['tarif_tindakanpr'],
            'kso' => $row['kso'],
            'menejemen' => $row['menejemen'],
            'total_byrdr' => $row['total_byrdr'],
            'total_byrpr' => $row['total_byrpr'],
            'total_byrdrpr' => $row['total_byrdrpr'],
            'kd_pj' => $row['kd_pj'],
            'kd_poli' => $row['kd_bangsal'],
            'status' => $row['status'],
            'kelas' => $row['kelas']
        ]);
    }
}
