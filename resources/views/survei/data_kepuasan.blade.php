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
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- <div style="overflow-x:auto;"> --}}
                            <table class="table table-bordered table-hover table-sm display nowrap" id="example2">
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
                                        <th class="align-middle">Akse</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $summary)
                                        <tr>
                                            <td>{{ $summary->no_hp }}</td>
                                            <td>{{ $summary->umur }}</td>
                                            <td>{{ $summary->pendidikan }}</td>
                                            <td>{{ $summary->pekerjaan }}</td>
                                            <td>{{ $summary->penjamin }}</td>
                                            <td>{{ $summary->unit }}</td>
                                            <td>{{ $summary->saran }}</td>
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
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "order": [
                    [7, 'desc']
                ],
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
                "order": [
                    [7, 'desc']
                ],
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
