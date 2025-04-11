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
                @endphp
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            Status Pengajuan Kronis:
                            {{ !empty($statusPengajuanKronis) ? \Carbon\Carbon::parse($statusPengajuanKronis->periodeKlaim->periode)->format('F Y') : '' }}
                            @can('vedika-upload')
                                @if (!empty($statusPengajuanKronis))
                                    <a href="/vedika/pengajuankronis/{{ Crypt::encrypt($statusPengajuanKronis->id) }}/delete"
                                        class="delete-confirm text-danger" data-toggle="tooltip" data-placement="bottom"
                                        title="Delete">
                                        <i class="fas fa-ban"></i>
                                    </a>
                                @endif
                            @endcan
                            <div class="float-right">
                                @can('vedika-verif-create')
                                    @if (empty($statusVerif))
                                        <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#modal-verif">
                                            <i class="far fa-edit"></i> Verifikasi</a>
                                        </button>
                                    @else
                                        <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#modal-edit-verif">
                                            <i class="far fa-edit"></i> Verifikasi</a>
                                        </button>
                                    @endif
                                @endcan
                                @can('vedika-kronis-create')
                                    @if (empty($statusPengajuanKronis))
                                        <button class="btn btn-light btn-sm" data-toggle="modal"
                                            data-target="#modal-pengajuan-kronis">
                                            <i class="fas fa-plus-circle"></i> Pengajuan Klaim Kronis</a>
                                        </button>
                                    @else
                                        <button class="btn btn-dark btn-sm" data-toggle="modal"
                                            data-target="#modal-pengajuan-kronis-edit">
                                            <i class="fas fa-pen"></i> Edit Pengajuan Klaim Kronis</a>
                                        </button>
                                    @endif
                                @endcan
                                <a href="/vedika/rajal/{{ Crypt::encrypt($pasien->no_rawat) }}/cronispdf"
                                    class="btn btn-secondary btn-sm" target="_blank">
                                    <i class="far fa-file-pdf"></i> Cronis PDF</a>
                                </a>
                                <button class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#modal-gabung-file">
                                    <i class="fas fa-file-download"></i> Gabung PDF</a>
                                </button>
                            </div>
                        </div>
                    </div>
                    {{-- Resep Obat --}}
                    @if (!empty($resepObat))
                        @foreach ($resepObat as $index => $resepObat)
                            <div class="card">
                                <div class="card-header">Obat</div>
                                <div class="card-body">
                                    <table class="table table-borderless mb-0">
                                        <tr>
                                            <td style="width:20%" rowspan="3"><img
                                                    src="{{ asset('image/logorsup.jpg') }}" alt="Logo RSUP" width="100">
                                            </td>
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
                                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">

                                        </div>
                                    </div>
                                    <table class="table table-borderless py-0">
                                        <tbody>
                                            <tr>
                                                <td class="pt-0 pb-0" style="width: 15%">Nama Pasien</td>
                                                <td class="pt-0 pb-0" style="width: 60%">: {{ $resepObat->nm_pasien }}
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
                                                <td class="pt-0 pb-0" style="width: 10%">: {{ $resepObat->jam }}</td>
                                            </tr>
                                            <tr>
                                                <td class="pt-0 pb-0" style="width: 15%">No.Rawat</td>
                                                <td class="pt-0 pb-0" style="width: 60%">: {{ $resepObat->no_rawat }}
                                                </td>
                                                <td class="pt-0 pb-0" style="width: 15%">BB (Kg)</td>
                                                <td class="pt-0 pb-0" style="width: 10%">:
                                                    {{ !empty($bbPasien[$index]) ? $bbPasien[$index]->berat : '' }}</td>
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
                                                    'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara elektronik oleh' .
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
                                            <td class="text-center pt-0 pb-0" style="width: 30%"> {!! QrCode::size(100)->generate($qr_dokter) !!}
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

                    {{-- DATA SEP --}}
                    @if (!empty($dataSep))
                        {{-- Data SEP lokal --}}
                        @if (!empty($dataSep->no_sep))
                            <div class="card">
                                <div class="card-header">SEP</div>
                                <div class="card-body">
                                    <table class="table table-borderless mb-0 table-sm">
                                        <tr>
                                            <td style="width:25%" rowspan="2"><img
                                                    src="{{ asset('image/logoBPJS.svg') }}" alt="Logo BPJS"
                                                    width="300">
                                            </td>
                                            <td class="pt-0 pb-0 align-middle ">
                                                <h3 class="pt-0 pb-0">SURAT ELIGIBILITAS PESERTA</h3>
                                            </td>
                                            <td style="width:30%" rowspan="3" class="align-center">
                                                <h2>{{ $dataSep->prb }}</h2>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="align-middle py-0">
                                                <h4>RSUP SURAKARTA</h4>
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="table table-borderless mb-0 table-sm">
                                        <tr>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td style="width:10%" class="pt-0 pb-0">No. SEP</td>
                                            <td style="width:50%" class="pt-0 pb-0">: {{ $dataSep->no_sep }}</td>
                                            <td class="pt-0 pb-0 text-center" colspan="2"></td>
                                        </tr>
                                        <tr>
                                            <td class="pt-0 pb-0">Tgl. SEP</td>
                                            <td class="pt-0 pb-0">: {{ $dataSep->tglsep }}</td>
                                            <td class="pt-0 pb-0" style="width:10%">Peserta</td>
                                            <td class="pt-0 pb-0" style="width:30%">: {{ $dataSep->peserta }}</td>
                                        </tr>
                                        <tr>
                                            <td class="pt-0 pb-0">No. Kartu</td>
                                            <td class="pt-0 pb-0">: {{ $dataSep->no_kartu }} (MR : {{ $dataSep->nomr }})
                                            </td>
                                            <td class="pt-0 pb-0"></td>
                                            <td class="pt-0 pb-0"></td>
                                        </tr>
                                        <tr>
                                            <td class="pt-0 pb-0">Nama Peserta</td>
                                            <td class="pt-0 pb-0">: {{ $dataSep->nama_pasien }}</td>
                                            <td class="pt-0 pb-0">Jns. Rawat</td>
                                            <td class="pt-0 pb-0">:
                                                @if ($dataSep->jnspelayanan == '1')
                                                    Rawat Inap
                                                @else
                                                    Rawat Jalan
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="pt-0 pb-0">Tgl. Lahir</td>
                                            <td class="pt-0 pb-0">
                                                <div class="row">
                                                    <div class="col-6">
                                                        :
                                                        {{ \Carbon\Carbon::parse($dataSep->tanggal_lahir)->format('Y-m-d') }}
                                                    </div>
                                                    <div class="col-6">Kelamin :
                                                        @if ($dataSep->jkel == 'L')
                                                            Laki-laki
                                                        @else
                                                            Perempuan
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="pt-0 pb-0">Jns. Kunjungan</td>
                                            <td class="pt-0 pb-0">:
                                                @if ($dataSep->tujuankunjungan == '0')
                                                    - Konsultasi dokter(pertama)
                                                @elseif ($dataSep->tujuankunjungan == '2')
                                                    - Kunjungan Kontrol(ulangan)
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="pt-0 pb-0">No. Telepon</td>
                                            <td class="pt-0 pb-0">: {{ $dataSep->notelep }}</td>
                                            <td class="pt-0 pb-0"></td>
                                            <td class="pt-0 pb-0"></td>
                                        </tr>
                                        <tr>
                                            <td class="pt-0 pb-0">Sub/Spesialis</td>
                                            <td class="pt-0 pb-0">: {{ $dataSep->nmpolitujuan }}</td>
                                            <td class="pt-0 pb-0">Poli Perujuk</td>
                                            <td class="pt-0 pb-0">: </td>
                                        </tr>
                                        <tr>
                                            <td class="pt-0 pb-0">Dokter</td>
                                            <td class="pt-0 pb-0">: {{ $dataSep->nmdpjplayanan }}</td>
                                            <td class="pt-0 pb-0">Kls. Hak</td>
                                            <td class="pt-0 pb-0">: Kelas {{ $dataSep->klsrawat }}</td>
                                        </tr>
                                        <tr>
                                            <td class="pt-0 pb-0">Faskes Perujuk</td>
                                            <td class="pt-0 pb-0">: {{ $dataSep->nmppkrujukan }}</td>
                                            <td class="pt-0 pb-0">Kls. Rawat</td>
                                            <td class="pt-0 pb-0">: </td>
                                        </tr>
                                        <tr>
                                            <td class="pt-0 pb-0">Diagnosa Awal</td>
                                            <td class="pt-0 pb-0">: {{ $dataSep->nmdiagnosaawal }}</td>
                                            <td class="pt-0 pb-0">Penjamin</td>
                                            <td class="pt-0 pb-0">{{ $dataSep->pembiayaan }}</td>
                                        </tr>
                                        <tr>
                                            <td class="pt-0 pb-0">Catatan</td>
                                            <td class="pt-0 pb-0">: {{ $dataSep->catatan }}</td>
                                            <td class="pt-0 pb-0"></td>
                                            <td class="pt-0 pb-0"></td>
                                        </tr>

                                        <tr>
                                            <td colspan="3" class="pt-0 pb-0"><small><i>
                                                        *Saya menyetujui BPJS Kesehatan
                                                        untuk:
                                                        <ol type="a" style="margin:-5px 50px -5px -25px">
                                                            <li>membuka dan atau
                                                                menggunakan informasi medis
                                                                Pasien untuk
                                                                keperluan administrasi, pembayaran asuransi atau jaminan
                                                                pembiayaan kesehatan
                                                            <li>memberikan akses informasi
                                                                medis atau riwayat
                                                                pelayanan kepada
                                                                dokter/tenaga medis pada RSUP Surakarta untuk kepentingan
                                                                pemeliharaan kesehatan, pengobatan, penyembuhan, dan
                                                                perawatan
                                                                Pasien
                                                        </ol>
                                                        *Saya mengetahui dan memahami:
                                                        <ol type="a" style="margin:-5px 50px -5px -25px">
                                                            <li>Rumah Sakit dapat melakukan
                                                                koordinasi dengan PT Jasa Raharja/PT
                                                                Taspen/PT ASABRI/BPJS Ketenagakerjaan atau Penjamin lainnya.
                                                                jika Peserta merupakan pasien yang mengalami kecelakaan
                                                                lalulintas dan / atau kecelakaan kerja
                                                            <li>SEP bukan sebagai bukti
                                                                penjaminan peserta
                                                        </ol>
                                                        *SEP bukan sebagai bukti penjaminan peserta<br>
                                                        ** Dengan tampilnya luaran SEP elektronik
                                                        ini merupakan hasil validasi terhadap eligibilitas Pasien secara
                                                        elektronik(validasi finger print atau biometrik /sistem validasi
                                                        lain)
                                                        dan selanjutnya Pasien dapat mengakses pelayanan kesehatan rujukan
                                                        sesuai ketentuan berlaku. Kebenaran dan keaslian atas informasi
                                                        Pasien
                                                        menjadi tanggung jawab penuh FKRTL
                                                    </i></small>
                                            </td>

                                            <td class="pt-0 pb-0 pl-3" rowspan="3">
                                                <div class="pt-0 pb-0">Persetujuan</div>
                                                <div class="pt-0 pb-0">Pasien/Keluarga Pasien</div>
                                                <div class="pt-3 pb-1 pl-5">
                                                    {!! QrCode::size(100)->generate($dataSep->no_kartu) !!}</div>
                                                <div class="pt-0 pb-0">
                                                    <h4>{{ $dataSep->nama_pasien }}</h4>
                                                </div>
                                                <div class="pt-2 text-right"><small>Cetakan ke 1
                                                        {{ \Carbon\Carbon::now()->format('d/m/Y g:i:s A') }}
                                                    </small>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        @elseif(!empty($dataSep->noSep))
                            @php
                                $peserta = \app\Http\Controllers\SepController::peserta(
                                    $dataSep->peserta->noKartu,
                                    $dataSep->tglSep
                                );
                                $kontrol = \app\Http\Controllers\SepController::getSep2($dataSep->noSep);
                            @endphp
                            <div class="card">
                                <div class="card-header">SEP</div>
                                <div class="card-body">
                                    <table class="table table-borderless mb-0 table-sm">
                                        <tr>
                                            <td style="width:25%" rowspan="2"><img
                                                    src="{{ asset('image/logoBPJS.svg') }}" alt="Logo BPJS"
                                                    width="300"></td>
                                            <td class="pt-0 pb-0 align-middle ">
                                                <h3 class="pt-0 pb-0">SURAT ELIGIBILITAS PESERTA</h3>
                                            </td>
                                            <td style="width:30%" rowspan="3" class="align-center">
                                                <h2></h2>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="align-middle py-0">
                                                <h4>RSUP SURAKARTA</h4>
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="table table-borderless mb-0 table-sm">
                                        <tr>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td style="width:10%" class="pt-0 pb-0">No. SEP</td>
                                            <td style="width:50%" class="pt-0 pb-0">: {{ $dataSep->noSep }}</td>
                                            <td class="pt-0 pb-0 text-center" colspan="2"></td>
                                        </tr>
                                        <tr>
                                            <td class="pt-0 pb-0">Tgl. SEP</td>
                                            <td class="pt-0 pb-0">: {{ $dataSep->tglSep }}</td>
                                            <td class="pt-0 pb-0" style="width:10%">Peserta</td>
                                            <td class="pt-0 pb-0" style="width:30%">: {{ $dataSep->peserta->jnsPeserta }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="pt-0 pb-0">No. Kartu</td>
                                            <td class="pt-0 pb-0">: {{ $dataSep->peserta->noKartu }} (MR :
                                                {{ $dataSep->peserta->noMr }})</td>
                                            <td class="pt-0 pb-0"></td>
                                            <td class="pt-0 pb-0"></td>
                                        </tr>
                                        <tr>
                                            <td class="pt-0 pb-0">Nama Peserta</td>
                                            <td class="pt-0 pb-0">: {{ $dataSep->peserta->nama }}</td>
                                            <td class="pt-0 pb-0">Jns. Rawat</td>
                                            <td class="pt-0 pb-0">: {{ $dataSep->jnsPelayanan }}</td>
                                        </tr>
                                        <tr>
                                            <td class="pt-0 pb-0">Tgl. Lahir</td>
                                            <td class="pt-0 pb-0">
                                                <div class="row">
                                                    <div class="col-6">
                                                        : {{ $dataSep->peserta->tglLahir }}
                                                    </div>
                                                    <div class="col-6">Kelamin :
                                                        @if ($dataSep->peserta->kelamin == 'L')
                                                            Laki-laki
                                                        @else
                                                            Perempuan
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="pt-0 pb-0">Jns. Kunjungan</td>
                                            <td class="pt-0 pb-0">:
                                                @if ($dataSep->tujuanKunj->kode == '0')
                                                    - Konsultasi dokter(pertama)
                                                @elseif ($dataSep->tujuanKunj->kode == '2')
                                                    - Kunjungan Kontrol(ulangan)
                                                @endif

                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="pt-0 pb-0">No. Telepon</td>
                                            <td class="pt-0 pb-0">: {{ $peserta->mr->noTelepon }}</td>
                                            <td class="pt-0 pb-0"></td>
                                            <td class="pt-0 pb-0">
                                                @if ($dataSep->flagProcedure->nama != null)
                                                    : - {{ $dataSep->flagProcedure->nama }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="pt-0 pb-0">Sub/Spesialis</td>
                                            <td class="pt-0 pb-0">: {{ $dataSep->poli }}</td>
                                            <td class="pt-0 pb-0">Poli Perujuk</td>
                                            <td class="pt-0 pb-0">: </td>
                                        </tr>
                                        <tr>
                                            <td class="pt-0 pb-0">Dokter</td>
                                            <td class="pt-0 pb-0">: {{ $dataSep->dpjp->nmDPJP }}</td>
                                            <td class="pt-0 pb-0">Kls. Hak</td>
                                            <td class="pt-0 pb-0">: Kelas {{ $dataSep->kelasRawat }}</td>
                                        </tr>
                                        <tr>
                                            <td class="pt-0 pb-0">Faskes Perujuk</td>
                                            <td class="pt-0 pb-0">: {{ $kontrol->provPerujuk->nmProviderPerujuk }}</td>
                                            <td class="pt-0 pb-0">Kls. Rawat</td>
                                            <td class="pt-0 pb-0">: </td>
                                        </tr>
                                        <tr>
                                            <td class="pt-0 pb-0">Diagnosa Awal</td>
                                            <td class="pt-0 pb-0">: {{ $kontrol->diagnosa }}</td>
                                            <td class="pt-0 pb-0">Penjamin</td>
                                            <td class="pt-0 pb-0">{{ $dataSep->penjamin }}</td>
                                        </tr>
                                        <tr>
                                            <td class="pt-0 pb-0">Catatan</td>
                                            <td class="pt-0 pb-0">: {{ $dataSep->catatan }}</td>
                                            <td class="pt-0 pb-0"></td>
                                            <td class="pt-0 pb-0"></td>
                                        </tr>

                                        <tr>
                                            <td colspan="3" class="pt-0 pb-0"><small><i>
                                                        *Saya menyetujui BPJS Kesehatan
                                                        untuk:
                                                        <ol type="a" style="margin:-5px 50px -5px -25px">
                                                            <li>membuka dan atau
                                                                menggunakan informasi medis
                                                                Pasien untuk
                                                                keperluan administrasi, pembayaran asuransi atau jaminan
                                                                pembiayaan kesehatan
                                                            <li>memberikan akses informasi
                                                                medis atau riwayat
                                                                pelayanan kepada
                                                                dokter/tenaga medis pada RSUP Surakarta untuk kepentingan
                                                                pemeliharaan kesehatan, pengobatan, penyembuhan, dan
                                                                perawatan
                                                                Pasien
                                                        </ol>
                                                        *Saya mengetahui dan memahami:
                                                        <ol type="a" style="margin:-5px 50px -5px -25px">
                                                            <li>Rumah Sakit dapat melakukan
                                                                koordinasi dengan PT Jasa Raharja/PT
                                                                Taspen/PT ASABRI/BPJS Ketenagakerjaan atau Penjamin lainnya.
                                                                jika Peserta merupakan pasien yang mengalami kecelakaan
                                                                lalulintas dan / atau kecelakaan kerja
                                                            <li>SEP bukan sebagai bukti
                                                                penjaminan peserta
                                                        </ol>
                                                        *SEP bukan sebagai bukti penjaminan peserta<br>
                                                        ** Dengan tampilnya luaran SEP elektronik
                                                        ini merupakan hasil validasi terhadap eligibilitas Pasien secara
                                                        elektronik(validasi finger print atau biometrik /sistem validasi
                                                        lain)
                                                        dan selanjutnya Pasien dapat mengakses pelayanan kesehatan rujukan
                                                        sesuai ketentuan berlaku. Kebenaran dan keaslian atas informasi
                                                        Pasien
                                                        menjadi tanggung jawab penuh FKRTL
                                                    </i></small>
                                            </td>

                                            <td class="pt-0 pb-0 pl-3" rowspan="3">
                                                <div class="pt-0 pb-0">Persetujuan</div>
                                                <div class="pt-0 pb-0">Pasien/Keluarga Pasien</div>
                                                <div class="pt-3 pb-1 pl-5">
                                                    {!! QrCode::size(100)->generate($dataSep->peserta->noKartu) !!}</div>
                                                <div class="pt-0 pb-0">
                                                    <h4>{{ $dataSep->peserta->nama }}</h4>
                                                </div>
                                                <div class="pt-2 text-right"><small>Cetakan ke 1
                                                        {{ \Carbon\Carbon::now()->format('d/m/Y g:i:s A') }}
                                                    </small>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        @endif

                    @endif

                    {{-- Data Billing --}}
                    <div class="card">
                        <div class="card-header">Billing</div>
                        @if ($billing->count() > 0)
                            <div class="card-body">
                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <td style="width:20%" rowspan="3"><img
                                                src="{{ asset('image/logorsup.jpg') }}" alt="Logo RSUP" width="100">
                                        </td>
                                        <td class="pt-0 pb-0 text-center align-middle">
                                            <h2>
                                                <center>RSUP SURAKARTA</center>
                                            </h2>
                                        </td>
                                        <td style="width:20%" rowspan="3"></td>
                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-0 text-center align-middle ">
                                            <center> Jl.Prof.Dr.R.Soeharso No.28 , Surakarta, Jawa Tengah</center>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-0 text-center align-middle ">
                                            <center>Telp.0271-713055 / 720002, E-mail : rsupsurakarta@kemkes.go.id</center>
                                        </td>
                                    </tr>
                                </table>
                                <div class="progress progress-xs mt-0 pt-0">
                                    <div class="progress-bar progress-bar bg-black" role="progressbar"
                                        aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">

                                    </div>
                                </div>
                                <table style="width: 100%; border: 0 solid black; line-height: 100%">
                                    <thead>
                                        <tr>
                                            <th colspan="8">
                                                <h3>
                                                    <center>BILLING OBAT<center>
                                                </h3>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $total = 0;
                                            $status_dokter = 0;
                                        @endphp
                                        @for ($i = 0; $i < 1; $i++)
                                            <tr>
                                                <td style="border:0px solid black; vertical-align:top; width:15%">
                                                    No.RM</td>
                                                <td>: {{ $billing[$i]->no_rkm_medis }}</td>
                                            </tr>
                                            <tr>
                                                <td style="border:0px solid black; vertical-align:top; width:15%">
                                                    No Rawat</td>
                                                <td>: {{ $billing[$i]->no_rawat }}</td>
                                            </tr>
                                            <tr>
                                                <td style="border:0px solid black; width:15%">
                                                    Nama Pasien</td>
                                                <td>: {{ $billing[$i]->nm_pasien }}</td>
                                            </tr>
                                            <tr>
                                                <td style="border:0px solid black; width:15%">
                                                    Alamat
                                                </td>
                                                <td style="border:0px solid black; width:85%">: {{ $billing[$i]->alamat }}
                                                </td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                                <br>
                                <table style="width: 100%; border: 0 solid black; line-height: 100%; ">
                                    <tr>
                                        <th style="border:1px solid black; width:5%">No </th>
                                        <th style="border:1px solid black; width:25%">Nama Obat </th>
                                        <th style="border:1px solid black; width:7.5%">Jumlah</th>
                                        <th style="border:1px solid black; width:15%">Biaya Obat</th>
                                        <th style="border:1px solid black; width:10%">Embalase</th>
                                        <th style="border:1px solid black; width:10%">Tuslah</th>
                                        <th style="border:1px solid black; width:17.5%">Total</th>
                                    </tr>
                                    @foreach ($billing as $index => $dataBill)
                                        <tr>
                                            <td
                                                style="border:0px solid black; text-align:center; vertical-align: text-top;">
                                                {{ ++$index }} </td>
                                            <td style="border:0px solid black;">{{ $dataBill->nama_brng }} </td>
                                            <td
                                                style="border:0px solid black; text-align:center; vertical-align: text-top;">
                                                {{ $dataBill->jml }}</td>
                                            <td
                                                style="border:0px solid black; text-align:right; vertical-align: text-top;">
                                                {{ number_format($dataBill->biaya_obat, 0, ',', '.') }}</td>
                                            <td
                                                style="border:0px solid black; text-align:center; vertical-align: text-top;">
                                                {{ $dataBill->embalase }}</td>
                                            <td
                                                style="border:0px solid black; text-align:center; vertical-align: text-top;">
                                                {{ $dataBill->tuslah }}</td>
                                            <td
                                                style="border:0px solid black; text-align:right; vertical-align: text-top;">
                                                {{ number_format($dataBill->total, 0, ',', '.') }}</td>
                                        </tr>
                                        @php
                                            $total = $total + $dataBill->total;
                                        @endphp
                                    @endforeach
                                    <tr>
                                        <th style="border:0px solid black; text-align:right" colspan="6">TOTAL BIAYA
                                        </th>
                                        <th style="text-align:right">
                                            {{ number_format($total, 0, ',', '.') }} </th>
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
                                        $qr_dokter =
                                            'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara elektronik oleh' .
                                            "\n" .
                                            $pasien->nm_dokter .
                                            "\n" .
                                            'ID ' .
                                            $pasien->kd_dokter .
                                            "\n" .
                                            \Carbon\Carbon::parse($pasien->tgl_registrasi)->format('d-m-Y');
                                        $qr_pasien =
                                            'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara elektronik oleh' .
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
                                                        <td class="pt-0 pb-0" style="width: 15%">No.Permintaan Lab</td>
                                                        <td class="pt-0 pb-0" style="width: 25%">: {{ $order->noorder }}
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
                                                            <td colspan="5" class="text-center">Belum ada hasil</td>
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
                                                <small><b>Catatan:</b> Jika ada keragu-raguan pemeriksaan, diharapkan segera
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
                                                                'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara elektronik oleh' .
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
                                                                'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara elektronik oleh' .
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

                                                        <td class="text-center pt-0 pb-0"> {!! QrCode::size(100)->generate($qr_dokter) !!} </td>
                                                        <td class="text-center pt-0 pb-0"> {!! QrCode::size(100)->generate($qr_petugas) !!} </td>
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
                        </div>
                    @endif
                    {{-- End hasil Lab --}}

                    {{-- Data Dokumen Tambahan --}}
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Berkas Tambahan Pasien
                            </div>
                            <div class="float-right">
                                @can('vedika-kronis-create')
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
                                        <th>Nama Berkas</th>
                                        {{-- <th>Keterangan</th> --}}
                                        <th>Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @if ($dataBerkas)
                                        @foreach($dataBerkas as $index => $berkas)
                                            <tr>
                                                <td class="text-center">{{ ++$index }}</td>
                                                <td>{{ $berkas->nama }}</td>
                                                {{-- <td></td> --}}
                                                <td>
                                                    <div class="col text-center">
                                                        <div class="btn-group">
                                                            {{-- <a href="{{ $path->base_url }}{{ $berkas->lokasi_file }}" --}}
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
                                        @endforeach
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
    {{-- Modal untuk pengajuan klaim Kronis --}}
    @if (empty($statusPengajuanKronis))
        <div class="modal fade" id="modal-pengajuan-kronis">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form method="POST" action="/vedika/pengajuankronis">
                        @csrf
                        <div class="modal-header">
                            <h4 class="modal-title">Pengajuan Klaim</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>No Rawat pasien</label>
                                        <input type="text" class="form-control" value="{{ $pasien->no_rawat }}"
                                            name="no_rawat" readonly />
                                    </div>
                                    <div class="form-group">
                                        <label>No Resep</label>
                                        <input type="text" class="form-control" name="no_resep" required />
                                    </div>
                                    <div class="form-group">
                                        <label>No SEP</label>
                                        <input type="text" class="form-control"
                                            value="{{ !empty($dataSep->no_sep) ? $dataSep->no_sep : '' }}"
                                            name="no_sep" {{ !empty($dataSep->no_sep) ? 'readonly' : 'required' }} />
                                    </div>
                                    <div class="form-group">
                                        <label>No Kartu</label>
                                        <input type="text" class="form-control" value="{{ $pasien->no_peserta }}"
                                            name="no_bpjs" readonly />
                                    </div>
                                    <div class="form-group">
                                        <label>Nama Pasien</label>
                                        <input type="text" class="form-control" value="{{ $pasien->nm_pasien }}"
                                            name="nama_pasien" readonly />
                                    </div>
                                    <div class="form-group">
                                        <label>Jenis Rawat</label>
                                        <input type="text" class="form-control" value="Rawat Jalan"
                                            name="jenis_rawat" readonly />
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Tgl Lahir</label>
                                        <input type="text" class="form-control" value="{{ $pasien->tgl_lahir }}"
                                            name="tgl_lahir" readonly />
                                    </div>
                                    <div class="form-group">
                                        <label>Jenis Kelamin</label>
                                        <input type="text" class="form-control" value="{{ $pasien->jk }}"
                                            name="jk" readonly />
                                    </div>
                                    <div class="form-group">
                                        <label>Tgl Registrasi</label>
                                        <input type="text" class="form-control"
                                            value="{{ $pasien->tgl_registrasi }}" name="tgl_registrasi" readonly />
                                    </div>
                                    <div class="form-group">
                                        <label>Poli Dituju</label>
                                        <input type="text" class="form-control" value="{{ $pasien->nm_poli }}"
                                            name="nm_poli" readonly />
                                    </div>
                                    <div class="form-group">
                                        <label>Periode</label>
                                        <select name="periode" class="form-control" required>
                                            <option value="">Pilih</option>
                                            @foreach ($periodeKlaim as $periode)
                                                <option value="{{ $periode->id }}">{{ $periode->periode }}</option>
                                            @endforeach
                                        </select>
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
            </div>
        </div>
    @else
        <div class="modal fade" id="modal-pengajuan-kronis-edit">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form method="POST"
                        action="/vedika/pengajuankronis/{{ Crypt::encrypt($statusPengajuanKronis->id) }}/update">
                        @csrf
                        <div class="modal-header">
                            <h4 class="modal-title">Edit Pengajuan Kronis</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>No Rawat pasien</label>
                                        <input type="text" class="form-control" value="{{ $pasien->no_rawat }}"
                                            name="no_rawat" readonly />
                                    </div>
                                    <div class="form-group">
                                        <label>No Resep</label>
                                        <input type="text" class="form-control"
                                            value="{{ $statusPengajuanKronis->no_resep }}" name="no_resep" required />
                                    </div>
                                    <div class="form-group">
                                        <label>No SEP</label>
                                        <input type="text" class="form-control"
                                            value="{{ !empty($dataSep->no_sep) ? $dataSep->no_sep : '' }}"
                                            name="no_sep" readonly />
                                    </div>
                                    <div class="form-group">
                                        <label>No Kartu</label>
                                        <input type="text" class="form-control" value="{{ $pasien->no_peserta }}"
                                            name="no_bpjs" readonly />
                                    </div>
                                    <div class="form-group">
                                        <label>Nama Pasien</label>
                                        <input type="text" class="form-control" value="{{ $pasien->nm_pasien }}"
                                            name="nama_pasien" readonly />
                                    </div>
                                    <div class="form-group">
                                        <label>Jenis Rawat</label>
                                        <input type="text" class="form-control" value="Rawat Jalan"
                                            name="jenis_rawat" readonly />
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Tgl Lahir</label>
                                        <input type="text" class="form-control" value="{{ $pasien->tgl_lahir }}"
                                            name="tgl_lahir" readonly />
                                    </div>
                                    <div class="form-group">
                                        <label>Jenis Kelamin</label>
                                        <input type="text" class="form-control" value="{{ $pasien->jk }}"
                                            name="jk" readonly />
                                    </div>
                                    <div class="form-group">
                                        <label>Tgl Registrasi</label>
                                        <input type="text" class="form-control"
                                            value="{{ $pasien->tgl_registrasi }}" name="tgl_registrasi" readonly />
                                    </div>
                                    <div class="form-group">
                                        <label>Poli Dituju</label>
                                        <input type="text" class="form-control" value="{{ $pasien->nm_poli }}"
                                            name="nm_poli" readonly />
                                    </div>
                                    <div class="form-group">
                                        <label>Periode</label>
                                        <select name="periode" class="form-control" required>
                                            <option value="">Pilih</option>
                                            @foreach ($periodeKlaim as $periode)
                                                <option value="{{ $periode->id }}"
                                                    {{ $statusPengajuanKronis->periode_klaim_id == $periode->id ? 'selected' : '' }}>
                                                    {{ $periode->periode }}</option>
                                            @endforeach
                                        </select>
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
            </div>
        </div>
    @endif
    {{-- //Verifikasi modal --}}
    <div class="modal fade" id="modal-verif">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="/vedika/verifikasi">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">Verifikasi Berkas Pasien</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- text input -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label>No Rawat pasien</label>
                                    <input type="text" class="form-control" value="{{ $pasien->no_rawat }}"
                                        name="no_rawat" readonly />
                                    <input type="hidden" class="form-control" value="Rajal" name="statusRawat"
                                        readonly />
                                </div>
                                <div class="form-group">
                                    <label>Catatan Verifikasi</label>
                                    <textarea name="catatan" class="form-control"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control" required>
                                        <option value="">Pilih</option>
                                        <option value="0"><span class="badge badge-warning"><i
                                                    class="fas fa-exclamation-triangle"></i> Perbaikan</span>
                                        </option>
                                        <option value="1"><i class="fas fa-check-circle"></i> Selesai</option>
                                    </select>
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
    {{-- //Verifikasi edit modal --}}
    @if (!empty($statusVerif))
        <div class="modal fade" id="modal-edit-verif">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="/vedika/verifikasi/{{ Crypt::encrypt($pasien->no_rawat) }}">
                        @csrf
                        <div class="modal-header">
                            <h4 class="modal-title">Verifikasi Berkas Pasien</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>No Rawat pasien</label>
                                        <input type="text" class="form-control" value="{{ $pasien->no_rawat }}"
                                            name="no_rawat" readonly />
                                    </div>
                                    <div class="form-group">
                                        <label>Catatan Verifikasi</label>
                                        <textarea name="catatan" class="form-control">{{ $statusVerif->verifikasi }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="status" class="form-control" required>
                                            <option value="">Pilih</option>
                                            <option value="0" {{ $statusVerif->status == 0 ? 'selected' : '' }}>
                                                Perbaikan</span>
                                            </option>
                                            <option value="1" {{ $statusVerif->status == 1 ? 'selected' : '' }}>
                                                Selesai
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Verifikator</label>
                                        <input type="text" class="form-control"
                                            value="{{ !empty($statusVerif->verificator->name) ? $statusVerif->verificator->name : '' }}"
                                            name="verificator" readonly />
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
            </div>
        </div>
    @endif

    {{-- //Gabung File --}}
    <div class="modal fade" id="modal-gabung-file">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Gabung Berkas Pasien</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- text input -->
                        <div class="col-12">
                            @can('vedika-kronis-create')
                                <a href="/vedika/obatkronis/{{ Crypt::encrypt($pasien->no_rawat) }}/gabungpdf"
                                    class="btn btn-success btn-sm btn-block" target="_blank">
                                    <i class="fas fa-sync-alt"></i></i> Gabung PDF Kronis</a>
                                    @if($dataSep)
                                        <a href="/vedika/obatkronis/{{ !empty($pasien->no_rawat)? Crypt::encrypt($pasien->no_rawat):Crypt::encrypt($dataSep->noSep) }}/viewgabungpdf"
                                            class="btn btn-danger btn-sm btn-block" target="_blank">
                                            <i class="fas fa-file-download"></i> Buka PDF Kronis</a>
                                        {{-- <a href="/vedika/obatkronis/{{ !empty($pasien->no_rawat)? Crypt::encrypt($pasien->no_rawat):Crypt::encrypt($dataSep->noSep) }}/deletepdf"
                                            class="btn btn-secondary btn-sm btn-block">
                                            <i class="fas fa-trash"></i> Hapus File PDF Kronis</a> --}}
                                    @endif
                            @endcan
                        </div>
                    </div>
                </div>
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
