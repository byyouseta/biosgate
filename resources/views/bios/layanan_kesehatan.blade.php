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
                        @if (Request::get('tanggal'))
                            @php
                                $tanggal = Request::get('tanggal');
                            @endphp
                        @else
                            @php
                                $tanggal = \Carbon\Carbon::now()
                                    ->locale('id')
                                    ->format('Y-m-d');
                            @endphp
                        @endif
                        <div class="card-body">
                            <form action="/layanan/kesehatan/lihat" method="GET">
                                <div class="form-group row">

                                    <div class="col-sm-1 col-form-label">
                                        <label>Tanggal</label>
                                    </div>
                                    <div class="col-sm-2 col-form-label">
                                        <div class="input-group date" id="tanggal" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input"
                                                data-target="#tanggal" data-toggle="datetimepicker" name="tanggal"
                                                value="{{ $tanggal }}" />
                                            <div class="input-group-append" data-target="#tanggal"
                                                data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-1 col-form-label">
                                        <button type="Submit" class="btn btn-primary btn-block">Lihat</button>
                                    </div>
                                    @can('facelift-kesehatan-client')
                                        <div class="col-sm-2 col-form-label">
                                            <a href="/layanan/kesehatan/client" class="btn btn-success" target="_blank">Jalankan
                                                Client</a>
                                        </div>
                                    @endcan
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Jumlah Pasien Rawat Inap</div>
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
                                            <tr>
                                                <td>{{ $data->tgl_transaksi }}</td>
                                                <td>{{ $data->kode_kelas }}</td>
                                                <td>{{ $data->jumlah }}</td>
                                            </tr>
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
                            <div class="card_title">Jumlah Pasien IGD</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Tanggal Transaksi</th>
                                            <th class="align-middle">Jumlah Pasien</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $igd->tgl_transaksi }}</td>
                                            <td>{{ $igd->jumlah }}</td>
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
                            <div class="card_title">Jumlah Layanan Laboratorium (Sample)</div>
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
                                            <td>{{ $labsample->tgl_transaksi }}</td>
                                            <td>{{ $labsample->jumlah }}</td>
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
                            <div class="card_title">Jumlah Layanan Laboratorium (Parameter)</div>
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
                                        @if (!empty($labparameter))
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
                                        @endif
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Jumlah Tindakan Operasi</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover" id="example3">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Tanggal Transaksi</th>
                                            <th class="align-middle">Klasifikasi Operasi</th>
                                            <th class="align-middle">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($operasi as $dataOperasi)
                                            <tr>
                                                <td>{{ $dataOperasi->tgl_transaksi }}</td>
                                                <td>{{ $dataOperasi->klasifikasi_operasi }}</td>
                                                <td>{{ $dataOperasi->jumlah }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Jumlah Layanan Radiologi</div>
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
                                            <td>{{ $radiologi->tgl_transaksi }}</td>
                                            <td>{{ $radiologi->jumlah }}</td>
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
                            <div class="card_title">Jumlah Pasien Rawat Jalan</div>
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
                                            <td>{{ $rajal->tgl_transaksi }}</td>
                                            <td>{{ $rajal->jumlah }}</td>
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
                            <div class="card_title">Jumlah Pasien Rawat Jalan/Poli</div>
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
                                        @forelse ($rajalpoli as $data)
                                            <tr>
                                                <td>{{ $data->tgl_transaksi }}</td>
                                                <td>{{ $data->nama_poli }}</td>
                                                <td>{{ $data->jumlah }}</td>
                                            </tr>
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
                            <div class="card_title">Jumlah Pasien BPJS dan non-BPJS</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>


                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th class="align-middle">Tanggal Transaksi</th>
                                            <td>{{ $bpjs->tgl_transaksi }}</td>
                                        </tr>
                                        <tr>
                                            <th class="align-middle">Jumlah Pasien BPJS</th>
                                            <td>{{ $bpjs->jumlah }}</td>
                                        </tr>
                                        <tr>
                                            <th class="align-middle">Jumlah Pasien NonBPJS</th>
                                            <td>{{ $nonbpjs->jumlah }}</td>
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
                            <div class="card_title">Jumlah Layanan Farmasi</div>
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
                                            <td>{{ $farmasi->tgl_transaksi }}</td>
                                            <td>{{ $farmasi->jumlah }}</td>
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
            $('#example3').DataTable({
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
