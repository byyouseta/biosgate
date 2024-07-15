<?php

namespace App\Exports;

use App\Pengaduan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PengaduanExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Pengaduan::all();
    }

    public function headings(): array
    {
        return [
            'nama',
            'no_hp',
            'email',
            'penerima',
            'punya_rm',
            'no_rm',
            'nama_pasien', 'lahir_pasien', 'waktu_kejadian', 'tempat_kejadian', 'pembiayaan', 'pendaftaran_online',
            'pendaftaran_rajal', 'pendaftaran_ranap', 'pendaftaran_igd', 'admin_bpjs', 'petugas_dr_sp', 'petugas_dr_umum', 'petugas_dr_gigi', 'petugas_perawat',
            'petugas_bidan', 'petugas_psikolog', 'petugas_apoteker', 'petugas_radiografer', 'petugas_fisioterapi', 'petugas_konselor',
            'petugas_ahli_gizi', 'petugas_administrasi', 'petugas_kebersihan', 'petugas_parkir', 'petugas_satpam', 'petugas_kasir',
            'petugas_rohaniawan', 'petugas_lainnya', 'layanan_poli_reg', 'layanan_poli_eks', 'layanan_ranap', 'layanan_igd', 'layanan_icu',
            'layanan_farmasi', 'layanan_jenazah', 'layanan_lab', 'layanan_mcu', 'layanan_hemodialisa', 'layanan_fisioterapi', 'layanan_radioterapi',
            'layanan_radiologi', 'layanan_lainnya', 'fasilitas_parkir', 'fasilitas_taman', 'fasilitas_ambulan', 'fasilitas_toilet', 'fasilitas_tunggu',
            'fasilitas_lainnya', 'deskripsi', 'nilai_gangguan', 'created_at', 'updated_at'
        ];
    }

    public function map($row): array
    {
        $fields = [
            $row->nama,
            $row->no_hp,
            $row->email,
            $row->penerima,
            $row->punya_rm,
            $row->no_rm,
            $row->nama_pasien,
            $row->lahir_pasien,
            $row->waktu_kejadian,
            $row->tempat_kejadian,
            $row->pembiayaan,
            $row->pendaftaran_online,
            $row->pendaftaran_rajal,
            $row->pendaftaran_ranap,
            $row->pendaftaran_igd,
            $row->admin_bpjs,
            $row->petugas_dr_sp,
            $row->petugas_dr_umum,
            $row->petugas_dr_gigi,
            $row->petugas_perawat,
            $row->petugas_bidan,
            $row->petugas_psikolog,
            $row->petugas_apoteker,
            $row->petugas_radiografer,
            $row->petugas_fisioterapi,
            $row->petugas_konselor,
            $row->petugas_ahli_gizi,
            $row->petugas_administrasi,
            $row->petugas_kebersihan,
            $row->petugas_parkir,
            $row->petugas_satpam,
            $row->petugas_kasir,
            $row->petugas_rohaniawan,
            $row->petugas_lainnya,
            $row->layanan_poli_reg,
            $row->layanan_poli_eks,
            $row->layanan_ranap,
            $row->layanan_igd,
            $row->layanan_icu,
            $row->layanan_farmasi,
            $row->layanan_jenazah,
            $row->layanan_lab,
            $row->layanan_mcu,
            $row->layanan_hemodialisa,
            $row->layanan_fisioterapi,
            $row->layanan_radioterapi,
            $row->layanan_radiologi,
            $row->layanan_lainnya,
            $row->fasilitas_parkir,
            $row->fasilitas_taman,
            $row->fasilitas_ambulan,
            $row->fasilitas_toilet,
            $row->fasilitas_tunggu,
            $row->fasilitas_lainnya,
            $row->deskripsi,
            $row->nilai_gangguan,
            $row->created_at,
            $row->updated_at
        ];
        return $fields;
    }
}
