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
                        <div class="card-header">
                            <div class="float-right">
                                <a href="/vedikanon/ranap/{{ Crypt::encrypt($pasien->no_rawat) }}/detailpdf"
                                    class="btn btn-primary btn-sm" target="_blank">
                                    <i class="far fa-file-pdf"></i> PDF</a>
                                </a>
                            </div>
                        </div>
                    </div>
                    {{-- Data Billing --}}
                    <div class="card">
                        <div class="card-header">Billing</div>
                        @if ($billing->count() > 0)
                            <div class="card-body">
                                <table class="table table-borderless mb-3">
                                    <tr>
                                        <td class="align-top" style="width:60%" rowspan="4"><img
                                                src="{{ asset('image/kemenkes_logo_horisontal.png') }}" alt="Logo RSUP"
                                                width="350">
                                        </td>
                                        <td class="pt-1 pb-0 align-middle"
                                            style="font-family: 'Segoe UI', Arial, sans-serif; font-weight: bold;">
                                            <div style="font-size: 18pt; color:#14bccc;">Kementerian
                                                Kesehatan</div>
                                            <div style="font-size: 14pt; color:#057c86; margin-top:-5pt">RS Surakarta
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-middle py-0">
                                            <img src="{{ asset('image/gps.png') }}" alt="pin lokasi" width="20"> Jalan
                                            Prof. Dr. R.Soeharso Nomor 28 Surakarta 57144
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-middle py-0">
                                            <img src="{{ asset('image/telephone.png') }}" alt="pin lokasi" width="17">
                                            (0271)
                                            713055
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-middle py-0">
                                            <img src="{{ asset('image/world-wide-web.png') }}" alt="pin lokasi" width="17">
                                            https://web.rsupsurakarta.co.id
                                        </td>
                                    </tr>

                                </table>
                                <div class="progress progress-xs mt-0 pt-0">
                                    <div class="progress-bar progress-bar bg-black" role="progressbar" aria-valuenow="100"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                                    </div>
                                </div>
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
                                        @foreach ($billing as $data)
                                            <tr>
                                                @if (
                                                    $data->status == 'TtlObat' ||
                                                        $data->status == 'TtlKamar' ||
                                                        $data->status == 'TtlRanap Dokter' ||
                                                        $data->status == 'TtlLaborat' ||
                                                        $data->status == 'TtlRadiologi' ||
                                                        $data->status == 'TtlRetur Obat' ||
                                                        $data->status == 'TtlOperasi')
                                                    <td class="pt-0 pb-0 text-right text-bold" colspan="7">
                                                        {{ $data->nm_perawatan != null ? $data->nm_perawatan : '' }}</td>
                                                @elseif($data->status == 'Dokter' && $status_dokter == 0)
                                                    <td class="pt-0 pb-0">Dokter</td>
                                                    <td class="pt-0 pb-0" colspan="6">
                                                        {{ $data->nm_perawatan != null ? ": $data->nm_perawatan" : '' }}
                                                    </td>
                                                    @php
                                                        $status_dokter = 1;
                                                    @endphp
                                                @elseif($data->status == 'Dokter' && $status_dokter == 1)
                                                    <td class="pt-0 pb-0"></td>
                                                    <td class="pt-0 pb-0" colspan="6">
                                                        {{ $data->nm_perawatan != null ? ": $data->nm_perawatan" : '' }}
                                                    </td>
                                                @elseif ($data->no_status != 'Dokter ')
                                                    <td class="pt-0 pb-0">
                                                        {{ $data->no_status != null ? $data->no_status : '' }}</td>
                                                    <td class="pt-0 pb-0">
                                                        {{ $data->nm_perawatan != null ? $data->nm_perawatan : '' }}</td>
                                                    <td class="pt-0 pb-0">
                                                        {{ $data->pemisah != null ? $data->pemisah : '' }}
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
                            </div>
                        @endif
                    </div>
                    {{-- End Billing --}}
                    {{-- Data SPRI --}}
                    {{-- @if ($spri)
                        <div class="card">
                            <div class="card-header">Surat Perintah Rawat Inap</div>
                            <div class="card-body">
                                <table class="table table-borderless mb-3">
                                    <tr>
                                        <td class="align-top" style="width:60%" rowspan="4"><img
                                                src="{{ asset('image/kemenkes_logo_horisontal.png') }}" alt="Logo RSUP"
                                                width="350">
                                        </td>
                                        <td class="pt-1 pb-0 align-middle"
                                            style="font-family: 'Segoe UI', Arial, sans-serif; font-weight: bold;">
                                            <div style="font-size: 18pt; color:#14bccc;">Kementerian
                                                Kesehatan</div>
                                            <div style="font-size: 14pt; color:#057c86; margin-top:-5pt">RS Surakarta
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-middle py-0">
                                            <img src="{{ asset('image/gps.png') }}" alt="pin lokasi" width="20"> Jalan
                                            Prof. Dr. R.Soeharso Nomor 28 Surakarta 57144
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-middle py-0">
                                            <img src="{{ asset('image/telephone.png') }}" alt="pin lokasi" width="17">
                                            (0271)
                                            713055
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-middle py-0">
                                            <img src="{{ asset('image/world-wide-web.png') }}" alt="pin lokasi" width="17">
                                            https://web.rsupsurakarta.co.id
                                        </td>
                                    </tr>

                                </table>
                                <div class="progress progress-xs mt-0 pt-0">
                                    <div class="progress-bar progress-bar bg-black" role="progressbar" aria-valuenow="100"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                                    </div>
                                </div>
                                <table class="table table-borderless table-sm ml-3">
                                    <thead>
                                        <tr>
                                            <th class="align-middle text-center pb-1" colspan="7">
                                                <h5><b><u>SURAT PERINTAH RAWAT INAP</u></b></h5>
                                                <div style="margin-top: -5pt">No : {{ $spri->no_rawat }}</div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="width: 20%">Pasien dikirim dari</td>
                                            <td>: {{ $pasien->nm_poli }}</td>
                                        </tr>
                                        <tr>
                                            <td>Hari/ Tanggal/ Jam</td>
                                            <td>: {{ $spri->tanggal }} {{ $spri->jam }}</td>
                                        </tr>
                                        <tr>
                                            <td>Nama Pasien</td>
                                            <td>: {{ $pasien->nm_pasien }}</td>
                                        </tr>
                                        <tr>
                                            <td>No. RM</td>
                                            <td>: {{ $pasien->no_rkm_medis }}</td>
                                        </tr>
                                        <tr>
                                            <td>Diagnosa Kerja</td>
                                            <td>: {{ $spri->diagnosa }}</td>
                                        </tr>
                                        <tr>
                                            <td>Indikasi Rawat</td>
                                            <td>: {{ $spri->catatan }}</td>
                                        </tr>
                                        <tr>
                                            <td>Tindakan yang akan dilakukan
                                                dan alternatifnya</td>
                                            <td>: {{ $spri->tindakan }}</td>
                                        </tr>
                                        <tr>
                                            <td>Perkiraan hasil yang</td>
                                            <td>: {{ $spri->perkiraan_hasil }}</td>
                                        </tr>
                                        <tr>
                                            <td>Cara Bayar</td>
                                            <td>: {{ $pasien->png_jawab }}</td>
                                        </tr>
                                        <tr>
                                            <td>Perkiraan biaya yang</td>
                                            <td>: {{ $spri->perkiraan_biaya }}</td>
                                        </tr>
                                        <tr>
                                            <td>Bangsal / Ruang / Kelas</td>
                                            <td>: {{ $spri->kd_kamar }} {{ $spri->nm_bangsal }}</td>
                                        </tr>
                                        <tr>
                                            <td>Nama DPJP pasien</td>
                                            <td class="mb-3">: {{ $spri->nm_dokter }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Informasi rencana perawatan hasil yang diharapkan dapat berubah
                                                selama perawatan rawat inap sesuai dengan
                                                perkembangan kondisi pasien.</td>
                                        </tr>
                                    </tbody>

                                </table>
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td style="width: 70%"></td>
                                        <td class="text-center">
                                            Surakarta,
                                            {{ \Carbon\Carbon::parse($spri->tanggal)->format('d-m-Y') }}<br>
                                            Dokter Pengirim
                                        </td>
                                    </tr>
                                    <tr>
                                        @php
                                            $qr_dokter =
                                                'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara
                                        elektronik oleh' .
                                                "\n" .
                                                $spri->nm_dokter .
                                                "\n" .
                                                'ID ' .
                                                $spri->kd_dokter .
                                                "\n" .
                                                \Carbon\Carbon::parse($spri->tanggal)->format('d-m-Y');

                                        @endphp
                                        <td style="width: 70%"></td>
                                        <td class="text-center pt-0 pb-0"> {!! QrCode::size(100)->generate($qr_dokter) !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td class="text-center"> ({{ $spri->nm_dokter }}) </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    @endif --}}
                    {{-- End SPRI --}}
                    {{-- Data Lab --}}
                    @if ($hasilLab != null)
                        @if ($hasilLab->count() > 0)
                            <div class="card card-primary card-outline card-outline-tabs">
                                <div class="card-header p-0 border-bottom-0">
                                    <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                                        @foreach ($permintaanLab as $order)
                                            {{-- @if ($order->status == 'ranap') --}}
                                            @if (!isset($index))
                                                @php
                                                    $index = 0;
                                                @endphp
                                            @endif
                                            <li class="nav-item">
                                                <a class="nav-link {{ $index == 0 ? 'active' : '' }}"
                                                    id="custom-tabs-four-home-tab" data-toggle="pill"
                                                    href="#custom-tabs-lap-{{ $order->noorder }}" role="tab"
                                                    aria-controls="custom-tabs-four-home" aria-selected="true"> Hasil
                                                    Lab {{ $order->noorder }}</a>
                                            </li>
                                            @php
                                                if (isset($index)) {
                                                    ++$index;
                                                }
                                            @endphp
                                            {{-- @endif --}}
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content" id="custom-tabs-four-tabContent">
                                        @foreach ($permintaanLab as $order)
                                            {{-- @if ($order->status == 'ranap') --}}
                                            @if (!isset($index2))
                                                @php
                                                    $index2 = 0;

                                                @endphp
                                            @endif
                                            <div class="tab-pane fade show {{ $index2 == 0 ? 'active' : '' }}"
                                                id="custom-tabs-lap-{{ $order->noorder }}" role="tabpanel"
                                                aria-labelledby="#custom-tabs-lap-{{ $order->noorder }}">

                                                <table class="table table-borderless mb-3">
                                                    <tr>
                                                        <td class="align-top" style="width:60%" rowspan="4"><img
                                                                src="{{ asset('image/kemenkes_logo_horisontal.png') }}"
                                                                alt="Logo RSUP" width="350">
                                                        </td>
                                                        <td class="pt-1 pb-0 align-middle"
                                                            style="font-family: 'Segoe UI', Arial, sans-serif; font-weight: bold;">
                                                            <div style="font-size: 18pt; color:#14bccc;">Kementerian
                                                                Kesehatan</div>
                                                            <div style="font-size: 14pt; color:#057c86; margin-top:-5pt">RS
                                                                Surakarta
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-middle py-0">
                                                            <img src="{{ asset('image/gps.png') }}" alt="pin lokasi"
                                                                width="20"> Jalan
                                                            Prof. Dr. R.Soeharso Nomor 28 Surakarta 57144
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-middle py-0">
                                                            <img src="{{ asset('image/telephone.png') }}" alt="pin lokasi"
                                                                width="17">
                                                            (0271)
                                                            713055
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-middle py-0">
                                                            <img src="{{ asset('image/world-wide-web.png') }}" alt="pin lokasi"
                                                                width="17">
                                                            https://web.rsupsurakarta.co.id
                                                        </td>
                                                    </tr>

                                                </table>
                                                <div class="progress progress-xs mt-0 pt-0">
                                                    <div class="progress-bar progress-bar bg-black" role="progressbar"
                                                        aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"
                                                        style="width: 100%">

                                                    </div>
                                                </div>
                                                <table class="table table-borderless table-sm py-0 ">
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
                                                            <td class="pt-0 pb-0" style="width: 45%">:
                                                                {{ $pasien->no_rkm_medis }}</td>
                                                            <td class="pt-0 pb-0" style="width: 15%">No.Permintaan Lab
                                                            </td>
                                                            <td class="pt-0 pb-0" style="width: 25%">:
                                                                {{ $order->noorder }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="pt-0 pb-0">Nama Pasien</td>
                                                            <td class="pt-0 pb-0">: {{ $pasien->nm_pasien }}</td>
                                                            <td class="pt-0 pb-0">Tgl.Permintaan</td>
                                                            <td class="pt-0 pb-0">:
                                                                {{ \Carbon\Carbon::parse($order->tgl_permintaan)->format('d-m-Y') }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="pt-0 pb-0">JK/Umur</td>
                                                            <td class="pt-0 pb-0">:
                                                                {{ $pasien->jk == 'L' ? 'Laki-laki' : 'Perempuan' }} /
                                                                {{-- {{ $data->umurdaftar }} {{ $data->sttsumur }} --}}
                                                                {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->diff(\Carbon\Carbon::parse($pasien->tgl_registrasi))->format('%y
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                Th %m Bl %d Hr') }}
                                                            </td>
                                                            <td class="pt-0 pb-0">Jam Permintaan</td>
                                                            <td class="pt-0 pb-0">: {{ $order->jam_permintaan }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="pt-0 pb-0">Alamat</td>
                                                            <td class="pt-0 pb-0">: {{ $pasien->alamat }}</td>
                                                            <td class="pt-0 pb-0">Tgl. Keluar Hasil</td>
                                                            <td class="pt-0 pb-0">:
                                                                {{ \Carbon\Carbon::parse($order->tgl_hasil)->format('d-m-Y') }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="pt-0 pb-0">No.Periksa</td>
                                                            <td class="pt-0 pb-0">: {{ $pasien->no_rawat }}</td>
                                                            <td class="pt-0 pb-0">Jam Keluar Hasil</td>
                                                            <td class="pt-0 pb-0">: {{ $order->jam_hasil }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="pt-0 pb-0">Dokter Pengirim</td>
                                                            <td class="pt-0 pb-0">: {{ $order->nm_dokter ? $order->nm_dokter:'' }}
                                                            </td>
                                                            @if ($order->status == 'ranap')
                                                                <td class="pt-0 pb-0">Kamar</td>
                                                                <td class="pt-0 pb-0">: {{ $pasien->kd_kamar }}
                                                                    {{ $pasien->nm_bangsal }}</td>
                                                            @else
                                                                <td class="pt-0 pb-0">Poli</td>
                                                                <td class="pt-0 pb-0">: {{ $pasien->nm_poli }}</td>
                                                            @endif
                                                        </tr>
                                                    </tbody>
                                                </table>

                                                <table class="table table-bordered table-sm mb-0 pb-0">
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
                                                            $hasil_lab = 0;
                                                        @endphp
                                                        @foreach ($hasilLab as $hasil)
                                                            @if ($hasil->jam == $order->jam_hasil)
                                                                <tr>
                                                                    <td
                                                                        class="pt-0 pb-0 border border-dark border-top-0 border-bottom-0">
                                                                        {{ $hasil->Pemeriksaan != null ? $hasil->Pemeriksaan : '' }}
                                                                    </td>
                                                                    <td
                                                                        class="pt-0 pb-0 text-center border border-dark border-top-0 border-bottom-0
                                                                                {{ $hasil->keterangan == 'H' ? 'text-danger' : '' }}
                                                                                {{ $hasil->keterangan == 'L' ? 'text-primary' : '' }}
                                                                                {{ $hasil->keterangan == 'T' ? 'text-bold' : '' }}">
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
                                                                    <td
                                                                        class="pt-0 pb-0 border border-dark border-top-0 border-bottom-0">
                                                                        {{ $hasil->keterangan != null ? $hasil->keterangan : '' }}
                                                                    </td>
                                                                </tr>
                                                                @php
                                                                    $hasil_lab = 1;
                                                                @endphp
                                                            @endif
                                                        @endforeach
                                                        @if ($hasil_lab == 0)
                                                            <tr>
                                                                <td colspan="5" class="text-center">Belum ada hasil
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    </tbody>

                                                </table>
                                                <table style="width: 100%; margin-top:0;">
                                                    @foreach ($kesanLab as $kesan)
                                                        @if ($kesan->jam == $order->jam_hasil)
                                                            <tr>
                                                                <td style="width: 5%; border-bottom: 1px solid black">
                                                                    Kesan</td>
                                                                <td style="width: 95%; border-bottom: 1px solid black">
                                                                    : {{ $kesan->kesan }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="width:5%; border-bottom: 1px solid black">
                                                                    Saran</td>
                                                                <td style="width:95%; border-bottom: 1px solid black">
                                                                    : {{ $kesan->saran }}</td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </table>
                                                <div>
                                                    <small><b>Catatan:</b> Jika ada keragu-raguan pemeriksaan, diharapkan
                                                        segera
                                                        menghubungi
                                                        laboratorium.</small>
                                                    {{-- <div class="float-right">Tgl.Cetak :
                                                {{ Carbon\Carbon::now()->format('d/m/Y h:i:s') }}
                                            </div> --}}
                                                </div>
                                                @if ($hasil_lab == 1)
                                                    <table class="table table-borderless">
                                                        <tr>
                                                            <td class="text-center pt-0 pb-0">Penanggung Jawab</td>
                                                            <td class="text-center pt-0 pb-0"> Petugas Laboratorium</td>
                                                        </tr>
                                                        <tr>
                                                            @php
                                                                $dokterLab = \App\Vedika::getDokter(
                                                                    $order->tgl_hasil,
                                                                    $order->jam_hasil
                                                                );

                                                                if($dokterLab){
                                                                $dokter_lab = $dokterLab->nm_dokter;
                                                                    $kd_dokter_lab = $dokterLab->kd_dokter;
                                                                }else{
                                                                    $dokter_lab = '';
                                                                    $kd_dokter_lab = '';
                                                                }


                                                                $petugasLab = \App\Vedika::getPetugas(
                                                                    $order->tgl_hasil,
                                                                    $order->jam_hasil
                                                                );

                                                                if($petugasLab){
                                                                    $petugas_lab = $petugasLab->nama;
                                                                    $kd_petugas_lab = $petugasLab->nip;
                                                                }else{
                                                                    $petugas_lab = '';
                                                                    $kd_petugas_lab = '';
                                                                }

                                                                $qr_dokter =
                                                                    'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara
                                                elektronik oleh' .
                                                                    "\n" .
                                                                    $dokter_lab .
                                                                    "\n" .
                                                                    'ID ' .
                                                                    $kd_dokter_lab .
                                                                    "\n" .
                                                                    \Carbon\Carbon::parse($order->tgl_hasil)->format('d-m-Y');
                                                                $qr_petugas =
                                                                    'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara
                                                elektronik oleh' .
                                                                    "\n" .
                                                                    $petugas_lab .
                                                                    "\n" .
                                                                    'ID ' .
                                                                    $kd_petugas_lab .
                                                                    "\n" .
                                                                    \Carbon\Carbon::parse($order->tgl_hasil)->format('d-m-Y');

                                                            @endphp

                                                            <td class="text-center pt-0 pb-0"> {!! QrCode::size(100)->generate($qr_dokter) !!}
                                                            </td>
                                                            <td class="text-center pt-0 pb-0"> {!! QrCode::size(100)->generate($qr_petugas) !!}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-center pt-0 pb-0">{{ $dokter_lab }}</td>
                                                            <td class="text-center pt-0 pb-0"> {{ $petugas_lab }} </td>
                                                        </tr>
                                                    </table>
                                                @endif
                                            </div>
                                            @php
                                                if (isset($index2)) {
                                                    ++$index2;
                                                }
                                            @endphp
                                            {{-- @endif --}}
                                        @endforeach
                                    </div>
                                </div>
                                <!-- /.card -->
                            </div>
                        @endif
                    @endif
                    {{-- End hasil Lab --}}

                    {{-- Data Radiologi --}}
                    @if ($dataRadiologiRanap != null || $dataRadiologiRajal != null)
                        @if ($dataRadiologiRanap->count() > 0 || count($dataRadiologiRajal) > 0)
                            <div class="card card-primary card-outline card-outline-tabs">
                                <div class="card-header p-0 border-bottom-0">
                                    <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                                        @php
                                            $index = 0;

                                        @endphp
                                        @if (!empty($dataRadiologiRajal))
                                            @foreach ($dataRadiologiRajal as $nourut => $detailRadioRajal)
                                                <li class="nav-item">
                                                    @php
                                                        // dd($dokterRadiologiRajal[$nourut]->tanggal);
                                                        $tgl_hasil = $dokterRadiologiRajal[$nourut]->tgl_periksa;
                                                        $jam_hasil = $dokterRadiologiRajal[$nourut]->jam;
                                                        $tab = \Carbon\Carbon::parse("$tgl_hasil $jam_hasil")->format('YmdHis');
                                                        // dd($tab);
                                                    @endphp
                                                    <a class="nav-link {{ $index == 0 ? 'active' : '' }}"
                                                        id="custom-tabs-four-home-tab" data-toggle="pill"
                                                        href="#custom-tabs-lap-{{ $tab }}" role="tab"
                                                        aria-controls="custom-tabs-four-home" aria-selected="true"> Hasil
                                                        Radiologi {{ $detailRadioRajal->noorder }}</a>
                                                </li>
                                                @php
                                                    $index++;
                                                @endphp
                                            @endforeach
                                        @endif
                                        @foreach ($dataRadiologiRanap as $order)
                                            <li class="nav-item">
                                                <a class="nav-link {{ $index == 0 ? 'active' : '' }}"
                                                    id="custom-tabs-four-home-tab" data-toggle="pill"
                                                    href="#custom-tabs-lap-{{ $order->noorder }}-{{ $order->kd_jenis_prw }}" role="tab"
                                                    aria-controls="custom-tabs-four-home" aria-selected="true"> Hasil
                                                    Radiologi {{ $order->noorder }}</a>
                                            </li>
                                            @php
                                                ++$index;
                                            @endphp
                                        @endforeach
                                        @if (!empty($tambahanDataRadiologi))
                                            @foreach ($tambahanDataRadiologi as $nourut => $detailRadioTambahan)
                                                <li class="nav-item">
                                                    @php
                                                        // dd($tambahanDokterRadiologi[$nourut]);
                                                        $tgl_hasil = $tambahanDokterRadiologi[$nourut]->tgl_periksa;
                                                        $jam_hasil = $tambahanDokterRadiologi[$nourut]->jam;
                                                        $tab = \Carbon\Carbon::parse("$tgl_hasil $jam_hasil")->format('YmdHis');
                                                        // dd($tab);
                                                    @endphp
                                                    <a class="nav-link {{ $index == 0 ? 'active' : '' }}"
                                                        id="custom-tabs-four-home-tab" data-toggle="pill"
                                                        href="#custom-tabs-lap-{{ $tab }}" role="tab"
                                                        aria-controls="custom-tabs-four-home" aria-selected="true"> Hasil
                                                        Radiologi {{ $detailRadioTambahan->noorder }}</a>
                                                </li>
                                                @php
                                                    $index++;
                                                @endphp
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content" id="custom-tabs-four-tabContent">
                                        @php
                                            $index2 = 0;
                                        @endphp
                                        @if (!empty($dataRadiologiRajal))
                                            @foreach ($dataRadiologiRajal as $urutan => $radioRajal)
                                                @php
                                                    $tgl_hasil = $dokterRadiologiRajal[$urutan]->tgl_periksa;
                                                    $jam_hasil = $dokterRadiologiRajal[$urutan]->jam;
                                                    $tab = \Carbon\Carbon::parse("$tgl_hasil $jam_hasil")->format('YmdHis');

                                                @endphp
                                                <div class="tab-pane fade show {{ $index2 == 0 ? 'active' : '' }}"
                                                    id="custom-tabs-lap-{{ $tab }}" role="tabpanel"
                                                    aria-labelledby="#custom-tabs-lap-{{ $tab }}">

                                                    <table class="table table-borderless mb-3">
                                                        <tr>
                                                            <td class="align-top" style="width:60%" rowspan="4"><img
                                                                    src="{{ asset('image/kemenkes_logo_horisontal.png') }}"
                                                                    alt="Logo RSUP" width="350">
                                                            </td>
                                                            <td class="pt-1 pb-0 align-middle"
                                                                style="font-family: 'Segoe UI', Arial, sans-serif; font-weight: bold;">
                                                                <div style="font-size: 18pt; color:#14bccc;">Kementerian
                                                                    Kesehatan</div>
                                                                <div style="font-size: 14pt; color:#057c86; margin-top:-5pt">RS
                                                                    Surakarta
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="align-middle py-0">
                                                                <img src="{{ asset('image/gps.png') }}" alt="pin lokasi"
                                                                    width="20"> Jalan
                                                                Prof. Dr. R.Soeharso Nomor 28 Surakarta 57144
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="align-middle py-0">
                                                                <img src="{{ asset('image/telephone.png') }}" alt="pin lokasi"
                                                                    width="17">
                                                                (0271)
                                                                713055
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="align-middle py-0">
                                                                <img src="{{ asset('image/world-wide-web.png') }}"
                                                                    alt="pin lokasi" width="17">
                                                                https://web.rsupsurakarta.co.id
                                                            </td>
                                                        </tr>

                                                    </table>
                                                    <div class="progress progress-xs mt-0 pt-0">
                                                        <div class="progress-bar progress-bar bg-black" role="progressbar"
                                                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"
                                                            style="width: 100%">

                                                        </div>
                                                    </div>
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
                                                                <td class="pt-0 pb-0">: {{ $pasien->no_rkm_medis }}</td>
                                                                <td class="pt-0 pb-0">Penanggung Jawab</td>
                                                                <td class="pt-0 pb-0">:
                                                                    {{ !empty($dokterRadiologiRajal[$urutan]->nm_dokter) ? $dokterRadiologiRajal[$urutan]->nm_dokter : '' }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="pt-0 pb-0">Nama Pasien</td>
                                                                <td class="pt-0 pb-0">: {{ $pasien->nm_pasien }}</td>
                                                                <td class="pt-0 pb-0">Dokter Pengirim</td>
                                                                <td class="pt-0 pb-0">:
                                                                    {{ !empty($radioRajal->nm_dokter) ? $radioRajal->nm_dokter : '' }}
                                                                </td>

                                                            </tr>
                                                            <tr>
                                                                <td class="pt-0 pb-0">JK/Umur</td>
                                                                <td class="pt-0 pb-0">:
                                                                    {{ $pasien->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                                                    /
                                                                    {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->diff(\Carbon\Carbon::parse($radioRajal->tgl_hasil))->format('%y
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            Th %m Bl %d Hr') }}
                                                                </td>
                                                                <td class="pt-0 pb-0">Tgl.Pemeriksaan</td>
                                                                <td class="pt-0 pb-0">:
                                                                    {{ \Carbon\Carbon::parse($radioRajal->tgl_hasil)->format('d-m-Y') }}
                                                                </td>

                                                            </tr>
                                                            <tr>
                                                                <td class="pt-0 pb-0">Alamat</td>
                                                                <td class="pt-0 pb-0">: {{ $radioRajal->alamat }}</td>
                                                                <td class="pt-0 pb-0">Jam Pemeriksaan</td>
                                                                <td class="pt-0 pb-0">: {{ $dokterRadiologiRajal[$urutan]->jam }}
                                                                </td>

                                                            </tr>
                                                            <tr>
                                                                <td class="pt-0 pb-0">No.Periksa</td>
                                                                <td class="pt-0 pb-0">: {{ $radioRajal->no_rawat }}</td>
                                                                <td class="pt-0 pb-0">Poli</td>
                                                                <td class="pt-0 pb-0">: {{ $radioRajal->nm_poli }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="pt-0 pb-0">Pemeriksaan</td>
                                                                <td class="pt-0 pb-0">:
                                                                    {{ $dokterRadiologiRajal[$urutan]->nm_perawatan }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="pt-0 pb-0">Hasil Pemeriksaan</td>
                                                            </tr>
                                                        </tbody>

                                                    </table>
                                                    @if (!empty($hasilRadiologiRajal[$urutan]->hasil))
                                                        @php
                                                            $paragraphs = explode("\n", $hasilRadiologiRajal[$urutan]->hasil);
                                                            $tinggi = 25 * count($paragraphs);
                                                        @endphp
                                                    @endif
                                                    <table class="table table-bordered">
                                                        <tbody class="border border-dark">
                                                            <tr>
                                                                <textarea class="form-control" readonly
                                                                    style="
                                                    min-height: {{ !empty($tinggi) ? $tinggi : '50' }}px;
                                                    resize: none;
                                                    overflow-y:hidden;
                                                    border:1px solid black;
                                                    background-color: white;
                                                ">{{ !empty($hasilRadiologiRajal[$urutan]->hasil) ? $hasilRadiologiRajal[$urutan]->hasil : '' }}</textarea>
                                                            </tr>
                                                        </tbody>

                                                    </table>
                                                    @if (!empty($dokterRadiologiRajal[$urutan]->nm_dokter))
                                                        <table class="table table-borderless mt-1">
                                                            <tr>
                                                                <td class="text-center pt-0 pb-0" style="width: 70%"></td>
                                                                <td class="text-center pt-0 pb-0" style="width: 30%">Dokter
                                                                    Radiologi
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                @php
                                                                    $qr_dokter =
                                                                        'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara
                                                elektronik oleh' .
                                                                        "\n" .
                                                                        $dokterRadiologiRajal[$urutan]->nm_dokter .
                                                                        "\n" .
                                                                        'ID ' .
                                                                        $dokterRadiologiRajal[$urutan]->kd_dokter .
                                                                        "\n" .
                                                                        \Carbon\Carbon::parse(
                                                                            $dokterRadiologiRajal[$urutan]->tgl_periksa
                                                                        )->format('d-m-Y');
                                                                @endphp
                                                                <td class="text-center pt-0 pb-0" style="width: 70%"></td>
                                                                <td class="text-center pt-0 pb-0" style="width: 30%">
                                                                    {!! QrCode::size(100)->generate($qr_dokter) !!}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-center pt-0 pb-0" style="width: 70%"></td>
                                                                <td class="text-center pt-0 pb-0" style="width: 30%">
                                                                    {{ $dokterRadiologiRajal[$urutan]->nm_dokter }}
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    @endif
                                                </div>
                                                @php
                                                    ++$index2;
                                                @endphp
                                            @endforeach
                                        @endif
                                        @foreach ($dataRadiologiRanap as $urutan => $order)
                                            <div class="tab-pane fade show {{ $index2 == 0 ? 'active' : '' }}"
                                                id="custom-tabs-lap-{{ $order->noorder }}-{{ $order->kd_jenis_prw }}" role="tabpanel"
                                                aria-labelledby="#custom-tabs-lap-{{ $order->noorder }}-{{ $order->kd_jenis_prw }}">
                                                @php
                                                    // dd($order);
                                                    $dokterRadiologi = \App\Vedika::getRadioDokter(
                                                        $order->no_rawat,
                                                        $order->jam_hasil
                                                    );
                                                @endphp

                                                <table class="table table-borderless mb-3">
                                                    <tr>
                                                        <td class="align-top" style="width:60%" rowspan="4"><img
                                                                src="{{ asset('image/kemenkes_logo_horisontal.png') }}"
                                                                alt="Logo RSUP" width="350">
                                                        </td>
                                                        <td class="pt-1 pb-0 align-middle"
                                                            style="font-family: 'Segoe UI', Arial, sans-serif; font-weight: bold;">
                                                            <div style="font-size: 18pt; color:#14bccc;">Kementerian
                                                                Kesehatan</div>
                                                            <div style="font-size: 14pt; color:#057c86; margin-top:-5pt">RS
                                                                Surakarta
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-middle py-0">
                                                            <img src="{{ asset('image/gps.png') }}" alt="pin lokasi"
                                                                width="20"> Jalan
                                                            Prof. Dr. R.Soeharso Nomor 28 Surakarta 57144
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-middle py-0">
                                                            <img src="{{ asset('image/telephone.png') }}" alt="pin lokasi"
                                                                width="17">
                                                            (0271)
                                                            713055
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-middle py-0">
                                                            <img src="{{ asset('image/world-wide-web.png') }}" alt="pin lokasi"
                                                                width="17">
                                                            https://web.rsupsurakarta.co.id
                                                        </td>
                                                    </tr>

                                                </table>
                                                <div class="progress progress-xs mt-0 pt-0">
                                                    <div class="progress-bar progress-bar bg-black" role="progressbar"
                                                        aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"
                                                        style="width: 100%">
                                                    </div>
                                                </div>
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
                                                            <td class="pt-0 pb-0">: {{ $pasien->no_rkm_medis }}</td>
                                                            <td class="pt-0 pb-0">Penanggung Jawab</td>
                                                            <td class="pt-0 pb-0">:
                                                                {{ !empty($dokterRadiologiRanap[$urutan]->nm_dokter) ? $dokterRadiologiRanap[$urutan]->nm_dokter : '' }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="pt-0 pb-0">Nama Pasien</td>
                                                            <td class="pt-0 pb-0">: {{ $pasien->nm_pasien }}</td>
                                                            <td class="pt-0 pb-0">Dokter Pengirim</td>
                                                            <td class="pt-0 pb-0">:
                                                                {{ !empty($order->nm_dokter) ? $order->nm_dokter : '' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="pt-0 pb-0">JK/Umur</td>
                                                            <td class="pt-0 pb-0">:
                                                                {{ $pasien->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                                                /
                                                                {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->diff(\Carbon\Carbon::parse($order->tgl_hasil))->format('%y
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                Th %m Bl %d Hr') }}
                                                            </td>
                                                            <td class="pt-0 pb-0">Tgl.Pemeriksaan</td>
                                                            <td class="pt-0 pb-0">:
                                                                {{ \Carbon\Carbon::parse($order->tgl_hasil)->format('d-m-Y') }}
                                                            </td>

                                                        </tr>
                                                        <tr>
                                                            <td class="pt-0 pb-0">Alamat</td>
                                                            <td class="pt-0 pb-0">: {{ $order->almt_pj }}</td>
                                                            <td class="pt-0 pb-0">Jam Pemeriksaan</td>
                                                            <td class="pt-0 pb-0">: {{ $order->jam_hasil }}</td>

                                                        </tr>
                                                        <tr>
                                                            <td class="pt-0 pb-0">No.Periksa</td>
                                                            <td class="pt-0 pb-0">: {{ $order->no_rawat }}</td>
                                                            <td class="pt-0 pb-0">Kamar</td>
                                                            <td class="pt-0 pb-0">:
                                                                {{ !empty($dokterRadiologi->kd_kamar) ? $dokterRadiologi->kd_kamar : '' }},
                                                                {{ !empty($dokterRadiologi->nm_bangsal) ? $dokterRadiologi->nm_bangsal : '' }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="pt-0 pb-0">Pemeriksaan</td>
                                                            <td class="pt-0 pb-0">: {{ $order->nm_perawatan }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="pt-0 pb-0">Hasil Pemeriksaan</td>
                                                        </tr>
                                                    </tbody>

                                                </table>
                                                @foreach ($hasilRadiologiRanap as $dataHasil)
                                                    @if (!empty($dataHasil->hasil) && $dataHasil->jam == $order->jam_hasil)
                                                        @php
                                                            $paragraphs = explode("\n", $dataHasil->hasil);
                                                            $tinggi = 25 * count($paragraphs);

                                                        @endphp

                                                        <table class="table table-bordered">
                                                            <tbody class="border border-dark">
                                                                <tr>
                                                                    <textarea class="form-control" readonly
                                                                        style="
                                                    min-height: {{ !empty($tinggi) ? $tinggi : '50' }}px;
                                                    resize: none;
                                                    overflow-y:hidden;
                                                    border:1px solid black;
                                                    background-color: white;
                                                ">
                                                {{ $dataHasil->jam == $order->jam_hasil ? $dataHasil->hasil : '' }}

                                            </textarea>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    @endif
                                                @endforeach
                                                {{-- @if (!empty($hasilRadiologiRanap[$urutan]->hasil))
                                        @php
                                        $paragraphs = explode("\n", $hasilRadiologiRanap[$urutan]->hasil);
                                        $tinggi = 25 * count($paragraphs);

                                        @endphp
                                        @endif
                                        <table class="table table-bordered">
                                            <tbody class="border border-dark">
                                                <tr>
                                                    <textarea class="form-control" readonly
                                                        style="
                                                        min-height: {{ !empty($tinggi) ? $tinggi : '50' }}px;
                                                        resize: none;
                                                        overflow-y:hidden;
                                                        border:1px solid black;
                                                        background-color: white;
                                                    ">{{ !empty($hasilRadiologiRanap[$urutan]->hasil) ? $hasilRadiologiRanap[$urutan]->hasil : '' }}</textarea>
                                                </tr>
                                            </tbody>
                                        </table> --}}

                                                @if (!empty($dokterRadiologiRanap[$urutan]->nm_dokter))
                                                    <table class="table table-borderless mt-1">
                                                        <tr>
                                                            <td class="text-center pt-0 pb-0" style="width: 70%"></td>
                                                            <td class="text-center pt-0 pb-0" style="width: 30%">Dokter Radiologi
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            @php
                                                                $qr_dokter =
                                                                    'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara
                                                elektronik oleh' .
                                                                    "\n" .
                                                                    $dokterRadiologiRanap[$urutan]->nm_dokter .
                                                                    "\n" .
                                                                    'ID ' .
                                                                    $dokterRadiologiRanap[$urutan]->kd_dokter .
                                                                    "\n" .
                                                                    \Carbon\Carbon::parse(
                                                                        $dokterRadiologiRanap[$urutan]->tgl_periksa
                                                                    )->format('d-m-Y');
                                                            @endphp
                                                            <td class="text-center pt-0 pb-0" style="width: 70%"></td>
                                                            <td class="text-center pt-0 pb-0" style="width: 30%">
                                                                {!! QrCode::size(100)->generate($qr_dokter) !!}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-center pt-0 pb-0" style="width: 70%"></td>
                                                            <td class="text-center pt-0 pb-0" style="width: 30%">
                                                                {{ $dokterRadiologiRanap[$urutan]->nm_dokter }}
                                                            </td>
                                                        </tr>
                                                    </table>
                                                @endif
                                            </div>
                                            @php
                                                ++$index2;
                                            @endphp
                                        @endforeach

                                        @if (!empty($tambahanDataRadiologi))
                                            @foreach ($tambahanDataRadiologi as $urutan => $tambahanData)
                                                @php
                                                    $tgl_hasil = $tambahanDokterRadiologi[$urutan]->tgl_periksa;
                                                    $jam_hasil = $tambahanDokterRadiologi[$urutan]->jam;
                                                    $tab = \Carbon\Carbon::parse("$tgl_hasil $jam_hasil")->format('YmdHis');

                                                @endphp
                                                <div class="tab-pane fade show {{ $index2 == 0 ? 'active' : '' }}"
                                                    id="custom-tabs-lap-{{ $tab }}" role="tabpanel"
                                                    aria-labelledby="#custom-tabs-lap-{{ $tab }}">

                                                    <table class="table table-borderless mb-3">
                                                        <tr>
                                                            <td class="align-top" style="width:60%" rowspan="4"><img
                                                                    src="{{ asset('image/kemenkes_logo_horisontal.png') }}"
                                                                    alt="Logo RSUP" width="350">
                                                            </td>
                                                            <td class="pt-1 pb-0 align-middle"
                                                                style="font-family: 'Segoe UI', Arial, sans-serif; font-weight: bold;">
                                                                <div style="font-size: 18pt; color:#14bccc;">Kementerian
                                                                    Kesehatan</div>
                                                                <div style="font-size: 14pt; color:#057c86; margin-top:-5pt">RS
                                                                    Surakarta
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="align-middle py-0">
                                                                <img src="{{ asset('image/gps.png') }}" alt="pin lokasi"
                                                                    width="20"> Jalan
                                                                Prof. Dr. R.Soeharso Nomor 28 Surakarta 57144
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="align-middle py-0">
                                                                <img src="{{ asset('image/telephone.png') }}" alt="pin lokasi"
                                                                    width="17">
                                                                (0271)
                                                                713055
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="align-middle py-0">
                                                                <img src="{{ asset('image/world-wide-web.png') }}"
                                                                    alt="pin lokasi" width="17">
                                                                https://web.rsupsurakarta.co.id
                                                            </td>
                                                        </tr>

                                                    </table>
                                                    <div class="progress progress-xs mt-0 pt-0">
                                                        <div class="progress-bar progress-bar bg-black" role="progressbar"
                                                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"
                                                            style="width: 100%">

                                                        </div>
                                                    </div>
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
                                                                <td class="pt-0 pb-0">: {{ $pasien->no_rkm_medis }}</td>
                                                                <td class="pt-0 pb-0">Penanggung Jawab</td>
                                                                <td class="pt-0 pb-0">:
                                                                    {{ !empty($tambahanDokterRadiologi[$urutan]->nm_dokter) ? $tambahanDokterRadiologi[$urutan]->nm_dokter : '' }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="pt-0 pb-0">Nama Pasien</td>
                                                                <td class="pt-0 pb-0">: {{ $pasien->nm_pasien }}</td>
                                                                <td class="pt-0 pb-0">Dokter Pengirim</td>
                                                                <td class="pt-0 pb-0">:
                                                                    {{ !empty($tambahanData->nm_dokter) ? $tambahanData->nm_dokter : '' }}
                                                                </td>

                                                            </tr>
                                                            <tr>
                                                                <td class="pt-0 pb-0">JK/Umur</td>
                                                                <td class="pt-0 pb-0">:
                                                                    {{ $pasien->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                                                    /
                                                                    {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->diff(\Carbon\Carbon::parse($tambahanData->tgl_hasil))->format('%y
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            Th %m Bl %d Hr') }}
                                                                </td>
                                                                <td class="pt-0 pb-0">Tgl.Pemeriksaan</td>
                                                                <td class="pt-0 pb-0">:
                                                                    {{ \Carbon\Carbon::parse($tambahanData->tgl_hasil)->format('d-m-Y') }}
                                                                </td>

                                                            </tr>
                                                            <tr>
                                                                <td class="pt-0 pb-0">Alamat</td>
                                                                <td class="pt-0 pb-0">: {{ $tambahanData->alamat }}</td>
                                                                <td class="pt-0 pb-0">Jam Pemeriksaan</td>
                                                                <td class="pt-0 pb-0">: {{ $tambahanDokterRadiologi[$urutan]->jam }}
                                                                </td>

                                                            </tr>
                                                            <tr>
                                                                <td class="pt-0 pb-0">No.Periksa</td>
                                                                <td class="pt-0 pb-0">: {{ $tambahanData->no_rawat }}</td>
                                                                <td class="pt-0 pb-0">Poli</td>
                                                                <td class="pt-0 pb-0">: {{ $tambahanData->nm_poli }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="pt-0 pb-0">Pemeriksaan</td>
                                                                <td class="pt-0 pb-0">:
                                                                    {{ $tambahanDokterRadiologi[$urutan]->nm_perawatan }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="pt-0 pb-0">Hasil Pemeriksaan</td>
                                                            </tr>
                                                        </tbody>

                                                    </table>
                                                    @if (!empty($tambahanHasilRadiologi[$urutan]->hasil))
                                                        @php
                                                            $paragraphs = explode("\n", $tambahanHasilRadiologi[$urutan]->hasil);
                                                            $tinggi = 25 * count($paragraphs);
                                                        @endphp
                                                    @endif
                                                    <table class="table table-bordered">
                                                        <tbody class="border border-dark">
                                                            <tr>
                                                                <textarea class="form-control" readonly
                                                                    style="
                                                    min-height: {{ !empty($tinggi) ? $tinggi : '50' }}px;
                                                    resize: none;
                                                    overflow-y:hidden;
                                                    border:1px solid black;
                                                    background-color: white;
                                                ">{{ !empty($tambahanHasilRadiologi[$urutan]->hasil) ? $tambahanHasilRadiologi[$urutan]->hasil : '' }}</textarea>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    @if (!empty($tambahanDokterRadiologi[$urutan]->nm_dokter))
                                                        <table class="table table-borderless mt-1">
                                                            <tr>
                                                                <td class="text-center pt-0 pb-0" style="width: 70%"></td>
                                                                <td class="text-center pt-0 pb-0" style="width: 30%">Dokter
                                                                    Radiologi
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                @php
                                                                    $qr_dokter =
                                                                        'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara
                                                elektronik oleh' .
                                                                        "\n" .
                                                                        $tambahanDokterRadiologi[$urutan]->nm_dokter .
                                                                        "\n" .
                                                                        'ID ' .
                                                                        $tambahanDokterRadiologi[$urutan]->kd_dokter .
                                                                        "\n" .
                                                                        \Carbon\Carbon::parse(
                                                                            $tambahanDokterRadiologi[$urutan]->tgl_periksa
                                                                        )->format('d-m-Y');
                                                                @endphp
                                                                <td class="text-center pt-0 pb-0" style="width: 70%"></td>
                                                                <td class="text-center pt-0 pb-0" style="width: 30%">
                                                                    {!! QrCode::size(100)->generate($qr_dokter) !!}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-center pt-0 pb-0" style="width: 70%"></td>
                                                                <td class="text-center pt-0 pb-0" style="width: 30%">
                                                                    {{ $tambahanDokterRadiologi[$urutan]->nm_dokter }}
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    @endif
                                                </div>
                                                @php
                                                    ++$index2;
                                                @endphp
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <!-- /.card -->
                            </div>
                        @endif
                    @endif
                    {{-- End Radiologi --}}



                {{-- Data Resume Ranap --}}
                @if ($resumeRanap1 && $resumeRanap2 && $resumeRanap3 && $resumeRanap4)
                    <style>
                        /* pre {
                                                                            white-space: pre-wrap !important;
                                                                        } */

                        pre {
                            font-family: 'Source Sans Pro';
                            font-size: 12pt;
                            /* white-space: pre-wrap !important; */
                            /* white-space: break-spaces !important; */
                            padding: 0;
                            margin: 0;
                        }

                        .tab1 {
                            tab-size: 2;
                        }
                    </style>

                    <div class="card">
                        <div class="card-header">Resume Medis Pasien</div>
                        <div class="card-body">
                            <table class="table table-borderless mb-3">
                                <tr>
                                    <td class="align-top" style="width:60%" rowspan="4"><img
                                            src="{{ asset('image/kemenkes_logo_horisontal.png') }}" alt="Logo RSUP"
                                            width="350">
                                    </td>
                                    <td class="pt-1 pb-0 align-middle"
                                        style="font-family: 'Segoe UI', Arial, sans-serif; font-weight: bold;">
                                        <div style="font-size: 18pt; color:#14bccc;">Kementerian
                                            Kesehatan</div>
                                        <div style="font-size: 14pt; color:#057c86; margin-top:-5pt">RS Surakarta
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle py-0">
                                        <img src="{{ asset('image/gps.png') }}" alt="pin lokasi" width="20"> Jalan
                                        Prof. Dr. R.Soeharso Nomor 28 Surakarta 57144
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle py-0">
                                        <img src="{{ asset('image/telephone.png') }}" alt="telepon" width="17"> (0271)
                                        713055
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle py-0">
                                        <img src="{{ asset('image/world-wide-web.png') }}" alt="website" width="17">
                                        https://web.rsupsurakarta.co.id
                                    </td>
                                </tr>

                            </table>
                            <div class="progress progress-xs mt-0 pt-0">
                                <div class="progress-bar progress-bar bg-black" role="progressbar" aria-valuenow="100"
                                    aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                                </div>
                            </div>
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th class="align-middle text-center pb-1" colspan="2">
                                            <h5><b>RESUME MEDIS PASIEN</b></h5>

                                        </th>
                                    </tr>
                                </thead>
                            </table>
                            <hr style="height:2px;border-width:0;color:gray;background-color:gray; margin-top:-2pt">
                            <table class="table table-borderless" style="margin-left: 5pt; margin-right:10pt">
                                <tbody>
                                    <tr>
                                        <td class="align-middle py-0" style="width: 15%">Nama Pasien</td>
                                        <td class="align-middle py-0" style="width: 35%">: {{ $pasien->nm_pasien }}</td>
                                        <td class="align-middle py-0" style="width: 15%">No. Rekam Medis</td>
                                        <td class="align-middle py-0" style="width: 35%">: {{ $pasien->no_rkm_medis }}</td>
                                    </tr>
                                    <tr>
                                        <td class="align-middle py-0">Umur</td>
                                        <td class="align-middle py-0">:
                                            {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->diff(\Carbon\Carbon::parse($pasien->tgl_registrasi))->format('%y
                                                                                                                                                                                                                                                                                                                                                                                                                                                            Th %m Bl') }}
                                        </td>
                                        <td class="align-middle py-0">Jenis Kelamin</td>
                                        <td class="align-middle py-0">: {{ $pasien->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-middle py-0">Tgl Lahir</td>
                                        <td class="align-middle py-0">:
                                            {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->format('d-m-Y') }}</td>
                                        <td class="align-middle py-0">Tanggal Masuk</td>
                                        <td class="align-middle py-0">:
                                            {{ $resumeRanap2->first()->waktu_masuk_ranap != '0000-00-00 00:00:00' ? \Carbon\Carbon::parse($resumeRanap2->first()->waktu_masuk_ranap)->format('d-m-Y'):'-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-middle py-0">Alamat</td>
                                        <td class="align-middle py-0">: {{ $pasien->alamat }}</td>
                                        <td class="align-middle py-0">Tanggal Keluar</td>
                                        <td class="align-middle py-0">:
                                            {{ $resumeRanap2->first()->waktu_keluar_ranap != '0000-00-00 00:00:00' ? \Carbon\Carbon::parse($resumeRanap2->last()->waktu_keluar_ranap)->format('d-m-Y'):'-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-middle py-0"></td>
                                        <td class="align-middle py-0"></td>
                                        <td class="align-middle py-0">Ruang</td>
                                        <td class="align-middle py-0">: {{ $resumeRanap2->last()->kd_kamar }}
                                            {{ $resumeRanap2->last()->nm_bangsal }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <hr style="height:2px;border-width:0;color:gray;background-color:gray; margin-top:5pt">
                            <table class="table table-borderless" style="margin-left: 5pt; margin-right:10pt; width:100%">
                                <tbody>
                                    <tr>
                                        <td class="align-top py-0" style="width: 20%">Keluhan Utama Riwayat Penyakit</td>
                                        <td class="align-top py-0" style="width: 2%">:</td>
                                        <td class="align-top py-0"
                                            style="width: 78%; word-wrap: break-word; word-break: break-all;">
                                            {{ $resumeRanap1->keluhan_utama }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top py-0" style="width: 20%">Diagnosis Masuk</td>
                                        <td class="align-top py-0" style="width: 2%">:</td>
                                        <td class="align-top py-0" style="width: 78%">
                                            {{ $resumeRanap1->diagnosa_awal }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top py-0" style="width: 20%">Indikasi Dirawat </td>
                                        <td class="align-top py-0" style="width: 2%">:</td>
                                        <td class="align-top py-0" style="width: 78%">
                                            {{ $resumeRanap1->alasan }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top py-0" style="width: 20%">Alergi</td>
                                        <td class="align-top py-0" style="width: 2%">:</td>
                                        <td class="align-top py-0" style="width: 78%">
                                            {{ $resumeRanap1->alergi }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top py-0" style="width: 20%">Pemeriksaan Fisik</td>
                                        <td class="align-top py-0" style="width: 2%">:</td>
                                        <td class="align-top py-0"
                                            style="width: 78%;  word-wrap: break-word; text-align:justify">
                                            {{ $resumeRanap1->pemeriksaan_fisik }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top py-0" style="width: 20%">Pemeriksaan Penunjang Radiologi
                                        </td>
                                        <td class="align-top py-0" style="width: 2%">:</td>
                                        <td class="align-top py-0"
                                            style="width: 78%;  word-wrap: break-word; text-align:justify">
                                            {{ $resumeRanap1->pemeriksaan_penunjang }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top py-0" style="width: 20%">Pemeriksaan Penunjang Laboratorium
                                        </td>
                                        <td class="align-top py-0" style="width: 2%">:</td>
                                        <td class="align-top py-0" style="width: 78%">
                                            {{ $resumeRanap1->hasil_laborat }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top py-0" style="width: 20%">Obat-obatan Selama Perawatan</td>
                                        <td class="align-top py-0" style="width: 2%">:</td>
                                        <td class="align-top py-0" style="width: 78%">
                                            {{ $resumeRanap1->obat_di_rs }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top py-0" style="width: 20%">Tindakan/Operasi Selama Perawatan
                                        </td>
                                        <td class="align-top py-0" style="width: 2%">:</td>
                                        <td class="align-top py-0" style="width: 78%">
                                            {{ $resumeRanap1->tindakan_dan_operasi }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top py-0" style="width: 20%">Diagnosa Utama</td>
                                        <td class="align-top py-0" style="width: 2%">:</td>
                                        <td class="align-top py-0" style="width: 78%">
                                            {{ $resumeRanap1->diagnosa_utama }}
                                            <div class="float-right">
                                                {{ $resumeRanap3->slice(0, 1)->first()
                                                    ? '(' . $resumeRanap3->slice(0, 1)->first()->kd_penyakit . ')'
                                                    : '(............)' }}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top py-0" style="width: 20%">Diagnosa Sekunder</td>
                                        <td class="align-top py-0" colspan="2"></td>
                                    </tr>
                                    @if ($resumeRanap1->diagnosa_sekunder)
                                        <tr>
                                            <td class="align-top py-0" colspan="3">1.
                                                {{ $resumeRanap1->diagnosa_sekunder }}
                                                <div class="float-right">
                                                    {{ $resumeRanap3->slice(1, 1)->first()
                                                        ? '(' . $resumeRanap3->slice(1, 1)->first()->kd_penyakit . ')'
                                                        : '(............)' }}
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($resumeRanap1->diagnosa_sekunder2)
                                        <tr>
                                            <td class="align-top py-0" colspan="3">2.
                                                {{ $resumeRanap1->diagnosa_sekunder2 }}
                                                <div class="float-right">
                                                    {{ $resumeRanap3->slice(2, 1)->first()
                                                        ? '(' . $resumeRanap3->slice(2, 1)->first()->kd_penyakit . ')'
                                                        : '(............)' }}
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($resumeRanap1->diagnosa_sekunder3)
                                        <tr>
                                            <td class="align-top py-0" colspan="3">3.
                                                {{ $resumeRanap1->diagnosa_sekunder3 }}
                                                <div class="float-right">
                                                    {{ $resumeRanap3->slice(3, 1)->first()
                                                        ? '(' . $resumeRanap3->slice(3, 1)->first()->kd_penyakit . ')'
                                                        : '(............)' }}
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($resumeRanap1->diagnosa_sekunder4 || $resumeRanap3->slice(4, 1)->first())
                                        <tr>
                                            <td class="align-top py-0" colspan="3">4.
                                                {{ $resumeRanap1->diagnosa_sekunder4 }}
                                                <div class="float-right">
                                                    {{-- {{ $resumeRanap3->orderBy('prioritas','ASC')->slice(4,20)?
                                                        '('.$resumeRanap3->orderBy('prioritas','ASC')->slice(4,20)->kd_penyakit.')':'(............)'
                                                        }} --}}
                                                    @if ($resumeRanap3->slice(4, 1))
                                                        (
                                                        @foreach ($resumeRanap3->slice(4, 20) as $list)
                                                            {{ $list->kd_penyakit }},
                                                        @endforeach
                                                        )
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td class="align-top py-0" style="width: 20%">Prosedur/Tindakan Utama</td>
                                        <td class="align-top py-0" style="width: 2%">:</td>
                                        <td class="align-top py-0" style="width: 78%">
                                            {{ $resumeRanap1->prosedur_utama }}
                                            <div class="float-right">
                                                <div class="float-right">
                                                    {{ $resumeRanap4->slice(0, 1)->first()
                                                        ? '(' . $resumeRanap4->slice(0, 1)->first()->kode . ')'
                                                        : '(............)' }}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top py-0" colspan="3">Prosedur/Tindakan Sekunder</td>
                                    </tr>
                                    @if ($resumeRanap1->prosedur_sekunder || $resumeRanap4->slice(1, 1)->first())
                                        <tr>
                                            <td class="align-top py-0" colspan="3">1.
                                                {{ $resumeRanap1->prosedur_sekunder }}
                                                <div class="float-right">
                                                    <div class="float-right">
                                                        {{ $resumeRanap4->slice(1, 1)->first()
                                                            ? '(' . $resumeRanap4->slice(1, 1)->first()->kode . ')'
                                                            : '(............)' }}
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($resumeRanap1->prosedur_sekunder2 || $resumeRanap4->slice(2, 1)->first())
                                        <tr>
                                            <td class="align-top py-0" colspan="3">2.
                                                {{ $resumeRanap1->prosedur_sekunder2 }}
                                                <div class="float-right">
                                                    <div class="float-right">
                                                        <div class="float-right">
                                                            {{ $resumeRanap4->slice(2, 1)->first()
                                                                ? '(' . $resumeRanap4->slice(2, 1)->first()->kode . ')'
                                                                : '(............)' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($resumeRanap1->prosedur_sekunder3 || $resumeRanap4->slice(3, 1)->first())
                                        <tr>
                                            <td class="align-top py-0" colspan="3">3.
                                                {{ $resumeRanap1->prosedur_sekunder3 }}
                                                <div class="float-right">
                                                    <div class="float-right">
                                                        @if ($resumeRanap4->where('prioritas', '>', 3)->first())
                                                            @php
                                                                $dataProsedurLainnya = $resumeRanap4->where(
                                                                    'prioritas',
                                                                    '>',
                                                                    3
                                                                );
                                                                $last = $dataProsedurLainnya->count();
                                                                echo '(';
                                                                foreach ($dataProsedurLainnya as $itemProsedur) {
                                                                    if ($itemProsedur->kode == $resumeRanap4->last()->kode) {
                                                                        echo "$itemProsedur->kode";
                                                                    } else {
                                                                        echo "$itemProsedur->kode, ";
                                                                    }
                                                                }
                                                                echo ')';
                                                            @endphp
                                                        @else
                                                            (............)
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td class="align-top py-0" style="width: 20%">Diet Selama Perawatan</td>
                                        <td class="align-top py-0" style="width: 2%">:</td>
                                        <td class="align-top py-0" style="width: 78%">
                                            {{ $resumeRanap1->diet }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top py-0" style="width: 20%">Keadaan Pulang</td>
                                        <td class="align-top py-0" style="width: 2%">:</td>
                                        <td class="align-top py-0" style="width: 78%">
                                            {{ $resumeRanap1->keadaan }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top py-0" style="width: 20%">Cara Keluar</td>
                                        <td class="align-top py-0" style="width: 2%">:</td>
                                        <td class="align-top py-0" style="width: 78%">
                                            {{ $resumeRanap1->cara_keluar }}
                                            {{ $resumeRanap1->ket_keluar }}
                                            <br>
                                            <pre class="tab1">TD : {{ $resumeRanap1->td }} mmHg     HR : {{ $resumeRanap1->hr }} x/menit   RR : {{ $resumeRanap1->rr }} x/menit   Suhu : {{ $resumeRanap1->suhu }} &#8451;</pre>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top py-0" style="width: 20%">Hasil Lab Yang Belum Selesai
                                            (Pending)</td>
                                        <td class="align-top py-0" style="width: 2%">:</td>
                                        <td class="align-top py-0" style="width: 78%">
                                            {{ $resumeRanap1->lab_belum }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top py-0" style="width: 20%">Obat-obatan waktu pulang</td>
                                        <td class="align-top py-0" style="width: 2%">:</td>
                                        <td class="align-top py-0" style="width: 78%">
                                            {{ $resumeRanap1->obat_pulang }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top py-0" style="width: 20%">PERAWATAN SELANJUTNYA</td>
                                        <td class="align-top py-0" style="width: 2%">:</td>
                                        <td class="align-top py-0" style="width: 78%">
                                            {{ $resumeRanap1->dilanjutkan }}
                                            {{ $resumeRanap1->ket_dilanjutkan }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top py-0" style="width: 20%">Tanggal Kontrol</td>
                                        <td class="align-top py-0" style="width: 2%">:</td>
                                        <td class="align-top py-0" style="width: 78%">
                                            {{ $resumeRanap1->kontrol }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top py-0" style="width: 20%">EDUKASI PASIEN</td>
                                        <td class="align-top py-0" style="width: 2%">:</td>
                                        <td class="align-top py-0" style="width: 78%">
                                            {{ $resumeRanap1->edukasi }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table table-borderless table-sm mt-5">
                                <tr>
                                    <td style="width: 50%; text-align:center;">
                                        Surakarta,
                                        {{ \Carbon\Carbon::parse($resumeRanap2->last()->waktu_keluar_ranap)->locale('id')->isoFormat('D MMMM Y') }}<br>
                                        Dokter Penanggung Jawab Pelayanan
                                    </td>
                                    <td class="text-center align-bottom">
                                        Pasien / Keluarga
                                    </td>
                                </tr>
                                <tr>
                                    @php
                                        $qr_dokter =
                                            'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara
                                        elektronik oleh' .
                                            "\n" .
                                            $resumeRanap1->nm_dokter .
                                            "\n" .
                                            'ID ' .
                                            $resumeRanap1->kd_dokter .
                                            "\n" .
                                            \Carbon\Carbon::now()->format('d-m-Y');

                                    @endphp

                                    <td class="text-center pt-0 pb-0"> {!! QrCode::size(100)->generate($qr_dokter) !!}
                                    </td>
                                    <td style="width: 50%"></td>
                                </tr>
                                <tr>

                                    <td class="text-center"> ({{ $resumeRanap1->nm_dokter }}) </td>
                                    <td class="text-center">(....................)</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                @endif
                {{-- End Resume Ranap --}}
                {{-- Data Dokumen Tambahan --}}
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Berkas Tambahan Pasien
                        </div>
                        <div class="float-right">
                            @can('vedika-upload')
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-default">
                                    <i class="fas fa-upload"></i> Unggah</a>
                                </button>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover">
                            <thead class="text-center">
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th>Jenis Berkas</th>
                                    <th>Nama Berkas</th>
                                    {{-- <th>Keterangan</th> --}}
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($dataBerkas)
                                    @forelse($dataBerkas as $index => $berkas)
                                        <tr>
                                            <td class="text-center">{{ ++$index }}</td>
                                            <td>{{ $berkas->nama }}</td>
                                            <td>
                                                @php
                                                $nama = explode('pages/upload/',$berkas->lokasi_file);
                                                $cekAda = Storage::disk('sftp')->exists($berkas->lokasi_file);
                                                @endphp
                                                {{ $nama[1] }}
                                                @if($cekAda)
                                                    <span class="badge badge-success"><i class="fas fa-check-circle"></i></span>
                                                @else
                                                    <span class="badge badge-danger"><i class="fas fa-times-circle"></i></span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="col text-center">
                                                    <div class="btn-group">
                                                        {{-- <a href="{{ $path->base_url }}{{ $berkas->lokasi_file }}" --}} <a
                                                            href="/vedika/berkas/{{ Crypt::encrypt($berkas->lokasi_file) }}/view"
                                                            target="_blank" class="btn btn-info btn-sm" data-toggle="tooltip"
                                                            data-placement="bottom" title="Lihat Berkas">
                                                            <i class="far fa-eye"></i>
                                                        </a>
                                                        <a href="/vedika/berkas/{{ Crypt::encrypt($berkas->lokasi_file) }}/delete"
                                                            class="btn btn-danger btn-sm delete-confirm @cannot('vedika-delete') disabled @endcannot"
                                                            data-toggle="tooltip" data-placement="bottom" title="Delete">
                                                            <i class="fas fa-ban"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="4">Belum ada berkas yang diunggah</td>
                                        </tr>
                                    @endforelse
                                @else
                                    <tr>
                                        <td class="text-center" colspan="4">Belum ada berkas yang diunggah</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        {{-- @foreach ($dataBerkas as $fileUpload)
                                @php
                                // dd(filetype($fileUpload->lokasi_file));
                                @endphp
                                <img src="{{ env('REMOTE_SERVER_URL') }}/images/FILE_NAME" />
                                @endforeach
                                <table>
                                    <thead>Preview Berkas</thead>
                                </table> --}}
                    </div>
                </div>
                {{-- End berkas --}}
                </div>
                <!-- /.col -->
            </div>
        <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>

    {{-- //Berkas Upload --}}
    <div class="modal fade" id="modal-default">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="/vedika/berkas/store" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">Tambah Berkas Pasien</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- text input -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Nama Berkas</label>
                                    <input type="hidden" class="form-control" value="{{ $pasien->no_rawat }}"
                                        name="no_rawat" />
                                    <input type="hidden" class="form-control" value="{{ $pasien->tgl_registrasi }}"
                                        name="tgl_registrasi" />
                                    <select name="master_berkas" class="form-control select2" required>
                                        <option value="">Pilih</option>
                                        @foreach ($masterBerkas as $master)
                                            <option value="{{ $master->kode }}-{{ $master->nama }}">
                                                {{ $master->nama }} </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('master_berkas_id'))
                                        <div class="text-danger">
                                            {{ $errors->first('master_berkas_id') }}
                                        </div>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label>File Berkas</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="customFile"
                                                name="file" required>
                                            <label class="custom-file-label" for="customFile">Pilih atau drop file
                                                disini</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default float-left" data-dismiss="modal">Tutup</button>
                        <button type="Submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

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
    <!-- bs-custom-file-input -->
    <script src="{{ asset('template/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
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
        $(function() {
            bsCustomFileInput.init();
        });
    </script>
@endsection
