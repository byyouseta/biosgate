<!DOCTYPE html>
<html lang="en">

<head>
    <title>Form Hak/ Kewajiban Pasien dan Keluarganya</title>
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
            margin-top: 10px
        }
    </style>
</head>

<body>
    @php
        $watermark = '';
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
                    <th style="border:0px solid black;"> RSUP SURAKARTA </th>
                    <td style="text-align:right; border:0px solid black; ">RM PP 02 Rev.1 Hal 1/2</td>
                </tr>
            </thead>
        </table>
        <table style="width: 100%; border: 1px solid black">
            <thead>
                <tr>
                    <th rowspan="5" style="width: 50%; border:1px solid black; text-align:center;">
                        <h4>HAK PASIEN<br> DAN KELUARGANYA</h4>
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
        <table style="width: 100%; border: 0px solid black; margin-top: 10px">
            <thead>
                <tr>
                    <th style="width: 5%; border:1px solid black; text-align:center;">No</th>
                    <th style="border:1px solid black; text-align:center;">Hak Pasien dan Keluarganya</th>
                    <th style="width: 15%; border:1px solid black; text-align:center;">Check List</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width: 5%; border:1px solid black; text-align:center;">1</td>
                    <td style="border:1px solid black; padding-left:5px">Memperoleh informasi mengenai tata tertib dan
                        peraturan yang
                        berlaku
                        di
                        rumah sakit</td>
                    <td style="width: 10%; border:1px solid black; text-align:center; vertical-align:baseline">
                        {{-- <div class="custom-control custom-checkbox">
                            <input class="custom-control-input custom-control-input-dark custom-control-input-outline"
                                type="checkbox" name="hak1" id="checkHak1" {{ $berkas->hak1 == 1 ? 'checked' : '' }}
                                onclick="return false;">
                            <label for="checkHak1" class="custom-control-label"></label>
                        </div> --}}

                        @if ($berkas->hak1 == 1)
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/check-square-regular.svg') }}" />
                        @else
                            <img width="15" src="{{ public_path('template/dist/img/check/square-regular.svg') }}" />
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 5%; border:1px solid black; text-align:center;">2</td>
                    <td style="border:1px solid black; padding-left:5px">Memperoleh informasi tentang hak dan kewajiban
                        pasien</td>
                    <td style="width: 10%; border:1px solid black; text-align:center;">
                        {{-- <div class="custom-control custom-checkbox">
                            <input class="custom-control-input custom-control-input-dark " type="checkbox"
                                name="hak2" id="checkHak2" {{ $berkas->hak2 == 1 ? 'checked' : '' }} value="1"
                                onclick="return false;">
                            <label for="checkHak2" class="custom-control-label"></label>
                        </div> --}}


                        @if ($berkas->hak2 == 1)
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/check-square-regular.svg') }}" />
                        @else
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/square-regular.svg') }}" />
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 5%; border:1px solid black; text-align:center;">3</td>
                    <td style="border:1px solid black; padding-left:5px">Memperoleh pelayanan yang manusiawi, adil, dan
                        jujur</td>
                    <td style="width: 10%; border:1px solid black; text-align:center;">
                        {{-- <div class="custom-control custom-checkbox">
                            <input class="custom-control-input custom-control-input-dark " type="checkbox"
                                name="hak3" id="checkHak3" {{ $berkas->hak3 == 1 ? 'checked' : '' }} value="1"
                                onclick="return false;">
                            <label for="checkHak3" class="custom-control-label"></label>
                        </div> --}}


                        @if ($berkas->hak3 == 1)
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/check-square-regular.svg') }}" />
                        @else
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/square-regular.svg') }}" />
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 5%; border:1px solid black; text-align:center;">4</td>
                    <td style="border:1px solid black; padding-left:5px">Memperoleh pelayanan medis yang bermutu sesuai
                        dengan standart
                        profesi
                        dan standar prosedur operasional</td>
                    <td style="width: 10%; border:1px solid black; text-align:center;">
                        {{-- <div class="custom-control custom-checkbox">
                            <input class="custom-control-input custom-control-input-dark " type="checkbox"
                                name="hak4" id="checkHak4" {{ $berkas->hak4 == 1 ? 'checked' : '' }} value="1"
                                onclick="return false;">
                            <label for="checkHak4" class="custom-control-label"></label>
                        </div> --}}


                        @if ($berkas->hak4 == 1)
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/check-square-regular.svg') }}" />
                        @else
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/square-regular.svg') }}" />
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 5%; border:1px solid black; text-align:center;">5</td>
                    <td style="border:1px solid black; padding-left:5px">Memperoleh layanan yang efektif dan efisien
                        sehingga pasien
                        terhindar
                        dari kerugian fisik dan materi</td>
                    <td style="width: 10%; border:1px solid black; text-align:center;">
                        {{-- <div class="custom-control custom-checkbox">
                            <input class="custom-control-input custom-control-input-dark " type="checkbox"
                                name="hak5" id="checkHak5" {{ $berkas->hak5 == 1 ? 'checked' : '' }} value="1"
                                onclick="return false;">
                            <label for="checkHak5" class="custom-control-label"></label>
                        </div> --}}


                        @if ($berkas->hak5 == 1)
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/check-square-regular.svg') }}" />
                        @else
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/square-regular.svg') }}" />
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 5%; border:1px solid black; text-align:center;">6</td>
                    <td style="border:1px solid black; padding-left:5px">Mengajukan pengaduan atas kealitas pelayanan
                        yang didapatkan
                    </td>
                    <td style="width: 10%; border:1px solid black; text-align:center;">
                        {{-- <div class="custom-control custom-checkbox">
                            <input class="custom-control-input custom-control-input-dark " type="checkbox"
                                name="hak6" id="checkHak6" {{ $berkas->hak6 == 1 ? 'checked' : '' }} value="1"
                                onclick="return false;">
                            <label for="checkHak6" class="custom-control-label"></label>
                        </div> --}}


                        @if ($berkas->hak6 == 1)
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/check-square-regular.svg') }}" />
                        @else
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/square-regular.svg') }}" />
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 5%; border:1px solid black; text-align:center;">7</td>
                    <td style="border:1px solid black; padding-left:5px">Memilih dokter dan kelas perawatan sesuai
                        dengan keinginannya
                        dan
                        peraturan yang berlaku di rumah sakit</td>
                    <td style="width: 10%; border:1px solid black; text-align:center;">
                        {{-- <div class="custom-control custom-checkbox">
                            <input class="custom-control-input custom-control-input-dark " type="checkbox"
                                name="hak7" id="checkHak7" {{ $berkas->hak7 == 1 ? 'checked' : '' }}
                                value="1" onclick="return false;">
                            <label for="checkHak7" class="custom-control-label"></label>
                        </div> --}}


                        @if ($berkas->hak7 == 1)
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/check-square-regular.svg') }}" />
                        @else
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/square-regular.svg') }}" />
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 5%; border:1px solid black; text-align:center;">8</td>
                    <td style="border:1px solid black; padding-left:5px">Meminta konsultasi tentang penyakit yang
                        dideritanya kepada
                        dokter
                        lain
                        yang mempunyai Surat Izin Praktek (SIP) baik didalam maupun diluar
                        rumah
                        sakit</td>
                    <td style="width: 10%; border:1px solid black; text-align:center;">
                        {{-- <div class="custom-control custom-checkbox">
                            <input class="custom-control-input custom-control-input-dark " type="checkbox"
                                name="hak8" id="checkHak8" {{ $berkas->hak8 == 1 ? 'checked' : '' }}
                                value="1" onclick="return false;">
                            <label for="checkHak8" class="custom-control-label"></label>
                        </div> --}}


                        @if ($berkas->hak8 == 1)
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/check-square-regular.svg') }}" />
                        @else
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/square-regular.svg') }}" />
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 5%; border:1px solid black; text-align:center;">9</td>
                    <td style="border:1px solid black; padding-left:5px">Mendapatkan privasi dan kerahasiaan penyakit
                        yang diderita
                        termasuk
                        data-data medisnya</td>
                    <td style="width: 10%; border:1px solid black; text-align:center;">
                        {{-- <div class="custom-control custom-checkbox">
                            <input class="custom-control-input custom-control-input-dark " type="checkbox"
                                name="hak9" id="checkHak9" {{ $berkas->hak9 == 1 ? 'checked' : '' }}
                                value="1" onclick="return false;">
                            <label for="checkHak9" class="custom-control-label"></label>
                        </div> --}}


                        @if ($berkas->hak9 == 1)
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/check-square-regular.svg') }}" />
                        @else
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/square-regular.svg') }}" />
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 5%; border:1px solid black; text-align:center;">10</td>
                    <td style="border:1px solid black; padding-left:5px">Mendapat informasi yang meliputi diagnosis dan
                        tata cara
                        tindakan
                        medis,
                        tujuan tindakan medis, alternatif tindakan, resiko dan komplikasi
                        yang
                        mungkin terjadi,
                        dan prognosis terhadap tindakan yang dilakukan serta perkiraan biaya
                        pengobatan</td>
                    <td style="width: 10%; border:1px solid black; text-align:center;">
                        {{-- <div class="custom-control custom-checkbox">
                            <input class="custom-control-input custom-control-input-dark " type="checkbox"
                                name="hak10" id="checkHak10" {{ $berkas->hak10 == 1 ? 'checked' : '' }}
                                value="1" onclick="return false;">
                            <label for="checkHak10" class="custom-control-label"></label>
                        </div> --}}

                        @if ($berkas->hak10 == 1)
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/check-square-regular.svg') }}" />
                        @else
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/square-regular.svg') }}" />
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 5%; border:1px solid black; text-align:center;">11</td>
                    <td style="border:1px solid black; padding-left:5px">Memberika persetujuan atau penolakan atas
                        tindakan yang akan
                        dilakukan
                        oleh tenaga kesehatan terhadap penyakit yang dideritanya</td>
                    <td style="width: 10%; border:1px solid black; text-align:center;">
                        {{-- <div class="custom-control custom-checkbox">
                            <input class="custom-control-input custom-control-input-dark " type="checkbox"
                                name="hak11" id="checkHak11" {{ $berkas->hak11 == 1 ? 'checked' : '' }}
                                value="1" onclick="return false;">
                            <label for="checkHak11" class="custom-control-label"></label>
                        </div> --}}


                        @if ($berkas->hak11 == 1)
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/check-square-regular.svg') }}" />
                        @else
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/square-regular.svg') }}" />
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 5%; border:1px solid black; text-align:center;">12</td>
                    <td style="border:1px solid black; padding-left:5px">Didampingi keluarganya dalam keadaan kritis
                    </td>
                    <td style="width: 10%; border:1px solid black; text-align:center;">
                        {{-- <div class="custom-control custom-checkbox">
                            <input class="custom-control-input custom-control-input-dark " type="checkbox"
                                name="hak12" id="checkHak12" {{ $berkas->hak12 == 1 ? 'checked' : '' }}
                                value="1" onclick="return false;">
                            <label for="checkHak12" class="custom-control-label"></label>
                        </div> --}}


                        @if ($berkas->hak12 == 1)
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/check-square-regular.svg') }}" />
                        @else
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/square-regular.svg') }}" />
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 5%; border:1px solid black; text-align:center;">13</td>
                    <td style="border:1px solid black; padding-left:5px">Menjalankan ibadah sesuai agama dan
                        kepercayaan yang dianutnya
                        selama
                        hal itu tidak mengganggu pasien lainnya</td>
                    <td style="width: 10%; border:1px solid black; text-align:center;">
                        {{-- <div class="custom-control custom-checkbox">
                            <input class="custom-control-input custom-control-input-dark " type="checkbox"
                                name="hak13" id="checkHak13" {{ $berkas->hak13 == 1 ? 'checked' : '' }}
                                value="1" onclick="return false;">
                            <label for="checkHak13" class="custom-control-label"></label>
                        </div> --}}


                        @if ($berkas->hak13 == 1)
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/check-square-regular.svg') }}" />
                        @else
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/square-regular.svg') }}" />
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 5%; border:1px solid black; text-align:center;">14</td>
                    <td style="border:1px solid black; padding-left:5px">Memperoleh keamanan dan keselamatan dirinya
                        selama dalam
                        perawatan
                        di
                        rumah sakit</td>
                    <td style="width: 10%; border:1px solid black; text-align:center;">
                        {{-- <div class="custom-control custom-checkbox">
                            <input class="custom-control-input custom-control-input-dark " type="checkbox"
                                name="hak14" id="checkHak14" {{ $berkas->hak14 == 1 ? 'checked' : '' }}
                                value="1" onclick="return false;">
                            <label for="checkHak14" class="custom-control-label"></label>
                        </div> --}}


                        @if ($berkas->hak14 == 1)
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/check-square-regular.svg') }}" />
                        @else
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/square-regular.svg') }}" />
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 5%; border:1px solid black; text-align:center;">15</td>
                    <td style="border:1px solid black; padding-left:5px">Mengajukan usul, saran, perbaikan atas
                        perlakuan rumah sakit
                        terhadap
                        dirinya</td>
                    <td style="width: 10%; border:1px solid black; text-align:center;">
                        {{-- <div class="custom-control custom-checkbox">
                            <input class="custom-control-input custom-control-input-dark " type="checkbox"
                                name="hak15" id="checkHak15" {{ $berkas->hak15 == 1 ? 'checked' : '' }}
                                value="1" onclick="return false;">
                            <label for="checkHak15" class="custom-control-label"></label>
                        </div> --}}


                        @if ($berkas->hak15 == 1)
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/check-square-regular.svg') }}" />
                        @else
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/square-regular.svg') }}" />
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 5%; border:1px solid black; text-align:center;">16</td>
                    <td style="border:1px solid black; padding-left:5px">Menolak pelayanan bimbingan rohani yang tidak
                        sesuai dengan
                        agama
                        dan
                        kepercayaan yang dianutnya</td>
                    <td style="width: 10%; border:1px solid black; text-align:center;">
                        {{-- <div class="custom-control custom-checkbox">
                            <input class="custom-control-input custom-control-input-dark " type="checkbox"
                                name="hak16" id="checkHak16" {{ $berkas->hak16 == 1 ? 'checked' : '' }}
                                value="1" onclick="return false;">
                            <label for="checkHak16" class="custom-control-label"></label>
                        </div> --}}


                        @if ($berkas->hak16 == 1)
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/check-square-regular.svg') }}" />
                        @else
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/square-regular.svg') }}" />
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 5%; border:1px solid black; text-align:center;">17</td>
                    <td style="border:1px solid black; padding-left:5px">Menggugat dan / atau menuntut rumah sakit
                        apabila rumah sakit
                        diduga
                        memberikan pelayanan yang tidak sesuai dengan standart
                        baik secara perdata ataupun pidana</td>
                    <td style="width: 10%; border:1px solid black; text-align:center;">
                        {{-- <div class="custom-control custom-checkbox">
                            <input class="custom-control-input custom-control-input-dark " type="checkbox"
                                name="hak17" id="checkHak17" {{ $berkas->hak17 == 1 ? 'checked' : '' }}
                                value="1" onclick="return false;">
                            <label for="checkHak17" class="custom-control-label"></label>
                        </div> --}}


                        @if ($berkas->hak17 == 1)
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/check-square-regular.svg') }}" />
                        @else
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/square-regular.svg') }}" />
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 5%; border:1px solid black; text-align:center;">18</td>
                    <td style="border:0.5px solid black; padding-left:5px">Mengeluhkan pelayanan rumah sakit yang tidak
                        sesuai dengan
                        standar
                        pelayanan melalui media cetak dan elektronik
                        sesuai dengan ketentuan perundang-undangan</td>
                    <td style="width: 10%; border:0.5px solid black; text-align:center;">
                        {{-- <div class="custom-control custom-checkbox">
                            <input class="custom-control-input custom-control-input-dark " type="checkbox"
                                name="hak18" id="checkHak18" {{ $berkas->hak18 == 1 ? 'checked' : '' }}
                                value="1" onclick="return false;">
                            <label for="checkHak18" class="custom-control-label"></label>
                        </div> --}}


                        @if ($berkas->hak18 == 1)
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/check-square-regular.svg') }}" />
                        @else
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/square-regular.svg') }}" />
                        @endif
                    </td>
                </tr>
                <tr>
                    <td colspan='3'
                        style='padding-left:5px; border-left:1px solid black; border-right:1px solid black'>
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
                        {{ $penetapan->format('j F Y') }}
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
                    <td style="text-align:right; border:0px solid black; ">RM PP 02 Rev.1 Hal 2/2</td>
                </tr>
            </thead>
        </table>
        <table style="width: 100%; border: 1px solid black">
            <thead>
                <tr>
                    <th rowspan="5" style="width: 50%; border:1px solid black; text-align:center;">
                        <h5>KEWAJIBAN PASIEN DAN<br>KELUARGANYA</h5>
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
        <table style="width: 100%; border: 0px solid black; margin-top: 10px">
            <thead>
                <tr>
                    <th style="width: 5%; border:1px solid black; text-align:center;">No</th>
                    <th style="border:1px solid black; text-align:center;">Hak Pasien dan Keluarganya
                    </th>
                    <th style="width: 15%; border:1px solid black; text-align:center;">Check List</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width: 5%; border:1px solid black; text-align:center;">1</td>
                    <td style="border:1px solid black; padding-left:5px">Setiap Pasien dan Keluarga berkewajiban untuk
                        mentaati peraturan dan
                        tata tertib RSUP Surakarta</td>
                    <td style="width: 10%; border:1px solid black; text-align:center;">
                        {{-- <div class="custom-control custom-checkbox">
                            <input class="custom-control-input custom-control-input-dark " type="checkbox"
                                name="hak1" id="checkHak1" {{ $berkas->hak1 == 1 ? 'checked' : '' }}
                                value="1" onclick="return false;">
                            <label for="checkHak1" class="custom-control-label"></label>
                        </div> --}}

                        @if ($berkas->kewajiban1 == 1)
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/check-square-regular.svg') }}" />
                        @else
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/square-regular.svg') }}" />
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 5%; border:1px solid black; text-align:center;">2</td>
                    <td style="border:1px solid black; padding-left:5px">Memperlakukan staf rumah sakit, pasien lainnya
                        dan pengunjung dengan
                        sopan dan hormat</td>
                    <td style="width: 10%; border:1px solid black; text-align:center;">
                        {{-- <div class="custom-control custom-checkbox">
                            <input class="custom-control-input custom-control-input-dark " type="checkbox"
                                name="hak2" id="checkHak2" {{ $berkas->hak2 == 1 ? 'checked' : '' }}
                                value="1" onclick="return false;">
                            <label for="checkHak2" class="custom-control-label"></label>
                        </div> --}}

                        @if ($berkas->kewajiban2 == 1)
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/check-square-regular.svg') }}" />
                        @else
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/square-regular.svg') }}" />
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 5%; border:1px solid black; text-align:center;">3</td>
                    <td style="border:1px solid black; padding-left:5px">Bertanggung jawab atas keamanan barang-barang
                        berharga selama di
                        rumah
                        sakit</td>
                    <td style="width: 10%; border:1px solid black; text-align:center;">
                        {{-- <div class="custom-control custom-checkbox">
                            <input class="custom-control-input custom-control-input-dark " type="checkbox"
                                name="hak3" id="checkHak3" {{ $berkas->hak3 == 1 ? 'checked' : '' }}
                                value="1" onclick="return false;">
                            <label for="checkHak3" class="custom-control-label"></label>
                        </div> --}}

                        @if ($berkas->kewajiban3 == 1)
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/check-square-regular.svg') }}" />
                        @else
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/square-regular.svg') }}" />
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 5%; border:1px solid black; text-align:center;">4</td>
                    <td style="border:1px solid black; padding-left:5px">Menyelesaikan tanggung jawab keuangan</td>
                    <td style="width: 10%; border:1px solid black; text-align:center;">
                        {{-- <div class="custom-control custom-checkbox">
                            <input class="custom-control-input custom-control-input-dark " type="checkbox"
                                name="hak4" id="checkHak4" {{ $berkas->hak4 == 1 ? 'checked' : '' }}
                                value="1" onclick="return false;">
                            <label for="checkHak4" class="custom-control-label"></label>
                        </div> --}}

                        @if ($berkas->kewajiban4 == 1)
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/check-square-regular.svg') }}" />
                        @else
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/square-regular.svg') }}" />
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 5%; border:1px solid black; text-align:center;">5</td>
                    <td style="border:1px solid black; padding-left:5px">Memberikan informasi yang diperlukan untuk
                        pengobatan dengan benar,
                        jelas dan jujur tentang masalah kesehatannya</td>
                    <td style="width: 10%; border:1px solid black; text-align:center;">
                        {{-- <div class="custom-control custom-checkbox">
                            <input class="custom-control-input custom-control-input-dark " type="checkbox"
                                name="hak5" id="checkHak5" {{ $berkas->hak5 == 1 ? 'checked' : '' }}
                                value="1" onclick="return false;">
                            <label for="checkHak5" class="custom-control-label"></label>
                        </div> --}}

                        @if ($berkas->kewajiban5 == 1)
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/check-square-regular.svg') }}" />
                        @else
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/square-regular.svg') }}" />
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 5%; border:1px solid black; text-align:center;">6</td>
                    <td style="border:1px solid black; padding-left:5px">Berpartisipasi aktif dan patuh terhadap
                        pengobatan, termasuk
                        keputusan
                        mengenai rencana pengobatan yang diberikan
                    </td>
                    <td style="width: 10%; border:1px solid black; text-align:center;">
                        {{-- <div class="custom-control custom-checkbox">
                            <input class="custom-control-input custom-control-input-dark " type="checkbox"
                                name="hak6" id="checkHak6" {{ $berkas->hak6 == 1 ? 'checked' : '' }}
                                value="1" onclick="return false;">
                            <label for="checkHak6" class="custom-control-label"></label>
                        </div> --}}

                        @if ($berkas->kewajiban6 == 1)
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/check-square-regular.svg') }}" />
                        @else
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/square-regular.svg') }}" />
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 5%; border:1px solid black; text-align:center;">7</td>
                    <td style="border:1px solid black; padding-left:5px">Bertanggung jawab atas semua konsekuensi yang
                        ada apabila menolak
                        pengobatan medis</td>
                    <td style="width: 10%; border:1px solid black; text-align:center;">
                        {{-- <div class="custom-control custom-checkbox">
                            <input class="custom-control-input custom-control-input-dark " type="checkbox"
                                name="hak7" id="checkHak7" {{ $berkas->hak7 == 1 ? 'checked' : '' }}
                                value="1" onclick="return false;">
                            <label for="checkHak7" class="custom-control-label"></label>
                        </div> --}}

                        @if ($berkas->kewajiban7 == 1)
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/check-square-regular.svg') }}" />
                        @else
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/square-regular.svg') }}" />
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 5%; border:1px solid black; text-align:center;">8</td>
                    <td style="border:1px solid black; padding-left:5px">Memberikan imbalan jasa atas pelayanan yang
                        diterima</td>
                    <td style="width: 10%; border:1px solid black; text-align:center;">
                        {{-- <div class="custom-control custom-checkbox">
                            <input class="custom-control-input custom-control-input-dark " type="checkbox"
                                name="hak8" id="checkHak8" {{ $berkas->hak8 == 1 ? 'checked' : '' }}
                                value="1" onclick="return false;">
                            <label for="checkHak8" class="custom-control-label"></label>
                        </div> --}}

                        @if ($berkas->kewajiban8 == 1)
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/check-square-regular.svg') }}" />
                        @else
                            <img width="15"
                                src="{{ public_path('template/dist/img/check/square-regular.svg') }}" />
                        @endif
                    </td>
                </tr>

                <tr>
                    <td colspan='3'
                        style='padding-left:5px; border-left:1px solid black; border-right:1px solid black'>
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
                    {{-- @php
                        $penetapan = \Carbon\Carbon::parse($berkas->updated_at)->locale('id');

                        $qr_informan = 'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara elektronik oleh' . "\n" . $berkas->user->name . "\n" . 'ID ' . $berkas->user_id . "\n" . \Carbon\Carbon::parse($berkas->updated_at)->format('d-m-Y');
                        $qrcode_informan = base64_encode(QrCode::format('svg')->size(100)->errorCorrection('H')->generate($qr_informan));
                    @endphp --}}
                    <td style="border-right: 1px solid black; text-align:center; padding-top:100px;">Surakarta,
                        {{ $penetapan->format('j F Y') }}
                    </td>
                </tr>
                <tr>
                    <td
                        style="border-left: 1px solid black; border-right: 0px solid black; padding-top:0px; text-align:center; vertical-align: text-top:">
                        Pasien/ Keluarga/ <br>Penanggung Jawab
                    </td>
                    <td style="text-align:center; border-right:1px solid black; vertical-align: text-top;">
                        Pemberi Informasi
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
