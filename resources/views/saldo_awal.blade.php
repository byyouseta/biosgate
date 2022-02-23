@extends('layouts.master')

@section('head')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('template/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- Tempusdominus|Datetime Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('template/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    @if (Request::get('tanggal'))
                        @php
                            $tanggal = Request::get('tanggal');
                        @endphp
                    @else
                        @php
                            $tanggal = \Carbon\Carbon::now()
                                ->locale('id')
                                ->format('Y-m-d');
                        @endphp
                    @endif
                    <div class="card">
                        <div class="card-header">
                            {{-- <div class="card_title">Saldo awal per hari ini</div> --}}
                            {{-- <div class="card-body"> --}}
                            <form action="/saldo/lihat" method="GET">
                                <div class="form-group row">
                                    <div class="col-sm-1 col-form-label">
                                        <label>Tanggal</label>
                                    </div>
                                    <div class="col-sm-2 col-form-label">
                                        <div class="input-group date" id="tanggal" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input"
                                                data-target="#tanggal" data-toggle="datetimepicker" name="tanggal"
                                                value="{{ $tanggal }}" />
                                            <div class="input-group-append" data-target="#tanggal"
                                                data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1 col-form-label">
                                        <button type="Submit" class="btn btn-primary btn-block">Lihat</button>
                                    </div>
                                    <div class="col-sm-2 col-form-label">
                                        <a href="/saldo/client" target="_blank" class="btn btn-success">Jalankan Client</a>
                                    </div>
                                </div>
                            </form>
                            {{-- </div> --}}
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Kode Kelas</th>
                                            <th class="align-middle">Jumlah Hari</th>
                                            <th class="align-middle">Jumlah Pasien</th>
                                            <th class="align-middle">per Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- <tr>
                                            <td>Kelas 1</td>
                                            <td>{{ $lamakelas1 }}</td>
                                            <td>{{ $pasien1 }}</td>
                                            <td>{{ \Carbon\Carbon::now()->locale('id')->format('Y/m/d') }}</td>
                                        </tr>
                                        <tr>
                                            <td>Kelas 2</td>
                                            <td>{{ $lamakelas2 }}</td>
                                            <td>{{ $pasien2 }}</td>
                                            <td>{{ \Carbon\Carbon::now()->locale('id')->format('Y/m/d') }}</td>
                                        </tr>
                                        <tr>
                                            <td>Kelas 3</td>
                                            <td>{{ $lamakelas3 }}</td>
                                            <td>{{ $pasien3 }}</td>
                                            <td>{{ \Carbon\Carbon::now()->locale('id')->format('Y/m/d') }}</td>
                                        </tr> --}}
                                        @foreach ($saldoawal as $data)
                                            <tr>
                                                <td>{{ $data->kd_kelas }}</td>
                                                <td>{{ $data->jml_hari }}</td>
                                                <td>{{ $data->jml_pasien }}</td>
                                                <td>{{ $data->tgl_transaksi }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
@endsection
@section('plugin')
    <script src="{{ asset('template/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('template/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('template/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('template/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{ asset('template/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('template/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <script>
        $(function() {
            $('#example2').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": false,
                "autoWidth": false,
                "responsive": false,
            });
        });
        //Date picker
        $('#tanggal').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    </script>
@endsection
