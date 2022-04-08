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

            <form role="form" action="/rsonline/pasienterlapor/pulang/{{ $data->lapId }}" method="post">
                {{ csrf_field() }}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Laporan Kepulangan Pasien</h3>
                    </div>
                    <!-- /.box-header -->

                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>Tanggal Keluar</label>
                                    <div class="input-group date" id="tgl_keluar" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input"
                                            data-target="#tgl_keluar" data-toggle="datetimepicker" name="tgl_keluar"
                                            value="{{ old('tgl_keluar') }}" autocomplete="off" required />
                                        <div class="input-group-append" data-target="#tgl_keluar"
                                            data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                    @if ($errors->has('tgl_keluar'))
                                        <div class="text-danger">
                                            {{ $errors->first('tgl_keluar') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Status Keluar</label>
                                    <select name="statusKeluar" class="form-control" required>
                                        @foreach ($datakeluar as $keluar)
                                            <option value="{{ $keluar->id }}_{{ $keluar->nama }}">
                                                {{ $keluar->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('statusKeluar'))
                                        <div class="text-danger">
                                            {{ $errors->first('statusKeluar') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Penyebab Kematian</label>
                                    <select name="penyebabKematian" class="form-control">
                                        <option value="0">Pilih/Kosongkan</option>
                                        @foreach ($datapenyebabkematian as $datakematian)
                                            <option value="{{ $datakematian->id }}_{{ $datakematian->nama }}">
                                                {{ $datakematian->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('penyebabKematian'))
                                        <div class="text-danger">
                                            {{ $errors->first('penyebabKematian') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Penyebab Kematian Langsung</label>
                                    <select name="penyebabKematianLangsung" class="form-control">
                                        <option value="0">Pilih/Kosongkan</option>
                                        @foreach ($datapenyebabkematianlangsung as $datalangsung)
                                            <option value="{{ $datalangsung->id }}_{{ $datalangsung->description }}">
                                                {{ $datalangsung->description }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('penyebabKematianLangsung'))
                                        <div class="text-danger">
                                            {{ $errors->first('penyebabKematianLangsung') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Status Pasien Saat Meninggal</label>
                                    <select name="statusPasienMeninggal" class="form-control">
                                        <option value="0">Pilih/Kosongkan</option>
                                        @foreach ($datapasiensaatmeninggal as $datameninggal)
                                            <option value="{{ $datameninggal->id }}_{{ $datameninggal->deskripsi }}">
                                                {{ $datameninggal->deskripsi }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('statusPasienMeninggal'))
                                        <div class="text-danger">
                                            {{ $errors->first('statusPasienMeninggal') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Komorbid Coinsiden</label>
                                    <select name="komorbidCoinsiden" class="form-control">
                                        <option value="0">Pilih/Kosongkan</option>
                                        @foreach ($datakomorbidcoinsiden as $datainsiden)
                                            <option value="{{ $datainsiden->id }}_{{ $datainsiden->nama }}">
                                                {{ $datainsiden->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('komorbidCoinsiden'))
                                        <div class="text-danger">
                                            {{ $errors->first('komorbidCoinsiden') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Nama Lengkap Pasien</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ $data->namaPasien }}"
                                            readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Tanggal Lahir</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ $data->tgl_lahir }}"
                                            readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- /.box-body -->
                    <div class="card-footer">
                        <a href="{{ URL::previous() }}" class="btn btn-default">Kembali</a>
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
        $('#tgl_keluar').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    </script>
@endsection
