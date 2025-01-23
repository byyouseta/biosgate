<?php

namespace App\Exports;

use App\TarifOperasi;
use Illuminate\Support\Facades\DB;
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
        // return TarifOperasi::all();
        return DB::connection('mysqlkhanzadummy')->table('paket_operasi')
            ->join('penjab', 'penjab.kd_pj', '=', 'paket_operasi.kd_pj')
            ->leftJoin('paket_operasi_detail', 'paket_operasi_detail.kode_paket', '=', 'paket_operasi.kode_paket')
            ->select(
                'paket_operasi.kode_paket as Kode Paket',
                'paket_operasi.nm_perawatan as Nama Operasi',
                'paket_operasi.kategori as Kategori',
                'paket_operasi.operator1 as Operator 1',
                'paket_operasi.operator2 as Operator 2',
                'paket_operasi.operator3 as Operator 3',
                'paket_operasi.asisten_operator1 as Asisten Op 1',
                'paket_operasi.asisten_operator2 as Asisten Op 2',
                'paket_operasi.asisten_operator3 as Asisten Op 3',
                'paket_operasi.instrumen as Instrumen',
                'paket_operasi.dokter_anak as dr Anak',
                'paket_operasi.perawaat_resusitas as Perawat Resus',
                'paket_operasi.dokter_anestesi as dr Anestesi',
                'paket_operasi.asisten_anestesi as Asisten Anes 1',
                'paket_operasi.asisten_anestesi2 as Asisten Anes 2',
                'paket_operasi.bidan  as Bidan 1',
                'paket_operasi.bidan2 as Bidan 2',
                'paket_operasi.bidan3 as Bidan 3',
                'paket_operasi.perawat_luar as Perawat Luar',
                'paket_operasi.sewa_ok as Sewa OK/VK',
                'paket_operasi.alat as Alat',
                'paket_operasi.akomodasi as Akomodasi',
                'paket_operasi.bagian_rs as N.M.S',
                'paket_operasi.omloop as Onloop 1',
                'paket_operasi.omloop2 as Onloop 2',
                'paket_operasi.omloop3 as Onloop 3',
                'paket_operasi.omloop4 as Onloop 4',
                'paket_operasi.omloop5 as Onloop 5',
                'paket_operasi.sarpras as Sarpras',
                'paket_operasi.dokter_pjanak as dr PJ Anak',
                'paket_operasi.dokter_umum as dr Umum',
                'paket_operasi.kd_pj as Kode PJ',
                'paket_operasi.status as Status',
                'paket_operasi.kelas as Kelas',
                'paket_operasi_detail.kptl as Kptl',
                'paket_operasi_detail.kptl as Keterangan'
            )
            ->get();
    }

    public function headings(): array
    {
        return [
            "Kode Paket",
            "Nama Operasi",
            "Kategori",
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
            'Kelas',
            'KPTL',
            'Keterangan'
        ];
    }
}
