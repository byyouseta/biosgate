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
        <div class="container-fluid">
            <div class="row">

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Data Pasien Kanker</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover" id="example2">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">No RM</th>
                                            <th class="align-middle">No Rawat</th>
                                            <th class="align-middle">Nama Pasien</th>
                                            <th class="align-middle">Kamar</th>
                                            <th class="align-middle">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $data)
                                            {{-- @if (\App\PelaporanCovid::cekLapor($data->no_rawat) == 0) --}}
                                            <tr>
                                                <td>{{ $data->no_rkm_medis }}</td>
                                                <td>{{ $data->no_rawat }}</td>
                                                <td>{{ $data->nm_pasien }}</td>
                                                <td>{{ $data->kd_kamar }} {{ $data->nm_bangsal }}</td>
                                                <td>
                                                    <div class="col text-center">
                                                        <div class="btn-group">
                                                            <a href="/rsonline/pasienbaru/add/{{ Crypt::encrypt($data->no_rawat) }}"
                                                                class="btn btn-success btn-sm @cannot('pasienbaru-create') disabled @endcannot"
                                                                data-toggle="tooltip" data-placement="bottom"
                                                                title="Tambah">
                                                                <i class="fas fa-plus-square"></i>
                                                            </a>

                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            {{-- @endif --}}
                                        @endforeach
                                    </tbody>
                                </table>

                            </div>
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
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": false,
                "scrollY": "400px",
                "scrollX": false,
            });
            $('#example').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": false,
                "ordering": false,
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
