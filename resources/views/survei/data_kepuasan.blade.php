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
                            <div class="card_title">{{ Session::get('anak') }}
                                <div class="float-right">
                                    <form action="/survei/datakepuasan" method="GET">
                                        <div class="input-group input-group" id="tanggal" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input"
                                                data-target="#tanggal" data-toggle="datetimepicker" name="tanggal"
                                                autocomplete="off" value="{{ $tanggal }}">
                                            <span class="input-group-append">
                                                <button type="submit" class="btn btn-info btn-flat btn-sm"><i
                                                        class="fas fa-search"></i> Tampilkan</button>
                                            </span>
                                            @if (!empty(Request::get('tanggal')))
                                                <span class="input-group-prepend">
                                                    <a href="/survei/datakepuasan/{{ Crypt::encrypt(Request::get('tanggal')) }}/exportExcel"
                                                        class="btn btn-success btn-flat btn-sm pt-2" target="_blank"><i
                                                            class="fas fa-file-excel"></i> Excel
                                                    </a>
                                                </span>
                                            @endif
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- <div style="overflow-x:auto;"> --}}
                            <table class="table table-bordered table-hover table-sm display nowrap" id="example"
                                style="width:100%">

                                <thead>
                                    <tr>
                                        <th class="align-middle">No HP</th>
                                        <th class="align-middle">Umur</th>
                                        <th class="align-middle">Pendidikan</th>
                                        <th class="align-middle">Pekerjaan</th>
                                        <th class="align-middle">Penjamin</th>
                                        <th class="align-middle">Unit</th>
                                        <th class="align-middle">Saran</th>
                                        <th class="align-middle">Waktu Pelaporan</th>
                                        <th class="align-middle">Akses</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $summary)
                                        @php
                                            //Ambil data untuk Bukti Pelayanan
                                            if ($summary->pendidikan == 1) {
                                                $pendidikan = 'SD';
                                            } elseif ($summary->pendidikan == 2) {
                                                $pendidikan = 'SLTP';
                                            } elseif ($summary->pendidikan == 3) {
                                                $pendidikan = 'SLTA';
                                            } elseif ($summary->pendidikan == 4) {
                                                $pendidikan = 'D1-D2-D3';
                                            } elseif ($summary->pendidikan == 5) {
                                                $pendidikan = 'D4-S1';
                                            } elseif ($summary->pendidikan == 6) {
                                                $pendidikan = 'S2 ke atas';
                                            }

                                            if ($summary->pekerjaan == 1) {
                                                $pekerjaan = 'PNS/TNI/POLRI';
                                            } elseif ($summary->pekerjaan == 2) {
                                                $pekerjaan = 'Pegawai Swasta';
                                            } elseif ($summary->pekerjaan == 3) {
                                                $pekerjaan = 'Wiraswasta/ Usahawan';
                                            } elseif ($summary->pekerjaan == 4) {
                                                $pekerjaan = 'Pelajar/Mahasiswa';
                                            } elseif ($summary->pekerjaan == 5) {
                                                $pekerjaan = 'Lainnya';
                                            }

                                            if ($summary->penjamin == 1) {
                                                $penjamin = 'BPJS';
                                            } elseif ($summary->penjamin == 2) {
                                                $penjamin = 'Asuransi';
                                            } elseif ($summary->penjamin == 3) {
                                                $penjamin = 'Tanggungan Pribadi';
                                            }

                                            if ($summary->unit == 1) {
                                                $unit = 'Rawat Jalan';
                                            } elseif ($summary->unit == 2) {
                                                $unit = 'Rawat Inap';
                                            } elseif ($summary->unit == 3) {
                                                $unit = 'IGD';
                                            } elseif ($summary->unit == 4) {
                                                $unit = 'Farmasi';
                                            } elseif ($summary->unit == 5) {
                                                $unit = 'Laboratorium';
                                            } elseif ($summary->unit == 6) {
                                                $unit = 'Radiologi';
                                            } elseif ($summary->unit == 7) {
                                                $unit = 'ICU; NICU; PICU';
                                            } elseif ($summary->unit == 8) {
                                                $unit = 'Pemulasaran Jenazah';
                                            } elseif ($summary->unit == 9) {
                                                $unit = 'Rehabilitasi medik';
                                            } elseif ($summary->unit == 10) {
                                                $unit = 'Radioterapi';
                                            } elseif ($summary->unit == 11) {
                                                $unit = 'Jantung';
                                            }

                                        @endphp
                                        <tr>
                                            <td>{{ $summary->no_hp }}</td>
                                            <td>{{ $summary->umur }}</td>
                                            <td class="align-middle">{{ $pendidikan != null ? $pendidikan : '' }}
                                            </td>
                                            <td class="align-middle">{{ $pekerjaan != null ? $pekerjaan : '' }}
                                            </td>
                                            <td class="align-middle">{{ $penjamin != null ? $penjamin : '' }} </td>
                                            <td class="align-middle">{{ $unit != null ? $unit : '' }} </td>
                                            <td>{{ \App\Kepuasan::createPreview($summary->saran, 100) }}</td>
                                            <td>{{ $summary->created_at }}</td>
                                            <td>
                                                <div class="col text-center">
                                                    <div class="btn-group">
                                                        <a class="btn btn-sm btn-success" data-toggle="tooltip"
                                                            data-placement="bottom" title="Detail"
                                                            href="/survei/datakepuasan/{{ Crypt::encrypt($summary->id) }}/detail">
                                                            <i class="fas fa-check-double"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
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
            // $('#example2').DataTable({
            //     "paging": true,
            //     "lengthChange": false,
            //     "searching": true,
            //     "ordering": true,
            //     "order": [
            //         [5, 'desc']
            //     ],
            //     "info": true,
            //     "autoWidth": false,
            //     "responsive": false,
            //     "scrollY": false,
            //     "scrollX": true,
            //     "buttons": ["copy", "excel", "pdf", "colvis"]
            // }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
            $('#example').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "order": [
                    [7, 'desc']
                ],
                "info": true,
                "autoWidth": true,
                "fixedHeader": true,
                "responsive": false,
                // "scrollY": "300px",
                "scrollX": true,
            });
        });
        //Date picker
        $('#tanggal').datetimepicker({
            format: 'YYYY-MM'
        });
    </script>
@endsection
