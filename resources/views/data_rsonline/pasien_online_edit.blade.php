@extends('layouts.master')

<!-- isi bagian konten -->
<!-- cara penulisan isi section yang panjang -->
@section('head')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('template/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <!-- Tempusdominus|Datetime Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('template/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">

            <form role="form" action="/rsonline/pasienterlapor/updatelap/{{ $data->lapId }}" method="post">
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
                                    <label>Kewarganegaraan</label>
                                    <select name="kewarganegaraan" class="form-control select2" required>
                                        @foreach ($kewarganegaraan as $negara)
                                            <option value="{{ $negara->id }}"
                                                @if ($negara->id == $data->kewarganegaraan) selected @endif>{{ $negara->nicename }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('kewarganegaraan'))
                                        <div class="text-danger">
                                            {{ $errors->first('kewarganegaraan') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>NIK</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="nik"
                                            value="{{ old('nik', $data->nik) }}" required>
                                    </div>
                                    @if ($errors->has('nik'))
                                        <div class="text-danger">
                                            {{ $errors->first('nik') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>No Passport</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="noPassport"
                                            placeholder="Kosongkan jika tidak ada"
                                            value="{{ old('noPassport', $data->noPassport) }}">
                                    </div>
                                    @if ($errors->has('noPassport'))
                                        <div class="text-danger">
                                            {{ $errors->first('noPassport') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Asal Pasien</label>

                                    <select name="asal" class="form-control" required>
                                        @foreach ($dataasal as $asal)
                                            <option value="{{ $asal->id }}"
                                                {{ $asal->id == old('asal', $data->asalPasien) ? 'selected' : '' }}>
                                                {{ $asal->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('asal'))
                                        <div class="text-danger">
                                            {{ $errors->first('asal') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>No RM / No Rawat</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="noRM"
                                            value="{{ old('noRm', $data->noRm) }}" required>
                                        <input type="text" class="form-control" name="noRawat"
                                            value="{{ old('noRawat', $data->noRawat) }}" readonly>
                                    </div>
                                    @if ($errors->has('noRM'))
                                        <div class="text-danger">
                                            {{ $errors->first('noRM') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Nama Lengkap Pasien</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="namaPasien"
                                            value="{{ old('namaPasien', $data->namaPasien) }}" required>
                                    </div>
                                    @if ($errors->has('namaPasien'))
                                        <div class="text-danger">
                                            {{ $errors->first('namaPasien') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Nama Inisial Pasien</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="inisial"
                                            value="{{ old('inisial', $data->inisial) }}" required>
                                    </div>
                                    @if ($errors->has('inisial'))
                                        <div class="text-danger">
                                            {{ $errors->first('inisial') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Tanggal Lahir</label>
                                    <div class="input-group date" id="tanggal" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input" data-target="#tanggal"
                                            data-toggle="datetimepicker" name="tgl_lahir"
                                            value="{{ old('tgl_lahir', $data->tgl_lahir) }}" autocomplete="off"
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
                                    <label>Email</label>
                                    <div class="input-group">
                                        <input type="email" class="form-control" name="email"
                                            value="{{ old('email', $data->email) }}"
                                            placeholder="Kosongkan jika tidak punya">
                                    </div>
                                    @if ($errors->has('email'))
                                        <div class="text-danger">
                                            {{ $errors->first('email') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>No Telepon</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="nohp"
                                            value="{{ old('nohp', $data->nohp) }}" required>
                                    </div>
                                    @if ($errors->has('nohp'))
                                        <div class="text-danger">
                                            {{ $errors->first('nohp') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <label>Jenis Kelamin</label>
                                    </div>
                                    <div class="col-sm-6 col-form-label">
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" id="jk1" type="radio" name="jk" value="L"
                                                @if (old('jk', $data->jk) == 'L') checked @endif>
                                            <label class="custom-control-label" for="jk1">Laki-laki</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-form-label">
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" id="jk2" type="radio" name="jk" value="P"
                                                @if (old('jk', $data->jk) == 'P') checked @endif>
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
                                    <label>Provinsi</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="provinsi"
                                            value="{{ old('provinsi', $data->provinsi) }}" required>
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
                                        <input type="text" class="form-control" name="kabKota"
                                            value="{{ old('kabKota', $data->kabKota) }}" required>
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
                                        <input type="text" class="form-control" name="kecamatan"
                                            value="{{ old('kecamatan', $data->kecamatan) }}" required>
                                    </div>
                                    @if ($errors->has('kecamatan'))
                                        <div class="text-danger">
                                            {{ $errors->first('kecamatan') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Pekerjaan</label>
                                    {{-- <div class="input-group">
                                        <input type="text" class="form-control" name="pekerjaan"
                                            value="{{ $data->pekerjaan }}" required>
                                    </div> --}}
                                    <select name="pekerjaan" class="form-control" required>
                                        @foreach ($datapekerjaan as $pekerjaan)
                                            <option value="{{ $pekerjaan->id }}"
                                                @if (old('pekerjaan', $pekerjaan->id) == $data->pekerjaan) selected @endif>{{ $pekerjaan->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('pekerjaan'))
                                        <div class="text-danger">
                                            {{ $errors->first('pekerjaan') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Tanggal Masuk</label>

                                    <div class="input-group date" id="tgl_masuk" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input"
                                            data-target="#tgl_masuk" data-toggle="datetimepicker" name="tgl_masuk"
                                            value="{{ old('tgl_masuk', $data->tgl_masuk) }}" autocomplete="off" />
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
                                    <label>Jenis Pasien</label>
                                    {{-- <div class="input-group">
                                        <input type="text" class="form-control" name="jenis_pasien" value="" required>
                                    </div> --}}
                                    <select class="form-control" name="jenis_pasien">
                                        @foreach ($datajenis as $jenis)
                                            <option value="{{ $jenis->id }}"
                                                @if ($jenis->id == $data->jenis_pasien) selected @endif>{{ $jenis->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('jenis_pasien'))
                                        <div class="text-danger">
                                            {{ $errors->first('jenis_pasien') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Varian Covid</label>
                                    <select class="form-control" name="varian_covid">
                                        @foreach ($datavarian as $varian)
                                            <option value="{{ $varian->id }}"
                                                {{ old('varian_covid', $data->varian_covid) == $varian->id ? 'selected' : '' }}>
                                                {{ $varian->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('varian_covid'))
                                        <div class="text-danger">
                                            {{ $errors->first('varian_covid') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Status Pasien</label>

                                    <select class="form-control" name="status_pasien">
                                        @foreach ($statuspasien as $statuspasien)
                                            <option value="{{ $statuspasien->id }}"
                                                {{ old('status_pasien', $data->status_pasien) == $statuspasien->id ? 'selected' : '' }}>
                                                {{ $statuspasien->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('status_pasien'))
                                        <div class="text-danger">
                                            {{ $errors->first('status_pasien') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <label>Status Coinsiden</label>
                                    </div>
                                    {{-- <div class="input-group">
                                        <input type="text" class="form-control" name="status_coinsiden" value="" required>
                                    </div> --}}
                                    <div class="col-sm-6 col-form-label">
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" name="status_coinsiden"
                                                value="1" id="statusCoinsiden1" required
                                                {{ old('status_coinsiden', $data->status_coinsiden) == '1' ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="statusCoinsiden1">Ya</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-form-label">
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" name="status_coinsiden"
                                                value="0" id="statusCoinsiden2" required
                                                {{ old('status_coinsiden', $data->status_coinsiden) == '0' ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="statusCoinsiden2">Tidak</label>
                                        </div>
                                    </div>
                                    @if ($errors->has('status_coinsiden'))
                                        <div class="text-danger">
                                            {{ $errors->first('status_coinsiden') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Status Rawat</label>
                                    <select class="form-control" name="status_rawat" required>
                                        @foreach ($statusrawat as $statusrawat)
                                            <option value="{{ $statusrawat->id }}"
                                                {{ old('status_rawat', $data->status_rawat) == $statusrawat->id ? 'selected' : '' }}>
                                                {{ $statusrawat->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('status_rawat'))
                                        <div class="text-danger">
                                            {{ $errors->first('status_rawat') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Alat Oksigen</label>

                                    <select class="form-control" name="alat_oksigen">
                                        <option value="">Tidak Pakai</option>
                                        @foreach ($alatoksigen as $alatoksigen)
                                            <option value="{{ $alatoksigen->id }}"
                                                {{ old('alat_oksigen', $data->alat_oksigen) == $alatoksigen->id ? 'selected' : '' }}>
                                                {{ $alatoksigen->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('alat_oksigen'))
                                        <div class="text-danger">
                                            {{ $errors->first('alat_oksigen') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12 ">
                                        <label>Penyintas</label>
                                    </div>

                                    <div class="col-sm-6 col-form-label">
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" name="penyintas" value="1"
                                                id="penyintas1" required
                                                {{ old('penyintas', $data->penyintas) == '1' ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="penyintas1">Ya</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-form-label">
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" name="penyintas" value="0"
                                                id="penyintas2" required
                                                {{ old('penyintas', $data->penyintas) == '0' ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="penyintas2">Tidak</label>
                                        </div>
                                    </div>
                                    @if ($errors->has('penyintas'))
                                        <div class="text-danger">
                                            {{ $errors->first('penyintas') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Tanggal Onset Gejala</label>

                                    <div class="input-group date" id="tgl_gejala" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input"
                                            data-target="#tgl_gejala" data-toggle="datetimepicker" name="tgl_gejala"
                                            value="{{ old('tgl_gejala', $data->tgl_gejala) }}" autocomplete="off"
                                            required />
                                        <div class="input-group-append" data-target="#tgl_gejala"
                                            data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                    @if ($errors->has('tgl_gejala'))
                                        <div class="text-danger">
                                            {{ $errors->first('tgl_gejala') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Kelompok Gejala</label>

                                    <select class="form-control" name="kelompok_gejala" required>
                                        @foreach ($datakelompok as $kelompokgejala)
                                            <option value="{{ $kelompokgejala->id }}"
                                                {{ old('kelompok_gejala', $data->kelompok_gejala) == $kelompokgejala->id ? 'selected' : '' }}>
                                                {{ $kelompokgejala->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('kelompok_gejala'))
                                        <div class="text-danger">
                                            {{ $errors->first('kelompok_gejala') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <table class="table borderless">
                                <thead>
                                    <tr>
                                        <th colspan="2">
                                            <h4>GEJALA</h4>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="form-group row">
                                                <div class="col-sm-4 col-form-label">
                                                    <label>Demam</label>
                                                </div>
                                                <div class="col-sm-4 col-form-label">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="demam" value="1"
                                                            id="demam1" required
                                                            {{ old('demam', $data->demam) == '1' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="demam1">Ya</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-form-label">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="demam" value="0"
                                                            id="demam2" required
                                                            {{ old('demam', $data->demam) == '0' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="demam2">Tidak</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-4 col-form-label">
                                                    <label>Batuk</label>
                                                </div>
                                                <div class="col-sm-4 col-form-label">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="batuk" value="1"
                                                            id="batuk1" required
                                                            {{ old('batuk', $data->batuk) == '1' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="batuk1">Ya</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-form-label">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="batuk" value="0"
                                                            id="batuk2" required
                                                            {{ old('batuk', $data->batuk) == '0' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="batuk2">Tidak</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-4 col-form-label">
                                                    <label>Pilek</label>
                                                </div>
                                                <div class="col-sm-4 col-form-label">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="pilek" value="1"
                                                            id="pilek1" required
                                                            {{ old('pilek', $data->pilek) == '1' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="pilek1">Ya</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-form-label">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="pilek" value="0"
                                                            id="pilek2" required
                                                            {{ old('pilek', $data->pilek) == '0' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="pilek2">Tidak</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-4 col-form-label">
                                                    <label>Sakit Tenggorokan</label>
                                                </div>
                                                <div class="col-sm-4 col-form-label">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" id="tenggorokan1"
                                                            name="sakit_tenggorokan" value="1" required
                                                            {{ old('sakit_tenggorokan', $data->sakit_tenggorokan) == '1' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="tenggorokan1">Ya</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-form-label">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" id="tenggorokan2"
                                                            name="sakit_tenggorokan" value="0" required
                                                            {{ old('sakit_tenggorokan', $data->sakit_tenggorokan) == '0' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="tenggorokan2">Tidak</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-4 col-form-label">
                                                    <label>Sesak Napas</label>
                                                </div>
                                                <div class="col-sm-4 col-form-label">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="sesak_napas"
                                                            id="sesak1" value="1" required
                                                            {{ old('sesak_napas', $data->sesak_napas) == '1' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="sesak1">Ya</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-form-label">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="sesak_napas"
                                                            id="sesak2" value="0" required
                                                            {{ old('sesak_napas', $data->sesak_napas) == '0' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="sesak2">Tidak</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-4 col-form-label">
                                                    <label>Lemas</label>
                                                </div>
                                                <div class="col-sm-4 col-form-label">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="lemas" value="1"
                                                            id="lemas1" required
                                                            {{ old('lemas', $data->lemas) == '1' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="lemas1">Ya</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-form-label">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="lemas" value="0"
                                                            id="lemas2" required
                                                            {{ old('lemas', $data->lemas) == '0' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="lemas2">Tidak</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-4 col-form-label">
                                                    <label>Nyeri Otot</label>
                                                </div>
                                                <div class="col-sm-4 col-form-label">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="nyeri_otot"
                                                            id="nyeri1" value="1" required
                                                            {{ old('nyeri_otot', $data->nyeri_otot) == '1' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="nyeri1">Ya</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-form-label">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="nyeri_otot"
                                                            id="nyeri2" value="0" required
                                                            {{ old('nyeri_otot', $data->nyeri_otot) == '0' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="nyeri2">Tidak</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group row">
                                                <div class="col-sm-4 col-form-label">
                                                    <label>Mual Muntah</label>
                                                </div>
                                                <div class="col-sm-4 col-form-label">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="mual_muntah"
                                                            id="mual1" value="1" required
                                                            {{ old('mual_muntah', $data->mual_muntah) == '1' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="mual1">Ya</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-form-label">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="mual_muntah"
                                                            id="mual2" value="0" required
                                                            {{ old('mual_muntah', $data->mual_muntah) == '0' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="mual2">Tidak</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-4 col-form-label">
                                                    <label>Diare</label>
                                                </div>
                                                <div class="col-sm-4 col-form-label">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="diare" value="1"
                                                            id="diare1" required
                                                            {{ old('diare', $data->diare) == '1' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="diare1">Ya</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-form-label">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="diare" value="0"
                                                            id="diare2" required
                                                            {{ old('diare', $data->diare) == '0' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="diare2">Tidak</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-4 col-form-label">
                                                    <label>Anosmia</label>
                                                </div>
                                                <div class="col-sm-4 col-form-label">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="anosmia"
                                                            id="anosmia1" value="1" required
                                                            {{ old('anosmia', $data->anosmia) == '1' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="anosmia1">Ya</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-form-label">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="anosmia"
                                                            id="anosmia2" value="0" required
                                                            {{ old('anosmia', $data->anosmia) == '0' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="anosmia2">Tidak</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-4 col-form-label">
                                                    <label>Napas Cepat</label>
                                                </div>
                                                <div class="col-sm-4 col-form-label">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="napas_cepat"
                                                            id="napas1" value="1" required
                                                            {{ old('napas_cepat', $data->napas_cepat) == '1' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="napas1">Ya</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-form-label">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="napas_cepat"
                                                            id="napas2" value="0" required
                                                            {{ old('napas_cepat', $data->napas_cepat) == '0' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="napas2">Tidak</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-4 col-form-label">
                                                    <label>Frekuensi Napas 30x / menit</label>
                                                </div>
                                                <div class="col-sm-4 col-form-label">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="frek_napas"
                                                            id="frek1" value="1" required
                                                            {{ old('frek_napas', $data->frek_napas) == '1' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="frek1">Ya</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-form-label">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="frek_napas"
                                                            id="frek2" value="0" required
                                                            {{ old('frek_napas', $data->frek_napas) == '0' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="frek2">Tidak</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-4 col-form-label">
                                                    <label>Distres Pernapasan Berat</label>
                                                </div>
                                                <div class="col-sm-4 col-form-label">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" id="distres1"
                                                            name="distres_pernapasan" value="1" required
                                                            {{ old('distres_pernapasan', $data->distres_pernapasan) == '1' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="distres1">Ya</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-form-label">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" id="distres2"
                                                            name="distres_pernapasan" value="0" required
                                                            {{ old('distres_pernapasan', $data->distres_pernapasan) == '0' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="distres2">Tidak</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-4 col-form-label">
                                                    <label>Lainnya</label>
                                                </div>
                                                <div class="col-sm-4 col-form-label">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="lainnya"
                                                            id="lain1" value="1" required
                                                            {{ old('lainnya', $data->lainnya) == '1' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="lain1">Ya</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-form-label">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="lainnya"
                                                            id="lain2" value="0" required
                                                            {{ old('lainnya', $data->lainnya) == '0' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="lain2">Tidak</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>


                    <!-- /.box-body -->
                    <div class="card-footer">
                        <a href="/rsonline/pasienterlapor" class="btn btn-default">Kembali</a>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>

                </div>
            </form>

        </div>
    </section>
@endsection
@section('plugin')
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{ asset('template/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('template/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}">
    </script>
    <!-- Select2 -->
    <script src="{{ asset('template/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(function() {
            //Initialize Select2 Elements
            $('.select2').select2()
        });
        //Date picker
        $('#tanggal,#tgl_masuk,#tgl_gejala').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    </script>
@endsection
