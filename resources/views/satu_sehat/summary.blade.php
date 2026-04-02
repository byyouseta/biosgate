@extends('layouts.master')

@section('head')
    <meta http-equiv="refresh" content="120" />
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
                                <div class="float-right">
                                    <form action="{{ url()->current() }}" method="GET">
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
                                        <th class="align-middle">Nama Pasien</th>
                                        <th class="align-middle">Poliklinik</th>
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
                                        <th class="align-middle">Goal ID</th>
                                        <th class="align-middle">CarePlan ID</th>
                                        <th class="align-middle">Upload Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dataPasien as $summary)
                                        @php
                                            $hasil = $dataLog->where('noRawat', $summary->no_rawat)->first();
                                        @endphp
                                        <tr>
                                            <td>{{ $summary->no_rawat }}</td>
                                            <td>{{ $summary->nm_pasien }}</td>
                                            <td>{{ $summary->alias_nm_poli }}</td>
                                            <td>{{ $hasil ? $hasil->encounter_id : '-' }}</td>
                                            <td>{{ $hasil ? $hasil->condition_id : '-' }}</td>
                                            <td>{{ $hasil ? $hasil->condition2_id : '-' }}</td>
                                            <td>{{ $hasil ? $hasil->heart_id : '-' }}</td>
                                            <td>{{ $hasil ? $hasil->respiratory_id : '-' }}</td>
                                            <td>{{ $hasil ? $hasil->systol_id : '-' }}</td>
                                            <td>{{ $hasil ? $hasil->diastol_id : '-' }}</td>
                                            <td>{{ $hasil ? $hasil->temperature_id : '-' }}</td>
                                            <td>{{ $hasil ? $hasil->procedure_id : '-' }}</td>
                                            <td>{{ $hasil ? $hasil->composition_id : '-' }}</td>
                                            <td>{{ $hasil ? $hasil->goal_id : '-' }}</td>
                                            <td>{{ $hasil ? $hasil->careplan_id : '-' }}</td>
                                            <td>{{ $hasil ? $hasil->created_at : '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{--
                        </div> --}}
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
