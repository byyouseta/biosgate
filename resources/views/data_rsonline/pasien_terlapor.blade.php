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
                            <div class="card_title">Data Pasien Covid</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover" id="example2">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">No RM</th>
                                            <th class="align-middle">Jenis Pasien</th>
                                            <th class="align-middle">Nama Pasien</th>
                                            <th class="align-middle">Status Rawat</th>
                                            <th class="align-middle">ID Lapor</th>
                                            <th class="align-middle">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $data)
                                            <tr>
                                                <td>{{ $data->noRm }}</td>
                                                <td>
                                                    @if ($data->jenis_pasien == 1)
                                                        Rawat Jalan
                                                    @elseif ($data->jenis_pasien == 2)
                                                        IGD
                                                    @elseif ($data->jenis_pasien == 3)
                                                        Rawat Inap
                                                    @endif
                                                </td>
                                                <td>{{ $data->namaPasien }}</td>
                                                <td>
                                                    @foreach ($statusrawat as $rawat)
                                                        @if ($rawat->id == $data->status_rawat)
                                                            {{ $rawat->nama }}
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td>{{ $data->lapId }}</td>
                                                <td>
                                                    <div class="col text-center">
                                                        <div class="btn-group">
                                                            <a href="/rsonline/pasienterlapor/editlap/{{ Crypt::encrypt($data->lapId) }}"
                                                                class="btn btn-warning @cannot('pasienbaru-edit') disabled @endcannot"
                                                                data-toggle="tooltip" data-placement="bottom" title="Edit">
                                                                <i class="fas fa-pen-square"></i>
                                                            </a>
                                                            <a href="/rsonline/pasienterlapor/laptambahan/{{ Crypt::encrypt($data->lapId) }}"
                                                                class="btn bg-maroon color-palette @cannot('laptambahan-list') disabled @endcannot"
                                                                data-toggle="tooltip" data-placement="bottom"
                                                                title="Laporan Tambahan">
                                                                <i class="fas fa-file-medical-alt"></i>
                                                            </a>
                                                            <a href="/rsonline/pasienterlapor/pulang/{{ Crypt::encrypt($data->lapId) }}"
                                                                class="btn bg-teal color-palette @cannot('pasienkeluar-create') disabled @endcannot"
                                                                data-toggle="tooltip" data-placement="bottom"
                                                                title="Laporan Status Keluar">
                                                                <i class="fas fa-walking"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
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
