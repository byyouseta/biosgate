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
                            <table class="table table-borderless py-0 mb-3">
                                <thead>
                                    <tr>
                                        <th class="align-middle text-center pb-1 " colspan="7">
                                            <h5>SURAT BUKTI PELAYANAN KESEHATAN RAWAT JALAN</h5>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="pt-0 pb-0" style="width: 15%">Nama Pasien</td>
                                        <td class="pt-0 pb-0" style="width: 45%">: {{ $data->nm_pasien }}</td>
                                        <td class="pt-0 pb-0"></td>
                                        <td class="pt-0 pb-0"></td>
                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-0">No. Rekam Medis</td>
                                        <td class="pt-0 pb-0">: {{ $data->no_rkm_medis }}</td>
                                        <td class="pt-0 pb-0">Cara Pulang</td>
                                        <td class="pt-0 pb-0"></td>
                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-0">Tanggal Lahir</td>
                                        <td class="pt-0 pb-0">:
                                            {{ \Carbon\Carbon::parse($data->tgl_lahir)->format('d/m/Y') }}</td>
                                        <td class="pt-0 pb-0">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" onclick="return false;"
                                                    {{ $data->stts == 'Sudah' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="defaultCheck1">
                                                    Atas Persetujuan Dokter
                                                </label>
                                            </div>
                                        </td>
                                        <td class="pt-0 pb-0"></td>
                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-0">Jenis Kelamin</td>
                                        <td class="pt-0 pb-0">: {{ $data->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                        </td>
                                        <td class="pt-0 pb-0">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" onclick="return false;"
                                                    {{ $data->stts == 'Dirujuk' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="defaultCheck1">
                                                    Rujuk
                                                </label>
                                            </div>
                                        </td>
                                        <td class="pt-0 pb-0"></td>
                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-0">Tanggal Kunjungan RS</td>
                                        <td class="pt-0 pb-0">:
                                            {{ \Carbon\Carbon::parse($data->tgl_registrasi)->format('d/m/Y') }}</td>
                                        <td class="pt-0 pb-0">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" onclick="return false;"
                                                    {{ $data->stts == 'Dirawat' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="defaultCheck1">
                                                    MRS
                                                </label>
                                            </div>
                                        </td>
                                        <td class="pt-0 pb-0"></td>
                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-0">Jam Masuk</td>
                                        <td class="pt-0 pb-0">: {{ $data->jam_reg }}</td>
                                        <td class="pt-0 pb-0"></td>
                                        <td class="pt-0 pb-0"></td>
                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-0">Poliklinik</td>
                                        <td class="pt-0 pb-0">: {{ $data->nm_poli }}</td>
                                        <td class="pt-0 pb-0"></td>
                                        <td class="pt-0 pb-0"></td>
                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-0">Umur</td>
                                        <td class="pt-0 pb-0">:
                                            {{ \Carbon\Carbon::parse($data->tgl_lahir)->diff(\Carbon\Carbon::now())->format('%y Th %m Bl %d Hr') }}
                                        </td>
                                        <td class="pt-0 pb-0"></td>
                                        <td class="pt-0 pb-0"></td>
                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-0">Alamat</td>
                                        <td class="pt-0 pb-0">: {{ $data->alamat }}</td>
                                        <td class="pt-0 pb-0"></td>
                                        <td class="pt-0 pb-0"></td>
                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-0">Status Pasien</td>
                                        <td class="pt-0 pb-0">: {{ $data->png_jawab }}</td>
                                        <td class="pt-0 pb-0"></td>
                                        <td class="pt-0 pb-0"></td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table table-bordered mb-3">
                                <thead class="text-center">
                                    <tr>
                                        <th class="border border-dark" style="width: 5%">No</th>
                                        <th class="border border-dark" style="width: 75%">Diagnosa</th>
                                        <th class="border border-dark" style="width: 20%">ICD X</th>
                                    </tr>
                                </thead>
                                <tbody class="border border-dark">
                                    @forelse ($diagnosa as $index => $dataDiagnosa)
                                        <tr>
                                            <td class="border border-dark text-center">{{ ++$index }} </td>
                                            <td class="border border-dark">{{ ++$dataDiagnosa->nm_penyakit }} </td>
                                            <td class="border border-dark  text-center">{{ ++$dataDiagnosa->kd_penyakit }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="border border-dark text-center"></td>
                                            <td class="border border-dark"></td>
                                            <td class="border border-dark text-center"></td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <table class="table table-bordered mb-5">
                                <thead class="text-center">
                                    <tr>
                                        <th class="border border-dark" style="width: 5%">No</th>
                                        <th class="border border-dark" style="width: 75%">Prosedur</th>
                                        <th class="border border-dark" style="width: 20%">ICD IX</th>
                                    </tr>
                                </thead>
                                <tbody class="border border-dark">
                                    @forelse ($prosedur as $index => $dataProsedur)
                                        <tr>
                                            <td class="border border-dark text-center">{{ ++$index }} </td>
                                            <td class="border border-dark">{{ ++$dataProsedur->deskripsi_panjang }} </td>
                                            <td class="border border-dark text-center">{{ ++$dataProsedur->kode }} </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="border border-dark text-center"></td>
                                            <td class="border border-dark"></td>
                                            <td class="border border-dark text-center"></td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-center pt-0 pb-0" style="width: 50%">Pasien</td>
                                    <td class="text-center pt-0 pb-0" style="width: 50%">DPJP/Dokter Pemeriksa</td>
                                </tr>
                                <tr>
                                    @php
                                        $qr_dokter = 'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara elektronik oleh' . "\n" . $data->nm_dokter . "\n" . 'ID ' . $data->kd_dokter . "\n" . \Carbon\Carbon::parse($data->tgl_registrasi)->format('d-m-Y');
                                        $qr_pasien = 'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara elektronik oleh' . "\n" . $data->nm_pasien . "\n" . 'ID ' . $data->no_rkm_medis . "\n" . \Carbon\Carbon::parse($data->tgl_registrasi)->format('d-m-Y');
                                    @endphp
                                    <td class="text-center pt-0 pb-0"> {!! QrCode::size(100)->generate($qr_pasien) !!} </td>
                                    <td class="text-center pt-0 pb-0"> {!! QrCode::size(100)->generate($qr_dokter) !!} </td>
                                </tr>
                                <tr>
                                    <td class="text-center pt-0 pb-0">{{ $data->nm_pasien }}</td>
                                    <td class="text-center pt-0 pb-0"> {{ $data->nm_dokter }} </td>
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
