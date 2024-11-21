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
                    $statusVerif = App\VedikaVerif::cekVerif($pasien->no_rawat, 'Ranap');
                    $statusPengajuan = App\DataPengajuanKlaim::cekPengajuan($pasien->no_rawat, 'Rawat Inap');
                    // dd($dataSep);
                @endphp
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            Status Pengajuan :
                            {{ !empty($statusPengajuan)
                                ? \Carbon\Carbon::parse($statusPengajuan->periodeKlaim->periode)->format('F Y')
                                : '' }}
                            @can('vedika-upload')
                                @if (!empty($statusPengajuan))
                                    <a href="/vedika/pengajuan/{{ Crypt::encrypt($statusPengajuan->id) }}/delete"
                                        class="delete-confirm text-danger" data-toggle="tooltip" data-placement="bottom"
                                        title="Delete">
                                        <i class="fas fa-ban"></i>
                                    </a>
                                @endif
                            @endcan
                            <div class="float-right">
                                @can('vedika-upload')
                                    @if (empty($statusPengajuan))
                                        <button class="btn btn-success btn-sm" data-toggle="modal"
                                            data-target="#modal-pengajuan">
                                            <i class="fas fa-plus-circle"></i> Pengajuan Klaim</a>
                                        </button>
                                    @else
                                        <button class="btn btn-warning btn-sm" data-toggle="modal"
                                            data-target="#modal-pengajuan-edit">
                                            <i class="fas fa-pen"></i> Edit Pengajuan Klaim</a>
                                        </button>
                                    @endif
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

                                <a href="/vedika/ranap/{{ Crypt::encrypt($pasien->no_rawat) }}/detailpdf"
                                    class="btn btn-primary btn-sm" target="_blank">
                                    <i class="far fa-file-pdf"></i> PDF</a>
                                </a>
                                <button class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#modal-gabung-file">
                                    <i class="fas fa-file-download"></i> Gabung PDF</a>
                                </button>
                            </div>
                        </div>
                    </div>
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
                                                    src="{{ asset('image/logoBPJS.svg') }}" alt="Logo BPJS" width="300">
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
                                            <td style="width:50%" class="pt-0 pb-0">: {{ $dataSep->no_sep ? $dataSep->no_sep:'-' }}</td>
                                            <td class="pt-0 pb-0 text-center" colspan="2"></td>
                                            {{-- <td class="pt-0 pb-0"></td> --}}
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
                                            <td class="pt-0 pb-0">: {{ $dataSep->nmdpdjp }}</td>
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
                    {{-- DATA EKLAIM --}}
                    @if (!empty($dataKlaim))
                        <div class="card">
                            <div class="card-header">Eklaim</div>
                            <div class="card-body">
                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <td style="width: 5%; border-bottom: 3px solid black; " rowspan="3">
                                            <img src="{{ asset('image/LogoKemenkesIcon.png') }}" alt="Logo Kemenkes"
                                                width="80" />
                                        </td>
                                        <td class="align-top" rowspan="3" style="border-bottom: 3px solid black; ">
                                            <b>KEMENTERIAN KESEHATAN REPUBLIK INDONESIA</b><br>
                                            <i>Berkas Klaim Individual Pasien</i>
                                        </td>
                                        <td class="align-top text-right" rowspan="3"
                                            style="border-bottom: 3px solid black; ">
                                            JKN<br>
                                            {{ $dataKlaim->tgl_pulang }}
                                        </td>
                                    </tr>
                                </table>
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td style="width: 15%; padding-top: 10px; padding-left:5px; padding-bottom:0px;">
                                            Kode
                                            Rumah Sakit</td>
                                        <td style="width: 35%;padding: 0;padding-top: 10px;">: {{ $dataKlaim->kode_rs }}
                                        </td>
                                        <td style="width: 15%;padding: 0;padding-top: 10px;">Kelas Rumah Sakit </td>
                                        <td style="width: 35%;padding: 0;padding-top: 10px;">: {{ $dataKlaim->kelas_rs }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 15%;padding: 0; padding-left:5px;">Nama RS</td>
                                        <td style="width: 35%;padding: 0;">: RSU PUSAT SURAKARTA</td>
                                        <td style="width: 15%;padding: 0;">Jenis Tarif</td>
                                        <td style="width: 35%;padding: 0;">:
                                            {{ $dataKlaim->kode_tarif == 'CP' ? 'TARIF RS KELAS C PEMERINTAH' : $dataKlaim->kode_tarif }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 15%;padding: 0; padding-left:5px;">Nomor Peserta</td>
                                        <td style="width: 35%;padding: 0;">: {{ $dataKlaim->nomor_kartu }}</td>
                                        <td style="width: 15%;padding: 0;">Nomor SEP</td>
                                        <td style="width: 35%;padding: 0;">: {{ $dataKlaim->nomor_sep }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 15%;padding: 0; padding-left:5px;">Nomor Rekam Medis</td>
                                        <td style="width: 35%;padding: 0;">: {{ $dataKlaim->nomor_rm }}</td>
                                        <td style="width: 15%;padding: 0;">Tanggal Masuk</td>
                                        <td style="width: 35%;padding: 0;">: {{ $dataKlaim->tgl_masuk }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 15%;padding: 0; padding-left:5px;">Umur Tahun</td>
                                        <td style="width: 35%;padding: 0;">: {{ $dataKlaim->umur_tahun }}</td>
                                        <td style="width: 15%;padding: 0;">Tanggal Keluar</td>
                                        <td style="width: 35%;padding: 0;">: {{ $dataKlaim->tgl_pulang }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 15%;padding: 0; padding-left:5px;">Umur Hari</td>
                                        <td style="width: 35%;padding: 0;">: {{ $dataKlaim->umur_hari }}</td>
                                        <td style="width: 15%;padding: 0;">Jenis Perawatan</td>
                                        <td style="width: 35%;padding: 0;">:
                                            {{ $dataKlaim->jenis_rawat == '1' ? '1 - Rawat Inap' : '2 - Rawat Jalan' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 15%;padding: 0; padding-left:5px;">Tanggal Lahir</td>
                                        <td style="width: 35%;padding: 0;">: {{ $dataKlaim->tgl_lahir }}</td>
                                        <td style="width: 15%;padding: 0;">Cara Pulang</td>
                                        <td style="width: 35%;padding: 0;">:
                                            {{ $dataKlaim->discharge_status == '1' ? '1 - Atas Persetujuan Dokter' : $dataKlaim->discharge_status }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 15%;padding: 0; padding-left:5px;">Jenis Kelamin</td>
                                        <td style="width: 35%;padding: 0;">:
                                            {{ $dataKlaim->gender == '1' ? '1 - Laki-laki' : '2 - Perempuan' }}</td>
                                        <td style="width: 15%;padding: 0;">LOS</td>
                                        <td style="width: 35%;padding: 0;">: {{ $dataKlaim->los }} hari</td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="width: 15%;padding: 0; padding-left:5px; padding-bottom:10px; border-bottom: 1px solid black;">
                                            Kelas Perawatan</td>
                                        <td
                                            style="width: 35%;padding: 0; padding-bottom:10px; border-bottom: 1px solid black;">
                                            : {{ $dataKlaim->kelas_rawat }} - Kelas {{ $dataKlaim->kelas_rawat }}</td>
                                        <td
                                            style="width: 15%;padding: 0; padding-bottom:10px; border-bottom: 1px solid black;">
                                            Berat Lahir</td>
                                        <td
                                            style="width: 35%;padding: 0; padding-bottom:10px; border-bottom: 1px solid black;">
                                            :
                                            {{ $dataKlaim->berat_lahir == '0' ? '-' : $dataKlaim->berat_lahir }}</td>
                                    </tr>

                                </table>
                                <table class="table table-borderless table-sm">
                                    @php
                                        $diagnosa = explode('#', $dataKlaim->diagnosa_inagrouper);
                                        $procedure = explode('#', $dataKlaim->procedure_inagrouper);
                                    @endphp
                                    <tr>
                                        <td style="width: 15%; padding-top: 10px; padding-left:5px; padding-bottom:0px;">
                                            Diagnosa Utama</td>
                                        <td style="width: 5%;padding: 0;padding-top: 10px;">: {{ $diagnosa[0] }}
                                        </td>
                                        <td style="width: 80%;padding: 0;padding-top: 10px;" colspan=2>
                                            {{ \App\Penyakit::getName($diagnosa[0]) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 15%;  padding-left:5px; padding-bottom:0px;">
                                            Diagnosa Sekunder</td>
                                        @for ($i = 1; $i < count($diagnosa); $i++)
                                            <td style="width: 5%;padding: 0;">: {{ $diagnosa[$i] }}
                                            </td>
                                            <td style="width: 80%;padding: 0;" colspan=2>
                                                {{ \App\Penyakit::getName($diagnosa[$i]) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 15%;  padding-left:5px; padding-bottom:0px;">
                                        </td>
                    @endfor
                    <td colspan=2>&nbsp</td>
                    @for ($j = 0; $j < count($procedure); $j++)
                        <tr>
                            <td style="width: 15%; ; padding-left:5px; padding-bottom:0px;">
                                {{ $j == 0 ? 'Prosedur' : '' }}</td>
                            <td style="width: 5%;padding: 0;">: {{ $procedure[$j] }}
                            </td>
                            <td style="width: 80%;padding: 0;" colspan=2>
                                {{ \App\Penyakit::getProcedure($procedure[$j]) }}</td>
                        </tr>
                    @endfor
                    </table>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td style="width: 15%;padding: 0; padding-left:5px; padding-top:20px">
                                ADL Sub Acute</td>
                            <td style="width: 35%;padding: 0; padding-top:20px">
                                : {{ $dataKlaim->adl_sub_acute == 0 ? '-' : $dataKlaim->adl_sub_acute }} </td>
                            <td style="width: 15%;padding: 0; padding-top:20px">
                                ADL Chronic</td>
                            <td style="width: 35%;padding: 0; padding-top:20px">
                                :
                                {{ $dataKlaim->adl_chronic == '0' ? '-' : $dataKlaim->adl_chronic }}</td>
                        </tr>
                    </table>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <th colspan=5 style='border-bottom: 1px solid black'>Hasil Grouping </th>
                        </tr>
                        <tr>
                            <td style="width: 15%;padding: 0; padding-left:5px; padding-top:20px">
                                INA-CBG</td>
                            <td style="width: 15%;padding: 0; padding-top:20px">
                                :
                                {{ $dataKlaim->grouper->response ? $dataKlaim->grouper->response->cbg->code : '' }}
                            </td>
                            <td style="width: 50%;padding: 0; padding-top:20px">
                                {{ $dataKlaim->grouper->response ? $dataKlaim->grouper->response->cbg->description : '' }}
                            </td>
                            <td style="width: 10%;padding: 0; padding-top:20px; text-align:right">
                                Rp</td>
                            <td style="width: 10%;padding: 0; padding-top:20px; text-align:right">
                                {{ isset($dataKlaim->grouper->response->cbg->tariff) ? number_format($dataKlaim->grouper->response->cbg->tariff, 2, ',', '.') : number_format(0, 2, ',', '.') }}
                            </td>

                        </tr>
                        <tr>
                            <td style="width: 15%;padding: 0; padding-left:5px; ">
                                Sub Acute</td>
                            <td style="width: 15%;padding: 0; ">
                                : - </td>
                            <td style="width: 50%;padding: 0; ">
                                -</td>
                            <td style="width: 10%;padding: 0; text-align:right">
                                Rp</td>
                            <td style="width: 10%;padding: 0; text-align:right">
                                {{ number_format(0, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td style="width: 15%;padding: 0; padding-left:5px; ">
                                Chronic</td>
                            <td style="width: 15%;padding: 0; ">
                                : - </td>
                            <td style="width: 50%;padding: 0; ">
                                -</td>
                            <td style="width: 10%;padding: 0; text-align:right">
                                Rp</td>
                            <td style="width: 10%;padding: 0; text-align:right">
                                {{ number_format(0, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td style="width: 15%;padding: 0; padding-left:5px; border-bottom: 1px solid black;">
                                Special CMG</td>
                            <td style="width: 15%;padding: 0; border-bottom: 1px solid black;">
                                : - </td>
                            <td style="width: 50%;padding: 0; border-bottom: 1px solid black; ">
                                -</td>
                            <td style="width: 10%;padding: 0; border-bottom: 1px solid black; text-align:right">
                                Rp</td>
                            <td style="width: 10%;padding: 0; border-bottom: 1px solid black; text-align:right">
                                {{ number_format(0, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td
                                style="width: 15%;padding: 0; padding-left:5px; padding-top:20px; padding-bottom:50px; border-bottom: 3px solid black">
                                Total Tarif</td>
                            <td
                                style="width: 15%;padding: 0; padding-top:20px; padding-bottom:50px; border-bottom: 3px solid black">
                                : </td>
                            <td
                                style="width: 50%;padding: 0; padding-top:20px; padding-bottom:50px; border-bottom: 3px solid black">
                                &nbsp</td>
                            <td
                                style="width: 10%;padding: 0; padding-top:20px; text-align:right; padding-bottom:50px; border-bottom: 3px solid black">
                                Rp</td>
                            <td
                                style="width: 10%;padding: 0; padding-top:20px; text-align:right; padding-bottom:50px; border-bottom: 3px solid black">
                                {{ isset($dataKlaim->grouper->response->cbg->tariff) ? number_format($dataKlaim->grouper->response->cbg->tariff, 2, ',', '.') : number_format(0, 2, ',', '.') }}
                            </td>

                        </tr>
                        <tr>
                            <td style="width: 15%;padding: 0; padding-left:5px;">
                                Generated</td>
                            <td style="width: 75%;padding: 0;" colspan='3'>
                                : Eklaim
                                {{ $dataKlaim->grouper->response ? $dataKlaim->grouper->response->inacbg_version : '' }}
                                @
                                {{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}</td>
                            <td style="width: 10%;padding: 0;; text-align:right">
                                Lembar 1 / 1</td>
                        </tr>
                    </table>
                </div>
            </div>
            @endif
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
            @if ($spri)
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
            @endif
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
                                                    <td class="pt-0 pb-0">: {{ $order->nm_dokter }}
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
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                @endif
            @endif
            {{-- End Radiologi --}}
            {{-- Data Obat --}}
            {{-- @if (!empty($resepObat))
                @foreach ($resepObat as $index => $resepObat)
                <div class="card">
                    <div class="card-header">Obat</div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td style="width:20%" rowspan="3"><img src="{{ asset('image/logorsup.jpg') }}"
                                        alt="Logo RSUP" width="100">
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
                            <div class="progress-bar progress-bar bg-black" role="progressbar" aria-valuenow="100"
                                aria-valuemin="0" aria-valuemax="100" style="width: 100%">

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
                                        {{ App\Vedika::getSep($resepObat->no_rawat) != null ?
                                        App\Vedika::getSep($resepObat->no_rawat)->no_sep : '' }}
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
                            <div class="progress-bar progress-bar bg-black" role="progressbar" aria-valuenow="100"
                                aria-valuemin="0" aria-valuemax="100" style="width: 100%">

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
                                // dd($obatJadi, $obatRacik);
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
                                        {{ \App\Vedika::aturanObatJadi($pasien->no_rawat, $listObat->kode_brng)->aturan
                                        }}
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
                                        $listObatRacik->jam,
                                        )->count();
                                        $jumlah = $jumlah - 1;
                                        @endphp
                                        (@foreach (\App\Vedika::getRacikan($pasien->no_rawat, $listObatRacik->jam) as $index => $listRacikan)
                                        {{ $listRacikan->nama_brng }}
                                        {{ \App\Vedika::getJmlRacikan($pasien->no_rawat, $listRacikan->kode_brng)->jml
                                        }}{{ $index != $jumlah ? ',' : '' }}
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
                                <td class="text-center pt-0 pb-0" style="width: 30%"> {!!
                                    QrCode::size(100)->generate($qr_dokter) !!}
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
                @endif --}}
            {{-- End data Obat --}}
            {{-- data Triase IGD --}}
            @if (!empty($dataTriase))
                <div class="card">
                    <div class="card-header">Data Triase</div>
                    <div class="card-body">
                        @php
                            for ($i = 1; $i <= 5; $i++) {
                                foreach ($skala[$i] as $dataPemeriksaan) {
                                    if ($dataPemeriksaan->nama_pemeriksaan == 'ASSESMENT TRIASE') {
                                        $urgensi = $dataPemeriksaan->pengkajian_skala;
                                    }
                                }
                            }
                            if (!empty($primer)) {
                                $plan = $primer->plan;
                            } elseif (!empty($sekunder)) {
                                $plan = $sekunder->plan;
                            } else {
                                $plan = null;
                            }

                            if ($plan == 'Zona Hijau') {
                                $bg_color = 'bg-success';
                            } elseif ($plan == 'Zona Kuning') {
                                $bg_color = 'bg-warning';
                            } elseif ($plan == 'Zona Merah') {
                                $bg_color = 'bg-danger';
                            } else {
                                $bg_color = '';
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
                                            <div class="col-8">: {{ $dataTriase->no_rkm_medis }} /
                                                {{ $dataTriase->no_ktp }}
                                            </div>
                                            <div class="col-4">Nama</div>
                                            <div class="col-8">: {{ $dataTriase->nm_pasien }}
                                                ({{ $dataTriase->jk }})
                                            </div>
                                            <div class="col-4">Tanggal Lahir</div>
                                            <div class="col-8">:
                                                {{ \Carbon\Carbon::parse($dataTriase->tgl_lahir)->format('d-m-Y') }}
                                            </div>
                                            <div class="col-4">Alamat</div>
                                            <div class="col-8">: {{ $dataTriase->alamat }}</div>
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
                                        Triase dilakukan segera setelah pasien datang dan sebelum pasien/
                                        keluarga
                                        mendaftar
                                        di TPP IGD
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="py-0 pl-5 border border-dark" colspan="5">
                                        Tanggal Kunjungan :
                                        {{ \Carbon\Carbon::parse($dataTriase->tgl_kunjungan)->format('d-m-Y') }}
                                    </td>
                                    <td class="py-0 border border-dark" colspan="5">
                                        Pukul :
                                        {{ \Carbon\Carbon::parse($dataTriase->tgl_kunjungan)->format('H:i:s') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-0 border border-dark" colspan="3">
                                        Cara Datang
                                    </td>
                                    <td class="py-0 border border-dark" colspan="7">
                                        {{ $dataTriase->cara_masuk }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-0 border border-dark" colspan="3">
                                        Macam Kasus
                                    </td>
                                    <td class="py-0 border border-dark" colspan="7">
                                        {{ $dataTriase->macam_kasus }}
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
                                            Suhu (C) : {{ $dataTriase->suhu }}, Nyeri :
                                            {{ $dataTriase->nyeri }},
                                            Tensi :
                                            {{ $dataTriase->tekanan_darah }}, Nadi(/menit) :
                                            {{ $dataTriase->nadi }},
                                            Saturasi
                                            O2(%) : {{ $dataTriase->saturasi_o2 }}, Respirasi(/menit) :
                                            {{ $dataTriase->pernapasan }}
                                        </td>

                                    </tr>
                                    <tr>
                                        <td class="py-0 border border-dark" colspan="3">
                                            KEBUTUHAN KHUSUS
                                        </td>
                                        <td class="py-0 border border-dark" colspan="7">
                                            {{ !empty($primer->kebutuhan_khusus) ? $primer->kebutuhan_khusus : '' }}
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td class="py-0 border border-dark" colspan="3">
                                            ANAMNESA SINGKAT
                                        </td>
                                        <td class="py-0 border border-dark" colspan="7">
                                            {{ !empty($sekunder->anamnesa_singkat) ? $sekunder->anamnesa_singkat : '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-0 border border-dark" colspan="3">
                                            TANDA VITAL
                                        </td>
                                        <td class="py-0 border border-dark" colspan="7">
                                            Suhu (C) : {{ $dataTriase->suhu }}, Nyeri :
                                            {{ $dataTriase->nyeri }},
                                            Tensi :
                                            {{ $dataTriase->tekanan_darah }}, Nadi(/menit) :
                                            {{ $dataTriase->nadi }},
                                            Saturasi
                                            O2(%) : {{ $dataTriase->saturasi_o2 }}, Respirasi(/menit) :
                                            {{ $dataTriase->pernapasan }}
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td class="py-0 text-center table-primary border border-dark" colspan="3">
                                        PEMERIKSAAN
                                    </td>
                                    <td class="py-0 text-center {{ $bg_color }} border border-dark" colspan="7">
                                        URGENSI
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
                    {{ $primer != null ? $primer->plan : '' }}
                    {{ $sekunder != null ? $sekunder->plan : '' }}
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
                    {{ $primer != null ? \Carbon\Carbon::parse($primer->tanggaltriase)->format('d-m-Y H:i:s') : '' }}
                    {{ $sekunder != null ? \Carbon\Carbon::parse($sekunder->tanggaltriase)->format('d-m-Y H:i:s') : '' }}
                </td>
            </tr>
            <tr>
                <td class="py-0 border border-dark" colspan="3">
                    Catatan
                </td>
                <td class="py-0 border border-dark" colspan="7">
                    {{ $primer != null ? $primer->catatan : '' }}
                    {{ $sekunder != null ? $sekunder->catatan : '' }}
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
                        } elseif (!empty($sekunder)) {
                            $nip_petugas = $sekunder->nip;
                            $nama_petugas = $sekunder->nama;
                            $tanggal_hasil = \Carbon\Carbon::parse($sekunder->tanggaltriase)->format('d-m-Y');
                        } else {
                            $nip_petugas = null;
                            $nama_petugas = null;
                            $tanggal_hasil = null;
                        }
                        $qr_petugas =
                            'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani
                                            secara elektronik oleh' .
                            "\n" .
                            $nama_petugas .
                            "\n" .
                            'ID ' .
                            $nip_petugas .
                            "\n" .
                            $tanggal_hasil;
                    @endphp
                    <div>
                        {{ $primer != null ? $primer->nama : '' }}
                        {{ $sekunder != null ? $sekunder->nama : '' }}
                        <div class="float-right pt-3 pb-3">{!! QrCode::size(100)->generate($qr_petugas) !!}</div>
                    </div>
                </td>
            </tr>
            </tbody>
            </table>
        </div>
        </div>
        @endif
        {{-- End Data Triase --}}
        {{-- Data Ringkasan IGD --}}
        @if (!empty($resumeIgd))
            <div class="card">
                <div class="card-header">Ringkasan Pasien IGD</div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <thead>
                            <tr>
                                <td style="width:3%" class="pr-0 align-middle"><img
                                        src="{{ asset('image/logorsup.jpg') }}" alt="Logo RSUP" width="30"
                                        class="px-0 py-0">
                                </td>
                                <td class="pt-2 pb-0 pl-1 align-middle">
                                    <h5 class="px-0 py-0">RSUP SURAKARTA</h5>
                                </td>
                            </tr>
                        </thead>
                    </table>
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th class="pt-0 pb-0 text-center align-middle border border-dark" rowspan="5"
                                    style="width: 40%">
                                    <h4>RINGKASAN PASIEN<br> GAWAT DARURAT</h4>
                                </th>
                                <th class="pt-0 pb-0 border border-dark border-right-0 border-bottom-0"
                                    style="width: 10%">No. RM
                                </th>
                                <th class="pt-0 pb-0 border border-dark border-left-0 border-bottom-0">:
                                    {{ $dataRingkasan->no_rkm_medis }}</th>
                            </tr>
                            <tr>
                                <th class="pt-0 pb-0 border-0">NIK </th>
                                <th class="pt-0 pb-0 border border-dark border-left-0 border-bottom-0 border-top-0">
                                    : {{ $dataRingkasan->no_ktp }}</th>
                            </tr>
                            <tr>
                                <th class="pt-0 pb-0 border-0">Nama Pasien </th>
                                <th class="pt-0 pb-0 border border-dark border-left-0 border-bottom-0 border-top-0">
                                    : {{ $dataRingkasan->nm_pasien }}</th>
                            </tr>
                            <tr>
                                <th class="pt-0 pb-0 border-0">Tanggal Lahir </th>
                                <th class="pt-0 pb-0 border border-dark border-left-0 border-bottom-0 border-top-0">
                                    : {{ $dataRingkasan->tgl_lahir }}</th>
                            </tr>
                            <tr>
                                <th class="pt-0 pb-0 border border-dark border-right-0 border-top-0">Alamat</th>
                                <th class="pt-0 pb-0 border border-dark border-left-0 border-top-0">:
                                    {{ $dataRingkasan->alamat }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border border-dark border-bottom-0 border-top-0" colspan="3">
                                    <b>Waktu
                                        Kedatangan</b> Tanggal :
                                    {{ \Carbon\Carbon::parse($dataRingkasan->tgl_registrasi)->format('d-m-Y') }}
                                    Jam :
                                    {{ $dataRingkasan->jam_reg }}
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-dark border-bottom-0 border-top-0" colspan="3">
                                    <b>Diagnosis:</b>
                                </td>
                            </tr>
                            <tr>
                                <td class="pl-5 border border-dark border-bottom-0 border-top-0" colspan="3">
                                    {{ $dataRingkasan->diagnosis }}</td>
                            </tr>
                            <tr>
                                <td class="border border-dark border-bottom-0 border-top-0" colspan="3">
                                    <b>Kondisi Pada Saat Keluar:</b>
                                </td>
                            </tr>
                            <tr>
                                <td class="pl-5 border border-dark border-bottom-0 border-top-0" colspan="3">
                                    {{ $resumeIgd->kondisi_pulang }}</td>
                            </tr>
                            <tr>
                                <td class="border border-dark border-bottom-0 border-top-0" colspan="3">
                                    <b>Tindak
                                        Lanjut:</b>
                                </td>
                            </tr>
                            <tr>
                                <td class="pl-5 border border-dark border-bottom-0 border-top-0" colspan="3">
                                    {{ $resumeIgd->tindak_lanjut }}</td>
                            </tr>
                            <tr>
                                <td class="border border-dark border-bottom-0 border-top-0" colspan="3"><b>Obat
                                        yang dibawa pulang:</b></td>
                            </tr>
                            @php
                                $obat = explode("\n", $resumeIgd->obat_pulang);
                            @endphp
                            @foreach ($obat as $obatPulang)
                                <tr>
                                    <td class="pl-5 pt-0 pb-0 border border-dark border-bottom-0 border-top-0"
                                        colspan="3">
                                        {{ $obatPulang }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td class="border border-dark border-bottom-0 border-top-0" colspan="3">
                                    <b>Edukasi:</b>
                                </td>
                            </tr>
                            <tr>
                                <td class="pl-5 pt-0 pb-0 border border-dark border-bottom-0 border-top-0" colspan="3">
                                    {{ $resumeIgd->edukasi }}</td>
                            </tr>
                            <tr>
                                <td class="border border-dark border-bottom-0 border-top-0" colspan="3">Waktu
                                    Selesai Pelayanan IGD Tanggal:
                                    {{ \Carbon\Carbon::parse($resumeIgd->tgl_selesai)->format('d-m-Y') }} Jam:
                                    {{ \Carbon\Carbon::parse($resumeIgd->tgl_selesai)->format('H:i:s') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-dark border-bottom-0 border-top-0" colspan="3">Tanda
                                    Tangan Dokter</td>
                            </tr>
                            <tr>
                                @php
                                    $qr_dokter =
                                        'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara
                                    elektronik oleh' .
                                        "\n" .
                                        $dataRingkasan->nm_dokter .
                                        "\n" .
                                        'ID ' .
                                        $dataRingkasan->kd_dokter .
                                        "\n" .
                                        \Carbon\Carbon::parse($resumeIgd->tgl_selesai)->format('d-m-Y');
                                @endphp
                                <td class="pt-0 pb-0 pl-5 border border-dark border-bottom-0 border-top-0" colspan="3">
                                    {!! QrCode::size(100)->generate($qr_dokter) !!} </td>
                            </tr>
                            <tr>
                                <td class="border border-dark border-top-0" colspan="3">Nama :
                                    {{ $dataRingkasan->nm_dokter }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
        {{-- End Ringksan IGD --}}
        {{-- Pasien Operasi --}}

        {{-- Data Operasi Multi Tab --}}
        @if ($dataOperasi2 != null && $dataOperasi1)
            @if ($dataOperasi2->count() > 0)
                <div class="card card-primary card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                            @foreach ($dataOperasi2 as $index => $listOperasi)
                                <li class="nav-item">
                                    <a class="nav-link {{ $index == 0 ? 'active' : '' }}" id="custom-tabs-four-home-tab"
                                        data-toggle="pill"
                                        href="#custom-tabs-lap-{{ \Carbon\Carbon::parse($listOperasi->tgl_operasi)->format('YmdHis') }}"
                                        role="tab" aria-controls="custom-tabs-four-home" aria-selected="true">
                                        Data
                                        Operasi Tanggal {{ $listOperasi->tgl_operasi }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="custom-tabs-four-tabContent">
                            @foreach ($dataOperasi2 as $index => $listOperasi)
                                <div class="tab-pane fade show {{ $index == 0 ? 'active' : '' }}"
                                    id="custom-tabs-lap-{{ \Carbon\Carbon::parse($listOperasi->tgl_operasi)->format('YmdHis') }}"
                                    role="tabpanel"
                                    aria-labelledby="#custom-tabs-lap-{{ \Carbon\Carbon::parse($listOperasi->tgl_operasi)->format('YmdHis') }}">
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
                                    <table class="table table-borderless mb-0">
                                        <thead>
                                            <tr>
                                                <td class="align-middle py-0 border border-dark border-top-5 border-left-0 border-right-0"
                                                    colspan="6">
                                                    <h3>LAPORAN OPERASI</h3>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle py-0">
                                                    Nama Pasien
                                                </td>
                                                <td class="align-middle py-0" colspan="2">
                                                    : {{ $pasien->nm_pasien }}
                                                </td>
                                                <td class="align-middle py-0">
                                                    No. Rekam Medis
                                                </td>
                                                <td class="align-middle py-0" colspan="2">
                                                    : {{ $pasien->no_rkm_medis }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle py-0">
                                                    Umur
                                                </td>
                                                <td class="align-middle py-0" colspan="2">
                                                    :
                                                    {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->diff(\Carbon\Carbon::parse($listOperasi->tgl_operasi))->format('%y
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            Th %m Bl %d Hr') }}
                                                </td>
                                                <td class="align-middle py-0">
                                                    Ruang
                                                </td>
                                                <td class="align-middle py-0" colspan="2">
                                                    : {{ $pasien->kd_kamar }} {{ $pasien->nm_bangsal }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle py-0">
                                                    Tgl Lahir
                                                </td>
                                                <td class="align-middle py-0" colspan="2">
                                                    :
                                                    {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->format('d/m/Y') }}
                                                </td>
                                                <td class="align-middle py-0">
                                                    Jenis Kelamin
                                                </td>
                                                <td class="align-middle py-0" colspan="2">
                                                    : {{ $pasien->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                                </td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="table-secondary">
                                                <td class="text-center align-middle py-0 border border-dark"
                                                    colspan="6">
                                                    <h5>PRE SURGICAL ASSESMENT</h5>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle py-0">
                                                    Tanggal
                                                </td>
                                                <td class="align-middle py-0">
                                                    :
                                                    {{ \Carbon\Carbon::parse($dataOperasi1->tgl_perawatan)->format('d/m/Y') }}
                                                </td>
                                                <td class="align-middle py-0 text-right">
                                                    Waktu
                                                </td>
                                                <td class="align-middle py-0">
                                                    : {{ $dataOperasi1->jam_rawat }}
                                                </td>
                                                <td class="align-middle py-0">
                                                    Alergi
                                                </td>
                                                <td class="align-middle py-0">
                                                    : {{ $dataOperasi1->alergi }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td
                                                    class="align-middle py-0 border border-dark border-left-0 border-right-0 border-top-0">
                                                    Dokter Bedah
                                                </td>
                                                <td class="align-middle py-0 border border-dark border-left-0 border-right-0 border-top-0"
                                                    colspan="5">
                                                    :
                                                    {!! $listOperasi->operator1 != '-' ? \App\Vedika::getPegawai($listOperasi->operator1)->nama : '-' !!}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle py-0">
                                                    Keluhan:
                                                </td>
                                                <td class="align-middle py-0" colspan="2">

                                                </td>
                                                <td
                                                    class="align-middle py-0 border border-dark border-bottom-0 border-right-0 border-top-0">
                                                    Penilaian:
                                                </td>
                                                <td class="align-middle py-0" colspan="2">

                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle py-0 pl-5" colspan="3">
                                                    <u>{{ $dataOperasi1->keluhan }}</u>
                                                </td>
                                                <td class="align-middle py-0 pl-5 border border-dark border-bottom-0 border-right-0 border-top-0"
                                                    colspan="3">
                                                    <u>{{ $dataOperasi1->penilaian }}</u>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle py-0">
                                                    Pemeriksaan:
                                                </td>
                                                <td class="align-middle py-0" colspan="2">

                                                </td>
                                                <td
                                                    class="align-middle py-0 border border-dark border-bottom-0 border-right-0 border-top-0">
                                                    Tindak Lanjut:
                                                </td>
                                                <td class="align-middle py-0" colspan="2">

                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle py-0 pl-5" colspan="3">
                                                    <u>{{ $dataOperasi1->pemeriksaan }}</u>
                                                </td>
                                                <td class="align-middle py-0 pl-5 border border-dark border-bottom-0 border-right-0 border-top-0"
                                                    colspan="3">
                                                    <u>{{ $dataOperasi1->rtl }}</u>
                                                </td>

                                            </tr>
                                            <tr>
                                                <td class="align-middle py-0 pl-5" colspan="2">
                                                    Suhu Tubuh.(C)
                                                </td>
                                                <td class="align-middle py-0">
                                                    : <u>{{ $dataOperasi1->suhu_tubuh }}</u>
                                                </td>
                                                <td
                                                    class="align-middle py-0 pl-5 border border-dark border-bottom-0 border-right-0 border-top-0">
                                                    Nadi (/Mnt)
                                                </td>
                                                <td class="align-middle py-0" colspan="2">
                                                    : <u>{{ $dataOperasi1->nadi }}</u>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle py-0 pl-5" colspan="2">
                                                    Tensi.
                                                </td>
                                                <td class="align-middle py-0">
                                                    : <u>{{ $dataOperasi1->tensi }}</u>
                                                </td>
                                                <td
                                                    class="align-middle py-0 pl-5 border border-dark border-bottom-0 border-right-0 border-top-0">
                                                    Respirasi (/Mnt).
                                                </td>
                                                <td class="align-middle py-0" colspan="2">
                                                    : <u>{{ $dataOperasi1->respirasi }}</u>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle py-0 pl-5" colspan="2">
                                                    Tinggi (Cm).
                                                </td>
                                                <td class="align-middle py-0">
                                                    : <u>{{ $dataOperasi1->tinggi }}</u>
                                                </td>
                                                <td
                                                    class="align-middle py-0 pl-5 border border-dark border-bottom-0 border-right-0 border-top-0">
                                                    GCS (E,V,M).
                                                </td>
                                                <td class="align-middle py-0" colspan="2">
                                                    : <u>{{ $dataOperasi1->gcs }}</u>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle py-0 pl-5" colspan="2">
                                                    Berat (Kg).
                                                </td>
                                                <td
                                                    class="align-middle py-0 border border-dark border-bottom-0 border-left-0 border-top-0">
                                                    : <u>{{ $dataOperasi1->berat }}</u>
                                                </td>

                                            </tr>
                                            <tr class="table-secondary">
                                                <td class="text-center align-middle py-0 border border-dark"
                                                    colspan="6">
                                                    <h5>POST SURGICAL REPORT</h5>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle py-0" colspan="2">
                                                    Tanggal & Waktu
                                                </td>
                                                <td class="align-middle py-0" colspan="3">
                                                    :
                                                    {{ \Carbon\Carbon::parse($listOperasi->tgl_operasi)->format('d/m/Y
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            H:i:s') }}
                                                </td>
                                                <td
                                                    class="align-middle py-0 border border-dark border-bottom-0 border-right-0 border-top-0">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle py-0 " colspan="2">
                                                    Dokter Bedah
                                                </td>
                                                <td class="align-middle py-0">
                                                    :
                                                </td>
                                                <td class="align-middle py-0 ">
                                                    Asisten Bedah
                                                </td>
                                                <td class="align-middle py-0">
                                                    :
                                                </td>
                                                <td
                                                    class="align-middle py-0 text-center border border-dark border-bottom-0 border-right-0 border-top-0">
                                                    Tipe/Jenis Anastesi
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle py-0 pl-5" colspan="3">
                                                    {!! $listOperasi->operator1 != '-' ? \App\Vedika::getPegawai($listOperasi->operator1)->nama : '-' !!}
                                                </td>
                                                <td class="align-middle py-0 pl-5" colspan="2">
                                                    {!! $listOperasi->asisten_operator1 != '-'
                                                        ? \App\Vedika::getPegawai($listOperasi->asisten_operator1)->nama
                                                        : '-' !!}
                                                </td>
                                                <td
                                                    class="align-middle py-0 border border-dark border-bottom-0 border-right-0 border-top-0">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle py-0 " colspan="2">
                                                    Dokter Bedah 2
                                                </td>
                                                <td class="align-middle py-0">
                                                    :
                                                </td>
                                                <td class="align-middle py-0 ">
                                                    Asisten Bedah 2
                                                </td>
                                                <td class="align-middle py-0">
                                                    :
                                                </td>
                                                <td
                                                    class="align-middle py-0 text-center border border-dark border-bottom-0 border-right-0 border-top-0">
                                                    {{ $listOperasi->jenis_anasthesi }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle py-0 pl-5" colspan="3">
                                                    {!! $listOperasi->operator2 != '-' ? \App\Vedika::getPegawai($listOperasi->operator2)->nama : '-' !!}
                                                </td>
                                                <td class="align-middle py-0 pl-5" colspan="2">
                                                    {!! $listOperasi->asisten_operator2 != '-'
                                                        ? \App\Vedika::getPegawai($listOperasi->asisten_operator2)->nama
                                                        : '-' !!}
                                                </td>
                                                <td
                                                    class="align-middle py-0 border border-dark border-bottom-0 border-right-0 border-top-0">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle py-0 " colspan="2">
                                                    Perawat Resusitas
                                                </td>
                                                <td class="align-middle py-0">
                                                    :
                                                </td>
                                                <td class="align-middle py-0 ">
                                                    Dokter Anastesi
                                                </td>
                                                <td class="align-middle py-0">
                                                    :
                                                </td>
                                                <td
                                                    class="align-middle py-0 border border-dark border-bottom-0 border-right-0 border-top-0">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle py-0 pl-5" colspan="3">
                                                    {!! $listOperasi->perawaat_resusitas != '-'
                                                        ? \App\Vedika::getPegawai($listOperasi->perawaat_resusitas)->nama
                                                        : '-' !!}
                                                </td>
                                                <td class="align-middle py-0 pl-5" colspan="2">
                                                    {!! $listOperasi->dokter_anestesi != '-' ? \App\Vedika::getPegawai($listOperasi->dokter_anestesi)->nama : '-' !!}
                                                </td>
                                                <td
                                                    class="align-middle py-0 border border-dark border-bottom-0 border-right-0 border-top-0">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle py-0 " colspan="2">
                                                    Instrumen
                                                </td>
                                                <td class="align-middle py-0">
                                                    :
                                                </td>
                                                <td class="align-middle py-0 ">
                                                    Asisten Anastesi
                                                </td>
                                                <td class="align-middle py-0">
                                                    :
                                                </td>
                                                <td
                                                    class="align-middle py-0  text-center border border-dark border-bottom-0 border-right-0 border-top-0">
                                                    Dikirim ke Pemeriksaaan PA
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle py-0 pl-5" colspan="3">
                                                    {!! $listOperasi->instrumen != '-' ? \App\Vedika::getPegawai($listOperasi->instrumen)->nama : '-' !!}
                                                </td>
                                                <td class="align-middle py-0 pl-5" colspan="2">
                                                    {!! $listOperasi->asisten_anestesi != '-'
                                                        ? \App\Vedika::getPegawai($listOperasi->asisten_anestesi)->nama
                                                        : '-' !!}
                                                </td>
                                                <td
                                                    class="align-middle py-0 text-center border border-dark border-bottom-0 border-right-0 border-top-0">
                                                    {{ $listOperasi->permintaan_pa }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle py-0 " colspan="2">
                                                    Dokter Anak
                                                </td>
                                                <td class="align-middle py-0">
                                                    :
                                                </td>
                                                <td class="align-middle py-0 ">
                                                    Bidan
                                                </td>
                                                <td class="align-middle py-0">
                                                    :
                                                </td>
                                                <td
                                                    class="align-middle py-0 border border-dark border-bottom-0 border-right-0 border-top-0">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle py-0 pl-5" colspan="3">
                                                    {!! $listOperasi->dokter_anak != '-' ? \App\Vedika::getPegawai($listOperasi->dokter_anak)->nama : '-' !!}
                                                </td>
                                                <td class="align-middle py-0 pl-5" colspan="2">
                                                    {!! $listOperasi->bidan != '-' ? \App\Vedika::getPegawai($listOperasi->bidan)->nama : '-' !!}
                                                </td>
                                                <td
                                                    class="align-middle py-0 border border-dark border-bottom-0 border-right-0 border-top-0">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle py-0 " colspan="2">
                                                    Dokter Umum
                                                </td>
                                                <td class="align-middle py-0">
                                                    :
                                                </td>
                                                <td class="align-middle py-0 ">
                                                    Onloop
                                                </td>
                                                <td class="align-middle py-0">
                                                    :
                                                </td>
                                                <td
                                                    class="align-middle py-0 text-center border border-dark border-bottom-0 border-right-0 border-top-0">
                                                    Tipe/Kategori Operasi
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle py-0 pl-5" colspan="3">
                                                    {!! $listOperasi->dokter_umum != '-' ? \App\Vedika::getPegawai($listOperasi->dokter_umum)->nama : '-' !!}
                                                </td>
                                                <td class="align-middle py-0 pl-5" colspan="2">
                                                    {!! $listOperasi->omloop != '-' ? \App\Vedika::getPegawai($listOperasi->omloop)->nama : '-' !!}
                                                </td>
                                                <td
                                                    class="align-middle py-0 text-center border border-dark border-bottom-0 border-right-0 border-top-0">
                                                    {{ $listOperasi->kategori }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle py-0 table-secondary" colspan="5">
                                                    Diagnosa Pre-Op / Pre Operation Diagnosis
                                                </td>
                                                <td
                                                    class="align-middle py-0 border border-dark border-bottom-0 border-right-0 border-top-0">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle py-0" colspan="5">
                                                    {{ $listOperasi->diagnosa_preop }}
                                                </td>
                                                <td
                                                    class="align-middle py-0 border border-dark border-bottom-0 border-right-0 border-top-0">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle py-0 table-secondary" colspan="5">
                                                    Jaringan Yang di-Eksisi/-Insisi
                                                </td>
                                                <td
                                                    class="align-middle py-0 text-center border border-dark border-bottom-0 border-right-0 border-top-0">
                                                    Selesai Operasi
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle py-0" colspan="5">
                                                    {{ $listOperasi->jaringan_dieksekusi }}
                                                </td>
                                                <td
                                                    class="align-middle py-0 text-center border border-dark border-bottom-0 border-right-0 border-top-0">
                                                    {{ \Carbon\Carbon::parse($listOperasi->selesaioperasi)->format('d/m/Y
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            H:i:s') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle py-0 table-secondary" colspan="5">
                                                    Diagnosa Post-Op / Post Operation Diagnosis
                                                </td>
                                                <td
                                                    class="align-middle py-0 border border-dark border-bottom-0 border-right-0 border-top-0">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle py-0" colspan="5">
                                                    {{ $listOperasi->diagnosa_postop }}
                                                </td>
                                                <td
                                                    class="align-middle py-0 border border-dark border-bottom-0 border-right-0 border-top-0">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center align-middle py-0 table-secondary border border-dark"
                                                    colspan="6">
                                                    <h5>REPORT ( PROCEDURES, SPECIFIC FINDINGS AND COMPLICATIONS )
                                                    </h5>
                                                </td>
                                            </tr>
                                            @php
                                                $dokterOperator = \App\Vedika::getPegawai($listOperasi->operator1)
                                                    ->nama;
                                                $draf = preg_split('/\r\n|\r|\n/', $listOperasi->laporan_operasi);
                                                $qr_dokter =
                                                    'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara
                                        elektronik oleh' .
                                                    "\n" .
                                                    $dokterOperator .
                                                    "\n" .
                                                    'ID ' .
                                                    $listOperasi->operator1 .
                                                    "\n" .
                                                    \Carbon\Carbon::parse($listOperasi->selesaioperasi)->format(
                                                        'd-m-Y'
                                                    );
                                            @endphp
                                            <tr>
                                                <td class="align-middle py-0" colspan="5">
                                                    @foreach ($draf as $laporan)
                                                        {{ $laporan }}<br>
                                                    @endforeach
                                                </td>
                                                <td class="text-center align-bottom py-0 ">
                                                    {{ \Carbon\Carbon::now()->format('d/m/Y') }}<br>
                                                    Dokter Bedah<br>
                                                    {!! QrCode::size(100)->generate($qr_dokter) !!}<br>
                                                    <u>{{ $dokterOperator }}</u>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                @php
                                    ++$index;
                                @endphp
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @endif
        {{-- End hasil operasi multi --}}
        {{-- End of laporan Operasi --}}
        {{-- Data Resume Ranap --}}
        @if ($resumeRanap1)
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
                                    {{ \Carbon\Carbon::parse($resumeRanap2->first()->waktu_masuk_ranap)->format('d-m-Y') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="align-middle py-0">Alamat</td>
                                <td class="align-middle py-0">: {{ $pasien->alamat }}</td>
                                <td class="align-middle py-0">Tanggal Keluar</td>
                                <td class="align-middle py-0">:
                                    {{ \Carbon\Carbon::parse($resumeRanap2->last()->waktu_keluar_ranap)->format('d-m-Y') }}
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
                                        @endphp
                                        {{ $nama[1] }}
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
                            <a href="/vedika/ranap/{{ Crypt::encrypt($pasien->no_rawat) }}/downloadpdf"
                                class="btn btn-success btn-sm btn-block" target="_blank">
                                <i class="fas fa-sync-alt"></i></i> Gabung PDF</a>
                            @if($dataSep)
                                <a href="/vedika/ranap/{{ !empty($dataSep->no_sep)? Crypt::encrypt($dataSep->no_sep):Crypt::encrypt($dataSep->noSep) }}/viewgabungpdf"
                                    class="btn btn-danger btn-sm btn-block" target="_blank">
                                    <i class="fas fa-file-download"></i> Buka PDF</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
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
                                    <input type="hidden" class="form-control" value="Ranap" name="statusRawat"
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
                                        <input type="hidden" class="form-control" value="{{ $statusVerif->id }}"
                                            name="id_verif" />
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
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default float-left"
                                data-dismiss="modal">Tutup</button>
                            <button type="Submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    {{-- //Verifikasi pengajuan --}}
    @php
        $dataSep = App\Vedika::getSep($pasien->no_rawat, 1);
    @endphp

    @if (empty($statusPengajuan))
        <div class="modal fade" id="modal-pengajuan">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form method="POST" action="/vedika/pengajuan">
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
                                        <input type="text" class="form-control" value="Rawat Inap"
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
                                        <label>Kamar</label>
                                        <input type="text" class="form-control"
                                            value="{{ $pasien->kd_kamar }} {{ $pasien->nm_bangsal }}" name="nm_poli"
                                            readonly />
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
                            <button type="button" class="btn btn-default float-left"
                                data-dismiss="modal">Tutup</button>
                            <button type="Submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @else
        <div class="modal fade" id="modal-pengajuan-edit">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form method="POST" action="/vedika/pengajuan/{{ Crypt::encrypt($statusPengajuan->id) }}/update">
                        @csrf
                        <div class="modal-header">
                            <h4 class="modal-title">Edit Pengajuan Klaim</h4>
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
                                        <label>No SEP</label>
                                        <input type="text" class="form-control" value="{{ $dataSep->no_sep }}"
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
                                        <input type="text" class="form-control" value="Rawat Inap"
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
                                        <label>Kamar</label>
                                        <input type="text" class="form-control"
                                            value="{{ $pasien->kd_kamar }} {{ $pasien->nm_bangsal }}" name="nm_poli"
                                            readonly />
                                    </div>
                                    <div class="form-group">
                                        <label>Periode</label>
                                        <select name="periode" class="form-control" required>
                                            <option value="">Pilih</option>
                                            @foreach ($periodeKlaim as $periode)
                                                <option value="{{ $periode->id }}"
                                                    {{ $statusPengajuan->periode_klaim_id == $periode->id ? 'selected' : '' }}>
                                                    {{ $periode->periode }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default float-left"
                                data-dismiss="modal">Tutup</button>
                            <button type="Submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
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
