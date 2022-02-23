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
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-sm-2 col-form-label">
                                    <a href="/layanan/bor/client" class="btn btn-success">Jalankan Client</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Jumlah Dokter Spesialis hari ini</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Tanggal Transaksi</th>
                                            <th class="align-middle">PNS</th>
                                            <th class="align-middle">Non PNS Tetap</th>
                                            <th class="align-middle">Kontrak</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <td>{{ $spesialis->tgl_transaksi }}</td>
                                        <td>{{ $spesialis->pns }}</td>
                                        <td>{{ $spesialis->non_pns_tetap }}</td>
                                        <td>{{ $spesialis->kontrak }}</td>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Jumlah Dokter Gigi hari ini</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Tanggal Transaksi</th>
                                            <th class="align-middle">PNS</th>
                                            <th class="align-middle">Non PNS Tetap</th>
                                            <th class="align-middle">Kontrak</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $drg->tgl_transaksi }}</td>
                                            <td>{{ $drg->pns }}</td>
                                            <td>{{ $drg->non_pns_tetap }}</td>
                                            <td>{{ $drg->kontrak }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Jumlah Dokter Umum hari ini</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Tanggal Transaksi</th>
                                            <th class="align-middle">PNS</th>
                                            <th class="align-middle">Non PNS Tetap</th>
                                            <th class="align-middle">Kontrak</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $umum->tgl_transaksi }}</td>
                                            <td>{{ $umum->pns }}</td>
                                            <td>{{ $umum->non_pns_tetap }}</td>
                                            <td>{{ $umum->kontrak }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Jumlah Perawat hari ini</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Tanggal Transaksi</th>
                                            <th class="align-middle">PNS</th>
                                            <th class="align-middle">Non PNS Tetap</th>
                                            <th class="align-middle">Kontrak</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $perawat->tgl_transaksi }}</td>
                                            <td>{{ $perawat->pns }}</td>
                                            <td>{{ $perawat->non_pns_tetap }}</td>
                                            <td>{{ $perawat->kontrak }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Jumlah Bidan hari ini</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Tanggal Transaksi</th>
                                            <th class="align-middle">PNS</th>
                                            <th class="align-middle">Non PNS Tetap</th>
                                            <th class="align-middle">Kontrak</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $bidan->tgl_transaksi }}</td>
                                            <td>{{ $bidan->pns }}</td>
                                            <td>{{ $bidan->non_pns_tetap }}</td>
                                            <td>{{ $bidan->kontrak }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Jumlah Pranata Laboratorium hari ini</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Tanggal Transaksi</th>
                                            <th class="align-middle">PNS</th>
                                            <th class="align-middle">Non PNS Tetap</th>
                                            <th class="align-middle">Kontrak</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $laborat->tgl_transaksi }}</td>
                                            <td>{{ $laborat->pns }}</td>
                                            <td>{{ $laborat->non_pns_tetap }}</td>
                                            <td>{{ $laborat->kontrak }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Jumlah Radiografer hari ini</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Tanggal Transaksi</th>
                                            <th class="align-middle">PNS</th>
                                            <th class="align-middle">Non PNS Tetap</th>
                                            <th class="align-middle">Kontrak</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $radio->tgl_transaksi }}</td>
                                            <td>{{ $radio->pns }}</td>
                                            <td>{{ $radio->non_pns_tetap }}</td>
                                            <td>{{ $radio->kontrak }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Jumlah Nutritionist hari ini</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Tanggal Transaksi</th>
                                            <th class="align-middle">PNS</th>
                                            <th class="align-middle">Non PNS Tetap</th>
                                            <th class="align-middle">Kontrak</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $nutrision->tgl_transaksi }}</td>
                                            <td>{{ $nutrision->pns }}</td>
                                            <td>{{ $nutrision->non_pns_tetap }}</td>
                                            <td>{{ $nutrision->kontrak }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Jumlah Fisioterapis hari ini</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Tanggal Transaksi</th>
                                            <th class="align-middle">PNS</th>
                                            <th class="align-middle">Non PNS Tetap</th>
                                            <th class="align-middle">Kontrak</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $fisio->tgl_transaksi }}</td>
                                            <td>{{ $fisio->pns }}</td>
                                            <td>{{ $fisio->non_pns_tetap }}</td>
                                            <td>{{ $fisio->kontrak }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Jumlah Pharmacist hari ini</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Tanggal Transaksi</th>
                                            <th class="align-middle">PNS</th>
                                            <th class="align-middle">Non PNS Tetap</th>
                                            <th class="align-middle">Kontrak</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $farmasi->tgl_transaksi }}</td>
                                            <td>{{ $farmasi->pns }}</td>
                                            <td>{{ $farmasi->non_pns_tetap }}</td>
                                            <td>{{ $farmasi->kontrak }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Jumlah Profesional Lain hari ini</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Tanggal Transaksi</th>
                                            <th class="align-middle">PNS</th>
                                            <th class="align-middle">Non PNS Tetap</th>
                                            <th class="align-middle">Kontrak</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $profesionallain->tgl_transaksi }}</td>
                                            <td>{{ $profesionallain->pns }}</td>
                                            <td>{{ $profesionallain->non_pns_tetap }}</td>
                                            <td>{{ $profesionallain->kontrak }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Jumlah Non Medis hari ini</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Tanggal Transaksi</th>
                                            <th class="align-middle">PNS</th>
                                            <th class="align-middle">Non PNS Tetap</th>
                                            <th class="align-middle">Kontrak</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $nonmedis->tgl_transaksi }}</td>
                                            <td>{{ $nonmedis->pns }}</td>
                                            <td>{{ $nonmedis->non_pns_tetap }}</td>
                                            <td>{{ $nonmedis->kontrak }}</td>
                                        </tr>
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
                "searching": false,
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
        });
        //Date picker
        $('#tanggal').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    </script>
@endsection
