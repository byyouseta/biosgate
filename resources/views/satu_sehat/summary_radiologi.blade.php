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
            if (!empty(Request::get('tanggal_awal'))) {
                $tanggal_awal = Request::get('tanggal_awal');
                $tanggal_akhir = Request::get('tanggal_akhir');
            } else {
                $tanggal_awal = \Carbon\Carbon::now()->format('Y-m-d');
                $tanggal_akhir = \Carbon\Carbon::now()->format('Y-m-d');
            }
        @endphp
        @php
            $permintaan = $dataPengunjung->unique('noorder')->count();
            $terkirim = 0;
            $tidakterkirim = 0;
            $noihs = 0;
        @endphp
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-hand-holding-medical"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Jumlah Permintaan</span>
                            <span class="info-box-number">{{ $permintaan }}</span>
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
                                <div id="terkirim">{{ $dataLog->where('service_request_id', '!=', null)->count() }}</div>
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
                                <div id="tidak-ada-ihs">{{ $dataPengunjung->where('idSehat', null)->count() }}</div>
                            </span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-secondary"><i class="fas fa-percentage"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Ketercapaian</span>
                            <span class="info-box-number">
                                <div id="ketercapaian">
                                    @if ($permintaan > 0)
                                        {{ number_format((($dataLog->where('idSehat', null)->count() + $dataPengunjung->where('idSehat', null)->count()) / $permintaan) * 100, 2, '.', '') }}%
                                    @else
                                        0.00%
                                    @endif
                                </div>
                            </span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <div class="col-12">
                    <!-- /.col -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">{{ Session::get('anak') }}
                                <div class="float-right">
                                    <form action="/satusehat/radiologi/summary" method="GET">
                                        <div class="input-group input-group">
                                            <input type="text" class="form-control datetimepicker-input w-10"
                                                id="tanggal_awal" data-target-input="nearest" data-target="#tanggal_awal"
                                                data-toggle="datetimepicker" name="tanggal_awal" autocomplete="off"
                                                value="{{ $tanggal_awal }}">
                                            <input type="text" class="form-control datetimepicker-input"
                                                id="tanggal_akhir" data-target-input="nearest" data-target="#tanggal_akhir"
                                                data-toggle="datetimepicker" name="tanggal_akhir" autocomplete="off"
                                                value="{{ $tanggal_akhir }}" style="width:30px">
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
                                        <th class="align-middle">ID IHS</th>
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
                                        {{-- <th class="align-middle">Update Terakhir</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dataPengunjung as $summary)
                                        @if (empty($summary->idSehat))
                                            @php
                                                ++$noihs;
                                            @endphp
                                        @endif
                                        <tr>
                                            <td>{{ $summary->no_rawat }}</td>
                                            <td>{{ $summary->nm_pasien }}</td>
                                            <td>{{ $summary->idSehat }}</td>
                                            <td>{{ $summary->kd_jenis_prw }}</td>
                                            <td>{{ $summary->nm_perawatan }}</td>
                                            <td>{{ $summary->status }}</td>
                                            <td>{{ $summary->ascension }}</td>
                                            <td>{{ $summary->noorder }}</td>
                                            <td>{{ $summary->dataResponse->encounter_id ?? '-' }} </td>
                                            <td>{{ $summary->dataResponse->service_request_id ?? '-' }}</td>
                                            <td>{{ $summary->dataResponse->imaging_study_id ?? '-' }}</td>
                                            <td>{{ $summary->dataResponse->observation_id ?? '-' }}</td>
                                            <td>{{ $summary->dataResponse->diagnostic_report_id ?? '-' }}</td>
                                            {{-- <td>{{ $dataRonsen->updated_at ?? '-' }}</td> --}}
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
        var terkirim = <?php echo json_encode($terkirim); ?>;
        var permintaan = <?php echo json_encode($permintaan); ?>;
        var noihs = <?php echo json_encode($noihs); ?>;

        document.getElementById('permintaan').innerHTML = permintaan;
        document.getElementById('terkirim').innerHTML = terkirim;
        document.getElementById('tidak-ada-ihs').innerHTML = noihs;
    </script>
    <script>
        $(function() {
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": true,
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
        $('#tanggal_awal,#tanggal_akhir').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    </script>
@endsection
