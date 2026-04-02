@extends('layouts.master')

@section('head')
    <meta http-equiv="refresh" content="300" />
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
                            <div class="card_title">{{ Session::get('anak') }}
                                <div class="float-right">
                                    <form action="/satusehat/radiologi" method="GET">
                                        <div class="input-group input-group" id="tanggal" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input"
                                                data-target="#tanggal" data-toggle="datetimepicker" name="tanggal"
                                                autocomplete="off" value="{{ $tanggal }}">
                                            <span class="input-group-append">
                                                <button type="submit" class="btn btn-info btn-flat btn-sm"><i
                                                        class="fas fa-search"></i> GO!</button>
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
                                        <th class="align-middle">Kode Periksa</th>
                                        <th class="align-middle">Nama Pemeriksaan</th>
                                        <th class="align-middle">Jenis Pelayanan</th>
                                        <th class="align-middle">Accession No</th>
                                        <th class="align-middle">No Order</th>
                                        <th class="align-middle">Encounter ID</th>
                                        <th class="align-middle">Service Request ID</th>
                                        <th class="align-middle">Study ID</th>
                                        <th class="align-middle">Observation ID</th>
                                        <th class="align-middle">Diagnostic Report ID</th>
                                        <th class="align-middle">Update Terakhir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dataPengunjung as $summary)
                                        @php
                                            $dataRonsen = $dataLog->where('no_order', $summary->noorder)->first();
                                        @endphp
                                        <tr>
                                            <td>{{ $summary->no_rawat }}</td>
                                            <td>{{ $summary->nm_pasien }}</td>
                                            <td>{{ $summary->kd_jenis_prw }}</td>
                                            <td>{{ $summary->nm_perawatan }}</td>
                                            <td>{{ $summary->status }}</td>
                                            <td>{{ $summary->ascension }}</td>
                                            <td>{{ $summary->noorder }}</td>
                                            {{-- <td>
                                                @if ($summary->status == 'ralan')
                                                    {{ $dataRonsen->responseSatuSehat->encounter_id ?? '-' }}
                                                @elseif($summary->status == 'ranap')
                                                    {{ $dataRonsen->responseRanapSatuSehat->encounter_id ?? '-' }}
                                                    @php
                                                        dd($dataRonsen->responseRanapSatuSehat);
                                                    @endphp
                                                @endif
                                            </td> --}}
                                            <td>{{ $dataRonsen->encounter_id ?? '-' }}</td>
                                            <td>{{ $dataRonsen->service_request_id ?? '-' }}</td>
                                            <td>{{ $dataRonsen->imaging_study_id ?? '-' }}</td>
                                            <td>{{ $dataRonsen->observation_id ?? '-' }}</td>
                                            <td>{{ $dataRonsen->diagnostic_report_id ?? '-' }}</td>
                                            <td>{{ $dataRonsen->updated_at ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{-- </div> --}}
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Log Error Pengiriman Data Radiologi
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-hover table-sm display nowrap" id="example">
                                <thead>
                                    <tr>
                                        <th class="align-middle">Subject</th>
                                        <th class="align-middle">Keterangan</th>
                                        <th class="align-middle">Created Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dataError as $log)
                                        <tr>
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
                "order": [
                    [6, 'desc']
                ],
                "info": true,
                "autoWidth": false,
                "responsive": false,
                "scrollY": false,
                "scrollX": true,
            });
            $('#example').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                // "order": [
                //     [6, 'desc']
                // ],
                "info": true,
                "autoWidth": false,
                "responsive": false,
                "scrollY": false,
                "scrollX": true,
            });
        });
        //Date picker
        $('#tanggal').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    </script>
@endsection
