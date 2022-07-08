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
                            @php
                                for ($i = 1; $i <= 5; $i++) {
                                    foreach ($skala[$i] as $dataPemeriksaan) {
                                        if ($dataPemeriksaan->nama_pemeriksaan == 'ASSESMENT TRIASE') {
                                            $urgensi = $dataPemeriksaan->pengkajian_skala;
                                        }
                                    }
                                }

                                //data PLAN
                                if (!empty($primer)) {
                                    $plan = $primer->plan;
                                } else {
                                    $plan = $sekunder->plan;
                                }

                                if ($plan == 'Zona Hijau') {
                                    $bg_color = 'bg-success';
                                } elseif ($plan == 'Zona Kuning') {
                                    $bg_color = 'bg-warning';
                                } elseif ($plan == 'Zona Merah') {
                                    $bg_color = 'bg-danger';
                                }
                            @endphp

                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <td class="pt-0 pb-0 border border-dark" style="width: 10%"></td>
                                        <td class="pt-0 pb-0 border border-dark" style="width: 10%"></td>
                                        <td class="pt-0 pb-0 border border-dark" style="width: 10%"></td>
                                        <td class="pt-0 pb-0 border border-dark" style="width: 10%"></td>
                                        <td class="pt-0 pb-0 border border-dark" style="width: 10%"></td>
                                        <td class="pt-0 pb-0 border border-dark" style="width: 10%"></td>
                                        <td class="pt-0 pb-0 border border-dark" style="width: 10%"></td>
                                        <td class="pt-0 pb-0 border border-dark" style="width: 10%"></td>
                                        <td class="pt-0 pb-0 border border-dark" style="width: 10%"></td>
                                        <td class="pt-0 pb-0 border border-dark" style="width: 10%"></td>
                                    </tr>
                                    <tr>
                                        <td class="border border-dark"><img src="{{ asset('image/logorsup.jpg') }}"
                                                alt="Logo RSUP" width="100">
                                        </td>
                                        <td class="pt-0 pb-0 text-center align-middle border border-dark" colspan="6">
                                            <div style="font-size: 30px">RSUP SURAKARTA</div>
                                            Jl.Prof.Dr.R.Soeharso No.28 , Surakarta, Jawa Tengah <br>
                                            Telp.0271-713055 / 720002 <br>
                                            E-mail : rsupsurakarta@kemkes.go.id
                                        </td>
                                        <td colspan="5" rowspan="2" class="border border-dark">
                                            <div class="row">
                                                <div class="col-4">No.RM / NIK</div>
                                                <div class="col-8">: {{ $data->no_rkm_medis }} / {{ $data->no_ktp }}
                                                </div>
                                                <div class="col-4">Nama</div>
                                                <div class="col-8">: {{ $data->nm_pasien }} ({{ $data->jk }})</div>
                                                <div class="col-4">Tanggal Lahir</div>
                                                <div class="col-8">:
                                                    {{ \Carbon\Carbon::parse($data->tgl_lahir)->format('d-m-Y') }}</div>
                                                <div class="col-4">Alamat</div>
                                                <div class="col-8">: {{ $data->alamat }}</div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center align-middle py-0 {{ $bg_color }} border border-dark"
                                            colspan="7">
                                            TRIASE PASIEN GAWAT DARURAT
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center align-middle py-0 border border-dark" colspan="10">
                                            Triase dilakukan segera setelah pasien datang dan sebelum pasien/ keluarga
                                            mendaftar
                                            di TPP IGD
                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="py-0 pl-5 border border-dark" colspan="5">
                                            Tanggal Kunjungan :
                                            {{ \Carbon\Carbon::parse($data->tgl_kunjungan)->format('d-m-Y') }}
                                        </td>
                                        <td class="py-0 border border-dark" colspan="5">
                                            Pukul : {{ \Carbon\Carbon::parse($data->tgl_kunjungan)->format('H:i:s') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-0 border border-dark" colspan="3">
                                            Cara Datang
                                        </td>
                                        <td class="py-0 border border-dark" colspan="7">
                                            {{ $data->cara_masuk }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-0 border border-dark" colspan="3">
                                            Macam Kasus
                                        </td>
                                        <td class="py-0 border border-dark" colspan="7">
                                            {{ $data->macam_kasus }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-0 text-center table-primary border border-dark" colspan="3">
                                            KETERANGAN
                                        </td>
                                        <td class="py-0 text-center table-primary border border-dark" colspan="7">
                                            {{ $primer != null ? 'TRIASE PRIMER' : 'TRIASE SEKUNDER' }}
                                        </td>
                                    </tr>
                                    @if (!empty($primer))
                                        <tr>
                                            <td class="py-0 border border-dark" colspan="3">
                                                KELUHAN UTAMA
                                            </td>
                                            <td class="py-0 border border-dark" colspan="7">
                                                {{ $primer->keluhan_utama }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-0 border border-dark" colspan="3">
                                                TANDA VITAL
                                            </td>
                                            <td class="py-0 border border-dark" colspan="7">
                                                Suhu (C) : {{ $data->suhu }}, Nyeri : {{ $data->nyeri }}, Tensi :
                                                {{ $data->tekanan_darah }}, Nadi(/menit) : {{ $data->nadi }}, Saturasi
                                                O2(%) : {{ $data->saturasi_o2 }}, Respirasi(/menit) :
                                                {{ $data->pernapasan }}
                                            </td>

                                        </tr>
                                        <tr>
                                            <td class="py-0 border border-dark" colspan="3">
                                                KEBUTUHAN KHUSUS
                                            </td>
                                            <td class="py-0 border border-dark" colspan="7">
                                                {{ $primer->kebutuhan_khusus }}
                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td class="py-0 border border-dark" colspan="3">
                                                ANAMNESA SINGKAT
                                            </td>
                                            <td class="py-0 border border-dark" colspan="7">
                                                {{ $sekunder->anamnesa_singkat }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-0 border border-dark" colspan="3">
                                                TANDA VITAL
                                            </td>
                                            <td class="py-0 border border-dark" colspan="7">
                                                Suhu (C) : {{ $data->suhu }}, Nyeri : {{ $data->nyeri }}, Tensi :
                                                {{ $data->tekanan_darah }}, Nadi(/menit) : {{ $data->nadi }}, Saturasi
                                                O2(%) : {{ $data->saturasi_o2 }}, Respirasi(/menit) :
                                                {{ $data->pernapasan }}
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td class="py-0 text-center table-primary border border-dark" colspan="3">
                                            PEMERIKSAAN
                                        </td>
                                        <td class="py-0 text-center {{ $bg_color }} border border-dark" colspan="7">
                                            URGENSI
                                            {{-- {{ $urgensi }} --}}
                                            @php
                                                $pemeriksaan = '';
                                            @endphp
                                            @for ($i = 1; $i <= 5; $i++)
                                                @foreach ($skala[$i] as $dataPemeriksaan)
                                                    @if ($dataPemeriksaan->nama_pemeriksaan != $pemeriksaan)
                                                        @php
                                                            $pemeriksaan = $dataPemeriksaan->nama_pemeriksaan;
                                                        @endphp
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-0 border border-dark" colspan="3">
                                            {{ $dataPemeriksaan->nama_pemeriksaan }}
                                        </td>
                                        <td class="py-0 {{ $bg_color }} border border-dark" colspan="7">
                                            {{ $dataPemeriksaan->pengkajian_skala }}
                                        @else
                                            , {{ $dataPemeriksaan->pengkajian_skala }}
                                            @endif
                                            @endforeach
                                            @endfor
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-0 border border-dark" colspan="3">
                                            PLAN
                                        </td>
                                        <td class="py-0 {{ $bg_color }} border border-dark" colspan="7">
                                            {{ $primer != null ? $primer->plan : $sekunder->plan }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-0 border border-dark" colspan="3">

                                        </td>
                                        <td class="py-0 text-center table-primary border border-dark" colspan="7">
                                            {{ $primer != null ? 'Petugas Triase Primer' : 'Petugas Triase Sekunder' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-0 border border-dark" colspan="3">
                                            Tanggal & Jam
                                        </td>
                                        <td class="py-0 border border-dark" colspan="7">
                                            {{ $primer != null ? \Carbon\Carbon::parse($primer->tanggaltriase)->format('d-m-Y H:i:s') : \Carbon\Carbon::parse($sekunder->tanggaltriase)->format('d-m-Y H:i:s') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-0 border border-dark" colspan="3">
                                            Catatan
                                        </td>
                                        <td class="py-0 border border-dark" colspan="7">
                                            {{ $primer != null ? $primer->catatan : $sekunder->catatan }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-0 border border-dark" colspan="3">
                                            Dokter/Petugas Jaga IGD
                                        </td>
                                        <td class="py-0 border border-dark" colspan="7">
                                            @php
                                                if (!empty($primer)) {
                                                    $nip_petugas = $primer->nip;
                                                    $nama_petugas = $primer->nama;
                                                    $tanggal_hasil = \Carbon\Carbon::parse($primer->tanggaltriase)->format('d-m-Y');
                                                } else {
                                                    $nip_petugas = $sekunder->nip;
                                                    $nama_petugas = $sekunder->nama;
                                                    $tanggal_hasil = \Carbon\Carbon::parse($sekunder->tanggaltriase)->format('d-m-Y');
                                                }
                                                $qr_petugas = 'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara elektronik oleh' . "\n" . $nama_petugas . "\n" . 'ID ' . $nip_petugas . "\n" . $tanggal_hasil;
                                            @endphp
                                            <div>
                                                {{ $primer != null ? $primer->nama : $sekunder->nama }}
                                                <div class="float-right pt-3 pb-3">{!! QrCode::size(100)->generate($qr_petugas) !!}</div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
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
