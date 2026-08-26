<!DOCTYPE html>
<html lang="en">

<head>
    <title>Form General Consent</title>
    {{-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous"> --}}
    <link rel="stylesheet" href="{{ public_path('template/dist/css/adminlte.min.css') }}">
    <!-- Font Awesome Icons -->
    {{-- <link rel="stylesheet" href="{{ public_path('template/plugins/fontawesome-free/css/all.min.css') }}"> --}}
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
            margin-top: 30px
        }
    </style>
</head>

<body>
    @php
        $watermark = '';
    @endphp
    <style>
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
            top: 25%;
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
    <div class="watermark">
        {{ $watermark }}
    </div>
    <div>
        <img src="{{ asset('image/kop.png') }}" alt="KOP RSUP">
        <table style="width: 100%; border: 0px solid black; margin-top: 0px; text-align: justify;">
            <tbody>
                <tr>
                    <td colspan="2"
                        style="text-align: center; text-decoration: underline; font-size: 16px; font-weight: bold; padding-bottom: 20px;">
                        SURAT
                        PERSETUJUAN / PENOLAKAN RAWAT INAP</td>
                </tr>
                <tr>
                    <td colspan="2">Yang bertanda tangan dibawah ini : </td>
                </tr>
                <tr>
                    <td style="width:25%; vertical-align: top;">Nama</td>
                    <td>: {{ $berkas ? $berkas->nama_pj : '' }} </td>
                </tr>
                <tr>
                    <td style="width:25%; vertical-align: top;">Tempat/ Tgl Lahir</td>
                    <td>: {{ $berkas ? $berkas->tempat_lahir_pj : '' }} /
                        {{ $berkas ? $berkas->tanggal_lahir_pj->locale('id')->translatedFormat('d F Y') : '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width:25%; vertical-align: top;">Jenis Kelamin</td>
                    <td>: {{ $berkas->jenis_kelamin_pj == 'L' ? 'Laki-laki' : 'Perempuan' }} </td>
                </tr>
                <tr>
                    <td style="width:25%; vertical-align: top;">Alamat</td>
                    <td>: {{ $berkas ? $berkas->alamat_pj : '' }} </td>
                </tr>
                <tr>
                    <td style="width:25%; vertical-align: top;">Pekerjaan</td>
                    <td>: {{ $berkas ? $berkas->pekerjaan_pj : '' }} </td>
                </tr>
                <tr>
                    <td style="width:25%; vertical-align: top;">No KTP</td>
                    <td>: {{ $berkas ? $berkas->no_ktp_pj : '' }} </td>
                </tr>
                <tr>
                    <td style="width:25%; vertical-align: top;">No Telepon</td>
                    <td>: {{ $berkas ? $berkas->no_telepon_pj : '' }} </td>
                </tr>
                <tr>
                    <td style="width:25%; vertical-align: top;">Bertindak untuk</td>
                    <td>: {{ $berkas ? $berkas->hubungan_pj : '' }} </td>
                </tr>
                <tr>
                    <td style="width:25%; vertical-align: top;">Bertindak untuk</td>
                    <td>: {{ $berkas ? $berkas->hubungan_pj : '' }} </td>
                </tr>
                <tr>
                    <td style="width:25%; vertical-align: top;">Nama Pasien</td>
                    <td>: {{ $berkas ? $berkas->nama_pasien : '' }}</td>
                </tr>
                <tr>
                    <td style="width:25%; vertical-align: top;">Tempat/ Tgl Lahir
                        Pasien</td>
                    <td>: {{ $berkas ? $berkas->tempat_lahir_pasien : '' }},
                        {{ $berkas ? $berkas->tanggal_lahir_pasien->locale('id')->translatedFormat('d F Y') : '' }}
                    </td>
                </tr>

                <tr>
                    <td style="width:25%; vertical-align: top;">Jenis Kelamin Pasien
                    </td>
                    <td>: {{ $berkas->jenis_kelamin_pasien == 'L' ? 'Laki-laki' : 'Perempuan' }}
                    </td>
                </tr>
                <tr>
                    <td style="width:25%; vertical-align: top;">Alamat Pasien</td>
                    <td>: {{ $berkas->alamat_pasien }}
                    </td>
                </tr>
                <tr>
                    <td style="width:25%; vertical-align: top;">Nomor Rekam Medis</td>
                    <td>: {{ $berkas ? $berkas->no_rm : '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width:25%; vertical-align: top;">Cara Bayar</td>
                    <td>: {{ $berkas ? $berkas->cara_bayar : '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width:25%; vertical-align: top;">Hak Kelas Rawat</td>
                    <td style="vertical-align: top;">
                        : {{ $berkas ? $berkas->kelas_rawat : '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width:25%; vertical-align: top;">Pindah Kelas Rawat yang
                        diinginkan</td>
                    <td style="vertical-align: top;">: {{ $berkas ? $berkas->pindah_kelas_rawat : '' }}</td>
                </tr>
            </tbody>
        </table>
        <table style="width: 100%; border: 0px solid black; margin-top: 10px; text-align: justify;">
            <tbody>
                <tr>
                    <td colspan="2" class="pt-3">Dengan ini menyatakan bahwa saya telah
                        menerima
                        informasi dari petugas kesehatan RSUP Surakarta untuk rencana rawat
                        inap pasien diatas dan sudah memahaminya, maka saya : </td>
                </tr>
                <tr>
                    <td style="width:5%; text-align: center; vertical-align: top;">1. </td>
                    <td><b>{{ $berkas ? $berkas->status_persetujuan : '' }}</b> dilakukan pelayanan rawat inap
                        di RSUP Surakarta kepada pasien tersebut diatas.</td>
                </tr>
                <tr>
                    <td style="width:5%; text-align: center; vertical-align: top;">2. </td>
                    <td>Meminta dan memberikan kuasa kepada dokter, perawat/ bidan, dan tenaga
                        kesehatan lainnya untuk memberikan asuhan keperawatan/kebidanan,
                        pemeriksaan fisik yang dilakukan oleh dokter, perawat/ bidan dan
                        melakukan prosedur diagnostik, radiologi dan/atau terapi serta
                        tatalaksana sesuai pertimbangan dokter yang diperlukan atau disarankan
                        pada perawatan pasien diatas. </td>
                </tr>
                <tr>
                    <td style="width:5%; text-align: center; vertical-align: top;">3. </td>
                    <td>Telah mengetahui hak dan kewajiban pasien dan akan mentaati seluruh
                        peraturan yang berlaku di RSUP Surakarta. </td>
                </tr>
                <tr>
                    <td style="width:5%; text-align: center; vertical-align: top;">4. </td>
                    <td> Telah mengetahui tarif dan fasilitas yang tersedia di rumah sakit,
                        khususnya pada ruang rawat yang akan ditempati. </td>
                </tr>
                <tr>
                    <td style="width:5%; text-align: center; vertical-align: top;">5. </td>
                    <td>Pasien Umum :
                        <ul>
                            <li> Bersedia membayar seluruh biaya perawatan dan tindakan yang
                                telah
                                diberikan kepada pasien tersebut diatas. </li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td style="width:5%; text-align: center; vertical-align: top;">6. </td>
                    <td> Pasien JKN/ Asuransi
                        <ul>
                            <li> Bersedia dirawat di ruang perawatan yang sesuai dengan hak
                                kelas
                                perawatan pasien tersebut diatas. </li>
                            <li> Bersedia di titipkan di ruang perawatan lebih tinggi atau lebih
                                rendah
                                dari hak kelas perawatan pasien tersebut diatas, apabila ruangan
                                perawatan yang sesuai hak kelas pasien dalam kondisi penuh.</li>
                            <li> Bersedia melengkapi berkas persyaratan penjaminan biaya
                                perawatan
                                dalam waktu 3x24 jam, apabila melebihi batas waktu tersebut dan
                                terjadi
                                masalah ketidakaktifan nomor kartu JKN/ asuransi maka bersedia
                                beralih
                                menjadi pasien umum.**</li>
                            <li> Bersedia membayar seluruh selisih tarif biaya perawatan yang
                                telah
                                dijalani pasien sesuai dengan hasil perhitungan tarif INA CBG
                                jika kelas
                                yang dipilih diatas hak kelas perawatan peserta JKN/
                                Asuransi.***</li>
                            <li> Jika naik kelas diatas kelas 1 / VIP bersedia membayar tambahan
                                biaya sesuai dengan peraturan yang berlaku di Rumah Sakit Umum
                                Pusat Surakarta.*** </li>
                            <li>Pembayaran selisih tarif biaya perawatan tersebut menjadi urusan
                                antara pasien/ keluarga pasien dengan RSUP Surakarta tanpa
                                melibatkan pihak perusahan penjamin.*** </li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td style="width:5%; text-align: center; vertical-align: top;">7. </td>
                    <td> Bersedia mematuhi rencana terapi yang direkomendasikan oleh dokter,
                        jika menolak rencana atas permintaan sendiri maka saya bersedia
                        menanggung segala konsekuensi termasuk dalam hal jaminan pembiayaan.
                    </td>
                </tr>
                <tr>
                    <td style="width:5%; text-align: center; vertical-align: top;">8. </td>
                    <td> Bertanggungjawab penuh atas segala resiko yang muncul dan tidak akan
                        menuntut pihak RSUP Surakarta dikemudian hari.
                    </td>
                </tr>
                <tr>
                    <td style="width:5%; text-align: center; vertical-align: top;">9. </td>
                    <td> Menyetujui jenis pembiayaan perawatan sesuai dengan jenis pembiayaan
                        yang dipilih dari awal masuk sampai dengan dinyatakan pulang.
                    </td>
                </tr>
                <tr>
                    <td colspan="2">Demikian surat pernyataan ini saya buat dengan penuh
                        kesadaran tanpa ada paksaan dari pihak manapun dan dapat digunakan
                        sebagaimana mestinya. </td>
                </tr>
            </tbody>
        </table>
        <table style="width: 100%; border: 0px solid black;">
            <tbody>
                <tr>
                    <td style="border-left: 0px solid black; text-align:center; ">
                    </td>
                    @php
                        $penetapan = \Carbon\Carbon::parse($berkas->updated_at)->locale('id');

                        $qr_informan =
                            'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara elektronik oleh' .
                            "\n" .
                            $berkas->petugas->name .
                            "\n" .
                            'ID ' .
                            $berkas->petugas_id .
                            "\n" .
                            \Carbon\Carbon::parse($berkas->updated_at)->format('d-m-Y');
                        $qrcode_informan = base64_encode(
                            QrCode::format('svg')->size(100)->errorCorrection('H')->generate($qr_informan),
                        );
                    @endphp
                    <td style="border-right: 0px solid black; text-align:center; padding-top:20px;">Surakarta,
                        {{ $penetapan->format('j F Y') }}
                    </td>
                </tr>
                <tr>
                    <td
                        style="border-left: 0px solid black; border-right: 0px solid black; padding-top:0px; text-align:center; vertical-align:text-top;">
                        Pasien/ Keluarga/ <br>Penanggung Jawab
                    </td>
                    <td style="text-align:center; border-right:0px solid black; vertical-align:text-top;">
                        Pemberi Informasi
                    </td>
                </tr>
                <tr>

                    <td style="border-left: 0px solid black;width: 50%; text-align:center">
                        <img src="{{ $berkas->tanda_tangan }}" width="auto" height="100px"
                            style="padding-left:0px; border:0px solid #555;">
                    </td>
                    <td style='text-align:center; border-right:0px solid black;'>
                        {{-- {!! QrCode::size(100)->generate($qr_informan) !!} --}}
                        <img src="data:image/png;base64, {!! $qrcode_informan !!}">
                    </td>
                </tr>
                <tr>
                    <td
                        style="text-align:center; border-left:0px solid black; border-bottom:0px solid black; text-transform:uppercase">
                        ( {{ $berkas->nama_pj }} )
                    </td>
                    <td
                        style="border-left: 0px solid black; border-right: 0px solid black; border-bottom: 0px solid black; text-align:center; text-transform:uppercase; padding-bottom:10px">

                        ( {{ $berkas->petugas->name }} )

                    </td>
                </tr>
                <tr>
                    <td style="font-size: 10px; padding-left: 10px;" colspan="2">
                        ** bagi pasien JKN/ asuransi <br>*** bagi pasien JKN naik kelas perawatan
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</body>

</html>
