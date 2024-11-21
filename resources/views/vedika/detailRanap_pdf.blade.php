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
                            {{ \Carbon\Carbon::parse($resumeRanap2->first()->waktu_masuk_ranap)->format('d-m-Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-left: 5pt;">Alamat</td>
                        <td>: {{ $pasien->alamat }}</td>
                        <td>Tanggal Keluar</td>
                        <td style="margin-right:10pt;">:
                            {{ \Carbon\Carbon::parse($resumeRanap2->last()->waktu_keluar_ranap)->format('d-m-Y') }}
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
                    if ($hasilRadiologiRajal) {
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
                        ">{{ $hasilRadiologiRajal != null ? $hasilRadiologiRajal[$urutan]->hasil : '' }}</textarea>
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
                } else {
                    $plan = $sekunder->plan;
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
                                {{ $sekunder->anamnesa_singkat }}
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
            {{ $primer != null ? $primer->plan : $sekunder->plan }}
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
            {{ $primer != null ? \Carbon\Carbon::parse($primer->tanggaltriase)->format('d-m-Y H:i:s') : \Carbon\Carbon::parse($sekunder->tanggaltriase)->format('d-m-Y H:i:s') }}
        </td>
    </tr>
    <tr>
        <td style="border:1px solid black;" colspan="3">
            Catatan
        </td>
        <td style="border:1px solid black;" colspan="7">
            {{ $primer != null ? $primer->catatan : $sekunder->catatan }}
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
                } else {
                    $nip_petugas = $sekunder->nip;
                    $nama_petugas = $sekunder->nama;
                    $tanggal_hasil = \Carbon\Carbon::parse($sekunder->tanggaltriase)->format('d-m-Y');
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
                {{ $primer != null ? $primer->nama : $sekunder->nama }}
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
                        <th
                            style="width: 15%; border-left:1px solid black; border-top: 1px solid black;text-align:left;">
                            No.
                            RM
                        </th>
                        <th style="border-right: 1px solid black; border-top: 1px solid black;text-align:left;">:
                            {{ $dataRingkasan->no_rkm_medis }}</th>
                    </tr>
                    <tr>
                        <th style="border-left: 1px solid black; text-align:left;">NIK </th>
                        <th style="border-right: 1px solid black; text-align:left;">
                            : {{ $dataRingkasan->no_ktp }}</th>
                    </tr>
                    <tr>
                        <th style="border-left: 1px solid black; text-align:left;">Nama Pasien </th>
                        <th style="border-right: 1px solid black; text-align:left;">
                            : {{ $dataRingkasan->nm_pasien }}</th>
                    </tr>
                    <tr>
                        <th style="border-left: 1px solid black; text-align:left;">Tanggal Lahir </th>
                        <th style="border-right: 1px solid black; text-align:left;">
                            : {{ $dataRingkasan->tgl_lahir }}</th>
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
                            style="border-left: 1px solid black; border-right: 1px solid black; padding-left:20px"colspan="3">
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
                                {{ $listOperasi->permintaan_pa }}
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
                                {{ $listOperasi->diagnosa_preop }}
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
                                {{ $listOperasi->jaringan_dieksekusi }}
                            </td>
                            <td style="border-left: 1px solid black; text-align:center;">
                                {{ \Carbon\Carbon::parse($listOperasi->selesaioperasi)->format('d/m/Y H:i:s') }}
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
                                {{ $listOperasi->diagnosa_postop }}
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
                            $draf = preg_split('/\r\n|\r|\n/', $listOperasi->laporan_operasi);
                            $qr_dokter =
                                'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara
                                        elektronik oleh' .
                                "\n" .
                                $dokterOperator .
                                "\n" .
                                'ID ' .
                                $listOperasi->operator1 .
                                "\n" .
                                \Carbon\Carbon::parse($listOperasi->selesaioperasi)->format('d-m-Y');

                            $qrcode_petugas = base64_encode(
                                QrCode::format('png')->size(100)->errorCorrection('H')->generate($qr_dokter)
                            );
                        @endphp
                        <tr>
                            <td style="padding-left:20px;" colspan="5">
                                @foreach ($draf as $laporan)
                                    {{ $laporan }}<br>
                                @endforeach
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

    {{-- </main> --}}
    {{-- <footer>
        Dicetak dari Vedika@BiosGateRSUP pada {{ \Carbon\Carbon::now()->format('d/m/Y h:i:s') }}
    </footer> --}}
</body>

</html>
