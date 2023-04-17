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
                            <div class="card_title">Data Saldo Operasional</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Kode Bank</th>
                                            <th class="align-middle">Nama Bank</th>
                                            <th class="align-middle">No Rekening</th>
                                            <th class="align-middle">Saldo</th>
                                            <th class="align-middle">Kode Rekening</th>
                                            <th class="align-middle">Tanggal Transaksi</th>
                                            <th class="align-middle">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($operasional as $data)
                                            <tr>
                                                <td>{{ $data->bank->kd_bank }}</td>
                                                <td>{{ $data->bank->nama }}</td>
                                                <td>{{ $data->bank->norek }}</td>
                                                <td>{{ number_format($data->saldo_akhir, 2, ',', '.') }}</td>
                                                <td>{{ $data->bank->Rekening->kode }}</td>
                                                <td>{{ $data->tgl_transaksi }}</td>
                                                <td>
                                                    @if ($data->status == 1)
                                                        <span class="right badge badge-success">Sudah Terkirim</span>
                                                    @else
                                                        <span class="right badge badge-danger">Belum Terkirim</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7">Data Pengiriman kemarin Kosong
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Data Saldo Pengelolaan Kas</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Kode Bank</th>
                                            <th class="align-middle">Nama Bank</th>
                                            <th class="align-middle">No Rekening</th>
                                            <th class="align-middle">Nilai Deposito</th>
                                            <th class="align-middle">Nilai Bunga</th>
                                            <th class="align-middle">Tanggal Transaksi</th>
                                            <th class="align-middle">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($pengelolaan as $data)
                                            <tr>
                                                <td>{{ $data->bank->kd_bank }}</td>
                                                <td>{{ $data->bank->nama }}</td>
                                                <td>{{ $data->bank->norek }}</td>
                                                <td>{{ number_format($data->nilai_deposito, 2, ',', '.') }}</td>
                                                <td>{{ number_format($data->nilai_bunga, 2, ',', '.') }}</td>
                                                <td>{{ $data->tgl_transaksi }}</td>
                                                <td>
                                                    @if ($data->status == 1)
                                                        <span class="right badge badge-success">Sudah Terkirim</span>
                                                    @else
                                                        <span class="right badge badge-danger">Belum Terkirim</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7">Data Pengiriman kemarin Kosong
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Data Saldo Dana Kelolaan</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Kode Bank</th>
                                            <th class="align-middle">Nama Bank</th>
                                            <th class="align-middle">No Rekening</th>
                                            <th class="align-middle">Saldo Akhir</th>
                                            <th class="align-middle">Tanggal Transaksi</th>
                                            <th class="align-middle">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($kelolaan as $data)
                                            <tr>
                                                <td>{{ $data->bank->kd_bank }}</td>
                                                <td>{{ $data->bank->nama }}</td>
                                                <td>{{ $data->bank->norek }}</td>
                                                <td>{{ number_format($data->saldo, 2, ',', '.') }}</td>
                                                <td>{{ $data->tgl_transaksi }}</td>
                                                <td>
                                                    @if ($data->status == 1)
                                                        <span class="right badge badge-success">Sudah Terkirim</span>
                                                    @else
                                                        <span class="right badge badge-danger">Belum Terkirim</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6">Data Pengiriman kemarin Kosong
                                                </td>
                                            </tr>
                                        @endforelse
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
