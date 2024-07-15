@extends('layouts.master')

@section('head')
    <meta http-equiv="refresh" content="600" />
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
        @php
            if (!empty(Request::get('tanggal'))) {
                $tanggal = Request::get('tanggal');
            } else {
                $tanggal = \Carbon\Carbon::now()->format('Y-m-d');
            }
        @endphp
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Summary data terkirim
                                <a href="/satusehat/ranap/encounter" class="btn btn-sm btn-primary"
                                    target="_blank">Encounter</a>
                                <a href="/satusehat/ranap/encounterupdate" class="btn btn-sm btn-primary"
                                    target="_blank">Update
                                    Encounter</a>
                                <div class="float-right">
                                    <form action="/satusehat/ranap" method="GET">
                                        <div class="input-group input-group" id="tanggal" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input"
                                                data-target="#tanggal" data-toggle="datetimepicker" name="tanggal"
                                                autocomplete="off" value="{{ $tanggal }}">
                                            <span class="input-group-append">
                                                <button type="submit" class="btn btn-info btn-flat btn-sm"><i
                                                        class="fas fa-search"></i> Tampilkan</button>
                                            </span>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- <div style="overflow-x:auto;"> --}}
                            <table class="table table-bordered table-hover table-sm display nowrap" id="example2">
                                <thead>
                                    <tr>
                                        <th class="align-middle">No Rawat</th>
                                        <th class="align-middle">Encounter ID</th>
                                        <th class="align-middle">Nadi ID</th>
                                        <th class="align-middle">Pernafasan ID</th>
                                        <th class="align-middle">Sistol ID</th>
                                        <th class="align-middle">Diastol ID</th>
                                        <th class="align-middle">Suhu ID</th>
                                        <th class="align-middle">Created Time</th>
                                        <th class="align-middle">Updated Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dataLog as $summary)
                                        <tr>
                                            <td>{{ $summary->noRawat }}</td>
                                            <td>{{ $summary->encounter_id }}</td>
                                            <td>{{ $summary->asesmen_nadi }}</td>
                                            <td>{{ $summary->asesmen_pernapasan }}</td>
                                            <td>{{ $summary->asesmen_sistol }}</td>
                                            <td>{{ $summary->asesmen_diastol }}</td>
                                            <td>{{ $summary->asesmen_suhu }}</td>
                                            <td>{{ $summary->created_at }}</td>
                                            <td>{{ $summary->updated_at }}</td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                            {{-- </div> --}}
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            Log Error
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-hover table-sm display nowrap" id="example">
                                <thead>
                                    <tr>
                                        <th class="align-middle">No</th>
                                        <th class="align-middle">Subject</th>
                                        <th class="align-middle">Keterangan</th>
                                        <th class="align-middle">Created Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($errorLog as $index => $log)
                                        <tr>
                                            <td class="text-center">{{ ++$index }}</td>
                                            <td>{{ $log->subject }}</td>
                                            <td>{{ $log->keterangan }}</td>
                                            <td>{{ $log->created_at }}</td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
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
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": false,
                "scrollY": false,
                "scrollX": true,
            });
            $('#example').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
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
