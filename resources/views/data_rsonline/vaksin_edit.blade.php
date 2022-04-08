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

            <form role="form" action="/rsonline/pasienterlapor/patchvaksin/{{ $data->lapVaksinId }}" method="post">
                {{ csrf_field() }}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Laporan Vaksinasi Pasien</h3>
                    </div>
                    <!-- /.box-header -->

                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>Dosis Vaksinasi</label>
                                    <select name="dosisVaksin" class="form-control select2" required>
                                        @foreach ($dosisvaksin as $vaksin)
                                            <option value="{{ $vaksin->id }}_{{ $vaksin->nama }}"
                                                {{ $data->dosisVaksinId == $vaksin->id ? 'selected' : '' }}>
                                                {{ $vaksin->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('dosisVaksin'))
                                        <div class="text-danger">
                                            {{ $errors->first('dosisVaksin') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Jenis Vaksinasi</label>
                                    <select name="jenisVaksin" class="form-control select2" required>
                                        @foreach ($jenisvaksin as $jenis)
                                            <option value="{{ $jenis->id }}_{{ $jenis->nama }}"
                                                {{ $data->jenisVaksinId == $jenis->id ? 'selected' : '' }}>
                                                {{ $jenis->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('jenisVaksin'))
                                        <div class="text-danger">
                                            {{ $errors->first('jenisVaksin') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Nama Lengkap Pasien</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control"
                                            value="{{ $data->PelaporanCovid->namaPasien }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Tanggal Lahir</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control"
                                            value="{{ $data->PelaporanCovid->tgl_lahir }}" readonly>
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
        $('#tanggal,#tgl_masuk,#tgl_gejala').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    </script>
@endsection
