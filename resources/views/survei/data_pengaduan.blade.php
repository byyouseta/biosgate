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
                                    <a href="/survei/datapengaduan/exportExcel" class="btn btn-success btn-sm"
                                        target="_blank">EXPORT
                                        EXCEL</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- <div style="overflow-x:auto;"> --}}
                            <table class="table table-bordered table-hover table-sm display nowrap" id="example"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="align-middle">Nama</th>
                                        <th class="align-middle">No HP</th>
                                        {{-- <th class="align-middle">Email</th> --}}
                                        <th class="align-middle">Waktu Kejadian</th>
                                        <th class="align-middle">Tempat Kejadian</th>
                                        <th class="align-middle">Deskripsi</th>
                                        <th class="align-middle">Waktu Pelaporan</th>
                                        <th class="align-middle">Status</th>
                                        <th class="align-middle">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $summary)
                                        <tr>
                                            <td>{{ $summary->nama }}</td>
                                            <td>{{ $summary->no_hp }}</td>
                                            {{-- <td>{{ $summary->email }}</td> --}}
                                            <td>{{ $summary->waktu_kejadian }}</td>
                                            <td>{{ $summary->tempat_kejadian }}</td>
                                            <td>{{ substr($summary->deskripsi, 0, 25) }}</td>
                                            <td>{{ $summary->created_at }}</td>
                                            <td>
                                                @if ($summary->status_keluhan_id == '0')
                                                    <span class="badge badge-danger">Pelaporan</span>
                                                @elseif ($summary->status_keluhan_id == '1')
                                                    <span class="badge badge-warning">Proses</span>
                                                @elseif ($summary->status_keluhan_id == '2')
                                                    <span class="badge badge-success">Selesai</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="col text-center">
                                                    <div class="btn-group">
                                                        <a class="btn btn-sm btn-success" data-toggle="tooltip"
                                                            data-placement="bottom" title="Detail"
                                                            href="/survei/datapengaduan/{{ Crypt::encrypt($summary->id) }}/detail">
                                                            <i class="fas fa-check-double"></i>
                                                        </a>
                                                        {{-- <a class="btn btn-sm btn-danger delete-confirm @cannot('survei-delete') disabled @endcannot"
                                                            data-toggle="tooltip" data-placement="bottom" title="Hapus"
                                                            href="/survei/datapengaduan/{{ Crypt::encrypt($summary->id) }}/delete">
                                                            <i class="fas fa-times-circle"></i>
                                                        </a> --}}
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
                    [6, 'desc']
                ],
                "info": false,
                "autoWidth": true,
                "fixedHeader": true,
                "responsive": false,
                // "scrollY": "300px",
                "scrollX": false,
            });
        });
        //Date picker
        $('#tanggal').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    </script>
@endsection
