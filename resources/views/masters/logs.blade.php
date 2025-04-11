@extends('layouts.master')

@section('head')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('template/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('template/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    @php
                        if (!empty(Request::get('tanggal'))) {
                            $tanggal = Request::get('tanggal') . '-15';
                            $tanggal = new \Carbon\Carbon($tanggal);
                        } else {
                            $tanggal = \Carbon\Carbon::now();
                        }
                    @endphp
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><strong>{{ session('anak') }}</strong></h3>
                            <form action="/logs" method="GET">
                                <div class="d-flex float-right">
                                    <input type="text" class="form-control datetimepicker-input mr-2 ml-2"
                                        id="tanggal" data-target="#tanggal" data-toggle="datetimepicker"
                                        name="tanggal" autocomplete="off" value="{{ $tanggal }}"
                                        style="max-width: 100px">
                                    <button type="submit" class="btn btn-info btn-block btn-sm"
                                        style="max-width: 100px"><i class="fas fa-search"></i> Tampilkan</button>

                                </div>
                            </form>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table class="table-bordered table-sm table-hover" style="width: 100%;" id="example">
                                <thead>
                                    <tr>
                                        <th style="width: 10%;">Log Name</th>
                                        <th style="width: 10%;">Description</th>
                                        <th style="width: 10%;">Causer</th>
                                        <th style="width: 10%;">Created At</th>
                                        <th style="width: 60%;">Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logs as $log)
                                        <tr>
                                            <td>{{ $log->log_name }}</td>
                                            <td><strong>{{ $log->description }}</strong></td>
                                            <td>{{ $log->causer->name ?? 'System' }}</td>
                                            <td>{{ $log->created_at }}</td>
                                            <td>{{ $log->properties ? json_encode($log->properties) : '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No logs found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                        {{-- <div class="card-footer">
                            {!! $logs->links() !!}
                        </div> --}}
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
    {{-- <!-- jQuery -->
    <script src="{{ asset('template/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('template/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script> --}}
    <!-- DataTables  & Plugins -->
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
     <!-- Tempusdominus|Datetime Bootstrap 4 -->
     <link rel="stylesheet"
     href="{{ asset('template/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <script>
        $(function() {
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": false,
            });
            $('#example').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "order": [[3, "desc"]],
                "info": true,
                "autoWidth": false,
                "responsive": false,
            });
        });
        //Date picker
        $('#tanggal,#tanggal_hapus').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    </script>
@endsection
