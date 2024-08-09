<?php

namespace App\Imports;

use App\TarifOperasi;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TarifOperasiImport implements ToModel, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function model(array $row)
    {
        return new TarifOperasi([
            'kode_paket' => $row['kode_paket'],
            'nm_perawatan' => $row['nm_perawatan'],
            'kategori' => $row['kategori'],
            'operator1' => $row['operator1'],
            'operator2' => $row['operator2'],
            'operator3' => $row['operator3'],
            'asisten_operator1' => $row['asisten_operator1'],
            'asisten_operator2' => $row['asisten_operator2'],
            'asisten_operator3' => $row['asisten_operator3'],
            'instrumen' => $row['instrumen'],
            'dokter_anak' => $row['dokter_anak'],
            'perawaat_resusitas' => $row['perawaat_resusitas'],
            'dokter_anestesi' => $row['dokter_anestesi'],
            'asisten_anestesi' => $row['asisten_anestesi'],
            'asisten_anestesi2' => $row['asisten_anestesi2'],
            'bidan' => $row['bidan'],
            'bidan2' => $row['bidan2'],
            'bidan3' => $row['bidan3'],
            'perawat_luar' => $row['perawat_luar'],
            'sewa_ok' => $row['sewa_ok'],
            'alat' => $row['alat'],
            'akomodasi' => $row['akomodasi'],
            'bagian_rs' => $row['bagian_rs'],
            'omloop' => $row['omloop'],
            'omloop2' => $row['omloop2'],
            'omloop3' => $row['omloop3'],
            'omloop4' => $row['omloop4'],
            'omloop5' => $row['omloop4'],
            'sarpras' => $row['sarpras'],
            'dokter_pjanak' => $row['dokter_pjanak'],
            'dokter_umum' => $row['dokter_umum'],
            'status' => $row['status'],
            'kd_pj' => $row['kd_pj'],
            'kelas' => $row['kelas']
        ]);
    }
}
