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
        <table style="border: 0px solid black; width:100%;">
            <thead>
                <tr style="width:100%">
                    <td style="width:3%; border:0px solid black;"><img src="{{ public_path('image/logorsup.jpg') }}"
                            alt="Logo RSUP" width="20">
                    </td>
                    <th style="border:0px solid black; text-align:left"> RSUP SURAKARTA </th>
                    <td style="text-align:right; border:0px solid black; ">RM PP 01 Rev.1</td>
                </tr>
            </thead>
        </table>
        <table style="width: 100%; border: 1px solid black">
            <thead>
                <tr>
                    <th rowspan="5" style="width: 50%; border:1px solid black; text-align:center;">
                        <h4>PERSETUJUAN UMUM/<br><i>GENERAL CONSENT<i></h4>
                    </th>
                    <th
                        style="width: 15%; border-left:1px solid black; border-top: 1px solid black;text-align:left; padding-left:5px">
                        Nomor
                        RM
                    </th>
                    <th style="border-right: 1px solid black; border-top: 1px solid black;text-align:left; ">
                        :
                        {{ $data->no_rkm_medis }}</th>
                </tr>
                <tr>
                    <th style="border-left: 1px solid black; text-align:left; padding-left:5px">NIK </th>
                    <th style="border-right: 1px solid black; text-align:left;">
                        : {{ $data->ktp_pasien }}</th>
                </tr>
                <tr>
                    <th style="border-left: 1px solid black; text-align:left; padding-left:5px">Nama Pasien </th>
                    <th style="border-right: 1px solid black; text-align:left;">
                        : {{ $data->nm_pasien }}</th>
                </tr>
                <tr>
                    <th style="border-left: 1px solid black; text-align:left; padding-left:5px">Tanggal Lahir </th>
                    <th style="border-right: 1px solid black; text-align:left;">
                        : {{ \Carbon\Carbon::parse($data->tgl_lahir)->format('d-m-Y') }}</th>
                </tr>
                <tr>
                    <th
                        style="border-left: 1px solid black; border-bottom: 1px solid black;text-align:left; vertical-align:top; padding-left:5px">
                        Alamat</th>
                    <th style="border-right: 1px solid black;border-bottom: 1px solid black; text-align:left;">:
                        {{ $data->alamat }}</th>
                </tr>
            </thead>
        </table>
        <table style="width: 100%; border: 0px solid black; margin-top: 10px; text-align: justify;">
            <tbody>
                <tr class="">
                    <th style="width:5%; border-left:1px solid black; border-top:1px solid black; " class="text-center">
                        1.</th>
                    <th style="border-top:1px solid black; border-right:1px solid black; ">HAK DAN KEWAJIBAN SEBAGAI
                        PASIEN.</th>
                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black; " class="text-center"></td>
                    <td style="border-right:1px solid black;">Saya mengakui bahwa pada proses pendaftaran untuk
                        mendapatkan
                        perawatan di RSUP Surakarta dan penandatanganan dokumen ini,
                        saya telah mendapat informasi tentang hak-hak dan kewajiban saya
                        sebagai pasien. </td>
                </tr>
                <tr>
                    <th style="width:5%; border-left:1px solid black; " class="text-center">2.</th>
                    <th style="border-right:1px solid black;">PERSETUJUAN PELAYANAN KESEHATAN</th>

                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black; " class="text-center"></td>
                    <td style="border-right:1px solid black;">Saya menyetujui dan memberikan persetujuan untuk mendapat
                        pelayanan
                        kesehatan di RSUP Surakarta dan dengan ini saya meminta dan
                        memberikan kuasa kepada dokter, perawat/ bidan, dan tenaga kesehatan
                        lainnya untuk memberikan asuhan keperawatan/kebidanan,
                        pemeriksaan fisik yang dilakukan oleh dokter, perawat/ bidan dan
                        melakukan prosedur diagnostik, radiologi dan/atau terapi dan
                        tatalaksana sesuai pertimbangan dokter yang diperlukan atau
                        disarankan pada perawatan saya. Hal ini mencakup seluruh pemeriksaan
                        dan
                        prosedur diagnostik rutin, termasuk X-ray, pemberian dan/atau
                        tindakan medis serta penyuntikan (intramuskular, intravena, cateter,
                        nasogastrictube ( NGT ), nasal kanul, partus normal dan prosedur
                        invasif lainnya) produk farmasi dan obat-obatan, pemasangan alat
                        kesehatan (kecuali yang membutuhkan persetujuan khusus/tertulis),
                        dan pengambilan darah untuk pemeriksaan laboratorium atau
                        pemeriksaan patologi yang dibutuhkan untuk pengobatan dan tindakan
                        yang aman. </td>
                </tr>
                <tr>
                    <th style="width:5%; border-left:1px solid black; " class="text-center">3.</th>
                    <th style="border-right:1px solid black;">KEYAKINAN DAN NILAI NILAI.</th>

                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black; " class="text-center"></td>
                    <td style="border-right:1px solid black;">Rumah Sakit Umum Pusat Surakarta sangat menghargai
                        keyakinan, nilai
                        nilai dan agama yang dianut oleh pasien, selama memperoleh
                        pelayanan kesehatan diantaranya:</td>
                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black; " class="text-center"></td>
                    <td style="border-right:1px solid black;">
                        @if (!empty($berkas->keyakinan1))
                            a. {{ $berkas->keyakinan1 }}
                        @else
                            a. .........................................
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black; " class="text-center"></td>
                    <td style="border-right:1px solid black;">
                        @if (!empty($berkas->keyakinan2))
                            b. {{ $berkas->keyakinan2 }}
                        @else
                            b. .........................................
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black; " class="text-center"></td>
                    <td style="border-right:1px solid black;">
                        @if (!empty($berkas->keyakinan3))
                            c. {{ $berkas->keyakinan3 }}
                        @else
                            c. .........................................
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black; " class="text-center"></td>
                    <td style="border-right:1px solid black;">
                        @if (!empty($berkas->keyakinan4))
                            d. {{ $berkas->keyakinan4 }}
                        @else
                            d. .........................................
                        @endif
                    </td>
                </tr>
                <tr>
                    <th style="width:5%; border-left:1px solid black; " class="text-center">4.</th>
                    <th style="border-right:1px solid black;">PRIVASI</th>
                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black;" class="text-center"></td>
                    <td style="border-right:1px solid black;">Saya memberi kuasa kepada RSUP Surakarta dan atau :</td>
                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black; " class="text-center"></td>
                    <td style="border-right:1px solid black;">
                        @if (!empty($berkas->privasi1))
                            a. {{ $berkas->privasi1 }}
                        @else
                            a. .........................................
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black; " class="text-center"></td>
                    <td style="border-right:1px solid black;">
                        @if (!empty($berkas->privasi2))
                            b. {{ $berkas->privasi2 }}
                        @else
                            b. .........................................
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black; " class='text-center'></td>
                    <td style="border-right:1px solid black;">
                        @if (!empty($berkas->privasi3))
                            c. {{ $berkas->privasi3 }}
                        @else
                            c. .........................................
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black; " class='text-center'></td>
                    <td style="border-right:1px solid black;">Untuk menjaga privasi dan kerahasian penyakit saya selama
                        dalam
                        perawatan serta memberikan persetujuan tindakan medis
                        Dalam keadaan tertentu seperti di ruang isolasi dan isolasi dengan
                        penyakit menular tertentu, maka :
                    </td>
                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black; " class='text-center'></td>
                    <td style="border-right:1px solid black;">a. Selama perawatan, keluarga tidak diizinkan untuk
                        menunggu,
                        kecuali dalam keadaan khusus sesuai indikasi medis dan keperawatan
                    </td>
                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black; " class='text-center'></td>
                    <td style="border-right:1px solid black;">b. Selama menunggu tidak boleh keluar masuk dan sering
                        berganti
                    </td>
                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black; " class='text-center'></td>
                    <td style="border-right:1px solid black;">c. Rumah Sakit Umum Pusat Surakarta tidak bertanggung
                        jawab terhadap
                        risiko yang ditimbulkan selama menunggu</td>
                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black; " class='text-center'></td>
                    <td style="border-right:1px solid black;">d. Pengawasan dan kebutuhan pasien selama diruang
                        perawatan
                        diberikan oleh petugas kesehatan</td>
                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black; " class='text-center'></td>
                    <td style="border-right:1px solid black;">e. Pengawasan pasien diruang perawatan dapat menggunakan
                        kemajuan
                        teknologi saat ini, misalnya CCTV</td>
                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black; " class='text-center'></td>
                    <td style="border-right:1px solid black; ">f. Komunikasi dan edukasi
                        kepada keluarga menggunakan video call, Whatsap saat ini yang berlaku di RSUP Surakarta
                    </td>
                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; "
                        class='text-center' colspan="2">
                        <div style="float: none;">
                            <div style="page-break-after: always;"></div>
                        </div>
                    </td>

                </tr>
                {{-- <div style="float: none;">
                    <div style="page-break-after: always;"></div>
                </div> --}}
                <tr>
                    <th style="width:5%; border-left:1px solid black; border-top: 1px solid black;"
                        class='text-center'>
                        5.</th>
                    <th style="border-right:1px solid black; border-top: 1px solid black;">RAHASIA INFORMASI KESEHATAN.
                    </th>
                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black; " class='text-center'></td>
                    <td style="border-right:1px solid black;">Saya setuju RSUP Surakarta wajib menjamin rahasia
                        informasi
                        kesehatan saya baik untuk kepentingan perawatan atau pengobatan,
                        pendidikan maupun penelitian kecuali saya mengungkapkan sendiri atau
                        orang lain yang saya berikuasa sebagai Penjamin. </td>
                </tr>
                <tr>
                    <th style="width:5%; border-left:1px solid black; " class='text-center'>6.</th>
                    <th style="border-right:1px solid black;">MEMBUKA RAHASIA INFORMASI KESEHATAN.</th>
                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black; " class='text-center'></td>
                    <td style="border-right:1px solid black;">a. Saya setuju untuk membuka rahasia informasi kesehatan
                        terkait
                        dengan kondisi kesehatan, asuhan dan pengobatan yang saya terima
                        kepada: </td>
                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black; " class='text-center'></td>
                    <td class="ml-3" style="border-right:1px solid black;">- Dokter dan tenaga kesehatan lain yang
                        memberikan asuhan kepada saya</td>
                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black; " class='text-center'></td>
                    <td style="border-right:1px solid black;">- Perusahaan asuransi kesehatan atau perusahaan lainnya
                        atau pihak
                        lain yang menjamin pembiayaan saya</td>
                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black; " class='text-center'></td>
                    <td style="border-right:1px solid black;">- Kepentingan dalam rangka untuk pencegahan dan
                        pengendalian
                        penyakit menular tertentu sesuai ketentuan yang berlaku</td>
                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black; " class='text-center'></td>
                    <td style="border-right:1px solid black;">- Kepentingan hukum.</td>
                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black; " class='text-center'></td>
                    <td style="border-right:1px solid black;">b. Saya mengetahui dan menyetujui bahwa berdasarkan
                        Peraturan
                        Menteri Kesehatan Nomor 24 Tahun 2022 tentang Rekam Medis,
                        fasilitas pelayanan kesehatan wajib membuka akses dan mengirim data
                        rekam medis kepada Kementerian Kesehatan melalui
                        Platform SATU SEHAT.</td>
                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black; " class='text-center'></td>
                    <td style="border-right:1px solid black;">c. Menyetujui untuk menerima dan membuka data Pasien dari
                        Fasilitas
                        Pelayanan Kesehatan lainnya melalui SATU SEHAT untuk
                        kepentingan pelayanan kesehatan dan/atau rujukan.
                    </td>
                </tr>
                <tr>
                    <th style="width:5%; border-left:1px solid black; " class='text-center'>7.</th>
                    <th style="border-right:1px solid black;">BARANG PRIBADI. </th>
                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black; " class='text-center'></td>
                    <td style="border-right:1px solid black;">Saya setuju untuk tidak membawa barang-barang berharga
                        yang tidak
                        diperlukan (seperti: perhiasan, elektronik, dll) selama dalam
                        perawatan. Saya memahami dan menyetujui bahwa apabila saya
                        membawanya, maka RSUP Surakarta tidak bertanggungjawab terhadap
                        kehilangan, kerusakan atau pencurian .</td>
                </tr>
                <tr>
                    <th style="width:5%; border-left:1px solid black; " class='text-center'>8.</th>
                    <th style="border-right:1px solid black;">PENELITIAN/ CLINICAL TRIAL</th>
                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black; " class='text-center'></td>
                    <td style="border-right:1px solid black;">Apabila saya dilibatkan dalam penelitian atau prosedur
                        eksperimental, maka hal tersebut hanya dapat dilakukan dengan
                        sepengetahuan dan persetujuan saya</td>
                </tr>
                <tr>
                    <th style="width:5%; border-left:1px solid black; " class='text-center'>9.</th>
                    <th style="border-right:1px solid black;">PENDIDIKAN</th>
                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black; " class='text-center'></td>
                    <td style="border-right:1px solid black;">Saya setuju untuk mengizinkan tenaga medis, keperawatan,
                        dan tenaga
                        kesehatan lainnya dalam pendidikan/pelatihan, kecuali diminta
                        sebaliknya, untuk hadir selama perawatan pasien, atau berpartisipasi
                        dalam perawatan pasien sebagai bagian dari pendidikan mereka</td>
                </tr>
                <tr>
                    <th style="width:5%; border-left:1px solid black; " class='text-center'>10.</th>
                    <th style="border-right:1px solid black;">PENGAJUAN KELUHAN. </th>
                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black; " class='text-center'></td>
                    <td style="border-right:1px solid black;">Saya menyatakan bahwa saya telah menerima informasi
                        tentang adanya
                        tatacara mengajukan dan mengatasi keluhan terkait pelayanan
                        medik yang diberikan terhadap diri saya. Saya setuju untuk mengikuti
                        tatacara mengajukan keluhan sesuai prosedur yang ada.
                    </td>
                </tr>
                <tr>
                    <th style="width:5%; border-left:1px solid black; " class='text-center'>11.</th>
                    <th style="border-right:1px solid black;">KEWAJIBAN PEMBAYARAN. </th>
                </tr>
                <tr>
                    <td style="width:5%;border-left:1px solid black; " class='text-center'></td>
                    <td style="border-right:1px solid black;">Saya menyatakan setuju, baik sebagai wali atau sebagai
                        pasien, bahwa
                        sesuai pertimbangan pelayanan yang diberikan kepada pasien,
                        maka saya wajib untuk membayar total biaya pelayanan. Biaya
                        pelayanan berdasarkan acuan biaya dan ketentuan di RSUP Surakarta.
                        Saya juga menyadari dan memahami bahwa:
                    </td>
                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black; " class='text-center'></td>
                    <td style="border-right:1px solid black;">a. Apabila saya tidak memberikan persetujuan, atau di
                        kemudian hari
                        mencabut persetujuan saya untuk melepaskan rahasia kedokteran
                        saya kepada perusahaan asuransi yang saya tentukan, maka saya
                        pribadi bertanggung jawab untuk membayar semua pelayanan dan
                        tindakan medis dari RSUP Surakarta
                    </td>
                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black; " class='text-center'></td>
                    <td style="border-right:1px solid black; ">b. Apabila rumah sakit
                        membutuhkan proses hukum untuk
                        menagih biaya
                        pelayanan rumah sakit dari saya, saya memahami bahwa saya
                        bertanggung jawab untuk membayar semua biaya yang disebabkan dari
                        proses hukum tersebut.
                    </td>
                </tr>
                <tr>
                    <td style="width:5%; border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; "
                        class='text-center' colspan="2">
                        <div style="float: none;">
                            <div style="page-break-after: always;"></div>
                        </div>
                    </td>

                </tr>
                <tr>
                    <td colspan='2'
                        style='padding-left:5px; border-top:1px solid black; border-left:1px solid black; border-right:1px solid black'>
                        SAYA TELAH MEMBACA / DIBACAKAN / dan SEPENUHNYA SETUJU dengan setiap pernyataan yang terdapat
                        pada formulir ini dan menanda tangani tanpa paksaan dan dengan kesadaran penuh.
                    </td>
                <tr>
            </tbody>
        </table>
        <table style="width: 100%; border: 0px solid black;">
            <tbody>
                <tr>
                    <td style="border-left: 1px solid black; text-align:center; ">
                    </td>
                    @php
                        $penetapan = \Carbon\Carbon::parse($berkas->updated_at)->locale('id');

                        $qr_informan = 'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara elektronik oleh' . "\n" . $berkas->user->name . "\n" . 'ID ' . $berkas->user_id . "\n" . \Carbon\Carbon::parse($berkas->updated_at)->format('d-m-Y');
                        $qrcode_informan = base64_encode(QrCode::format('svg')->size(100)->errorCorrection('H')->generate($qr_informan));
                    @endphp
                    <td style="border-right: 1px solid black; text-align:center; ">Surakarta,
                        {{ $penetapan->format('j F Y') }} Jam {{ $penetapan->format('H:i') }} WIB
                    </td>
                </tr>
                <tr>
                    <td
                        style="border-left: 1px solid black; border-right: 0px solid black; padding-top:0px; text-align:center; vertical-align:text-top;">
                        Pasien/ Keluarga/ <br>Penanggung Jawab
                    </td>
                    <td style="text-align:center; border-right:1px solid black; vertical-align:text-top;">
                        Pemberi Informasi
                    </td>
                </tr>
                <tr>

                    <td style="border-left: 1px solid black;width: 50%; text-align:center">
                        <img src="{{ $berkas->tandaTangan }}" width="auto" height="100px"
                            style="padding-left:0px; border:0px solid #555;">
                    </td>
                    <td style='text-align:center; border-right:1px solid black;'>
                        {{-- {!! QrCode::size(100)->generate($qr_informan) !!} --}}
                        <img src="data:image/png;base64, {!! $qrcode_informan !!}">
                    </td>
                </tr>
                <tr>
                    <td
                        style="text-align:center; border-left:1px solid black; border-bottom:1px solid black; text-transform:uppercase">
                        ( {{ $berkas->namaPj }} )
                    </td>
                    <td
                        style="border-left: 0px solid black; border-right: 1px solid black; border-bottom: 1px solid black; text-align:center; text-transform:uppercase; padding-bottom:10px">

                        ( {{ $berkas->user->name }} )

                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div style="float: none;">
        <div style="page-break-after: always;"></div>
    </div>
    {{-- Kewajiban PASIEN --}}
    <div class="watermark">
        {{ $watermark }}
    </div>
    <div>
        <table style="border: 0px solid black; width:100%;">
            <thead>
                <tr style="width:100%">
                    <td style="width:3%; border:0px solid black;"><img src="{{ public_path('image/logorsup.jpg') }}"
                            alt="Logo RSUP" width="20">
                    </td>
                    <th style="border:0px solid black;"> RSUP SURAKARTA </th>
                    <td style="text-align:right; border:0px solid black; ">RM PP 01 Rev.1</td>
                </tr>
            </thead>
        </table>
        <table style="width: 100%; border: 1px solid black; border-bottom: 0px solid black; ">
            <thead>
                <tr class="text-center">
                    <th colspan='3' style='padding-top: 20px'>SURAT PERNYATAAN PEMILIHAN DOKTER</th>
                </tr>
                <tr class="text-center">
                    <th colspan='3'>( OLEH PASIEN / KELUARGA / PENANGGUNG JAWAB)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan='3' style='padding-top:30px; padding-left:30px'>Yang bertanda tangan di
                        bawah ini :</td>
                </tr>
                <tr>
                    <td width='40%' class='align-middle pl-5'>Nama</td>
                    <td colspan='2'> : {{ $berkas->namaPj }}
                    </td>
                </tr>
                <tr>
                    <td width='40%' class='align-middle pl-5'>Tanggal Lahir/ Umur</td>
                    <td>
                        : {{ \Carbon\Carbon::parse($berkas->tglLahirPj)->format('d-m-Y') }}
                    </td>
                    <td width='20%'>
                        / {{ $berkas->umurPj }} Tahun
                    </td>
                </tr>
                <tr>
                    <td width='40%' class='align-middle pl-5'>Alamat</td>
                    <td colspan='2'>: {{ $berkas->alamatPj }}</td>
                </tr>
                <tr>
                    <td colspan='3' style='padding-left:30px'>Atas nama pasien tersebut di
                        bawah ini :</td>
                </tr>
                <tr>
                    <td width='40%' class='align-middle pl-5'>Nama</td>
                    <td colspan='2'>: {{ $data->nm_pasien }}</td>
                </tr>
                <tr>
                    <td width='40%' class='align-middle pl-5'>Tanggal Lahir/Umur</td>
                    <td>: {{ Carbon\Carbon::parse($data->tgl_lahir)->format('d-m-Y') }}
                    </td>
                    <td width='20%'>
                        /
                        {{ \Carbon\Carbon::parse($data->tgl_lahir)->diffInYears(\Carbon\Carbon::parse($berkas->created_at)) }}
                        Tahun
                    </td>
                </tr>
                <tr>
                    <td width='40%' class='align-middle pl-5'>No. Rekam Medik</td>
                    <td colspan='2'>: {{ $data->no_rkm_medis }}</td>
                </tr>
                <tr>
                    <td colspan="3" style='padding-left:30px; text-align:justify;'>Dengan ini menyatakan bahwa
                        untuk menangani
                        penyakit atau gangguan kesehatan saya / orang tua/ anak / suami /
                        isteri / keluarga, saya
                        memilih dokter <b>{{ $berkas->dpjp }}</b>.</td>
                </tr>

                <tr>
                    <td colspan="3" style='padding-left:30px; text-align:justify;'> Apabila di kemudian hari
                        dokter tersebut
                        berhalangan karena alasan yang dapat
                        dimengerti maka untuk meneruskan perawatan, saya :
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style='padding-left:30px; text-align:justify;'>1. Memberikan kuasa kepada pihak
                        rumah sakit
                        untuk menunjuk dokter pengganti yang sederajat.
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style='padding-left:30px; text-align:justify;'>2. Memberikan kuasa kepada
                        dokter tersebut di
                        atas untuk menunjuk dokter pengganti yang sederajat
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style='padding-left:30px; text-align:justify;'>3. Menentukan sendiri dokter
                        pengganti
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style='padding-left:30px; text-align:justify;'>Demikian surat pernyataan ini
                        saya buat dengan sebenar-benarnya.
                    </td>
                </tr>
            </tbody>
        </table>
        <table style="width: 100%; border: 0px solid black; ">
            <tbody>

                <tr>
                    <td
                        style="border-left: 1px solid black; border-right: 0px solid black; padding-top:20px; text-align:center; vertical-align: text-top;">
                        Pasien/ Keluarga/ <br>Penanggung Jawab
                    </td>
                    <td
                        style="text-align:center; border-right:1px solid black; vertical-align: text-top; padding-top:20px">
                        Pemberi Informasi <br> (Petugas Pendaftaran)
                    </td>
                </tr>
                <tr>

                    <td
                        style="border-left: 1px solid black;width: 50%; text-align:center; padding-top:0px;padding-bottom:0px;">
                        <img src="{{ $berkas->tandaTangan }}" width="auto" height="80px"
                            style="padding-left:0px; border:0px solid #555;">
                    </td>
                    <td style='text-align:center; border-right:1px solid black;'>
                        {{-- {!! QrCode::size(100)->generate($qr_informan) !!} --}}
                        <img src="data:image/png;base64, {!! $qrcode_informan !!}">
                    </td>
                </tr>
                <tr>
                    <td style="text-align:center; border-left:1px solid black; border-bottom:1px solid black">
                        ( {{ $berkas->namaPj }} )
                    </td>
                    <td
                        style="border-left: 0px solid black; border-right: 1px solid black; border-bottom: 1px solid black; text-align:center; text-transform:uppercase">
                        ( {{ $berkas->user->name }} )
                    </td>
                </tr>
            </tbody>
        </table>
        {{-- </main> --}}
        {{-- <footer>
        Dicetak dari Vedika@BiosGateRSUP pada {{ \Carbon\Carbon::now()->format('d/m/Y h:i:s') }}
    </footer> --}}
</body>

</html>
