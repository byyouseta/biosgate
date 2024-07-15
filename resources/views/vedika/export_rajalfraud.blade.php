<html>

<head></head>

<body>
    <table>
        <thead>
            <tr>
                <th>No RM</th>
                <th>Nama</th>
                <th>No SEP</th>
                <th>KODE ICD X</th>
                <th>KODE ICD IX</th>
                <th>UP-CODING</th>
                <th>PHANTOM BILLING</th>
                <th>CLONING</th>
                <th>INFLATED BILLS</th>
                <th>PEMECAHAN EPISODE</th>
                <th>RUJUKAN SEMU</th>
                <th>REPEAT BILLING</th>
                <th>PROLONGED LOS</th>
                <th>MANIPULASI KELS PERAWATAN</th>
                <th>RE-ADMISI</th>
                <th>TINDAKAN TDK SESUAI INDIKASI</th>
                <th>MENAGIHKAN TINDAKAN YG TDK DILAKUKAN</th>
                <th>KLARIFIKASI</th>
                <th>KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dataFraud as $data)
                @php
                    //Ambil data untuk Bukti Pelayanan
                    $buktiPelayanan = \App\Http\Controllers\VedikaController::buktiPelayanan($data->dataPengajuan->no_rawat);
                    $diagnosa = $buktiPelayanan[0];
                    $prosedur = $buktiPelayanan[1];
                    $norm_pasien = $buktiPelayanan[2]->no_rkm_medis;
                @endphp
                <tr>
                    <td class="align-middle">{{ $norm_pasien }}</td>
                    <td class="align-middle">{{ $data->dataPengajuan->nama_pasien }}</td>
                    <td class="align-middle">{{ $data->dataPengajuan->no_sep }} </td>
                    <td class="align-middle">
                        @if (!empty($diagnosa))
                            @foreach ($diagnosa as $index => $dataDiagnosa)
                                {{ $dataDiagnosa->kd_penyakit }},
                            @endforeach
                        @endif
                    </td>
                    <td class="align-middle">
                        @if (!empty($prosedur))
                            @foreach ($prosedur as $index => $dataProsedur)
                                {{ $dataProsedur->kode }},
                            @endforeach
                        @endif
                    </td>
                    <td class="align-middle">{{ $data->up_coding == 1 ? 'YA' : 'TIDAK' }}</td>
                    <td class="align-middle">{{ $data->phantom_billing == 1 ? 'YA' : 'TIDAK' }}</td>
                    <td class="align-middle">{{ $data->cloning == 1 ? 'YA' : 'TIDAK' }}</td>
                    <td class="align-middle">{{ $data->inflated_bills == 1 ? 'YA' : 'TIDAK' }}</td>
                    <td class="align-middle">{{ $data->pemecahan == 1 ? 'YA' : 'TIDAK' }}</td>
                    <td class="align-middle">{{ $data->rujukan_semu == 1 ? 'YA' : 'TIDAK' }}</td>
                    <td class="align-middle">{{ $data->repeat_billing == 1 ? 'YA' : 'TIDAK' }}</td>
                    <td class="align-middle">{{ $data->prolonged_los == 1 ? 'YA' : 'TIDAK' }}</td>
                    <td class="align-middle">{{ $data->manipulasi_kels == 1 ? 'YA' : 'TIDAK' }}</td>
                    <td class="align-middle">{{ $data->re_admisi == 1 ? 'YA' : 'TIDAK' }}</td>
                    <td class="align-middle">{{ $data->kesesuaian_tindakan == 1 ? 'YA' : 'TIDAK' }}</td>
                    <td class="align-middle">{{ $data->tagihan_tindakan == 1 ? 'YA' : 'TIDAK' }}</td>
                    <td class="align-middle">{{ $data->klarifikasi }}</td>
                    <td class="align-middle">{{ $data->keterangan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
