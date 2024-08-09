<?php

namespace App\Imports;

use App\TarifRadiologi;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TarifRadioImport implements ToModel, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function model(array $row)
    {
        return new TarifRadiologi([
            'kd_jenis_prw' => $row['kode_periksa'],
            'nm_perawatan' => $row['nama_pemeriksaan'],
            'bagian_rs' => $row['jasa_rs'],
            'bhp' => $row['paket_bhp'],
            'tarif_perujuk' => $row['jm_perujuk'],
            'tarif_tindakan_dokter' => $row['jm_dokter'],
            'tarif_tindakan_petugas' => $row['jm_petugas'],
            'kso' => $row['kso'],
            'menejemen' => $row['menejemen'],
            'total_byr' => $row['ttl_tarif'],
            'kd_pj' => $row['jenis_bayar'],
            'status' => $row['status_aktif'],
            'kelas' => $row['kelas'],
            'kategori' => $row['kategori']
        ]);
    }
}
