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
                            <form action="{{ route('datasurvei.dashboard') }}" method="GET">
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

                {{-- @foreach ($dataTable1 as $label => $value)
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
                @endforeach --}}

                @php
                    $colors = ['info', 'primary', 'warning', 'success'];
                    $icons = ['far fa-hospital', 'fas fa-user-friends', 'fas fa-search', 'fas fa-check'];
                    $i = 0;
                @endphp

                {{-- @foreach ($dataTable2 as $label => $value)
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
                @endforeach --}}
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
                            <canvas id="myChartKepuasan"
                                style="min-height: 250px; height: 250px; max-height: 500px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th class="text-center" colspan="9">Perhitungan Nilai
                                                {{ \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('F Y') }}
                                            </th>
                                            {{-- <th class="text-center align-middle bg-primary" rowspan="2">Total Rerata</th> --}}

                                        </tr>
                                        <tr class="text-center">
                                            <th>Rerata Total Pertanyaan 1</th>
                                            <th>Rerata Total Pertanyaan 2</th>
                                            <th>Rerata Total Pertanyaan 3</th>
                                            <th>Rerata Total Pertanyaan 4</th>
                                            <th>Rerata Total Pertanyaan 5</th>
                                            <th>Rerata Total Pertanyaan 6</th>
                                            <th>Rerata Total Pertanyaan 7</th>
                                            <th>Rerata Total Pertanyaan 8</th>
                                            <th>Rerata Total Pertanyaan 9-10</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="text-right">
                                            <td>{{ number_format($avgTotalPertanyaan1 ?? 0, 2) }}</td>
                                            <td>{{ number_format($avgTotalPertanyaan2 ?? 0, 2) }}</td>
                                            <td>{{ number_format($avgTotalPertanyaan3 ?? 0, 2) }}</td>
                                            <td>{{ number_format($avgTotalPertanyaan4 ?? 0, 2) }}</td>
                                            <td>{{ number_format($avgTotalPertanyaan5 ?? 0, 2) }}</td>
                                            <td>{{ number_format($avgTotalPertanyaan6 ?? 0, 2) }}</td>
                                            <td>{{ number_format($avgTotalPertanyaan7 ?? 0, 2) }}</td>
                                            <td>{{ number_format($avgTotalPertanyaan8 ?? 0, 2) }}</td>
                                            <td>{{ number_format($avgTotalPertanyaan910 ?? 0, 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-primary">
                            <i class="fas fa-chart-bar"></i>
                        </span>

                        <div class="info-box-content">
                            <span class="info-box-text">Rerata Total</span>
                            <span class="info-box-number">{{ number_format($avgTotalAll ?? 0, 2) }}</span>
                        </div>
                    </div>
                    <div class="info-box">
                        <span class="info-box-icon bg-warning">
                            <i class="far fa-laugh"></i>
                        </span>

                        <div class="info-box-content">
                            <span class="info-box-text">Nilai</span>
                            <span class="info-box-number">{{ number_format($avgTotalAll * 25 ?? 0, 2) }}</span>
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
        var labelsBulan = {!! json_encode($labelsNamaBulan) !!};
        var dataPengaduan = {!! json_encode($dataPengaduanBulanan) !!};
        var dataKepuasan = {!! json_encode($dataKepuasanBulanan) !!};
        var tahun = {!! json_encode(Carbon\Carbon::parse($tanggal)->format('Y')) !!};

        const dataPengaduanChart = {
            labels: labelsBulan,
            datasets: [{
                label: 'Pengaduan',
                data: dataPengaduan,
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
            data: dataPengaduanChart,
            options: {
                responsive: true,
                plugins: {
                    // legend: {
                    //     position: 'bottom',
                    // },
                    title: {
                        display: true,
                        text: 'Pengaduan per Bulan ' + tahun
                    }
                }
            }
        };

        const dataKepuasanChart = {
            labels: labelsBulan,
            datasets: [{
                label: 'Kepuasan',
                data: dataKepuasan,
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

        const configKepuasan = {
            type: 'bar',
            data: dataKepuasanChart,
            options: {
                responsive: true,
                plugins: {
                    // legend: {
                    //     position: 'bottom',
                    // },
                    title: {
                        display: true,
                        text: 'Survei Kepuasan per Bulan ' + tahun
                    }
                }
            }
        };

        const myChart = new Chart(
            document.getElementById('myChart'),
            config
        );

        const myChartKepuasan = new Chart(
            document.getElementById('myChartKepuasan'),
            configKepuasan
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
