@extends('layouts.master')


<!-- isi bagian konten -->
<!-- cara penulisan isi section yang panjang -->
@section('head')
    <!-- Tempusdominus|Datetime Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('template/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">

            <form role="form" action="/master/bank/update/{{ $data->id }}" method="post">
                {{ csrf_field() }}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Bank</h3>
                    </div>
                    <!-- /.box-header -->

                    <div class="card-body">
                        <div class="row">
                            <!-- text input -->
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Nama Bank</label>
                                    <select name="nama" class="form-control select2" required>
                                        @foreach ($bank as $bank)
                                            <option value="{{ $bank->kode }}-{{ $bank->uraian }}"
                                                @if ($bank->kode == $data->kd_bank) selected @endif>
                                                {{ $bank->kode }}
                                                -
                                                {{ $bank->uraian }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('nama'))
                                        <div class="text-danger">
                                            {{ $errors->first('nama') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>No Rekening</label>
                                    <input type="text" class="form-control" placeholder="Masukkan No Rekening"
                                        name="norek" value="{{ $data->norek }}" required>
                                    @if ($errors->has('norek'))
                                        <div class="text-danger">
                                            {{ $errors->first('norek') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Nama Rekening</label>
                                    <input type="text" class="form-control" placeholder="Masukkan Nama Rekening"
                                        name="namaRek" value="{{ $data->namaRek }}" required>
                                    @if ($errors->has('namaRek'))
                                        <div class="text-danger">
                                            {{ $errors->first('namaRek') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Bank Cabang</label>
                                    <input type="text" class="form-control" placeholder="Masukkan Cabang Bank"
                                        name="cabang" value="{{ $data->cabang }}" required>
                                    @if ($errors->has('cabang'))
                                        <div class="text-danger">
                                            {{ $errors->first('cabang') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6">

                                <div class="form-group">
                                    <label>No Bilyet</label>
                                    <input type="text" class="form-control" placeholder="Masukkan No Bilyet jika Ada"
                                        name="noBilyet" value="{{ $data->noBilyet }}">
                                    @if ($errors->has('noBilyet'))
                                        <div class="text-danger">
                                            {{ $errors->first('noBilyet') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Kode Rekening</label>
                                    <select name="kd_rek" class="form-control select2">
                                        @foreach ($rekening as $rekening)
                                            <option value="{{ $rekening->id }}"
                                                {{ $rekening->id == $data->rekening_id ? 'selected' : '' }}>
                                                {{ $rekening->uraian }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('kd_rek'))
                                        <div class="text-danger">
                                            {{ $errors->first('kd_rek') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Default Bank</label>
                                    <select name="default" class="form-control">
                                        <option value="0" {{ $data->default == '0' ? 'selected' : '' }}>Tidak</option>
                                        <option value="1" {{ $data->default == '1' ? 'selected' : '' }}>Ya</option>
                                    </select>
                                    @if ($errors->has('default'))
                                        <div class="text-danger">
                                            {{ $errors->first('default') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group" id="tanggal" data-target-input="nearest">
                                    <label>Tanggal Buka</label>
                                    <input type="text" class="form-control datetimepicker-input" data-target="#tanggal"
                                        data-toggle="datetimepicker" name="tanggal" autocomplete="off"
                                        value="{{ $data->tgl_buka }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="card-footer">
                        <a href="/master/bank" class="btn btn-default">Kembali</a>
                        <button type="submit" class="btn btn-primary">Perbaharui</button>
                    </div>

                </div>
            </form>

        </div>
    </section>
@endsection
@section('plugin')
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{ asset('template/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('template/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <script>
        //Date picker
        $('#tanggal').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    </script>
@endsection
