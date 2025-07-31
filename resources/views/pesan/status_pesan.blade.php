@extends('layouts.master')

@section('head')
    <meta http-equiv="refresh" content="120;" />
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
                    @php
                        if (!empty(Request::get('tanggal'))) {
                            $tanggal = Request::get('tanggal');
                            $tanggal = new \Carbon\Carbon($tanggal);
                        } else {
                            $tanggal = \Carbon\Carbon::now();
                        }
                    @endphp
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><strong>{{ session('anak') }}</strong></h3>
                            <form action="{{ route('wa.status') }}" method="GET">
                                <div class="d-flex float-right">
                                    <input type="text" class="form-control datetimepicker-input mr-2 ml-2"
                                        id="tanggal" data-target="#tanggal" data-toggle="datetimepicker"
                                        name="tanggal" autocomplete="off" value="{{ $tanggal }}"
                                        style="max-width: 130px">
                                    <button type="submit" class="btn btn-info btn-block btn-sm"
                                        style="max-width: 120px"><i class="fas fa-search"></i> Tampilkan</button>
                                </div>
                            </form>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table id="example" class="table table-bordered table-hover table-sm">
                                    <thead>
                                        <tr >
                                            <th>No RM</th>
                                            <th>Nama Pasien</th>
                                            <th>No Telepon</th>
                                            <th>Tanggal Periksa</th>
                                            <th>Status</th>
                                            <th style="vertical-align:center;">Pembaharuan Terakhir</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data as $log)
                                            <tr>
                                                <td>{{ $log->no_rm }}</td>
                                                <td>{{ $log->nama_pasien }}</td>
                                                <td>{{ $log->no_telp }}</td>
                                                <td>{{ $log->tgl_periksa }}</td>
                                                <td>{{ $log->status }}</td>
                                                <td class="text-center">
                                                    {{ \Carbon\Carbon::parse($log->updated_at)->format('Y-m-d H:i') }}
                                                </td>
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
            $('#example').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "order": [[5, 'desc']],
                "info": true,
                "autoWidth": false,
                "responsive": false,
                //"scrollY": "300px",
                //"scrollX": false,
            });
        });
        //Date picker
        $('#tanggal').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    </script>

@endsection
