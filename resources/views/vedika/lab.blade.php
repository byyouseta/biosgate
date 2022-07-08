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
                                            <h5>HASIL PEMERIKSAAN LABORATIUM</h5>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="pt-0 pb-0" style="width: 15%">No.RM</td>
                                        <td class="pt-0 pb-0" style="width: 45%">: {{ $data->no_rkm_medis }}</td>
                                        <td class="pt-0 pb-0" style="width: 15%">No.Permintaan Lab</td>
                                        <td class="pt-0 pb-0" style="width: 25%">: {{ $data->noorder }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-0">Nama Pasien</td>
                                        <td class="pt-0 pb-0">: {{ $data->nm_pasien }}</td>
                                        <td class="pt-0 pb-0">Tgl.Permintaan</td>
                                        <td class="pt-0 pb-0">:
                                            {{ \Carbon\Carbon::parse($data->tgl_permintaan)->format('d-m-Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-0">JK/Umur</td>
                                        <td class="pt-0 pb-0">: {{ $data->jk == 'L' ? 'Laki-laki' : 'Perempuan' }} /
                                            {{-- {{ $data->umurdaftar }} {{ $data->sttsumur }} --}}
                                            {{ \Carbon\Carbon::parse($data->tgl_lahir)->diff(\Carbon\Carbon::now())->format('%y Th %m Bl %d Hr') }}
                                        </td>
                                        <td class="pt-0 pb-0">Jam Permintaan</td>
                                        <td class="pt-0 pb-0">: {{ $data->jam_permintaan }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-0">Alamat</td>
                                        <td class="pt-0 pb-0">: {{ $data->almt_pj }}</td>
                                        <td class="pt-0 pb-0">Tgl. Keluar Hasil</td>
                                        <td class="pt-0 pb-0">:
                                            {{ \Carbon\Carbon::parse($data->tgl_hasil)->format('d-m-Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-0">No.Periksa</td>
                                        <td class="pt-0 pb-0">: {{ $data->no_rawat }}</td>
                                        <td class="pt-0 pb-0">Jam Keluar Hasil</td>
                                        <td class="pt-0 pb-0">: {{ $data->jam_hasil }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-0">Dokter Pengirim</td>
                                        <td class="pt-0 pb-0">: {{ $dokterPerujuk->nm_dokter }}</td>
                                        <td class="pt-0 pb-0">Poli</td>
                                        <td class="pt-0 pb-0">: {{ $data->nm_poli }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table table-bordered">
                                <thead class="text-center">
                                    <tr>
                                        <th class="border border-dark">Pemeriksaan</th>
                                        <th class="border border-dark">Hasil</th>
                                        <th class="border border-dark">Satuan</th>
                                        <th class="border border-dark">Nilai Rujukan</th>
                                        <th class="border border-dark">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="border border-dark">
                                    @php
                                        $nama_perawatan = '';
                                    @endphp
                                    @foreach ($hasil_periksa as $hasil)
                                        @if ($hasil->jam == $data->jam_hasil)
                                            @if ($nama_perawatan != $hasil->nm_perawatan)
                                                @php
                                                    $nama_perawatan = $hasil->nm_perawatan;
                                                @endphp
                                                <tr>
                                                    <td class="pt-0 pb-0 border border-dark border-top-0 border-bottom-0">
                                                        {{ $hasil->nm_perawatan != null ? $hasil->nm_perawatan : '' }}
                                                        <p class="mb-0 pl-2">
                                                            {{ $hasil->Pemeriksaan != null ? $hasil->Pemeriksaan : '' }}
                                                        </p>
                                                    </td>
                                                    <td
                                                        class="pt-0 pb-0 text-center border border-dark border-top-0 border-bottom-0">
                                                        <br>
                                                        {{ $hasil->nilai != null ? $hasil->nilai : '' }}
                                                    </td>
                                                    <td
                                                        class="pt-0 pb-0 text-center border border-dark border-top-0 border-bottom-0">
                                                        <br>
                                                        {{ $hasil->satuan != null ? $hasil->satuan : '' }}
                                                    </td>
                                                    <td
                                                        class="pt-0 pb-0 text-center border border-dark border-top-0 border-bottom-0">
                                                        <br>
                                                        {{ $hasil->nilai_rujukan != null ? $hasil->nilai_rujukan : '' }}
                                                    </td>
                                                    <td class="pt-0 pb-0 border border-dark border-top-0 border-bottom-0">
                                                        <br>
                                                        {{ $hasil->keterangan != null ? $hasil->keterangan : '' }}
                                                    </td>
                                                </tr>
                                            @else
                                                @php
                                                    $nama_perawatan = $hasil->nm_perawatan;
                                                @endphp
                                                <tr>
                                                    <td class="pt-0 pb-0 border border-dark border-top-0 border-bottom-0">
                                                        <p class="mb-0 pl-2">
                                                            {{ $hasil->Pemeriksaan != null ? $hasil->Pemeriksaan : '' }}
                                                        </p>
                                                    </td>
                                                    <td
                                                        class="pt-0 pb-0 text-center border border-dark border-top-0 border-bottom-0">
                                                        {{ $hasil->nilai != null ? $hasil->nilai : '' }}
                                                    </td>
                                                    <td
                                                        class="pt-0 pb-0 text-center border border-dark border-top-0 border-bottom-0">
                                                        {{ $hasil->satuan != null ? $hasil->satuan : '' }}
                                                    </td>
                                                    <td
                                                        class="pt-0 pb-0 text-center border border-dark border-top-0 border-bottom-0">
                                                        {{ $hasil->nilai_rujukan != null ? $hasil->nilai_rujukan : '' }}
                                                    </td>
                                                    <td class="pt-0 pb-0 border border-dark border-top-0 border-bottom-0">
                                                        {{ $hasil->keterangan != null ? $hasil->keterangan : '' }}
                                                    </td>
                                                </tr>
                                            @endif
                                        @endif
                                    @endforeach
                                </tbody>

                            </table>
                            <div>
                                <small><b>Catatan:</b> Jika ada keragu-raguan pemeriksaan, diharapkan segera menghubungi
                                    laboratorium.</small>
                                <div class="float-right">Tgl.Cetak : {{ Carbon\Carbon::now()->format('d/m/Y h:i:s') }}
                                </div>
                            </div>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-center pt-0 pb-0">Penanggung Jawab</td>
                                    <td class="text-center pt-0 pb-0"> Petugas Laboratorium</td>
                                </tr>
                                <tr>
                                    @php
                                        $qr_dokter = 'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara elektronik oleh' . "\n" . $dokterLab->nm_dokter . "\n" . 'ID ' . $dokterLab->kd_dokter . "\n" . \Carbon\Carbon::parse($data->tgl_hasil)->format('d-m-Y');
                                        $qr_petugas = 'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara elektronik oleh' . "\n" . $petugas->nama . "\n" . 'ID ' . $petugas->nip . "\n" . \Carbon\Carbon::parse($data->tgl_hasil)->format('d-m-Y');
                                    @endphp
                                    <td class="text-center pt-0 pb-0"> {!! QrCode::size(100)->generate($qr_dokter) !!} </td>
                                    <td class="text-center pt-0 pb-0"> {!! QrCode::size(100)->generate($qr_petugas) !!} </td>
                                </tr>
                                <tr>
                                    <td class="text-center pt-0 pb-0">{{ $dokterLab->nm_dokter }}</td>
                                    <td class="text-center pt-0 pb-0"> {{ $petugas->nama }} </td>
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
