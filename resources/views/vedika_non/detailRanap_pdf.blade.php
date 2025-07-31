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
            color: black;
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
        //dd($billing,$permintaanLab,$dataRadiologiRajal,$resepObat, $dataTriase, $resumeIgd);
    @endphp
    <style>
        table,
        th,
        td {
            border: 0px solid black;
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
            border: 2px solid black;
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
    {{-- Resume Medis --}}
    @if ($resumeRanap1)
        <div class="watermark">
            {{ $watermark }}
        </div>
        <div>
            <img src="{{ asset('image/kop.png') }}" alt="KOP RSUP">
            <hr class='new4' />
            <table style="width:100%;">
                <thead>
                    <tr>
                        <th colspan="4" style="border-bottom:2px solid gray;">
                            <div style="font-size:16pt; font-weight:bold; padding-bottom:2pt;">RESUME MEDIS PASIEN
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="width: 15%; vertical-align:middle;padding-left: 5pt;">Nama Pasien</td>
                        <td style="width: 40%; vertical-align:middle;">:
                            {{ $pasien->nm_pasien }}</td>
                        <td style="width: 15%; vertical-align:middle;">No. Rekam Medis</td>
                        <td style="width: 30%; vertical-align:middle; margin-right:10pt;">:
                            {{ $pasien->no_rkm_medis }}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 5pt;">Umur</td>
                        <td>:
                            {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->diff(\Carbon\Carbon::parse($pasien->tgl_registrasi))->format('%y Th %m Bl') }}
                        </td>
                        <td>Jenis Kelamin</td>
                        <td style=" margin-right:10pt;">: {{ $pasien->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-left: 5pt;">Tgl Lahir</td>
                        <td>:
                            {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->format('d-m-Y') }}</td>
                        <td>Tanggal Masuk</td>
                        <td style=" margin-right:10pt;">:
                            {{ $resumeRanap2->first()->waktu_masuk_ranap != '0000-00-00 00:00:00' ? \Carbon\Carbon::parse($resumeRanap2->first()->waktu_masuk_ranap)->format('d-m-Y'):'-' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-left: 5pt;">Alamat</td>
                        <td>: {{ $pasien->alamat }}</td>
                        <td>Tanggal Keluar</td>
                        <td style="margin-right:10pt;">:
                            {{ $resumeRanap2->first()->waktu_keluar_ranap != '0000-00-00 00:00:00' ? \Carbon\Carbon::parse($resumeRanap2->last()->waktu_keluar_ranap)->format('d-m-Y'):'-' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="margin-left: 5pt;"></td>
                        <td></td>
                        <td>Ruang</td>
                        <td style=" margin-right:10pt;">: {{ $resumeRanap2->last()->kd_kamar }}
                            {{ $resumeRanap2->last()->nm_bangsal }}</td>
                    </tr>
                </tbody>
            </table>
            <hr style="height:2px;border-width:0;color:gray;background-color:gray; margin-top:5pt">
            <table style="margin-left: 5pt; margin-right:10pt; width:100%">
                <tbody>
                    <tr>
                        <td style="width: 20%;  vertical-align:top;">Keluhan Utama Riwayat
                            Penyakit</td>
                        <td style="width: 2%; vertical-align:top; ">:</td>
                        <td
                            style="width: 78%; word-wrap: break-word; word-break: break-all; vertical-align:top;">
                            {{ $resumeRanap1->keluhan_utama }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%; vertical-align:top;">Diagnosis Masuk</td>
                        <td style="width: 2%; vertical-align:top;">:</td>
                        <td style="width: 78%; vertical-align:top;">
                            {{ $resumeRanap1->diagnosa_awal }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%; vertical-align:top;">Indikasi Dirawat </td>
                        <td style="width: 2%; vertical-align:top;">:</td>
                        <td style="width: 78%; vertical-align:top;">
                            {{ $resumeRanap1->alasan }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%; vertical-align:top;">Alergi</td>
                        <td style="width: 2%; vertical-align:top;">:</td>
                        <td style="width: 78%; vertical-align:top;">
                            {{ $resumeRanap1->alergi }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%; vertical-align:top;">Pemeriksaan Fisik</td>
                        <td style="width: 2%; vertical-align:top;">:</td>
                        <td
                            style="width: 78%;  word-wrap: break-word; text-align:justify; vertical-align:top;">
                            {{ $resumeRanap1->pemeriksaan_fisik }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%; vertical-align:top;">Pemeriksaan Penunjang
                            Radiologi
                        </td>
                        <td style="width: 2%; vertical-align:top;">:</td>
                        <td
                            style="width: 78%;  word-wrap: break-word; text-align:justify; vertical-align:top;">
                            {{ $resumeRanap1->pemeriksaan_penunjang }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%; vertical-align:top;">Pemeriksaan Penunjang
                            Laboratorium
                        </td>
                        <td style="width: 2%; vertical-align:top;">:</td>
                        <td style="width: 78%; vertical-align:top;">
                            {{ $resumeRanap1->hasil_laborat }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%; vertical-align:top;">Obat-obatan Selama
                            Perawatan</td>
                        <td style="width: 2%; vertical-align:top;">:</td>
                        <td style="width: 78%; vertical-align:top;">
                            {{ $resumeRanap1->obat_di_rs }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%; vertical-align:top;">Tindakan/Operasi Selama
                            Perawatan
                        </td>
                        <td style="width: 2%; vertical-align:top;">:</td>
                        <td style="width: 78%; vertical-align:top;">
                            {{ $resumeRanap1->tindakan_dan_operasi }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%; vertical-align:top;">Diagnosa Utama</td>
                        <td style="width: 2%; vertical-align:top;">:</td>
                        <td style="width: 78%; vertical-align:top;">
                            {{ $resumeRanap1->diagnosa_utama }}
                            <div style="float: right; text-align:right;">
                                {{ $resumeRanap3->slice(0, 1)->first()
                                    ? '(' . $resumeRanap3->slice(0, 1)->first()->kd_penyakit . ')'
                                    : '(............)' }}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%; vertical-align:top;">Diagnosa Sekunder</td>
                        <td colspan="2"></td>
                    </tr>
                    @if ($resumeRanap1->diagnosa_sekunder)
                        <tr>
                            <td style="padding-left: 20pt;" colspan="3">
                                1.
                                {{ $resumeRanap1->diagnosa_sekunder }}
                                <div style="float: right;">
                                    {{ $resumeRanap3->slice(1, 1)->first()
                                        ? '(' . $resumeRanap3->slice(1, 1)->first()->kd_penyakit . ')'
                                        : '(............)' }}
                                </div>
                            </td>
                        </tr>
                    @endif
                    @if ($resumeRanap1->diagnosa_sekunder2)
                        <tr>
                            <td style="padding-left: 20pt;" colspan="3">2.
                                {{ $resumeRanap1->diagnosa_sekunder2 }}
                                <div style="float: right;">
                                    {{ $resumeRanap3->slice(2, 1)->first()
                                        ? '(' . $resumeRanap3->slice(2, 1)->first()->kd_penyakit . ')'
                                        : '(............)' }}
                                </div>
                            </td>
                        </tr>
                    @endif

                    @if ($resumeRanap1->diagnosa_sekunder3)
                        <tr>
                            <td style="padding-left: 20pt;" colspan="3">3.
                                {{ $resumeRanap1->diagnosa_sekunder3 }}
                                <div style="float: right">
                                    {{ $resumeRanap3->slice(3, 1)->first()
                                        ? '(' . $resumeRanap3->slice(3, 1)->first()->kd_penyakit . ')'
                                        : '(............)' }}
                                </div>
                            </td>
                        </tr>
                    @endif
                    @if ($resumeRanap1->diagnosa_sekunder4 || $resumeRanap3->slice(4, 1)->first())
                        <tr>
                            <td style="padding-left: 20pt;" colspan="3">4.
                                {{ $resumeRanap1->diagnosa_sekunder4 }}
                                <div style="float: right">
                                    {{-- {{ $resumeRanap3->orderBy('prioritas','ASC')->slice(4,20)?
                                    '('.$resumeRanap3->orderBy('prioritas','ASC')->slice(4,20)->kd_penyakit.')':'(............)'
                                    }} --}}
                                    @if ($resumeRanap3->slice(4, 1))
                                        (
                                        @foreach ($resumeRanap3->slice(4, 20) as $list)
                                            {{ $list->kd_penyakit }},
                                        @endforeach
                                        )
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td style="vertical-align:top; width: 20%">Prosedur/Tindakan Utama</td>
                        <td style="vertical-align:top; width: 2%">:</td>
                        <td style="vertical-align:top; width: 78%">
                            {{ $resumeRanap1->prosedur_utama }}
                            <div style="float: right">
                                {{ $resumeRanap4->slice(0, 1)->first()
                                    ? '(' . $resumeRanap4->slice(0, 1)->first()->kode . ')'
                                    : '(............)' }}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;" colspan="3">Prosedur/Tindakan Sekunder</td>
                    </tr>
                    @if ($resumeRanap1->prosedur_sekunder || $resumeRanap4->slice(1, 1)->first())
                        <tr>
                            <td style="padding-left: 20pt;" colspan="3">1.
                                {{ $resumeRanap1->prosedur_sekunder }}
                                <div style="float: right">
                                    {{ $resumeRanap4->slice(1, 1)->first()
                                        ? '(' . $resumeRanap4->slice(1, 1)->first()->kode . ')'
                                        : '(............)' }}
                                </div>
                            </td>
                        </tr>
                    @endif
                    @if ($resumeRanap1->prosedur_sekunder2 || $resumeRanap4->slice(2, 1)->first())
                        <tr>
                            <td style="padding-left: 20pt;" colspan="3">2.
                                {{ $resumeRanap1->prosedur_sekunder2 }}
                                <div style="float: right">
                                    {{ $resumeRanap4->slice(2, 1)->first()
                                        ? '(' . $resumeRanap4->slice(2, 1)->first()->kode . ')'
                                        : '(............)' }}
                                </div>
                            </td>
                        </tr>
                    @endif
                    @if ($resumeRanap1->prosedur_sekunder3 || $resumeRanap4->slice(3, 1)->first())
                        <tr>
                            <td style="padding-left: 20pt;" colspan="3">3.
                                {{ $resumeRanap1->prosedur_sekunder3 }}
                                <div style="float: right">
                                    @if ($resumeRanap4->where('prioritas', '>', 3)->first())
                                        @php
                                            $dataProsedurLainnya = $resumeRanap4->where('prioritas', '>', 3);
                                            $last = $dataProsedurLainnya->count();
                                            echo '(';
                                            foreach ($dataProsedurLainnya as $itemProsedur) {
                                                if ($itemProsedur->kode == $resumeRanap4->last()->kode) {
                                                    echo "$itemProsedur->kode";
                                                } else {
                                                    echo "$itemProsedur->kode, ";
                                                }
                                            }
                                            echo ')';
                                        @endphp
                                    @else
                                        (............)
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td style="vertical-align:top; width: 20%">Diet Selama Perawatan</td>
                        <td style="vertical-align:top; width: 2%">:</td>
                        <td style="vertical-align:top; width: 78%">
                            {{ $resumeRanap1->diet }}
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top; width: 20%">Keadaan Pulang</td>
                        <td style="vertical-align:top; width: 2%">:</td>
                        <td style="vertical-align:top; width: 78%">
                            {{ $resumeRanap1->keadaan }}
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top; width: 20%">Cara Keluar</td>
                        <td style="vertical-align:top; width: 2%">:</td>
                        <td style="vertical-align:top; width: 78%">
                            {{ $resumeRanap1->cara_keluar }}
                            {{ $resumeRanap1->ket_keluar }}
                            <br>
                            <pre class="tab1">TD : {{ $resumeRanap1->td }} mmHg     HR : {{ $resumeRanap1->hr }} x/menit   RR : {{ $resumeRanap1->rr }} x/menit   Suhu : {{ $resumeRanap1->suhu }} &deg;C</pre>

                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;width: 20%">Hasil Lab yang Belum Selesai
                            (Pending)</td>
                        <td style="vertical-align:top;width: 2%">:</td>
                        <td style="vertical-align:top;width: 78%">
                            {{ $resumeRanap1->lab_belum }}
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;width: 20%">Obat-obatan Waktu Pulang</td>
                        <td style="vertical-align:top;width: 2%">:</td>
                        <td style="vertical-align:top;width: 78%">
                            {{ $resumeRanap1->obat_pulang }}
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;width: 20%">Perawatan Selanjutnya</td>
                        <td style="vertical-align:top;width: 2%">:</td>
                        <td style="vertical-align:top;width: 78%">
                            {{ $resumeRanap1->dilanjutkan }}
                            {{ $resumeRanap1->ket_dilanjutkan }}
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;width: 20%">Tanggal Kontrol</td>
                        <td style="vertical-align:top;width: 2%">:</td>
                        <td style="vertical-align:top;width: 78%">
                            {{ $resumeRanap1->kontrol }}
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;width: 20%">Edukasi Pasien</td>
                        <td style="vertical-align:top;width: 2%">:</td>
                        <td style="vertical-align:top;width: 78%">
                            {{ $resumeRanap1->edukasi }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <table style="padding-top: 20pt; width:100%">
                <tr>
                    <td style="width: 50%; text-align:center;">
                        Surakarta,
                        {{ \Carbon\Carbon::parse($resumeRanap2->last()->waktu_keluar_ranap)->locale('id')->isoFormat('D MMMM Y') }}<br>
                        Dokter Penanggung Jawab Pelayanan
                    </td>
                    <td style="text-align: center; vertical-align:bottom;">
                        Pasien / Keluarga
                    </td>
                </tr>
                <tr>
                    @php
                        $qr_dokter =
                            'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara
                    elektronik oleh' .
                            "\n" .
                            $resumeRanap1->nm_dokter .
                            "\n" .
                            'ID ' .
                            $resumeRanap1->kd_dokter .
                            "\n" .
                            \Carbon\Carbon::now()->format('d-m-Y');
                        $qrcode_dokter = base64_encode(
                            QrCode::format('png')->size(100)->errorCorrection('H')->generate($qr_dokter)
                        );
                    @endphp

                    <td style="text-align: center"> <img src="data:image/png;base64, {!! $qrcode_dokter !!}">
                    </td>
                    <td style="width: 50%"></td>
                </tr>
                <tr>
                    <td style="text-align: center;"> ({{ $resumeRanap1->nm_dokter }}) </td>
                    <td style="text-align: center;">(....................)</td>
                </tr>
            </table>
        </div>
    @endif

    {{-- Halaman Billing --}}
    @if ($billing->count() > 0)
        <div style="float: none;">
            <div style="page-break-after: always;"></div>
        </div>
        <div style="top: -30px">
            <div class="watermark">
                {{ $watermark }}
            </div>
            <img src="{{ asset('image/kop.png') }}" alt="KOP RSUP">
        </div>
        <div>
            <hr class='new4' />
            <table style="width: 100%; border: 0 solid black; line-height: 80%">
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
                                <td style="border:0px solid black; text-align:right" colspan="8">
                                    {{ $data->nm_perawatan != null ? $data->nm_perawatan : '' }}
                                </td>
                            @elseif($data->status == 'Dokter' && $status_dokter == 0)
                                <td style="border:0px solid black">Dokter</td>
                                <td style="border:0px solid black" colspan="7">
                                    {{ $data->nm_perawatan != null ? ": $data->nm_perawatan" : '' }}
                                </td>
                                @php
                                    $status_dokter = 1;
                                @endphp
                            @elseif($data->no_status == 'Alamat Pasien')
                                <td style="border:0px solid black; vertical-align:top">Alamat Pasien</td>
                                <td style="border:0px solid black" colspan="7">
                                    {{ $data->nm_perawatan != null ? "$data->nm_perawatan" : '' }}
                                </td>
                            @elseif($data->status == 'Dokter' && $status_dokter == 1)
                                <td style="border:0px solid black"></td>
                                <td style="border:0px solid black" colspan="6">
                                    {{ $data->nm_perawatan != null ? ": $data->nm_perawatan" : '' }}
                                </td>
                            @elseif ($data->no_status != 'Dokter ')
                                <td style="border:0px solid black; vertical-align:top; width:20%">
                                    {{ $data->no_status != null ? $data->no_status : '' }}</td>
                                <td style="border:0px solid black; width:40%">
                                    {{ $data->nm_perawatan != null ? $data->nm_perawatan : '' }}</td>
                                <td style="border:0px solid black; width:2.5%">
                                    {{ $data->pemisah != null ? $data->pemisah : '' }}
                                </td>
                                <td style="border:0px solid black; text-align:right; width:15%">
                                    {{ $data->biaya != null ? number_format($data->biaya, 0, ',', '.') : '' }}
                                </td>
                                <td style="border:0px solid black; width:2.5%"></td>
                                <td style="border:0px solid black; width:2.5%">
                                    {{ $data->jumlah != null ? $data->jumlah : '' }}</td>
                                <td style="border:0px solid black; width:2.5%">
                                    {{ $data->tambahan != null ? $data->tambahan : '' }}
                                </td>
                                <td style="border:0px solid black; text-align:right; width:15%">
                                    {{ $data->totalbiaya != null ? number_format($data->totalbiaya, 0, ',', '.') : '' }}
                                    @php
                                        $total = $total + $data->totalbiaya;
                                    @endphp
                                </td>
                            @endif
                        </tr>
                    @endforeach
                    <tr>
                        <td style="border:0px solid black">TOTAL BIAYA</td>
                        <td style="border:0px solid black">: </td>
                        <td style="text-align:right" colspan="6">
                            {{ number_format($total, 0, ',', '.') }} </td>
                    </tr>
                </tbody>
            </table>
            <table style="border: 0px solid black; width:100%">
                <tr>
                    <td style="border: 0px solid black; width:50%; text-align:center">Keluarga Pasien </td>
                    <td style="border: 0px solid black; width:50%; text-align:center; line-height:80%">
                        Surakarta,
                        {{ \Carbon\Carbon::parse($data->tgl_byr)->format('d-m-Y') }}<br>
                        <p>Petugas Kasir</p>
                    </td>
                </tr>
                <tr>
                    <td style="border: 0px solid black; width:50%; text-align:center">(..........................)</td>
                    <td style="border: 0px solid black; width:50%; text-align:center"> (..........................)
                    </td>
                </tr>
            </table>
        </div>
    @endif
    {{-- Data SPRI --}}
    {{-- @if ($spri)
        <div>
            <div style="float: none;">
                <div style="page-break-after: always;"></div>
            </div>
            <div style="top: -30px">
                <div class="watermark">
                    {{ $watermark }}
                </div>
                <img src="{{ asset('image/kop.png') }}" alt="KOP RSUP">
            </div>
            <hr class='new4' />
            <table style="width: 100%">
                <thead>
                    <tr>
                        <th colspan="7">
                            <div style="font-size:18px; text-decoration: underline;margin-top:12px;">SURAT PERINTAH
                                RAWAT INAP</div>
                            <div style="margin-bottom:12px;">No : {{ $spri->no_rawat }}</div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="width: 30%">Pasien dikirim dari</td>
                        <td style="width: 70%">: {{ $pasien->nm_poli }}</td>
                    </tr>
                    <tr>
                        <td>Hari/ Tanggal/ Jam</td>
                        <td>: {{ $spri->tanggal }} {{ $spri->jam }}</td>
                    </tr>
                    <tr>
                        <td>Nama Pasien</td>
                        <td>: {{ $pasien->nm_pasien }}</td>
                    </tr>
                    <tr>
                        <td>No. RM</td>
                        <td>: {{ $pasien->no_rkm_medis }}</td>
                    </tr>
                    <tr>
                        <td>Diagnosa Kerja</td>
                        <td>: {{ $spri->diagnosa }}</td>
                    </tr>
                    <tr>
                        <td>Indikasi Rawat</td>
                        <td>: {{ $spri->catatan }}</td>
                    </tr>
                    <tr>
                        <td>Tindakan yang akan dilakukan
                            dan alternatifnya</td>
                        <td style="vertical-align: top">: {{ $spri->tindakan }}</td>
                    </tr>
                    <tr>
                        <td>Perkiraan hasil yang</td>
                        <td>: {{ $spri->perkiraan_hasil }}</td>
                    </tr>
                    <tr>
                        <td>Cara Bayar</td>
                        <td>: {{ $pasien->png_jawab }}</td>
                    </tr>
                    <tr>
                        <td>Perkiraan biaya yang</td>
                        <td>: {{ $spri->perkiraan_biaya }}</td>
                    </tr>
                    <tr>
                        <td>Bangsal / Ruang / Kelas</td>
                        <td>: {{ $spri->kd_kamar }} {{ $spri->nm_bangsal }}</td>
                    </tr>
                    <tr>
                        <td>Nama DPJP pasien</td>
                        <td>: {{ $spri->nm_dokter }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding-top: 10pt">Informasi rencana perawatan hasil yang diharapkan
                            dapat berubah
                            selama perawatan rawat inap sesuai dengan
                            perkembangan kondisi pasien.</td>
                    </tr>
                </tbody>

            </table>
            <table style="width: 100%">
                <tr>
                    <td style="width: 70%"></td>
                    <td style="width: 30%;">
                        <div>Surakarta,
                            {{ \Carbon\Carbon::parse($spri->tanggal)->format('d-m-Y') }}</div>
                        <div style="text-align:center;">
                            Dokter Pengirim
                        </div>
                    </td>
                </tr>
                <tr>
                    @php
                        $qr_dokter =
                            'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara
                elektronik oleh' .
                            "\n" .
                            $spri->nm_dokter .
                            "\n" .
                            'ID ' .
                            $spri->kd_dokter .
                            "\n" .
                            \Carbon\Carbon::parse($spri->tanggal)->format('d-m-Y');
                        $qrcode_dokter = base64_encode(
                            QrCode::format('png')->size(100)->errorCorrection('H')->generate($qr_dokter)
                        );
                    @endphp
                    <td style="width: 70%"></td>
                    <td style="width: 30%; text-align:center;"> <img
                            src="data:image/png;base64, {!! $qrcode_dokter !!}">
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td style="text-align: center;"> ({{ $spri->nm_dokter }}) </td>
                </tr>
            </table>
        </div>
    @endif --}}
    {{-- End SPRI --}}
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
                <img src="{{ asset('image/kop.png') }}" alt="KOP RSUP">
                <hr class='new4' />
                <table style="width: 100%; border: 0px solid black">
                    <thead>
                        <tr>
                            <th style="width: 100%; border: 0px solid black" colspan="7">
                                <h3>HASIL PEMERIKSAAN LABORATIUM</h3>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="border: 0px solid black;width: 15%">No.RM</td>
                            <td style="border: 0px solid black;width: 40%">:
                                {{ $pasien->no_rkm_medis }}</td>
                            <td style="border: 0px solid black;width: 25%">No.Permintaan Lab</td>
                            <td style="border: 0px solid black;width: 25%">: {{ $order->noorder }}
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
                            <td style="border: 0px solid black; vertical-align:top">Alamat</td>
                            <td style="border: 0px solid black;">: {{ $pasien->alamat }}</td>
                            <td style="border: 0px solid black; vertical-align:top">Tgl. Keluar Hasil</td>
                            <td style="border: 0px solid black; vertical-align:top">:
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

                <table style="width: 100%; border: 1px solid black; margin-top:20px">
                    <thead>
                        <tr>
                            <th style="border: 1px solid black; width:35%;text-align: center">Pemeriksaan</th>
                            <th style="border: 1px solid black; width:15%;text-align: center">Hasil</th>
                            <th style="border: 1px solid black; width:10%;text-align: center">Satuan</th>
                            <th style="border: 1px solid black; width:20%;text-align: center">Nilai Rujukan</th>
                            <th style="border: 1px solid black; width:20%;text-align: center">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $hasil_lab = 0;
                        @endphp
                        @foreach ($hasilLab as $hasil)
                            @if ($hasil->jam == $order->jam_hasil)
                                <tr>
                                    <td style="border-left: 1px solid black;border-right: 1px solid black">
                                        {{ $hasil->Pemeriksaan != null ? $hasil->Pemeriksaan : '' }}
                                    </td>
                                    <td
                                        style="border-left: 1px solid black;border-right: 1px solid black; text-align:center">
                                        {{ $hasil->nilai != null ? $hasil->nilai : '' }}
                                    </td>
                                    <td
                                        style="border-left: 1px solid black;border-right: 1px solid black; text-align:center">
                                        {{ $hasil->satuan != null ? $hasil->satuan : '' }}
                                    </td>
                                    <td
                                        style="border-left: 1px solid black;border-right: 1px solid black; text-align:center">
                                        {{ $hasil->nilai_rujukan != null ? $hasil->nilai_rujukan : '' }}
                                    </td>
                                    <td
                                        style="border-left: 1px solid black;border-right: 1px solid black; text-align:center">
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
                <table style="width: 100%; margin-top:0;">
                    @foreach ($kesanLab as $kesan)
                        @if ($kesan->jam == $order->jam_hasil)
                            <tr>
                                <td style="width: 5%; border-bottom: 1px solid black; vertical-align:top">
                                    Kesan</td>
                                <td style="width: 95%; border-bottom: 1px solid black">
                                    : {{ $kesan->kesan }}</td>
                            </tr>
                            <tr>
                                <td style="width:5%; border-bottom: 1px solid black">
                                    Saran</td>
                                <td style="width:95%; border-bottom: 1px solid black">
                                    : {{ $kesan->saran }}</td>
                            </tr>
                        @endif
                    @endforeach
                </table>
                <div>
                    <small><b>Catatan:</b> Jika ada keragu-raguan pemeriksaan, diharapkan
                        segera
                        menghubungi
                        laboratorium.</small>
                    {{-- <div class="float-right">Tgl.Cetak :
                {{ Carbon\Carbon::now()->format('d/m/Y h:i:s') }}
            </div> --}}
                </div>
                @if ($hasil_lab == 1)
                    <table style="width: 100%">
                        <tr>
                            <td style="border: 0px solid black; text-align:center">Penanggung Jawab</td>
                            <td style="border: 0px solid black; text-align:center"> Petugas Laboratorium</td>
                        </tr>
                        <tr>
                            @php
                                $dokterLab = \App\Vedika::getDokter($order->tgl_hasil, $order->jam_hasil);
                                $dokter_lab = isset($dokterLab) && $dokterLab->nm_dokter? $dokterLab->nm_dokter :'';
                                $kd_dokter_lab = isset($dokterLab) && $dokterLab->kd_dokter ? $dokterLab->kd_dokter :'';

                                $petugasLab = \App\Vedika::getPetugas($order->tgl_hasil, $order->jam_hasil);
                                $petugas_lab = isset($petugasLab) && $petugasLab->nama?$petugasLab->nama:'';
                                $kd_petugas_lab = isset($petugasLab) && $petugasLab->nip?$petugasLab->nip:'';

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

                            <td style="border: 0px solid black; text-align:center"> <img
                                    src="data:image/png;base64, {!! $qrcode_dokter !!}"></td>
                            <td style="border: 0px solid black; text-align:center"> <img
                                    src="data:image/png;base64, {!! $qrcode_petugas !!}"></td>
                        </tr>
                        <tr>
                            <td style="border: 0px solid black; text-align:center">{{ $dokter_lab }}</td>
                            <td style="border: 0px solid black; text-align:center"> {{ $petugas_lab }} </td>
                        </tr>
                    </table>
                @endif
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
                <img src="{{ asset('image/kop.png') }}" alt="KOP RSUP">
                <hr class='new4' />
                <table style="border: 0px solid black; width:100%">
                    <thead>
                        <tr>
                            <th style="border: 0px solid black;text-align: center; width:100%" colspan="4">
                                <h3>
                                    <center>HASIL PEMERIKSAAN RADIOLOGI<center>
                                </h3>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="border: 0px solid black; width:15%; vertical-align:top">No.RM</td>
                            <td style="border: 0px solid black; width:45%; vertical-align:top">:
                                {{ $pasien->no_rkm_medis }}
                            </td>
                            <td style="border: 0px solid black; width:15%; vertical-align:top">Penanggung Jawab</td>
                            <td style="border: 0px solid black; width:25%; vertical-align:top">:
                                {{ $dokterRadiologiRajal[$urutan]->nm_dokter }}</td>
                        </tr>
                        <tr>
                            <td style="border: 0px solid black; vertical-align:top">Nama Pasien</td>
                            <td style="border: 0px solid black; vertical-align:top">: {{ $pasien->nm_pasien }}</td>
                            <td style="border: 0px solid black; vertical-align:top">Dokter Pengirim</td>
                            <td style="border: 0px solid black; vertical-align:top">:
                                {{ !empty($orderRadio->nm_dokter) ? $orderRadio->nm_dokter : '' }}
                            </td>

                        </tr>
                        <tr>
                            <td style="border: 0px solid black;">JK/Umur</td>
                            <td style="border: 0px solid black;">:
                                {{ $pasien->jk == 'L' ? 'Laki-laki' : 'Perempuan' }} /
                                {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->diff(\Carbon\Carbon::parse($orderRadio->tgl_hasil))->format('%y Th %m Bl %d Hr') }}
                            </td>
                            <td style="border: 0px solid black;">Tgl.Pemeriksaan</td>
                            <td style="border: 0px solid black;">:
                                {{ \Carbon\Carbon::parse($orderRadio->tgl_hasil)->format('d-m-Y') }}
                            </td>

                        </tr>
                        <tr>
                            <td style="border: 0px solid black; vertical-align:top">Alamat</td>
                            <td style="border: 0px solid black; text-align:justify; padding-right:10px">:
                                {{ $orderRadio->alamat }}</td>
                            <td style="border: 0px solid black;vertical-align:top">Jam Pemeriksaan</td>
                            <td style="border: 0px solid black;vertical-align:top">:
                                {{ $dokterRadiologiRajal[$urutan]->jam }}
                            </td>

                        </tr>
                        <tr>
                            <td style="border: 0px solid black;">No.Periksa</td>
                            <td style="border: 0px solid black;">: {{ $orderRadio->no_rawat }}</td>
                            <td style="border: 0px solid black;">Poli</td>
                            <td style="border: 0px solid black;">: {{ $orderRadio->nm_poli }}</td>
                        </tr>
                        <tr>
                            <td style="border: 0px solid black;">Pemeriksaan</td>
                            <td style="border: 0px solid black;">: {{ $dokterRadiologiRajal[$urutan]->nm_perawatan }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border: 0px solid black;" colspan="4">Hasil Pemeriksaan</td>
                        </tr>
                    </tbody>
                </table>
                @php
                    if (isset($hasilRadiologiRajal) && isset($hasilRadiologiRajal[$urutan]->hasil)) {
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
                            border:1px solid black;
                            background-color: white;
                        ">{{ isset($hasilRadiologiRajal) && isset($hasilRadiologiRajal[$urutan]->hasil) ? $hasilRadiologiRajal[$urutan]->hasil : '' }}</textarea>
                    </tr>
                </table>
                <table style="width: 100%; text-align:center">
                    <tr>
                        <td style="width: 70%; border: 0px solid black"></td>
                        <td style="width: 30%; border: 0px solid black">Dokter Radiologi</td>
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
                        <td style="width: 70%; border: 0px solid black"></td>
                        <td style="width: 30%; border: 0px solid black"><img
                                src="data:image/png;base64, {!! $qrcode_dokter !!}"></td>
                    </tr>
                    <tr>
                        <td style="width: 70%; border: 0px solid black"></td>
                        <td style="width: 30%; border: 0px solid black">
                            {{ $dokterRadiologiRajal[$urutan]->nm_dokter }}
                        </td>
                    </tr>
                </table>
            </div>
        @endforeach
    @endif
    @if ($dataRadiologiRanap)
        @foreach ($dataRadiologiRanap as $urutan => $orderRadio)
            @php
                $dokterRadiologi = \App\Vedika::getRadioDokter($orderRadio->no_rawat, $orderRadio->jam_hasil);
            @endphp
            <div style="float: none;">
                <div style="page-break-after: always;"></div>
            </div>
            <div class="watermark">
                {{ $watermark }}
            </div>
            <div>
                <img src="{{ asset('image/kop.png') }}" alt="KOP RSUP">
                <hr class='new4' />
                <table style="border: 0px solid black; width:100%">
                    <thead>
                        <tr>
                            <th style="border: 0px solid black;text-align: center; width:100%" colspan="4">
                                <h3>
                                    <center>HASIL PEMERIKSAAN RADIOLOGI<center>
                                </h3>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="border: 0px solid black; width:15%; vertical-align:top">No.RM</td>
                            <td style="border: 0px solid black; width:45%; vertical-align:top">:
                                {{ $pasien->no_rkm_medis }}
                            </td>
                            <td style="border: 0px solid black; width:15%; vertical-align:top">Penanggung Jawab</td>
                            <td style="border: 0px solid black; width:25%; vertical-align:top">:
                                {{ $dokterRadiologiRanap[$urutan]->nm_dokter }}</td>
                        </tr>
                        <tr>
                            <td style="border: 0px solid black; vertical-align:top">Nama Pasien</td>
                            <td style="border: 0px solid black; vertical-align:top">: {{ $pasien->nm_pasien }}</td>
                            <td style="border: 0px solid black; vertical-align:top">Dokter Pengirim</td>
                            <td style="border: 0px solid black; vertical-align:top">:
                                {{ !empty($orderRadio->nm_dokter) ? $orderRadio->nm_dokter : '' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border: 0px solid black;">JK/Umur</td>
                            <td style="border: 0px solid black;">:
                                {{ $pasien->jk == 'L' ? 'Laki-laki' : 'Perempuan' }} /
                                {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->diff(\Carbon\Carbon::parse($orderRadio->tgl_hasil))->format('%y Th %m Bl %d Hr') }}
                            </td>
                            <td style="border: 0px solid black;">Tgl.Pemeriksaan</td>
                            <td style="border: 0px solid black;">:
                                {{ \Carbon\Carbon::parse($orderRadio->tgl_hasil)->format('d-m-Y') }}
                            </td>

                        </tr>
                        <tr>
                            <td style="border: 0px solid black; vertical-align:top">Alamat</td>
                            <td style="border: 0px solid black; text-align:justify; padding-right:10px">:
                                {{ $orderRadio->almt_pj }}</td>
                            <td style="border: 0px solid black;vertical-align:top">Jam Pemeriksaan</td>
                            <td style="border: 0px solid black;vertical-align:top">:
                                {{ $orderRadio->jam_hasil }}
                            </td>

                        </tr>
                        <tr>
                            <td style="border: 0px solid black;vertical-align:top">No.Periksa</td>
                            <td style="border: 0px solid black;vertical-align:top">: {{ $orderRadio->no_rawat }}</td>
                            <td style="vertical-align:top;">Kamar</td>
                            <td style="vertical-align:top;">:
                                {{ !empty($dokterRadiologi->kd_kamar) ? $dokterRadiologi->kd_kamar : '' }},
                                {{ !empty($dokterRadiologi->nm_bangsal) ? $dokterRadiologi->nm_bangsal : '' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border: 0px solid black;">Pemeriksaan</td>
                            <td style="border: 0px solid black;">: {{ $orderRadio->nm_perawatan }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border: 0px solid black;" colspan="4">Hasil Pemeriksaan</td>
                        </tr>
                    </tbody>
                </table>
                @foreach ($hasilRadiologiRanap as $dataHasil)
                    @if (!empty($dataHasil->hasil) && $dataHasil->jam == $orderRadio->jam_hasil)
                        @php

                            $paragraphs = explode("\n", $dataHasil->hasil);
                            $tinggi = 25 * count($paragraphs);

                        @endphp

                        <table style="width: 100%">
                            <tbody>
                                <tr>
                                    <td style="width: 78%; border:1px solid black; ">
                                        <pre style="white-space: pre-wrap; word-wrap: break-word; overflow: hidden; padding-left: 10pt;">{{ $dataHasil->jam == $orderRadio->jam_hasil ? $dataHasil->hasil : '' }}</pre>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    @endif
                @endforeach
                <table style="width: 100%; text-align:center">
                    <tr>
                        <td style="width: 70%; border: 0px solid black"></td>
                        <td style="width: 30%; border: 0px solid black">Dokter Radiologi</td>
                    </tr>
                    <tr>
                        @php
                            $qr_dokter =
                                'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara elektronik oleh' .
                                "\n" .
                                $dokterRadiologiRanap[$urutan]->nm_dokter .
                                "\n" .
                                'ID ' .
                                $dokterRadiologiRanap[$urutan]->kd_dokter .
                                "\n" .
                                \Carbon\Carbon::parse($dokterRadiologiRanap[$urutan]->tgl_periksa)->format('d-m-Y');
                            $qrcode_dokter = base64_encode(
                                QrCode::format('png')->size(100)->errorCorrection('H')->generate($qr_dokter)
                            );
                        @endphp
                        <td style="width: 70%; border: 0px solid black"></td>
                        <td style="width: 30%; border: 0px solid black"><img
                                src="data:image/png;base64, {!! $qrcode_dokter !!}"></td>
                    </tr>
                    <tr>
                        <td style="width: 70%; border: 0px solid black"></td>
                        <td style="width: 30%; border: 0px solid black">
                            {{ $dokterRadiologiRanap[$urutan]->nm_dokter }}
                        </td>
                    </tr>
                </table>
            </div>
        @endforeach
    @endif

</body>

</html>
