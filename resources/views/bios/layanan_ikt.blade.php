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
                    <div class="card">
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
                        <div class="card-body">
                            <form action="/layanan/visit/lihat" method="GET">
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
                                    @can('facelift-ikt-client')
                                        <div class="col-sm-2 col-form-label">
                                            <a href="/layanan/bor/client" class="btn btn-success">Jalankan Client</a>
                                        </div>
                                    @endcan
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Data Visite Pasien <= Jam 10:00 hari ini</div>
                            </div>
                            <div class="card-body">
                                <div style="overflow-x:auto;">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="align-middle">Tanggal Transaksi</th>
                                                <th class="align-middle">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <td>{{ $visitpagi->tgl_transaksi }}</td>
                                            <td>{{ $visitpagi->jumlah }}</td>
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="card_title">Data Visite Pasien > 10:00 s.d. 12:00 hari ini</div>
                            </div>
                            <div class="card-body">
                                <div style="overflow-x:auto;">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="align-middle">Tanggal Transaksi</th>
                                                <th class="align-middle">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ $visitsiang1->tgl_transaksi }}</td>
                                                <td>{{ $visitsiang1->jumlah }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="card_title">Data Visite Pasien > 12:00 hari ini</div>
                            </div>
                            <div class="card-body">
                                <div style="overflow-x:auto;">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="align-middle">Tanggal Transaksi</th>
                                                <th class="align-middle">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ $visitsiang2->tgl_transaksi }}</td>
                                                <td>{{ $visitsiang2->jumlah }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="card_title">DPJP Tidak Visite hari ini</div>
                            </div>
                            <div class="card-body">
                                <div style="overflow-x:auto;">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="align-middle">Tanggal Transaksi</th>
                                                <th class="align-middle">jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ $tidakvisit->tgl_transaksi }}</td>
                                                <td>{{ $tidakvisit->jumlah }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="card_title">Kegiatan Visite Pasien Pertama hari ini</div>
                            </div>
                            <div class="card-body">
                                <div style="overflow-x:auto;">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="align-middle">Tanggal Transaksi</th>
                                                <th class="align-middle">jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ $pertama->tgl_transaksi }}</td>
                                                <td>{{ $pertama->jumlah }}</td>
                                            </tr>
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
                "ordering": false,
                "info": false,
                "autoWidth": false,
                "responsive": false,
                "scrollY": "300px",
                "scrollX": false,
            });
            $('#example').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": false,
                "ordering": false,
                "info": false,
                "autoWidth": false,
                "responsive": false,
                "scrollY": "300px",
                "scrollX": false,
            });
        });
        //Date picker
        $('#tanggal').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    </script>
@endsection
