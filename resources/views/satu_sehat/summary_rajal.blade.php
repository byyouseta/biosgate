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
        @php
            if (!empty(Request::get('tanggal_awal'))) {
                $tanggal_awal = Request::get('tanggal_awal');
                $tanggal_akhir = Request::get('tanggal_akhir');
            } else {
                $tanggal_awal = \Carbon\Carbon::now()->format('Y-m-d');
                $tanggal_akhir = \Carbon\Carbon::now()->format('Y-m-d');
            }
        @endphp
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-hand-holding-medical"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Kunjungan Rawat Jalan</span>
                            <span class="info-box-number">{{ $dataLog->count() }}</span>
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
                 <div class="col-md-2 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-secondary"><i class="fas fa-times"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Batal Kunjungan</span>
                            <span class="info-box-number">
                                <div>{{ $dataLog->where('stts', 'Batal')->count() }}</div>
                            </span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-md-2 col-sm-6 col-12">
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
                <div class="col-md-2 col-sm-6 col-12">
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
                <!-- /.col -->
                @php
                    $terkirim = 0;
                    $tidakterkirim = 0;
                    $noihs = 0;
                @endphp
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Summary Cek data terkirim
                                <div class="float-right">
                                    <form action="/satusehat/cek" method="GET">
                                        <div class="input-group input-group" >
                                            <input type="text" class="form-control datetimepicker-input w-10" id="tanggal_awal" data-target-input="nearest"
                                                data-target="#tanggal_awal" data-toggle="datetimepicker" name="tanggal_awal"
                                                autocomplete="off" value="{{ $tanggal_awal }}">
                                            <input type="text" class="form-control datetimepicker-input" id="tanggal_akhir" data-target-input="nearest"
                                                data-target="#tanggal_akhir" data-toggle="datetimepicker" name="tanggal_akhir"
                                                autocomplete="off" value="{{ $tanggal_akhir }}" style="width:30px">
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
                                        {{-- <th class="align-middle">No KTP</th> --}}
                                        <th class="align-middle">Id IHS</th>
                                        <th class="align-middle">Nama Dokter</th>
                                        <th class="align-middle">Penjamin</th>
                                        <th class="align-middle">Poliklinik</th>
                                        <th class="align-middle">Status Pelayanan</th>
                                        <th class="align-middle">Encounter ID</th>
                                        {{-- <th class="align-middle">Diastol ID</th>
                                        <th class="align-middle">Temperature ID</th>
                                        <th class="align-middle">Procedure ID</th>
                                        <th class="align-middle">Composition ID</th> --}}
                                        <th class="align-middle">Upload Time</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($dataLog as $summary)
                                        <tr>
                                            <td>{{ $summary->no_rawat }}</td>
                                            <td>{{ $summary->nm_pasien }}</td>
                                            {{-- <td>{{ $summary->ktp_pasien }}</td> --}}
                                            <td>
                                                @if ($summary->ktp_pasien)
                                                    @php
                                                        $idSehat = \App\PasienSehat::getIdSehat($summary->ktp_pasien);
                                                    @endphp
                                                    @if ($idSehat)
                                                        {{ $idSehat }}
                                                    @else
                                                        @php
                                                            ++$noihs;
                                                        @endphp
                                                    @endif
                                                @endif
                                            </td>
                                            <td>{{ $summary->nama_dokter }}</td>
                                            <td>{{ $summary->png_jawab }}</td>
                                            <td>{{ $summary->nm_poli }}</td>
                                            <td class="text-center">
                                                @if ($summary->stts == 'Sudah')
                                                    <span class="badge badge-success badge-sm">{{ $summary->stts }}</span>
                                                @elseif($summary->stts == 'Berkas Lengkap')
                                                    <span class="badge badge-warning badge-sm">{{ $summary->stts }}</span>
                                                @elseif($summary->stts == 'Belum')
                                                    <span class="badge badge-info badge-sm">{{ $summary->stts }}</span>
                                                @elseif($summary->stts == 'Batal')
                                                    <span
                                                        class="badge badge-secondary badge-sm">{{ $summary->stts }}</span>
                                                @else
                                                <span class="badge badge-danger badge-sm">
                                                    {{ $summary->stts }}</span>
                                                @endif
                                            </td>
                                            @php
                                                $dataEncounter = \App\ResponseSatuSehat::getEncounter(
                                                    $summary->no_rawat
                                                );
                                            @endphp
                                            <td>
                                                @if ($dataEncounter)
                                                    @php
                                                        ++$terkirim;
                                                    @endphp
                                                    {{ $dataEncounter->encounter_id }}
                                                @else
                                                    @php
                                                        ++$tidakterkirim;
                                                    @endphp
                                                    <a href="{{ route('satuSehat.checkRajalDetail', Crypt::encrypt($summary->no_rawat)) }}"
                                                        class='badge badge-info badge-sm' target="_blank">Check</a>
                                                @endif
                                            </td>
                                            <td>{{ $dataEncounter ? $dataEncounter->created_at : '--' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            {{--
                        </div> --}}
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
        $('#tanggal_awal,#tanggal_akhir').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    </script>
@endsection
