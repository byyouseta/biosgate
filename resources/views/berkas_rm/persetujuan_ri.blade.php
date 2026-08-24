@extends('layouts.master')

@section('head')
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.css">

    {{-- <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <link type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/south-street/jquery-ui.css"
        rel="stylesheet">
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script type="text/javascript" src="//keith-wood.name/js/jquery.signature.js"></script>

    <link rel="stylesheet" type="text/css" href="//keith-wood.name/css/jquery.signature.css"> --}}

    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/south-street/jquery-ui.css"
        rel="stylesheet">
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

    <script type="text/javascript" src="{{ asset('template/plugins/jquery-tandatangan/js/jquery.signature.min.js') }}">
    </script>
    <script type="text/javascript" src="{{ asset('template/plugins/jquery-tandatangan/js/jquery.ui.touch-punch.min.js') }}">
    </script>
    <link rel="stylesheet" type="text/css" href="{{ asset('template/plugins/jquery-tandatangan/css/jquery.signature.css') }}">
    <!-- Tempusdominus|Datetime Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('template/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('template/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
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
                            <div class="card-title">SURAT PERSETUJUAN / PENOLAKAN RAWAT INAP
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="no_rm">Nomor RM</label>
                                        <input type="text" class="form-control" name="noRm" id="no_rm"
                                            value="{{ $data->no_rkm_medis }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="nik">NIK</label>
                                        <input type="text" class="form-control" name="nik" id="nik"
                                            value="{{ $data->ktp_pasien }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="nama_pasien">Nama Pasien</label>
                                        <input type="text" class="form-control" name="nama" id="nama_pasien"
                                            value="{{ $data->nm_pasien }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tglLahir">Tanggal Lahir</label>
                                        <input type="text" class="form-control" name="tglLahir" id="tglLahir"
                                            value="{{ $data->tgl_lahir }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="alamat">Alamat</label>
                                        <input type="text" class="form-control" name="alamat" id="alamat"
                                            value="{{ $data->alamat }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-12">
                    <form
                        action="{{ $berkas ? route('berkasrm.persetujuanRI.Update', $berkas->id) : route('berkasrm.persetujuanRI.Store') }}"
                        method="POST" id="myForm">
                        @if (!empty($berkas))
                            @method('PUT')
                        @endif
                        @csrf
                        <div class="card card-primary card-outline card-outline-tabs">
                            <div class="card-header p-0 border-bottom-0">
                                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link " id="custom-tabs-hak-tab" data-toggle="pill"
                                            href="#custom-tabs-four-hak" role="tab"
                                            aria-controls="custom-tabs-four-home" aria-selected="true">PERSETUJUAN</a>
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
                                        <table class="table table-bordeless table-sm">
                                            <tbody>
                                                <tr>
                                                    <th colspan="2" class="text-center p-2">SURAT PERSETUJUAN / PENOLAKAN
                                                        RAWAT INAP</th>
                                                </tr>
                                                <tr>
                                                    <th colspan="2" class="">Yang bertanda tangan dibawah ini :
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <td style="width:15%; vertical-align: middle;">Nama</td>
                                                    <td><input type="text" name="nama_pj" required
                                                            value="{{ old('nama_pj', $berkas ? $berkas->nama_pj : '') }}"
                                                            class="form-control"></td>

                                                </tr>
                                                <tr>
                                                    <td style="width:15%; vertical-align: middle;">Tempat Lahir</td>
                                                    <td><input type="text" name="tempat_lahir_pj" required
                                                            value="{{ old('tempat_lahir_pj', $berkas ? $berkas->tempat_lahir_pj : '') }}"
                                                            class="form-control">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:15%; vertical-align: middle;">Tanggal Lahir</td>
                                                    <td>
                                                        <div class="input-group date w-25" data-target-input="nearest">
                                                            <input type="text"
                                                                class="form-control datetimepicker-input" id="tglLahirPj"
                                                                data-target="#tgl_lahir_pj" data-toggle="datetimepicker"
                                                                name="tgl_lahir_pj" autocomplete="off" required
                                                                value="{{ old('tgl_lahir_pj', $berkas ? $berkas->tanggal_lahir_pj : '') }}" />
                                                            <div class="input-group-append" data-target="#tgl_lahir_pj"
                                                                data-toggle="datetimepicker">
                                                                <div class="input-group-text"><i
                                                                        class="fa fa-calendar"></i></div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:15%; vertical-align: middle;">Jenis Kelamin</td>
                                                    <td>
                                                        <select name="jk_pj" id="" class="form-control w-25"
                                                            name="jk" required>
                                                            <option value="L"
                                                                {{ old('jk_pj', $berkas ? $berkas->jenis_kelamin_pj : '') == 'L' ? 'selected' : '' }}>
                                                                Laki-laki
                                                            </option>
                                                            <option value="P"
                                                                {{ old('jk_pj', $berkas ? $berkas->jenis_kelamin_pj : '') == 'P' ? 'selected' : '' }}>
                                                                Perempuan
                                                            </option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:15%; vertical-align: middle;">Alamat</td>
                                                    <td><input type="text" name="alamat_pj" id=""
                                                            class="form-control" required
                                                            value="{{ old('alamat_pj', $berkas ? $berkas->alamat_pj : '') }}">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:15%; vertical-align: middle;">Pekerjaan</td>
                                                    <td><input type="text" name="pekerjaan_pj" id=""
                                                            class="form-control" required
                                                            value="{{ old('pekerjaan_pj', $berkas ? $berkas->pekerjaan_pj : '') }}">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:15%; vertical-align: middle;">No KTP</td>
                                                    <td><input type="text" name="no_ktp_pj" id=""
                                                            class="form-control" required
                                                            value="{{ old('no_ktp_pj', $berkas ? $berkas->no_ktp_pj : '') }}">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:15%; vertical-align: middle;">No Telepon</td>
                                                    <td><input type="text" name="no_telp_pj" id=""
                                                            class="form-control" required
                                                            value="{{ old('no_telp_pj', $berkas ? $berkas->no_telepon_pj : '') }}">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:15%; vertical-align: middle;">Bertindak untuk</td>
                                                    <td>
                                                        <select name="hubungan_pj" id=""
                                                            class="form-control w-25" required>
                                                            <option value="Diri Sendiri"
                                                                {{ old('hubungan_pj', $berkas ? $berkas->hubungan_pj : '') == 'Diri Sendiri' ? 'selected' : '' }}>
                                                                Diri Sendiri</option>
                                                            <option value="Suami"
                                                                {{ old('hubungan_pj', $berkas ? $berkas->hubungan_pj : '') == 'Suami' ? 'selected' : '' }}>
                                                                Suami
                                                            </option>
                                                            <option value="Istri"
                                                                {{ old('hubungan_pj', $berkas ? $berkas->hubungan_pj : '') == 'Istri' ? 'selected' : '' }}>
                                                                Istri
                                                            </option>
                                                            <option value="Anak"
                                                                {{ old('hubungan_pj', $berkas ? $berkas->hubungan_pj : '') == 'Anak' ? 'selected' : '' }}>
                                                                Anak
                                                            </option>
                                                            <option value="Orang Tua"
                                                                {{ old('hubungan_pj', $berkas ? $berkas->hubungan_pj : '') == 'Orang Tua' ? 'selected' : '' }}>
                                                                Orang Tua</option>
                                                            <option value="Wali"
                                                                {{ old('hubungan_pj', $berkas ? $berkas->hubungan_pj : '') == 'Wali' ? 'selected' : '' }}>
                                                                Wali
                                                            </option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:15%; vertical-align: middle;">Nama Pasien</td>
                                                    <td><input type="text" name="nama_pasien" id=""
                                                            class="form-control"
                                                            value="{{ $berkas ? $berkas->nama_pasien : $data->nm_pasien }}"
                                                            required>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:15%; vertical-align: middle;">Tempat Lahir
                                                        Pasien</td>
                                                    <td><input type="text" name="tempat_lahir_pasien" id=""
                                                            class="form-control"
                                                            value="{{ $berkas ? $berkas->tempat_lahir_pasien : $data->nm_kab ?? 'N/A' }}"
                                                            required>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:15%; vertical-align: middle;">Tanggal Lahir Pasien
                                                    </td>
                                                    <td>
                                                        {{-- <input type="text" name="tgl_lahir_pasien" id="tglLahirPasien"
                                                            class="form-control w-25" value="{{ $data->tgl_lahir }}"> --}}
                                                        <div class="input-group date w-25" data-target-input="nearest">
                                                            <input type="text"
                                                                class="form-control datetimepicker-input"
                                                                id="tglLahirPasien" data-target="#tgl_lahir_pasien"
                                                                data-toggle="datetimepicker" name="tgl_lahir_pasien"
                                                                autocomplete="off"
                                                                value="{{ $berkas ? $berkas->tanggal_lahir_pasien : $data->tgl_lahir }}"
                                                                required>
                                                            <div class="input-group-append"
                                                                data-target="#tgl_lahir_pasien"
                                                                data-toggle="datetimepicker">
                                                                <div class="input-group-text"><i
                                                                        class="fa fa-calendar"></i></div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:15%; vertical-align: middle;">Jenis Kelamin Pasien
                                                    </td>
                                                    <td>
                                                        <select name="jk_pasien" id="" class="form-control w-25"
                                                            required>
                                                            <option value="L"
                                                                {{ $berkas ? ($berkas->jenis_kelamin_pasien == 'L' ? 'selected' : '') : ($data->jk == 'L' ? 'selected' : '') }}>
                                                                Laki-laki</option>
                                                            <option value="P"
                                                                {{ $berkas ? ($berkas->jenis_kelamin_pasien == 'P' ? 'selected' : '') : ($data->jk == 'P' ? 'selected' : '') }}>
                                                                Perempuan</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:15%; vertical-align: middle;">Alamat Pasien</td>
                                                    <td><input type="text" name="alamat_pasien" required
                                                            id="" class="form-control"
                                                            value="{{ $berkas ? $berkas->alamat_pasien : $data->alamat }} {{ $berkas ? $berkas->kelurahan : $data->nm_kel }} {{ $berkas ? $berkas->kecamatan : $data->nm_kec }} {{ $berkas ? $berkas->kabupaten : $data->nm_kab }} {{ $berkas ? $berkas->propinsi : $data->nm_prop }}">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:15%; vertical-align: middle;">Nomor Rekam Medis</td>
                                                    <td><input type="text" name="nomor_rekam_medis" required
                                                            id="" class="form-control"
                                                            value="{{ $berkas ? $berkas->no_rm : $data->no_rkm_medis }}">
                                                        <input type="hidden" name="no_rawat"
                                                            value="{{ $berkas ? $berkas->no_rawat : $data->no_rawat }}">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:15%; vertical-align: middle;">Cara Bayar</td>
                                                    <td><input type="text" name="cara_bayar" required id=""
                                                            class="form-control"
                                                            value="{{ $berkas ? $berkas->cara_bayar : $data->cara_bayar }}">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:15%; vertical-align: middle;">Hak Kelas Rawat</td>
                                                    <td>
                                                        <select name="hak_kelas_rawat" id="" required
                                                            class="form-control w-25">
                                                            <option value="">Pilih Kelas</option>
                                                            <option value="Kelas 1"
                                                                {{ $berkas ? ($berkas->kelas_rawat == 'Kelas 1' ? 'selected' : '') : '' }}>
                                                                Kelas 1</option>
                                                            <option value="Kelas 2"
                                                                {{ $berkas ? ($berkas->kelas_rawat == 'Kelas 2' ? 'selected' : '') : 'selected' }}>
                                                                Kelas 2</option>
                                                            <option value="Kelas 3"
                                                                {{ $berkas ? ($berkas->kelas_rawat == 'Kelas 3' ? 'selected' : '') : '' }}>
                                                                Kelas 3</option>
                                                            <option value="VIP"
                                                                {{ $berkas ? ($berkas->kelas_rawat == 'VIP' ? 'selected' : '') : '' }}>
                                                                VIP</option>
                                                        </select>
                                                        @if ($errors->has('hak_kelas_rawat'))
                                                            <div class="text-danger">
                                                                {{ $errors->first('hak_kelas_rawat') }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:15%; vertical-align: middle;">Pindah Kelas Rawat yang
                                                        diinginkan</td>
                                                    <td><select name="pindah_kelas_rawat" class="form-control w-25">
                                                            <option value="">Pilih Kelas</option>
                                                            <option value="Kelas 1"
                                                                {{ $berkas ? ($berkas->pindah_kelas_rawat == 'Kelas 1' ? 'selected' : '') : '' }}>
                                                                Kelas 1</option>
                                                            <option value="Kelas 2"
                                                                {{ $berkas ? ($berkas->pindah_kelas_rawat == 'Kelas 2' ? 'selected' : '') : '' }}>
                                                                Kelas 2</option>
                                                            <option value="Kelas 3"
                                                                {{ $berkas ? ($berkas->pindah_kelas_rawat == 'Kelas 3' ? 'selected' : '') : '' }}>
                                                                Kelas 3</option>
                                                            <option value="VIP"
                                                                {{ $berkas ? ($berkas->pindah_kelas_rawat == 'VIP' ? 'selected' : '') : '' }}>
                                                                VIP</option>
                                                        </select></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table class="table table-bordeless table-sm">
                                            <tr>
                                                <td colspan="2" class="pt-3">Dengan ini menyatakan bahwa saya telah
                                                    menerima
                                                    informasi dari petugas kesehatan RSUP Surakarta untuk rencana rawat
                                                    inap pasien diatas dan sudah memahaminya, maka saya : </td>
                                            </tr>
                                            <tr>
                                                <td style="width:5%; text-align: center;">1. </td>
                                                <td>Setuju / Menolak * dilakukan pelayanan rawat inap
                                                    di RSUP Surakarta kepada pasien tersebut diatas.</td>
                                            </tr>
                                            <tr>
                                                <td style="width:5%; text-align: center;">2. </td>
                                                <td>Meminta dan memberikan kuasa kepada dokter, perawat/ bidan, dan tenaga
                                                    kesehatan lainnya untuk memberikan asuhan keperawatan/kebidanan,
                                                    pemeriksaan fisik yang dilakukan oleh dokter, perawat/ bidan dan
                                                    melakukan prosedur diagnostik, radiologi dan/atau terapi serta
                                                    tatalaksana sesuai pertimbangan dokter yang diperlukan atau disarankan
                                                    pada perawatan pasien diatas. </td>
                                            </tr>
                                            <tr>
                                                <td style="width:5%; text-align: center;">3. </td>
                                                <td>Telah mengetahui hak dan kewajiban pasien dan akan mentaati seluruh
                                                    peraturan yang berlaku di RSUP Surakarta. </td>
                                            </tr>
                                            <tr>
                                                <td style="width:5%; text-align: center;">4. </td>
                                                <td> Telah mengetahui tarif dan fasilitas yang tersedia di rumah sakit,
                                                    khususnya pada ruang rawat yang akan ditempati. </td>
                                            </tr>
                                            <tr>
                                                <td style="width:5%; text-align: center;">5. </td>
                                                <td>Pasien Umum : <br>
                                                    <ul>
                                                        <li> Bersedia membayar seluruh biaya perawatan dan tindakan yang
                                                            telah
                                                            diberikan kepada pasien tersebut diatas. </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width:5%; text-align: center;">6. </td>
                                                <td> Pasien JKN/ Asuransi <br>
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
                                                <td style="width:5%; text-align: center;">7. </td>
                                                <td> Bersedia mematuhi rencana terapi yang direkomendasikan oleh dokter,
                                                    jika menolak rencana atas permintaan sendiri maka saya bersedia
                                                    menanggung segala konsekuensi termasuk dalam hal jaminan pembiayaan.
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width:5%; text-align: center;">8. </td>
                                                <td> Bertanggungjawab penuh atas segala resiko yang muncul dan tidak akan
                                                    menuntut pihak RSUP Surakarta dikemudian hari.
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width:5%; text-align: center;">9. </td>
                                                <td> Menyetujui jenis pembiayaan perawatan sesuai dengan jenis pembiayaan
                                                    yang dipilih dari awal masuk sampai dengan dinyatakan pulang.
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">Demikian surat pernyataan ini saya buat dengan penuh
                                                    kesadaran tanpa ada paksaan dari pihak manapun dan dapat digunakan
                                                    sebagaimana mestinya. </td>
                                            </tr>
                                        </table>
                                    </div>

                                    <div class="tab-pane fade show active" id="custom-tabs-four-tandatangan"
                                        role="tabpanel" aria-labelledby="custom-tabs-four-tandatangan-tab">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="informan">Pemberi Informasi</label>
                                                    <input type="text" class="form-control" name="informan"
                                                        id="informan" value="{{ Auth::user()->name }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <label for="sig">Tanda tangan</label>
                                                <br />
                                                <div id="sig"></div>
                                                <br />
                                                <button id="clear" class="btn btn-danger btn-sm">Ulang Tanda
                                                    tangan</button>
                                                <textarea id="signature64" name="signed" style="display: none"></textarea>
                                                @if ($errors->has('signed'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('signed') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card -->
                            </div>
                            <div class="card-footer">
                                @if (Session::get('anak') == 'Rawat Jalan/IGD')
                                    <a href="{{ route('berkasrm.rajal') }}" class="btn btn-default">Kembali</a>
                                @else
                                    <a href="{{ route('berkasrm.ranap') }}" class="btn btn-default">Kembali</a>
                                @endif

                                @if (!empty($berkas))
                                    <a href="/berkasrm/persetujuanri/{{ Crypt::encrypt($berkas->no_rawat) }}/delete"
                                        class="btn btn-danger delete-confirm"><i class="fas fa-times-circle"></i>
                                        Hapus</a>
                                    <div class="float-right">
                                        <a href="/berkasrm/persetujuanri/{{ Crypt::encrypt($berkas->no_rawat) }}/print"
                                            class="btn btn-secondary" target="_blank"><i class="far fa-file-pdf"></i>
                                            Print</a>
                                        @if (empty($fileSftp))
                                            <a href="/berkasrm/persetujuanri/{{ Crypt::encrypt($berkas->no_rawat) }}/send"
                                                class="btn btn-info"><i class="fas fa-share-square"></i>
                                                Kirim Berkas</a>
                                        @else
                                            <a href="/berkasrm/persetujuanri/{{ Crypt::encrypt($berkas->no_rawat) }}/view"
                                                class="btn btn-info" target="_blank"><i class="fas fa-file-download"></i>
                                                Ambil Berkas</a>
                                        @endif
                                    </div>
                                @else
                                    <button type="submit"
                                        class="btn btn-primary">{{ $berkas == null ? 'Simpan' : 'Update' }}</button>
                                @endif
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
    <!-- Select2 -->
    <script src="{{ asset('template/plugins/select2/js/select2.full.min.js') }}"></script>
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
        $('#tglLahirPj,#tglLahirPasien').datetimepicker({
            format: 'YYYY-MM-DD'
        });

        //Initialize Select2 Elements
        $('.select2').select2();

        $(function() {
            var $src = $('#hal1'),
                $dst = $('#hal2');
            $src.on('input', function() {
                $dst.val($src.val());
            });
        });
    </script>
@endsection
