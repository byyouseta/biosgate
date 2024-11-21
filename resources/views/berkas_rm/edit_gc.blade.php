@extends('layouts.master')

@section('head')
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.css">

    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <link type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/south-street/jquery-ui.css"
        rel="stylesheet">
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script type="text/javascript" src="//keith-wood.name/js/jquery.signature.js"></script>

    <link rel="stylesheet" type="text/css" href="//keith-wood.name/css/jquery.signature.css">
    <!-- Tempusdominus|Datetime Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('template/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <style>
        .kbw-signature {
            width: 100%;
            height: 220px;
        }

        #sig canvas {
            width: 100% !important;
            height: auto;
        }
    </style>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 mx-auto">
                    <div class="card ">
                        <div class="card-header">
                            <div class="card-title">FORM PERSETUJUAN UMUM/ GENERAL CONSENT </div>
                            <div class="float-right">RM PP 01 Rev. 1</div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="no_hp">Nomor RM</label>
                                        <input type="text" class="form-control" name="noRm"
                                            value="{{ $data->no_rkm_medis }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="no_hp">NIK</label>
                                        <input type="text" class="form-control" name="nik"
                                            value="{{ $data->ktp_pasien }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="no_hp">Nama Pasien</label>
                                        <input type="text" class="form-control" name="nama"
                                            value="{{ $data->nm_pasien }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="no_hp">Tanggal Lahir</label>
                                        <input type="text" class="form-control" name="tglLahir"
                                            value="{{ $data->tgl_lahir }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="no_hp">Alamat</label>
                                        <input type="text" class="form-control" name="alamat"
                                            value="{{ $data->alamat }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-12">
                    <form action="/berkasrm/generalconsent/edit" method="POST">
                        @csrf
                        <div class="card card-primary card-outline card-outline-tabs">
                            <div class="card-header p-0 border-bottom-0">
                                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link " id="custom-tabs-hak-tab" data-toggle="pill"
                                            href="#custom-tabs-four-hak" role="tab"
                                            aria-controls="custom-tabs-four-home" aria-selected="true">PERSETUJUAN UMUM</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="custom-tabs-kewajiban-tab" data-toggle="pill"
                                            href="#custom-tabs-four-kewajiban" role="tab"
                                            aria-controls="custom-tabs-four-kewajiban" aria-selected="false">PEMILIHAN
                                            DOKTER</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link active" id="custom-tabs-tandatangan-tab" data-toggle="pill"
                                            href="#custom-tabs-four-tandatangan" role="tab"
                                            aria-controls="custom-tabs-four-tandatangan" aria-selected="false">TANDA
                                            TANGAN</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content" id="custom-tabs-four-tabContent">
                                    <div class="tab-pane fade " id="custom-tabs-four-hak" role="tabpanel"
                                        aria-labelledby="custom-tabs-four-hak-tab">
                                        <table class="table table-bordered table-sm">
                                            <tbody>
                                                <tr class="">
                                                    <th style="width:5%" class="text-center">1.</th>
                                                    <th>HAK DAN KEWAJIBAN SEBAGAI PASIEN.</th>

                                                </tr>
                                                <tr>
                                                    <td style="width:5%" class="text-center"></td>
                                                    <td>Saya mengakui bahwa pada proses pendaftaran untuk mendapatkan
                                                        perawatan di RSUP Surakarta dan penandatanganan dokumen ini,
                                                        saya telah mendapat informasi tentang hak-hak dan kewajiban saya
                                                        sebagai pasien. </td>

                                                </tr>
                                                <tr>
                                                    <th style="width:5%" class="text-center">2.</th>
                                                    <th>PERSETUJUAN PELAYANAN KESEHATAN</th>

                                                </tr>
                                                <tr>
                                                    <td style="width:5%" class="text-center"></td>
                                                    <td>Saya menyetujui dan memberikan persetujuan untuk mendapat pelayanan
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
                                                    <th style="width:5%" class="text-center">3.</th>
                                                    <th>KEYAKINAN DAN NILAI NILAI.</th>

                                                </tr>
                                                <tr>
                                                    <td style="width:5%" class="text-center"></td>
                                                    <td>Rumah Sakit Umum Pusat Surakarta sangat menghargai keyakinan, nilai
                                                        nilai dan agama yang dianut oleh pasien, selama memperoleh
                                                        pelayanan kesehatan diantaranya:</td>
                                                </tr>
                                                <tr>
                                                    <td style="width:5%" class="text-center"></td>
                                                    <td>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">a.</span>
                                                            </div>
                                                            <input type="text" class="form-control" name="keyakinan1"
                                                                value="{{ $berkas->keyakinan1 }}"
                                                                placeholder="Silahkan isi keyakinan dan nilai-nilai yang diyakini">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:5%" class="text-center"></td>
                                                    <td>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">b.</span>
                                                            </div>
                                                            <input type="text" class="form-control" name="keyakinan2"
                                                                value="{{ $berkas->keyakinan2 }}"
                                                                placeholder="Silahkan isi keyakinan dan nilai-nilai yang diyakini">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:5%" class="text-center"></td>
                                                    <td>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">c.</span>
                                                            </div>
                                                            <input type="text" class="form-control" name="keyakinan3"
                                                                value="{{ $berkas->keyakinan3 }}"
                                                                placeholder="Silahkan isi keyakinan dan nilai-nilai yang diyakini">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:5%" class="text-center"></td>
                                                    <td>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">d.</span>
                                                            </div>
                                                            <input type="text" class="form-control" name="keyakinan4"
                                                                value="{{ $berkas->keyakinan4 }}"
                                                                placeholder="Silahkan isi keyakinan dan nilai-nilai yang diyakini">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="width:5%" class="text-center">4.</th>
                                                    <th>PRIVASI</th>
                                                </tr>
                                                <tr>
                                                    <td style="width:5%" class="text-center"></td>
                                                    <td>Saya memberi kuasa kepada RSUP Surakarta dan atau :</td>
                                                </tr>
                                                <tr>
                                                    <td style="width:5%" class="text-center"></td>
                                                    <td>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">a.</span>
                                                            </div>
                                                            <input type="text" class="form-control" name="privasi1"
                                                                value="{{ $berkas->privasi1 }}"
                                                                placeholder="Silahkan isi privasi yang diinginkan">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:5%" class="text-center"></td>
                                                    <td>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">b.</span>
                                                            </div>
                                                            <input type="text" class="form-control" name="privasi2"
                                                                value="{{ $berkas->privasi2 }}"
                                                                placeholder="Silahkan isi privasi yang diinginkan">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:5%" class='text-center'></td>
                                                    <td>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">c.</span>
                                                            </div>
                                                            <input type="text" class="form-control" name="privasi3"
                                                                value="{{ $berkas->privasi3 }}"
                                                                placeholder="Silahkan isi privasi yang diinginkan">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:5%" class='text-center'></td>
                                                    <td>Untuk menjaga privasi dan kerahasian penyakit saya selama dalam
                                                        perawatan serta memberikan persetujuan tindakan medis
                                                        Dalam keadaan tertentu seperti di ruang isolasi dan isolasi dengan
                                                        penyakit menular tertentu, maka :
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:5%" class='text-center'></td>
                                                    <td>a. Selama perawatan, keluarga tidak diizinkan untuk menunggu,
                                                        kecuali dalam keadaan khusus sesuai indikasi medis dan keperawatan
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:5%" class='text-center'></td>
                                                    <td>b. Selama menunggu tidak boleh keluar masuk dan sering berganti
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:5%" class='text-center'></td>
                                                    <td>c. Rumah Sakit Umum Pusat Surakarta tidak bertanggung jawab terhadap
                                                        risiko yang ditimbulkan selama menunggu</td>
                                                </tr>
                                                <tr>
                                                    <td style="width:5%" class='text-center'></td>
                                                    <td>d. Pengawasan dan kebutuhan pasien selama diruang perawatan
                                                        diberikan oleh petugas kesehatan</td>
                                                </tr>
                                                <tr>
                                                    <td style="width:5%" class='text-center'></td>
                                                    <td>e. Pengawasan pasien diruang perawatan dapat menggunakan kemajuan
                                                        teknologi saat ini, misalnya CCTV</td>
                                                </tr>
                                                <tr>
                                                    <td style="width:5%" class='text-center'></td>
                                                    <td>f. Komunikasi dan edukasi kepada keluarga menggunakan video call,
                                                        Whatsap saat ini yang berlaku di RSUP Surakarta</td>
                                                </tr>
                                                <tr>
                                                    <th style="width:5%" class='text-center'>5.</th>
                                                    <th>RAHASIA INFORMASI KESEHATAN. </th>
                                                </tr>
                                                <tr>
                                                    <td style="width:5%" class='text-center'></td>
                                                    <td>Saya setuju RSUP Surakarta wajib menjamin rahasia informasi
                                                        kesehatan saya baik untuk kepentingan perawatan atau pengobatan,
                                                        pendidikan maupun penelitian kecuali saya mengungkapkan sendiri atau
                                                        orang lain yang saya berikuasa sebagai Penjamin. </td>
                                                </tr>
                                                <tr>
                                                    <th style="width:5%" class='text-center'>6.</th>
                                                    <th>MEMBUKA RAHASIA INFORMASI KESEHATAN.</th>
                                                </tr>
                                                <tr>
                                                    <td style="width:5%" class='text-center'></td>
                                                    <td>a. Saya setuju untuk membuka rahasia informasi kesehatan terkait
                                                        dengan kondisi kesehatan, asuhan dan pengobatan yang saya terima
                                                        kepada: </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:5%" class='text-center'></td>
                                                    <td class="ml-3">- Dokter dan tenaga kesehatan lain yang
                                                        memberikan asuhan kepada saya</td>
                                                </tr>
                                                <tr>
                                                    <td style="width:5%" class='text-center'></td>
                                                    <td>- Perusahaan asuransi kesehatan atau perusahaan lainnya atau pihak
                                                        lain yang menjamin pembiayaan saya</td>
                                                </tr>
                                                <tr>
                                                    <td style="width:5%" class='text-center'></td>
                                                    <td>- Kepentingan dalam rangka untuk pencegahan dan pengendalian
                                                        penyakit menular tertentu sesuai ketentuan yang berlaku</td>
                                                </tr>
                                                <tr>
                                                    <td style="width:5%" class='text-center'></td>
                                                    <td>- Kepentingan hukum.</td>
                                                </tr>
                                                <tr>
                                                    <td style="width:5%" class='text-center'></td>
                                                    <td>b. Saya mengetahui dan menyetujui bahwa berdasarkan Peraturan
                                                        Menteri Kesehatan Nomor 24 Tahun 2022 tentang Rekam Medis,
                                                        fasilitas pelayanan kesehatan wajib membuka akses dan mengirim data
                                                        rekam medis kepada Kementerian Kesehatan melalui
                                                        Platform SATU SEHAT.</td>
                                                </tr>
                                                <tr>
                                                    <td style="width:5%" class='text-center'></td>
                                                    <td>c. Menyetujui untuk menerima dan membuka data Pasien dari Fasilitas
                                                        Pelayanan Kesehatan lainnya melalui SATU SEHAT untuk
                                                        kepentingan pelayanan kesehatan dan/atau rujukan.
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="width:5%" class='text-center'>7.</th>
                                                    <th>BARANG PRIBADI. </th>
                                                </tr>
                                                <tr>
                                                    <td style="width:5%" class='text-center'></td>
                                                    <td>Saya setuju untuk tidak membawa barang-barang berharga yang tidak
                                                        diperlukan (seperti: perhiasan, elektronik, dll) selama dalam
                                                        perawatan. Saya memahami dan menyetujui bahwa apabila saya
                                                        membawanya, maka RSUP Surakarta tidak bertanggungjawab terhadap
                                                        kehilangan, kerusakan atau pencurian .</td>
                                                </tr>
                                                <tr>
                                                    <th style="width:5%" class='text-center'>8.</th>
                                                    <th>PENELITIAN/ CLINICAL TRIAL</th>
                                                </tr>
                                                <tr>
                                                    <td style="width:5%" class='text-center'></td>
                                                    <td>Apabila saya dilibatkan dalam penelitian atau prosedur
                                                        eksperimental, maka hal tersebut hanya dapat dilakukan dengan
                                                        sepengetahuan dan persetujuan saya</td>
                                                </tr>
                                                <tr>
                                                    <th style="width:5%" class='text-center'>9.</th>
                                                    <th>PENDIDIKAN</th>
                                                </tr>
                                                <tr>
                                                    <td style="width:5%" class='text-center'></td>
                                                    <td>Saya setuju untuk mengizinkan tenaga medis, keperawatan, dan tenaga
                                                        kesehatan lainnya dalam pendidikan/pelatihan, kecuali diminta
                                                        sebaliknya, untuk hadir selama perawatan pasien, atau berpartisipasi
                                                        dalam perawatan pasien sebagai bagian dari pendidikan mereka</td>
                                                </tr>
                                                <tr>
                                                    <th style="width:5%" class='text-center'>10.</th>
                                                    <th>PENGAJUAN KELUHAN. </th>
                                                </tr>
                                                <tr>
                                                    <td style="width:5%" class='text-center'></td>
                                                    <td>Saya menyatakan bahwa saya telah menerima informasi tentang adanya
                                                        tatacara mengajukan dan mengatasi keluhan terkait pelayanan
                                                        medik yang diberikan terhadap diri saya. Saya setuju untuk mengikuti
                                                        tatacara mengajukan keluhan sesuai prosedur yang ada.
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="width:5%" class='text-center'>11.</th>
                                                    <th>KEWAJIBAN PEMBAYARAN. </th>
                                                </tr>
                                                <tr>
                                                    <td style="width:5%" class='text-center'></td>
                                                    <td>Saya menyatakan setuju, baik sebagai wali atau sebagai pasien, bahwa
                                                        sesuai pertimbangan pelayanan yang diberikan kepada pasien,
                                                        maka saya wajib untuk membayar total biaya pelayanan. Biaya
                                                        pelayanan berdasarkan acuan biaya dan ketentuan di RSUP Surakarta.
                                                        Saya juga menyadari dan memahami bahwa:
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:5%" class='text-center'></td>
                                                    <td>a. Apabila saya tidak memberikan persetujuan, atau di kemudian hari
                                                        mencabut persetujuan saya untuk melepaskan rahasia kedokteran
                                                        saya kepada perusahaan asuransi yang saya tentukan, maka saya
                                                        pribadi bertanggung jawab untuk membayar semua pelayanan dan
                                                        tindakan medis dari RSUP Surakarta
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:5%" class='text-center'></td>
                                                    <td>b. Apabila rumah sakit membutuhkan proses hukum untuk menagih biaya
                                                        pelayanan rumah sakit dari saya, saya memahami bahwa saya
                                                        bertanggung jawab untuk membayar semua biaya yang disebabkan dari
                                                        proses hukum tersebut.
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade" id="custom-tabs-four-kewajiban" role="tabpanel"
                                        aria-labelledby="custom-tabs-four-kewajiban-tab">
                                        <table class="table table-bordered table-sm">
                                            <thead>
                                                <tr class="text-center">
                                                    <th colspan='3'>SURAT PERNYATAAN PEMILIHAN DOKTER</th>
                                                </tr>
                                                <tr class="text-center">
                                                    <th colspan='3'>( OLEH PASIEN / KELUARGA / PENANGGUNG JAWAB)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan='3' style='padding-top:80px'>Yang bertanda tangan di
                                                        bawah ini :</td>
                                                </tr>
                                                <tr>
                                                    <td width='20%' class='align-middle pl-5'>Nama</td>
                                                    <td colspan='2'><input type="text" class="form-control"
                                                            value={{ $berkas->namaPj }} id="hal1" name='namaPj'
                                                            required></td>
                                                </tr>
                                                <tr>
                                                    <td width='20%' class='align-middle pl-5'>Tanggal Lahir/Umur</td>
                                                    <td>
                                                        <div class="input-group date"
                                                            data-target-input="nearest">
                                                            <input type="text"
                                                                class="form-control datetimepicker-input"
                                                                value={{ $berkas->tglLahirPj }} id="tanggalLahirPj"
                                                                data-target="#tanggalLahirPj" data-toggle="datetimepicker"
                                                                name="tanggalLahirPj" autocomplete="off" onchange="hitungUsia()"/>
                                                            <div class="input-group-append" data-target="#tanggalLahirPj"
                                                                data-toggle="datetimepicker">
                                                                <div class="input-group-text"><i
                                                                        class="fa fa-calendar"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td width='20%'>
                                                        <div class="input-group">
                                                            <input type="number" class="form-control" name="umurPj" id="usia"
                                                                value={{ $berkas->umurPj }} placeholder="Umur PJ"
                                                                step="1" required readonly>
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Tahun</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width='20%' class='align-middle pl-5'>Alamat</td>
                                                    <td colspan='2'><input type="text" class="form-control"
                                                            value={{ $berkas->alamatPj }} name ="alamatPj" required></td>
                                                </tr>
                                                <tr>
                                                    <td colspan='2'>Atas nama pasien tersebut di
                                                        bawah ini :</td>
                                                </tr>
                                                <tr>
                                                    <td width='20%' class='align-middle pl-5'>Nama</td>
                                                    <td colspan='2'><input type="text" class="form-control"
                                                            value="{{ $data->nm_pasien }}" readonly></td>
                                                </tr>
                                                <tr>
                                                    <td width='20%' class='align-middle pl-5'>Tanggal Lahir/Umur</td>
                                                    <td><input type="text" class="form-control"
                                                            value="{{ Carbon\Carbon::parse($data->tgl_lahir)->format('d-m-Y') }}"
                                                            readonly>
                                                    </td>
                                                    <td width='20%'>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" name="umurPasien"
                                                                placeholder="Umur Pasien"
                                                                value="{{ \Carbon\Carbon::parse($data->tgl_lahir)->diffInYears(\Carbon\Carbon::now()) }}"
                                                                readonly>
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Tahun</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width='20%' class='align-middle pl-5'>No. Rekam Medik</td>
                                                    <td colspan='2'><input type="text" class="form-control"
                                                            value="{{ $data->no_rkm_medis }}" readonly></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3">Dengan ini menyatakan bahwa
                                                        untuk menangani
                                                        penyakit atau gangguan kesehatan saya / orang tua/ anak / suami /
                                                        isteri / keluarga, saya
                                                        memilih dokter</td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td> <select name='dpjp' class="form-control" required>
                                                            <option value="">Pilih</option>
                                                            @foreach ($dokter as $listDokter)
                                                                <option value="{{ $listDokter->nm_dokter }}"
                                                                    {{ $listDokter->nm_dokter == $berkas->dpjp ? 'selected' : '' }}>
                                                                    {{ $listDokter->nm_dokter }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3"> Apabila di kemudian hari
                                                        dokter tersebut
                                                        berhalangan karena alasan yang dapat
                                                        dimengerti maka untuk meneruskan perawatan, saya :
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3">1. Memberikan kuasa kepada pihak rumah sakit
                                                        untuk menunjuk dokter pengganti yang sederajat.
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3">2. Memberikan kuasa kepada dokter tersebut di
                                                        atas untuk menunjuk dokter pengganti yang sederajat
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3">3. Menentukan sendiri dokter pengganti
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade show active" id="custom-tabs-four-tandatangan"
                                        role="tabpanel" aria-labelledby="custom-tabs-four-tandatangan-tab">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="informan">Pemberi Informasi</label>
                                                    <input type="text" class="form-control" name="informan"
                                                        value="{{ Auth::user()->name }}" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label for="namaPj">Nama Penanggung Jawab</label>
                                                    <input type="text" class="form-control" name="namaPj"
                                                        id="hal2" value={{ $berkas->namaPj }} required>
                                                    <input type="hidden" class="form-control" name="noRawat"
                                                        value="{{ $data->no_rawat }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <label class="" for="">Tanda tangan</label>
                                                <br />
                                                <div id="sig"></div>
                                                <br />
                                                <button id="clear" class="btn btn-danger btn-sm">Ulang Tanda
                                                    tangan</button>
                                                <textarea id="signature64" name="signed" style="display: none"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card -->
                            </div>
                            <div class="card-footer">
                                {{-- <a href="/vedika" class="btn btn-default">Kembali</a> --}}
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="/berkasrm/generalconsent/{{ Crypt::encrypt($berkas->noRawat) }}/delete"
                                    class="btn btn-danger delete-confirm"><i class="fas fa-times-circle"></i>
                                    Hapus</a>
                                <div class="float-right">
                                    <a href="/berkasrm/generalconsent/{{ Crypt::encrypt($berkas->noRawat) }}/print"
                                        class="btn btn-secondary" target="_blank"><i class="far fa-file-pdf"></i>
                                        Print</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </section><!-- /.container-fluid -->
    <script type="text/javascript">
        var sig = $('#sig').signature({
            syncField: '#signature64',
            syncFormat: 'PNG'
        });
        $('#clear').click(function(e) {
            e.preventDefault();
            sig.signature('clear');
            $("#signature64").val('');
        });
    </script>
@endsection
@section('plugin')
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{ asset('template/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('template/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <script>
        // Listen for click on toggle checkbox
        $('#select-all').click(function(event) {
            if (this.checked) {
                // Iterate each checkbox
                $(':checkbox').each(function() {
                    this.checked = true;
                });
            } else {
                $(':checkbox').each(function() {
                    this.checked = false;
                });
            }
        });
        //Date picker
        $('#tanggalLahirPj').datetimepicker({
            format: 'YYYY-MM-DD'
        });
        // jQuery implementation

        $(function() {
            var $src = $('#hal1'),
                $dst = $('#hal2');
            $src.on('input', function() {
                $dst.val($src.val());
            });
        });

        function hitungUsia() {
            // Ambil nilai dari input tanggal lahir
            var tanggalLahir = document.getElementById('tanggalLahirPj').value;
            if (tanggalLahir) {
                // Konversi string tanggal lahir ke objek Date
                var dob = new Date(tanggalLahir);
                var today = new Date();

                // Hitung selisih tahun
                var age = today.getFullYear() - dob.getFullYear();

                // Cek apakah bulan dan tanggal ulang tahun sudah lewat tahun ini
                var monthDiff = today.getMonth() - dob.getMonth();
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                    age--;
                }

                // Set nilai usia ke input usia
                document.getElementById('usia').value = age;
            }
        }
    </script>
@endsection
