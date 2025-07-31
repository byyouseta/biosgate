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

        .inline-field input[type="checkbox"],
        .inline-field label {
            display: inline-block; /* or display: inline; */
            vertical-align: middle; /* Helps with vertical alignment */
            margin-bottom: 0; /* Remove default margins that might cause wrapping */
        }

        /* If you nested the input inside the label: */
        label input[type="checkbox"] {
            display: inline-block;
            vertical-align: middle;
        }

        .checkbox-group-flex {
            /* Mengaktifkan Flexbox pada kontainer parent */
            display: flex;
            /* Ini adalah kunci! Menyelaraskan item di tengah secara vertikal */
            align-items: center;
            /* Memberikan sedikit jarak antara checkbox dan label */
            gap: 5px;
            /* Opsional: Sesuaikan margin bawah jika Anda memiliki banyak grup ini */
            margin-bottom: 10px;
        }

        .checkbox-group-flex input[type="checkbox"] {
            /* Mencegah checkbox mengecil jika ruang terbatas */
            flex-shrink: 0;
            /* Pastikan checkbox memiliki ukuran yang konsisten */
            width: 15px; /* Sesuaikan sesuai kebutuhan Anda */
            height: 15px; /* Sesuaikan sesuai kebutuhan Anda */
            /* Pastikan tidak ada margin default yang mengganggu */
            margin: 0;
            padding: 0;
        }

        .checkbox-group-flex label {
            /* Membiarkan label menggunakan sisa ruang yang tersedia */
            flex-grow: 1;
            /* Pastikan tidak ada margin default yang mengganggu */
            margin: 0;
            padding: 0;
            /* Jika Anda ingin teks label rata kiri di awal */
            text-align: left;
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
        <div style="float: none;">
            <div style="page-break-after: always;"></div>
        </div>
    @endif
    {{-- DATA SEP --}}
    @if (!empty($dataSep))
        {{-- Data SEP lokal --}}
        @if (!empty($dataSep->no_sep))
            <div>
                <table style="margin-top: 15pt;">
                    <tr>
                        <td style="width:25%; border:0pt solid black; vertical-align: top; padding-top:5pt"
                            rowspan="2"><img src="{{ asset('image/logoBPJS.png') }}" alt="Logo BPJS" width="250"
                                style="border:0pt solid black; vertical-align: top">
                        </td>
                        <td style=" border:0pt solid black; width:40%">
                            <div
                                style="padding-top: 0pt; padding-bottom:0pt; vertical-align:bottom; margin-top:0pt; margin-left:5pt; font-size:14pt">
                                SURAT ELIGIBILITAS PESERTA</div>
                        </td>
                        <td style=" border:0pt solid black; width:35%; vertical-align:top;" rowspan="3"
                            >
                            <div style="font-size:12pt; margin-left:5pt">{{ $dataSep->prb }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td style=" border:0pt solid black;">
                            <div
                                style="padding-top: 2pt; padding-bottom:0pt; vertical-align:top;margin-left:5pt; font-size:12pt">
                                RSUP SURAKARTA</div>
                        </td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="width:15%">No. SEP</td>
                        <td style="width:40%">: {{ $dataSep->no_sep }}</td>
                        <td class="pt-0 pb-0 text-center" colspan="2"></td>
                        {{-- <td class="pt-0 pb-0"></td> --}}
                    </tr>
                    <tr>
                        <td>Tgl. SEP</td>
                        <td>: {{ $dataSep->tglsep }}</td>
                        <td style="width:15%">Peserta</td>
                        <td style="width:30%">: {{ $dataSep->peserta }}</td>
                    </tr>
                    <tr>
                        <td>No. Kartu</td>
                        <td>: {{ $dataSep->no_kartu }} (MR :
                            {{ $dataSep->nomr }})
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Nama Peserta</td>
                        <td>: {{ $dataSep->nama_pasien }}</td>
                        <td>Jns. Rawat</td>
                        <td>:
                            @if ($dataSep->jnspelayanan == '1')
                                Rawat Inap
                            @else
                                Rawat Jalan
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top">Tgl. Lahir</td>
                        <td style="vertical-align: top">
                            <table>
                                <tr>
                                    <td>
                                        :
                                        {{ \Carbon\Carbon::parse($dataSep->tanggal_lahir)->format('Y-m-d') }}
                                    </td>
                                    <td>
                                        <div style="margin-left:15pt">Kelamin :
                                            @if ($dataSep->jkel == 'L')
                                                Laki-laki
                                            @else
                                                Perempuan
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td>Jns. Kunjungan</td>
                        <td>:
                            @if ($dataSep->tujuankunjungan == '0')
                                - Konsultasi dokter(pertama)
                            @elseif ($dataSep->tujuankunjungan == '2')
                                - Kunjungan Kontrol(ulangan)
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>No. Telepon</td>
                        <td>: {{ $dataSep->notelep }}</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Sub/Spesialis</td>
                        <td>: {{ $dataSep->nmpolitujuan }}</td>
                        <td>Poli Perujuk</td>
                        <td>: </td>
                    </tr>
                    <tr>
                        <td>Dokter</td>
                        <td>: {{ $dataSep->nmdpjplayanan }}</td>
                        <td>Kls. Hak</td>
                        <td>: Kelas {{ $dataSep->klsrawat }}</td>
                    </tr>
                    <tr>
                        <td>Faskes Perujuk</td>
                        <td>: {{ $dataSep->nmppkrujukan }}</td>
                        <td>Kls. Rawat</td>
                        <td>: </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;">Diagnosa Awal</td>
                        <td>: {{ $dataSep->nmdiagnosaawal }}</td>
                        <td style="vertical-align:top;">Penjamin</td>
                        <td>{{ $dataSep->pembiayaan }}</td>
                    </tr>
                    <tr>
                        <td>Catatan</td>
                        <td>: {{ $dataSep->catatan }}</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="3"><small><i>
                                    *Saya menyetujui BPJS Kesehatan
                                    untuk:
                                    <ol type="a" style="margin:-5px 50px -5px -25px">
                                        <li>membuka dan atau
                                            menggunakan informasi medis
                                            Pasien untuk
                                            keperluan administrasi, pembayaran asuransi atau jaminan
                                            pembiayaan kesehatan
                                        <li>memberikan akses informasi
                                            medis atau riwayat
                                            pelayanan kepada
                                            dokter/tenaga medis pada RSUP Surakarta untuk
                                            kepentingan
                                            pemeliharaan kesehatan, pengobatan, penyembuhan, dan
                                            perawatan
                                            Pasien
                                    </ol>
                                    *Saya mengetahui dan memahami:
                                    <ol type="a" style="margin:-5px 50px -5px -25px">
                                        <li>Rumah Sakit dapat melakukan
                                            koordinasi dengan PT Jasa Raharja/PT
                                            Taspen/PT ASABRI/BPJS Ketenagakerjaan atau Penjamin
                                            lainnya.
                                            jika Peserta merupakan pasien yang mengalami kecelakaan
                                            lalulintas dan / atau kecelakaan kerja
                                        <li>SEP bukan sebagai bukti
                                            penjaminan peserta
                                    </ol>
                                    *SEP bukan sebagai bukti penjaminan peserta<br>
                                    ** Dengan tampilnya luaran SEP elektronik
                                    ini merupakan hasil validasi terhadap eligibilitas Pasien secara
                                    elektronik(validasi finger print atau biometrik /sistem validasi
                                    lain)
                                    dan selanjutnya Pasien dapat mengakses pelayanan kesehatan
                                    rujukan
                                    sesuai ketentuan berlaku. Kebenaran dan keaslian atas informasi
                                    Pasien
                                    menjadi tanggung jawab penuh FKRTL
                                </i></small>
                        </td>
                        <td rowspan="3">
                            <div  style="margin-left:5pt;">Persetujuan</div>
                            <div  style="margin-left:5pt;">Pasien/Keluarga Pasien</div>
                            <div style="margin-left:5pt; margin-top:5pt">
                                @php
                                    $qrcode_pasien = base64_encode(
                                        QrCode::format('png')
                                            ->size(100)
                                            ->errorCorrection('H')
                                            ->generate($dataSep->no_kartu)
                                    );
                                @endphp
                                <img src="data:image/png;base64, {!! $qrcode_pasien !!}">
                            </div>
                            <div style="margin-left:5pt;">
                                <h4>{{ $dataSep->nama_pasien }}</h4>
                            </div>
                            <div style="text-align:right"><small>Cetakan ke 1
                                    {{ \Carbon\Carbon::now()->format('d/m/Y g:i:s A') }}
                                </small>
                            </div>

                        </td>
                    </tr>
                </table>
            </div>
        @elseif(!empty($dataSep->noSep))
            @php
                $peserta = \app\Http\Controllers\SepController::peserta($dataSep->peserta->noKartu, $dataSep->tglSep);
                $kontrol = \app\Http\Controllers\SepController::getSep2($dataSep->noSep);
            @endphp
            <div>
                <table style="margin-top: 15pt;">
                    <tr>
                        <td style="width:25%" rowspan="2"><img src="{{ asset('image/logoBPJS.png') }}"
                                alt="Logo BPJS" width="250"></td>
                        <td style=" border:0pt solid black; width:40%">
                            <div
                                style="padding-top: 0pt; padding-bottom:0pt; vertical-align:bottom; margin-top:0pt; margin-left:5pt; font-size:14pt">
                                SURAT ELIGIBILITAS PESERTA</div>
                        </td>
                        <td style=" border:0pt solid black; width:35%; vertical-align:top;" rowspan="3"
                            >
                            <div style="font-size:12pt; margin-left:5pt"></div>
                        </td>
                    </tr>
                    <tr>
                        <td style=" border:0pt solid black;">
                            <div
                                style="padding-top: 2pt; padding-bottom:0pt; vertical-align:top;margin-left:5pt; font-size:12pt">
                                RSUP SURAKARTA</div>
                        </td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="width:15%">No. SEP</td>
                        <td style="width:40%">: {{ $dataSep->noSep }}</td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td>Tgl. SEP</td>
                        <td>: {{ $dataSep->tglSep }}</td>
                        <td style="width:15%">Peserta</td>
                        <td style="width:30%">:
                            {{ $dataSep->peserta->jnsPeserta }}
                        </td>
                    </tr>
                    <tr>
                        <td>No. Kartu</td>
                        <td>: {{ $dataSep->peserta->noKartu }} (MR :
                            {{ $dataSep->peserta->noMr }})</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Nama Peserta</td>
                        <td>: {{ $dataSep->peserta->nama }}</td>
                        <td>Jns. Rawat</td>
                        <td>: {{ $dataSep->jnsPelayanan }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top">Tgl. Lahir</td>
                        <td>
                            <table>
                                <tr>
                                    <td>
                                        <div>
                                            : {{ $dataSep->peserta->tglLahir }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="margin-left: 15pt">Kelamin :
                                            @if ($dataSep->peserta->kelamin == 'L')
                                                Laki-laki
                                            @else
                                                Perempuan
                                            @endif
                                        </div>
                                    </td>

                                </tr>
                            </table>
                        </td>
                        <td >Jns. Kunjungan</td>
                        <td >:
                            @if ($dataSep->tujuanKunj->kode == '0')
                                - Konsultasi dokter(pertama)
                            @elseif ($dataSep->tujuanKunj->kode == '2')
                                - Kunjungan Kontrol(ulangan)
                            @endif

                        </td>
                    </tr>
                    <tr>
                        <td >No. Telepon</td>
                        <td >: {{ $peserta->mr->noTelepon }}</td>
                        <td ></td>
                        <td >
                            @if ($dataSep->flagProcedure->nama != null)
                                : - {{ $dataSep->flagProcedure->nama }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td >Sub/Spesialis</td>
                        <td >: {{ $dataSep->poli }}</td>
                        <td >Poli Perujuk</td>
                        <td >: </td>
                    </tr>
                    <tr>
                        <td >Dokter</td>
                        <td >: {{ $dataSep->dpjp->nmDPJP }}</td>
                        <td >Kls. Hak</td>
                        <td >: Kelas {{ $dataSep->kelasRawat }}</td>
                    </tr>
                    <tr>
                        <td >Faskes Perujuk</td>
                        <td >: {{ $kontrol->provPerujuk->nmProviderPerujuk }}
                        </td>
                        <td>Kls. Rawat</td>
                        <td>: </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top">Diagnosa Awal</td>
                        <td>: {{ $kontrol->diagnosa }}</td>
                        <td style="vertical-align: top">Penjamin</td>
                        <td>{{ $dataSep->penjamin }}</td>
                    </tr>
                    <tr>
                        <td>Catatan</td>
                        <td>: {{ $dataSep->catatan }}</td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td colspan="3"><small><i>
                                    *Saya menyetujui BPJS Kesehatan
                                    untuk:
                                    <ol type="a" style="margin:-5px 50px -5px -25px">
                                        <li>membuka dan atau
                                            menggunakan informasi medis
                                            Pasien untuk
                                            keperluan administrasi, pembayaran asuransi atau jaminan
                                            pembiayaan kesehatan
                                        <li>memberikan akses informasi
                                            medis atau riwayat
                                            pelayanan kepada
                                            dokter/tenaga medis pada RSUP Surakarta untuk
                                            kepentingan
                                            pemeliharaan kesehatan, pengobatan, penyembuhan, dan
                                            perawatan
                                            Pasien
                                    </ol>
                                    *Saya mengetahui dan memahami:
                                    <ol type="a" style="margin:-5px 50px -5px -25px">
                                        <li>Rumah Sakit dapat melakukan
                                            koordinasi dengan PT Jasa Raharja/PT
                                            Taspen/PT ASABRI/BPJS Ketenagakerjaan atau Penjamin
                                            lainnya.
                                            jika Peserta merupakan pasien yang mengalami kecelakaan
                                            lalulintas dan / atau kecelakaan kerja
                                        <li>SEP bukan sebagai bukti
                                            penjaminan peserta
                                    </ol>
                                    *SEP bukan sebagai bukti penjaminan peserta<br>
                                    ** Dengan tampilnya luaran SEP elektronik
                                    ini merupakan hasil validasi terhadap eligibilitas Pasien secara
                                    elektronik(validasi finger print atau biometrik /sistem validasi
                                    lain)
                                    dan selanjutnya Pasien dapat mengakses pelayanan kesehatan
                                    rujukan
                                    sesuai ketentuan berlaku. Kebenaran dan keaslian atas informasi
                                    Pasien
                                    menjadi tanggung jawab penuh FKRTL
                                </i></small>
                        </td>
                        <td rowspan="3">
                            <div style="margin-left:5pt;">Persetujuan</div>
                            <div style="margin-left:5pt;">Pasien/Keluarga Pasien</div>
                            <div style="margin-left:5pt; margin-top:5pt">
                                @php
                                    $qrcode_pasien = base64_encode(
                                        QrCode::format('png')
                                            ->size(100)
                                            ->errorCorrection('H')
                                            ->generate($dataSep->peserta->noKartu)
                                    );
                                @endphp
                                <img src="data:image/png;base64, {!! $qrcode_pasien !!}">
                            </div>
                            <div style="margin-left:5pt;">
                                <h4>{{ $dataSep->peserta->nama }}</h4>
                            </div>
                            <div style="text-align:right"><small>Cetakan ke 1
                                    {{ \Carbon\Carbon::now()->format('d/m/Y g:i:s A') }}
                                </small>
                            </div>

                        </td>
                    </tr>
                </table>
            </div>
        @endif
        <div style="float: none;">
            <div style="page-break-after: always;"></div>
        </div>
    @endif
    {{-- Data Eklaim --}}
    @if (!empty($dataKlaim))
        <div>
            <table style="width: 100%">
                <tr>
                    <td style="width: 5%; border-bottom: 3px solid black; " rowspan="3">
                        <img src="{{ asset('image/LogoKemenkesIcon.png') }}" alt="Logo Kemenkes" width="80" />
                    </td>
                    <td rowspan="3" style="border-bottom: 3px solid black; width: 90%;">
                        <b>KEMENTERIAN KESEHATAN REPUBLIK INDONESIA</b><br>
                        <i>Berkas Klaim Individual Pasien</i>
                    </td>
                    <td rowspan="3"
                        style="border-bottom: 3px solid black; width: 5%;">
                        JKN<br>
                        {{ $dataKlaim->tgl_pulang }}
                    </td>
                </tr>
            </table>
            <table style="width: 100%">
                <tr>
                    <td
                        style="width: 20%; padding-top: 10px; padding-left:5px; padding-bottom:0px; vertical-align:top;">
                        Kode
                        Rumah Sakit</td>
                    <td style="width: 30%;padding: 0;padding-top: 10px;vertical-align:top;">:
                        {{ $dataKlaim->kode_rs }}
                    </td>
                    <td style="width: 20%;padding: 0;padding-top: 10px;vertical-align:top;">Kelas Rumah Sakit </td>
                    <td style="width: 30%;padding: 0;padding-top: 10px;vertical-align:top;">:
                        {{ $dataKlaim->kelas_rs }}
                    </td>
                </tr>
                <tr>
                    <td
                        style="width: 20%;padding: 0; padding-left:5px; padding-bottom:10px;border-bottom: 1px solid black; vertical-align:top;">
                        Nama RS</td>
                    <td
                        style="width: 30%;padding: 0; padding-bottom:10px;border-bottom: 1px solid black; vertical-align:top;">
                        : RSU PUSAT
                        SURAKARTA</td>
                    <td
                        style="width: 20%;padding: 0; padding-bottom:10px;border-bottom: 1px solid black; vertical-align:top;">
                        Jenis Tarif
                    </td>
                    <td
                        style="width: 30%;padding: 0; padding-bottom:10px;border-bottom: 1px solid black; vertical-align:top;">
                        :
                        {{ $dataKlaim->kode_tarif == 'CP' ? 'TARIF RS KELAS C PEMERINTAH' : $dataKlaim->kode_tarif }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 20%;padding: 0; padding-left:5px;vertical-align:top;">Nomor Peserta</td>
                    <td style="width: 30%;padding: 0;vertical-align:top;">: {{ $dataKlaim->nomor_kartu }}</td>
                    <td style="width: 20%;padding: 0;vertical-align:top;">Nomor SEP</td>
                    <td style="width: 30%;padding: 0;vertical-align:top;">: {{ $dataKlaim->nomor_sep }}</td>
                </tr>
                <tr>
                    <td style="width: 20%;padding: 0; padding-left:5px;vertical-align:top;">Nomor Rekam Medis</td>
                    <td style="width: 30%;padding: 0;vertical-align:top;">: {{ $dataKlaim->nomor_rm }}</td>
                    <td style="width: 20%;padding: 0;vertical-align:top;">Tanggal Masuk</td>
                    <td style="width: 30%;padding: 0;vertical-align:top;">: {{ $dataKlaim->tgl_masuk }}</td>
                </tr>
                <tr>
                    <td style="width: 20%;padding: 0; padding-left:5px;vertical-align:top;">Umur Tahun</td>
                    <td style="width: 30%;padding: 0;vertical-align:top;">: {{ $dataKlaim->umur_tahun }}</td>
                    <td style="width: 20%;padding: 0;vertical-align:top;">Tanggal Keluar</td>
                    <td style="width: 30%;padding: 0;vertical-align:top;">: {{ $dataKlaim->tgl_pulang }}</td>
                </tr>
                <tr>
                    <td style="width: 20%;padding: 0; padding-left:5px;vertical-align:top;">Umur Hari</td>
                    <td style="width: 30%;padding: 0;vertical-align:top;">: {{ $dataKlaim->umur_hari }}</td>
                    <td style="width: 20%;padding: 0;vertical-align:top;">Jenis Perawatan</td>
                    <td style="width: 30%;padding: 0;vertical-align:top;">:
                        {{ $dataKlaim->jenis_rawat == '1' ? '1 - Rawat Inap' : '2 - Rawat Jalan' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 20%;padding: 0; padding-left:5px;vertical-align:top;">Tanggal Lahir</td>
                    <td style="width: 30%;padding: 0;vertical-align:top;">: {{ $dataKlaim->tgl_lahir }}</td>
                    <td style="width: 20%;padding: 0;vertical-align:top;">Cara Pulang</td>
                    <td style="width: 30%;padding: 0;vertical-align:top;">:
                        {{ $dataKlaim->discharge_status == '1' ? '1 - Atas Persetujuan Dokter' : $dataKlaim->discharge_status }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 20%;padding: 0; padding-left:5px;vertical-align:top;">Jenis Kelamin</td>
                    <td style="width: 30%;padding: 0;vertical-align:top;">:
                        {{ $dataKlaim->gender == '1' ? '1 - Laki-laki' : '2 - Perempuan' }}</td>
                    <td style="width: 20%;padding: 0;vertical-align:top;">LOS</td>
                    <td style="width: 30%;padding: 0;vertical-align:top;">: {{ $dataKlaim->los }} hari</td>
                </tr>
                <tr>
                    <td
                        style="width: 20%;padding: 0; padding-left:5px; padding-bottom:10px; border-bottom: 1px solid black;vertical-align:top;">
                        Kelas Perawatan</td>
                    <td
                        style="width: 30%;padding: 0; padding-bottom:10px; border-bottom: 1px solid black;vertical-align:top;">
                        : {{ $dataKlaim->kelas_rawat }} - Kelas {{ $dataKlaim->kelas_rawat }}</td>
                    <td
                        style="width: 20%;padding: 0; padding-bottom:10px; border-bottom: 1px solid black;vertical-align:top;">
                        Berat Lahir</td>
                    <td
                        style="width: 30%;padding: 0; padding-bottom:10px; border-bottom: 1px solid black;vertical-align:top;">
                        :
                        {{ $dataKlaim->berat_lahir == '0' ? '-' : $dataKlaim->berat_lahir }}</td>
                </tr>
            </table>
            <table style="width: 100%">
                @php
                    $diagnosa = explode('#', $dataKlaim->diagnosa_inagrouper);
                    $procedure = explode('#', $dataKlaim->procedure_inagrouper);
                @endphp
                <tr>
                    <td
                        style="width: 20%; padding-top: 10px; padding-left:5px; padding-bottom:0px;vertical-align:top;">
                        Diagnosa Utama</td>
                    <td style="width: 5%;padding: 0;padding-top: 10px;vertical-align:top;">: {{ $diagnosa[0] }}
                    </td>
                    <td style="width: 75%;padding: 0;padding-top: 10px;vertical-align:top;" colspan=2>
                        {{ \App\Penyakit::getName($diagnosa[0]) }}</td>
                </tr>
                <tr>
                    <td style="width: 20%;  padding-left:5px; padding-bottom:0px;vertical-align:top;">
                        Diagnosa Sekunder</td>
                    @for ($i = 1; $i < count($diagnosa); $i++)
                        <td style="width: 10%;padding: 0;vertical-align:top;">: {{ $diagnosa[$i] }}
                        </td>
                        <td style="width: 70%;padding: 0;vertical-align:top;" colspan=2>
                            {{ \App\Penyakit::getName($diagnosa[$i]) }}</td>
                </tr>
                <tr>
                    <td style="width: 20%;  padding-left:5px; padding-bottom:0px;vertical-align:top;">
                    </td>
    @endfor
    <td colspan=2></td>
    @for ($j = 0; $j < count($procedure); $j++)
        <tr>
            <td style="width: 20%; ; padding-left:5px; padding-bottom:0px;vertical-align:top;">
                {{ $j == 0 ? 'Prosedur' : '' }}</td>
            <td style="width: 10%;padding: 0;vertical-align:top;">: {{ $procedure[$j] }}
            </td>
            <td style="width: 70%;padding: 0;vertical-align:top;" colspan=2>
                {{ \App\Penyakit::getProcedure($procedure[$j]) }}</td>
        </tr>
    @endfor
    </table>
    <table style="width: 100%">
        <tr>
            <td style="width: 20%;padding: 0; padding-left:5px; padding-top:20px; vertical-align:top;">
                ADL Sub Acute</td>
            <td style="width: 30%;padding: 0; padding-top:20px; vertical-align:top;">
                : {{ $dataKlaim->adl_sub_acute == 0 ? '-' : $dataKlaim->adl_sub_acute }} </td>
            <td style="width: 20%;padding: 0; padding-top:20px; vertical-align:top;">
                ADL Chronic</td>
            <td style="width: 30%;padding: 0; padding-top:20px; vertical-align:top;">
                :
                {{ $dataKlaim->adl_chronic == '0' ? '-' : $dataKlaim->adl_chronic }}</td>
        </tr>
    </table>
    <table style="width: 100%; padding-top:20px;">
        <tr>
            <td colspan=5 style='font-weight: bold; padding-left:5px; border-bottom: 1px solid black'>Hasil Grouping
            </td>
        </tr>
        <tr>
            <td style="width: 20%;padding: 0; padding-left:5px; padding-top:10px; vertical-align:top;">
                INA-CBG</td>
            <td style="width: 20%;padding: 0; padding-top:10px; vertical-align:top;">
                :
                {{ $dataKlaim->grouper->response ? $dataKlaim->grouper->response->cbg->code : '' }}
            </td>
            <td style="width: 50%;padding: 0; padding-top:10px; vertical-align:top;">
                {{ $dataKlaim->grouper->response ? $dataKlaim->grouper->response->cbg->description : '' }}
            </td>
            <td style="width: 10%;padding: 0; padding-top:10px; text-align:right; vertical-align:top;">
                Rp</td>
            <td style="width: 10%;padding: 0; padding-top:10px; text-align:right; vertical-align:top;">
                {{ isset($dataKlaim->grouper->response->cbg->tariff) ? number_format($dataKlaim->grouper->response->cbg->tariff, 2, ',', '.') : number_format(0, 2, ',', '.') }}
            </td>

        </tr>
        <tr>
            <td style="width: 15%;padding: 0; padding-left:5px; vertical-align:top;">
                Sub Acute</td>
            <td style="width: 15%;padding: 0; vertical-align:top;">
                : - </td>
            <td style="width: 50%;padding: 0; vertical-align:top;">
                -</td>
            <td style="width: 10%;padding: 0; text-align:right;vertical-align:top;">
                Rp</td>
            <td style="width: 10%;padding: 0; text-align:right;vertical-align:top;">
                {{ number_format(0, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="width: 15%;padding: 0; padding-left:5px;  vertical-align:top;">
                Chronic</td>
            <td style="width: 15%;padding: 0;  vertical-align:top;">
                : - </td>
            <td style="width: 50%;padding: 0;  vertical-align:top;">
                -</td>
            <td style="width: 10%;padding: 0; text-align:right;  vertical-align:top;">
                Rp</td>
            <td style="width: 10%;padding: 0; text-align:right;  vertical-align:top;">
                {{ number_format(0, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td
                style="width: 15%;padding: 0; padding-left:5px; padding-bottom:10px; border-bottom: 1px solid black; vertical-align:top;">
                Special CMG</td>
            <td style="width: 15%;padding: 0; padding-bottom:10px; border-bottom: 1px solid black;vertical-align:top;">
                : - </td>
            <td
                style="width: 50%;padding: 0; padding-bottom:10px; border-bottom: 1px solid black; vertical-align:top;">
                -</td>
            <td
                style="width: 10%;padding: 0; padding-bottom:10px; border-bottom: 1px solid black; text-align:right;vertical-align:top;">
                Rp</td>
            <td
                style="width: 10%;padding: 0; padding-bottom:10px; border-bottom: 1px solid black; text-align:right;vertical-align:top;">
                {{ number_format(0, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td
                style="width: 15%;padding: 0; padding-left:5px; padding-top:10px; padding-bottom:50px; border-bottom: 3px solid black">
                Total Tarif</td>
            <td style="width: 15%;padding: 0; padding-top:10px; padding-bottom:50px; border-bottom: 3px solid black">
                : </td>
            <td style="width: 50%;padding: 0; padding-top:10px; padding-bottom:50px; border-bottom: 3px solid black">
            </td>
            <td
                style="width: 10%;padding: 0; padding-top:10px; text-align:right; padding-bottom:50px; border-bottom: 3px solid black">
                Rp</td>
            <td
                style="width: 10%;padding: 0; padding-top:10px; text-align:right; padding-bottom:50px; border-bottom: 3px solid black">
                {{ isset($dataKlaim->grouper->response->cbg->tariff) ? number_format($dataKlaim->grouper->response->cbg->tariff, 2, ',', '.') : number_format(0, 2, ',', '.') }}
            </td>

        </tr>
        <tr>
            <td style="width: 15%;padding: 0; padding-left:5px; font:grey">
                Generated</td>
            <td style="width: 75%;padding: 0;" colspan='3'>
                : Eklaim
                {{ $dataKlaim->grouper->response ? $dataKlaim->grouper->response->inacbg_version : '' }}
                @
                {{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}</td>
            <td style="width: 10%;padding: 0;; text-align:right">
                Lembar 1 / 1</td>
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
    @if ($spri)
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
    @endif
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
    @if ($tambahanDataRadiologi)
        @foreach ($tambahanDataRadiologi as $urutan => $orderRadio)
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
                                {{ $tambahanDokterRadiologi[$urutan]->nm_dokter }}</td>
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
                                {{ $tambahanDokterRadiologi[$urutan]->jam }}
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
                            <td style="border: 0px solid black;">: {{ $tambahanDokterRadiologi[$urutan]->nm_perawatan }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border: 0px solid black;" colspan="4">Hasil Pemeriksaan</td>
                        </tr>
                    </tbody>
                </table>
                @php
                    if ($tambahanHasilRadiologi) {
                        $paragraphs = explode("\n", $tambahanHasilRadiologi[$urutan]->hasil);
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
                        ">{{ $tambahanHasilRadiologi != null ? $tambahanHasilRadiologi[$urutan]->hasil : '' }}</textarea>
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
                                $tambahanDokterRadiologi[$urutan]->nm_dokter .
                                "\n" .
                                'ID ' .
                                $tambahanDokterRadiologi[$urutan]->kd_dokter .
                                "\n" .
                                \Carbon\Carbon::parse($tambahanDokterRadiologi[$urutan]->tgl_periksa)->format('d-m-Y');
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
                            {{ $tambahanDokterRadiologi[$urutan]->nm_dokter }}
                        </td>
                    </tr>
                </table>
            </div>
        @endforeach
    @endif


    {{-- Lembar selanjutnya Triase IGD --}}
    @if (!empty($dataTriase) && $dataTriase)
        <div style="float: none;">
            <div style="page-break-after: always;"></div>
        </div>
        <div class="watermark">
            {{ $watermark }}
        </div>
        <div>
            @php
                for ($i = 1; $i <= 5; $i++) {
                    foreach ($skala[$i] as $dataPemeriksaan) {
                        if ($dataPemeriksaan->nama_pemeriksaan == 'ASSESMENT TRIASE') {
                            $urgensi = $dataPemeriksaan->pengkajian_skala;
                        }
                    }
                }
                if (!empty($primer)) {
                    $plan = $primer->plan;
                } elseif (!empty($sekunder)) {
                    $plan = $sekunder->plan;
                } else {
                    $plan = null;
                }

                if ($plan == 'Zona Hijau') {
                    $bg_color = 'background-color:green;';
                } elseif ($plan == 'Zona Kuning') {
                    $bg_color = 'background-color:yellow;';
                } elseif ($plan == 'Zona Merah') {
                    $bg_color = 'background-color:red;';
                } else {
                    $bg_color = '';
                }
            @endphp

            <table style="width: 100%; border:0px solid black">
                <thead>
                    <tr>
                        <td style="width: 15%; border:0px solid black"></td>
                        <td style="width: 10%; border:0px solid black"></td>
                        <td style="width: 10%; border:0px solid black"></td>
                        <td style="width: 10%; border:0px solid black"></td>
                        <td style="width: 10%; border:0px solid black"></td>
                        <td style="width: 10%; border:0px solid black"></td>
                        <td style="width: 10%; border:0px solid black"></td>
                        <td style="width: 5%; border:0px solid black"></td>
                        <td style="width: 10%; border:0px solid black"></td>
                        <td style="width: 10%; border:0px solid black"></td>
                    </tr>
                    <tr>
                        <td style="border:1px solid black" rowspan="4"><img
                                src="{{ public_path('image/logorsup.jpg') }}" alt="Logo RSUP" width="100">
                        </td>
                        <td style="border-top:1px solid black" colspan="5">
                            <div style="font-size: 25px; text-align:center">RSUP SURAKARTA</div>
                        </td>
                        <td style="border-left:1px solid black;border-top:1px solid black; vertical-align:top"
                            colspan="2">No.RM / NIK </td>
                        <td style="border-right:1px solid black;border-top:1px solid black; vertical-align:top"
                            colspan="2">:
                            {{ $dataTriase->no_rkm_medis }} /
                            {{ $dataTriase->no_ktp }} </td>
                    </tr>
                    <tr>
                        <td style="border:0px solid black; text-align:center" colspan="5">Jl.Prof.Dr.R.Soeharso
                            No.28 , Surakarta,
                            Jawa Tengah</td>
                        <td style="border-left:1px solid black; vertical-align:top" colspan="2">Nama</td>
                        <td style="border-right:1px solid black; vertical-align:top" colspan="2">:
                            {{ $dataTriase->nm_pasien }}
                            ({{ $dataTriase->jk }}) </td>
                    </tr>
                    <tr>
                        <td style="border:0px solid black; text-align:center" colspan="5">Telp.0271-713055 / 720002
                        </td>
                        <td style="border-left:1px solid black; vertical-align:top" colspan="2">Tanggal Lahir</td>
                        <td style="border-right:1px solid black; vertical-align:top" colspan="2">:
                            {{ \Carbon\Carbon::parse($dataTriase->tgl_lahir)->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom:1px solid black; text-align:center" colspan="5">E-mail :
                            rsupsurakarta@kemkes.go.id</td>
                        <td style="border-left:1px solid black; border-bottom:1px solid black; vertical-align:top"
                            colspan="2" rowspan="2">
                            Alamat
                        </td>
                        <td style="border-right:1px solid black; border-bottom:1px solid black;vertical-align:top"
                            colspan="2" rowspan="2">:
                            {{ $dataTriase->alamat }}</td>
                    </tr>
                    <tr>
                        <td style="border:1px solid black; text-align:center; {{ $bg_color }}" colspan="6">
                            TRIASE PASIEN GAWAT DARURAT
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center; border:1px solid black;" colspan="10">
                            Triase dilakukan segera setelah pasien datang dan sebelum pasien/ keluarga
                            mendaftar
                            di TPP IGD
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border: 1px solid black" colspan="5">
                            Tanggal Kunjungan :
                            {{ \Carbon\Carbon::parse($dataTriase->tgl_kunjungan)->format('d-m-Y') }}
                        </td>
                        <td style="border: 1px solid black" colspan="5">
                            Pukul :
                            {{ \Carbon\Carbon::parse($dataTriase->tgl_kunjungan)->format('H:i:s') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black" colspan="3">
                            Cara Datang
                        </td>
                        <td style="border: 1px solid black" colspan="7">
                            {{ $dataTriase->cara_masuk }}
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black" colspan="3">
                            Macam Kasus
                        </td>
                        <td style="border: 1px solid black" colspan="7">
                            {{ $dataTriase->macam_kasus }}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center; background-color:lightskyblue; border:1px solid black"
                            colspan="3">
                            KETERANGAN
                        </td>
                        <td style="text-align: center; background-color:lightskyblue; border:1px solid black"
                            colspan="7">
                            {{ $primer != null ? 'TRIASE PRIMER' : 'TRIASE SEKUNDER' }}
                        </td>
                    </tr>
                    @if (!empty($primer))
                        <tr>
                            <td style="border: 1px solid black" colspan="3">
                                KELUHAN UTAMA
                            </td>
                            <td style="border: 1px solid black" colspan="7">
                                {{ $primer->keluhan_utama }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid black" colspan="3">
                                TANDA VITAL
                            </td>
                            <td style="border: 1px solid black" colspan="7">
                                Suhu (C) : {{ $dataTriase->suhu }}, Nyeri :
                                {{ $dataTriase->nyeri }},
                                Tensi :
                                {{ $dataTriase->tekanan_darah }}, Nadi(/menit) :
                                {{ $dataTriase->nadi }},
                                Saturasi
                                O2(%) : {{ $dataTriase->saturasi_o2 }}, Respirasi(/menit) :
                                {{ $dataTriase->pernapasan }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid black" colspan="3">
                                KEBUTUHAN KHUSUS
                            </td>
                            <td style="border: 1px solid black" colspan="7">
                                {{ $primer->kebutuhan_khusus }}
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td style="border: 1px solid black" colspan="3">
                                ANAMNESA SINGKAT
                            </td>
                            <td style="border: 1px solid black" colspan="7">
                                {{ !empty($sekunder) && $sekunder->anamnesa_singkat ? $sekunder->anamnesa_singkat:'' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid black; vertical-align:top" colspan="3">
                                TANDA VITAL
                            </td>
                            <td style="border: 1px solid black" colspan="7">
                                Suhu (C) : {{ $dataTriase->suhu }}, Nyeri :
                                {{ $dataTriase->nyeri }},
                                Tensi :
                                {{ $dataTriase->tekanan_darah }}, Nadi(/menit) :
                                {{ $dataTriase->nadi }},
                                Saturasi
                                O2(%) : {{ $dataTriase->saturasi_o2 }}, Respirasi(/menit) :
                                {{ $dataTriase->pernapasan }}
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td style="border: 1px solid black; background-color:lightskyblue; text-align:center"
                            colspan="3">
                            PEMERIKSAAN
                        </td>
                        <td style="border: 1px solid black; text-align:center;{{ $bg_color }} " colspan="7">
                            URGENSI
                            @php
                                $pemeriksaan = '';
                            @endphp
                            @for ($i = 1; $i <= 5; $i++)
                                @foreach ($skala[$i] as $dataPemeriksaan)
                                    @if ($dataPemeriksaan->nama_pemeriksaan != $pemeriksaan)
                                        @php
                                            $pemeriksaan = $dataPemeriksaan->nama_pemeriksaan;
                                        @endphp
                        </td>
                    </tr>
                    <tr>
                        <td style="border:1px solid black;" colspan="3">
                            {{ $dataPemeriksaan->nama_pemeriksaan }}
                        </td>
                        <td style="border:1px solid black;{{ $bg_color }}" colspan="7">
                            {{ $dataPemeriksaan->pengkajian_skala }}
                        @else
                            , {{ $dataPemeriksaan->pengkajian_skala }}
    @endif
    @endforeach
    @endfor
    </td>
    </tr>
    <tr>
        <td style="border:1px solid black" colspan="3">
            PLAN
        </td>
        <td style="border:1px solid black; {{ $bg_color }}" colspan="7">
            {{ !empty($primer) && !empty($primer->plan) ? $primer->plan : '' }}
            {{ !empty($sekunder) && $sekunder->plan != null ? $sekunder->plan : '' }}
        </td>
    </tr>
    <tr>
        <td style="border:1px solid black;" colspan="3">

        </td>
        <td style="border:1px solid black; background-color:lightskyblue" colspan="7">
            {{ $primer != null ? 'Petugas Triase Primer' : 'Petugas Triase Sekunder' }}
        </td>
    </tr>
    <tr>
        <td style="border:1px solid black;" colspan="3">
            Tanggal & Jam
        </td>
        <td style="border:1px solid black;" colspan="7">
            {{ $primer != null ? \Carbon\Carbon::parse($primer->tanggaltriase)->format('d-m-Y H:i:s') : '' }}
            {{ $sekunder != null ? \Carbon\Carbon::parse($sekunder->tanggaltriase)->format('d-m-Y H:i:s') : '' }}
        </td>
    </tr>
    <tr>
        <td style="border:1px solid black;" colspan="3">
            Catatan
        </td>
        <td style="border:1px solid black;" colspan="7">
            {{ $primer != null ? $primer->catatan : '' }}
            {{ $sekunder != null ? $sekunder->catatan : '' }}
        </td>
    </tr>
    <tr>
        <td style="border:1px solid black;vertical-align:top" colspan="3">
            Dokter/Petugas Jaga IGD
        </td>
        <td style="border:1px solid black;" colspan="7">
            @php
                if (!empty($primer)) {
                    $nip_petugas = $primer->nip;
                    $nama_petugas = $primer->nama;
                    $tanggal_hasil = \Carbon\Carbon::parse($primer->tanggaltriase)->format('d-m-Y');
                } elseif (!empty($sekunder)) {
                    $nip_petugas = $sekunder->nip;
                    $nama_petugas = $sekunder->nama;
                    $tanggal_hasil = \Carbon\Carbon::parse($sekunder->tanggaltriase)->format('d-m-Y');
                } else {
                    $nip_petugas = null;
                    $nama_petugas = null;
                    $tanggal_hasil = null;
                }
                $qr_petugas =
                    'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara elektronik oleh' .
                    "\n" .
                    $nama_petugas .
                    "\n" .
                    'ID ' .
                    $nip_petugas .
                    "\n" .
                    $tanggal_hasil;
                $qrcode_petugas = base64_encode(
                    QrCode::format('png')->size(100)->errorCorrection('H')->generate($qr_dokter)
                );
            @endphp
            <div>
                {{ $primer != null ? $primer->nama : '' }}
                {{ $sekunder != null ? $sekunder->nama : '' }}
                <div style="text-align: right; padding-right:10px; padding-bottom:10px;"><img
                        src="data:image/png;base64, {!! $qrcode_dokter !!}"></div>
            </div>
        </td>
    </tr>
    </tbody>
    </table>
    </div>
    @endif
    {{-- Lembar selanjutnya resume IGD --}}
    @if (!empty($resumeIgd) && $resumeIgd)
        <div style="float: none;">
            <div style="page-break-after: always;"></div>
        </div>
        <div class="watermark">
            {{ $watermark }}
        </div>
        <div>
            <table style="border: 0px solid black">
                <thead>
                    <tr>
                        <td style="width:3%"><img src="{{ public_path('image/logorsup.jpg') }}" alt="Logo RSUP"
                                width="30">
                        </td>
                        <td>
                            <h5>RSUP SURAKARTA</h5>
                        </td>
                    </tr>
                </thead>
            </table>
            <table style="width: 100%; border: 1px solid black">
                <thead>
                    <tr>
                        <th rowspan="5" style="width: 50%; border:1px solid black; text-align:center;">
                            <h4>RINGKASAN PASIEN<br> GAWAT DARURAT</h4>
                        </th>
                        <th style="width: 15%; border-left:1px solid black; border-top: 1px solid black;text-align:left;">
                            No.RM
                        </th>
                        <th style="border-right: 1px solid black; border-top: 1px solid black;text-align:left;">:
                            {{ $dataRingkasan->no_rkm_medis }}
                        </th>
                    </tr>
                    <tr>
                        <th style="border-left: 1px solid black; text-align:left;">NIK </th>
                        <th style="border-right: 1px solid black; text-align:left;">
                            : {{ $dataRingkasan->no_ktp }}
                        </th>
                    </tr>
                    <tr>
                        <th style="border-left: 1px solid black; text-align:left;">Nama Pasien </th>
                        <th style="border-right: 1px solid black; text-align:left;">
                            : {{ $dataRingkasan->nm_pasien }}
                        </th>
                    </tr>
                    <tr>
                        <th style="border-left: 1px solid black; text-align:left;">Tanggal Lahir </th>
                        <th style="border-right: 1px solid black; text-align:left;">
                            : {{ $dataRingkasan->tgl_lahir }}
                        </th>
                    </tr>
                    <tr>
                        <th
                            style="border-left: 1px solid black; border-bottom: 1px solid black;text-align:left; vertical-align:top">
                            Alamat</th>
                        <th style="border-right: 1px solid black;border-bottom: 1px solid black; text-align:left;">:
                            {{ $dataRingkasan->alamat }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border-left: 1px solid black; border-right: 1px solid black" colspan="3">
                            <b>Waktu
                                Kedatangan</b> Tanggal :
                            {{ \Carbon\Carbon::parse($dataRingkasan->tgl_registrasi)->format('d-m-Y') }} Jam :
                            {{ $dataRingkasan->jam_reg }}
                        </td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black; border-right: 1px solid black; " colspan="3">
                            <b>Diagnosis:</b>
                        </td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black; border-right: 1px solid black; padding-left:20px"
                            colspan="3">
                            {{ $dataRingkasan->diagnosis }}</td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black; border-right: 1px solid black" colspan="3">
                            <b>Kondisi Pada Saat Keluar:</b>
                        </td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black; border-right: 1px solid black; padding-left:20px"
                            colspan="3">
                            {{ $resumeIgd->kondisi_pulang }}</td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black; border-right: 1px solid black" colspan="3">
                            <b>Tindak
                                Lanjut:</b>
                        </td>
                    </tr>
                    <tr>
                        <td
                            style="border-left: 1px solid black; border-right: 1px solid black; padding-left:20px" colspan="3">
                            {{ $resumeIgd->tindak_lanjut }}</td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black; border-right: 1px solid black" colspan="3"><b>Obat
                                yang dibawa pulang:</b></td>
                    </tr>
                    @php
                        $obat = explode("\n", $resumeIgd->obat_pulang);
                    @endphp
                    @foreach ($obat as $obatPulang)
                        <tr>
                            <td style="border-left: 1px solid black; border-right: 1px solid black; padding-left:20px"
                                colspan="3">
                                {{ $obatPulang }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td style="border-left: 1px solid black; border-right: 1px solid black" colspan="3">
                            <b>Edukasi:</b>
                        </td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black; border-right: 1px solid black; padding-left:20px"
                            colspan="3">
                            {{ $resumeIgd->edukasi }}</td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black; border-right: 1px solid black" colspan="3">Waktu
                            Selesai Pelayanan IGD Tanggal:
                            {{ \Carbon\Carbon::parse($resumeIgd->tgl_selesai)->format('d-m-Y') }} Jam:
                            {{ \Carbon\Carbon::parse($resumeIgd->tgl_selesai)->format('H:i:s') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black; border-right: 1px solid black; padding-top:10px"
                            colspan="3">Tanda
                            Tangan Dokter</td>
                    </tr>
                    <tr>
                        @php
                            $qr_dokter =
                                'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara elektronik oleh' .
                                "\n" .
                                $dataRingkasan->nm_dokter .
                                "\n" .
                                'ID ' .
                                $dataRingkasan->kd_dokter .
                                "\n" .
                                \Carbon\Carbon::parse($resumeIgd->tgl_selesai)->format('d-m-Y');
                            $qrcode_dokter = base64_encode(
                                QrCode::format('png')->size(100)->errorCorrection('H')->generate($qr_dokter)
                            );
                        @endphp
                        <td style="border-left: 1px solid black; border-right: 1px solid black; padding-left:20px"
                            colspan="3">
                            <img src="data:image/png;base64, {!! $qrcode_dokter !!}">
                        </td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black"
                            colspan="3">Nama :
                            {{ $dataRingkasan->nm_dokter }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endif

    @if(!empty($dataRingkasan))
        <div style="float: none;">
            <div style="page-break-after: always;"></div>
        </div>
        <div class="watermark">
            {{ $watermark }}
        </div>
        <div>
            <table style="width: 100%; padding-bottom:5px;">
                <thead>
                    <tr>
                        <td style="width: 3%; padding-right: 0; vertical-align: middle;">
                            <img src="image/logorsup.jpg" alt="Logo RSUP" width="70" style="padding: 0;">
                        </td>
                        <td style="padding-top: 0; padding-bottom: 0; text-align: center; vertical-align: middle;" colspan="6">
                            <div style="font-size: 14pt; font-weight: bold;">RSUP SURAKARTA</div>
                            Jl.Prof.Dr.R.Soeharso No.28 , Surakarta, Jawa Tengah <br>
                            Telp.0271-713055 / 720002 <br>
                            E-mail : rsupsurakarta@kemkes.go.id
                        </td>
                        <td style="width: 3%; padding-right: 0; vertical-align: middle;">&nbsp;</td>
                    </tr>
                </thead>
            </table>
            <table style="width: 100%; table-layout: fixed; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align: center; border: 1px solid black;" colspan="6">
                            <div style="font-size:12pt;">PENILAIAN AWAL MEDIS GAWAT DARURAT</div>
                        </th>
                    </tr>
                    <tr>
                        <th style="border-left:1px solid black; border-right:0px solid black; width: 15%; text-align:left;">No. RM
                        </th>
                        <th style="border-left:0px solid black; border-right:0px solid black; text-align:left;">:
                            {{ $dataRingkasan->no_rkm_medis }}</th>
                        <th style="text-align:left;">Jenis Kelamin
                        </th>
                        <th style="text-align:left;">:
                            {{ $dataRingkasan->jk == 'L' ? 'Laki-laki':'Perempuan' }}</th>
                        <th style="width: 10%; text-align:left; border-left: 1px solid black">Tanggal
                        </th>
                        <th style="border-right: 1px solid black; text-align:left;">:
                            {{ $dataRingkasan->tanggal }}</th>
                    </tr>
                    <tr style="vertical-align: top;">
                        <th style="border-left:1px solid black; border-bottom:1px solid black; text-align:left;">Nama Pasien</th>
                        <th style="border-bottom: 1px solid black; text-align:left;">
                            : {{ $dataRingkasan->nm_pasien }}</th>
                        <th style="border-bottom: 1px solid black; text-align:left;">Tanggal Lahir</th>
                        <th style="border-bottom: 1px solid black; text-align:left;">
                            : {{ $dataRingkasan->tgl_lahir }}</th>
                        <th style="border-bottom: 1px solid black; border-left: 1px solid black; text-align:left;">Anamnesis</th>
                        <th style="border-bottom: 1px solid black; border-right: 1px solid black; text-align:left;">:
                            {{ $dataRingkasan->anamnesis }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border: 1px solid black; border-top: 0px solid black;" colspan="6">
                            <b>I. RIWAYAT KESEHATAN</b><br>
                            <p>Keluhan Utama :  {{ $dataRingkasan->keluhan_utama }} </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black; border-top: 0px solid black;" colspan="6">
                            <p>Riwayat Penyakit Sekarang :  {{ $dataRingkasan->rps }} </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black; border-top: 0px solid black;" colspan="3">
                            <p>Riwayat Penyakit Dahulu :  {{ $dataRingkasan->rpd }} </p>
                        </td>
                        <td style="border: 1px solid black; border-top: 0px solid black; vertical-align:top;" colspan="3">
                            <p>Riwayat Penyakit dalam Keluarga :  {{ $dataRingkasan->rpk }} </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black; border-top: 0px solid black;width: 50%;" colspan="3" >
                            <p>Riwayat Pengobatan :  {{ $dataRingkasan->rpo }} </p>
                        </td>
                        <td style="border: 1px solid black; border-top: 0px solid black;width: 50%;" colspan="3">
                            <p>Riwayat Alergi :  {{ $dataRingkasan->alergi }} </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black; border-top: 0px solid black;" colspan="6">
                            <b>II. PEMERIKSAAN FISIK </b>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black; border-top: 0px solid black; border-right: 0px solid black;" colspan="2">
                            Keadaan Umum : {{ $dataRingkasan->keadaan }}
                        </td>
                        <td style="border-bottom: 1px solid black;" colspan="2">
                            Kesadaran : {{ $dataRingkasan->kesadaran }}
                        </td>
                        <td style="border: 1px solid black; border-top: 0px solid black; border-left: 0px solid black;" colspan="2">
                            GCS(E,V,M) : {{ $dataRingkasan->gcs }}
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black; border-top: 0px solid black; text-align:center;" colspan="6">
                            Tanda Vital :&emsp;  TD: {{ $dataRingkasan->td }}&ensp;  N: {{ $dataRingkasan->nadi }}&ensp;  R: {{ $dataRingkasan->rr }}&ensp; S: {{ $dataRingkasan->suhu }}&ensp;  SPO2: {{ $dataRingkasan->spo }}&ensp;  BB: {{ $dataRingkasan->bb }}&ensp;  TB: {{ $dataRingkasan->tb }}
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black; border-top: 0px solid black;  border-bottom: 0px solid black;" >
                            Kepala
                        </td>
                        <td style="border: 1px solid black; border-top: 0px solid black; border-bottom: 0px solid black;" >
                            {{ $dataRingkasan->kepala }}
                        </td>
                        <td style="border: 1px solid black; border-top: 0px solid black; border-bottom: 0px solid black;" >
                            Thoraks
                        </td>
                        <td style="border: 1px solid black; border-top: 0px solid black; border-bottom: 0px solid black;" >
                            {{ $dataRingkasan->thoraks }}
                        </td>
                        <td style="border : 1px solid black; border-top: 0px solid black;" colspan="2" rowspan="4">
                            <pre style="white-space: pre-wrap; word-wrap: break-word; overflow: hidden; padding-left: 10pt;">{{ $dataRingkasan->ket_fisik }}</pre>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black; border-top: 0px solid black; border-bottom: 0px solid black;" >
                            Mata
                        </td>
                        <td style="border: 1px solid black; border-top: 0px solid black; border-bottom: 0px solid black;" >
                            {{ $dataRingkasan->mata }}
                        </td>
                        <td style="border: 1px solid black; border-top: 0px solid black; border-bottom: 0px solid black;" >
                            Abdomen
                        </td>
                        <td style="border: 1px solid black; border-top: 0px solid black; border-bottom: 0px solid black;" >
                            {{ $dataRingkasan->abdomen }}
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black; border-top: 0px solid black; border-bottom: 0px solid black;" >
                            Gigi dan Mulut
                        </td>
                        <td style="border: 1px solid black; border-top: 0px solid black; border-bottom: 0px solid black;" >
                            {{ $dataRingkasan->gigi }}
                        </td>
                        <td style="border: 1px solid black; border-top: 0px solid black; border-bottom: 0px solid black;" >
                            Genital & Anus
                        </td>
                        <td style="border: 1px solid black; border-top: 0px solid black; border-bottom: 0px solid black;" >
                            {{ $dataRingkasan->genital }}
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black; border-top: 0px solid black; border-bottom: 1px solid black;" >
                            Leher
                        </td>
                        <td style="border: 1px solid black; border-top: 0px solid black; border-bottom: 1px solid black;" >
                            {{ $dataRingkasan->leher }}
                        </td>
                        <td style="border: 1px solid black; border-top: 0px solid black; border-bottom: 1px solid black;" >
                            Ekstremitas
                        </td>
                        <td style="border: 1px solid black; border-top: 0px solid black; border-bottom: 1px solid black;" >
                            {{ $dataRingkasan->ekstremitas }}
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black; border-top: 0px solid black;" colspan="6">
                            <b>III. STATUS LOKALIS</b>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black; border-top: 0px solid black;" colspan="6">
                            <p>Keterangan : {{ $dataRingkasan->ket_lokalis }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black; border-top: 0px solid black;" colspan="6">
                            <b>IV. PEMERIKSAAN PENUNJANG</b>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black; border-top: 0px solid black;" colspan="2">
                            <p>EKG : {{ $dataRingkasan->ekg }}</p>
                        </td>
                        <td style="border: 1px solid black; border-top: 0px solid black;" colspan="2">
                            <p>Radiologi : {{ $dataRingkasan->rad }}</p>
                        </td>
                        <td style="border: 1px solid black; border-top: 0px solid black;" colspan="2">
                            <p>Laboratorium : {{ $dataRingkasan->lab }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black; border-top: 0px solid black;" colspan="6">
                            <b>V. DIAGNOSIS</b><br>
                            <p>{{ $dataRingkasan->diagnosis }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black; border-top: 0px solid black; vertical-align: top;" colspan="3">
                            <b>VI. TATA LAKSANA</b><br>
                            <pre style="white-space: pre-wrap; word-wrap: break-word; overflow: hidden; padding-left: 10pt;">{{ $dataRingkasan->tata }}</pre>
                        </td>
                        <td style="border: 1px solid black; border-top: 0px solid black; vertical-align:top;" colspan="3">
                            <b>VII. RINGKASAN PASIEN GAWAT DARURAT</b><br>
                            <p>Kondisi Pada Saat Keluar : {{ $resumeIgd && $resumeIgd->kondisi_pulang? $resumeIgd->kondisi_pulang:'-' }}</p>
                            <p>Tindak Lanjut : {{ $resumeIgd && $resumeIgd->tindak_lanjut? $resumeIgd->tindak_lanjut:'-' }}</p>
                            <p>Kebutuhan : {{ $resumeIgd && $resumeIgd->kebutuhan? $resumeIgd->kebutuhan:'-' }}</p>
                            <p>Edukasi : {{ $resumeIgd && $resumeIgd->edukasi ? $resumeIgd->edukasi:'-' }}</p>
                            <p>Obat Yang Dibawa Pulang : {{ $resumeIgd && $resumeIgd->obat_pulang ? $resumeIgd->obat_pulang:'-' }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black; border-top: 0px solid black; text-align: center;" colspan="3">Tanggal dan Jam</td>
                        <td style="border: 1px solid black; border-top: 0px solid black; text-align: center;" colspan="3">Nama Dokter dan Tanda
                            Tangan</td>
                    </tr>
                    <tr>
                        @php
                            $qr_dokter =
                                'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara
                            elektronik oleh' .
                                "\n" .
                                $dataRingkasan->nm_dokter .
                                "\n" .
                                'ID ' .
                                $dataRingkasan->kd_dokter .
                                "\n" .
                                \Carbon\Carbon::parse($dataRingkasan->tanggal)->format('d-m-Y');

                                $qrcode_dokter = base64_encode(
                                QrCode::format('png')->size(100)->errorCorrection('H')->generate($qr_dokter)
                                );


                        @endphp
                        <td style="border: 1px solid black; border-top: 0px solid black; text-align:center; vertical-align:middle;" colspan="3">{{ \Carbon\Carbon::parse($dataRingkasan->tanggal)->format('d-m-Y H:i:s') }} WIB</td>
                        <td style="padding: 5pt; border: 1px solid black; border-right: 0px solid black">
                            <img src="data:image/png;base64, {!! $qrcode_dokter !!}"> </td>
                        <td style="border: 1px solid black; border-left: 0px solid black; vertical-align:bottom;" colspan="2">
                            {{ $dataRingkasan->nm_dokter }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endif

    @if($skor_psi)
        <div style="float: none;">
            <div style="page-break-after: always;"></div>
        </div>
        <div class="watermark">
            {{ $watermark }}
        </div>
        <img src="{{ asset('image/kop.png') }}" alt="KOP RSUP">
        <hr class='new4' />
        <div class="row justify-content-center">
        <table style="width: 100%; margin-bottom:10px; ">
            <thead>
                <tr>
                    <th style="text-align: center;" colspan="4">
                        <div style="font-size: 14pt;">Pneumonia Saverity Index(PSI)</div>
                    </th>
                </tr>
                <tr>
                    <td style="width: 20%; padding-left: 25px;">No. Rekam Medis</td>
                    <td style="width: 30%; padding-left: 25px;">: {{ $pasien->no_rkm_medis }}</td>
                    <td style="width: 20%; padding-left: 25px;">JK</td>
                    <td style="width: 30%; padding-left: 25px;">: {{ $pasien->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 25px;">Nama Pasien</td>
                    <td style="padding-left: 25px;">: {{ $pasien->nm_pasien }}</td>
                    <td style="padding-left: 25px;">Tanggal Lahir</td>
                    <td style="padding-left: 25px;">: {{ $pasien->tgl_lahir }}</td>
                </tr>
                <tr>
                    <td style="border-bottom: 3px solid black; padding-left: 25px;">Umur</td>
                    <td style="border-bottom: 3px solid black; padding-left: 25px;">:
                        {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->diff(\Carbon\Carbon::parse($pasien->tgl_registrasi))->format('%y Th') }}
                    </td>
                    <td style="border-bottom: 3px solid black; padding-left: 25px;">Alamat</td>
                    <td style="border-bottom: 3px solid black; padding-left: 25px;">: {{ $pasien->alamat }}</td>
                </tr>
            </thead>
        </table>
            <div class="col-8">
                <table style="width: 100%; margin-bottom:25px;" cellspacing="0" cellpadding="5">
                    <tbody>
                        <tr>
                            <td style="border:1px solid black;font-weight:bold;text-align:center; width: 70%">Karakteristik pasien</td>
                            <td style="border:1px solid black;font-weight:bold;text-align:center; width: 20%">Nilai</td>
                            <td style="border:1px solid black;font-weight:bold;text-align:center; width: 10%">Skor PSI</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid black;font-weight:bold;">Faktor demografik</td>
                            <td style="border:1px solid black;"></td>
                            <td style="border:1px solid black;"></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid black; font-weight:bold;">Umur</td>
                            <td style="border:1px solid black;"></td>
                            <td style="border:1px solid black;"></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid black;">Laki-laki</td>
                            <td style="border:1px solid black;text-align: center">Umur(tahun)</td>
                            <td style="border:1px solid black;text-align: center">{{ $skor_psi->skor_usia }}</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid black;">Perempuan</td>
                            <td style="border:1px solid black;text-align: center">Umur(tahun)-10</td>
                            <td style="border:1px solid black;text-align: center">{{ $skor_psi->skor_jenis_kelamin }}</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid black;">Penghuni panti werda</td>
                            <td style="border:1px solid black;text-align: center">+ 10</td>
                            <td style="border:1px solid black;text-align: center">{{ $skor_psi->skor_panti_werda }}</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid black;font-weight:bold;text-align:center;">Penyakit komorbid</td>
                            <td style="border:1px solid black;"></td>
                            <td style="border:1px solid black;"></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid black;">Keganasan</td>
                            <td style="border:1px solid black;text-align: center">+ 30</td>
                            <td style="border:1px solid black;text-align: center">{{ $skor_psi->skor_keganasan }}</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid black;">Penyakit Hati</td>
                            <td style="border:1px solid black;text-align: center">+ 20</td>
                            <td style="border:1px solid black;text-align: center">{{ $skor_psi->skor_penyakit_hati }}</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid black;">Penyakit jantung kongestif</td>
                            <td style="border:1px solid black;text-align: center">+ 10</td>
                            <td style="border:1px solid black;text-align: center">{{ $skor_psi->skor_penyakit_jantung }}</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid black;">Penyakit serebro vaskular</td>
                            <td style="border:1px solid black;text-align: center">+ 10</td>
                            <td style="border:1px solid black;text-align: center">{{ $skor_psi->skor_penyakit_serebro }}</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid black;">Penyakit ginjal</td>
                            <td style="border:1px solid black;text-align: center">+ 10</td>
                            <td style="border:1px solid black;text-align: center">{{ $skor_psi->skor_penyakit_ginjal }}</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid black; font-weight:bold;text-align:center;">Pemeriksaan fisis</td>
                            <td style="border:1px solid black;"></td>
                            <td style="border:1px solid black;"></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid black;">Gangguan kesadaran </td>
                            <td style="border:1px solid black;text-align: center">+ 20</td>
                            <td style="border:1px solid black;text-align: center">{{ $skor_psi->skor_gangguan_kesadaran }}</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid black;">Frekuensi nafas > 30 x/menit </td>
                            <td style="border:1px solid black;text-align: center">+ 20</td>
                            <td style="border:1px solid black;text-align: center">{{ $skor_psi->skor_frekuensi_nafas }}</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid black;">Tekanan darah sistolik < 90 mmHg </td>
                            <td style="border:1px solid black;text-align: center">+ 20</td>
                            <td style="border:1px solid black;text-align: center">{{ $skor_psi->skor_sistolik }}</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid black;">Suhu tubuh < 30 &#8451; atau 40 &#8451;</td>
                            <td style="border:1px solid black;text-align: center">+ 15</td>
                            <td style="border:1px solid black;text-align: center">{{ $skor_psi->skor_suhu_tubuh }}</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid black;">Frekuensi nadi > 12 x/menit </td>
                            <td style="border:1px solid black;text-align: center">+ 10</td>
                            <td style="border:1px solid black;text-align: center">{{ $skor_psi->skor_nadi }}</td>
                        </tr>
                        <tr>
                            <td  style="border:1px solid black; font-weight:bold;text-align:center;">Hasil laboratorium</td>
                            <td style="border:1px solid black;"></td>
                            <td style="border:1px solid black;"></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid black;">pH < 7.35 </td>
                            <td style="border:1px solid black;text-align: center">+ 30</td>
                            <td style="border:1px solid black;text-align: center">{{ $skor_psi->skor_ph }}</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid black;">Ureum > 64.2 mg/dL </td>
                            <td style="border:1px solid black;text-align: center">+ 20</td>
                            <td style="border:1px solid black;text-align: center">{{ $skor_psi->skor_ureum }}</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid black;">Natrium < 130 mEq/dL </td>
                            <td style="border:1px solid black;text-align: center">+ 20</td>
                            <td style="border:1px solid black;text-align: center">{{ $skor_psi->skor_natrium }}</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid black;">Glukosa > 250 mg/dL</td>
                            <td style="border:1px solid black;text-align: center">+ 10</td>
                            <td style="border:1px solid black;text-align: center">{{ $skor_psi->skor_glukosa }}</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid black;">Hematokrit < 30&#37;</td>
                            <td style="border:1px solid black;text-align: center">+ 10</td>
                            <td style="border:1px solid black;text-align: center">{{ $skor_psi->skor_hematokrit }}</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid black;">Tekanan O<sub>2</sub> darah arteri < 60 mmHg</td>
                            <td style="border:1px solid black;text-align: center">+ 10</td>
                            <td style="border:1px solid black;text-align: center">{{ $skor_psi->skor_tekanan_o2 }}</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid black;">Efusi pleura</td>
                            <td style="border:1px solid black;; text-align: center">+ 10</td>
                            <td style="border:1px solid black;text-align: center">{{ $skor_psi->skor_efusi_pleura }}</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid black;font-weight:bold;text-align:center;" colspan="2">Total Skoring</td>
                            <td style="border:1px solid black;text-align: center">{{ $skor_psi->total }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-12">
                <p class="ml-3">PSI digunakan untuk menetapkan indikasi rawat inap pneumonia komunitas:</p>
                <ol type="1" class="ml-3"> <!-- Ordered list pertama menggunakan angka -->
                    <li>Skor PSI lebih dari 70.</li>
                    <li>
                        Bila skor PSI kurang dari 70, pasien tetap perlu dirawat inap bila dijumpai salah satu dari kriteria di bawah ini:
                        <ol type="a"> <!-- Ordered list kedua menggunakan alfabet -->
                            <li>Frekuensi nafas > 30 x/menit</li>
                            <li>PaO2/FiO2 kurang dari 250 mmHg</li>
                            <li>Radiologi menunjukkan infiltrat/opasitas/konsolidasi multi lobus</li>
                            <li>Tekanan sistolik &lt; 90mmHg</li>
                            <li>Tekanan diastolik &lt; 60 mmHg</li>
                        </ol>
                    </li>
                </ol>
            </div>
            <div class="col-10">
                <div style="text-align: center; font-size:10pt;">Tabel 4. Derajat skor risiko PSI</div>
                <table cellspacing="0" cellpadding="5" style="width: 100%;">
                    <thead>
                        <tr >
                            <th style="border: 1px solid black; text-align: center">Total Poin</th>
                            <th style="border: 1px solid black; text-align: center">Risiko</th>
                            <th style="border: 1px solid black; text-align: center">Kelas Risiko</th>
                            <th style="border: 1px solid black; text-align: center">Angka Kematian</th>
                            <th style="border: 1px solid black; text-align: center">Perawatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="border: 1px solid black">Tidak diprediksi</td>
                            <td style="border: 1px solid black">Rendah</td>
                            <td style="border: 1px solid black; text-align: center;">I</td>
                            <td style="border: 1px solid black; text-align: center;">0,1%</td>
                            <td style="border: 1px solid black">Rawat jalan</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid black">&lt; 70</td>
                            <td style="border: 1px solid black"></td>
                            <td style="border: 1px solid black; text-align: center;">II</td>
                            <td style="border: 1px solid black; text-align: center;">0,6%</td>
                            <td style="border: 1px solid black">Rawat jalan</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid black">71 - 90</td>
                            <td style="border: 1px solid black"></td>
                            <td style="border: 1px solid black; text-align: center;">III</td>
                            <td style="border: 1px solid black; text-align: center;">2,8%</td>
                            <td style="border: 1px solid black">Rawat inap/jalan</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid black">91 - 130</td>
                            <td style="border: 1px solid black">Sedang</td>
                            <td style="border: 1px solid black; text-align: center;">IV</td>
                            <td style="border: 1px solid black; text-align: center;">8,2%</td>
                            <td style="border: 1px solid black">Rawat inap</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid black">&gt; 130</td>
                            <td style="border: 1px solid black">Berat</td>
                            <td style="border: 1px solid black; text-align: center;">V</td>
                            <td style="border: 1px solid black; text-align: center;">29,2%</td>
                            <td style="border: 1px solid black">Rawat inap</td>
                        </tr>
                    </tbody>
                </table>
                <div style="text-align: center; font-size:10pt; padding-bottom:25px;">Dikutip dari Iksan M et al.</div>
                @php
                    $dokter_jaga = App\Vedika::getPegawai($skor_psi->kd_dokter);
                    $dokter_dpjp = App\Vedika::getPegawai($skor_psi->kd_dpjp);

                    $qr_dokter_jaga =
                        'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara
                    elektronik oleh' .
                        "\n" .
                        $dokter_jaga->nama .
                        "\n" .
                        'ID ' .
                        $dokter_jaga->nik .
                        "\n" .
                        \Carbon\Carbon::parse($skor_psi->tanggal)->format('d-m-Y');
                    $qrcode_jaga = base64_encode(
                            QrCode::format('png')->size(100)->errorCorrection('H')->generate($qr_dokter_jaga)
                        );
                    $qr_dokter_dpjp =
                        'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara
                    elektronik oleh' .
                        "\n" .
                        $dokter_dpjp->nama .
                        "\n" .
                        'ID ' .
                        $dokter_dpjp->nik .
                        "\n" .
                        \Carbon\Carbon::parse($skor_psi->tanggal)->format('d-m-Y');
                    $qrcode_dpjp = base64_encode(
                        QrCode::format('png')->size(100)->errorCorrection('H')->generate($qr_dokter_dpjp)
                    );
                @endphp
                <table style="width: 100%">
                    <tbody>
                        <tr>
                            <td style="width: 40%; text-align:center;">Dokter Jaga</td>
                            <td style="width: 20%"></td>
                            <td style="width: 40%; text-align:center;">Dokter Penanggung Jawab Pasien</td>
                        </tr>
                        <tr>
                            <td style="width: 40%; text-align:center;">
                                <img src="data:image/png;base64, {!! $qrcode_jaga !!}">
                            </td>
                            <td style="width: 20%"></td>
                            <td style="width: 40%; text-align:center;"><img src="data:image/png;base64, {!! $qrcode_dpjp !!}"></td>
                        </tr>
                        <tr>
                            <td style="width: 40%; text-align:center;">{{ $dokter_jaga->nama }}</td>
                            <td style="width: 20%"></td>
                            <td style="width: 40%; text-align:center;">{{ $dokter_dpjp->nama }}</td>
                        </tr>
                        <tr>
                            <td style="width: 40%; text-align:center;">{{ $dokter_jaga->nik }}</td>
                            <td style="width: 20%"></td>
                            <td style="width: 40%; text-align:center;">{{ $dokter_dpjp->nik }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif
    @if($skor_curb)
        <div style="float: none;">
            <div style="page-break-after: always;"></div>
        </div>
        <div class="watermark">
            {{ $watermark }}
        </div>
        <img src="{{ asset('image/kop.png') }}" alt="KOP RSUP">
        <hr class='new4' />
        <div class="row justify-content-center">
                <table style="width: 100%; margin-bottom:20px; ">
                    <thead>
                        <tr>
                            <th style="text-align: center;" colspan="4">
                                <div style="font-size: 14pt;">PENILAIAN KRITERIA CURB-65</div>
                            </th>
                        </tr>
                        <tr>
                            <td style="width: 20%; padding-left: 25px;">No. Rekam Medis</td>
                            <td style="width: 30%; padding-left: 25px;">: {{ $pasien->no_rkm_medis }}</td>
                            <td style="width: 20%; padding-left: 25px;">JK</td>
                            <td style="width: 30%; padding-left: 25px;">: {{ $pasien->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                        </tr>
                        <tr>
                            <td style="padding-left: 25px;">Nama Pasien</td>
                            <td style="padding-left: 25px;">: {{ $pasien->nm_pasien }}</td>
                            <td style="padding-left: 25px;">Tanggal Lahir</td>
                            <td style="padding-left: 25px;">: {{ $pasien->tgl_lahir }}</td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 3px solid black; padding-left: 25px;">Umur</td>
                            <td style="border-bottom: 3px solid black; padding-left: 25px;">:
                                {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->diff(\Carbon\Carbon::parse($pasien->tgl_registrasi))->format('%y Th') }}
                            </td>
                            <td style="border-bottom: 3px solid black; padding-left: 25px;">Alamat</td>
                            <td style="border-bottom: 3px solid black; padding-left: 25px;">: {{ $pasien->alamat }}</td>
                        </tr>
                    </thead>
                </table>
                <div class="col-8">
                    <table style="width: 100%; margin-bottom:25px;" cellspacing="0" cellpadding="5">
                        <tbody>
                            <tr>
                                <td style="border:1px solid black;font-weight:bold;text-align:center; width: 5%">No.</td>
                                <td style="border:1px solid black;font-weight:bold;text-align:center; width: 30%">CURB-65</td>
                                <td style="border:1px solid black;font-weight:bold;text-align:center; width: 50%">GAMBARAN KLINIS</td>
                                <td style="border:1px solid black;font-weight:bold;text-align:center; width: 15%">SKOR</td>
                            </tr>
                            <tr>
                                <td style="border:1px solid black;font-weight:bold;text-align:center;">1.</td>
                                <td style="border:1px solid black; text-align:center;">C</td>
                                <td style="border:1px solid black;">Confusion Uji Mental Nilai <= 8</td>
                                <td style="border:1px solid black; text-align:center;">{{ $skor_curb->C }}</td>
                            </tr>
                            <tr>
                                <td style="border:1px solid black; font-weight:bold;text-align:center;">2.</td>
                                <td style="border:1px solid black; text-align:center;">U</td>
                                <td style="border:1px solid black;">Ureum > 40 mg/dL</td>
                                <td style="border:1px solid black; text-align:center;">{{ $skor_curb->U }}</td>
                            </tr>
                            <tr>
                                <td style="border:1px solid black; font-weight:bold;text-align:center;">3.</td>
                                <td style="border:1px solid black; text-align:center;">R</td>
                                <td style="border:1px solid black;">Respiratory Rate >30x / menit</td>
                                <td style="border:1px solid black; text-align:center;">{{ $skor_curb->R }}</td>
                            </tr>
                            <tr>
                                <td style="border:1px solid black; font-weight:bold;text-align:center;">4.</td>
                                <td style="border:1px solid black; text-align:center;">B</td>
                                <td style="border:1px solid black;">Blood Pressure &lt;90/60 mmHg </td>
                                <td style="border:1px solid black; text-align:center;">{{ $skor_curb->B }}</td>
                            </tr>
                            <tr>
                                <td style="border:1px solid black; font-weight:bold;text-align:center;">5.</td>
                                <td style="border:1px solid black; text-align:center;">65</td>
                                <td style="border:1px solid black;">Umur > 65 Tahun</td>
                                <td style="border:1px solid black; text-align:center;">{{ $skor_curb->U65 }}</td>
                            </tr>
                            <tr>
                                <td style="border:1px solid black; "></td>
                                <td style="border:1px solid black; font-weight:bold; text-align:right;" colspan="2">Total</td>
                                <td style="border:1px solid black; text-align:center;">{{ $skor_curb->total }}</td>
                            </tr>
                            <tr>
                                <td style="border:1px solid black; "></td>
                                <td style="border:1px solid black; font-weight:bold; text-align:center;" colspan="2">Respons</td>
                                <td style="border:1px solid black; font-weight:bold; text-align:center;">Nilai</td>
                            </tr>
                            <tr>
                                <td style="border-left:1px solid black; border-right:1px solid black;"></td>
                                <td style="" colspan="2">Umur</td>
                                <td style="border-left:1px solid black; border-right:1px solid black; text-align:center;">{{ $skor_curb->res1 }}</td>
                            </tr>
                            <tr>
                                <td style="border-left:1px solid black; border-right:1px solid black;"></td>
                                <td style="" colspan="2">Tanggal Lahir</td>
                                <td style="border-left:1px solid black; border-right:1px solid black; text-align:center;">{{ $skor_curb->res2 }}</td>
                            </tr>
                            <tr>
                                <td style="border-left:1px solid black; border-right:1px solid black;"></td>
                                <td style="" colspan="2">Waktu</td>
                                <td style="border-left:1px solid black; border-right:1px solid black; text-align:center;">{{ $skor_curb->res3 }}</td>
                            </tr>
                            <tr>
                                <td style="border-left:1px solid black; border-right:1px solid black;"></td>
                                <td style="" colspan="2">Tahun Sekarang</td>
                                <td style="border-left:1px solid black; border-right:1px solid black; text-align:center;">{{ $skor_curb->res4 }}</td>
                            </tr>
                            <tr>
                                <td style="border-left:1px solid black; border-right:1px solid black;"></td>
                                <td style="" colspan="2">Nama Rumah Sakit</td>
                                <td style="border-left:1px solid black; border-right:1px solid black; text-align:center;">{{ $skor_curb->res5 }}</td>
                            </tr>
                            <tr>
                                <td style="border-left:1px solid black; border-right:1px solid black;"></td>
                                <td style="" colspan="2">Dapat mengidentifikasi 2 orang</td>
                                <td style="border-left:1px solid black; border-right:1px solid black; text-align:center;">{{ $skor_curb->res6 }}</td>
                            </tr>
                            <tr>
                                <td style="border-left:1px solid black; border-right:1px solid black;"></td>
                                <td style="" colspan="2">Alamat Rumah</td>
                                <td style="border-left:1px solid black; border-right:1px solid black; text-align:center;">{{ $skor_curb->res7 }}</td>
                            </tr>
                            <tr>
                                <td style="border-left:1px solid black; border-right:1px solid black;"></td>
                                <td style="" colspan="2">Tanggal Kemerdekaan</td>
                                <td style="border-left:1px solid black; border-right:1px solid black; text-align:center;">{{ $skor_curb->res8 }}</td>
                            </tr>
                            <tr>
                                <td style="border-left:1px solid black; border-right:1px solid black;"></td>
                                <td style="" colspan="2">Nama Presiden</td>
                                <td style="border-left:1px solid black; border-right:1px solid black; text-align:center;">{{ $skor_curb->res9 }}</td>
                            </tr>
                            <tr>
                                <td style="border-left:1px solid black; border-right:1px solid black;"></td>
                                <td style="" colspan="2">Hitung Mundul < 20</td>
                                <td style="border-left:1px solid black; border-right:1px solid black; text-align:center;">{{ $skor_curb->res10 }}</td>
                            </tr>
                            <tr>
                                <td style="border:1px solid black;"></td>
                                <td style="border:1px solid black; font-weight:bold; text-align:right;" colspan="2">Total</td>
                                <td style="border:1px solid black; text-align:center;">{{ $skor_curb->totalrespon }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-8">
                    @php
                        $qr_dokter =
                            'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara
                        elektronik oleh' .
                            "\n" .
                            $skor_curb->nama .
                            "\n" .
                            'ID ' .
                            $skor_curb->kd_dokter .
                            "\n" .
                            \Carbon\Carbon::parse($skor_curb->tanggal)->format('d-m-Y');
                            $qrcode_dokter = base64_encode(
                                            QrCode::format('png')->size(100)->errorCorrection('H')->generate($qr_dokter)
                                        );
                    @endphp
                    <table style="width: 100%">
                        <tbody>
                            <tr>
                                <td style="width: 20%;">Level</td>
                                <td colspan="2">: {{ $skor_curb->level_resiko }}</td>
                            </tr>
                            <tr>
                                <td>Perawatan</td>
                                <td colspan="2">: {{ $skor_curb->perawatan_disarankan }}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td style="width: 40%; text-align:center; padding-left:50px;">Surakarta, {{ \Carbon\Carbon::parse($skor_curb->tanggal)->format('d-m-Y') }}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td style="width: 40%; text-align:center;padding-left:50px;"><img src="data:image/png;base64, {!! $qrcode_dokter !!}"></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td style="width: 40%; text-align:center;padding-left:50px;">{{ $skor_curb->kd_dokter }}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td style="width: 40%; text-align:center;padding-left:50px;">{{ $skor_curb->nama }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if($dataTransfusi)
        @foreach ($dataTransfusi as $listTransfusi)
            <div style="float: none;">
                <div style="page-break-after: always;"></div>
            </div>
            <div class="watermark">
                {{ $watermark }}
            </div>
            <img src="{{ asset('image/kop.png') }}" alt="KOP RSUP">
            <div class="row justify-content-center">
                <table style="width: 100%; margin-top:10px; margin-bottom:-2px;" class="table table-borderless table-sm">
                    <thead>
                        <tr>
                            <th style="text-align: center; border-bottom: 1px solid black; border-top: 3px solid black; border-left: 1px solid black; border-right: 1px solid black;" colspan="5">
                                <h3><b>MONITORING TRANSFUSI DARAH / PRODUK DARAH</b></h3>
                            </th>
                        </tr>
                        <tr>
                            <td style="border-left: 1px solid black;">
                                Identitas Pasien
                            </td>
                            <td style="" colspan="2">
                                : {{ $listTransfusi->nm_pasien }} / {{ $listTransfusi->no_rkm_medis }} / {{ $listTransfusi->jk }}
                            </td>
                            <td style="border-right: 1px solid black;" class="text-bold" colspan="2">
                                PETUGAS BANK DARAH
                            </td>
                        </tr>
                        <tr>
                            <td style="border-left: 1px solid black;">
                                Nomor Kantong
                            </td>
                            <td style="" colspan="2">
                                : {{ $listTransfusi->nomor_kantong }}
                            </td>
                            <td style="" class="">
                                Nama Petugas
                            </td>
                            <td style="border-right: 1px solid black;" class="">
                                : {{ $listTransfusi->petugas1 }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border-left: 1px solid black;">
                                Golongan Darah
                            </td>
                            <td style="" colspan="2">
                                : {{ $listTransfusi->gol_darah }}
                            </td>
                            <td style="" class="">
                                Waktu Penyerahan
                            </td>
                            <td style="border-right: 1px solid black;" class="">
                                : {{ $listTransfusi->tgl_penyerahan }} {{ $listTransfusi->wp_jam }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border-left: 1px solid black;">
                                Jenis Darah / Komponen
                            </td>
                            <td style="" colspan="2">
                                : {{ $listTransfusi->jenis_darah }}
                            </td>
                            <td style="border-right: 1px solid black;" class="text-bold" colspan="2">
                                PENERIMA DARAH
                            </td>
                        </tr>
                        <tr>
                            <td style="border-left: 1px solid black;">
                                Tanggal Kadaluarsa
                            </td>
                            <td style="" colspan="2">
                                : {{ $listTransfusi->tgl_kadaluwarsa }}
                            </td>
                            <td style="" class="">
                                Nama
                            </td>
                            <td style="border-right: 1px solid black;" class="">
                                : {{ $listTransfusi->penerima }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border-left: 1px solid black;">
                            </td>
                            <td style="" colspan="2">
                            </td>
                            <td style="" class="">
                                Waktu Transfusi
                            </td>
                            <td style="border-right: 1px solid black;" class="">
                                : {{ $listTransfusi->tgl_transfusi }} {{ $listTransfusi->jam_transfusi }}
                            </td>
                        </tr>
                    </thead>
                </table>
                <table class="table table-borderless table-sm" style="border: 1px solid black;">
                    <tbody>
                        <tr>
                            <td style="vertical-align: middle; text-align:center; width:20%; border: 1px solid black;"><b>KONDISI</b></td>
                            <td colspan="2" style="vertical-align: middle; text-align:center;width:20%; border: 1px solid black;"><b>SEBELUM TRANSFUSI</b><br>{{ $listTransfusi->jam_st }} WIB</td>
                            <td colspan="2" style="text-align: center; width:20%; border: 1px solid black;"><b>15-30 MENIT TRANSFUSI</b><br>{{ $listTransfusi->jam_mt }} WIB</td>
                            <td colspan="2" style="text-align: center; width:20%; border: 1px solid black;"><b>2 JAM TRANSFUSI</b><br>{{ $listTransfusi->jam_t }} WIB</td>
                            <td colspan="2" style="text-align: center; width:20%; border: 1px solid black;"><b>PASCA TRANSFUSI</b><br>{{ $listTransfusi->jam_pt }} WIB</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; text-align:center;border: 1px solid black;">Keadaan Umum</td>
                            <td colspan="2" style="vertical-align: middle; text-align:center;border: 1px solid black;">{{ $listTransfusi->ku_st }}</td>
                            <td colspan="2" style="text-align: center; border: 1px solid black;">{{ $listTransfusi->ku_mt }}</td>
                            <td colspan="2" style="text-align: center; border: 1px solid black;">{{ $listTransfusi->ku_t }}</td>
                            <td colspan="2" style="text-align: center; border: 1px solid black;">{{ $listTransfusi->ku_pt }}</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; text-align:center;border: 1px solid black;">Suhu Tubuh</td>
                            <td colspan="2" style="vertical-align: middle; text-align:center;border: 1px solid black;">{{ $listTransfusi->st_st }}</td>
                            <td colspan="2" style="text-align: center; border: 1px solid black;">{{ $listTransfusi->st_mt }}</td>
                            <td colspan="2" style="text-align: center; border: 1px solid black;">{{ $listTransfusi->st_t }}</td>
                            <td colspan="2" style="text-align: center; border: 1px solid black;">{{ $listTransfusi->st_pt }}</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; text-align:center; border: 1px solid black;">Nadi</td>
                            <td colspan="2" style="vertical-align: middle; text-align:center; border: 1px solid black;">{{ $listTransfusi->nadi_st }}</td>
                            <td colspan="2" style="text-align: center; border: 1px solid black;">{{ $listTransfusi->nadi_mt }}</td>
                            <td colspan="2" style="text-align: center; border: 1px solid black;">{{ $listTransfusi->nadi_t }}</td>
                            <td colspan="2" style="text-align: center; border: 1px solid black;">{{ $listTransfusi->nadi_pt }}</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; text-align:center; border: 1px solid black;">Tekanan Darah</td>
                            <td colspan="2" style="vertical-align: middle; text-align:center; border: 1px solid black;">{{ $listTransfusi->td_st }}</td>
                            <td colspan="2" style="text-align: center; border: 1px solid black;">{{ $listTransfusi->td_mt }}</td>
                            <td colspan="2" style="text-align: center; border: 1px solid black;">{{ $listTransfusi->td_t }}</td>
                            <td colspan="2" style="text-align: center; border: 1px solid black;">{{ $listTransfusi->td_pt }}</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; text-align:center; border: 1px solid black;"><i>Respiratory Rate</i></td>
                            <td colspan="2" style="vertical-align: middle; text-align:center; border: 1px solid black;">{{ $listTransfusi->rr_st }}</td>
                            <td colspan="2" style="text-align: center; border: 1px solid black;">{{ $listTransfusi->rr_mt }}</td>
                            <td colspan="2" style="text-align: center; border: 1px solid black;">{{ $listTransfusi->rr_t }}</td>
                            <td colspan="2" style="text-align: center; border: 1px solid black;">{{ $listTransfusi->rr_pt }}</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; text-align:center; border: 1px solid black;">Volume & Warna Urine</td>
                            <td colspan="2" style="vertical-align: middle; text-align:center; border: 1px solid black;">{{ $listTransfusi->vol_st }} WIB</td>
                            <td colspan="2" style="text-align: center; border: 1px solid black;">{{ $listTransfusi->vol_mt }}</td>
                            <td colspan="2" style="text-align: center; border: 1px solid black;">{{ $listTransfusi->vol_t }}</td>
                            <td colspan="2" style="text-align: center; border: 1px solid black;">{{ $listTransfusi->vol_pt }}</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; border: 1px solid black;" rowspan="5">Gejala dan tanda reaksi transfusi yang ditemukan &#42;&#41;</td>
                            <td style="border-left: 1px solid black;">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_1 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">urtikaria</label>
                                </div>
                            </td>
                            <td style="border-right: 1px solid black;">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_6 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">nyeri dada</label>
                                </div>
                            </td>
                            <td style="">
                                {{-- <div class="form-check">
                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $listTransfusi->gr_10 == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        urtikaria
                                    </label>
                                </div> --}}
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_10 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">urtikaria</label>
                                </div>
                            </td>
                            <td style="border-right: 1px solid black;">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_15 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">nyeri dada</label>
                                </div>
                            </td>
                            <td style="">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_19 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">urtikaria</label>
                                </div>
                            </td>
                            <td style="border-right: 1px solid black;">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_24 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">nyeri dada</label>
                                </div>
                            </td>
                            <td style="">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_28 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">urtikaria</label>
                                </div>
                            </td>
                            <td style="border-right: 1px solid black;">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_33 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">nyeri dada</label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_2 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">demam</label>
                                </div>
                            </td>
                            <td style="border-right: 1px solid black;">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_7 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">nyeri kepala</label>
                                </div>
                            </td>
                            <td style="">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_11 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">demam</label>
                                </div>
                            </td>
                            <td style="border-right: 1px solid black;">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_16 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">nyeri kepala</label>
                                </div>
                            </td>
                            <td style="">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_20 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">demam</label>
                                </div>
                            </td>
                            <td style="border-right: 1px solid black;">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_25 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">nyeri kepala</label>
                                </div>
                            </td>
                            <td style="">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_29 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">demam</label>
                                </div>
                            </td>
                            <td style="border-right: 1px solid black;">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_34 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">nyeri kepala</label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_3 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">gatal</label>
                                </div>
                            </td>
                            <td style="border-right: 1px solid black;">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_8 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">Syok&#42;&#42;</label>
                                </div>
                            </td>
                            <td style="">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_12 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">gatal</label>
                                </div>
                            </td>
                            <td style="border-right: 1px solid black;">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_17 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">Syok&#42;&#42;</label>
                                </div>
                            </td>
                            <td style="">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_21 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">gatal</label>
                                </div>
                            </td>
                            <td style="border-right: 1px solid black;">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_26 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">Syok&#42;&#42;</label>
                                </div>
                            </td>
                            <td style="">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_30 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">gatal</label>
                                </div>
                            </td>
                            <td style="border-right: 1px solid black;">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_35 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">Syok&#42;&#42;</label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_4 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">takikardi</label>
                                </div>
                            </td>
                            <td style="border-right: 1px solid black;">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_9 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">sesak napas&#42;&#42;</label>
                                </div>
                            </td>
                            <td style="">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_13 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">takikardi</label>
                                </div>
                            </td>
                            <td style="border-right: 1px solid black;">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_18 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">sesak napas&#42;&#42;</label>
                                </div>
                            </td>
                            <td style="">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_22 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">takikardi</label>
                                </div>
                            </td>
                            <td style="border-right: 1px solid black;">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_27 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">sesak napas&#42;&#42;</label>
                                </div>
                            </td>
                            <td style="">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_31 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">takikardi</label>
                                </div>
                            </td>
                            <td style="border-right: 1px solid black;">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_36 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">sesak napas&#42;&#42;</label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-right: 1px solid black; border-bottom: 1px solid black;" colspan="2">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_5 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">hematuria / Hemoglobinuria&#42;&#42;</label>
                                </div>
                            </td>
                            <td style="border-right: 1px solid black; border-bottom: 1px solid black;" colspan="2">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_14 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">hematuria / Hemoglobinuria&#42;&#42;</label>
                                </div>
                            </td>
                            <td style="border-right: 1px solid black; border-bottom: 1px solid black;" colspan="2">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_23 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">hematuria / Hemoglobinuria&#42;&#42;</label>
                                </div>
                            </td>
                            <td style="border-right: 1px solid black; border-bottom: 1px solid black;" colspan="2">
                                <div class="checkbox-group-flex">
                                    <input type="checkbox" id="myCheckbox" {{ $listTransfusi->gr_32 == 'true' ? 'checked' : '' }}>
                                    <label for="myCheckbox">hematuria / Hemoglobinuria&#42;&#42;</label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left:25px;" colspan="3">Nama Perawat yang melakukan transfusi <br> <i>(double check)</i>
                            </td>
                            <td style="" colspan="3">
                                1&#41; {{ $listTransfusi->petugas2 }} <br>
                                2&#41; {{ $listTransfusi->petugas3 }} <br>
                            </td>
                            <td style="text-align:center;" colspan="3">
                                Surakarta, {{ \Carbon\Carbon::parse($listTransfusi->tanggal)->format('d-m-Y') }} <br> Petugas Transfusi
                            </td>
                        </tr>
                        @php
                            $qr_petugas =
                            'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara
                            elektronik oleh' .
                            "\n" .
                            $listTransfusi->petugas2 .
                            "\n" .
                            'ID ' .
                            $listTransfusi->kd_petugas_2 .
                            "\n" .
                            \Carbon\Carbon::parse($listTransfusi->tanggal)->format('d-m-Y');

                            $qrcode_petugas= base64_encode(
                                QrCode::format('png')->size(100)->errorCorrection('H')->generate($qr_petugas)
                            );
                        @endphp
                        <tr>
                            <td style="padding-left:25px; vertical-align:middle;" colspan="6">
                                &#42;&#41; gejala yang ditemukan <br>
                                &#42;&#42;&#41; mengikuti SPO pelaporan reaksi transfusi
                            </td>
                            <td style="text-align:center;" colspan="3">
                                <img src="data:image/png;base64, {!! $qrcode_dokter !!}"> <br> {{ $listTransfusi->petugas2 }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endforeach
    @endif
    {{-- Data Operasi --}}
    @if ($dataOperasi2)
        @foreach ($dataOperasi2 as $index => $listOperasi)
            <div style="float: none;">
                <div style="page-break-after: always;"></div>
            </div>
            <div class="watermark">
                {{ $watermark }}
            </div>
            <div>
                <img src="{{ asset('image/kop.png') }}" alt="KOP RSUP">
                <hr class='new4' />
                <table>
                    <thead>
                        <tr>
                            <td colspan="6" style="border-bottom:1pt solid black">
                                <div style="font-size: 16pt;">LAPORAN OPERASI</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Nama Pasien
                            </td>
                            <td colspan="2">
                                : {{ $pasien->nm_pasien }}
                            </td>
                            <td>
                                No. Rekam Medis
                            </td>
                            <td colspan="2">
                                : {{ $pasien->no_rkm_medis }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Umur
                            </td>
                            <td colspan="2">
                                :
                                {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->diff(\Carbon\Carbon::parse($listOperasi->tgl_operasi))->format('%y Th %m Bl %d Hr') }}
                            </td>
                            <td>
                                Ruang
                            </td>
                            <td colspan="2">
                                : {{ $pasien->kd_kamar }} {{ $pasien->nm_bangsal }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid black;">
                                Tgl Lahir
                            </td>
                            <td style="border-bottom: 1px solid black;" colspan="2">
                                :
                                {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->format('d/m/Y') }}
                            </td>
                            <td style="border-bottom: 1px solid black;">
                                Jenis Kelamin
                            </td>
                            <td style="border-bottom: 1px solid black;" colspan="2">
                                : {{ $pasien->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="border: 1px solid black; background-color:lightgray" colspan="6">
                                <div style="font-size: 14pt; text-align:center">PRE SURGICAL ASSESMENT</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Tanggal
                            </td>
                            <td>
                                :
                                {{ \Carbon\Carbon::parse($dataOperasi1->tgl_perawatan)->format('d/m/Y') }}
                            </td>
                            <td>
                                Waktu
                            </td>
                            <td>
                                : {{ $dataOperasi1->jam_rawat }}
                            </td>
                            <td>
                                Alergi
                            </td>
                            <td>
                                : {{ $dataOperasi1->alergi }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Dokter Bedah
                            </td>
                            <td
                                colspan="5">
                                :
                                {!! $listOperasi->operator1 != '-' ? \App\Vedika::getPegawai($listOperasi->operator1)->nama : '-' !!}
                            </td>
                        </tr>
                        <tr>
                            <td style="border-top:1px solid black">
                                Keluhan:
                            </td>
                            <td style="border-top:1px solid black" colspan="2">

                            </td>
                            <td style="border-top:1px solid black;border-left:1px solid black">
                                Penilaian:
                            </td>
                            <td style="border-top:1px solid black;" colspan="2">

                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <div style="text-decoration: underline; vertical-align:center; padding-left:10px">
                                    {{ $dataOperasi1->keluhan }}</div>
                            </td>
                            <td style="border-left:1px solid black" solid black colspan="3">
                                <div style="text-decoration: underline; vertical-align:center; padding-left:10px">
                                    {{ $dataOperasi1->penilaian }}</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Pemeriksaan:
                            </td>
                            <td colspan="2">

                            </td>
                            <td style="border-left:1px solid black">
                                Tindak Lanjut:
                            </td>
                            <td colspan="2">

                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <div style="text-decoration: underline; vertical-align:center; padding-left:10px">
                                    {{ $dataOperasi1->pemeriksaan }}</div>
                            </td>
                            <td style="border-left:1px solid black" colspan="3">
                                <div style="text-decoration: underline; vertical-align:center; padding-left:10px">
                                    {{ $dataOperasi1->rtl }}</div>
                            </td>

                        </tr>
                        <tr>
                            <td style="padding-left:10px" colspan="2">
                                Suhu Tubuh.(C)
                            </td>
                            <td>
                                : <u>{{ $dataOperasi1->suhu_tubuh }}</u>
                            </td>
                            <td style="padding-left:10px; border-left:1px solid black">
                                Nadi (/Mnt)
                            </td>
                            <td colspan="2">
                                : <u>{{ $dataOperasi1->nadi }}</u>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left:10px" colspan="2">
                                Tensi.
                            </td>
                            <td>
                                : <u>{{ $dataOperasi1->tensi }}</u>
                            </td>
                            <td style="padding-left:10px; border-left:1px solid black">
                                Respirasi (/Mnt).
                            </td>
                            <td colspan="2">
                                : <u>{{ $dataOperasi1->respirasi }}</u>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left:10px" colspan="2">
                                Tinggi (Cm).
                            </td>
                            <td>
                                : <u>{{ $dataOperasi1->tinggi }}</u>
                            </td>
                            <td style="padding-left:10px; border-left:1px solid black">
                                GCS (E,V,M).
                            </td>
                            <td colspan="2">
                                : <u>{{ $dataOperasi1->gcs }}</u>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left:10px" colspan="2">
                                Berat (Kg).
                            </td>
                            <td>
                                : <u>{{ $dataOperasi1->berat }}</u>
                            </td>
                            <td style="border-left:1px solid black" colspan="3">
                            </td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid black; background-color:lightgray" colspan="6">
                                <div style="font-size: 14pt; text-align:center">POST SURGICAL REPORT</div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                Tanggal & Waktu
                            </td>
                            <td colspan="3">
                                :
                                {{ \Carbon\Carbon::parse($listOperasi->tgl_operasi)->format('d/m/Y H:i:s') }}
                            </td>
                            <td style="border-left: 1px solid black">
                            </td>
                        </tr>
                        <tr>
                            <td  colspan="2">
                                Dokter Bedah
                            </td>
                            <td>
                                :
                            </td>
                            <td >
                                Asisten Bedah
                            </td>
                            <td>
                                :
                            </td>
                            <td style="border-left: 1px solid black; text-align:center">
                                Tipe/Jenis Anastesi
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left: 20px;" colspan="3">
                                {!! $listOperasi->operator1 != '-' ? \App\Vedika::getPegawai($listOperasi->operator1)->nama : '-' !!}
                            </td>
                            <td style="padding-left: 20px;" colspan="2">
                                {!! $listOperasi->asisten_operator1 != '-'
                                    ? \App\Vedika::getPegawai($listOperasi->asisten_operator1)->nama
                                    : '-' !!}
                            </td>
                            <td style="border-left: 1px solid black;text-align:center">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                Dokter Bedah 2
                            </td>
                            <td>
                                :
                            </td>
                            <td>
                                Asisten Bedah 2
                            </td>
                            <td>
                                :
                            </td>
                            <td style="border-left: 1px solid black; text-align:center;">
                                {{ $listOperasi->jenis_anasthesi }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left:20px;" colspan="3">
                                {!! $listOperasi->operator2 != '-' ? \App\Vedika::getPegawai($listOperasi->operator2)->nama : '-' !!}
                            </td>
                            <td style="padding-left:20px;" colspan="2">
                                {!! $listOperasi->asisten_operator2 != '-'
                                    ? \App\Vedika::getPegawai($listOperasi->asisten_operator2)->nama
                                    : '-' !!}
                            </td>
                            <td style="border-left: 1px solid black; text-align:center;">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                Perawat Resusitas
                            </td>
                            <td>
                                :
                            </td>
                            <td>
                                Dokter Anastesi
                            </td>
                            <td>
                                :
                            </td>
                            <td style="border-left: 1px solid black; text-align:center;">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left:20px;" colspan="3">
                                {!! $listOperasi->perawaat_resusitas != '-'
                                    ? \App\Vedika::getPegawai($listOperasi->perawaat_resusitas)->nama
                                    : '-' !!}
                            </td>
                            <td style="padding-left:20px;" colspan="2">
                                {!! $listOperasi->dokter_anestesi != '-' ? \App\Vedika::getPegawai($listOperasi->dokter_anestesi)->nama : '-' !!}
                            </td>
                            <td style="border-left: 1px solid black; text-align:center;">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                Instrumen
                            </td>
                            <td>
                                :
                            </td>
                            <td>
                                Asisten Anastesi
                            </td>
                            <td>
                                :
                            </td>
                            <td style="border-left: 1px solid black; text-align:center;">
                                Dikirim ke Pemeriksaaan PA
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left:20px;" colspan="3">
                                {!! $listOperasi->instrumen != '-' ? \App\Vedika::getPegawai($listOperasi->instrumen)->nama : '-' !!}
                            </td>
                            <td style="padding-left:20px;" colspan="2">
                                {!! $listOperasi->asisten_anestesi != '-'
                                    ? \App\Vedika::getPegawai($listOperasi->asisten_anestesi)->nama
                                    : '-' !!}
                            </td>
                            <td style="border-left: 1px solid black; text-align:center;">
                                {{ isset($listOperasi->permintaan_pa) ? $listOperasi->permintaan_pa:'-' }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                Dokter Anak
                            </td>
                            <td>
                                :
                            </td>
                            <td>
                                Bidan
                            </td>
                            <td>
                                :
                            </td>
                            <td style="border-left: 1px solid black; text-align:center;">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left:20px;" colspan="3">
                                {!! $listOperasi->dokter_anak != '-' ? \App\Vedika::getPegawai($listOperasi->dokter_anak)->nama : '-' !!}
                            </td>
                            <td style="padding-left:20px;" colspan="2">
                                {!! $listOperasi->bidan != '-' ? \App\Vedika::getPegawai($listOperasi->bidan)->nama : '-' !!}
                            </td>
                            <td style="border-left: 1px solid black; text-align:center;">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                Dokter Umum
                            </td>
                            <td>
                                :
                            </td>
                            <td>
                                Onloop
                            </td>
                            <td>
                                :
                            </td>
                            <td style="border-left: 1px solid black; text-align:center;">
                                Tipe/Kategori Operasi
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left:20px;" colspan="3">
                                {!! $listOperasi->dokter_umum != '-' ? \App\Vedika::getPegawai($listOperasi->dokter_umum)->nama : '-' !!}
                            </td>
                            <td style="padding-left:20px;" colspan="2">
                                {!! $listOperasi->omloop != '-' ? \App\Vedika::getPegawai($listOperasi->omloop)->nama : '-' !!}
                            </td>
                            <td style="border-left: 1px solid black; text-align:center;">
                                {{ $listOperasi->kategori }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5">
                                Diagnosa Pre-Op / Pre Operation Diagnosis
                            </td>
                            <td style="border-left: 1px solid black; text-align:center;">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left:20px;" colspan="5">
                                {{ isset($listOperasi->diagnosa_preop)? $listOperasi->diagnosa_preop:'-' }}
                            </td>
                            <td style="border-left: 1px solid black; text-align:center;">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5">
                                Jaringan Yang di-Eksisi/-Insisi
                            </td>
                            <td style="border-left: 1px solid black; text-align:center;">
                                Selesai Operasi
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left:20px;" colspan="5">
                                {{ isset($listOperasi->jaringan_dieksekusi)? $listOperasi->jaringan_dieksekusi:'-' }}
                            </td>
                            <td style="border-left: 1px solid black; text-align:center;">
                                {{ isset($listOperasi->selesaioperasi)?\Carbon\Carbon::parse($listOperasi->selesaioperasi)->format('d/m/Y H:i:s'):'-' }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5">
                                Diagnosa Post-Op / Post Operation Diagnosis
                            </td>
                            <td style="border-left: 1px solid black; text-align:center;">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left:20px;" colspan="5">
                                {{ isset($listOperasi->diagnosa_postop)? $listOperasi->diagnosa_postop:'-' }}
                            </td>
                            <td style="border-left: 1px solid black; text-align:center;">
                            </td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid black; background-color:lightgray" colspan="6">
                                <div style="text-align: center; font-size:16pt">REPORT ( PROCEDURES, SPECIFIC FINDINGS
                                    AND COMPLICATIONS )
                                </div>
                            </td>
                        </tr>
                        @php
                            $dokterOperator = \App\Vedika::getPegawai($listOperasi->operator1)->nama;
                            if(isset($listOperasi->laporan_operasi)){
                                $draf = preg_split('/\r\n|\r|\n/', $listOperasi->laporan_operasi);
                            }else{
                                $draf = null;
                            }

                            $qr_dokter =
                                'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara
                                        elektronik oleh' .
                                "\n" .
                                $dokterOperator .
                                "\n" .
                                'ID ' .
                                $listOperasi->operator1 .
                                "\n" .
                                \Carbon\Carbon::parse(isset($listOperasi->selesaioperasi)?$listOperasi->selesaioperasi:\Carbon\Carbon::now())->format('d-m-Y');

                            $qrcode_petugas = base64_encode(
                                QrCode::format('png')->size(100)->errorCorrection('H')->generate($qr_dokter)
                            );
                        @endphp
                        <tr>
                            <td style="padding-left:20px;" colspan="5">
                                @if(isset($draf))
                                    @foreach ($draf as $laporan)
                                        {{ $laporan }}<br>
                                    @endforeach
                                @endif
                            </td>
                            <td style="text-align: center">
                                {{ \Carbon\Carbon::now()->format('d/m/Y') }}<br>
                                Dokter Bedah<br>
                                <img src="data:image/png;base64, {!! $qrcode_dokter !!}"><br>
                                <u>{{ $dokterOperator }}</u>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @php
                ++$index;
            @endphp
        @endforeach
    @endif
    {{-- End Data Operasi --}}

    @if($dataAnestesi)
        <div style="float: none;">
            <div style="page-break-after: always;"></div>
        </div>
        <div class="watermark">
            {{ $watermark }}
        </div>
        <div>
            <img src="{{ asset('image/kop.png') }}" alt="KOP RSUP">
            <table class="table table-borderless table-sm mb-2">
                <thead>
                    <tr>
                        <th class="align-middle text-center pb-1" colspan="2" rowspan="6" style="width: 50%; border: 1px solid black; font-size: 14pt;">
                            <h5><b>ASESMEN PRASEDASI DAN ANESTESI</b></h5>
                        </th>
                        <td style="width: 20%; border-top: 1px solid black;">
                            No. Rawat
                        </td>
                        <td style="width: 30%; border-top: 1px solid black; border-right: 1px solid black;">: {{ $dataAnestesi->no_rawat }}</td>
                    </tr>
                    <tr>
                        <td>
                            No. Rekam Medis
                        </td>
                        <td style="border-right: 1px solid black;">: {{ $dataAnestesi->no_rkm_medis }}</td>
                    </tr>
                    <tr>
                        <td>
                            Nama Pasien
                        </td>
                        <td style="border-right: 1px solid black;">: {{ $dataAnestesi->nm_pasien }}/ Th/ {{ $dataAnestesi->jk == 'L'? 'Laki-laki':'Perempuan' }}</td>
                    </tr>
                    <tr>
                        <td>
                            Tanggal Lahir
                        </td>
                        <td style="border-right: 1px solid black;">: {{ \Carbon\Carbon::parse($dataAnestesi->tgl_lahir)->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <td>
                            Alamat
                        </td>
                        <td style="border-right: 1px solid black;">: {{ $dataAnestesi->alamat }}, {{ $dataAnestesi->kelurahan }}, {{ $dataAnestesi->kecamatan }}, {{ $dataAnestesi->kabupaten }}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid black; ">
                            Ruang Rawat
                        </td>
                        <td style="border-bottom: 1px solid black; border-right: 1px solid black;">: {{ $dataAnestesi->nm_bangsal }}</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="2" rowspan="6" style="width:50%; border: 1px solid black;">
                            Anamnesis: <br>
                            {{ $dataAnestesi->anamnesis }}
                        </td>
                        <td style="width:20%; border-top: 1px solid black;">
                            Diagnosa Pre Operasi
                        </td>
                        <td style="width:30%; border-top: 1px solid black; border-right: 1px solid black;">
                            : {{ $dataAnestesi->diagnosa_preop }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Rencana Operasi
                        </td>
                        <td style="border-right: 1px solid black;">
                            : {{ $dataAnestesi->rencana_operasi }}
                        </td>
                    </tr>
                    <tr>
                        <td style="border-top: 1px solid black;">
                            TB : {{ $dataAnestesi->tb }}
                        </td>
                        <td style="border-top: 1px solid black; border-right: 1px solid black;">
                            BB : {{ $dataAnestesi->bb }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border-right: 1px solid black;">
                            Obat yang dikonsumsi saat ini :
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border-right: 1px solid black;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" onclick="return false;"
                                    {{ $dataAnestesi->obat_dikonsumsi == 'Tidak Ada' ? 'checked' : '' }}>
                                <label class="form-check-label" for="defaultCheck1">
                                    Tidak Ada
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border-right: 1px solid black; border-bottom: 1px solid black;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" onclick="return false;"
                                    {{ $dataAnestesi->obat_dikonsumsi == 'Ada' ? 'checked' : '' }}>
                                <label class="form-check-label" for="defaultCheck1">
                                    Ada {{ $dataAnestesi->obat_dikonsumsi_ket?  $dataAnestesi->obat_dikonsumsi_ket:'-' }}
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black;">Riwayat Alergi</td>
                        <td >: {{ $dataAnestesi->riwayat_alergi }} {{ $dataAnestesi->riwayat_alergi_ket }}</td>
                        <td>Riwayat Merokok</td>
                        <td style="border-right: 1px solid black;">: {{ $dataAnestesi->riwayat_merokok }}</td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black;">Riwayat Penyakit</td>
                        <td colspan="3" style="border-right: 1px solid black;">: {{ $dataAnestesi->riwayat_penyakit }} {{ $dataAnestesi->riwayat_penyakit_ket }}</td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black; border-bottom: 1px solid black">Riwayat Anestesi</td>
                        <td style="border-bottom: 1px solid black">: {{ $dataAnestesi->riwayat_anestesi }} {{ $dataAnestesi->jenis_anestesi }}</td>
                        <td style="border-bottom: 1px solid black">Komplikasi Anestesi</td>
                        <td style="border-right: 1px solid black; border-bottom: 1px solid black">: {{ $dataAnestesi->komplikasi_anestesi }}</td>
                    </tr>
                    <tr style="vertical-align: top;">
                        <td style="border-left:1px solid black;border-right: 1px solid black;" colspan="4">
                            Pemeriksaan Fisik :
                        </td>
                    </tr>
                    <tr style="vertical-align: top;">
                        <td style="border-left:1px solid black;">B1/Breathing : {{ $dataAnestesi->fisik_b1 }}</td>
                        <td>alat pembebas jalan napas</td>
                        <td>: {{ $dataAnestesi->fisik_alat }}</td>
                        <td style="border-right: 1px solid black;">RR : {{ $dataAnestesi->fisik_rr }} X/menit </td>
                    </tr>
                    <tr style="vertical-align: top;">
                        <td style="border-left:1px solid black;">Vesikuler : {{ $dataAnestesi->fisik_vesikuler }}</td>
                        <td>Rhonki : {{ $dataAnestesi->fisik_rhonki }}</td>
                        <td>Wheezing</td>
                        <td style="border-right: 1px solid black;">: (+){{ $dataAnestesi->fisik_wheezing_plus }} (-){{ $dataAnestesi->fisik_wheezing_min }}</td>
                    </tr>
                    <tr style="vertical-align: top;">
                        <td style="border-left:1px solid black;">B2/Blood : TD : {{ $dataAnestesi->fisik_td }}</td>
                        <td>, HR : {{ $dataAnestesi->fisik_hr }} {{ $dataAnestesi->fisik_hr_ket }}</td>
                        <td>, {{ $dataAnestesi->fisik_hr_ket }}</td>
                        <td style="border-right: 1px solid black;">Konjingtiva: {{ $dataAnestesi->fisik_konjungtiva }}</td>
                    </tr>
                    <tr style="vertical-align: top;">
                        <td style="border-left:1px solid black;">B3/Brain : GCS E:{{ $dataAnestesi->fisik_gcse }}</td>
                        <td>M: {{ $dataAnestesi->fisik_gcsm }}   V: {{ $dataAnestesi->fisik_gcsv }}</td>
                        <td>Pupil: {{ $dataAnestesi->fisik_pupil }}</td>
                        <td style="border-right: 1px solid black;">Hemiparese : {{ $dataAnestesi->fisik_hemiparese }} X/menit </td>
                    </tr>
                    <tr style="vertical-align: top;">
                        <td style="border-left:1px solid black;">B4/Badder : Produksi Urin:{{ $dataAnestesi->fisik_urin }} cc/jam</td>
                        <td style="border-right: 1px solid black;" colspan="3">, Warna Urine : {{ $dataAnestesi->fisik_warnaurin }}</td>
                    </tr>
                    <tr style="vertical-align: top;">
                        <td style="border-left:1px solid black;">B5/Bowel : Perut Distensi/kembung:{{ $dataAnestesi->fisik_perut }}</td>
                        <td>Diare : {{ $dataAnestesi->fisik_diare }}</td>
                        <td style="border-right: 1px solid black;" colspan="2">Muntah : {{ $dataAnestesi->fisik_muntah }}</td>
                    </tr>
                    <tr style="vertical-align: top;">
                        <td style="border-bottom: 1px solid black; border-left: 1px solid black;">B6/Bone : Alat Bantu Jalan:{{ $dataAnestesi->fisik_alatbantu }} cc/jam</td>
                        <td style="border-bottom: 1px solid black; border-right: 1px solid black;" colspan="3">Fraktur : {{ $dataAnestesi->fisik_fraktur }}</td>
                    </tr>
                    <tr>
                        <td style="border-left:1px solid black;">Laboratorium</td>
                        <td colspan="3" style="border-right:1px solid black;">: {{ $dataAnestesi->penunjang_lab }}</td>
                    </tr>
                    <tr>
                        <td style="border-left:1px solid black;">Radiologi</td>
                        <td colspan="3" style="border-right:1px solid black;">: {{ $dataAnestesi->penunjang_rad }}</td>
                    </tr>
                    <tr>
                        <td style="border-left:1px solid black; border-bottom: 1px solid black;">Elektrokardiografi</td>
                        <td colspan="3" style="border-bottom:1px solid black; border-right:1px solid black;">: {{ $dataAnestesi->penunjang_elektro }}</td>
                    </tr>
                    <tr>
                        <td style="border-left:1px solid black;">Status Fisik</td>
                        <td colspan="3" style="border-right:1px solid black;">:</td>
                    </tr>
                    <tr>
                        <td class="text-center" colspan="4" style="border-left:1px solid black; border-right:1px solid black; border-bottom: 1px solid black;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="white-space: nowrap;">
                                        <label><input type="checkbox" onclick="return false;" {{ $dataAnestesi->asa == 'true' ? 'checked' : '' }}> ASA</label>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <label><input type="checkbox" onclick="return false;" {{ $dataAnestesi->asa1 == 'true' ? 'checked' : '' }}> ASA 1</label>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <label><input type="checkbox" onclick="return false;" {{ $dataAnestesi->asa2 == 'true' ? 'checked' : '' }}> ASA 2</label>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <label><input type="checkbox" onclick="return false;" {{ $dataAnestesi->asa3 == 'true' ? 'checked' : '' }}> ASA 3</label>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <label><input type="checkbox" onclick="return false;" {{ $dataAnestesi->asa4 == 'true' ? 'checked' : '' }}> ASA 4</label>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <label><input type="checkbox" onclick="return false;" {{ $dataAnestesi->asa5 == 'true' ? 'checked' : '' }}> ASA 5</label>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <label><input type="checkbox" onclick="return false;" {{ $dataAnestesi->asa6 == 'true' ? 'checked' : '' }}> ASA 6</label>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <label><input type="checkbox" onclick="return false;" {{ $dataAnestesi->asaE == 'true' ? 'checked' : '' }}> E</label>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: center; border-left: 1px solid black; border-right: 1px solid black;">PERENCANA TINDAKAN ANESTESI</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="border-left: 1px solid black; border-right: 1px solid black;">Rencana Tindakan Anestesi</td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black;">GA</td>
                        <td colspan="3" style="border-right: 1px solid black;">: {{ $dataAnestesi->rencana_ga }}</td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black;">Regional</td>
                        <td colspan="3" style="border-right: 1px solid black;">: {{ $dataAnestesi->rencana_reg }}</td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black;">Blok</td>
                        <td colspan="3" style="border-right: 1px solid black;">: {{ $dataAnestesi->rencana_blok }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="border-left: 1px solid black; border-right: 1px solid black;">Alat / bahan khusus yang diperlukan ( obat-obatan dan cairan) :</td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black;">Obat - obatan</td>
                        <td colspan="3" style="border-right: 1px solid black;">: {{ $dataAnestesi->obat_obatan }} {{ $dataAnestesi->obat_obatan_ket }}</td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black; border-bottom: 1px solid black; padding-bottom: 24pt;">Cairan</td>
                        <td colspan="3" style="border-right: 1px solid black; border-bottom: 1px solid black; padding-bottom: 24pt;">: {{ $dataAnestesi->cairan }} {{ $dataAnestesi->cairan_ket }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="border-left: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; ">Prosedur monitoring khusus saat tindakan anestesi :</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black;">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" onclick="return false;"
                                    {{ $dataAnestesi->monitoring_khusus == 'Tidak' ? 'checked' : '' }}>
                                <label class="form-check-label" for="defaultCheck1">
                                    Tidak
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" onclick="return false;"
                                    {{ $dataAnestesi->monitoring_khusus == 'Ya' ? 'checked' : '' }}>
                                <label class="form-check-label" for="defaultCheck1">
                                    Ya
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="border-left: 1px solid black; border-right: 1px solid black;">Rencana perawatan setelah tindakan :</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="white-space: nowrap;">
                                        <label>
                                            <input type="checkbox" onclick="return false;" {{ $dataAnestesi->rencana_perawatan_inap == 'true' ? 'checked' : '' }}>
                                            Rawat Inap
                                        </label>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <label>
                                            <input type="checkbox" onclick="return false;" {{ $dataAnestesi->rencana_hcu == 'true' ? 'checked' : '' }}>
                                            HCU
                                        </label>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <label>
                                            <input type="checkbox" onclick="return false;" {{ $dataAnestesi->rencana_icu == 'true' ? 'checked' : '' }}>
                                            ICU/PICU/NICU
                                        </label>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <label>
                                            <input type="checkbox" onclick="return false;" {{ $dataAnestesi->rencana_rajal == 'true' ? 'checked' : '' }}>
                                            Rawat Jalan
                                        </label>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <label>
                                            <input type="checkbox" onclick="return false;" {{ $dataAnestesi->rencana_igd == 'true' ? 'checked' : '' }}>
                                            IGD
                                        </label>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table style="border: 1px solid black;" style="width: 100%;">
                <tr>
                    <td style="width: 50%"></td>
                    <td style="width: 50%; text-align:center;">
                        Surakarta,
                        {{ \Carbon\Carbon::parse($dataAnestesi->tanggal)->locale('id')->isoFormat('D MMMM Y') }}<br>
                        Dokter Anestesi
                    </td>
                </tr>
                <tr>
                    @php
                        $qr_dokter =
                            'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara
                                    elektronik oleh' .
                            "\n" .
                            $dataAnestesi->nm_dokter .
                            "\n" .
                            'ID ' .
                            $dataAnestesi->kd_dokter .
                            "\n" .
                            \Carbon\Carbon::parse(isset($dataAnestesi->tanggal)?$dataAnestesi->tanggal:\Carbon\Carbon::now())->format('d-m-Y');

                        $qrcode_dokter= base64_encode(
                            QrCode::format('png')->size(100)->errorCorrection('H')->generate($qr_dokter)
                        );

                    @endphp
                    <td style="width: 50%"></td>
                    <td style="text-align:center;"> <img src="data:image/png;base64, {!! $qrcode_dokter !!}">
                    </td>

                </tr>
                <tr>
                    <td style="width: 50%"></td>
                    <td style="text-align:center;"> {{ $dataAnestesi->kd_dokter }}</td>
                </tr>
                <tr>
                    <td style="width: 50%"></td>
                    <td style="text-align:center;"> {{ $dataAnestesi->nm_dokter }} </td>
                </tr>
            </table>
        </div>
    @endif

    @if($dataAnestesi2)
        <div style="float: none;">
            <div style="page-break-after: always;"></div>
        </div>
        <div class="watermark">
            {{ $watermark }}
        </div>
        <div>
            <img src="{{ asset('image/kop.png') }}" alt="KOP RSUP">
            <table class="table table-borderless table-sm mb-2" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="align-middle text-center pb-1" colspan="2" rowspan="6" style="width: 40%; border: 1px solid black; font-size: 14pt;">
                            <h5><b>ASESMEN PRA INDUKSI</b></h5>
                        </th>
                        <td style="width: 20%; border-top: 1px solid black;">
                            No. Rawat
                        </td>
                        <td style="width: 40%; border-top: 1px solid black; border-right: 1px solid black;">: {{ $dataAnestesi2->no_rawat }}</td>
                    </tr>
                    <tr>
                        <td>
                            No. Rekam Medis
                        </td>
                        <td style="border-right: 1px solid black;">: {{ $dataAnestesi2->no_rkm_medis }}</td>
                    </tr>
                    <tr>
                        <td>
                            Nama Pasien
                        </td>
                        <td style="border-right: 1px solid black;">: {{ $dataAnestesi2->nm_pasien }}/ Th/ {{ $dataAnestesi2->jk == 'L'? 'Laki-laki':'Perempuan' }}</td>
                    </tr>
                    <tr>
                        <td>
                            Tanggal Lahir
                        </td>
                        <td style="border-right: 1px solid black;">: {{ \Carbon\Carbon::parse($dataAnestesi2->tgl_lahir)->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <td>
                            Alamat
                        </td>
                        <td style="border-right: 1px solid black;">: {{ $dataAnestesi2->alamat }}, {{ $dataAnestesi2->kelurahan }}, {{ $dataAnestesi2->kecamatan }}, {{ $dataAnestesi2->kabupaten }}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid black; ">
                            Ruang Rawat
                        </td>
                        <td style="border-bottom: 1px solid black; border-right: 1px solid black;">: {{ $dataAnestesi2->nm_bangsal }}</td>
                    </tr>
                </thead>
            </table>
            <table class="table table-borderless table-sm" style="width: 100%;">
                <tbody>
                    <tr >
                        <td colspan="4" style="border-left: 1px solid black; border-top: 1px solid black; border-right: 1px solid black;" >Keadaan Prainduksi :</td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black;">BB : {{ $dataAnestesi2->bb }} Kg</td>
                        <td>TB : {{ $dataAnestesi2->tb }} Cm</td>
                        <td>Gol. Darah : {{ $dataAnestesi2->gol_darah }} </td>
                        <td style="border-right: 1px solid black;">HB : {{ $dataAnestesi2->hb }} </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border-left: 1px solid black;">TD : {{ $dataAnestesi2->td }} mmHg</td>
                        <td>Suhu : {{ $dataAnestesi2->suhu }} C</td>
                        <td style="border-right: 1px solid black;">VAS : {{ $dataAnestesi2->vas }} </td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black;">Nadi : {{ $dataAnestesi2->nadi }} x/mnt</td>
                        <td>Respirasi : {{ $dataAnestesi2->respirasi }} x/mnt</td>
                        <td>GCS : {{ $dataAnestesi2->gcs }} </td>
                        <td style="border-right: 1px solid black;">Ht : {{ $dataAnestesi2->ht }} </td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black;">Alergi :
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" onclick="return false;"
                                    {{ $dataAnestesi2->alergi == 'false' ? 'checked' : '' }}>
                                <label class="form-check-label" for="defaultCheck1">
                                    Tidak
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" onclick="return false;"
                                    {{ $dataAnestesi2->alergi == 'true' ? 'checked' : '' }}>
                                <label class="form-check-label" for="defaultCheck1">
                                    Ya
                                </label>
                            </div>
                        </td>
                        <td>{{ $dataAnestesi2->alergi_ket }}</td>
                        <td>Rh : {{ $dataAnestesi2->rh }} </td>
                        <td style="border-right: 1px solid black;">Lain : {{ $dataAnestesi2->lain }} </td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black; border-top: 1px solid black;">Pemeriksaan Fisik</td>
                        <td colspan="3" style="border-right: 1px solid black; border-top: 1px solid black;">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" onclick="return false;"
                                    {{ $dataAnestesi2->fisik_bukamulut == 'true' ? 'checked' : '' }}>
                                <label class="form-check-label" for="defaultCheck1">
                                    Buka Mulut > 2 Jari
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black;">Jalan Nafas :
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" onclick="return false;"
                                    {{ $dataAnestesi2->fisik_normal == 'true' ? 'checked' : '' }}>
                                <label class="form-check-label" for="defaultCheck1">
                                    Normal
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" onclick="return false;"
                                    {{ $dataAnestesi2->fisik_jarak == 'true' ? 'checked' : '' }}>
                                <label class="form-check-label" for="defaultCheck1">
                                    Jarak Thyromental > 3 Jam
                                </label>
                            </div>
                        </td>
                        <td colspan="2" style="border-right: 1px solid black;">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" onclick="return false;"
                                    {{ $dataAnestesi2->fisik_abnormal == 'true' ? 'checked' : '' }}>
                                <label class="form-check-label" for="defaultCheck1">
                                    Abnormal
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black;"></td>
                        <td colspan="2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" onclick="return false;"
                                    {{ $dataAnestesi2->fisik_mallampati == 'true' ? 'checked' : '' }}>
                                <label class="form-check-label" for="defaultCheck1">
                                    Mallampati I / II / III / IV
                                </label>
                            </div>
                        </td>
                        <td style="border-right: 1px solid black;"></td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black;"></td>
                        <td colspan="2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" onclick="return false;"
                                    {{ $dataAnestesi2->fisik_gerakanleher == 'true' ? 'checked' : '' }}>
                                <label class="form-check-label" for="defaultCheck1">
                                    Gerakan Leher Maksimal
                                </label>
                            </div>
                        </td>
                        <td style="border-right: 1px solid black;"></td>
                    </tr>
                    <tr>
                        <td style="border-top: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; white-space: nowrap;" colspan="4">
                            Anamnesis:
                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $dataAnestesi2->anamnesis_auto == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        Auto
                                    </label>
                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $dataAnestesi2->anamnesis_allo == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        Allo
                                    </label>
                        </td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black; border-top: 1px solid black;">Status Fisik Asa</td>
                        <td class="text-center"  style="border-top: 1px solid black; white-space: nowrap;">
                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $dataAnestesi2->asa1 == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        1
                                    </label>

                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $dataAnestesi2->asa2 == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        2
                                    </label>

                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $dataAnestesi2->asa3 == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        3
                                    </label>

                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $dataAnestesi2->asa4 == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        4
                                    </label>

                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $dataAnestesi2->asa5 == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        5
                                    </label>

                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $dataAnestesi2->asaE == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        E
                                    </label>
                        </td>
                        <td style="border-top: 1px solid black;border-right: 1px solid black;" colspan="2">Penyulit Praanestesi : {{ $dataAnestesi2->penyulit_praanestesi }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="border-left: 1px solid black;border-top: 1px solid black;border-right: 1px solid black;">Checklist Sebelum Induksi :</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="border-left:1px solid black; border-right:1px solid black; border-bottom: 1px solid black; white-space: nowrap;">

                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $dataAnestesi2->cek_1 == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        Ijin Operasi
                                    </label>

                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $dataAnestesi2->cek_2 == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        Cek Mohon Anestesi
                                    </label>

                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $dataAnestesi2->cek_3 == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        Cek Suction Unit
                                    </label>

                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $dataAnestesi2->cek_4 == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        Persiapan Obat-obatan
                                    </label>

                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $dataAnestesi2->cek_5 == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        Antibiotika Profilaksis
                                    </label>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="border-left:1px solid black;border-right:1px solid black;">Teknik Anestesi</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="border-left:1px solid black; border-right:1px solid black; white-space: nowrap;">

                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $dataAnestesi2->ga_1 == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        GA
                                    </label>

                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $dataAnestesi2->ga_2 == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        TIVA
                                    </label>

                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $dataAnestesi2->ga_3 == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        LMA

                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $dataAnestesi2->ga_4 == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        FACEMASK
                                    </label>

                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $dataAnestesi2->ga_5 == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        ET
                                    </label>

                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="border-left:1px solid black; border-right:1px solid black;white-space: nowrap;">

                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $dataAnestesi2->reg_1 == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        Regional
                                    </label>

                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $dataAnestesi2->reg_2 == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        Spinal
                                    </label>

                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $dataAnestesi2->reg_3 == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        Epidural
                                    </label>

                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $dataAnestesi2->reg_4 == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        Kaudal
                                    </label>

                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $dataAnestesi2->reg_5 == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        Blok Saraf Tepi
                                    </label>

                        </td>
                    </tr>
                    <tr>
                        <td style="border-left:1px solid black; border-bottom: 1px solid black;white-space: nowrap;">

                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $dataAnestesi2->anestesi_lain == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        Lain-lain
                                    </label>

                        </td>
                        <td colspan="3" style="border-right:1px solid black; border-bottom: 1px solid black;">{{ $dataAnestesi2->anestesi_lain_ket }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="border-left: 1px solid black;border-right: 1px solid black; ">Monitoring :</td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black;">
                            <div style="display: inline-flex; gap: 20px;">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $dataAnestesi2->monitoring_1 == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        EKG Lead
                                    </label>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="display: inline-flex; gap: 20px;">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $dataAnestesi2->monitoring_2 == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        SPO2 %
                                    </label>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="display: inline-flex; gap: 20px;">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $dataAnestesi2->monitoring_3 == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        TD {{ $dataAnestesi2->monitoring_3_ket }} mmHg
                                    </label>
                                </div>
                            </div>
                        </td>
                        <td style="border-right: 1px solid black;">
                            <div style="display: inline-flex; gap: 20px;">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $dataAnestesi2->monitoring_4 == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        Temp {{ $dataAnestesi2->monitoring_4_ket }}
                                    </label>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black;">
                            <div style="display: inline-flex; gap: 20px;">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $dataAnestesi2->monitoring_5 == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        CVC  mmHg
                                    </label>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="display: inline-flex; gap: 20px;">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $dataAnestesi2->monitoring_6 == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        PCO %
                                    </label>
                                </div>
                            </div>
                        </td>
                        <td colspan="2" style="border-right: 1px solid black;">
                            <div style="display: inline-flex; gap: 20px;">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $dataAnestesi2->monitoring_7 == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        Urin Catheter
                                    </label>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border-left: 1px solid black; border-bottom: 1px solid black;">
                            <div style="display: inline-flex; gap: 20px;">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $dataAnestesi2->monitoring_8 == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        Stetoscop
                                    </label>
                                </div>
                            </div>
                        </td>
                        <td colspan="2" style="border-right: 1px solid black; border-bottom: 1px solid black;">
                            <div style="display: inline-flex; gap: 20px;">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" onclick="return false;"
                                        {{ $dataAnestesi2->monitoring_9 == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultCheck1">
                                        NGT
                                    </label>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table style="border: 1px solid black;" style="width: 100%;">
                <tr>
                    <td style="width: 50%"></td>
                    <td style="width: 50%; text-align:center;">
                        Surakarta,
                        {{ \Carbon\Carbon::parse($dataAnestesi2->tanggal)->locale('id')->isoFormat('D MMMM Y') }}<br>
                        Dokter Anestesi
                    </td>
                </tr>
                <tr>
                    @php
                        $qr_dokter =
                            'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara
                                    elektronik oleh' .
                            "\n" .
                            $dataAnestesi2->nm_dokter .
                            "\n" .
                            'ID ' .
                            $dataAnestesi2->kd_dokter .
                            "\n" .
                            \Carbon\Carbon::parse(isset($dataAnestesi2->tanggal)?$dataAnestesi2->tanggal:\Carbon\Carbon::now())->format('d-m-Y');

                        $qrcode_dokter= base64_encode(
                            QrCode::format('png')->size(100)->errorCorrection('H')->generate($qr_dokter)
                        );

                    @endphp
                    <td style="width: 50%"></td>
                    <td style="text-align:center;"> <img src="data:image/png;base64, {!! $qrcode_dokter !!}">
                    </td>

                </tr>
                <tr>
                    <td style="width: 50%"></td>
                    <td style="text-align:center;"> {{ $dataAnestesi2->kd_dokter }}</td>
                </tr>
                <tr>
                    <td style="width: 50%"></td>
                    <td style="text-align:center;"> {{ $dataAnestesi2->nm_dokter }} </td>
                </tr>
            </table>
        </div>
        @endif

    {{-- </main> --}}
    {{-- <footer>
        Dicetak dari Vedika@BiosGateRSUP pada {{ \Carbon\Carbon::now()->format('d/m/Y h:i:s') }}
    </footer> --}}
</body>

</html>
