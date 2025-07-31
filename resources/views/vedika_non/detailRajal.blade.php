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
                @php
                    $statusVerif = App\VedikaVerif::cekVerif($pasien->no_rawat, 'Rajal');
                    $statusPengajuan = App\DataPengajuanKlaim::cekPengajuan($pasien->no_rawat, 'Rawat Jalan');
                    $statusPengajuanKronis = App\DataPengajuanKronis::cekPengajuanKronis($pasien->no_rawat);
                    // dd($dataKlaim, $pasien);
                @endphp
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="float-right">
                                <a href="/vedikanon/rajal/{{ Crypt::encrypt($pasien->no_rawat) }}/detailpdf"
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
                                            <img src="{{ asset('image/gps.png') }}" alt="pin lokasi" width="20">
                                            Jalan
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
                                        aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
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
                                                @if ($data->status == 'TtlObat')
                                                    <td class="pt-0 pb-0 text-right text-bold" colspan="7">
                                                        {{ $data->nm_perawatan != null ? $data->nm_perawatan : '' }}
                                                    </td>
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
                                                        {{ $data->nm_perawatan != null ? $data->nm_perawatan : '' }}
                                                    </td>
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
                    {{-- Data Surat Bukti Pelayanan --}}
                    <div class="card">
                        <div class="card-header">Surat Bukti Pelayanan Kesehatan</div>

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
                                        <img src="{{ asset('image/world-wide-web.png') }}" alt="pin lokasi"
                                            width="17">
                                        https://web.rsupsurakarta.co.id
                                    </td>
                                </tr>

                            </table>
                            <div class="progress progress-xs mt-0 pt-0">
                                <div class="progress-bar progress-bar bg-black" role="progressbar" aria-valuenow="100"
                                    aria-valuemin="0" aria-valuemax="100" style="width: 100%">

                                </div>
                            </div>
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
                                        <td class="pt-0 pb-0" style="width: 45%">: {{ $pasien->nm_pasien }}</td>
                                        <td class="pt-0 pb-0"></td>
                                        <td class="pt-0 pb-0"></td>
                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-0">No. Rekam Medis</td>
                                        <td class="pt-0 pb-0">: {{ $pasien->no_rkm_medis }}</td>
                                        <td class="pt-0 pb-0">Cara Pulang</td>
                                        <td class="pt-0 pb-0"></td>
                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-0">Tanggal Lahir</td>
                                        <td class="pt-0 pb-0">:
                                            {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->format('d/m/Y') }}</td>
                                        <td class="pt-0 pb-0">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" onclick="return false;"
                                                    {{ $pasien->stts == 'Sudah' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="defaultCheck1">
                                                    Atas Persetujuan Dokter
                                                </label>
                                            </div>
                                        </td>
                                        <td class="pt-0 pb-0"></td>
                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-0">Jenis Kelamin</td>
                                        <td class="pt-0 pb-0">: {{ $pasien->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                        </td>
                                        <td class="pt-0 pb-0">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" onclick="return false;"
                                                    {{ $pasien->stts == 'Dirujuk' ? 'checked' : '' }}>
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
                                            {{ \Carbon\Carbon::parse($pasien->tgl_registrasi)->format('d/m/Y') }}</td>
                                        <td class="pt-0 pb-0">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" onclick="return false;"
                                                    {{ $pasien->stts == 'Dirawat' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="defaultCheck1">
                                                    MRS
                                                </label>
                                            </div>
                                        </td>
                                        <td class="pt-0 pb-0"></td>
                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-0">Jam Masuk</td>
                                        <td class="pt-0 pb-0">: {{ $pasien->jam_reg }}</td>
                                        <td class="pt-0 pb-0"></td>
                                        <td class="pt-0 pb-0"></td>
                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-0">Poliklinik</td>
                                        <td class="pt-0 pb-0">: {{ $pasien->nm_poli }}</td>
                                        <td class="pt-0 pb-0"></td>
                                        <td class="pt-0 pb-0"></td>
                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-0">Umur</td>
                                        <td class="pt-0 pb-0">:
                                            {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->diff(\Carbon\Carbon::parse($pasien->tgl_registrasi))->format('%y Th %m Bl %d Hr') }}
                                        </td>
                                        <td class="pt-0 pb-0"></td>
                                        <td class="pt-0 pb-0"></td>
                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-0">Alamat</td>
                                        <td class="pt-0 pb-0">: {{ $pasien->alamat }}</td>
                                        <td class="pt-0 pb-0"></td>
                                        <td class="pt-0 pb-0"></td>
                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-0">Status Pasien</td>
                                        <td class="pt-0 pb-0">: {{ $pasien->png_jawab }}</td>
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
                                    <tr>
                                        <td class="border border-dark text-center">1</td>
                                        <td class="border border-dark">
                                            {{ !empty($dataRalan->penilaian) ? $dataRalan->penilaian : '' }}
                                            {{ !empty($statusVerif->verifikasi) ? ", $statusVerif->verifikasi" : '' }}
                                        </td>
                                        <td class="border border-dark  text-center">
                                            @if (!empty($diagnosa))
                                                @foreach ($diagnosa as $index => $dataDiagnosa)
                                                    {{ $dataDiagnosa->kd_penyakit }},<br>
                                                @endforeach
                                            @endif
                                        </td>
                                    </tr>
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
                                            <td class="border border-dark">{{ $dataProsedur->deskripsi_panjang }}
                                            </td>
                                            <td class="border border-dark text-center">{{ $dataProsedur->kode }}
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

                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-center pt-0 pb-0" style="width: 50%">Pasien</td>
                                    <td class="text-center pt-0 pb-0" style="width: 50%">DPJP/Dokter Pemeriksa</td>
                                </tr>
                                <tr>
                                    @php
                                        $ttd_pasien = \App\Vedika::getTtd($pasien->no_rawat);
                                        // dd($ttd_pasien);
                                        $qr_dokter =
                                            'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara
                                elektronik oleh' .
                                            "\n" .
                                            $pasien->nm_dokter .
                                            "\n" .
                                            'ID ' .
                                            $pasien->kd_dokter .
                                            "\n" .
                                            \Carbon\Carbon::parse($pasien->tgl_registrasi)->format('d-m-Y');
                                        $qr_pasien =
                                            'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara
                                elektronik oleh' .
                                            "\n" .
                                            $pasien->nm_pasien .
                                            "\n" .
                                            'ID ' .
                                            $pasien->no_rkm_medis .
                                            "\n" .
                                            \Carbon\Carbon::parse($pasien->tgl_registrasi)->format('d-m-Y');
                                    @endphp
                                    <td class="text-center pt-0 pb-0">
                                        @if (!empty($ttd_pasien->tandaTangan))
                                            <img src={{ $ttd_pasien->tandaTangan }} width="auto" height="100px" />
                                        @else
                                            {!! QrCode::size(100)->generate($qr_pasien) !!}
                                        @endif
                                    </td>
                                    <td class="text-center pt-0 pb-0"> {!! QrCode::size(100)->generate($qr_dokter) !!} </td>
                                </tr>
                                <tr>
                                    <td class="text-center pt-0 pb-0">{{ $pasien->nm_pasien }}</td>
                                    <td class="text-center pt-0 pb-0"> {{ $pasien->nm_dokter }} </td>
                                </tr>
                            </table>
                        </div>

                    </div>
                    {{-- End Surat Bukti Pelayanan --}}
                    {{-- Data Lab --}}
                    @if ($hasilLab != null)
                        <div class="card card-primary card-outline card-outline-tabs">
                            <div class="card-header p-0 border-bottom-0">
                                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                                    @foreach ($permintaanLab as $index => $order)
                                        <li class="nav-item">
                                            <a class="nav-link {{ $index == 0 ? 'active' : '' }}"
                                                id="custom-tabs-four-home-tab" data-toggle="pill"
                                                href="#custom-tabs-lap-{{ $order->noorder }}" role="tab"
                                                aria-controls="custom-tabs-four-home" aria-selected="true"> Hasil
                                                Lab {{ $order->noorder }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content" id="custom-tabs-four-tabContent">
                                    @foreach ($permintaanLab as $index => $order)
                                        <div class="tab-pane fade show {{ $index == 0 ? 'active' : '' }}"
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
                                                            {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->diff(\Carbon\Carbon::parse($pasien->tgl_registrasi))->format('%y Th %m Bl %d Hr') }}
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
                                                        <td class="pt-0 pb-0">: {{ $order->nm_dokter }}
                                                        </td>
                                                        <td class="pt-0 pb-0">Poli</td>
                                                        <td class="pt-0 pb-0">: {{ $pasien->nm_poli }}</td>
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
                                                            $dokter_lab = $dokterLab->nm_dokter;
                                                            $kd_dokter_lab = $dokterLab->kd_dokter;

                                                            $petugasLab = \App\Vedika::getPetugas(
                                                                $order->tgl_hasil,
                                                                $order->jam_hasil
                                                            );
                                                            $petugas_lab = $petugasLab->nama;
                                                            $kd_petugas_lab = $petugasLab->nip;

                                                            $qr_dokter =
                                                                'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara
                                        elektronik oleh' .
                                                                "\n" .
                                                                $dokter_lab .
                                                                "\n" .
                                                                'ID ' .
                                                                $kd_dokter_lab .
                                                                "\n" .
                                                                \Carbon\Carbon::parse($order->tgl_hasil)->format(
                                                                    'd-m-Y'
                                                                );
                                                            $qr_petugas =
                                                                'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara
                                        elektronik oleh' .
                                                                "\n" .
                                                                $petugas_lab .
                                                                "\n" .
                                                                'ID ' .
                                                                $kd_petugas_lab .
                                                                "\n" .
                                                                \Carbon\Carbon::parse($order->tgl_hasil)->format(
                                                                    'd-m-Y'
                                                                );

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
                                            ++$index;
                                        @endphp
                                    @endforeach
                                </div>
                            </div>
                            <!-- /.card -->
                        </div>
                    @endif
                    {{-- End hasil Lab --}}
                    {{-- Data Radiologi --}}
                    @if ($dataRadiologiRajal->count() > 0 && $dokterRadiologiRajal[0] != null)
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
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content" id="custom-tabs-four-tabContent">
                                    @php
                                        $index2 = 0;
                                    @endphp
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
                                                            {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->diff(\Carbon\Carbon::parse($radioRajal->tgl_hasil))->format('%y Th %m Bl %d Hr') }}
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

                                </div>
                            </div>
                        </div>
                    @endif
                    {{-- End Radiologi --}}
                    {{-- Data Obat --}}
                    @if (!empty($resepObat))
                        @foreach ($resepObat as $index => $resepObat)
                            <div class="card">
                                <div class="card-header">Obat</div>
                                <div class="card-body">
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
                                                <div style="font-size: 14pt; color:#057c86; margin-top:-5pt">RS Surakarta
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
                                        <tbody>
                                            <tr>
                                                <td class="pt-0 pb-0" style="width: 15%">Nama Pasien</td>
                                                <td class="pt-0 pb-0" style="width: 60%">:
                                                    {{ $resepObat->nm_pasien }}
                                                </td>
                                                <td class="pt-0 pb-0" style="width: 15%">Jam Peresepan</td>
                                                <td class="pt-0 pb-0" style="width: 10%">:
                                                    {{ $resepObat->jam_peresepan }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="pt-0 pb-0" style="width: 15%">No.RM</td>
                                                <td class="pt-0 pb-0" style="width: 60%">:
                                                    {{ $resepObat->no_rkm_medis }}
                                                </td>
                                                <td class="pt-0 pb-0" style="width: 15%">Jam Pelayanan</td>
                                                <td class="pt-0 pb-0" style="width: 10%">: {{ $resepObat->jam }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="pt-0 pb-0" style="width: 15%">No.Rawat</td>
                                                <td class="pt-0 pb-0" style="width: 60%">:
                                                    {{ $resepObat->no_rawat }}
                                                </td>
                                                <td class="pt-0 pb-0" style="width: 15%">BB (Kg)</td>
                                                <td class="pt-0 pb-0" style="width: 10%">:
                                                    {{ !empty($bbPasien[$index]) ? $bbPasien[$index]->berat : '' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="pt-0 pb-0" style="width: 15%">Tanggal Lahir</td>
                                                <td class="pt-0 pb-0" style="width: 45%">:
                                                    {{ \Carbon\Carbon::parse($resepObat->tgl_lahir)->format('d-m-Y') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="pt-0 pb-0">Penanggung</td>
                                                <td class="pt-0 pb-0">: BPJS</td>
                                            </tr>
                                            <tr>
                                                <td class="pt-0 pb-0">Pemberi Resep</td>
                                                <td class="pt-0 pb-0">: {{ $resepObat->nm_dokter }}</td>
                                            </tr>
                                            <tr>
                                                <td class="pt-0 pb-0">No. Resep</td>
                                                <td class="pt-0 pb-0">: {{ $resepObat->no_resep }}</td>
                                            </tr>
                                            <tr>
                                                <td class="pt-0 pb-0">No. SEP</td>
                                                <td class="pt-0 pb-0">:
                                                    {{ App\Vedika::getSep($resepObat->no_rawat,2) != null ? App\Vedika::getSep($resepObat->no_rawat,2)->no_sep : '' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="pt-0 pb-0">Alamat</td>
                                                <td class="pt-0 pb-0">: {{ $resepObat->alamat }},
                                                    {{ $resepObat->nm_kel }},{{ $resepObat->nm_kec }},
                                                    {{ $resepObat->nm_kab }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="progress progress-xs mt-3 pt-0">
                                        <div class="progress-bar progress-bar bg-black" role="progressbar"
                                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"
                                            style="width: 100%">

                                        </div>
                                    </div>
                                    <table class="table table-borderless table-sm">
                                        <thead>
                                            <tr>
                                                <th class="align-middle text-center pb-1" colspan="7">
                                                    <h5>RESEP</h5>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $no = 0;
                                            @endphp
                                            @if (!empty($obatJadi[$index]))
                                                @foreach ($obatJadi[$index] as $listObat)
                                                    @if (\App\Vedika::aturanObatJadi($pasien->no_rawat, $listObat->kode_brng) != null)
                                                        <tr>
                                                            <td class="text-center">
                                                                {{ ++$no }}
                                                            </td>
                                                            <td>
                                                                {{ $listObat->nama_brng }} <br>
                                                                {{ \App\Vedika::aturanObatJadi($pasien->no_rawat, $listObat->kode_brng)->aturan }}
                                                            </td>
                                                            <td>
                                                                {{ $listObat->jml }} {{ $listObat->satuan }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            @endif
                                            @if (!empty($obatRacik[$index]))
                                                @foreach ($obatRacik[$index] as $listObatRacik)
                                                    @if ($resepObat->jam == $listObatRacik->jam)
                                                        <tr>
                                                            <td class="text-center">
                                                                {{ ++$no }}
                                                            </td>
                                                            <td>
                                                                {{ $listObatRacik->nama_racik }}
                                                                @php
                                                                    $jumlah = \App\Vedika::getRacikan(
                                                                        $pasien->no_rawat,
                                                                        $listObatRacik->jam
                                                                    )->count();
                                                                    $jumlah = $jumlah - 1;
                                                                @endphp
                                                                (@foreach (\App\Vedika::getRacikan($pasien->no_rawat, $listObatRacik->jam) as $index => $listRacikan)
                                                                    {{ $listRacikan->nama_brng }}
                                                                    {{ \App\Vedika::getJmlRacikan($pasien->no_rawat, $listRacikan->kode_brng, $listObatRacik->jam)->jml }}{{ $index != $jumlah ? ',' : '' }}
                                                                @endforeach)
                                                                <br>
                                                                {{ $listObatRacik->aturan_pakai }}
                                                            </td>
                                                            <td>
                                                                {{ $listObatRacik->jml_dr }}
                                                                {{ $listObatRacik->nm_racik }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            @endif

                                        </tbody>

                                    </table>
                                    <table class="table table-borderless mt-3">
                                        <tr>
                                            <td class="text-center pt-0 pb-0" style="width: 70%"></td>
                                            <td class="text-center pt-0 pb-0" style="width: 30%">Surakarta,
                                                {{ \Carbon\Carbon::parse($resepObat->tgl_perawatan)->format('d-m-Y') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            @php
                                                $qr_dokter =
                                                    'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara
                                elektronik oleh' .
                                                    "\n" .
                                                    $resepObat->nm_dokter .
                                                    "\n" .
                                                    'ID ' .
                                                    $resepObat->kd_dokter .
                                                    "\n" .
                                                    \Carbon\Carbon::parse($resepObat->tgl_perawatan)->format('d-m-Y');

                                            @endphp
                                            <td class="text-center pt-0 pb-0" style="width: 70%">
                                                &nbsp;
                                            </td>
                                            <td class="text-center pt-0 pb-0" style="width: 30%">
                                                {!! QrCode::size(100)->generate($qr_dokter) !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-center pt-0 pb-0" style="width: 70%">
                                                &nbsp;<br> &nbsp;
                                            </td>
                                            <td class="text-center pt-0 pb-0"> {{ $resepObat->nm_dokter }} </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    @endif
                    {{-- End data Obat --}}
                    {{-- Data Dokumen Tambahan --}}
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Berkas Tambahan Pasien</div>
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
                                                            <a href="/vedika/berkas/{{ Crypt::encrypt($berkas->lokasi_file) }}/view"
                                                                target="_blank" class="btn btn-info btn-sm"
                                                                data-toggle="tooltip" data-placement="bottom"
                                                                title="Lihat Berkas">
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
