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
                                <a href="/satusehat/bundle" class="btn btn-sm btn-primary" target="_blank">Api Bundle</a>
                                {{-- <a href="/satusehat/encounter" class="btn btn-sm btn-primary" target="_blank">Api
                                    Encounter</a> --}}
                                <a href="/satusehat/composition" class="btn btn-sm btn-primary" target="_blank">Api
                                    Composition</a>
                                <a href="/satusehat/medication" class="btn btn-sm btn-primary" target="_blank">Api
                                    Medication</a>
                                <a href="/satusehat/lab" class="btn btn-sm btn-primary" target="_blank">Api
                                    Lab</a>
                                <div class="float-right">
                                    <form action="/satusehat" method="GET">
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
                                        <th class="align-middle">Condition1 ID</th>
                                        <th class="align-middle">Condition2 ID</th>
                                        <th class="align-middle">HeartRate ID</th>
                                        <th class="align-middle">Respiratory ID</th>
                                        <th class="align-middle">Systol ID</th>
                                        <th class="align-middle">Diastol ID</th>
                                        <th class="align-middle">Temperature ID</th>
                                        <th class="align-middle">Procedure ID</th>
                                        <th class="align-middle">Composition ID</th>
                                        <th class="align-middle">Upload Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dataLog as $summary)
                                        <tr>
                                            <td>{{ $summary->noRawat }}</td>
                                            <td>{{ $summary->encounter_id }}</td>
                                            <td>{{ $summary->condition_id }}</td>
                                            <td>{{ $summary->condition2_id }}</td>
                                            <td>{{ $summary->heart_id }}</td>
                                            <td>{{ $summary->respiratory_id }}</td>
                                            <td>{{ $summary->systol_id }}</td>
                                            <td>{{ $summary->diastol_id }}</td>
                                            <td>{{ $summary->temperature_id }}</td>
                                            <td>{{ $summary->procedure_id }}</td>
                                            <td>{{ $summary->composition_id }}</td>
                                            <td>{{ $summary->created_at }}</td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                            {{-- </div> --}}
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
