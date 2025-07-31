<!DOCTYPE html>
<html lang="en">

<head>
    <title>Detail Pasien</title>
    {{-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous"> --}}
    {{-- <link rel="stylesheet" href="adminlte/plugins/bootstrap413/dist/css/bootstrap.min.css"> --}}
    <style>
        .header-billing {
            position: fixed;
            top: -30px;
            left: 0px;
            right: 0px;
            height: 50px;

            /* Extra personal styles
            background-color: #03a9f4; */
            color: black;;
            text-align: right;
            line-height: 0px;
        }

        .billing {
            position: fixed;
            top: 80px;
            left: 0px;
            right: 0px;
            height: 50px;
        }

        .page-break {
            page-break-after: always;
            page-break-inside: avoid;
            display: block;
        }

        footer {
            position: fixed;
            bottom: -60px;
            left: 0px;
            right: 0px;
            height: 50px;

            /** Extra personal styles **/
            /* background-color: #03a9f4; */
            color: grey;
            text-align: right;
            font-size: 11px;
            line-height: 35px;
        }

        html {
            margin-top: 10px
        }
    </style>
</head>

<body>
    @php
        $watermark = 'Vedika@RSUPGate';
    @endphp
    <style>
        table,
        th,
        td {
            border: 0px solid black;;
        }

        table {
            border-spacing: 0px;
        }
    </style>

    <style type="text/css">
        table tr td,
        table tr th {
            font-size: 10pt;
        }

        hr.new4 {
            border: 2px solid black;;
            margin-left: auto;
            margin-right: auto;
            margin-top: 0em;
            margin-bottom: 0em;
        }

        p.ex1 {
            margin-left: auto;
            margin-right: auto;
            margin-top: auto;
            margin-bottom: auto;
        }

        .watermark {
            position: fixed;
            top: 30%;
            width: 100%;
            text-align: center;
            font-size: 50px;
            color: rgb(228, 145, 145);
            opacity: .5;
            transform: rotate(-30deg);
            transform-origin: 50% 50%;
            z-index: -1000;
        }
    </style>

    {{-- Halaman Billing --}}
    @if ($billing->count()>0)
        <div style="top: -30px">
            <div class="watermark">
                {{ $watermark }}
            </div>
            {{-- <table style="width: 100%">
                <tr>
                    <td style="width:20%" rowspan="3"><img src="{{ public_path('image/logorsup.jpg') }}" alt="Logo RSUP"
                            width="100">
                    </td>
                    <td>
                        <h2>
                            <center>RSUP SURAKARTA</center>
                        </h2>
                    </td>
                    <td style="width:20%" rowspan="3"></td>
                </tr>
                <tr>
                    <td>
                        <center> Jl.Prof.Dr.R.Soeharso No.28 , Surakarta, Jawa Tengah</center>
                    </td>
                </tr>
                <tr>
                    <td>
                        <center>Telp.0271-713055 / 720002, E-mail : rsupsurakarta@kemkes.go.id</center>
                    </td>
                </tr>
            </table> --}}
            <img src="{{ asset('image/kop.png') }}" alt="KOP RSUP" >
        </div>
        <div>
            <hr class='new4' />
            <table style="width: 100%; border: 0 solid black;; line-height: 80%">
                <thead>
                    <tr>
                        <th colspan="8">
                            <h3>
                                <center>BILLING<center>
                            </h3>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total = 0;
                        $status_dokter = 0;
                    @endphp
                    @foreach ($billing as $data)
                        <tr>
                            @if ($data->status == 'TtlObat')
                                <td style="border:0px solid black;; text-align:right" colspan="8">
                                    {{ $data->nm_perawatan != null ? $data->nm_perawatan : '' }}
                                </td>
                            @elseif($data->status == 'Dokter' && $status_dokter == 0)
                                <td style="border:0px solid black;">Dokter</td>
                                <td style="border:0px solid black;" colspan="7">
                                    {{ $data->nm_perawatan != null ? ": $data->nm_perawatan" : '' }}
                                </td>
                                @php
                                    $status_dokter = 1;
                                @endphp
                            @elseif($data->no_status == 'Alamat Pasien')
                                <td style="border:0px solid black;; vertical-align:top">Alamat Pasien</td>
                                <td style="border:0px solid black;" colspan="7">
                                    {{ $data->nm_perawatan != null ? "$data->nm_perawatan" : '' }}
                                </td>
                            @elseif($data->status == 'Dokter' && $status_dokter == 1)
                                <td style="border:0px solid black;"></td>
                                <td style="border:0px solid black;" colspan="6">
                                    {{ $data->nm_perawatan != null ? ": $data->nm_perawatan" : '' }}
                                </td>
                            @elseif ($data->no_status != 'Dokter ')
                                <td style="border:0px solid black;; vertical-align:top; width:20%">
                                    {{ $data->no_status != null ? $data->no_status : '' }}</td>
                                <td style="border:0px solid black;; width:40%">
                                    {{ $data->nm_perawatan != null ? $data->nm_perawatan : '' }}</td>
                                <td style="border:0px solid black;; width:2.5%">
                                    {{ $data->pemisah != null ? $data->pemisah : '' }}
                                </td>
                                <td style="border:0px solid black;; text-align:right; width:15%">
                                    {{ $data->biaya != null ? number_format($data->biaya, 0, ',', '.') : '' }}
                                </td>
                                <td style="border:0px solid black;; width:2.5%"></td>
                                <td style="border:0px solid black;; width:2.5%">
                                    {{ $data->jumlah != null ? $data->jumlah : '' }}</td>
                                <td style="border:0px solid black;; width:2.5%">
                                    {{ $data->tambahan != null ? $data->tambahan : '' }}
                                </td>
                                <td style="border:0px solid black;; text-align:right; width:15%">
                                    {{ $data->totalbiaya != null ? number_format($data->totalbiaya, 0, ',', '.') : '' }}
                                    @php
                                        $total = $total + $data->totalbiaya;
                                    @endphp
                                </td>
                            @endif
                        </tr>
                    @endforeach
                    <tr>
                        <td style="border:0px solid black;">TOTAL BIAYA</td>
                        <td style="border:0px solid black;">: </td>
                        <td style="text-align:right" colspan="6">
                            {{ number_format($total, 0, ',', '.') }} </td>
                    </tr>
                </tbody>
            </table>
            <table style="border: 0px solid black;; width:100%">
                <tr>
                    <td style="border: 0px solid black;; width:50%; text-align:center">Keluarga Pasien </td>
                    <td style="border: 0px solid black;; width:50%; text-align:center; line-height:80%">
                        Surakarta,
                        {{ \Carbon\Carbon::parse($data->tgl_byr)->format('d-m-Y') }}<br>
                        <p>Petugas Kasir</p>
                    </td>
                </tr>
                <tr>
                    <td style="border: 0px solid black;; width:50%; text-align:center">(..........................)</td>
                    <td style="border: 0px solid black;; width:50%; text-align:center"> (..........................)
                    </td>
                </tr>
            </table>
        </div>
        <div style="float: none;">
            <div style="page-break-after: always;"></div>
        </div>
    @endif

    {{-- Lembar Selanjutnya Surat Bukti Pelayanan --}}
    <div>
        <div class="watermark">
            {{ $watermark }}
        </div>
        <img src="{{ asset('image/kop.png') }}" alt="KOP RSUP" >
        <hr class='new4' />
        <table style="border: 0px solid black;; width:100%">
            <tr>
                <th style="border: 0px solid black;; text-align:center" colspan="7">
                    <h3>SURAT BUKTI PELAYANAN KESEHATAN RAWAT JALAN</h3>
                </th>
            </tr>

            <tr>
                <td style="border: 0px solid black;; width: 20%">Nama Pasien</td>
                <td style="border: 0px solid black;; width: 50%">: {{ $pasien->nm_pasien }}</td>
                <td style="border: 0px solid black;; width: 25%"></td>
                <td style="border: 0px solid black;;"></td>
            </tr>
            <tr>
                <td style="border: 0px solid black;;">No. Rekam Medis</td>
                <td style="border: 0px solid black;;">: {{ $pasien->no_rkm_medis }}</td>
                <td style="border: 0px solid black;;">Cara Pulang</td>
                <td style="border: 0px solid black;;"></td>
            </tr>
            <tr>
                <td style="border: 0px solid black;;">Tanggal Lahir</td>
                <td style="border: 0px solid black;;">:
                    {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->format('d/m/Y') }}</td>
                <td style="border: 0px solid black;; vertical-align:top">
                    <div>
                        <input type="checkbox" onclick="return false;"
                            {{ $pasien->stts == 'Sudah' ? 'checked' : '' }}>
                        <label for="defaultCheck1">
                            Atas Persetujuan Dokter
                        </label>
                    </div>
                </td>
                <td style="border: 0px solid black;;"></td>
            </tr>
            <tr>
                <td style="border: 0px solid black;;">Jenis Kelamin</td>
                <td style="border: 0px solid black;;">: {{ $pasien->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}
                </td>
                <td style="border: 0px solid black;;">
                    <div>
                        <input type="checkbox" onclick="return false;"
                            {{ $pasien->stts == 'Dirujuk' ? 'checked' : '' }}>
                        <label for="defaultCheck1">
                            Rujuk
                        </label>
                    </div>
                </td>
                <td style="border: 0px solid black;;"></td>
            </tr>
            <tr>
                <td style="border: 0px solid black;;">Tanggal Kunjungan RS</td>
                <td style="border: 0px solid black;;">:
                    {{ \Carbon\Carbon::parse($pasien->tgl_registrasi)->format('d/m/Y') }}</td>
                <td style="border: 0px solid black;;">
                    <div>
                        <input type="checkbox" onclick="return false;"
                            {{ $pasien->stts == 'Dirawat' ? 'checked' : '' }}>
                        <label for="defaultCheck1">
                            MRS
                        </label>
                    </div>
                </td>
                <td style="border: 0px solid black;;"></td>
            </tr>
            <tr>
                <td style="border: 0px solid black;;">Jam Masuk</td>
                <td style="border: 0px solid black;;">: {{ $pasien->jam_reg }}</td>
                <td style="border: 0px solid black;;"></td>
                <td style="border: 0px solid black;;"></td>
            </tr>
            <tr>
                <td style="border: 0px solid black;;">Poliklinik</td>
                <td style="border: 0px solid black;;">: {{ $pasien->nm_poli }}</td>
                <td style="border: 0px solid black;;"></td>
                <td style="border: 0px solid black;;"></td>
            </tr>
            <tr>
                <td style="border: 0px solid black;;">Umur</td>
                <td style="border: 0px solid black;;">:
                    {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->diff(\Carbon\Carbon::parse($pasien->tgl_registrasi))->format('%y Th %m Bl %d Hr') }}
                </td>
                <td style="border: 0px solid black;;"></td>
                <td style="border: 0px solid black;;"></td>
            </tr>
            <tr>
                <td style="border: 0px solid black;;">Alamat</td>
                <td style="border: 0px solid black;;">: {{ $pasien->alamat }}</td>
                <td style="border: 0px solid black;;"></td>
                <td style="border: 0px solid black;;"></td>
            </tr>
            <tr>
                <td style="border: 0px solid black;;">Status Pasien</td>
                <td style="border: 0px solid black;;">: {{ $pasien->png_jawab }}</td>
                <td style="border: 0px solid black;;"></td>
                <td style="border: 0px solid black;;"></td>
            </tr>
        </table>
        <table style="width: 100%; margin-top:20px">
            <thead>
                <tr>
                    <th style="border: 1px solid black;;width: 5%">No</th>
                    <th style="border: 1px solid black;;width: 75%">Diagnosa</th>
                    <th style="border: 1px solid black;;width: 20%">ICD X</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border: 1px solid black;;  text-align:center;">1</td>
                    <td style="border: 1px solid black;;">
                        {{-- {{ !empty($dataRalan->penilaian) ? $dataRalan->penilaian : '' }} --}}
                        {{ !empty($dataRalan->penilaian) ? $dataRalan->penilaian : '' }}
                        {{ !empty($statusVerif->verifikasi) ? ", $statusVerif->verifikasi" : '' }}
                    </td>
                    <td style="border: 1px solid black;;">
                        @if (!empty($diagnosa))
                            @foreach ($diagnosa as $index => $dataDiagnosa)
                                {{ $dataDiagnosa->kd_penyakit }},<br>
                            @endforeach
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
        <table style="width: 100%; margin-top:20px; border: 1px solid black;">
            <thead>
                <tr>
                    <th style="border: 1px solid black;;width: 5%">No</th>
                    <th style="border: 1px solid black;;width: 75%">Prosedur</th>
                    <th style="border: 1px solid black;;width: 20%">ICD IX</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($prosedur as $index => $dataProsedur)
                    <tr>
                        <td style="border: 1px solid black;; text-align:center;">{{ ++$index }} </td>
                        <td style="border: 1px solid black;;">{{ $dataProsedur->deskripsi_panjang }}
                        </td>
                        <td style="border: 1px solid black;;">{{ $dataProsedur->kode }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td style="border: 1px solid black;;">&nbsp;</td>
                        <td style="border: 1px solid black;;">&nbsp;</td>
                        <td style="border: 1px solid black;;">&nbsp;</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <table style="width: 100%; margin-top:20px; border: 0px solid black;; text-align:center">
            <tr>
                <td style="border: 0px solid black;;width: 50%">Pasien</td>
                <td style="border: 0px solid black;;width: 50%">DPJP/Dokter Pemeriksa</td>
            </tr>
            <tr>
                @php
                    $ttd_pasien = \App\Vedika::getTtd($pasien->no_rawat);
                    $qr_dokter =
                        'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara elektronik oleh' .
                        "\n" .
                        $pasien->nm_dokter .
                        "\n" .
                        'ID ' .
                        $pasien->kd_dokter .
                        "\n" .
                        \Carbon\Carbon::parse($pasien->tgl_registrasi)->format('d-m-Y');
                    $qr_pasien =
                        'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara elektronik oleh' .
                        "\n" .
                        $pasien->nm_pasien .
                        "\n" .
                        'ID ' .
                        $pasien->no_rkm_medis .
                        "\n" .
                        \Carbon\Carbon::parse($pasien->tgl_registrasi)->format('d-m-Y');
                    $qrcode_dokter = base64_encode(
                        QrCode::format('png')->size(100)->errorCorrection('H')->generate($qr_dokter)
                    );
                    $qrcode_pasien = base64_encode(
                        QrCode::format('png')->size(100)->errorCorrection('H')->generate($qr_pasien)
                    );
                @endphp
                @if (!empty($ttd_pasien->tandaTangan))
                    <td style="border: 0px solid black;;width: 50%"> <img src="{{ $ttd_pasien->tandaTangan }}"
                            width="auto" height="100px"></td>
                @else
                    <td style="border: 0px solid black;;width: 50%"> <img
                            src="data:image/png;base64, {!! $qrcode_pasien !!}"></td>
                @endif

                <td style="border: 0px solid black;;width: 50%"> <img
                        src="data:image/png;base64, {!! $qrcode_dokter !!}"></td>
            </tr>
            <tr>
                <td style="border: 0px solid black;;width: 50%">{{ $pasien->nm_pasien }}</td>
                <td style="border: 0px solid black;;width: 50%"> {{ $pasien->nm_dokter }} </td>
            </tr>
        </table>
    </div>
    {{-- Lembar Selanjutnya Hasil Lab --}}
    @if ($permintaanLab)
        @foreach ($permintaanLab as $index => $order)
            <div style="float: none;">
                <div style="page-break-after: always;"></div>
            </div>
            <div class="watermark">
                {{ $watermark }}
            </div>
            <div>
                <img src="{{ asset('image/kop.png') }}" alt="KOP RSUP" >
                <hr class='new4' />
                <table style="width: 100%; border: 0px solid black;">
                    <thead>
                        <tr>
                            <th style="width: 100%; border: 0px solid black;" colspan="7">
                                <h3>HASIL PEMERIKSAAN LABORATIUM</h3>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="border: 0px solid black;;width: 15%">No.RM</td>
                            <td style="border: 0px solid black;;width: 40%">:
                                {{ $pasien->no_rkm_medis }}</td>
                            <td style="border: 0px solid black;;width: 25%">No.Permintaan Lab</td>
                            <td style="border: 0px solid black;;width: 25%">: {{ $order->noorder }}
                            </td>
                        </tr>
                        <tr>
                            <td>Nama Pasien</td>
                            <td>: {{ $pasien->nm_pasien }}</td>
                            <td>Tgl.Permintaan</td>
                            <td>:
                                {{ \Carbon\Carbon::parse($order->tgl_permintaan)->format('d-m-Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td>JK/Umur</td>
                            <td>:
                                {{ $pasien->jk == 'L' ? 'Laki-laki' : 'Perempuan' }} /
                                {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->diff(\Carbon\Carbon::parse($pasien->tgl_registrasi))->format('%y Th %m Bl %d Hr') }}
                            </td>
                            <td>Jam Permintaan</td>
                            <td>: {{ $order->jam_permintaan }}</td>
                        </tr>
                        <tr>
                            <td style="border: 0px solid black;; vertical-align:top">Alamat</td>
                            <td style="border: 0px solid black;;">: {{ $pasien->alamat }}</td>
                            <td style="border: 0px solid black;; vertical-align:top">Tgl. Keluar Hasil</td>
                            <td style="border: 0px solid black;; vertical-align:top">:
                                {{ \Carbon\Carbon::parse($order->tgl_hasil)->format('d-m-Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td>No.Periksa</td>
                            <td>: {{ $pasien->no_rawat }}</td>
                            <td>Jam Keluar Hasil</td>
                            <td>: {{ $order->jam_hasil }}</td>
                        </tr>
                        <tr>
                            <td>Dokter Pengirim</td>
                            <td>: {{ $order->nm_dokter }}
                            </td>
                            <td>Poli</td>
                            <td>: {{ $pasien->nm_poli }}</td>
                        </tr>
                    </tbody>
                </table>

                <table style="width: 100%; border: 1px solid black;; margin-top:20px">
                    <thead>
                        <tr>
                            <th style="border: 1px solid black;; width:35%;text-align: center">Pemeriksaan</th>
                            <th style="border: 1px solid black;; width:15%;text-align: center">Hasil</th>
                            <th style="border: 1px solid black;; width:10%;text-align: center">Satuan</th>
                            <th style="border: 1px solid black;; width:20%;text-align: center">Nilai Rujukan</th>
                            <th style="border: 1px solid black;; width:20%;text-align: center">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $hasil_lab = 0;
                        @endphp
                        @foreach ($hasilLab as $hasil)
                            @if ($hasil->jam == $order->jam_hasil)
                                <tr>
                                    <td style="border-left: 1px solid black;;border-right: 1px solid black;">
                                        {{ $hasil->Pemeriksaan != null ? $hasil->Pemeriksaan : '' }}
                                    </td>
                                    <td
                                        style="border-left: 1px solid black;;border-right: 1px solid black;; text-align:center">
                                        {{ $hasil->nilai != null ? $hasil->nilai : '' }}
                                    </td>
                                    <td
                                        style="border-left: 1px solid black;;border-right: 1px solid black;; text-align:center">
                                        {{ $hasil->satuan != null ? $hasil->satuan : '' }}
                                    </td>
                                    <td
                                        style="border-left: 1px solid black;;border-right: 1px solid black;; text-align:center">
                                        {{ $hasil->nilai_rujukan != null ? $hasil->nilai_rujukan : '' }}
                                    </td>
                                    <td
                                        style="border-left: 1px solid black;;border-right: 1px solid black;; text-align:center">
                                        {{ $hasil->keterangan != null ? $hasil->keterangan : '' }}
                                    </td>
                                </tr>
                                @php
                                    $hasil_lab = 1;
                                @endphp
                            @endif
                        @endforeach
                        @if ($hasil_lab == 0)
                            <tr>
                                <td colspan="5">
                                    <center>Belum ada hasil</center>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                <table style="width: 100%">
                    <tr>
                        <td style="border: 0px solid black;">
                            <p>Catatan:</b> Jika ada keragu-raguan pemeriksaan, diharapkan segera menghubungi
                                laboratorium.
                            </p>
                        </td>
                        <td style="border: 0px solid black;; text-align: right">Tgl.Cetak :
                            {{ Carbon\Carbon::now()->format('d/m/Y h:i:s') }}
                        </td>
                    </tr>
                </table>
                @if ($hasil_lab == 1)
                    <table style="width: 100%">
                        <tr>
                            <td style="border: 0px solid black;; text-align:center">Penanggung Jawab</td>
                            <td style="border: 0px solid black;; text-align:center"> Petugas Laboratorium</td>
                        </tr>
                        <tr>
                            @php
                                $dokterLab = \App\Vedika::getDokter($order->tgl_hasil, $order->jam_hasil);
                                $dokter_lab = $dokterLab->nm_dokter;
                                $kd_dokter_lab = $dokterLab->kd_dokter;

                                $petugasLab = \App\Vedika::getPetugas($order->tgl_hasil, $order->jam_hasil);
                                $petugas_lab = $petugasLab->nama;
                                $kd_petugas_lab = $petugasLab->nip;

                                $qr_dokter =
                                    'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara elektronik oleh' .
                                    "\n" .
                                    $dokter_lab .
                                    "\n" .
                                    'ID ' .
                                    $kd_dokter_lab .
                                    "\n" .
                                    \Carbon\Carbon::parse($order->tgl_hasil)->format('d-m-Y');
                                $qr_petugas =
                                    'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara elektronik oleh' .
                                    "\n" .
                                    $petugas_lab .
                                    "\n" .
                                    'ID ' .
                                    $kd_petugas_lab .
                                    "\n" .
                                    \Carbon\Carbon::parse($order->tgl_hasil)->format('d-m-Y');

                                $qrcode_dokter = base64_encode(
                                    QrCode::format('png')->size(100)->errorCorrection('H')->generate($qr_dokter)
                                );
                                $qrcode_petugas = base64_encode(
                                    QrCode::format('png')->size(100)->errorCorrection('H')->generate($qr_petugas)
                                );
                            @endphp

                            <td style="border: 0px solid black;; text-align:center"> <img
                                    src="data:image/png;base64, {!! $qrcode_dokter !!}"></td>
                            <td style="border: 0px solid black;; text-align:center"> <img
                                    src="data:image/png;base64, {!! $qrcode_petugas !!}"></td>
                        </tr>
                        <tr>
                            <td style="border: 0px solid black;; text-align:center">{{ $dokter_lab }}</td>
                            <td style="border: 0px solid black;; text-align:center"> {{ $petugas_lab }} </td>
                        </tr>
                    </table>
                @endif
            </div>
            <div style="float: none;">
                <div style="page-break-after: always;"></div>
            </div>
        @endforeach
    @endif
    {{-- Lembar Selanjutnya Radiologi --}}

    @if ($dataRadiologiRajal)
        @foreach ($dataRadiologiRajal as $urutan => $orderRadio)
            <div style="float: none;">
                <div style="page-break-after: always;"></div>
            </div>
            <div class="watermark">
                {{ $watermark }}
            </div>
            <div>
                <img src="{{ asset('image/kop.png') }}" alt="KOP RSUP" >
                <hr class='new4' />
                <table style="border: 0px solid black;; width:100%">
                    <thead>
                        <tr>
                            <th style="border: 0px solid black;;text-align: center; width:100%" colspan="4">
                                <h3>
                                    <center>HASIL PEMERIKSAAN RADIOLOGI<center>
                                </h3>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="border: 0px solid black;; width:15%; vertical-align:top">No.RM</td>
                            <td style="border: 0px solid black;; width:45%; vertical-align:top">:
                                {{ $pasien->no_rkm_medis }}
                            </td>
                            <td style="border: 0px solid black;; width:15%; vertical-align:top">Penanggung Jawab</td>
                            <td style="border: 0px solid black;; width:25%; vertical-align:top">:
                                {{ $dokterRadiologiRajal[$urutan]->nm_dokter }}</td>
                        </tr>
                        <tr>
                            <td style="border: 0px solid black;; vertical-align:top">Nama Pasien</td>
                            <td style="border: 0px solid black;; vertical-align:top">: {{ $pasien->nm_pasien }}</td>
                            <td style="border: 0px solid black;; vertical-align:top">Dokter Pengirim</td>
                            <td style="border: 0px solid black;; vertical-align:top">: {{ !empty($orderRadio->nm_dokter) ?
                                $orderRadio->nm_dokter : '' }}</td>

                        </tr>
                        <tr>
                            <td style="border: 0px solid black;;">JK/Umur</td>
                            <td style="border: 0px solid black;;">:
                                {{ $pasien->jk == 'L' ? 'Laki-laki' : 'Perempuan' }} /
                                {{
                                \Carbon\Carbon::parse($pasien->tgl_lahir)->diff(\Carbon\Carbon::parse($orderRadio->tgl_hasil))
                                ->format('%y Th %m Bl %d Hr') }}
                            </td>
                            <td style="border: 0px solid black;;">Tgl.Pemeriksaan</td>
                            <td style="border: 0px solid black;;">:
                                {{ \Carbon\Carbon::parse($orderRadio->tgl_hasil)->format('d-m-Y') }}
                            </td>

                        </tr>
                        <tr>
                            <td style="border: 0px solid black;; vertical-align:top">Alamat</td>
                            <td style="border: 0px solid black;; text-align:justify; padding-right:10px">:
                                {{ $orderRadio->alamat }}</td>
                            <td style="border: 0px solid black;;vertical-align:top">Jam Pemeriksaan</td>
                            <td style="border: 0px solid black;;vertical-align:top">: {{ $dokterRadiologiRajal[$urutan]->jam }}
                            </td>

                        </tr>
                        <tr>
                            <td style="border: 0px solid black;;">No.Periksa</td>
                            <td style="border: 0px solid black;;">: {{ $orderRadio->no_rawat }}</td>
                            <td style="border: 0px solid black;;">Poli</td>
                            <td style="border: 0px solid black;;">: {{ $orderRadio->nm_poli }}</td>
                        </tr>
                        <tr>
                            <td style="border: 0px solid black;;">Pemeriksaan</td>
                            <td style="border: 0px solid black;;">: {{ $dokterRadiologiRajal[$urutan]->nm_perawatan }}</td>
                        </tr>
                        <tr>
                            <td style="border: 0px solid black;;" colspan="4">Hasil Pemeriksaan</td>
                        </tr>
                    </tbody>
                </table>
                @php
                    if ((isset($hasilRadiologiRajal[$urutan])) && isset($hasilRadiologiRajal[$urutan]->hasil)) {
                        $paragraphs = explode("\n", $hasilRadiologiRajal[$urutan]->hasil);
                        $tinggi = 25 * count($paragraphs);
                    }else{
                        $tinggi = 25;
                    }
                @endphp
                <table style="width: 100%;">
                    <tr>
                        <textarea readonly
                            style="
                            min-height: {{ $tinggi }}px;
                            resize: none;
                            overflow-y:hidden;
                            border:1px solid black;;
                            background-color: white;
                        ">{{ isset($hasilRadiologiRajal[$urutan]) && isset($hasilRadiologiRajal[$urutan]->hasil) ? $hasilRadiologiRajal[$urutan]->hasil : '' }}</textarea>
                    </tr>
                </table>
                <table style="width: 100%; text-align:center">
                    <tr>
                        <td style="width: 70%; border: 0px solid black;"></td>
                        <td style="width: 30%; border: 0px solid black;">Dokter Radiologi</td>
                    </tr>
                    <tr>
                        @php
                            $qr_dokter =
                                'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara elektronik oleh' .
                                "\n" .
                                $dokterRadiologiRajal[$urutan]->nm_dokter .
                                "\n" .
                                'ID ' .
                                $dokterRadiologiRajal[$urutan]->kd_dokter .
                                "\n" .
                                \Carbon\Carbon::parse($dokterRadiologiRajal[$urutan]->tgl_periksa)->format('d-m-Y');
                            $qrcode_dokter = base64_encode(
                                QrCode::format('png')->size(100)->errorCorrection('H')->generate($qr_dokter)
                            );
                        @endphp
                        <td style="width: 70%; border: 0px solid black;"></td>
                        <td style="width: 30%; border: 0px solid black;"><img
                                src="data:image/png;base64, {!! $qrcode_dokter !!}"></td>
                    </tr>
                    <tr>
                        <td style="width: 70%; border: 0px solid black;"></td>
                        <td style="width: 30%; border: 0px solid black;">
                            {{ $dokterRadiologiRajal[$urutan]->nm_dokter }}
                        </td>
                    </tr>
                </table>
            </div>
        @endforeach
    @endif
    {{-- lembar selanjutnya Obat --}}
    @if ($resepObat)
        @foreach ($resepObat as $index => $resepObat)
            <div style="float: none;">
                <div style="page-break-after: always;"></div>
            </div>
            <div class="watermark">
                {{ $watermark }}
            </div>
            <div>
                <img src="{{ asset('image/kop.png') }}" alt="KOP RSUP" >
                <hr class='new4' />
                <table style="width: 100%">
                    <tbody>
                        <tr>
                            <td style="width: 15%; border:0px solid black;">Nama Pasien</td>
                            <td style="width: 60%; border:0px solid black;">: {{ $resepObat->nm_pasien }}</td>
                            <td style="width: 15%; border:0px solid black;">Jam Peresepan</td>
                            <td style="width: 10%; border:0px solid black;">: {{ $resepObat->jam_peresepan }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 15%; border:0px solid black;">No.RM</td>
                            <td style="width: 60%; border:0px solid black;">: {{ $resepObat->no_rkm_medis }}
                            </td>
                            <td style="width: 15%; border:0px solid black;">Jam Pelayanan</td>
                            <td style="width: 10%; border:0px solid black;">: {{ $resepObat->jam }}</td>
                        </tr>
                        <tr>
                            <td style="width: 15%; border:0px solid black;">No.Rawat</td>
                            <td style="width: 60%; border:0px solid black;">: {{ $resepObat->no_rawat }}</td>
                            <td style="width: 15%; border:0px solid black;">BB (Kg)</td>
                            <td style="width: 10%; border:0px solid black;">:
                                {{ !empty($bbPasien[$index]) ? $bbPasien[$index]->berat : '' }}</td>
                        </tr>
                        <tr>
                            <td style="width: 15%; border:0px solid black;">Tanggal Lahir</td>
                            <td style="width: 45%; border:0px solid black;">:
                                {{ \Carbon\Carbon::parse($resepObat->tgl_lahir)->format('d-m-Y') }}</td>
                        </tr>
                        <tr>
                            <td style="border:0px solid black;">Penanggung</td>
                            <td style="border:0px solid black;">: BPJS</td>
                        </tr>
                        <tr>
                            <td style="border:0px solid black;">Pemberi Resep</td>
                            <td style="border:0px solid black;">: {{ $resepObat->nm_dokter }}</td>
                        </tr>
                        <tr>
                            <td style="border:0px solid black;">No. Resep</td>
                            <td style="border:0px solid black;">: {{ $resepObat->no_resep }}</td>
                        </tr>
                        <tr>
                            <td style="border:0px solid black;">No. SEP</td>
                            <td style="border:0px solid black;">:
                                {{ App\Vedika::getSep($resepObat->no_rawat,2) != null ? App\Vedika::getSep($resepObat->no_rawat,2)->no_sep : '' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border:0px solid black;; vertical-align:top">Alamat</td>
                            <td style="border:0px solid black;" colspan="3">: {{ $resepObat->alamat }},
                                {{ $resepObat->nm_kel }},{{ $resepObat->nm_kec }},
                                {{ $resepObat->nm_kab }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <hr class='new4' />
                <table style="width:100%; border:0px solid black;">
                    <thead>
                        <tr>
                            <th colspan="3">
                                <h3>
                                    <center>RESEP</center>
                                </h3>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 0;
                        @endphp
                        @if (!empty($obatJadi[$index]))
                            @foreach ($obatJadi[$index] as $listObat)
                                @if (\App\Vedika::aturanObatJadi($pasien->no_rawat, $listObat->kode_brng) != null)
                                    <tr>
                                        <td style="border:0px solid black;; vertical-align:top">
                                            {{ ++$no }}
                                        </td>
                                        <td style="border:0px solid black;">
                                            {{ $listObat->nama_brng }} <br>
                                            {{ \App\Vedika::aturanObatJadi($pasien->no_rawat, $listObat->kode_brng)->aturan }}
                                        </td>
                                        <td style="border:0px solid black;; vertical-align:top">
                                            {{ $listObat->jml }} {{ $listObat->satuan }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        @endif
                        @if (!empty($obatRacik[$index]))
                            @foreach ($obatRacik[$index] as $listObatRacik)
                                @if ($resepObat->jam == $listObatRacik->jam)
                                    <tr>
                                        <td style="border:0px solid black;; vertical-align:top">
                                            {{ ++$no }}
                                        </td>
                                        <td style="border:0px solid black;">
                                            {{ $listObatRacik->nama_racik }}
                                            @php
                                                $jumlah = \App\Vedika::getRacikan(
                                                    $pasien->no_rawat,
                                                    $listObatRacik->jam
                                                )->count();
                                                $jumlah = $jumlah - 1;
                                            @endphp
                                            (@foreach (\App\Vedika::getRacikan($pasien->no_rawat, $listObatRacik->jam) as $index => $listRacikan)
                                                {{ $listRacikan->nama_brng }}
                                                {{ \App\Vedika::getJmlRacikan($pasien->no_rawat, $listRacikan->kode_brng, $listObatRacik->jam)->jml }}{{ $index != $jumlah ? ',' : '' }}
                                            @endforeach)
                                            <br>
                                            {{ $listObatRacik->aturan_pakai }}
                                        </td>
                                        <td style="border:0px solid black;; vertical-align:top">
                                            {{ $listObatRacik->jml_dr }} {{ $listObatRacik->nm_racik }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        @endif

                    </tbody>

                </table>
                <table style="width: 100%; border:0px solid black;">
                    <tr>
                        <td style="width: 70%; border:0px solid black;"></td>
                        <td style="width: 30%; border:0px solid black;; text-align:center">Surakarta,
                            {{ \Carbon\Carbon::parse($resepObat->tgl_perawatan)->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        @php
                            $qr_dokter =
                                'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara elektronik oleh' .
                                "\n" .
                                $resepObat->nm_dokter .
                                "\n" .
                                'ID ' .
                                $resepObat->kd_dokter .
                                "\n" .
                                \Carbon\Carbon::parse($resepObat->tgl_perawatan)->format('d-m-Y');
                            $qrcode_dokter = base64_encode(
                                QrCode::format('png')->size(100)->errorCorrection('H')->generate($qr_dokter)
                            );
                        @endphp
                        <td style="width: 70%; border:0px solid black;; text-align:center; vertical-align:top">
                            &nbsp;
                        </td>
                        <td style="width: 30%; border:0px solid black;; text-align:center"> <img
                                src="data:image/png;base64, {!! $qrcode_dokter !!}">
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 70%; border:0px solid black;; text-align:center">
                            &nbsp;<br> &nbsp;
                        </td>
                        <td style="text-align:center"> {{ $resepObat->nm_dokter }} </td>
                    </tr>
                </table>
            </div>
        @endforeach
    @endif

</body>

</html>
