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

            <form role="form" action="/penerimaan/update/{{ $data->id }}" method="post">
                {{ csrf_field() }}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Penerimaan</h3>
                    </div>
                    <!-- /.box-header -->

                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>Kode Akun</label>
                                    <select name="kd_akun" class="form-control select2" disabled>
                                        @foreach ($akun as $akun)
                                            @if (substr($akun->kode, 0, 1) == '4')
                                                <option value="{{ $akun->kode }}"
                                                    @if ($data->kd_akun == $akun->kode) selected @endif>{{ $akun->kode }} -
                                                    {{ $akun->uraian }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @if ($errors->has('kd_akun'))
                                        <div class="text-danger">
                                            {{ $errors->first('kd_akun') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Jumlah</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="number" class="form-control" name="jumlah"
                                            value="{{ $data->jumlah }}" required>

                                    </div>
                                    @if ($errors->has('jumlah'))
                                        <div class="text-danger">
                                            {{ $errors->first('jumlah') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Tanggal Transaksi</label>
                                    <div class="input-group">
                                        <div class="input-group date" id="tanggal" data-target-input="nearest">
                                            {{-- <input type="text" class="form-control" name="created_at"
                                                value="{{ $data->tgl_transaksi }}" readonly> --}}
                                            <input type="text" class="form-control datetimepicker-input"
                                                data-target="#tanggal" data-toggle="datetimepicker" name="tanggal"
                                                value="{{ $data->tgl_transaksi }}" autocomplete="off" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Pembaharuan Terakhir</label>

                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ $data->updated_at }}"
                                            readonly>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>


                    <!-- /.box-body -->
                    <div class="card-footer">
                        <a href="/penerimaan" class="btn btn-default">Kembali</a>
                        <button type="submit" class="btn btn-primary">Perbaharui</button>
                    </div>

                </div>
            </form>

        </div>
    </section>
@endsection
@section('plugin')
    <!-- Select2 -->
    <script src="{{ asset('template/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{ asset('template/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('template/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <script>
        $(function() {
            //Initialize Select2 Elements
            $('.select2').select2()
        });
        //Date picker
        // $('#tanggal').datetimepicker({
        //     format: 'YYYY-MM-DD'
        // });
    </script>
@endsection
