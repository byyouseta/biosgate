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
                                $tanggal = \Carbon\Carbon::now()->locale('id')->format('Y-m-d');
                            @endphp
                        @endif
                        @php
                            $kemarin = \Carbon\Carbon::parse($tanggal)->yesterday()->format('Y-m-d');
                        @endphp
                        <div class="card-body">
                            <form action="{{ route('vedika.FraudChart') }}" method="GET">
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
                @php
                    $colors = ['info', 'primary', 'warning', 'success'];
                    $icons = ['fas fa-running', 'fas fa-user-friends', 'fas fa-search', 'fas fa-check'];
                    $i = 0;
                @endphp

                @foreach ($dataTable1 as $label => $value)
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-{{ $colors[$i] ?? 'secondary' }}">
                                <i class="{{ $icons[$i] ?? 'fas fa-chart-pie' }}"></i>
                            </span>

                            <div class="info-box-content">
                                <span class="info-box-text">{{ $label }}</span>
                                <span class="info-box-number">{{ number_format($value) }}</span>
                            </div>
                        </div>
                    </div>
                    @php $i++; @endphp
                @endforeach

                @php
                    $colors = ['info', 'primary', 'warning', 'success'];
                    $icons = ['far fa-hospital', 'fas fa-user-friends', 'fas fa-search', 'fas fa-check'];
                    $i = 0;
                @endphp

                @foreach ($dataTable2 as $label => $value)
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-{{ $colors[$i] ?? 'secondary' }}">
                                <i class="{{ $icons[$i] ?? 'fas fa-chart-pie' }}"></i>
                            </span>

                            <div class="info-box-content">
                                <span class="info-box-text">{{ $label }}</span>
                                <span class="info-box-number">{{ number_format($value) }}</span>
                            </div>
                        </div>
                    </div>
                    @php $i++; @endphp
                @endforeach
                <div class="col-md-6">
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
                            <canvas id="myChartRanap"
                                style="min-height: 250px; height: 250px; max-height: 500px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <canvas id="chartTahunan"
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

    <script>
        var labelsFraudKriteria = {!! json_encode($labelsFraudKriteria) !!};
        var dataFraudKriteriaRajal = {!! json_encode($dataFraudKriteriaRajal) !!};
        var dataFraudKriteriaRanap = {!! json_encode($dataFraudKriteriaRanap) !!};
        var labelsBulan = {!! json_encode($labelsBulan) !!};
        var dataBulanRajal = {!! json_encode($dataBulanRajal) !!};
        var dataBulanRanap = {!! json_encode($dataBulanRanap) !!};

        const dataFraud = {
            labels: labelsFraudKriteria,
            datasets: [{
                label: 'Kriteria Potensi Fraud Rajal Terbanyak',
                data: dataFraudKriteriaRajal,
                backgroundColor: [
                    '#4e73df', // Biru
                    '#e74a3b', // Merah
                    '#f6c23e', // Kuning
                    '#1cc88a', // Hijau
                    '#36b9cc', // Toska
                    '#858796', // Abu-abu
                    '#6f42c1', // Ungu
                    '#fd7e14', // Oranye
                    '#20c997', // Hijau muda
                    '#0d6efd', // Biru terang
                    '#adb5bd', // Abu terang
                    '#198754', // Hijau tua
                    '#dc3545' // Merah tua
                ],
            }]
        };

        const config = {
            type: 'bar',
            data: dataFraud,
            options: {
                responsive: true,
                plugins: {
                    // legend: {
                    //     position: 'bottom',
                    // },
                    title: {
                        display: true,
                        text: 'Kriteria Potensi Fraud Rajal Terbanyak'
                    }
                }
            }
        };

        const dataFraudRanap = {
            labels: labelsFraudKriteria,
            datasets: [{
                label: 'Kriteria Potensi Fraud Ranap Terbanyak',
                data: dataFraudKriteriaRanap,
                backgroundColor: [
                    '#4e73df', // Biru
                    '#e74a3b', // Merah
                    '#f6c23e', // Kuning
                    '#1cc88a', // Hijau
                    '#36b9cc', // Toska
                    '#858796', // Abu-abu
                    '#6f42c1', // Ungu
                    '#fd7e14', // Oranye
                    '#20c997', // Hijau muda
                    '#0d6efd', // Biru terang
                    '#adb5bd', // Abu terang
                    '#198754', // Hijau tua
                    '#dc3545' // Merah tua
                ],
            }]
        };

        const configRanap = {
            type: 'bar',
            data: dataFraudRanap,
            options: {
                responsive: true,
                plugins: {
                    // legend: {
                    //     position: 'bottom',
                    // },
                    title: {
                        display: true,
                        text: 'Kriteria Potensi Fraud Ranap Terbanyak'
                    }
                }
            }
        };

        const dataTahunan = {
            labels: labelsBulan,
            datasets: [{
                label: 'Potensi Fraud Rajal',
                backgroundColor: 'rgb(102, 153, 255)',
                borderColor: 'rgb(102, 153, 255)',
                data: dataBulanRajal,
            }, {
                label: 'Potensi Fraud Ranap',
                backgroundColor: 'rgb(255, 102, 153)',
                borderColor: 'rgb(255, 99, 132)',
                data: dataBulanRanap,
            }, ]
        };

        const configTahunan = {
            type: 'line',
            data: dataTahunan,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Potensi Fraud Tahunan Rajal dan Ranap'
                    }
                }
            }
        };

        const myChart = new Chart(
            document.getElementById('myChart'),
            config
        );

        const myChartRanap = new Chart(
            document.getElementById('myChartRanap'),
            configRanap
        );

        const myChartTahunan = new Chart(
            document.getElementById('chartTahunan'),
            configTahunan
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
