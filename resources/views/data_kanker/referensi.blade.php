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
                <div class="col-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Referensi Cara Masuk</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Kode</th>
                                            <th class="align-middle">Cara Masuk</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($caraMasuk as $caraMasuk)
                                            <tr>
                                                <td>{{ $caraMasuk->kode_cara_masuk_pasien }}</td>
                                                <td>{{ $caraMasuk->cara_masuk_pasien }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Referensi Asal Rujukan</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover" id="example">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Kode</th>
                                            <th class="align-middle">Jenis Asal Rujukan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($asalRujukan as $asalRujukan)
                                            <tr>
                                                <td>{{ $asalRujukan->kode_asal_rujukan_pasien }}</td>
                                                <td>{{ $asalRujukan->asal_rujukan_pasien }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Referensi Instalasi</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Kode</th>
                                            <th class="align-middle">Nama Instalasi Unit</th>
                                            {{-- <th class="align-middle">Provinsi ID</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($instalasi as $instalasi)
                                            <tr>
                                                <td>{{ $instalasi->kode_instalasi_unit }}</td>
                                                <td>{{ $instalasi->instalasi_unit }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Referensi Sub Instalasi</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover" id="example4">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Kode Instalasi</th>
                                            <th class="align-middle">Kode SubInstalasi</th>
                                            <th class="align-middle">Nama Sub Instalasi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($subinstalasi as $subinstalasi)
                                            <tr>
                                                <td>{{ $subinstalasi->kode_instalasi_unit }}</td>
                                                <td>{{ $subinstalasi->kode_gabung_sub_instalasi_unit }}</td>
                                                <td>{{ $subinstalasi->sub_instalasi_unit }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Referensi Cara Keluar</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover" id="example5">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Kode</th>
                                            <th class="align-middle">Nama Cara Keluar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($caraKeluar as $Keluar)
                                            <tr>
                                                <td>{{ $Keluar->kode_cara_keluar }}</td>
                                                <td>{{ $Keluar->cara_keluar }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Referensi Keadaan Keluar</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover" id="example5">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Kode</th>
                                            <th class="align-middle">Nama Keadaan Keluar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($keadaanKeluar as $Keluar)
                                            <tr>
                                                <td>{{ $Keluar->kode_keadaan_keluar }}</td>
                                                <td>{{ $Keluar->keadaan_keluar }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Referensi Cara Bayar</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover" id="example5">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Kode</th>
                                            <th class="align-middle">Nama Cara Bayar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($caraBayar as $bayar)
                                            <tr>
                                                <td>{{ $bayar->kode_cara_bayar }}</td>
                                                <td>{{ $bayar->cara_bayar }}</td>
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
                "paging": false,
                "lengthChange": false,
                "searching": true,
                "ordering": false,
                "info": false,
                "autoWidth": false,
                "responsive": false,
                "scrollY": "300px",
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
            $('#example3').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": true,
                "ordering": false,
                "info": false,
                "autoWidth": false,
                "responsive": false,
                "scrollY": "300px",
                "scrollX": false,
            });

            $('#example4').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": false,
                "autoWidth": false,
                "responsive": false,
                "scrollY": "300px",
                "scrollX": false,
            });
            $('#example5').DataTable({
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
