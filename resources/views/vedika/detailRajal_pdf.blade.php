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
        //dd($billing,$permintaanLab,$dataRadiologiRajal,$resepObat, $dataTriase, $resumeIgd);
        $statusVerif = App\VedikaVerif::cekVerif($pasien->no_rawat, 'Rajal');
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
    {{-- Data Eklaim --}}
    @if (!empty($dataKlaim))
        <div class="watermark">
            {{ $watermark }}
        </div>
        <div>
            <table style="width: 100%">
                <tr>
                    <td style="width: 5%; border-bottom: 3px solid black;; " rowspan="3">
                        <img src="{{ asset('image/LogoKemenkesIcon.png') }}" alt="Logo Kemenkes" width="80" />
                    </td>
                    <td rowspan="3" style="border-bottom: 3px solid black;; width: 90%;">
                        <b>KEMENTERIAN KESEHATAN REPUBLIK INDONESIA</b><br>
                        <i>Berkas Klaim Individual Pasien</i>
                    </td>
                    <td rowspan="3"
                        style="border-bottom: 3px solid black;; width: 5%;">
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
                        style="width: 20%;padding: 0; padding-left:5px; padding-bottom:10px;border-bottom: 1px solid black;; vertical-align:top;">
                        Nama RS</td>
                    <td
                        style="width: 30%;padding: 0; padding-bottom:10px;border-bottom: 1px solid black;; vertical-align:top;">
                        : RSU PUSAT
                        SURAKARTA</td>
                    <td
                        style="width: 20%;padding: 0; padding-bottom:10px;border-bottom: 1px solid black;; vertical-align:top;">
                        Jenis Tarif
                    </td>
                    <td
                        style="width: 30%;padding: 0; padding-bottom:10px;border-bottom: 1px solid black;; vertical-align:top;">
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
                        style="width: 20%;padding: 0; padding-left:5px; padding-bottom:10px; border-bottom: 1px solid black;;vertical-align:top;">
                        Kelas Perawatan</td>
                    <td
                        style="width: 30%;padding: 0; padding-bottom:10px; border-bottom: 1px solid black;;vertical-align:top;">
                        : {{ $dataKlaim->kelas_rawat }} - Kelas {{ $dataKlaim->kelas_rawat }}</td>
                    <td
                        style="width: 20%;padding: 0; padding-bottom:10px; border-bottom: 1px solid black;;vertical-align:top;">
                        Berat Lahir</td>
                    <td
                        style="width: 30%;padding: 0; padding-bottom:10px; border-bottom: 1px solid black;;vertical-align:top;">
                        :
                        {{ $dataKlaim->berat_lahir == '0' ? '-' : $dataKlaim->berat_lahir }}</td>
                </tr>
            </table>
            <table style="width: 100%">
                @php
                    $diagnosaKlaim = explode('#', $dataKlaim->diagnosa_inagrouper);
                    $procedureKlaim = explode('#', $dataKlaim->procedure_inagrouper);
                @endphp
                <tr>
                    <td
                        style="width: 20%; padding-top: 10px; padding-left:5px; padding-bottom:0px;vertical-align:top;">
                        Diagnosa Utama</td>
                    <td style="width: 5%;padding: 0;padding-top: 10px;vertical-align:top;">: {{ $diagnosaKlaim[0] }}
                    </td>
                    <td style="width: 75%;padding: 0;padding-top: 10px;vertical-align:top;" colspan=2>
                        {{ \App\Penyakit::getName($diagnosaKlaim[0]) }}</td>
                </tr>
                <tr>
                    <td style="width: 20%;  padding-left:5px; padding-bottom:0px;vertical-align:top;">
                        Diagnosa Sekunder</td>
                    @for ($i = 1; $i < count($diagnosaKlaim); $i++)
                        <td style="width: 10%;padding: 0;vertical-align:top;">: {{ $diagnosaKlaim[$i] }}
                        </td>
                        <td style="width: 70%;padding: 0;vertical-align:top;" colspan=2>
                            {{ \App\Penyakit::getName($diagnosaKlaim[$i]) }}</td>
                </tr>
                <tr>
                    <td style="width: 20%;  padding-left:5px; padding-bottom:0px;vertical-align:top;">
                    </td>
        @endfor
        <td colspan=2></td>
        @for ($j = 0; $j < count($procedureKlaim); $j++)
            <tr>
                <td style="width: 20%; ; padding-left:5px; padding-bottom:0px;vertical-align:top;">
                    {{ $j == 0 ? 'Prosedur' : '' }}</td>
                <td style="width: 10%;padding: 0;vertical-align:top;">: {{ $procedureKlaim[$j] }}
                </td>
                <td style="width: 70%;padding: 0;vertical-align:top;" colspan=2>
                    {{ \App\Penyakit::getProcedure($procedureKlaim[$j]) }}</td>
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
                <td colspan=5 style='font-weight: bold; padding-left:5px; border-bottom: 1px solid black;'>Hasil Grouping
                </td>
            </tr>
            <tr>
                <td style="width: 20%;padding: 0; padding-left:5px; padding-top:10px; vertical-align:top;">
                    INA-CBG</td>
                <td style="width: 20%;padding: 0; padding-top:10px; vertical-align:top;">
                    :
                    {{ $dataKlaim->grouper->response_inacbg ? $dataKlaim->grouper->response_inacbg->cbg->code : '' }}
                </td>
                <td style="width: 50%;padding: 0; padding-top:10px; vertical-align:top;">
                    {{ $dataKlaim->grouper->response_inacbg ? $dataKlaim->grouper->response_inacbg->cbg->description : '' }}
                </td>
                <td style="width: 5%;padding: 0; padding-top:10px; text-align:right; vertical-align:top;">
                    Rp</td>
                <td style="width: 15%;padding: 0; padding-top:10px; text-align:right; vertical-align:top;">
                    {{ isset($dataKlaim->grouper->response_inacbg->cbg->tariff) ? number_format($dataKlaim->grouper->response_inacbg->cbg->tariff, 2, ',', '.') : number_format(0, 2, ',', '.') }}
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
                    style="width: 15%;padding: 0; padding-left:5px; padding-bottom:10px; border-bottom: 1px solid black;; vertical-align:top;">
                    Special CMG</td>
                <td style="width: 15%;padding: 0; padding-bottom:10px; border-bottom: 1px solid black;;vertical-align:top;">
                    : - </td>
                <td
                    style="width: 50%;padding: 0; padding-bottom:10px; border-bottom: 1px solid black;; vertical-align:top;">
                    -</td>
                <td
                    style="width: 10%;padding: 0; padding-bottom:10px; border-bottom: 1px solid black;; text-align:right;vertical-align:top;">
                    Rp</td>
                <td
                    style="width: 10%;padding: 0; padding-bottom:10px; border-bottom: 1px solid black;; text-align:right;vertical-align:top;">
                    {{ number_format(0, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td
                    style="width: 15%;padding: 0; padding-left:5px; padding-top:10px; padding-bottom:50px; border-bottom: 3px solid black;">
                    Total Tarif</td>
                <td style="width: 15%;padding: 0; padding-top:10px; padding-bottom:50px; border-bottom: 3px solid black;">
                    : </td>
                <td style="width: 50%;padding: 0; padding-top:10px; padding-bottom:50px; border-bottom: 3px solid black;">
                </td>
                <td
                    style="width: 10%;padding: 0; padding-top:10px; text-align:right; padding-bottom:50px; border-bottom: 3px solid black;">
                    Rp</td>
                <td
                    style="width: 10%;padding: 0; padding-top:10px; text-align:right; padding-bottom:50px; border-bottom: 3px solid black;">
                    {{ isset($dataKlaim->grouper->response_inacbg->cbg->tariff) ? number_format($dataKlaim->grouper->response_inacbg->cbg->tariff, 2, ',', '.') : number_format(0, 2, ',', '.') }}
                </td>

            </tr>
            <tr>
                <td style="width: 15%;padding: 0; padding-left:5px; font:grey">
                    Generated</td>
                <td style="width: 75%;padding: 0;" colspan='3'>
                    : Eklaim
                    {{ $dataKlaim->grouper->response_inacbg ? $dataKlaim->grouper->response_inacbg->inacbg_version : '' }}
                    @
                    {{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}</td>
                <td style="width: 10%;padding: 0; text-align:right">
                    Lembar 1 / 1</td>
            </tr>
        </table>
        </div>
        <div class="watermark">
                    {{ $watermark }}
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
                    <td style="width:25%; border:0pt solid black;; vertical-align: top; padding-top:5pt" rowspan="2"><img src="{{ asset('image/logoBPJS.png') }}"
                            alt="Logo BPJS" width="250" style="border:0pt solid black;; vertical-align: top">
                    </td>
                    <td style=" border:0pt solid black;; width:40%">
                        <div style="padding-top: 0pt; padding-bottom:0pt; vertical-align:bottom; margin-top:0pt; margin-left:5pt; font-size:14pt">SURAT ELIGIBILITAS PESERTA</div>
                    </td>
                    <td style=" border:0pt solid black;; width:35%; vertical-align:top;" rowspan="3">
                        <div style="font-size:12pt; margin-left:5pt">{{ $dataSep->prb }}</div>
                    </td>
                </tr>
                <tr>
                    <td style=" border:0pt solid black;;">
                        <div style="padding-top: 2pt; padding-bottom:0pt; vertical-align:top;margin-left:5pt; font-size:12pt">RSUP SURAKARTA</div>
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
                    <td colspan="2"></td>
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
                            <td><div style="margin-left:15pt">Kelamin :
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
                    <td  style="vertical-align:top;">Penjamin</td>
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
                    <td rowspan="3" >
                        <div style="margin-left:5pt;">Persetujuan</div>
                        <div style="margin-left:5pt;">Pasien/Keluarga Pasien</div>
                        <div style="margin-left:5pt; margin-top:5pt">
                            @php
                            $qrcode_pasien = base64_encode(
                                QrCode::format('png')->size(100)->errorCorrection('H')->generate($dataSep->no_kartu)
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
    $peserta = \app\Http\Controllers\SepController::peserta(
    $dataSep->peserta->noKartu,
    $dataSep->tglSep
    );
    $kontrol = \app\Http\Controllers\SepController::getSep2($dataSep->noSep);

    @endphp
    <div>
        <table style="margin-top: 15pt;">
            <tr>
                <td style="width:25%" rowspan="2"><img src="{{ asset('image/logoBPJS.png') }}"
                        alt="Logo BPJS" width="250"></td>
                    <td style=" border:0pt solid black;; width:40%">
                        <div style="padding-top: 0pt; padding-bottom:0pt; vertical-align:bottom; margin-top:0pt; margin-left:5pt; font-size:14pt">SURAT ELIGIBILITAS PESERTA</div>
                    </td>
                    <td style=" border:0pt solid black;; width:35%; vertical-align:top;" rowspan="3">
                        <div style="font-size:12pt; margin-left:5pt"></div>
                    </td>
                </tr>
                <tr>
                    <td style=" border:0pt solid black;;">
                        <div style="padding-top: 2pt; padding-bottom:0pt; vertical-align:top;margin-left:5pt; font-size:12pt">RSUP SURAKARTA</div>
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
                <td>Jns. Kunjungan</td>
                <td>:
                    @if ($dataSep->tujuanKunj->kode == '0')
                    - Konsultasi dokter(pertama)
                    @elseif ($dataSep->tujuanKunj->kode == '2')
                    - Kunjungan Kontrol(ulangan)
                    @endif

                </td>
            </tr>
            <tr>
                <td>No. Telepon</td>
                <td>: {{ $peserta->mr->noTelepon }}</td>
                <td></td>
                <td>
                    @if ($dataSep->flagProcedure->nama != null)
                    : - {{ $dataSep->flagProcedure->nama }}
                    @endif
                </td>
            </tr>
            <tr>
                <td>Sub/Spesialis</td>
                <td>: {{ $dataSep->poli }}</td>
                <td>Poli Perujuk</td>
                <td>: </td>
            </tr>
            <tr>
                <td>Dokter</td>
                <td>: {{ $dataSep->dpjp->nmDPJP }}</td>
                <td>Kls. Hak</td>
                <td>: Kelas {{ $dataSep->kelasRawat }}</td>
            </tr>
            <tr>
                <td>Faskes Perujuk</td>
                <td>: {{ $kontrol->provPerujuk->nmProviderPerujuk }}
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
                <td rowspan="3" >
                    <div style="margin-left:5pt;">Persetujuan</div>
                    <div style="margin-left:5pt;">Pasien/Keluarga Pasien</div>
                    <div style="margin-left:5pt; margin-top:5pt">
                        @php
                        $qrcode_pasien = base64_encode(
                            QrCode::format('png')->size(100)->errorCorrection('H')->generate($dataSep->peserta->noKartu)
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
                } elseif($sekunder) {
                    $plan = $sekunder->plan;
                }else{
                    $plan= '';
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

            <table style="width: 100%; border:0px solid black;">
                <thead>
                    <tr>
                        <td style="width: 15%; border:0px solid black;"></td>
                        <td style="width: 10%; border:0px solid black;"></td>
                        <td style="width: 10%; border:0px solid black;"></td>
                        <td style="width: 10%; border:0px solid black;"></td>
                        <td style="width: 10%; border:0px solid black;"></td>
                        <td style="width: 10%; border:0px solid black;"></td>
                        <td style="width: 10%; border:0px solid black;"></td>
                        <td style="width: 5%; border:0px solid black;"></td>
                        <td style="width: 10%; border:0px solid black;"></td>
                        <td style="width: 10%; border:0px solid black;"></td>
                    </tr>
                    <tr>
                        <td style="border:1px solid black;" rowspan="4"><img
                                src="{{ public_path('image/logorsup.jpg') }}" alt="Logo RSUP" width="100">
                        </td>
                        <td style="border-top:1px solid black;" colspan="5">
                            <div style="font-size: 25px; text-align:center">RSUP SURAKARTA</div>
                        </td>
                        <td style="border-left:1px solid black;;border-top:1px solid black;; vertical-align:top"
                            colspan="2">No.RM / NIK </td>
                        <td style="border-right:1px solid black;;border-top:1px solid black;; vertical-align:top"
                            colspan="2">:
                            {{ $dataTriase->no_rkm_medis }} /
                            {{ $dataTriase->no_ktp }} </td>
                    </tr>
                    <tr>
                        <td style="border:0px solid black;; text-align:center" colspan="5">Jl.Prof.Dr.R.Soeharso
                            No.28 , Surakarta,
                            Jawa Tengah</td>
                        <td style="border-left:1px solid black;; vertical-align:top" colspan="2">Nama</td>
                        <td style="border-right:1px solid black;; vertical-align:top" colspan="2">:
                            {{ $dataTriase->nm_pasien }}
                            ({{ $dataTriase->jk }}) </td>
                    </tr>
                    <tr>
                        <td style="border:0px solid black;; text-align:center" colspan="5">Telp.0271-713055 / 720002
                        </td>
                        <td style="border-left:1px solid black;; vertical-align:top" colspan="2">Tanggal Lahir</td>
                        <td style="border-right:1px solid black;; vertical-align:top" colspan="2">:
                            {{ \Carbon\Carbon::parse($dataTriase->tgl_lahir)->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom:1px solid black;; text-align:center" colspan="5">E-mail :
                            rsupsurakarta@kemkes.go.id</td>
                        <td style="border-left:1px solid black;; border-bottom:1px solid black;; vertical-align:top"
                            colspan="2" rowspan="2">
                            Alamat
                        </td>
                        <td style="border-right:1px solid black;; border-bottom:1px solid black;;vertical-align:top"
                            colspan="2" rowspan="2">:
                            {{ $dataTriase->alamat }}</td>
                    </tr>
                    <tr>
                        <td style="border:1px solid black;; text-align:center; {{ $bg_color }}" colspan="6">
                            TRIASE PASIEN GAWAT DARURAT
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center; border:1px solid black;;" colspan="10">
                            Triase dilakukan segera setelah pasien datang dan sebelum pasien/ keluarga
                            mendaftar
                            di TPP IGD
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border: 1px solid black;" colspan="5">
                            Tanggal Kunjungan :
                            {{ \Carbon\Carbon::parse($dataTriase->tgl_kunjungan)->format('d-m-Y') }}
                        </td>
                        <td style="border: 1px solid black;" colspan="5">
                            Pukul :
                            {{ \Carbon\Carbon::parse($dataTriase->tgl_kunjungan)->format('H:i:s') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black;" colspan="3">
                            Cara Datang
                        </td>
                        <td style="border: 1px solid black;" colspan="7">
                            {{ $dataTriase->cara_masuk }}
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black;" colspan="3">
                            Macam Kasus
                        </td>
                        <td style="border: 1px solid black;" colspan="7">
                            {{ $dataTriase->macam_kasus }}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center; background-color:lightskyblue; border:1px solid black;"
                            colspan="3">
                            KETERANGAN
                        </td>
                        <td style="text-align: center; background-color:lightskyblue; border:1px solid black;"
                            colspan="7">
                            {{ $primer != null ? 'TRIASE PRIMER' : 'TRIASE SEKUNDER' }}
                        </td>
                    </tr>
                    @if (!empty($primer))
                        <tr>
                            <td style="border: 1px solid black;" colspan="3">
                                KELUHAN UTAMA
                            </td>
                            <td style="border: 1px solid black;" colspan="7">
                                {{ $primer->keluhan_utama }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid black;" colspan="3">
                                TANDA VITAL
                            </td>
                            <td style="border: 1px solid black;" colspan="7">
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
                            <td style="border: 1px solid black;" colspan="3">
                                KEBUTUHAN KHUSUS
                            </td>
                            <td style="border: 1px solid black;" colspan="7">
                                {{ $primer->kebutuhan_khusus }}
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td style="border: 1px solid black;" colspan="3">
                                ANAMNESA SINGKAT
                            </td>
                            <td style="border: 1px solid black;" colspan="7">
                                {{ $sekunder ? $sekunder->anamnesa_singkat:'' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid black;; vertical-align:top" colspan="3">
                                TANDA VITAL
                            </td>
                            <td style="border: 1px solid black;" colspan="7">
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
                        <td style="border: 1px solid black;; background-color:lightskyblue; text-align:center"
                            colspan="3">
                            PEMERIKSAAN
                        </td>
                        <td style="border: 1px solid black;; text-align:center;{{ $bg_color }} " colspan="7">
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
                        <td style="border:1px solid black;;" colspan="3">
                            {{ $dataPemeriksaan->nama_pemeriksaan }}
                        </td>
                        <td style="border:1px solid black;;{{ $bg_color }}" colspan="7">
                            {{ $dataPemeriksaan->pengkajian_skala }}
                        @else
                            , {{ $dataPemeriksaan->pengkajian_skala }}
    @endif
    @endforeach
    @endfor
    </td>
    </tr>
    <tr>
        <td style="border:1px solid black;" colspan="3">
            PLAN
        </td>
        <td style="border:1px solid black;; {{ $bg_color }}" colspan="7">
            {{ $primer != null ? $primer->plan : '' }}
            {{ $sekunder != null ? $sekunder->plan : '' }}
        </td>
    </tr>
    <tr>
        <td style="border:1px solid black;;" colspan="3">

        </td>
        <td style="border:1px solid black;; background-color:lightskyblue" colspan="7">
            {{ $primer != null ? 'Petugas Triase Primer' : 'Petugas Triase Sekunder' }}
        </td>
    </tr>
    <tr>
        <td style="border:1px solid black;;" colspan="3">
            Tanggal & Jam
        </td>
        <td style="border:1px solid black;;" colspan="7">
            {{ $primer != null  ? \Carbon\Carbon::parse($primer->tanggaltriase)->format('d-m-Y H:i:s') : '' }}
            {{ $sekunder !=null ? \Carbon\Carbon::parse($sekunder->tanggaltriase)->format('d-m-Y H:i:s') : '' }}
        </td>
    </tr>
    <tr>
        <td style="border:1px solid black;;" colspan="3">
            Catatan
        </td>
        <td style="border:1px solid black;;" colspan="7">
            {{ $primer != null ? $primer->catatan : '' }}
            {{ $sekunder != null ? $sekunder->catatan:'' }}
        </td>
    </tr>
    <tr>
        <td style="border:1px solid black;;vertical-align:top" colspan="3">
            Dokter/Petugas Jaga IGD
        </td>
        <td style="border:1px solid black;;" colspan="7">
            @php
                if (!empty($primer)) {
                    $nip_petugas = $primer->nip;
                    $nama_petugas = $primer->nama;
                    $tanggal_hasil = \Carbon\Carbon::parse($primer->tanggaltriase)->format('d-m-Y');
                } elseif(!empty($sekunder)) {
                    $nip_petugas = $sekunder->nip;
                    $nama_petugas = $sekunder->nama;
                    $tanggal_hasil = \Carbon\Carbon::parse($sekunder->tanggaltriase)->format('d-m-Y');
                }else{
                    $nip_petugas = 'kosong';
                    $nama_petugas = 'kosong';
                    $tanggal_hasil = \Carbon\Carbon::now()->format('d-m-Y');
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
                {{ $primer != null ? $primer->nama : ''}}
                {{ $sekunder != null ? $sekunder->nama:''}}
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
            <table style="border: 0px solid black;">
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
            <table style="width: 100%; border: 1px solid black;">
                <thead>
                    <tr>
                        <th rowspan="5" style="width: 50%; border:1px solid black;; text-align:center;">
                            <h4>RINGKASAN PASIEN<br> GAWAT DARURAT</h4>
                        </th>
                        <th
                            style="width: 15%; border-left:1px solid black;; border-top: 1px solid black;;text-align:left;">
                            No.
                            RM
                        </th>
                        <th style="border-right: 1px solid black;; border-top: 1px solid black;;text-align:left;">:
                            {{ $dataRingkasan->no_rkm_medis }}</th>
                    </tr>
                    <tr>
                        <th style="border-left: 1px solid black;; text-align:left;">NIK </th>
                        <th style="border-right: 1px solid black;; text-align:left;">
                            : {{ $dataRingkasan->no_ktp }}</th>
                    </tr>
                    <tr>
                        <th style="border-left: 1px solid black;; text-align:left;">Nama Pasien </th>
                        <th style="border-right: 1px solid black;; text-align:left;">
                            : {{ $dataRingkasan->nm_pasien }}</th>
                    </tr>
                    <tr>
                        <th style="border-left: 1px solid black;; text-align:left;">Tanggal Lahir </th>
                        <th style="border-right: 1px solid black;; text-align:left;">
                            : {{ $dataRingkasan->tgl_lahir }}</th>
                    </tr>
                    <tr>
                        <th
                            style="border-left: 1px solid black;; border-bottom: 1px solid black;;text-align:left; vertical-align:top">
                            Alamat</th>
                        <th style="border-right: 1px solid black;;border-bottom: 1px solid black;; text-align:left;">:
                            {{ $dataRingkasan->alamat }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border-left: 1px solid black;; border-right: 1px solid black;" colspan="3">
                            <b>Waktu
                                Kedatangan</b> Tanggal :
                            {{ \Carbon\Carbon::parse($dataRingkasan->tgl_registrasi)->format('d-m-Y') }} Jam :
                            {{ $dataRingkasan->jam_reg }}
                        </td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black;; border-right: 1px solid black;; " colspan="3">
                            <b>Diagnosis:</b>
                        </td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black;; border-right: 1px solid black;; padding-left:20px"
                            colspan="3">
                            {{ $dataRingkasan->diagnosis }}</td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black;; border-right: 1px solid black;" colspan="3">
                            <b>Kondisi Pada Saat Keluar:</b>
                        </td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black;; border-right: 1px solid black;; padding-left:20px"
                            colspan="3">
                            {{ $resumeIgd->kondisi_pulang }}</td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black;; border-right: 1px solid black;" colspan="3">
                            <b>Tindak
                                Lanjut:</b>
                        </td>
                    </tr>
                    <tr>
                        <td
                            style="border-left: 1px solid black;; border-right: 1px solid black;; padding-left:20px"colspan="3">
                            {{ $resumeIgd->tindak_lanjut }}</td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black;; border-right: 1px solid black;" colspan="3"><b>Obat
                                yang dibawa pulang:</b></td>
                    </tr>
                    @php
                        $obat = explode("\n", $resumeIgd->obat_pulang);
                    @endphp
                    @foreach ($obat as $obatPulang)
                        <tr>
                            <td style="border-left: 1px solid black;; border-right: 1px solid black;; padding-left:20px"
                                colspan="3">
                                {{ $obatPulang }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td style="border-left: 1px solid black;; border-right: 1px solid black;" colspan="3">
                            <b>Edukasi:</b>
                        </td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black;; border-right: 1px solid black;; padding-left:20px"
                            colspan="3">
                            {{ $resumeIgd->edukasi }}</td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black;; border-right: 1px solid black;" colspan="3">Waktu
                            Selesai Pelayanan IGD Tanggal:
                            {{ \Carbon\Carbon::parse($resumeIgd->tgl_selesai)->format('d-m-Y') }} Jam:
                            {{ \Carbon\Carbon::parse($resumeIgd->tgl_selesai)->format('H:i:s') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black;; border-right: 1px solid black;; padding-top:10px"
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
                        <td style="border-left: 1px solid black;; border-right: 1px solid black;; padding-left:20px"
                            colspan="3">
                            <img src="data:image/png;base64, {!! $qrcode_dokter !!}">
                        </td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid black;; border-right: 1px solid black;; border-bottom: 1px solid black;"
                            colspan="3">Nama :
                            {{ $dataRingkasan->nm_dokter }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endif

    {{-- Lembar penilaian IGD --}}
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

    {{-- Data Operasi --}}
    @if($dataOperasi)
     {{-- @foreach ($dataOperasi as $index => $listOperasi) --}}
         <div style="float: none;">
             <div style="page-break-after: always;"></div>
         </div>
         <div class="watermark">
             {{ $watermark }}
         </div>
         <div>
             <img src="{{ asset('image/kop.png') }}" alt="KOP RSUP">
             <hr class='new4' />
             <table style="width: 100%;">
                 <thead>
                     <tr>
                         <td style="padding: 0; border: 1px solid black;; border-top-width: 5px; border-left-width: 0; border-right-width: 0; text-align: center;" colspan="6">
                             <h3 style="margin: 0;">LAPORAN OPERASI</h3>
                         </td>
                     </tr>
                     <tr>
                         <td style="padding: 0; vertical-align: middle;">Nama Pasien</td>
                         <td style="padding: 0; vertical-align: middle;" colspan="2">: {{ $pasien->nm_pasien }}</td>
                         <td style="padding: 0; vertical-align: middle;">No. Rekam Medis</td>
                         <td style="padding: 0; vertical-align: middle;" colspan="2">: {{ $pasien->no_rkm_medis }}</td>
                     </tr>
                     <tr>
                         <td style="padding: 0; vertical-align: middle;">Umur</td>
                         <td style="padding: 0; vertical-align: middle;" colspan="2">
                             : {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->diff(\Carbon\Carbon::parse($dataOperasi->tgl_operasi))->format('%y Th %m Bl %d Hr') }}
                         </td>
                         <td style="padding: 0; vertical-align: middle;">Ruang</td>
                         <td style="padding: 0; vertical-align: middle;" colspan="2">: {{ $pasien->nm_poli }}</td>
                     </tr>
                     <tr>
                         <td style="padding: 0; vertical-align: middle;">Tgl Lahir</td>
                         <td style="padding: 0; vertical-align: middle;" colspan="2">: {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->format('d/m/Y') }}</td>
                         <td style="padding: 0; vertical-align: middle;">Jenis Kelamin</td>
                         <td style="padding: 0; vertical-align: middle;" colspan="2">: {{ $pasien->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                     </tr>
                 </thead>
                 <tbody>
                     <tbody>
                         <tr>
                             <td style="border: 1px solid black;; background-color:lightgray" colspan="6">
                                 <div style="font-size: 14pt; text-align:center">PRE SURGICAL ASSESMENT</div>
                             </td>
                         </tr>
                         <tr>
                             <td>
                                 Tanggal
                             </td>
                             <td>
                                 :
                                 {{ \Carbon\Carbon::parse($dataOperasi->tgl_perawatan)->format('d/m/Y') }}
                             </td>
                             <td>
                                 Waktu
                             </td>
                             <td>
                                 : {{ $dataOperasi->jam_rawat }}
                             </td>
                             <td>
                                 Alergi
                             </td>
                             <td>
                                 : {{ $dataOperasi->alergi }}
                             </td>
                         </tr>
                         <tr>
                             <td>
                                 Dokter Bedah
                             </td>
                             <td
                                 colspan="5">
                                 :
                                 {!! $dataOperasi->operator1 != '-' ? \App\Vedika::getPegawai($dataOperasi->operator1)->nama : '-' !!}
                             </td>
                         </tr>
                         <tr>
                             <td style="border-top:1px solid black;">
                                 Keluhan:
                             </td>
                             <td style="border-top:1px solid black;" colspan="2">

                             </td>
                             <td style="border-top:1px solid black;;border-left:1px solid black;">
                                 Penilaian:
                             </td>
                             <td style="border-top:1px solid black;;" colspan="2">

                             </td>
                         </tr>
                         <tr>
                             <td colspan="3">
                                 <div style="text-decoration: underline; vertical-align:center; padding-left:10px">
                                     {{ $dataOperasi->keluhan }}</div>
                             </td>
                             <td style="border-left:1px solid black;" solid black; colspan="3">
                                 <div style="text-decoration: underline; vertical-align:center; padding-left:10px">
                                     {{ $dataOperasi->penilaian }}</div>
                             </td>
                         </tr>
                         <tr>
                             <td>
                                 Pemeriksaan:
                             </td>
                             <td colspan="2">

                             </td>
                             <td style="border-left:1px solid black;">
                                 Tindak Lanjut:
                             </td>
                             <td colspan="2">

                             </td>
                         </tr>
                         <tr>
                             <td colspan="3">
                                 <div style="text-decoration: underline; vertical-align:center; padding-left:10px">
                                     {{ $dataOperasi->pemeriksaan }}</div>
                             </td>
                             <td style="border-left:1px solid black;" colspan="3">
                                 <div style="text-decoration: underline; vertical-align:center; padding-left:10px">
                                     {{ $dataOperasi->rtl }}</div>
                             </td>

                         </tr>
                         <tr>
                             <td style="padding-left:10px" colspan="2">
                                 Suhu Tubuh.(C)
                             </td>
                             <td>
                                 : <u>{{ $dataOperasi->suhu_tubuh }}</u>
                             </td>
                             <td style="padding-left:10px; border-left:1px solid black;">
                                 Nadi (/Mnt)
                             </td>
                             <td colspan="2">
                                 : <u>{{ $dataOperasi->nadi }}</u>
                             </td>
                         </tr>
                         <tr>
                             <td style="padding-left:10px" colspan="2">
                                 Tensi.
                             </td>
                             <td>
                                 : <u>{{ $dataOperasi->tensi }}</u>
                             </td>
                             <td style="padding-left:10px; border-left:1px solid black;">
                                 Respirasi (/Mnt).
                             </td>
                             <td colspan="2">
                                 : <u>{{ $dataOperasi->respirasi }}</u>
                             </td>
                         </tr>
                         <tr>
                             <td style="padding-left:10px" colspan="2">
                                 Tinggi (Cm).
                             </td>
                             <td>
                                 : <u>{{ $dataOperasi->tinggi }}</u>
                             </td>
                             <td style="padding-left:10px; border-left:1px solid black;">
                                 GCS (E,V,M).
                             </td>
                             <td colspan="2">
                                 : <u>{{ $dataOperasi->gcs }}</u>
                             </td>
                         </tr>
                         <tr>
                             <td style="padding-left:10px" colspan="2">
                                 Berat (Kg).
                             </td>
                             <td>
                                 : <u>{{ $dataOperasi->berat }}</u>
                             </td>
                             <td style="border-left:1px solid black;" colspan="3">
                             </td>
                         </tr>
                         <tr>
                             <td style="border: 1px solid black;; background-color:lightgray" colspan="6">
                                 <div style="font-size: 14pt; text-align:center">POST SURGICAL REPORT</div>
                             </td>
                         </tr>
                         <tr>
                             <td colspan="2">
                                 Tanggal & Waktu
                             </td>
                             <td colspan="3">
                                 :
                                 {{ \Carbon\Carbon::parse($dataOperasi->tgl_operasi)->format('d/m/Y H:i:s') }}
                             </td>
                             <td style="border-left: 1px solid black;">
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
                             <td style="border-left: 1px solid black;; text-align:center">
                                 Tipe/Jenis Anastesi
                             </td>
                         </tr>
                         <tr>
                             <td style="padding-left: 20px;" colspan="3">
                                 {!! $dataOperasi->operator1 != '-' ? \App\Vedika::getPegawai($dataOperasi->operator1)->nama : '-' !!}
                             </td>
                             <td style="padding-left: 20px;" colspan="2">
                                 {!! $dataOperasi->asisten_operator1 != '-'
                                     ? \App\Vedika::getPegawai($dataOperasi->asisten_operator1)->nama
                                     : '-' !!}
                             </td>
                             <td style="border-left: 1px solid black;;text-align:center">
                             </td>
                         </tr>
                         <tr>
                             <td  colspan="2">
                                 Dokter Bedah 2
                             </td>
                             <td>
                                 :
                             </td>
                             <td >
                                 Asisten Bedah 2
                             </td>
                             <td>
                                 :
                             </td>
                             <td style="border-left: 1px solid black;; text-align:center;">
                                 {{ $dataOperasi->jenis_anasthesi }}
                             </td>
                         </tr>
                         <tr>
                             <td style="padding-left:20px;" colspan="3">
                                 {!! $dataOperasi->operator2 != '-' ? \App\Vedika::getPegawai($dataOperasi->operator2)->nama : '-' !!}
                             </td>
                             <td style="padding-left:20px;" colspan="2">
                                 {!! $dataOperasi->asisten_operator2 != '-'
                                     ? \App\Vedika::getPegawai($dataOperasi->asisten_operator2)->nama
                                     : '-' !!}
                             </td>
                             <td style="border-left: 1px solid black;; text-align:center;">
                             </td>
                         </tr>
                         <tr>
                             <td  colspan="2">
                                 Perawat Resusitas
                             </td>
                             <td>
                                 :
                             </td>
                             <td >
                                 Dokter Anastesi
                             </td>
                             <td>
                                 :
                             </td>
                             <td style="border-left: 1px solid black;; text-align:center;">
                             </td>
                         </tr>
                         <tr>
                             <td style="padding-left:20px;" colspan="3">
                                 {!! $dataOperasi->perawaat_resusitas != '-'
                                     ? \App\Vedika::getPegawai($dataOperasi->perawaat_resusitas)->nama
                                     : '-' !!}
                             </td>
                             <td style="padding-left:20px;" colspan="2">
                                 {!! $dataOperasi->dokter_anestesi != '-' ? \App\Vedika::getPegawai($dataOperasi->dokter_anestesi)->nama : '-' !!}
                             </td>
                             <td style="border-left: 1px solid black;; text-align:center;">
                             </td>
                         </tr>
                         <tr>
                             <td  colspan="2">
                                 Instrumen
                             </td>
                             <td>
                                 :
                             </td>
                             <td >
                                 Asisten Anastesi
                             </td>
                             <td>
                                 :
                             </td>
                             <td style="border-left: 1px solid black;; text-align:center;">
                                 Dikirim ke Pemeriksaaan PA
                             </td>
                         </tr>
                         <tr>
                             <td style="padding-left:20px;" colspan="3">
                                 {!! $dataOperasi->instrumen != '-' ? \App\Vedika::getPegawai($dataOperasi->instrumen)->nama : '-' !!}
                             </td>
                             <td style="padding-left:20px;" colspan="2">
                                 {!! $dataOperasi->asisten_anestesi != '-'
                                     ? \App\Vedika::getPegawai($dataOperasi->asisten_anestesi)->nama
                                     : '-' !!}
                             </td>
                             <td style="border-left: 1px solid black;; text-align:center;">
                                 {{ $dataOperasi->permintaan_pa }}
                             </td>
                         </tr>
                         <tr>
                             <td  colspan="2">
                                 Dokter Anak
                             </td>
                             <td>
                                 :
                             </td>
                             <td >
                                 Bidan
                             </td>
                             <td>
                                 :
                             </td>
                             <td style="border-left: 1px solid black;; text-align:center;">
                             </td>
                         </tr>
                         <tr>
                             <td style="padding-left:20px;" colspan="3">
                                 {!! $dataOperasi->dokter_anak != '-' ? \App\Vedika::getPegawai($dataOperasi->dokter_anak)->nama : '-' !!}
                             </td>
                             <td style="padding-left:20px;" colspan="2">
                                 {!! $dataOperasi->bidan != '-' ? \App\Vedika::getPegawai($dataOperasi->bidan)->nama : '-' !!}
                             </td>
                             <td style="border-left: 1px solid black;; text-align:center;">
                             </td>
                         </tr>
                         <tr>
                             <td  colspan="2">
                                 Dokter Umum
                             </td>
                             <td>
                                 :
                             </td>
                             <td >
                                 Onloop
                             </td>
                             <td>
                                 :
                             </td>
                             <td style="border-left: 1px solid black;; text-align:center;">
                                 Tipe/Kategori Operasi
                             </td>
                         </tr>
                         <tr>
                             <td style="padding-left:20px;" colspan="3">
                                 {!! $dataOperasi->dokter_umum != '-' ? \App\Vedika::getPegawai($dataOperasi->dokter_umum)->nama : '-' !!}
                             </td>
                             <td style="padding-left:20px;" colspan="2">
                                 {!! $dataOperasi->omloop != '-' ? \App\Vedika::getPegawai($dataOperasi->omloop)->nama : '-' !!}
                             </td>
                             <td style="border-left: 1px solid black;; text-align:center;">
                                 {{ $dataOperasi->kategori }}
                             </td>
                         </tr>
                         <tr>
                             <td colspan="5">
                                 Diagnosa Pre-Op / Pre Operation Diagnosis
                             </td>
                             <td style="border-left: 1px solid black;; text-align:center;">
                             </td>
                         </tr>
                         <tr>
                             <td style="padding-left:20px;" colspan="5">
                                 {{ $dataOperasi->diagnosa_preop }}
                             </td>
                             <td style="border-left: 1px solid black;; text-align:center;">
                             </td>
                         </tr>
                         <tr>
                             <td colspan="5">
                                 Jaringan Yang di-Eksisi/-Insisi
                             </td>
                             <td style="border-left: 1px solid black;; text-align:center;">
                                 Selesai Operasi
                             </td>
                         </tr>
                         <tr>
                             <td style="padding-left:20px;" colspan="5">
                                 {{ $dataOperasi->jaringan_dieksekusi }}
                             </td>
                             <td style="border-left: 1px solid black;; text-align:center;">
                                 {{ \Carbon\Carbon::parse($dataOperasi->selesaioperasi)->format('d/m/Y H:i:s') }}
                             </td>
                         </tr>
                         <tr>
                             <td colspan="5">
                                 Diagnosa Post-Op / Post Operation Diagnosis
                             </td>
                             <td style="border-left: 1px solid black;; text-align:center;">
                             </td>
                         </tr>
                         <tr>
                             <td style="padding-left:20px;" colspan="5">
                                 {{ $dataOperasi->diagnosa_postop }}
                             </td>
                             <td style="border-left: 1px solid black;; text-align:center;">
                             </td>
                         </tr>
                         <tr>
                             <td style="border: 1px solid black;; background-color:lightgray" colspan="6">
                                 <div style="text-align: center; font-size:16pt">REPORT ( PROCEDURES, SPECIFIC FINDINGS
                                     AND COMPLICATIONS )
                                 </div>
                             </td>
                         </tr>
                         @php
                             $dokterOperator = \App\Vedika::getPegawai($dataOperasi->operator1)->nama;
                             $draf = preg_split('/\r\n|\r|\n/', $dataOperasi->laporan_operasi);
                             $qr_dokter =
                                 'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara
                                         elektronik oleh' .
                                 "\n" .
                                 $dokterOperator .
                                 "\n" .
                                 'ID ' .
                                 $dataOperasi->operator1 .
                                 "\n" .
                                 \Carbon\Carbon::parse($dataOperasi->selesaioperasi)->format('d-m-Y');

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
     {{-- @endforeach --}}
    @endif
    {{-- Data SOAP --}}
    @if (!empty($soap) && $pasien->nm_poli == 'REHABILITASI MEDIK')
        <div style="float: none;">
            <div style="page-break-after: always;"></div>
        </div>
        <div class="watermark">
            {{ $watermark }}
        </div>
        <div>
            <img src="{{ asset('image/kop.png') }}" alt="KOP RSUP" >
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 0;">
                <thead>
                    <tr>
                        <td style="vertical-align: middle; padding: 0; border: 3px solid black;; border-top-width: 5px; border-left: none; border-right: none; text-align: center;" colspan="6">
                            <div style="font-size: 16pt">SOAP</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; padding: 0; width: 15%;">
                            Tanggal
                        </td>
                        <td style="vertical-align: middle; padding: 0;" colspan="2">
                            : {{ $soap->tgl_perawatan }}
                        </td>
                        <td style="vertical-align: middle; padding: 0; width: 15%;">
                            Nama Petugas/Profesi
                        </td>
                        <td style="vertical-align: middle; padding: 0;" colspan="2">
                            : {{ $soap->petugas }} / {{ $soap->jabatan_petugas }}
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; padding: 0;">
                            Nama Pasien
                        </td>
                        <td style="vertical-align: middle; padding: 0;" colspan="2">
                            : {{ $pasien->nm_pasien }}
                        </td>
                        <td style="vertical-align: middle; padding: 0;">
                            No. Rekam Medis
                        </td>
                        <td style="vertical-align: middle; padding: 0;" colspan="2">
                            : {{ $pasien->no_rkm_medis }}
                        </td>
                    </tr>
                </thead>
                <tbody style="margin-top: 10px;">
                    <tr>
                        <th style="border: 1px solid black;; vertical-align:top; ">Subjek</th>
                        <td style="border: 1px solid black;; padding-left:5pt;" colspan="5">
                            {{ $soap->keluhan }}
                        </td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid black;;">Objek</th>
                        <td style="border: 1px solid black;; padding-left:5pt;" colspan="5">
                            {{ $soap->pemeriksaan }}
                        </td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid black;; border-bottom: none;"></th>
                        <th style="border: 1px solid black;;">Suhu</th>
                        <th style="border: 1px solid black;;">Tensi</th>
                        <th style="border: 1px solid black;;">Nadi(/menit)</th>
                        <th style="border: 1px solid black;;">Respirasi(/menit)</th>
                        <th style="border: 1px solid black;; border-bottom: none;"></th>
                    </tr>
                    <tr>
                        <td style="text-align: right; border: 1px solid black;; border-top: none;"></td>
                        <td style="text-align: right; border: 1px solid black;;">{{ $soap->suhu_tubuh }}</td>
                        <td style="text-align: right; border: 1px solid black;;">{{ $soap->tensi }}</td>
                        <td style="text-align: right; border: 1px solid black;;">{{ $soap->nadi }}</td>
                        <td style="text-align: right; border: 1px solid black;; width: 20%;">
                            {{ $soap->respirasi }}
                        </td>
                        <td style="text-align: right; border: 1px solid black;; border-top: none; width: 20%;">&nbsp;</td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid black;;">Alergi</th>
                        <td style="border: 1px solid black;; padding-left:5pt;" colspan="5">
                            {{ $soap->alergi }}
                        </td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid black;;">Asessmen</th>
                        <td style="border: 1px solid black;; padding-left:5pt;" colspan="5">
                            {{ $soap->penilaian }}
                        </td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid black;;">Plan</th>
                        <td style="border: 1px solid black;; padding-left:5pt;" colspan="5">
                            {{ $soap->rtl }}
                        </td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid black;;">Implementasi</th>
                        <td style="border: 1px solid black;; padding-left:5pt;" colspan="5">
                            {{ $soap->instruksi }}
                        </td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid black;;">Evaluasi</th>
                        <td style="border: 1px solid black;; padding-left:5pt;" colspan="5">
                            {{ $soap->evaluasi }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endif

    @if($dataUsg)
        <div style="float: none;">
            <div style="page-break-after: always;"></div>
        </div>
        <div class="watermark">
            {{ $watermark }}
        </div>
        <div>
            <img src="{{ asset('image/kop.png') }}" alt="KOP RSUP" >
            <table style="width: 100%; margin-bottom:50px; margin-top:10px; border: 1px solid black;">
                <thead>
                    <tr>
                        <th style="text-align: center; border: 1px solid black;;" colspan="4">
                            <h5><b><u>HASIL PEMERIKSAAN ULTRASONOGRAFI (USG) OBSTETRI</u></b></h5>
                        </th>
                    </tr>
                    <tr>
                        <td style="width: 20%; padding-left: 25px;">No. Rekam Medis</td>
                        <td style="width: 30%; ">: {{ $pasien->no_rkm_medis }}</td>
                        <td style="padding-left: 25px; text-align:right;">Tanggal Lahir</td>
                        <td style="padding-left: 50px;">: {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 25px; border-bottom: 1px solid black;;">Nama Pasien</td>
                        <td style="border-bottom: 1px solid black;;" colspan="3">: {{ $pasien->nm_pasien }}</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding-left: 25px;">Kiriman Dari</td>
                        <td colspan="3">: {{ $dataUsg->kiriman_dari }}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 25px;">Diagnosa Klinis</td>
                        <td colspan="3">: {{ $dataUsg->diagnosa_klinis }}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 25px;">HTA</td>
                        <td colspan="3">: {{ $dataUsg->hta }}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 25px;">Jenis Prestasi</td>
                        <td colspan="3">: {{ $dataUsg->jenis_prestasi }}</td>
                    </tr>
                    @php
                        $formattedDate = \Carbon\Carbon::parse($dataUsg->tanggal)->format('M, d/m/Y H:i:s') . ' WIB';
                        $qr_dokter =
                        'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara
                        elektronik oleh' .
                        "\n" .
                        $dataUsg->nama .
                        "\n" .
                        'ID ' .
                        $dataUsg->kd_dokter .
                        "\n" .
                        \Carbon\Carbon::parse($dataUsg->tanggal)->format('d-m-Y');
                        $qrcode_dokter = base64_encode(
                                 QrCode::format('png')->size(100)->errorCorrection('H')->generate($qr_dokter)
                             );
                    @endphp
                    <tr>
                        <td style="padding-left: 25px; vertical-align:top; ">Kesimpulan</td>
                        <td colspan="3" class="padding-left:25px;">: {!! nl2br(e($dataUsg->kesimpulan)) !!}</td>
                    </tr>
                    <tr>
                        <td style="text-align:center; border-top: 1px solid black;; border-right: 1px solid black;;">Tanggal dan Jam</td>
                        <td style="text-align:center; border-top: 1px solid black;;" colspan="3">Nama Dokter dan Tanda Tangan</td>
                    </tr>

                    <tr>
                        <td style="text-align: center; border-top: 1px solid black;; border-right: 1px solid black;;">{{ $formattedDate }}</td>
                        <td style="padding: 10px;" style="border-top: 1px solid black;; padding:5px;"><img src="data:image/png;base64, {!! $qrcode_dokter !!}"></td>
                        <td colspan="2" style="border-top: 1px solid black;;">{{ $dataUsg->nama }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endif
    @if($dataUsgGynecologi)
        <div style="float: none;">
            <div style="page-break-after: always;"></div>
        </div>
        <div class="watermark">
            {{ $watermark }}
        </div>
        <div>
            <img src="{{ asset('image/kop.png') }}" alt="KOP RSUP" >
            <table style="width: 100%; margin-bottom:50px; margin-top:10px; border: 1px solid black;">
                <thead>
                    <tr>
                        <th style="text-align: center; border: 1px solid black;;" colspan="4">
                            <h5><b><u>HASIL PEMERIKSAAN ULTRASONOGRAFI (USG) GINEKOLOGI</u></b></h5>
                        </th>
                    </tr>
                    <tr>
                        <td style="width: 20%; padding-left: 25px;">No. Rekam Medis</td>
                        <td style="width: 30%; ">: {{ $pasien->no_rkm_medis }}</td>
                        <td style="padding-left: 25px; text-align:right;">Tanggal Lahir</td>
                        <td style="padding-left: 50px;">: {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 25px; border-bottom: 1px solid black;;">Nama Pasien</td>
                        <td style="border-bottom: 1px solid black;;" colspan="3">: {{ $pasien->nm_pasien }}</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding-left: 25px;">Kiriman Dari</td>
                        <td colspan="3">: {{ $dataUsgGynecologi->kiriman_dari }}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 25px;">Diagnosa Klinis</td>
                        <td colspan="3">: {{ $dataUsgGynecologi->diagnosa_klinis }}</td>
                    </tr>
                    @php
                        $formattedDate = \Carbon\Carbon::parse($dataUsgGynecologi->tanggal)->format('M, d/m/Y H:i:s') . ' WIB';
                        $qr_dokter =
                        'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara
                        elektronik oleh' .
                        "\n" .
                        $dataUsgGynecologi->nama .
                        "\n" .
                        'ID ' .
                        $dataUsgGynecologi->kd_dokter .
                        "\n" .
                        \Carbon\Carbon::parse($dataUsgGynecologi->tanggal)->format('d-m-Y');
                        $qrcode_dokter = base64_encode(
                                 QrCode::format('png')->size(100)->errorCorrection('H')->generate($qr_dokter)
                             );
                    @endphp
                    <tr>
                        <td style="padding-left: 25px; vertical-align:top; ">Kesimpulan</td>
                        <td colspan="3">: {!! nl2br(e($dataUsgGynecologi->kesimpulan)) !!}</td>
                    </tr>
                    <tr>
                        <td style="text-align:center; border: 1px solid black;;">Tanggal dan Jam</td>
                        <td style="text-align:center; border: 1px solid black;;" colspan="3">Nama Dokter dan Tanda Tangan</td>
                    </tr>

                    <tr>
                        <td style="text-align: center; border: 1px solid black;;">{{ $formattedDate }}</td>
                        <td style="padding: 10px;"><img src="data:image/png;base64, {!! $qrcode_dokter !!}"></td>
                        <td colspan="2">{{ $dataUsgGynecologi->nama }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endif
    @if($dataSpiro)
        <div style="float: none;">
            <div style="page-break-after: always;"></div>
        </div>
        <div class="watermark">
            {{ $watermark }}
        </div>
        <div>
            <img src="{{ asset('image/kop.png') }}" alt="KOP RSUP" >
            <table style="width: 100%; margin-bottom:10px;">
                <thead>
                    <tr>
                        <th style="text-align: center; border-bottom: 1px solid black;; border-top: 3px solid black;; font-size:14pt; padding:10px;" colspan="4">
                            <b>PEMERIKSAAN SPIROMETRI</b>
                        </th>
                    </tr>
                    <tr>
                        <td style="padding-top:10px;" colspan="4">
                            A. IDENTITAS
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%; padding-left: 25px;">No. Rawat</td>
                        <td style="width: 30%; ">: {{ $pasien->no_rawat }}</td>
                        <td style="padding-left: 20px; width:20%">Tanggal Periksa</td>
                        <td style="padding-left: 20px; width:30%">: {{ \Carbon\Carbon::parse($dataSpiro->tanggal)->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 20%; padding-left: 25px;">No. Rekam Medis</td>
                        <td style="width: 30%; ">: {{ $pasien->no_rkm_medis }}</td>
                        <td style="padding-left: 20px; vertical-align:top;">DPJP</td>
                        <td style="padding-left: 20px;">: {{ $dataSpiro->nm_dokter }}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 25px; border-bottom: 0px solid black;;">Nama Pasien</td>
                        <td style="border-bottom: 0px solid black;;">: {{ $pasien->nm_pasien }}</td>
                        <td style="padding-left: 20px; vertical-align:top;">Dokter Pengirim</td>
                        <td style="padding-left: 20px;">: {{ $dataSpiro->nm_dokter }}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 25px; border-bottom: 0px solid black;;">Umur</td>
                        <td style="border-bottom: 0px solid black;;">:
                            {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->diff(\Carbon\Carbon::parse($dataSpiro->tanggal))->format('%y Th') }} </td>
                        <td style="padding-left: 20px; ">Tinggi Badan</td>
                        <td style="padding-left: 20px;">: {{ $dataSpiro->tb }} Cm</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 25px; border-bottom: 0px solid black; vertical-align:top;">Alamat</td>
                        <td style="border-bottom: 0px solid black;;">: {{$pasien->alamat}} </td>
                        <td style="padding-left: 20px; ">Berat Badan</td>
                        <td style="padding-left: 20px;">: {{ $dataSpiro->bb }} Cm</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border-top: 1px solid black;; padding-top:10px;" colspan="4">B. RIWAYAT PEKERJAAN / KEBIASAAN</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 25px;">Pekerjaan</td>
                        <td colspan="4">: {{ $dataSpiro->pekerjaan }}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 25px;">Merokok</td>
                        <td>: {{ $dataSpiro->merokok }}</td>
                        <td>Lama : {{ $dataSpiro->lama_merokok }}</td>
                        <td>Jumlah : 20 Btg / Hari, Eks : {{ $dataSpiro->jumlah_merokok }} </td>
                    </tr>
                    <tr>
                        <td style="padding-left: 25px;">Pengobatan</td>
                        <td colspan="4">: {{ $dataSpiro->pengobatan }}</td>
                    </tr>
                </tbody>
            </table>
            <table style="width:100%; padding:10px;">
                <thead>
                    <tr>
                        <th rowspan="2" style="vertical-align: middle; text-align:center; ;border:1px solid black; width:5%;">No</th>
                        <th rowspan="2" style="vertical-align: middle; text-align:center;border:1px solid black; width:30%;">Pemeriksaan</th>
                        <th colspan="7" style="text-align: center;border:1px solid black; width:65%;">Nilai</th>
                    </tr>
                    <tr>
                        <th style="text-align: center;border:1px solid black; width:10%;" colspan="2">Hasil</th>
                        <th style="text-align: center;border:1px solid black;">Prediksi</th>
                        <th style="text-align: center;border:1px solid black;">Normal</th>
                        <th style="text-align: center;border:1px solid black; width:15%" colspan="2">Uji Bromkodilator</th>
                        <th style="text-align: center;border:1px solid black;">Kenaikan Vep 1</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="3" style="text-align:center;border:1px solid black;">1</td>
                        <td rowspan="3" style="border:1px solid black;">Kapasitas Vital</td>
                        <td style="text-align:center;border:1px solid black;">1</td>
                        <td style="text-align: center;border:1px solid black;">{{ $dataSpiro->pemeriksaan_1a }}</td>
                        <td rowspan="3" style="width: 5%; text-align:center; vertical-align:middle;border:1px solid black;">{{ $dataSpiro->prediksi_1a }}</td>
                        <td rowspan="3" style="background-color:beige;border:1px solid black;"></td>
                        <td rowspan="3" colspan="2" style="background-color:beige;border:1px solid black;"></td>
                        <td rowspan="4" style="border:1px solid black;"></td>
                    </tr>
                    <tr>
                        <td style="text-align:center;border:1px solid black;">2</td>
                        <td style="text-align:center;border:1px solid black;">{{ $dataSpiro->pemeriksaan_1b }}</td>
                    </tr>
                    <tr>
                        <td style="text-align:center;border:1px solid black;">3</td>
                        <td style="text-align:center;border:1px solid black;">{{ $dataSpiro->pemeriksaan_1c }}</td>
                    </tr>
                    <tr>
                        <td style="text-align:center;border:1px solid black;">2</td>
                        <td style="border:1px solid black;">% KV ( KV / KV Prediksi )</td>
                        <td colspan="2" style="text-align:center;border:1px solid black;">{{ $dataSpiro->hasil_2a }}%</td>
                        <td style="background-color:beige;border:1px solid black;"></td>
                        <td style="text-align:center;border:1px solid black;">80 %</td>
                        <td colspan="2" style="background-color:beige;border:1px solid black;"></td>
                    </tr>
                    <tr>
                        <td rowspan="3" style="text-align:center;border:1px solid black;">3</td>
                        <td rowspan="3" style="border:1px solid black;">Kapasital Vital Paksa</td>
                        <td style="text-align:center;border:1px solid black;">1</td>
                        <td style="text-align:center;border:1px solid black;">{{ $dataSpiro->pemeriksaan_3a }}</td>
                        <td rowspan="3" style="text-align:center; vertical-align:middle;border:1px solid black;">{{ $dataSpiro->prediksi_3a }}</td>
                        <td rowspan="3" style="background-color:beige;border:1px solid black;"></td>
                        <td rowspan="3" colspan="2" style="background-color:beige;border:1px solid black;"></td>
                        <td rowspan="4" style="border:1px solid black;"></td>
                    </tr>
                    <tr>
                        <td style="text-align:center;border:1px solid black;">2</td>
                        <td style="text-align:center;border:1px solid black;">{{ $dataSpiro->pemeriksaan_3b }}</td>
                    </tr>
                    <tr>
                        <td style="text-align:center;border:1px solid black;">3</td>
                        <td style="text-align:center;border:1px solid black;">{{ $dataSpiro->pemeriksaan_3c }}</td>
                    </tr>
                    <tr>
                        <td style="text-align:center;border:1px solid black;">4</td>
                        <td style="border:1px solid black;;">% KV ( KV / KV Prediksi )</td>
                        <td colspan="2" style="text-align:center;border:1px solid black;">{{ $dataSpiro->hasil_4a }}%</td>
                        <td style="background-color:beige;border:1px solid black;"></td>
                        <td style="text-align:center;border:1px solid black;">80 %</td>
                        <td colspan="2" style="background-color:beige;border:1px solid black;"></td>
                    </tr>
                    <tr>
                        <td rowspan="3" style="text-align:center;border:1px solid black;">5</td>
                        <td rowspan="3" style="border: 1px solid black;">Volome Ekspirasi Paksa
                            Detik 1 ( 1 VEP )</td>
                        <td style="text-align:center;border:1px solid black;">1</td>
                        <td style="text-align:center;border:1px solid black;">{{ $dataSpiro->pemeriksaan_5a }}</td>
                        <td rowspan="3" style="text-align:center; vertical-align:middle;border:1px solid black;">{{ $dataSpiro->prediksi_5a }}</td>
                        <td rowspan="3" style="background-color:beige;border:1px solid black;"></td>
                        <td style="width: 3%; text-align:center;border:1px solid black;">1</td>
                        <td style="text-align:right;border:1px solid black;">{{ $dataSpiro->uji_5a }} Ml</td>
                        <td rowspan="9" style="text-align:center;border:1px solid black; vertical-align:middle;">%</td>
                    </tr>
                    <tr>
                        <td style="text-align:center;border:1px solid black;">2</td>
                        <td style="text-align:center;border:1px solid black;">{{ $dataSpiro->pemeriksaan_5b }}</td>
                        <td style="text-align:center;border:1px solid black;">2</td>
                        <td style="text-align:right;border:1px solid black;">{{ $dataSpiro->uji_5b }} Ml</td>
                    </tr>
                    <tr>
                        <td style="text-align:center;border:1px solid black;">3</td>
                        <td style="text-align:center;border:1px solid black;">{{ $dataSpiro->pemeriksaan_5c }}</td>
                        <td style="text-align:center;border:1px solid black;">3</td>
                        <td style="text-align:right;border:1px solid black;">{{ $dataSpiro->uji_5c }} Ml</td>
                    </tr>
                    <tr>
                        <td style="text-align:center;border:1px solid black;">6</td>
                        <td style="border:1px solid black;">% VEP 1( VEP 1 Prediksi )</td>
                        <td colspan="2" style="text-align:center;border:1px solid black;">{{ $dataSpiro->hasil_6a }}%</td>
                        <td style="background-color:beige;border:1px solid black;"></td>
                        <td style="text-align:center;border:1px solid black;">80 %</td>
                        <td colspan="2" style="text-align:right;border:1px solid black;">%</td>
                    </tr>
                    <tr>
                        <td style="text-align:center;border:1px solid black;">7</td>
                        <td style="border:1px solid black;">VEP 1%( VEP 1 / KVP )</td>
                        <td colspan="2" style="text-align:center;border:1px solid black;">{{ $dataSpiro->hasil_7a }}%</td>
                        <td style="text-align: center;border:1px solid black;">%</td>
                        <td style="text-align:center;border:1px solid black;">75 %</td>
                        <td colspan="2" style="background-color:beige;border:1px solid black;"></td>
                    </tr>
                    <tr>
                        <td rowspan="3" style="text-align:center;border:1px solid black;">8</td>
                        <td rowspan="3" style="border:1px solid black;">Arus Puncak Ekspirasi</td>
                        <td style="text-align:center;border:1px solid black;">1</td>
                        <td style="text-align:center;border:1px solid black;">{{ $dataSpiro->pemeriksaan_8a }}</td>
                        <td rowspan="3" style="background-color:beige;border:1px solid black;"></td>
                        <td rowspan="3" style="background-color:beige;border:1px solid black;"></td>
                        <td colspan="2" style="text-align:right;border:1px solid black;">Ml/detik</td>
                    </tr>
                    <tr>
                        <td style="text-align:center;border:1px solid black;">2</td>
                        <td style="text-align:center;border:1px solid black;">{{ $dataSpiro->pemeriksaan_8b }}</td>
                        <td colspan="2" style="text-align:right;border:1px solid black;">Ml/detik</td>
                    </tr>
                    <tr>
                        <td style="text-align:center;border:1px solid black;">3</td>
                        <td style="text-align:center;border:1px solid black;">{{ $dataSpiro->pemeriksaan_8c }}</td>
                        <td colspan="2" style="text-align:right;border:1px solid black;">Ml/detik</td>
                    </tr>
                    <tr>
                        <td style="text-align:center;border:1px solid black;">9</td>
                        <td style="border:1px solid black;">Air Trapping</td>
                        <td colspan="2" style="background-color:beige;border:1px solid black;"></td>
                        <td style="background-color:beige;border:1px solid black;"></td>
                        <td  style="background-color:beige;border:1px solid black;"></td>
                        <td colspan="2" style="background-color:beige;border:1px solid black;"></td>
                    </tr>
                </tbody>
            </table>
            @php
                        $qr_dokter =
                        'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara
                        elektronik oleh' .
                        "\n" .
                        $dataSpiro->nama .
                        "\n" .
                        'ID ' .
                        $dataSpiro->kd_dokter .
                        "\n" .
                        \Carbon\Carbon::parse($dataSpiro->tanggal)->format('d-m-Y');
                        $qrcode_dokter = base64_encode(
                                 QrCode::format('png')->size(100)->errorCorrection('H')->generate($qr_dokter)
                             );
                    @endphp

            <table style="width: 100%; margin-bottom:50px; margin-top:50px; border: 0px solid black;" >
                <tbody>
                    <tr>
                        <td colspan="2">C. KESIMPULAN / HASIL :</td>
                        <td>CATATAN :</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 25px; width:5%;">1.</td>
                        <td style="width:45%">{{ $dataSpiro->kesimpulan_hasil_a }}</td>
                        <td style="width:50%" rowspan="3">{{ $dataSpiro->catatandokter }}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 25px;">2.</td>
                        <td>Restriksi : {{ $dataSpiro->kesimpulan_hasil_b }}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 25px;">3.</td>
                        <td>Obstruksi : {{ $dataSpiro->kesimpulan_hasil_c }}</td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td  style="text-align: center;">Surakarta, {{ \Carbon\Carbon::parse($dataSpiro->tanggal)->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td style="text-align: center;">Dokter Pemeriksa</td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td style="text-align: center;"><img src="data:image/png;base64, {!! $qrcode_dokter !!}"></td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td style="text-align: center;">{{ $dataSpiro->kd_dokter }}</td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td style="text-align: center;">{{ $dataSpiro->nm_dokter }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endif
    {{-- </main> --}}
    {{-- <footer>
        Dicetak dari Vedika@BiosGateRSUP pada {{ \Carbon\Carbon::now()->format('d/m/Y h:i:s') }}
    </footer> --}}
</body>

</html>
