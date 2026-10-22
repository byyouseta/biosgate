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
                {{-- <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-hand-holding-medical"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Summary Charge Item</span>
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
                @endphp --}}
                <div class="container-fluid">

                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="card_title">Summary Charge Item Satu Sehat

                                    <div class="d-flex justify-content-end align-items-end gap-2">

                                        {{-- Sync Semua --}}
                                        <form method="POST" action="{{ route('satuSehatCharge.sync-all') }}"
                                            onsubmit="return confirm('Sync semua kunjungan dari tanggal {{ $awal }} sampai {{ $akhir }}?')">

                                            @csrf
                                            <input type="hidden" name="awal" value="{{ $awal }}">
                                            <input type="hidden" name="akhir" value="{{ $akhir }}">

                                            <button type="submit" class="btn btn-primary mr-2">
                                                <i class="ti ti-refresh me-1"></i>
                                                Sync Semua
                                            </button>
                                        </form>

                                        {{-- Filter tanggal --}}
                                        <form action="/satusehat/charge" method="GET">

                                            <div class="input-group">
                                                <input type="text" class="form-control datetimepicker-input"
                                                    id="tanggal" data-target="#tanggal" data-toggle="datetimepicker"
                                                    name="awal" autocomplete="off" value="{{ $awal }}"
                                                    style="max-width: 120px;">

                                                <input type="text" class="form-control datetimepicker-input"
                                                    id="tanggal2" data-target="#tanggal2" data-toggle="datetimepicker"
                                                    name="akhir" autocomplete="off" value="{{ $akhir }}"
                                                    style="max-width: 120px;">
                                                <span class="input-group-append">
                                                    <button type="submit" class="btn btn-info btn-flat btn-sm">
                                                        <i class="fas fa-search"></i>
                                                        Tampilkan
                                                    </button>
                                                </span>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-hover table-sm display nowrap" id="example2">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">No Rawat</th>
                                            <th class="align-middle">Nama Pasien</th>
                                            <th class="align-middle">Poliklinik</th>
                                            <th class="align-middle">Status Pelayanan</th>
                                            <th class="align-middle">Status Lanjut</th>
                                            <th class="align-middle">Charge Item</th>
                                            <th class="text-center">
                                                Status
                                            </th>
                                            <th class="align-middle">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pasien as $summary)
                                            <tr>
                                                <td>{{ $summary->no_rawat }}</td>
                                                <td>{{ $summary->nm_pasien }}</td>
                                                <td>{{ $summary->nm_poli }}</td>
                                                <td>{{ $summary->stts }}</td>
                                                <td>{{ $summary->status_lanjut }}</td>
                                                <td class="text-center">
                                                    @if ($summary->charge_total > 0)
                                                        <span class="badge bg-info">
                                                            {{ $summary->charge_total }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">
                                                            0
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if ($summary->charge_total == 0)
                                                        <span class="badge bg-secondary">
                                                            Belum Sync
                                                        </span>
                                                    @elseif($summary->charge_failed > 0)
                                                        <span class="badge bg-danger">
                                                            Failed
                                                            ({{ $summary->charge_failed }})
                                                        </span>
                                                    @elseif($summary->charge_pending > 0)
                                                        <span class="badge bg-warning text-dark">
                                                            Pending
                                                            ({{ $summary->charge_pending }})
                                                        </span>
                                                    @elseif($summary->charge_sent == $summary->charge_total)
                                                        <span class="badge bg-success">
                                                            Sent
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">
                                                            Proses
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <form
                                                        action="{{ route('satuSehatCharge.sync', Crypt::encrypt($summary->no_rawat)) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf

                                                        <button type="submit" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-sync"></i>
                                                            Sync
                                                        </button>
                                                    </form>
                                                    <form
                                                        action="{{ route('satuSehatCharge.send', Crypt::encrypt($summary->no_rawat . '_' . $summary->status_lanjut)) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf

                                                        <button type="submit" class="btn btn-sm btn-info">
                                                            <i class="far fa-paper-plane"></i>
                                                            Send
                                                        </button>
                                                    </form>
                                                </td>
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
    {{-- <script src="{{ asset('template/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('template/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('template/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('template/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script> --}}
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
        $('#tanggal,#tanggal2').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    </script>
@endsection
