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
                <div class="col-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Jumlah Pasien Rawat Inap hari ini</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Tanggal Transaksi</th>
                                            <th class="align-middle">Kode Kelas</th>
                                            <th class="align-middle">Jumlah Pasien</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($inap as $data)
                                            {{-- <tr>
                                                <td>{{ $data->tgl_transaksi }}</td>
                                                <td>{{ $data->kode_kelas }}</td>
                                                <td>{{ $data->jumlah }}</td>
                                            </tr> --}}
                                        @empty
                                            <tr>
                                                <td colspan="3">Data Kosong</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Jumlah Pasien Lab 1 (Sample) hari ini</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Tanggal Transaksi</th>
                                            <th class="align-middle">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            {{-- <td>{{ $labsample->tgl_transaksi }}</td>
                                            <td>{{ $labsample->jumlah }}</td> --}}
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
                            <div class="card_title">Jumlah Pasien Lab 2 (Parameter) hari ini</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover" id="example">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Tanggal Transaksi</th>
                                            <th class="align-middle">Nama Layanan</th>
                                            <th class="align-middle">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- @if (!empty($labparameter))
                                            @forelse ($labparameter as $data)
                                                <tr>
                                                    <td>{{ $data->tgl_transaksi }}</td>
                                                    <td>{{ $data->nama_layanan }}</td>
                                                    <td>{{ $data->jumlah }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3">Data Kosong</td>
                                                </tr>
                                            @endforelse
                                        @endif --}}
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Jumlah Pasien Operasi hari ini</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Tanggal Transaksi</th>
                                            <th class="align-middle">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            {{-- <td>{{ $operasi->tgl_transaksi }}</td>
                                            <td>{{ $operasi->jumlah }}</td> --}}
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
                            <div class="card_title">Jumlah Pasien Radiologi hari ini</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Tanggal Transaksi</th>
                                            <th class="align-middle">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            {{-- <td>{{ $radiologi->tgl_transaksi }}</td>
                                            <td>{{ $radiologi->jumlah }}</td> --}}
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
                            <div class="card_title">Jumlah Pasien Rawat Jalan hari ini</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Tanggal Transaksi</th>
                                            <th class="align-middle">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            {{-- <td>{{ $rajal->tgl_transaksi }}</td>
                                            <td>{{ $rajal->jumlah }}</td> --}}
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
                            <div class="card_title">Jumlah Pasien Rawat Jalan/Poli hari ini</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover" id="example2">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Tanggal Transaksi</th>
                                            <th class="align-middle">Nama Poli</th>
                                            <th class="align-middle">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- @forelse ($rajalpoli as $data)
                                            <tr>
                                                <td>{{ $data->tgl_transaksi }}</td>
                                                <td>{{ $data->nama_poli }}</td>
                                                <td>{{ $data->jumlah }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3">Data Kosong</td>
                                            </tr>
                                        @endforelse --}}
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Jumlah Pasien BPJS hari ini</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Tanggal Transaksi</th>
                                            <th class="align-middle">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            {{-- <td>{{ $bpjs->tgl_transaksi }}</td>
                                            <td>{{ $bpjs->jumlah }}</td> --}}
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
                            <div class="card_title">Jumlah Pasien NonBPJS hari ini</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Tanggal Transaksi</th>
                                            <th class="align-middle">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            {{-- <td>{{ $nonbpjs->tgl_transaksi }}</td>
                                            <td>{{ $nonbpjs->jumlah }}</td> --}}
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
