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

            <form role="form" action="/master/vedika/klaimpending/{{ $data->id }}/update" method="post">
                {{ csrf_field() }}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Periode Klaim</h3>
                    </div>
                    <!-- /.box-header -->

                    <div class="card-body">
                        <div class="row">
                            <!-- text input -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Periode</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control datetimepicker-input" id="tanggal"
                                            data-target="#tanggal" data-toggle="datetimepicker" name="periode"
                                            autocomplete="off" placeholder="Tanggal periode" value="{{ $data->periode }}"
                                            required>
                                        <span class="input-group-append">
                                            <button type="button" class="btn btn-default btn-flat btn-sm"
                                                data-target="#tanggal" data-toggle="datetimepicker"><i
                                                    class="far fa-calendar-plus"></i></button>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Keterangan</label>
                                    <textarea class="form-control" name="keterangan" placeholder="Silahkan diisi jika ada">{{ $data->keterangan }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control" required>
                                        <option value="0" {{ $data->status == '0' ? 'selected' : '' }}>Open</option>
                                        <option value="1" {{ $data->status == '1' ? 'selected' : '' }}>Close</option>
                                    </select>
                                    @if ($errors->has('status'))
                                        <div class="text-danger">
                                            {{ $errors->first('status') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="card-footer">
                        <a href="/master/vedika/klaimpending" class="btn btn-default">Kembali</a>
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
            format: 'YYYY-MM-01'
        });
    </script>
@endsection
