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
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td style="width:20%" rowspan="3"><img src="{{ asset('image/logorsup.jpg') }}"
                                            alt="Logo RSUP" width="100"></td>
                                    <td class="pt-0 pb-0 text-center align-middle ">
                                        <h3 class="pt-0 pb-0">RSUP SURAKARTA</h3>
                                    </td>
                                    <td style="width:20%" rowspan="3"></td>
                                </tr>
                                <tr>
                                    <td class="text-center align-middle py-0">
                                        Jl.Prof.Dr.R.Soeharso No.28 , Surakarta, Jawa Tengah
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center align-middle py-0">
                                        Telp.0271-713055 / 720002, E-mail : rsupsurakarta@kemkes.go.id
                                    </td>
                                </tr>

                            </table>
                            <div class="progress progress-xs mt-0 pt-0">
                                <div class="progress-bar progress-bar bg-black" role="progressbar" aria-valuenow="100"
                                    aria-valuemin="0" aria-valuemax="100" style="width: 100%">

                                </div>
                            </div>
                            {{-- <div style="overflow-x:auto;"> --}}
                            <table class="table table-borderless py-0">
                                <thead>
                                    <tr>
                                        <th class="align-middle text-center pb-1" colspan="7">
                                            <h5>BILLING</h5>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total = 0;
                                        $status_dokter = 0;
                                    @endphp
                                    @foreach ($data as $data)
                                        <tr>
                                            @if ($data->status == 'TtlObat')
                                                <td class="pt-0 pb-0 text-right text-bold" colspan="7">
                                                    {{ $data->nm_perawatan != null ? $data->nm_perawatan : '' }}</td>
                                            @elseif($data->status == 'Dokter' && $status_dokter == 0)
                                                <td class="pt-0 pb-0">Dokter</td>
                                                <td class="pt-0 pb-0" colspan="6">
                                                    {{ $data->nm_perawatan != null ? ": $data->nm_perawatan" : '' }}</td>
                                                @php
                                                    $status_dokter = 1;
                                                @endphp
                                            @elseif($data->status == 'Dokter' && $status_dokter == 1)
                                                <td class="pt-0 pb-0"></td>
                                                <td class="pt-0 pb-0" colspan="6">
                                                    {{ $data->nm_perawatan != null ? ": $data->nm_perawatan" : '' }}</td>
                                                {{-- @elseif ($data->no === 'Dokter')
                                                <td>{{ $data->no }}</td> --}}
                                            @elseif ($data->no_status != 'Dokter ')
                                                <td class="pt-0 pb-0">
                                                    {{ $data->no_status != null ? $data->no_status : '' }}</td>
                                                <td class="pt-0 pb-0">
                                                    {{ $data->nm_perawatan != null ? $data->nm_perawatan : '' }}</td>
                                                <td class="pt-0 pb-0">{{ $data->pemisah != null ? $data->pemisah : '' }}
                                                </td>
                                                <td class="pt-0 pb-0 text-right">
                                                    {{ $data->biaya != null ? number_format($data->biaya, 0, ',', '.') : '' }}
                                                </td>
                                                <td class="pt-0 pb-0 text-right">
                                                    {{ $data->jumlah != null ? $data->jumlah : '' }}</td>
                                                <td class="pt-0 pb-0 text-right">
                                                    {{ $data->tambahan != null ? $data->tambahan : '' }}
                                                </td>
                                                <td class="pt-0 pb-0 text-right">
                                                    {{ $data->totalbiaya != null ? number_format($data->totalbiaya, 0, ',', '.') : '' }}
                                                    @php
                                                        $total = $total + $data->totalbiaya;
                                                    @endphp
                                                </td>
                                            @endif

                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td class="pt-0 pb-0 text-bold">TOTAL BIAYA</td>
                                        <td class="pt-0 pb-0 text-bold">: </td>
                                        <td class="pt-0 pb-0 text-right text-bold" colspan="5">
                                            {{ number_format($total, 0, ',', '.') }} </td>
                                    </tr>
                                </tbody>

                            </table>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-center">Keluarga Pasien </td>
                                    <td class="text-center">
                                        Surakarta,
                                        {{ \Carbon\Carbon::parse($data->tgl_byr)->format('d-m-Y') }}<br>
                                        <p>Petugas Kasir</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center">(..........................)</td>
                                    <td class="text-center"> (..........................) </td>
                                </tr>
                            </table>
                            {{-- </div> --}}
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
