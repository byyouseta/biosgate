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
                                            <h5>HASIL PEMERIKSAAN RADIOLOGI</h5>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="pt-0 pb-0">No.RM</td>
                                        <td class="pt-0 pb-0">: {{ $data->no_rkm_medis }}</td>
                                        <td class="pt-0 pb-0">Penanggung Jawab</td>
                                        <td class="pt-0 pb-0">: {{ $dokterRad->nm_dokter }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-0">Nama Pasien</td>
                                        <td class="pt-0 pb-0">: {{ $data->nm_pasien }}</td>
                                        <td class="pt-0 pb-0">Dokter Pengirim</td>
                                        <td class="pt-0 pb-0">: {{ $data->nm_dokter }}</td>

                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-0">JK/Umur</td>
                                        <td class="pt-0 pb-0">: {{ $data->jk == 'L' ? 'Laki-laki' : 'Perempuan' }} /
                                            {{ \Carbon\Carbon::parse($data->tgl_lahir)->diff(\Carbon\Carbon::now())->format('%y Th %m Bl %d Hr') }}
                                        </td>
                                        <td class="pt-0 pb-0">Tgl.Pemeriksaan</td>
                                        <td class="pt-0 pb-0">:
                                            {{ \Carbon\Carbon::parse($data->tgl_periksa)->format('d-m-Y') }}</td>

                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-0">Alamat</td>
                                        <td class="pt-0 pb-0">: {{ $data->almt_pj }}</td>
                                        <td class="pt-0 pb-0">Jam Pemeriksaan</td>
                                        <td class="pt-0 pb-0">: {{ $data->jam }}</td>

                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-0">No.Periksa</td>
                                        <td class="pt-0 pb-0">: {{ $data->no_rawat }}</td>
                                        <td class="pt-0 pb-0">Poli</td>
                                        <td class="pt-0 pb-0">: {{ $data->nm_poli }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-0">Pemeriksaan</td>
                                        <td class="pt-0 pb-0">: {{ $data->nm_perawatan }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-0">Hasil Pemeriksaan</td>
                                    </tr>
                                </tbody>

                            </table>
                            @php
                                $paragraphs = explode("\n", $data->hasil);
                                $tinggi = 25 * count($paragraphs);
                            @endphp
                            <table class="table table-bordered">
                                <tbody class="border border-dark">
                                    <tr>
                                        <textarea class="form-control" readonly
                                            style="
                                            min-height: {{ $tinggi }}px;
                                            resize: none;
                                            overflow-y:hidden;
                                            border:1px solid black;
                                            background-color: white;
                                        ">{{ $data->hasil != null ? $data->hasil : '' }}</textarea>
                                    </tr>
                                    {{-- <tr>
                                        <td>

                                            @foreach ($paragraphs as $paragraph)
                                                <p>{{ $paragraph }}</p>
                                            @endforeach
                                        </td>
                                    </tr> --}}
                                </tbody>

                            </table>
                            {{-- <div>
                                <small><b>Catatan:</b> Jika ada keragu-raguan pemeriksaan, diharapkan segera menghubungi
                                    laboratorium.</small>
                                <div class="float-right">Tgl.Cetak : {{ Carbon\Carbon::now()->format('d/m/Y h:i:s') }}
                                </div>
                            </div> --}}
                            <table class="table table-borderless mt-1">
                                <tr>
                                    <td class="text-center pt-0 pb-0" style="width: 70%"></td>
                                    <td class="text-center pt-0 pb-0" style="width: 30%">Dokter Radiologi</td>
                                </tr>
                                <tr>
                                    @php
                                        $qr_dokter = 'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara elektronik oleh' . "\n" . $dokterRad->nm_dokter . "\n" . 'ID ' . $dokterRad->kd_dokter . "\n" . \Carbon\Carbon::parse($data->tgl_periksa)->format('d-m-Y');
                                    @endphp
                                    <td class="text-center pt-0 pb-0" style="width: 70%"></td>
                                    <td class="text-center pt-0 pb-0" style="width: 30%">{!! QrCode::size(100)->generate($qr_dokter) !!}</td>
                                </tr>
                                <tr>
                                    <td class="text-center pt-0 pb-0" style="width: 70%"></td>
                                    <td class="text-center pt-0 pb-0" style="width: 30%">{{ $dokterRad->nm_dokter }}</td>
                                </tr>
                            </table>
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
