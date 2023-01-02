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
                    {{-- Multi Tab --}}
                    <div class="card card-primary card-outline card-outline-tabs">
                        <div class="card-header p-0 border-bottom-0">
                            <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                                @foreach ($pasien as $index => $resep)
                                    <li class="nav-item">
                                        <a class="nav-link {{ $index == 0 ? 'active' : '' }}" id="custom-tabs-four-home-tab"
                                            data-toggle="pill" href="#custom-tabs-lap-{{ $resep->no_resep }}" role="tab"
                                            aria-controls="custom-tabs-four-home" aria-selected="true"> Resep No.
                                            {{ $resep->no_resep }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="custom-tabs-four-tabContent">
                                @foreach ($pasien as $index => $listResep)
                                    <div class="tab-pane fade show {{ $index == 0 ? 'active' : '' }}"
                                        id="custom-tabs-lap-{{ $listResep->no_resep }}" role="tabpanel"
                                        aria-labelledby="#custom-tabs-lap-{{ $listResep->no_resep }}">

                                        <table class="table table-borderless mb-0">
                                            <tr>
                                                <td style="width:20%" rowspan="3"><img
                                                        src="{{ asset('image/logorsup.jpg') }}" alt="Logo RSUP"
                                                        width="100"></td>
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
                                            <div class="progress-bar progress-bar bg-black" role="progressbar"
                                                aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"
                                                style="width: 100%">

                                            </div>
                                        </div>
                                        {{-- <div style="overflow-x:auto;"> --}}
                                        <table class="table table-borderless py-0">
                                            <tbody>
                                                <tr>
                                                    <td class="pt-0 pb-0">Nama Pasien</td>
                                                    <td class="pt-0 pb-0">: {{ $listResep->nm_pasien }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="pt-0 pb-0" style="width: 15%">No.RM</td>
                                                    <td class="pt-0 pb-0" style="width: 45%">:
                                                        {{ $listResep->no_rkm_medis }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="pt-0 pb-0" style="width: 15%">No.Rawat</td>
                                                    <td class="pt-0 pb-0" style="width: 45%">: {{ $listResep->no_rawat }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="pt-0 pb-0">Penanggung</td>
                                                    <td class="pt-0 pb-0">: BPJS</td>
                                                </tr>
                                                <tr>
                                                    <td class="pt-0 pb-0">Pemberi Resep</td>
                                                    <td class="pt-0 pb-0">: {{ $listResep->nm_dokter }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="pt-0 pb-0">No. Resep</td>
                                                    <td class="pt-0 pb-0">: {{ $listResep->no_resep }}</td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <table class="table table-border table-sm">
                                            <tbody>
                                                @php
                                                    $totalBiaya = $index = 0;
                                                @endphp
                                                @foreach ($data as $daftarObat)
                                                    @if ($daftarObat->jam == $listResep->jam)
                                                        <tr>
                                                            <td class="text-center">
                                                                {{ ++$index }}
                                                            </td>
                                                            <td>
                                                                {{ $daftarObat->nama_brng }}
                                                            </td>
                                                            <td class="text-center">
                                                                {{ number_format((int) $daftarObat->jml, 1, '.', ',') }}
                                                                {{ $daftarObat->kode_sat }}
                                                            </td>
                                                            <td class="text-right">
                                                                Rp
                                                                {{ number_format((int) $daftarObat->total, 2, '.', ',') }}
                                                                @php
                                                                    $totalBiaya = $totalBiaya + (int) $daftarObat->total;
                                                                @endphp
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3" class="pl-5">TOTAL : </td>
                                                    <td class="text-right">
                                                        Rp {{ number_format($totalBiaya, 2, '.', ',') }}
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>

                                        <table class="table table-borderless">
                                            <tr>
                                                <td class="text-center pt-0 pb-0" style="width: 70%"></td>
                                                <td class="text-center pt-0 pb-0">Surakarta,
                                                    {{ \Carbon\Carbon::parse($listResep->tgl_perawatan)->format('d-m-Y') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                @php
                                                    $qr_dokter = 'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara elektronik oleh' . "\n" . $listResep->nm_dokter . "\n" . 'ID ' . $listResep->kd_dokter . "\n" . \Carbon\Carbon::parse($listResep->tgl_perawatan)->format('d-m-Y');

                                                @endphp
                                                <td class="text-center pt-0 pb-0" style="width: 70%"> </td>
                                                <td class="text-center pt-0 pb-0"> {!! QrCode::size(100)->generate($qr_dokter) !!} </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center pt-0 pb-0" style="width: 70%"></td>
                                                <td class="text-center pt-0 pb-0"> {{ $listResep->nm_dokter }} </td>
                                            </tr>
                                        </table>

                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <!-- /.card -->
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
