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

        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Summary data terkirim

                            </div>
                        </div>
                        <div class="card-body">
                            {{-- <div style="overflow-x:auto;"> --}}
                            <table class="table table-bordered table-hover table-sm display nowrap" id="example2">
                                <thead>
                                    <tr>
                                        <th class="align-middle">Kode Booking</th>
                                        <th class="align-middle">Jenis Pasien</th>
                                        <th class="align-middle">NIK</th>
                                        <th class="align-middle">Kode Poli</th>
                                        <th class="align-middle">Nama Poli</th>
                                        <th class="align-middle">Pasien Baru</th>
                                        <th class="align-middle">no RM</th>
                                        <th class="align-middle">Tanggal Periksa</th>
                                        <th class="align-middle">Kode Dokter</th>
                                        <th class="align-middle">Nama Dokter</th>
                                        <th class="align-middle">Jenis Kunjungan</th>
                                        <th class="align-middle">No Referensi</th>
                                        <th class="align-middle">No Antrean</th>
                                        <th class="align-middle">Status</th>
                                        <th class="align-middle">Update terakhir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $summary)
                                        <tr>
                                            <td>{{ $summary->kodeBooking }}</td>
                                            <td>{{ $summary->jenisPasien }}</td>
                                            <td>{{ $summary->nik }}</td>
                                            <td>{{ $summary->kodePoli }}</td>
                                            <td>{{ $summary->namaPoli }}</td>
                                            <td>
                                                {{-- {{ $summary->pasienBaru }} --}}
                                                @if ($summary->pasienBaru == 1)
                                                    <span class='badge badge-info'>Baru</span>
                                                @else
                                                    <span class='badge badge-secondary'>Lama</span>
                                                @endif
                                            </td>
                                            <td>{{ $summary->noRm }}</td>
                                            <td>{{ $summary->tglPeriksa }}</td>
                                            <td>{{ $summary->kodeDokter }}</td>
                                            <td>{{ $summary->namaDokter }}</td>
                                            <td>
                                                {{-- {{ $summary->jenisKunjungan }} --}}
                                                @if ($summary->jenisKunjungan == 1)
                                                    1. Rujukan FKTP
                                                @elseif ($summary->jenisKunjungan == 3)
                                                    3. Kontrol
                                                @elseif ($summary->jenisKunjungan == 4)
                                                    4. Rujukan Antar RS
                                                @endif
                                            </td>
                                            <td>{{ $summary->nomorReferensi }}</td>
                                            <td>{{ $summary->nomorAntrean }}</td>
                                            <td>
                                                {{-- {{ $summary->statusKirim }} --}}
                                                @if ($summary->statusKirim == 1)
                                                    <span class='badge badge-success'>Sudah</span>
                                                @else
                                                    <span class='badge badge-danger'>Belum</span>
                                                @endif
                                            </td>
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
                            Task Info
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-hover table-sm display nowrap" id="example">
                                <thead>
                                    <tr>
                                        <th class="align-middle">No</th>
                                        <th class="align-middle">Kode Booking</th>
                                        <th class="align-middle">Task1</th>
                                        <th class="align-middle">Task2</th>
                                        <th class="align-middle">Task3</th>
                                        <th class="align-middle">Task4</th>
                                        <th class="align-middle">Task5</th>
                                        <th class="align-middle">Task6</th>
                                        <th class="align-middle">Task7</th>
                                        <th class="align-middle">Task99</th>
                                        <th class="align-middle">Status</th>
                                        <th class="align-middle">Update Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dataTask as $index => $log)
                                        <tr>
                                            <td class="text-center">{{ ++$index }}</td>
                                            <td>{{ $log->kodeBooking }}</td>
                                            <td>{{ $log->taskid1 }}</td>
                                            <td>{{ $log->taskid2 }}</td>
                                            <td>{{ $log->taskid3 != null ? \Carbon\Carbon::createFromTimestamp($log->taskid3)->toDateTimeString() : '' }}
                                            </td>
                                            <td>{{ $log->taskid4 != null ? \Carbon\Carbon::createFromTimestamp($log->taskid4)->toDateTimeString() : '' }}
                                            </td>
                                            <td>{{ $log->taskid5 != null ? \Carbon\Carbon::createFromTimestamp($log->taskid5)->toDateTimeString() : '' }}
                                            </td>
                                            <td>{{ $log->taskid6 != null ? \Carbon\Carbon::createFromTimestamp($log->taskid6)->toDateTimeString() : '' }}
                                            </td>
                                            <td>{{ $log->taskid7 != null ? \Carbon\Carbon::createFromTimestamp($log->taskid7)->toDateTimeString() : '' }}
                                            </td>
                                            <td>{{ $log->taskid99 != null ? \Carbon\Carbon::createFromTimestamp($log->taskid99)->toDateTimeString() : '0000-00-00 00:00:00' }}
                                            </td>
                                            <td>
                                                {{-- {{ $log->statusKirim }} --}}
                                                @if ($log->statusKirim == 1)
                                                    <span class='badge badge-success'>Sudah</span>
                                                @else
                                                    <span class='badge badge-danger'>Belum</span>
                                                @endif
                                            </td>
                                            <td>{{ $log->updated_at }}</td>
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
