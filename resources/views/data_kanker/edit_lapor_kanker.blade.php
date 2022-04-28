@extends('layouts.master')

<!-- isi bagian konten -->
<!-- cara penulisan isi section yang panjang -->
@section('head')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('template/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    {{-- <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" /> --}}
    {{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" /> --}}
    <!-- Tempusdominus|Datetime Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('template/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">

            <form role="form" action="/kanker/{{ $data->idReg }}/update" method="post">
                {{ csrf_field() }}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Laporan Pasien</h3>
                    </div>
                    <!-- /.box-header -->

                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>Jenis Pelaporan</label>
                                    <input type="text" class="form-control" name="jenisLaporan"
                                        value="Penyakit Tidak Menular" readonly />
                                </div>
                                <div class="form-group">
                                    <label>Jenis Kasus</label>
                                    <input type="text" class="form-control" name="jenisKasus" value="Kanker" readonly />
                                </div>
                                <div class="form-group">
                                    <label>Nomor Rawat</label>
                                    <input type="text" class="form-control" name="noRawat" value="{{ $data->noRawat }}"
                                        readonly />
                                </div>
                                <div class="form-group">
                                    <label>NIK/No.Paspor</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="nik" value="{{ $data->nik }}"
                                            required>
                                    </div>
                                    @if ($errors->has('nik'))
                                        <div class="text-danger">
                                            {{ $errors->first('nik') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Nama Lengkap Pasien</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="namaPasien"
                                            value="{{ $data->nama_pasien }}" required>
                                    </div>
                                    @if ($errors->has('namaPasien'))
                                        <div class="text-danger">
                                            {{ $errors->first('namaPasien') }}
                                        </div>
                                    @endif
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <label>Jenis Kelamin</label>
                                    </div>
                                    <div class="col-sm-6 col-form-label">
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" id="jk1" type="radio" name="jk" value="1"
                                                @if (old('jk', $data->id_jenis_kelamin) == '1') checked @endif>
                                            <label class="custom-control-label" for="jk1">Laki-laki</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-form-label">
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" id="jk2" type="radio" name="jk" value="2"
                                                @if (old('jk', $data->id_jenis_kelamin) == '2') checked @endif>
                                            <label class="custom-control-label" for="jk2">Perempuan</label>
                                        </div>
                                    </div>
                                    @if ($errors->has('jk'))
                                        <div class="text-danger">
                                            {{ $errors->first('jk') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Tanggal Lahir</label>
                                    <div class="input-group date" id="tanggal" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input" data-target="#tanggal"
                                            data-toggle="datetimepicker" name="tgl_lahir"
                                            value="{{ old('tgl_lahir', $data->tanggal_lahir) }}" autocomplete="off"
                                            required />
                                        <div class="input-group-append" data-target="#tanggal" data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                    @if ($errors->has('tgl_lahir'))
                                        <div class="text-danger">
                                            {{ $errors->first('tgl_lahir') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Alamat</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="alamat"
                                            value="{{ old('alamat', $data->alamat) }}" required>
                                    </div>
                                    @if ($errors->has('alamat'))
                                        <div class="text-danger">
                                            {{ $errors->first('alamat') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Provinsi</label>
                                    <div class="input-group">
                                        <select class="form-control select2" name="provinsi" id="provinsi">
                                            <option value="">Pilih Provinsi</option>
                                            @foreach ($provinsi as $prov)
                                                <option value="{{ $prov->id }}">
                                                    {{ $prov->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @php
                                            if (!empty(\App\Provinsi::NamaProvinsi($data->id_provinsi))) {
                                                $nama = \App\Provinsi::NamaProvinsi($data->id_provinsi);
                                                $keterangan = $nama->nama;
                                            } else {
                                                $keterangan = 'Unknow';
                                            }
                                        @endphp
                                        <input type="text" class="form-control col-4" name="dataProvinsi"
                                            value="{{ $data->id_provinsi }}-{{ $keterangan }}" readonly>
                                    </div>
                                    @if ($errors->has('provinsi'))
                                        <div class="text-danger">
                                            {{ $errors->first('provinsi') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Kabupaten/ Kota</label>
                                    <div class="input-group">
                                        <select class="form-control select2" name="kabKota" id="kabKota">
                                            <option value="">Pilih Kab/Kota</option>
                                        </select>
                                        @php
                                            if (!empty(\App\KabKota::NamaKabKota($data->id_kab_kota))) {
                                                $nama = \App\KabKota::NamaKabKota($data->id_kab_kota);
                                                $keterangan = $nama->nama;
                                            } else {
                                                $keterangan = 'Unknow';
                                            }
                                        @endphp
                                        <input type="text" class="form-control col-4" name="dataKabKota"
                                            value="{{ $data->id_kab_kota }}-{{ $keterangan }}" readonly>
                                    </div>
                                    @if ($errors->has('kabKota'))
                                        <div class="text-danger">
                                            {{ $errors->first('kabKota') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Kecamatan</label>
                                    <div class="input-group">
                                        <select class="form-control select2" name="kecamatan" id="kecamatan">
                                            <option value="">Pilih Kecamatan</option>
                                        </select>
                                        @php
                                            if (!empty(\App\Kecamatan::NamaKec($data->id_kecamatan))) {
                                                $namakec = \App\Kecamatan::NamaKec($data->id_kecamatan);
                                                $keterangan = $namakec->nama;
                                            } else {
                                                $keterangan = 'Unknow';
                                            }
                                        @endphp
                                        <input type="text" class="form-control col-4" name="dataKecamatan"
                                            value="{{ $data->id_kecamatan }}-{{ $keterangan }}" readonly>
                                    </div>

                                    @if ($errors->has('kecamatan'))
                                        <div class="text-danger">
                                            {{ $errors->first('kecamatan') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Kelurahan</label>
                                    <div class="input-group">
                                        <select class="form-control select2" name="kelurahan" id="kelurahan">
                                            <option value="">Pilih Kelurahan</option>
                                        </select>
                                        @php
                                            if (!empty(\App\Kelurahan::NamaKel($data->id_kelurahan))) {
                                                $namakel = \App\Kelurahan::NamaKel($data->id_kelurahan);
                                                $keterangan = $namakel->nama;
                                            } else {
                                                $keterangan = 'Unknow';
                                            }
                                        @endphp
                                        <input type="text" class="form-control col-4" name="dataKelurahan"
                                            value="{{ $data->id_kelurahan }}-{{ $keterangan }}" readonly>
                                    </div>

                                    @if ($errors->has('kelurahan'))
                                        <div class="text-danger">
                                            {{ $errors->first('kelurahan') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Alamat Tinggal</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="alamatTinggal"
                                            value="{{ old('alamatTinggal', $data->alamat_tinggal) }}">
                                    </div>
                                    @if ($errors->has('alamatTinggal'))
                                        <div class="text-danger">
                                            {{ $errors->first('alamatTinggal') }}
                                        </div>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="AlamatTinggalSama"
                                            name="AlamatTinggalSama" onclick="DisableAlamatTinggal()">
                                        <label for="AlamatTinggalSama" class="custom-control-label">Alamat Tinggal sama
                                            dengan
                                            Alamat</label>
                                    </div>
                                </div>
                                <script type="text/javascript">
                                    function DisableAlamatTinggal() {
                                        var alamatSama = document.getElementById("AlamatTinggalSama");
                                        var ProvinsiTinggal = document.getElementById("provinsiTinggal");
                                        var KabKotaTinggal = document.getElementById("kabKotaTinggal");
                                        var KecamatanTinggal = document.getElementById("kecamatanTinggal");
                                        var KelurahanTinggal = document.getElementById("kelurahanTinggal");
                                        ProvinsiTinggal.disabled = alamatSama.checked ? true : false;
                                        KabKotaTinggal.disabled = alamatSama.checked ? true : false;
                                        KecamatanTinggal.disabled = alamatSama.checked ? true : false;
                                        KelurahanTinggal.disabled = alamatSama.checked ? true : false;
                                    }
                                </script>
                                <div class="form-group">
                                    <label>Provinsi Tinggal</label>
                                    <div class="input-group">
                                        <select class="form-control select2 alamat-sama" name="provinsiTinggal"
                                            id="provinsiTinggal">
                                            <option value="">Pilih Provinsi</option>
                                            @foreach ($provinsi as $prov)
                                                <option value="{{ $prov->id }}">
                                                    {{ $prov->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @php
                                            if (!empty(\App\Provinsi::NamaProvinsi($data->id_provinsi_tinggal))) {
                                                $nama = \App\Provinsi::NamaProvinsi($data->id_provinsi_tinggal);
                                                $keterangan = $nama->nama;
                                            } else {
                                                $keterangan = 'Unknow';
                                            }
                                        @endphp
                                        <input type="text" class="form-control col-4" name="dataProvinsiTinggal"
                                            value="{{ $data->id_provinsi_tinggal }}-{{ $keterangan }}" readonly>
                                    </div>
                                    @if ($errors->has('provinsiTinggal'))
                                        <div class="text-danger">
                                            {{ $errors->first('provinsiTinggal') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Kabupaten/ Kota Tinggal</label>
                                    <div class="input-group">
                                        <select class="form-control select2 alamat-sama" name="kabKotaTinggal"
                                            id="kabKotaTinggal">
                                            <option value="">Pilih Kab/Kota</option>
                                        </select>
                                        @php
                                            if (!empty(\App\KabKota::NamaKabKota($data->id_kab_kota_tinggal))) {
                                                $nama = \App\KabKota::NamaKabKota($data->id_kab_kota_tinggal);
                                                $keterangan = $nama->nama;
                                            } else {
                                                $keterangan = 'Unknow';
                                            }
                                        @endphp
                                        <input type="text" class="form-control col-4" name="dataKabKotaTinggal"
                                            value="{{ $data->id_kab_kota_tinggal }}-{{ $keterangan }}" readonly>
                                    </div>
                                    @if ($errors->has('kabKotaTinggal'))
                                        <div class="text-danger">
                                            {{ $errors->first('kabKotaTinggal') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Kecamatan Tinggal</label>
                                    <div class="input-group">
                                        <select class="form-control select2 alamat-sama" name="kecamatanTinggal"
                                            id="kecamatanTinggal">
                                            <option value="">Pilih Kecamatan</option>
                                        </select>
                                        @php
                                            if (!empty(\App\Kecamatan::NamaKec($data->id_kecamatan_tinggal))) {
                                                $namakec = \App\Kecamatan::NamaKec($data->id_kecamatan_tinggal);
                                                $keterangan = $namakec->nama;
                                            } else {
                                                $keterangan = 'Unknow';
                                            }
                                        @endphp
                                        <input type="text" class="form-control col-4" name="dataKecamatanTinggal"
                                            value="{{ $data->id_kecamatan_tinggal }}-{{ $keterangan }}" readonly>
                                    </div>
                                    @if ($errors->has('kecamatanTinggal'))
                                        <div class="text-danger">
                                            {{ $errors->first('kecamatanTinggal') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Kelurahan Tinggal</label>
                                    <div class="input-group">
                                        <select class="form-control select2 alamat-sama" name="kelurahanTinggal"
                                            id="kelurahanTinggal">
                                            <option value="">Pilih Kelurahan</option>
                                        </select>
                                        @php
                                            if (!empty(\App\Kelurahan::NamaKel($data->id_kelurahan_tinggal))) {
                                                $namakel = \App\Kelurahan::NamaKel($data->id_kelurahan_tinggal);
                                                $keterangan = $namakel->nama;
                                            } else {
                                                $keterangan = 'Unknow';
                                            }
                                        @endphp
                                        <input type="text" class="form-control col-4" name="dataKelurahanTinggal"
                                            value="{{ $data->id_kelurahan_tinggal }}-{{ $keterangan }}" readonly>
                                    </div>
                                    @if ($errors->has('kelurahanTinggal'))
                                        <div class="text-danger">
                                            {{ $errors->first('kelurahanTinggal') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Kontak Pasien</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="nohp"
                                            value="{{ old('nohp', $data->kontak_pasien) }}" required>
                                    </div>
                                    @if ($errors->has('nohp'))
                                        <div class="text-danger">
                                            {{ $errors->first('nohp') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Tanggal Masuk</label>
                                    <div class="input-group date" id="tgl_masuk" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input"
                                            data-target="#tgl_masuk" data-toggle="datetimepicker" name="tgl_masuk"
                                            value="{{ old('tgl_masuk', $data->tanggal_masuk) }}" autocomplete="off" />
                                        <div class="input-group-append" data-target="#tgl_masuk"
                                            data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                    @if ($errors->has('tgl_masuk'))
                                        <div class="text-danger">
                                            {{ $errors->first('tgl_masuk') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Cara Masuk</label>
                                    <select name="caraMasuk" class="form-control" required>
                                        <option value="">Pilih</option>
                                        @foreach ($caraMasuk as $masuk)
                                            <option value="{{ $masuk->kode_cara_masuk_pasien }}"
                                                @if (old('caraMasuk', $data->id_cara_masuk_pasien) == $masuk->kode_cara_masuk_pasien) selected @endif>
                                                {{ $masuk->cara_masuk_pasien }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('caraMasuk'))
                                        <div class="text-danger">
                                            {{ $errors->first('caraMasuk') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Asal Rujukan</label>
                                    <select class="form-control" name="asalRujukan" id="asalRujukan"
                                        onchange="EnableFaskeslain()">
                                        <option value="">Pilih</option>
                                        @foreach ($asalRujukan as $asal)
                                            <option value="{{ $asal->kode_asal_rujukan_pasien }}"
                                                @if (old('asalRujukan', $data->id_asal_rujukan_pasien) == $asal->kode_asal_rujukan_pasien) selected @endif>
                                                {{ $asal->asal_rujukan_pasien }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('asalRujukan'))
                                        <div class="text-danger">
                                            {{ $errors->first('asalRujukan') }}
                                        </div>
                                    @endif
                                </div>
                                <script type="text/javascript">
                                    function EnableFaskeslain() {
                                        var asalRujukan = document.getElementById("asalRujukan");
                                        if (asalRujukan.value == '9')
                                            document.getElementById("fasyankesLain").disabled = false;
                                        else
                                            document.getElementById("fasyankesLain").disabled = true;
                                    }
                                </script>
                                <div class="form-group">
                                    <label>Asal Rujukan pasien fasyankes lainnya*</label>
                                    <input type="text" class="form-control" name="fasyankesLain" id="fasyankesLain"
                                        value="{{ old('fasyankesLain') }}"
                                        {{ $data->asal_rujukan_pasien_fasyankes_lainnya == '' ? 'disabled' : '' }}>
                                    <h6 class="mt-2">Optional diisi jika fasyankes tidak ada di
                                        Asal Rujukan</h6>
                                    @if ($errors->has('fasyankesLain'))
                                        <div class="text-danger">
                                            {{ $errors->first('fasyankesLain') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Diagnosis Masuk</label>
                                    <input type="text" class="form-control" name="DiagnosisMasuk"
                                        value="{{ $data->id_diagnosa_masuk }}" readonly />
                                </div>
                                <div class="form-group">
                                    <label>Instalasi/Unit</label>

                                    @php
                                        $instalasileft = substr($data->id_sub_instalasi_unit, 0, 1);
                                    @endphp
                                    <div class="input-group">
                                        <select class="form-control select2" name="instalasi" id="instalasiUnit">
                                            <option>Sesuaikan Instalasi/Unit</option>
                                            @foreach ($instalasi as $instalasi)
                                                <option value="{{ $instalasi->kode_instalasi_unit }}"
                                                    {{ old('instalasi', $instalasileft) == $instalasi->kode_instalasi_unit ? 'selected' : '' }}>
                                                    {{ $instalasi->instalasi_unit }}
                                                </option>
                                            @endforeach
                                        </select>
                                        {{-- <input type="text" class="form-control col-4" value="{{ $instalasileft }}"
                                            readonly /> --}}
                                    </div>

                                    @if ($errors->has('instalasi'))
                                        <div class="text-danger">
                                            {{ $errors->first('instalasi') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Sub Instalasi/Sub Unit</label>
                                    <div class="input-group">
                                        <select class="form-control" name="subinstalasi" id="subinstalasi">
                                            <option value="">Sesuaikan Sub Instalasi/Unit</option>
                                        </select>
                                        <input type="text" class="form-control col-4" name="dataSubinstalasi"
                                            value="{{ $data->id_sub_instalasi_unit }}_{{ $data->SubInstalasi->sub_instalasi_unit }}"
                                            readonly />
                                    </div>
                                    @if ($errors->has('subinstalasi'))
                                        <div class="text-danger">
                                            {{ $errors->first('subinstalasi') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Diagnosis Utama</label>
                                    <div class="input-group">
                                        <select class="diagnosaUtama form-control" name="diagnosaUtama">
                                        </select>
                                        <input type="text" class="form-control col-4" name="dataDiagnosaUtama"
                                            value="{{ $data->id_diagnosa_utama }}" readonly />
                                    </div>
                                    @if ($errors->has('diagnosaUtama'))
                                        <div class="text-danger">
                                            {{ $errors->first('diagnosaUtama') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Diagnosis Sekunder 1</label>
                                    <div class="input-group">
                                        <select class="sekunder1 form-control" name="sekunder1">
                                        </select>
                                        <input type="text" class="form-control col-4" name="dataSekunder1"
                                            value="{{ $data->id_diagnosa_sekunder1 }}" readonly />
                                    </div>
                                    @if ($errors->has('sekunder1'))
                                        <div class="text-danger">
                                            {{ $errors->first('sekunder1') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Diagnosis Sekunder 2</label>
                                    <div class="input-group">
                                        <select class="sekunder2 form-control" name="sekunder2">
                                            <option value="">-</option>
                                        </select>
                                        <input type="text" class="form-control col-4" name="dataSekunder2"
                                            value="{{ $data->id_diagnosa_sekunder2 }}" readonly />
                                    </div>
                                    @if ($errors->has('sekunder2'))
                                        <div class="text-danger">
                                            {{ $errors->first('sekunder2') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Diagnosis Sekunder 3</label>
                                    <div class="input-group">
                                        <select class="sekunder3 form-control" name="sekunder3">
                                            <option value="">-</option>
                                        </select>
                                        <input type="text" class="form-control col-4" name="dataSekunder3"
                                            value="{{ $data->id_diagnosa_sekunder3 }}" readonly />
                                    </div>
                                    @if ($errors->has('sekunder3'))
                                        <div class="text-danger">
                                            {{ $errors->first('sekunder3') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Tanggal Diagnosis</label>

                                    <div class="input-group date" id="tglDiagnosis" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input"
                                            data-target="#tglDiagnosis" data-toggle="datetimepicker" name="tglDiagnosis"
                                            value="{{ old('tglDiagnosis', $data->tanggal_diagnosa) }}" autocomplete="off"
                                            required />
                                        <div class="input-group-append" data-target="#tglDiagnosis"
                                            data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                    @if ($errors->has('tglDiagnosis'))
                                        <div class="text-danger">
                                            {{ $errors->first('tglDiagnosis') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Tanggal Keluar</label>
                                    <div class="input-group date" id="tglKeluar" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input"
                                            data-target="#tglKeluar" data-toggle="datetimepicker" name="tglKeluar"
                                            value="{{ old('tglKeluar', $data->tanggal_keluar) }}" autocomplete="off" />
                                        <div class="input-group-append" data-target="#tglKeluar"
                                            data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                    @if ($errors->has('tglKeluar'))
                                        <div class="text-danger">
                                            {{ $errors->first('tglKeluar') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Cara Keluar</label>
                                    <select class="form-control" name="caraKeluar" required>
                                        @foreach ($caraKeluar as $keluar)
                                            <option value="{{ $keluar->kode_cara_keluar }}"
                                                {{ old('caraKeluar', $data->id_cara_keluar) == $keluar->kode_cara_keluar ? 'selected' : '' }}>
                                                {{ $keluar->cara_keluar }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('caraKeluar'))
                                        <div class="text-danger">
                                            {{ $errors->first('caraKeluar') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Keadaan Keluar / Hasil akhir pengobatan</label>
                                    <select class="form-control" name="keadaanKeluar" required>
                                        @foreach ($keadaanKeluar as $keluar)
                                            <option value="{{ $keluar->kode_keadaan_keluar }}"
                                                {{ old('keadaanKeluar', $data->id_keadaan_keluar) == $keluar->kode_keadaan_keluar ? 'selected' : '' }}>
                                                {{ $keluar->keadaan_keluar }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('keadaanKeluar'))
                                        <div class="text-danger">
                                            {{ $errors->first('keadaanKeluar') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Sebab kematian langsung (1a)</label>
                                    <div class="input-group">
                                        <select class="kematian1a form-control" name="kematian1a">
                                            <option value="">-</option>

                                        </select>
                                        <input type="text" class="form-control col-4" name="dataKematian1a"
                                            value="{{ $data->id_kematian1a }}" readonly />
                                    </div>
                                    @if ($errors->has('kematianLangsung'))
                                        <div class="text-danger">
                                            {{ $errors->first('kematianLangsung') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Sebab kematian antara (1b)</label>
                                    <div class="input-group">
                                        <select class="kematian1b form-control" name="kematian1b">
                                            <option value="">-</option>

                                        </select>
                                        <input type="text" class="form-control col-4" value="{{ $data->id_kematian1b }}"
                                            name="dataKematian1b" readonly />
                                    </div>
                                    @if ($errors->has('kematian1b'))
                                        <div class="text-danger">
                                            {{ $errors->first('kematian1b') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Sebab kematian antara (1c)</label>
                                    <div class="input-group">
                                        <select class="kematian1c form-control" name="kematian1c">
                                            <option value="">-</option>

                                        </select>
                                        <input type="text" class="form-control col-4" value="{{ $data->id_kematian1c }}"
                                            name="dataKematian1c" readonly />
                                    </div>
                                    @if ($errors->has('kematian1c'))
                                        <div class="text-danger">
                                            {{ $errors->first('kematian1c') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Sebab kematian dasar (1d)</label>
                                    <div class="input-group">
                                        <select class="kematian1d form-control" name="kematian1d">
                                            <option value="">-</option>

                                        </select>
                                        <input type="text" class="form-control col-4" value="{{ $data->id_kematian1d }}"
                                            name="dataKematian1d" readonly />
                                    </div>
                                    @if ($errors->has('kematian1d'))
                                        <div class="text-danger">
                                            {{ $errors->first('kematian1d') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Kondisi yang berkontribusi terhadap kematian</label>
                                    <select class="form-control" name="kondisi">
                                        <option value="">-</option>
                                    </select>
                                    @if ($errors->has('kondisi'))
                                        <div class="text-danger">
                                            {{ $errors->first('kondisi') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Penyebab dasar kematian</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="sebabDasar"
                                            value="{{ old('sebabDasar', $data->sebab_dasar_kematian) }}">
                                    </div>
                                    @if ($errors->has('sebabDasar'))
                                        <div class="text-danger">
                                            {{ $errors->first('sebabDasar') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Cara bayar</label>
                                    <div class="input-group">
                                        <select class="form-control" name="caraBayar" required>
                                            <option value="">Sesuaikan Data</option>
                                            @foreach ($caraBayar as $bayar)
                                                <option value="{{ $bayar->kode_cara_bayar }}"
                                                    {{ old('caraBayar', $data->id_cara_bayar) == $bayar->kode_cara_bayar ? 'selected' : '' }}>
                                                    {{ $bayar->cara_bayar }}
                                                </option>
                                            @endforeach
                                        </select>
                                        {{-- <input type="text" class="form-control col-4" value="{{ $data->kd_pj }}"
                                            readonly /> --}}
                                    </div>
                                    @if ($errors->has('caraBayar'))
                                        <div class="text-danger">
                                            {{ $errors->first('caraBayar') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>No BPJS</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="noBpjs"
                                            value="{{ old('noBpjs', $data->nomor_bpjs) }}">
                                    </div>
                                    @if ($errors->has('noBpjs'))
                                        <div class="text-danger">
                                            {{ $errors->first('noBpjs') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- /.box-body -->
                    <div class="card-footer">
                        <a href="{{ URL::previous() }}" class="btn btn-default">Kembali</a>
                        <button type="submit" class="btn btn-primary">Tambahkan</button>
                    </div>

                </div>
            </form>

        </div>
    </section>
@endsection
@section('get')
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script> --}}
    <!-- Select2 -->
    <script src="{{ asset('template/plugins/select2/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        // $('.select2').select2();
        $('.diagnosaUtama, .sekunder1, .sekunder2, .sekunder3, .kematian1a, .kematian1b, .kematian1c, .kematian1d')
            .select2({
                placeholder: 'Cari...',
                ajax: {
                    url: '/geticd10',
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: item.nama_penyakit,
                                    id: item.kd_penyakit
                                }
                            })
                        };
                    },
                    cache: true
                }
            });
    </script>
@endsection
@section('plugin')
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{ asset('template/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('template/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}">
    </script>
    {{-- <script src="{{ asset('template/plugins/select2/js/select2.full.min.js') }}"></script> --}}
    <script>
        // $(function() {
        //     //Initialize Select2 Elements
        //     $('.select2').select2()
        // });
        //Date picker
        $('#tanggal,#tgl_masuk,#tglDiagnosis,#tglKeluar').datetimepicker({
            format: 'YYYY-MM-DD'
        });

        $(function() {
            $('#provinsi').on('change', function() {
                axios.post('{{ route('getKabKota') }}', {
                        id: $(this).val()
                    })
                    .then(function(response) {
                        $('#kabKota').empty();
                        $('#kabKota').append(new Option("Pilih Kab/Kota"))

                        $.each(response.data, function(id, nama) {
                            $('#kabKota').append(new Option(nama, id))
                        })
                    });
            });
        });
        $(function() {
            $('#kabKota').on('change', function() {
                axios.post('{{ route('getKecamatan') }}', {
                        id: $(this).val()
                    })
                    .then(function(response) {
                        $('#kecamatan').empty();
                        $('#kecamatan').append(new Option("Pilih Kecamatan"))

                        $.each(response.data, function(id, nama) {
                            $('#kecamatan').append(new Option(nama, id))
                        })
                    });
            });
        });
        $(function() {
            $('#kecamatan').on('change', function() {
                axios.post('{{ route('getKelurahan') }}', {
                        id: $(this).val()
                    })
                    .then(function(response) {
                        $('#kelurahan').empty();
                        $('#kelurahan').append(new Option("Pilih Kelurahan"))

                        $.each(response.data, function(id, nama) {
                            $('#kelurahan').append(new Option(nama, id))
                        })
                    });
            });
        });
        $(function() {
            $('#provinsiTinggal').on('change', function() {
                axios.post('{{ route('getKabKota') }}', {
                        id: $(this).val()
                    })
                    .then(function(response) {
                        $('#kabKotaTinggal').empty();
                        $('#kabKotaTinggal').append(new Option("Pilih Kab/Kota"))

                        $.each(response.data, function(id, nama) {
                            $('#kabKotaTinggal').append(new Option(nama, id))
                        })
                    });
            });
        });
        $(function() {
            $('#kabKotaTinggal').on('change', function() {
                axios.post('{{ route('getKecamatan') }}', {
                        id: $(this).val()
                    })
                    .then(function(response) {
                        $('#kecamatanTinggal').empty();
                        $('#kecamatanTinggal').append(new Option("Pilih Kecamatan"))

                        $.each(response.data, function(id, nama) {
                            $('#kecamatanTinggal').append(new Option(nama, id))
                        })
                    });
            });
        });
        $(function() {
            $('#kecamatanTinggal').on('change', function() {
                axios.post('{{ route('getKelurahan') }}', {
                        id: $(this).val()
                    })
                    .then(function(response) {
                        $('#kelurahanTinggal').empty();
                        $('#kelurahanTinggal').append(new Option("Pilih Kelurahan"))

                        $.each(response.data, function(id, nama) {
                            $('#kelurahanTinggal').append(new Option(nama, id))
                        })
                    });
            });
        });
        $(function() {
            $('#instalasiUnit').on('change', function() {
                axios.post('{{ route('getSubinstalasi') }}', {
                        id: $(this).val()
                    })
                    .then(function(response) {
                        $('#subinstalasi').empty();
                        $('#subinstalasi').append(new Option("Pilih Sub Instalasi/Unit"))

                        $.each(response.data, function(id, nama) {
                            $('#subinstalasi').append(new Option(nama, id))
                        })
                    });
            });
        });
    </script>
@endsection
