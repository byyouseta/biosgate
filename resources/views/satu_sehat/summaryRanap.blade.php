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
            if (!empty(Request::get('awal'))) {
                $awal = Request::get('awal');
                $akhir = Request::get('akhir');
            } else {
                $awal = \Carbon\Carbon::now()->format('Y-m-d');
                $akhir = \Carbon\Carbon::now()->format('Y-m-d');
            }
        @endphp
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-hand-holding-medical"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Kunjungan IGD</span>
                            <span class="info-box-number">{{ $dataPasien->count() }}</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>

                <!-- /.col -->
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-paper-plane"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Terkirim Satu Sehat</span>
                            <span class="info-box-number">
                                <div id="terkirim"></div>
                            </span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-primary"><i class="fas fa-id-card"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">No ID IHS</span>
                            <span class="info-box-number">
                                <div id="tidak-ada-ihs"></div>
                            </span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-danger"><i class="fas fa-undo-alt"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Perlu diCheck</span>
                            <span class="info-box-number">
                                <div id="tidak-terkirim"></div>
                            </span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                @php
                    $terkirim = 0;
                    $tidakterkirim = 0;
                    $noihs = 0;
                @endphp
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Summary data terkirim
                                {{-- <a href="/satusehat/ranap/kirimencounter" class="btn btn-sm btn-primary"
                                    target="_blank">Encounter</a>
                                <a href="/satusehat/ranap/encounterupdate" class="btn btn-sm btn-primary"
                                    target="_blank">Update
                                    Encounter</a> --}}
                                <div class="float-right">
                                    <form action="/satusehat/ranap" method="GET">
                                        <div class="input-group input-group" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input" id="tanggal"
                                                data-target="#tanggal" data-toggle="datetimepicker" name="awal"
                                                autocomplete="off" value="{{ $awal }}" style="max-width: 120px;">
                                            <input type="text" class="form-control datetimepicker-input" id="tanggal2"
                                                data-target="#tanggal2" data-toggle="datetimepicker" name="akhir"
                                                autocomplete="off" value="{{ $akhir }}" style="max-width: 120px;">
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
                                        <th class="align-middle">Status Lanjut</th>
                                        <th class="align-middle">ID IHS</th>
                                        <th class="align-middle">Encounter ID</th>
                                        <th class="align-middle">Asesment Nadi</th>
                                        <th class="align-middle">Diagnosis Primer</th>
                                        <th class="align-middle">Kondisi Stabil</th>
                                        <th class="align-middle">Cara Keluar</th>
                                        <th class="align-middle">Created Time</th>
                                        <th class="align-middle">Updated Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dataPasien as $summary)
                                        <tr>
                                            <td>{{ $summary->no_rawat }}</td>
                                            <td>{{ $summary->nm_pasien }}</td>
                                            <td>{{ $summary->status_lanjut }}</td>
                                            <td>{{ $summary->idSehat }}</td>
                                            <td>{{ $summary->dataEncounter->encounter_id ?? '-' }}</td>
                                            <td>{{ $summary->dataEncounter->assesmen_nadi ?? '-' }}</td>
                                            <td>{{ $summary->dataEncounter->diagnosa_primer ?? '-' }}</td>
                                            <td>{{ $summary->dataEncounter->kondisi_stabil ?? '-' }}</td>
                                            <td>{{ $summary->dataEncounter->cara_keluar ?? '-' }}</td>
                                            <td>{{ $summary->dataEncounter->created_at ?? '-' }}</td>
                                            <td>{{ $summary->dataEncounter->updated_at ?? '-' }}</td>
                                        </tr>
                                        @php
                                            if ($summary->dataEncounter && $summary->dataEncounter->encounter_id) {
                                                ++$terkirim;
                                            } else {
                                                ++$tidakterkirim;
                                            }

                                            if (empty($summary->idSehat)) {
                                                ++$noihs;
                                            }
                                        @endphp
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
        var terkirim = <?php echo json_encode($terkirim); ?>;
        var tidakterkirim = <?php echo json_encode($tidakterkirim); ?>;
        var noihs = <?php echo json_encode($noihs); ?>;

        document.getElementById('terkirim').innerHTML = terkirim;
        document.getElementById('tidak-terkirim').innerHTML = tidakterkirim;
        document.getElementById('tidak-ada-ihs').innerHTML = noihs;
    </script>
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
        $('#tanggal, #tanggal2').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    </script>
@endsection
