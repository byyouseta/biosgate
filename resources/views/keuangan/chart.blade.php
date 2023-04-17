@extends('layouts.master')

@section('head')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('template/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- Tempusdominus|Datetime Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('template/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('template/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
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
                        @php
                            $kemarin = \Carbon\Carbon::parse($tanggal)
                                ->yesterday()
                                ->format('Y-m-d');
                        @endphp
                        <div class="card-body">
                            <form action="/saldo/laporan" method="GET">
                                <div class="form-group row">

                                    <div class="col-sm-1 col-form-label">
                                        <label>Tanggal</label>
                                    </div>
                                    <div class="col-sm-2 col-form-label">
                                        <div class="input-group date" id="tanggal" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input"
                                                data-target="#tanggal" data-toggle="datetimepicker" name="tanggal"
                                                value="{{ $tanggal }}" autocomplete="off" />
                                            <div class="input-group-append" data-target="#tanggal"
                                                data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-1 col-form-label">
                                        <button type="Submit" class="btn btn-primary btn-block">Lihat</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                {{-- <div class="col-md-6">
                    <!-- AREA CHART -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Area Chart</h3>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart">
                                <canvas id="areaChart"
                                    style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div> --}}
                <!-- /.col (LEFT) -->
                <div class="col-md-12">


                    {{-- <!-- BAR CHART -->
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Bar Chart</h3>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart">
                                <canvas id="barChart"
                                    style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div> --}}
                    <!-- /.card -->
                    <div class="card">
                        <div class="card-body">
                            <canvas id="myChart"
                                style="min-height: 250px; height: 250px; max-height: 500px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <canvas id="myChartBulanan"
                                style="min-height: 250px; height: 250px; max-height: 500px; max-width: 100%;"></canvas>
                        </div>
                    </div>

                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <canvas id="mySaldoHarian"
                                style="min-height: 250px; height: 250px; max-height: 500px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>

            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    {{-- Modal Add --}}
@endsection
@section('plugin')
    <!-- ChartJS -->
    {{-- <script src="{{ asset('template/plugins/chart.js/Chart.min.js') }}"></script> --}}
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
    <!-- Select2 -->
    <script src="{{ asset('template/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script type="text/javascript">
        var labels = <?php echo json_encode($labels); ?>;
        var labelsBulan = <?php echo json_encode($labelsBulan); ?>;
        var pemasukan = <?php echo json_encode($dataPemasukan); ?>;
        var pemasukanBulan = <?php echo json_encode($pemasukanBulan); ?>;
        var pengeluaran = <?php echo json_encode($dataPengeluaran); ?>;
        var pengeluaranBulan = <?php echo json_encode($pengeluaranBulan); ?>;
        var operasional = <?php echo json_encode($operasional); ?>;
        var pengelolaanDepo = <?php echo json_encode($pengelolaanDepo); ?>;
        var pengelolaanBunga = <?php echo json_encode($pengelolaanBunga); ?>;
        var danaKelola = <?php echo json_encode($danaKelola); ?>;

        const data = {
            labels: labels,
            datasets: [{
                label: 'Penerimaan Keuangan',
                backgroundColor: 'rgb(102, 153, 255)',
                borderColor: 'rgb(102, 153, 255)',
                data: pemasukan,
            }, {
                label: 'Pengeluaran Keuangan',
                backgroundColor: 'rgb(255, 102, 153)',
                borderColor: 'rgb(255, 99, 132)',
                data: pengeluaran,
            }, ]
        };

        const config = {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Penerimaan vs Pengeluaran Harian'
                    }
                }
            }
        };

        const data2 = {
            labels: labelsBulan,
            datasets: [{
                label: 'Penerimaan Keuangan',
                backgroundColor: 'rgb(102, 153, 255)',
                borderColor: 'rgb(102, 153, 255)',
                data: pemasukanBulan,
            }, {
                label: 'Pengeluaran Keuangan',
                backgroundColor: 'rgb(255, 102, 153)',
                borderColor: 'rgb(255, 99, 132)',
                data: pengeluaranBulan,
            }, ]
        };

        const config2 = {
            type: 'bar',
            data: data2,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Penerimaan vs Pengeluaran Bulanan'
                    }
                }
            }
        };

        const data3 = {
            labels: labels,
            datasets: [{
                label: 'Saldo Operasional',
                backgroundColor: 'rgb(102, 153, 255)',
                borderColor: 'rgb(102, 153, 255)',
                data: operasional,
            }, {
                label: 'Saldo Pengelolaan Kas Deposite',
                backgroundColor: 'rgb(255, 102, 153)',
                borderColor: 'rgb(255, 99, 132)',
                data: pengelolaanDepo,
            }, {
                label: 'Saldo Pengelolaan Kas Bunga',
                backgroundColor: 'rgb(255, 255, 0)',
                borderColor: 'rgb(255, 255, 102)',
                data: pengelolaanBunga,
            }, {
                label: 'Saldo Dana Kelolaan',
                backgroundColor: 'rgb(0, 204, 153)',
                borderColor: 'rgb(0, 255, 204)',
                data: danaKelola,
            }, ]
        };

        const config3 = {
            type: 'bar',
            data: data3,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Saldo Rekening BLU'
                    },
                },
                responsive: true,
                scales: {
                    x: {
                        stacked: true,
                    },
                    y: {
                        stacked: true
                    }
                }
            }
        };

        const myChart = new Chart(
            document.getElementById('myChart'),
            config
        );
        const myChart2 = new Chart(
            document.getElementById('myChartBulanan'),
            config2
        );
        const myChart3 = new Chart(
            document.getElementById('mySaldoHarian'),
            config3
        );
    </script>
    <script>
        $(function() {
            $('#example').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "order": [
                    [2, 'desc']
                ],
                "info": false,
                "autoWidth": false,
                "responsive": false,
                "scrollY": "Auto",
            });
            //Initialize Select2 Elements
            $('.select2').select2()
        });
        //Date picker
        $('#tanggal').datetimepicker({
            format: 'YYYY-MM'
        });
        $('#tanggal_transaksi').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    </script>
@endsection
