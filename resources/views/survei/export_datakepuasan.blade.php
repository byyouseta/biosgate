<html>

<head></head>

<body>
    <table>
        <thead>
            <tr>
                <th>No Handhphone</th>
                <th>Umur</th>
                <th>Jenis Kelamin</th>
                <th>Pendidikan Terakhir</th>
                <th>Pekerjaan Utama</th>
                <th>Debitur</th>
                <th>Unit Pelayanan</th>
                <th>Pertanyaan 1</th>
                <th>Pertanyaan 2</th>
                <th>Pertanyaan 3</th>
                <th>Pertanyaan 4</th>
                <th>Pertanyaan 5</th>
                <th>Pertanyaan 6</th>
                <th>Pertanyaan 7</th>
                <th>Pertanyaan 8</th>
                <th>Pertanyaan 9</th>
                <th>Pertanyaan 10</th>
                <th>Kritik dan Saran</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dataKepuasan as $data)
                @php
                    //Ambil data untuk Bukti Pelayanan
                    if ($data->pendidikan == 1) {
                        $pendidikan = 'SD';
                    } elseif ($data->pendidikan == 2) {
                        $pendidikan = 'SLTP';
                    } elseif ($data->pendidikan == 3) {
                        $pendidikan = 'SLTA';
                    } elseif ($data->pendidikan == 4) {
                        $pendidikan = 'D1-D2-D3';
                    } elseif ($data->pendidikan == 5) {
                        $pendidikan = 'D4-S1';
                    } elseif ($data->pendidikan == 6) {
                        $pendidikan = 'S2 ke atas';
                    }

                    if ($data->pekerjaan == 1) {
                        $pekerjaan = 'PNS/TNI/POLRI';
                    } elseif ($data->pekerjaan == 2) {
                        $pekerjaan = 'Pegawai Swasta';
                    } elseif ($data->pekerjaan == 3) {
                        $pekerjaan = 'Wiraswasta/ Usahawan';
                    } elseif ($data->pekerjaan == 4) {
                        $pekerjaan = 'Pelajar/Mahasiswa';
                    } elseif ($data->pekerjaan == 5) {
                        $pekerjaan = 'Lainnya';
                    }

                    if ($data->penjamin == 1) {
                        $penjamin = 'BPJS';
                    } elseif ($data->penjamin == 2) {
                        $penjamin = 'Asuransi';
                    } elseif ($data->penjamin == 3) {
                        $penjamin = 'Tanggungan Pribadi';
                    }

                    if ($data->unit == 1) {
                        $unit = 'Rawat Jalan';
                    } elseif ($data->unit == 2) {
                        $unit = 'Rawat Inap';
                    } elseif ($data->unit == 3) {
                        $unit = 'Medical Check Up (MCU)';
                    } elseif ($data->unit == 4) {
                        $unit = 'IGD';
                    } elseif ($data->unit == 5) {
                        $unit = 'Instalasi Bedah Sentral (IBS)';
                    } elseif ($data->unit == 6) {
                        $unit = 'ICU/NICU/PICU';
                    } elseif ($data->unit == 7) {
                        $unit = 'Farmasi';
                    } elseif ($data->unit == 8) {
                        $unit = 'Laboratorium';
                    } elseif ($data->unit == 9) {
                        $unit = 'Radiologi';
                    } elseif ($data->unit == 10) {
                        $unit = 'Rehabilitasi Medik';
                    } elseif ($data->unit == 11) {
                        $unit = 'Pendaftaran';
                    }elseif ($data->unit == 12) {
                        $unit = 'Konseling Gizi';
                    }elseif ($data->unit == 13) {
                        $unit = 'Konseling Kesehatan (TB/Asma)';
                    }

                @endphp
                <tr>
                    <td class="align-middle">{{ $data->no_hp }}</td>
                    <td class="align-middle">{{ $data->umur }}</td>
                    <td class="align-middle">{{ $data->jk == 1 ? 'Laki-laki' : 'Perempuan' }} </td>
                    <td class="align-middle">{{ $pendidikan != null ? $pendidikan : '' }} </td>
                    <td class="align-middle">{{ $pekerjaan != null ? $pekerjaan : '' }} </td>
                    <td class="align-middle">{{ $penjamin != null ? $penjamin : '' }} </td>
                    <td class="align-middle">{{ $unit != null ? $unit : '' }} </td>
                    <td class="align-middle">{{ $data->pertanyaan1 }} </td>
                    <td class="align-middle">{{ $data->pertanyaan2 }} </td>
                    <td class="align-middle">{{ $data->pertanyaan3 }} </td>
                    <td class="align-middle">{{ $data->pertanyaan4 }} </td>
                    <td class="align-middle">{{ $data->pertanyaan5 }} </td>
                    <td class="align-middle">{{ $data->pertanyaan6 }} </td>
                    <td class="align-middle">{{ $data->pertanyaan7 }} </td>
                    <td class="align-middle">{{ $data->pertanyaan8 }} </td>
                    <td class="align-middle">{{ $data->pertanyaan9 }} </td>
                    <td class="align-middle">{{ $data->pertanyaan10 }} </td>
                    <td class="align-middle">{{ $data->saran }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
