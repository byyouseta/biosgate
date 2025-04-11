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
                            Status Pengajuan :
                            {{ !empty($statusPengajuan)
                                ? \Carbon\Carbon::parse($statusPengajuan->periodeKlaim->periode)->format('F Y')
                                : '' }}
                            @can('vedika-pengajuan-delete')
                                @if (!empty($statusPengajuan))
                                    <a href="/vedika/pengajuan/{{ Crypt::encrypt($statusPengajuan->id) }}/delete"
                                        class="delete-confirm text-danger" data-toggle="tooltip" data-placement="bottom"
                                        title="Delete">
                                        <i class="fas fa-ban"></i>
                                    </a>
                                @endif
                            @endcan

                            <div class="float-right">
                                @can('vedika-pengajuan-create')
                                    <button class="btn btn-secondary btn-sm" data-toggle="modal"
                                        data-target="#modal-pengajuan-pending">
                                        <i class="fas fa-plus-circle"></i> Pengajuan Ulang</a>
                                    </button>
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
                                @endcan
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
                                <a href="/vedika/rajal/{{ Crypt::encrypt($pasien->no_rawat) }}/detailpdf"
                                    class="btn btn-primary btn-sm" target="_blank">
                                    <i class="far fa-file-pdf"></i> PDF</a>
                                </a>
                                <a href="/vedika/rajal/{{ Crypt::encrypt($pasien->no_rawat) }}/cronispdf"
                                    class="btn btn-secondary btn-sm" target="_blank">
                                    <i class="far fa-file-pdf"></i> Cronis PDF</a>
                                </a>
                                <button class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#modal-gabung-file">
                                    <i class="fas fa-file-download"></i> Gabung PDF</a>
                                </button>
                            </div>

                            @if (!empty($statusPengajuanKronis))
                                <br>
                                Status Pengajuan Kronis :
                                {{ !empty($statusPengajuanKronis)
                                    ? \Carbon\Carbon::parse($statusPengajuanKronis->periodeKlaim->periode)->format('F Y')
                                    : '' }}
                                @can('vedika-kronis-delete')
                                    @if (!empty($statusPengajuanKronis))
                                        <a href="/vedika/pengajuankronis/{{ Crypt::encrypt($statusPengajuanKronis->id) }}/delete"
                                            class="delete-confirm text-danger" data-toggle="tooltip" data-placement="bottom"
                                            title="Delete">
                                            <i class="fas fa-ban"></i>
                                        </a>
                                    @endif
                                @endcan
                            @endif
                        </div>
                    </div>
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
                                        $diagnosaKlaim = explode('#', $dataKlaim->diagnosa_inagrouper);
                                        $procedureKlaim = explode('#', $dataKlaim->procedure_inagrouper);
                                    @endphp
                                    <tr>
                                        <td style="width: 15%; padding-top: 10px; padding-left:5px; padding-bottom:0px;">
                                            Diagnosa Utama</td>
                                        <td style="width: 5%;padding: 0;padding-top: 10px;">: {{ $diagnosaKlaim[0] }}
                                        </td>
                                        <td style="width: 80%;padding: 0;padding-top: 10px;" colspan=2>
                                            {{ \App\Penyakit::getName($diagnosaKlaim[0]) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 15%;  padding-left:5px; padding-bottom:0px;">
                                            Diagnosa Sekunder</td>
                                        @for ($i = 1; $i < count($diagnosaKlaim); $i++)
                                            <td style="width: 5%;padding: 0;">: {{ $diagnosaKlaim[$i] }}
                                            </td>
                                            <td style="width: 80%;padding: 0;" colspan=2>
                                                {{ \App\Penyakit::getName($diagnosaKlaim[$i]) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 15%;  padding-left:5px; padding-bottom:0px;">
                                        </td>
                        @endfor
                        <td colspan=2>&nbsp</td>
                        @for ($j = 0; $j < count($procedureKlaim); $j++)
                        <tr>
                            <td style="width: 15%; ; padding-left:5px; padding-bottom:0px;">
                                {{ $j == 0 ? 'Prosedur' : '' }}</td>
                            <td style="width: 5%;padding: 0;">: {{ $procedureKlaim[$j] }}
                            </td>
                            <td style="width: 80%;padding: 0;" colspan=2>
                                {{ \App\Penyakit::getProcedure($procedureKlaim[$j]) }}</td>
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
                                {{ isset($dataKlaim->grouper->response->cbg->tariff)? number_format($dataKlaim->grouper->response->cbg->tariff, 2, ',', '.') : number_format(0, 2, ',', '.') }}
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
                                            <td style="width:50%" class="pt-0 pb-0">: {{ $dataSep->no_sep }}</td>
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
                                            <td class="pt-0 pb-0">: {{ $dataSep->no_kartu }} (MR :
                                                {{ $dataSep->nomr }})
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
                                                                dokter/tenaga medis pada RSUP Surakarta untuk
                                                                kepentingan
                                                                pemeliharaan kesehatan, pengobatan, penyembuhan, dan
                                                                perawatan
                                                                Pasien
                                                        </ol>
                                                        *Saya mengetahui dan memahami:
                                                        <ol type="a" style="margin:-5px 50px -5px -25px">
                                                            <li>Rumah Sakit dapat melakukan
                                                                koordinasi dengan PT Jasa Raharja/PT
                                                                Taspen/PT ASABRI/BPJS Ketenagakerjaan atau Penjamin
                                                                lainnya.
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
                                                        dan selanjutnya Pasien dapat mengakses pelayanan kesehatan
                                                        rujukan
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
                                            <td class="pt-0 pb-0" style="width:30%">:
                                                {{ $dataSep->peserta->jnsPeserta }}
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
                                            <td class="pt-0 pb-0">: {{ $kontrol->provPerujuk->nmProviderPerujuk }}
                                            </td>
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
                                                                dokter/tenaga medis pada RSUP Surakarta untuk
                                                                kepentingan
                                                                pemeliharaan kesehatan, pengobatan, penyembuhan, dan
                                                                perawatan
                                                                Pasien
                                                        </ol>
                                                        *Saya mengetahui dan memahami:
                                                        <ol type="a" style="margin:-5px 50px -5px -25px">
                                                            <li>Rumah Sakit dapat melakukan
                                                                koordinasi dengan PT Jasa Raharja/PT
                                                                Taspen/PT ASABRI/BPJS Ketenagakerjaan atau Penjamin
                                                                lainnya.
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
                                                        dan selanjutnya Pasien dapat mengakses pelayanan kesehatan
                                                        rujukan
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
                                            <td class="pt-0 pb-0 text-center align-middle border border-dark"
                                                colspan="6">
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
                                            <td class="py-0 text-center {{ $bg_color }} border border-dark"
                                                colspan="7">
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
                                    <td class="pl-5 pt-0 pb-0 border border-dark border-bottom-0 border-top-0"
                                        colspan="3">
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
                                    <td class="pt-0 pb-0 pl-5 border border-dark border-bottom-0 border-top-0"
                                        colspan="3">
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

            {{-- Data Penilaian Awal IGD --}}
            @if (!empty($dataRingkasan))
                <div class="card">
                    <div class="card-header">Penilaian Awal Medis IGD</div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <thead>
                                <tr>
                                    <td style="width:3%" class="pr-0 align-middle"><img
                                            src="{{ asset('image/logorsup.jpg') }}" alt="Logo RSUP" width="70"
                                            class="px-0 py-0">
                                    </td>
                                    <td class="pt-0 pb-0 text-center align-middle" colspan="6">
                                        <div style="font-size: 16pt">RSUP SURAKARTA</div>
                                        Jl.Prof.Dr.R.Soeharso No.28 , Surakarta, Jawa Tengah <br>
                                        Telp.0271-713055 / 720002 <br>
                                        E-mail : rsupsurakarta@kemkes.go.id
                                    </td>
                                    <td style="width:3%" class="pr-0 align-middle">&nbsp;</td>
                                </tr>
                            </thead>
                        </table>
                        <table class="table table-bordered table-sm mb-0">
                            <thead>
                                <tr>
                                    <th class="pt-0 pb-0 text-center align-middle border border-dark" colspan="6">
                                        <h4>PENILAIAN AWAL MEDIS GAWAT DARURAT</h4>
                                    </th>
                                </tr>
                                <tr>
                                    <th class="pt-0 pb-0 border border-dark border-right-0 border-bottom-0"
                                        style="width: 10%">No. RM
                                    </th>
                                    <th class="pt-0 pb-0 border border-dark border-left-0 border-right-0 border-bottom-0">:
                                        {{ $dataRingkasan->no_rkm_medis }}</th>
                                    <th class="pt-0 pb-0 border border-dark border-left-0 border-right-0 border-bottom-0"
                                        style="width: 10%">Jenis Kelamin
                                    </th>
                                    <th class="pt-0 pb-0 border border-dark border-left-0 border-bottom-0">:
                                        {{ $dataRingkasan->jk == 'L' ? 'Laki-laki':'Perempuan' }}</th>
                                    <th class="pt-0 pb-0 border border-dark border-left-0 border-right-0 border-bottom-0"
                                        style="width: 10%">Tanggal
                                    </th>
                                    <th class="pt-0 pb-0 border border-dark border-left-0 border-bottom-0">:
                                        {{ $dataRingkasan->tanggal }}</th>
                                </tr>
                                <tr>
                                    <th class="pt-0 pb-0 border border-dark border-left-1 border-right-0 border-bottom-1 border-top-0">Nama Pasien</th>
                                    <th class="pt-0 pb-0 border border-dark border-left-0 border-right-0 border-top-0">
                                        : {{ $dataRingkasan->nm_pasien }}</th>
                                    <th class="pt-0 pb-0 border border-dark border-left-0 border-right-0 border-bottom-1 border-top-0 ">Tanggal Lahir</th>
                                    <th class="pt-0 pb-0 border border-dark border-left-0 border-bottom-1 border-top-0">
                                        : {{ $dataRingkasan->tgl_lahir }}</th>
                                    <th class="pt-0 pb-0 border border-dark border-right-0 border-top-0">Anamnesis</th>
                                    <th class="pt-0 pb-0 border border-dark border-left-0 border-top-0">:
                                        {{ $dataRingkasan->anamnesis }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="pt-0 pb-0 border border-dark" colspan="6">
                                        <b>I. RIWAYAT KESEHATAN</b><br>
                                        <p>Keluhan Utama :  {{ $dataRingkasan->keluhan_utama }} </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pt-0 pb-0 border border-dark" colspan="6">
                                        <p>Riwayat Penyakit Sekarang :  {{ $dataRingkasan->rps }} </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pt-0 pb-0 border border-dark" colspan="3">
                                        <p>Riwayat Penyakit Dahulu :  {{ $dataRingkasan->rpd }} </p>
                                    </td>
                                    <td class="pt-0 pb-0 border border-dark" colspan="3">
                                        <p>Riwayat Penyakit dalam Keluarga :  {{ $dataRingkasan->rpk }} </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pt-0 pb-0 border border-dark" colspan="3" style="width: 50%;">
                                        <p>Riwayat Pengobatan :  {{ $dataRingkasan->rpo }} </p>
                                    </td>
                                    <td class="pt-0 pb-0 border border-dark" colspan="3" style="width: 50%;">
                                        <p>Riwayat Alergi :  {{ $dataRingkasan->alergi }} </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pt-0 pb-0 border border-dark" colspan="6">
                                        <b>II. PEMERIKSAAN FISIK </b>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pt-0 pb-0 border border-dark border-right-0" colspan="2">
                                        Keadaan Umum : {{ $dataRingkasan->keadaan }}
                                    </td>
                                    <td class="pt-0 pb-0 border border-dark border-left-0 border-right-0" colspan="2">
                                        Kesadaran : {{ $dataRingkasan->kesadaran }}
                                    </td>
                                    <td class="pt-0 pb-0 border border-dark border-left-0 " colspan="2">
                                        GCS(E,V,M) : {{ $dataRingkasan->gcs }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pt-0 pb-0 border border-dark text-center" colspan="6">
                                        Tanda Vital :&emsp;  TD: {{ $dataRingkasan->td }}&ensp;  N: {{ $dataRingkasan->nadi }}&ensp;  R: {{ $dataRingkasan->rr }}&ensp; S: {{ $dataRingkasan->suhu }}&ensp;  SPO2: {{ $dataRingkasan->spo }}&ensp;  BB: {{ $dataRingkasan->bb }}&ensp;  TB: {{ $dataRingkasan->tb }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pt-0 pb-0 border border-dark border-right-0 border-bottom-0 border-top-0" >
                                        Kepala
                                    </td>
                                    <td class="pt-0 pb-0 border border-dark border-right-0 border-bottom-0 border-top-0" >
                                        {{ $dataRingkasan->kepala }}
                                    </td>
                                    <td class="pt-0 pb-0 border border-dark border-right-0 border-bottom-0 border-top-0" >
                                        Thoraks
                                    </td>
                                    <td class="pt-0 pb-0 border border-dark border-right-1 border-bottom-0 border-top-0" >
                                        {{ $dataRingkasan->thoraks }}
                                    </td>
                                    <td class="pt-0 pb-0 border border-dark border-left-0 border-right-1" colspan="2" rowspan="4">
                                        <pre>{{ $dataRingkasan->ket_fisik }}</pre>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pt-0 pb-0 border border-dark border-right-0 border-bottom-0 border-top-0" >
                                        Mata
                                    </td>
                                    <td class="pt-0 pb-0 border border-dark border-right-0 border-bottom-0 border-top-0" >
                                        {{ $dataRingkasan->mata }}
                                    </td>
                                    <td class="pt-0 pb-0 border border-dark border-right-0 border-bottom-0 border-top-0" >
                                        Abdomen
                                    </td>
                                    <td class="pt-0 pb-0 border border-dark border-right-1 border-bottom-0 border-top-0" >
                                        {{ $dataRingkasan->abdomen }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pt-0 pb-0 border border-dark border-right-0 border-bottom-0 border-top-0" >
                                        Gigi dan Mulut
                                    </td>
                                    <td class="pt-0 pb-0 border border-dark border-right-0 border-bottom-0 border-top-0" >
                                        {{ $dataRingkasan->gigi }}
                                    </td>
                                    <td class="pt-0 pb-0 border border-dark border-right-0 border-bottom-0 border-top-0" >
                                        Genital & Anus
                                    </td>
                                    <td class="pt-0 pb-0 border border-dark border-right-1 border-bottom-0 border-top-0" >
                                        {{ $dataRingkasan->genital }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pt-0 pb-0 border border-dark border-right-0 border-top-0" >
                                        Leher
                                    </td>
                                    <td class="pt-0 pb-0 border border-dark border-right-0 border-top-0" >
                                        {{ $dataRingkasan->leher }}
                                    </td>
                                    <td class="pt-0 pb-0 border border-dark border-right-0 border-top-0" >
                                        Ekstremitas
                                    </td>
                                    <td class="pt-0 pb-0 border border-dark border-right-1 border-top-0" >
                                        {{ $dataRingkasan->ekstremitas }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pt-0 pb-0 border border-dark" colspan="6">
                                        <b>III. STATUS LOKALIS</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pt-0 pb-0 border border-dark" colspan="6">
                                        <p>Keterangan : {{ $dataRingkasan->ket_lokalis }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pt-0 pb-0 border border-dark" colspan="6">
                                        <b>IV. PEMERIKSAAN PENUNJANG</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pt-0 pb-0 border border-dark" colspan="2">
                                        <p>EKG : {{ $dataRingkasan->ekg }}</p>
                                    </td>
                                    <td class="pt-0 pb-0 border border-dark" colspan="2">
                                        <p>Radiologi : {{ $dataRingkasan->rad }}</p>
                                    </td>
                                    <td class="pt-0 pb-0 border border-dark" colspan="2">
                                        <p>Laboratorium : {{ $dataRingkasan->lab }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pt-0 pb-0 border border-dark" colspan="6">
                                        <b>V. DIAGNOSIS</b><br>
                                        <p>{{ $dataRingkasan->diagnosis }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pt-0 pb-0 border border-dark" colspan="3">
                                        <b>VI. TATA LAKSANA</b><br>
                                        <p>{{ $dataRingkasan->tata }}</p>
                                    </td>
                                    <td class="pt-0 pb-0 border border-dark" colspan="3">
                                        <b>VII. RINGKASAN PASIEN GAWAT DARURAT</b><br>
                                        <p>Kondisi Pada Saat Keluar : {{ $resumeIgd && $resumeIgd->kondisi_pulang? $resumeIgd->kondisi_pulang:'-' }}</p>
                                        <p>Tindak Lanjut : {{ $resumeIgd && $resumeIgd->tindak_lanjut? $resumeIgd->tindak_lanjut:'-' }}</p>
                                        <p>Kebutuhan : {{ $resumeIgd && $resumeIgd->kebutuhan? $resumeIgd->kebutuhan:'-' }}</p>
                                        <p>Edukasi : {{ $resumeIgd && $resumeIgd->edukasi ? $resumeIgd->edukasi:'-' }}</p>
                                        <p>Obat Yang Dibawa Pulang : {{ $resumeIgd && $resumeIgd->obat_pulang ? $resumeIgd->obat_pulang:'-' }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="border border-dark border-top-0 text-center" colspan="3">Tanggal dan Jam</td>
                                    <td class="border border-dark border-top-0 text-center" colspan="3">Nama Dokter dan Tanda
                                        Tangan</td>
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
                                            \Carbon\Carbon::parse($dataRingkasan->tanggal)->format('d-m-Y');
                                    @endphp
                                    <td class="border border-dark border-top-0 text-center align-middle" colspan="3">{{ \Carbon\Carbon::parse($dataRingkasan->tanggal)->format('d-m-Y H:i:s') }} WIB</td>
                                    <td class="pt-1 pb-1 pl-5 border border-dark border-top-0 border-right-0">
                                        {!! QrCode::size(100)->generate($qr_dokter) !!} </td>
                                    <td class="border border-dark border-top-0 border-left-0 align-bottom" colspan="2">
                                        {{ $dataRingkasan->nm_dokter }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
            {{-- End Ringksan IGD --}}

            {{-- Pasien Operasi --}}
            @if (!empty($dataOperasi))
                <div class="card">
                    <div class="card-header">Laporan Operasi</div>
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
                                        {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->diff(\Carbon\Carbon::parse($dataOperasi->tgl_operasi))->format('%y Th %m Bl %d Hr') }}
                                    </td>
                                    <td class="align-middle py-0">
                                        Ruang
                                    </td>
                                    <td class="align-middle py-0" colspan="2">
                                        : {{ $pasien->nm_poli }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle py-0">
                                        Tgl Lahir
                                    </td>
                                    <td class="align-middle py-0" colspan="2">
                                        : {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->format('d/m/Y') }}
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
                                    <td class="text-center align-middle py-0 border border-dark" colspan="6">
                                        <h5>PRE SURGICAL ASSESMENT</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle py-0">
                                        Tanggal
                                    </td>
                                    <td class="align-middle py-0">
                                        : {{ \Carbon\Carbon::parse($dataOperasi->tgl_perawatan)->format('d/m/Y') }}
                                    </td>
                                    <td class="align-middle py-0 text-right">
                                        Waktu
                                    </td>
                                    <td class="align-middle py-0">
                                        : {{ $dataOperasi->jam_rawat }}
                                    </td>
                                    <td class="align-middle py-0">
                                        Alergi
                                    </td>
                                    <td class="align-middle py-0">
                                        : {{ $dataOperasi->alergi }}
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
                                        {!! $dataOperasi->operator1 != '-' ? \App\Vedika::getPegawai($dataOperasi->operator1)->nama : '-' !!}
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
                                        <u>{{ $dataOperasi->keluhan }}</u>
                                    </td>
                                    <td class="align-middle py-0 pl-5 border border-dark border-bottom-0 border-right-0 border-top-0"
                                        colspan="3">
                                        <u>{{ $dataOperasi->penilaian }}</u>
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
                                        <u>{{ $dataOperasi->pemeriksaan }}</u>
                                    </td>
                                    <td class="align-middle py-0 pl-5 border border-dark border-bottom-0 border-right-0 border-top-0"
                                        colspan="3">
                                        <u>{{ $dataOperasi->rtl }}</u>
                                    </td>

                                </tr>
                                <tr>
                                    <td class="align-middle py-0 pl-5" colspan="2">
                                        Suhu Tubuh.(C)
                                    </td>
                                    <td class="align-middle py-0">
                                        : <u>{{ $dataOperasi->suhu_tubuh }}</u>
                                    </td>
                                    <td
                                        class="align-middle py-0 pl-5 border border-dark border-bottom-0 border-right-0 border-top-0">
                                        Nadi (/Mnt)
                                    </td>
                                    <td class="align-middle py-0" colspan="2">
                                        : <u>{{ $dataOperasi->nadi }}</u>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle py-0 pl-5" colspan="2">
                                        Tensi.
                                    </td>
                                    <td class="align-middle py-0">
                                        : <u>{{ $dataOperasi->tensi }}</u>
                                    </td>
                                    <td
                                        class="align-middle py-0 pl-5 border border-dark border-bottom-0 border-right-0 border-top-0">
                                        Respirasi (/Mnt).
                                    </td>
                                    <td class="align-middle py-0" colspan="2">
                                        : <u>{{ $dataOperasi->respirasi }}</u>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle py-0 pl-5" colspan="2">
                                        Tinggi (Cm).
                                    </td>
                                    <td class="align-middle py-0">
                                        : <u>{{ $dataOperasi->tinggi }}</u>
                                    </td>
                                    <td
                                        class="align-middle py-0 pl-5 border border-dark border-bottom-0 border-right-0 border-top-0">
                                        GCS (E,V,M).
                                    </td>
                                    <td class="align-middle py-0" colspan="2">
                                        : <u>{{ $dataOperasi->gcs }}</u>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle py-0 pl-5" colspan="2">
                                        Berat (Kg).
                                    </td>
                                    <td
                                        class="align-middle py-0 border border-dark border-bottom-0 border-left-0 border-top-0">
                                        : <u>{{ $dataOperasi->berat }}</u>
                                    </td>

                                </tr>
                                <tr class="table-secondary">
                                    <td class="text-center align-middle py-0 border border-dark" colspan="6">
                                        <h5>POST SURGICAL REPORT</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle py-0" colspan="2">
                                        Tanggal & Waktu
                                    </td>
                                    <td class="align-middle py-0" colspan="3">
                                        :
                                        {{ \Carbon\Carbon::parse($dataOperasi->tgl_operasi)->format('d/m/Y H:i:s') }}
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
                                        {!! $dataOperasi->operator1 != '-' ? \App\Vedika::getPegawai($dataOperasi->operator1)->nama : '-' !!}
                                    </td>
                                    <td class="align-middle py-0 pl-5" colspan="2">
                                        {!! $dataOperasi->asisten_operator1 != '-'
                                            ? \App\Vedika::getPegawai($dataOperasi->asisten_operator1)->nama
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
                                        {{ $dataOperasi->jenis_anasthesi }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle py-0 pl-5" colspan="3">
                                        {!! $dataOperasi->operator2 != '-' ? \App\Vedika::getPegawai($dataOperasi->operator2)->nama : '-' !!}
                                    </td>
                                    <td class="align-middle py-0 pl-5" colspan="2">
                                        {!! $dataOperasi->asisten_operator2 != '-'
                                            ? \App\Vedika::getPegawai($dataOperasi->asisten_operator2)->nama
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
                                        {!! $dataOperasi->perawaat_resusitas != '-'
                                            ? \App\Vedika::getPegawai($dataOperasi->perawaat_resusitas)->nama
                                            : '-' !!}
                                    </td>
                                    <td class="align-middle py-0 pl-5" colspan="2">
                                        {!! $dataOperasi->dokter_anestesi != '-' ? \App\Vedika::getPegawai($dataOperasi->dokter_anestesi)->nama : '-' !!}
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
                                        {!! $dataOperasi->instrumen != '-' ? \App\Vedika::getPegawai($dataOperasi->instrumen)->nama : '-' !!}
                                    </td>
                                    <td class="align-middle py-0 pl-5" colspan="2">
                                        {!! $dataOperasi->asisten_anestesi2 != '-'
                                            ? \App\Vedika::getPegawai($dataOperasi->asisten_anestesi2)->nama
                                            : '-' !!}
                                    </td>
                                    <td
                                        class="align-middle py-0 text-center border border-dark border-bottom-0 border-right-0 border-top-0">
                                        {{ $dataOperasi->permintaan_pa }}
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
                                        {!! $dataOperasi->dokter_anak != '-' ? \App\Vedika::getPegawai($dataOperasi->dokter_anak)->nama : '-' !!}
                                    </td>
                                    <td class="align-middle py-0 pl-5" colspan="2">
                                        {!! $dataOperasi->bidan != '-' ? \App\Vedika::getPegawai($dataOperasi->bidan)->nama : '-' !!}
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
                                        {!! $dataOperasi->dokter_umum != '-' ? \App\Vedika::getPegawai($dataOperasi->dokter_umum)->nama : '-' !!}
                                    </td>
                                    <td class="align-middle py-0 pl-5" colspan="2">
                                        {!! $dataOperasi->omloop != '-' ? \App\Vedika::getPegawai($dataOperasi->omloop)->nama : '-' !!}
                                    </td>
                                    <td
                                        class="align-middle py-0 text-center border border-dark border-bottom-0 border-right-0 border-top-0">
                                        {{ $dataOperasi->kategori }}
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
                                        {{ $dataOperasi->diagnosa_preop }}
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
                                        {{ $dataOperasi->jaringan_dieksekusi }}
                                    </td>
                                    <td
                                        class="align-middle py-0 text-center border border-dark border-bottom-0 border-right-0 border-top-0">
                                        {{ \Carbon\Carbon::parse($dataOperasi->selesaioperasi)->format('d/m/Y H:i:s') }}
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
                                        {{ $dataOperasi->diagnosa_postop }}
                                    </td>
                                    <td
                                        class="align-middle py-0 border border-dark border-bottom-0 border-right-0 border-top-0">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center align-middle py-0 table-secondary border border-dark"
                                        colspan="6">
                                        <h5>REPORT ( PROCEDURES, SPECIFIC FINDINGS AND COMPLICATIONS )</h5>
                                    </td>
                                </tr>
                                @php
                                    $dokterOperator = \App\Vedika::getPegawai($dataOperasi->operator1)->nama;
                                    $draf = preg_split('/\r\n|\r|\n/', $dataOperasi->laporan_operasi);
                                    $qr_dokter =
                                        'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara
                                elektronik oleh' .
                                        "\n" .
                                        $dokterOperator .
                                        "\n" .
                                        'ID ' .
                                        $dataOperasi->operator1 .
                                        "\n" .
                                        \Carbon\Carbon::parse($dataOperasi->selesaioperasi)->format('d-m-Y');
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
                </div>
            @endif
            {{-- Data Operasi Multi Tab --}}
            {{-- Data SOAP --}}
            @if (!empty($soap) && $pasien->nm_poli == 'REHABILITASI MEDIK')
                <div class="card">
                    <div class="card-header">SOAP Pasien</div>
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
                        <table class="table table-borderless mb-0">
                            <thead>
                                <tr>
                                    <td class="align-middle py-0 border border-dark border-top-5 border-left-0 border-right-0 text-center"
                                        colspan="6">
                                        <h3>SOAP</h3>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle py-0" style="width: 15%">
                                        Tanggal
                                    </td>
                                    <td class="align-middle py-0" colspan="2" style="width: 40%">
                                        : {{ $soap->tgl_perawatan }}
                                    </td>
                                    <td class="align-middle py-0" style="width: 15%">
                                        Nama Petugas/Profesi
                                    </td>
                                    <td class="align-middle py-0" colspan="2" style="width: 40%">
                                        : {{ $soap->petugas }} / {{ $soap->jabatan_petugas }}
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
                            </thead>
                            <tbody class="mt-3">
                                <tr>
                                    <th class="border border-dark">Subjek</th>
                                    <td class="border border-dark" colspan="5">
                                        {{ $soap->keluhan }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="border border-dark">Objek</th>
                                    <td class="border border-dark" colspan="5">
                                        {{ $soap->pemeriksaan }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="border border-dark border-bottom-0"></th>
                                    <th class="border border-dark">Suhu</th>
                                    <th class="border border-dark">Tensi</th>
                                    <th class="border border-dark">Nadi(/menit)</th>
                                    <th class="border border-dark">Respirasi(/menit)</th>
                                    <th class="border border-dark border-bottom-0"></th>
                                </tr>
                                <tr>
                                    <td class="text-right border border-dark border-top-0"></td>
                                    <td class="text-right border border-dark">{{ $soap->suhu_tubuh }}</td>
                                    <td class="text-right border border-dark">{{ $soap->tensi }}</td>
                                    <td class="text-right border border-dark">{{ $soap->nadi }}</td>
                                    <td class="text-right border border-dark" style="width: 20%">
                                        {{ $soap->respirasi }}</td>
                                    <td class="text-right border border-dark border-top-0" style="width: 20%">&nbsp;
                                    </td>
                                </tr>
                                <tr>
                                    <th class="border border-dark">Alergi</th>
                                    <td class="border border-dark" colspan="5">
                                        {{ $soap->alergi }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="border border-dark">Asessmen</th>
                                    <td class="border border-dark" colspan="5">
                                        {{ $soap->penilaian }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="border border-dark">Plan</th>
                                    <td class="border border-dark" colspan="5">
                                        {{ $soap->rtl }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="border border-dark">Implementasi</th>
                                    <td class="border border-dark" colspan="5">
                                        {{ $soap->instruksi }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="border border-dark">Evaluasi</th>
                                    <td class="border border-dark" colspan="5">
                                        {{ $soap->evaluasi }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if($dataUsg)
                <div class="card">
                    <div class="card-header">Data USG</div>
                    <div class="card-body">
                        <table class="table table-borderless mb-3">
                            <thead>
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
                            </thead>
                        </table>
                        <div class="progress progress-xs mt-0 pt-0">
                            <div class="progress-bar progress-bar bg-black" role="progressbar" aria-valuenow="100"
                                aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <table style="width: 100%; margin-bottom:50px; margin-top:10px; border: 1px solid black">
                                <thead>
                                    <tr>
                                        <th style="text-align: center; border: 1px solid black;" colspan="4">
                                            <h5><b><u>HASIL PEMERIKSAAN ULTRASONOGRAFI (USG) OBSTETRI</u></b></h5>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td style="width: 20%; padding-left: 25px;">No. Rekam Medis</td>
                                        <td style="width: 30%; ">: {{ $pasien->no_rkm_medis }}</td>
                                        <td style="padding-left: 25px; text-align:right;">Tanggal Lahir</td>
                                        <td style="padding-left: 50px;">: {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding-left: 25px; border-bottom: 1px solid black;">Nama Pasien</td>
                                        <td style="border-bottom: 1px solid black;" colspan="4">: {{ $pasien->nm_pasien }}</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="padding-left: 25px;">Kiriman Dari</td>
                                        <td colspan="3">: {{ $dataUsg->kiriman_dari }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding-left: 25px;">Diagnosa Klinis</td>
                                        <td colspan="3">: {{ $dataUsg->diagnosa_klinis }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding-left: 25px;">HTA</td>
                                        <td colspan="3">: {{ $dataUsg->hta }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding-left: 25px;">Jenis Prestasi</td>
                                        <td colspan="3">: {{ $dataUsg->jenis_prestasi }}</td>
                                    </tr>
                                    @php
                                        $formattedDate = \Carbon\Carbon::parse($dataUsg->tanggal)->format('M, d/m/Y H:i:s') . ' WIB';
                                        $qr_dokter =
                                        'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara
                                        elektronik oleh' .
                                        "\n" .
                                        $dataUsg->nama .
                                        "\n" .
                                        'ID ' .
                                        $dataUsg->kd_dokter .
                                        "\n" .
                                        \Carbon\Carbon::parse($dataUsg->tanggal)->format('d-m-Y');
                                    @endphp
                                    <tr>
                                        <td style="padding-left: 25px; vertical-align:top; ">Kesimpulan</td>
                                        <td colspan="3">: {!! nl2br(e($dataUsg->kesimpulan)) !!}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:center; border: 1px solid black;">Tanggal dan Jam</td>
                                        <td style="text-align:center; border: 1px solid black;" colspan="3">Nama Dokter dan Tanda Tangan</td>
                                    </tr>

                                    <tr>
                                        <td style="text-align: center; border: 1px solid black;">{{ $formattedDate }}</td>
                                        <td style="padding: 10px;">{!! QrCode::size(100)->generate($qr_dokter) !!}</td>
                                        <td colspan="2">{{ $dataUsg->nama }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            @if($dataUsgGynecologi)
                <div class="card">
                    <div class="card-header">Data USG Gynecologi</div>
                    <div class="card-body">
                        <table class="table table-borderless mb-3">
                            <thead>
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
                            </thead>
                        </table>
                        <div class="progress progress-xs mt-0 pt-0">
                            <div class="progress-bar progress-bar bg-black" role="progressbar" aria-valuenow="100"
                                aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <table style="width: 100%; margin-bottom:50px; margin-top:10px; border: 1px solid black">
                                <thead>
                                    <tr>
                                        <th style="text-align: center; border: 1px solid black;" colspan="4">
                                            <h5><b><u>HASIL PEMERIKSAAN ULTRASONOGRAFI (USG) GINEKOLOGI</u></b></h5>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td style="width: 20%; padding-left: 25px;">No. Rekam Medis</td>
                                        <td style="width: 30%; ">: {{ $pasien->no_rkm_medis }}</td>
                                        <td style="padding-left: 25px; text-align:right;">Tanggal Lahir</td>
                                        <td style="padding-left: 50px;">: {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding-left: 25px; border-bottom: 1px solid black;">Nama Pasien</td>
                                        <td style="border-bottom: 1px solid black;" colspan="4">: {{ $pasien->nm_pasien }}</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="padding-left: 25px;">Kiriman Dari</td>
                                        <td colspan="3">: {{ $dataUsgGynecologi->kiriman_dari }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding-left: 25px;">Diagnosa Klinis</td>
                                        <td colspan="3">: {{ $dataUsgGynecologi->diagnosa_klinis }}</td>
                                    </tr>
                                    @php
                                        $formattedDate = \Carbon\Carbon::parse($dataUsgGynecologi->tanggal)->format('M, d/m/Y H:i:s') . ' WIB';
                                        $qr_dokter =
                                        'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara
                                        elektronik oleh' .
                                        "\n" .
                                        $dataUsgGynecologi->nama .
                                        "\n" .
                                        'ID ' .
                                        $dataUsgGynecologi->kd_dokter .
                                        "\n" .
                                        \Carbon\Carbon::parse($dataUsgGynecologi->tanggal)->format('d-m-Y');
                                    @endphp
                                    <tr>
                                        <td style="padding-left: 25px; vertical-align:top; ">Kesimpulan</td>
                                        <td colspan="3">: {!! nl2br(e($dataUsgGynecologi->kesimpulan)) !!}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:center; border: 1px solid black;">Tanggal dan Jam</td>
                                        <td style="text-align:center; border: 1px solid black;" colspan="3">Nama Dokter dan Tanda Tangan</td>
                                    </tr>

                                    <tr>
                                        <td style="text-align: center; border: 1px solid black;">{{ $formattedDate }}</td>
                                        <td style="padding: 10px;">{!! QrCode::size(100)->generate($qr_dokter) !!}</td>
                                        <td colspan="2">{{ $dataUsgGynecologi->nama }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            @if($dataSpiro)
                <div class="card">
                    <div class="card-header">Pemeriksaan Spirometri</div>
                    <div class="card-body">
                        <table class="table table-borderless mb-3">
                            <thead>
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
                            </thead>
                        </table>
                        {{-- <div class="progress progress-xs mt-0 pt-0">
                            <div class="progress-bar progress-bar bg-black" role="progressbar" aria-valuenow="100"
                                aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                            </div>
                        </div> --}}
                        <div class="row justify-content-center">
                            <table style="width: 100%; margin-bottom:50px; margin-top:10px;" class="table table-borderless table-sm">
                                <thead>
                                    <tr>
                                        <th style="text-align: center; border-bottom: 1px solid black; border-top: 3px solid black;" colspan="4">
                                            <h5><b>PEMERIKSAAN SPIROMETRI</b></h5>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td style="" colspan="4">
                                            A. IDENTITAS
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 20%; padding-left: 25px;">No. Rawat</td>
                                        <td style="width: 30%; ">: {{ $pasien->no_rawat }}</td>
                                        <td style="padding-left: 100px; ">Tanggal Periksa</td>
                                        <td style="padding-left: 50px;">: {{ \Carbon\Carbon::parse($dataSpiro->tanggal)->format('d-m-Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 20%; padding-left: 25px;">No. Rekam Medis</td>
                                        <td style="width: 30%; ">: {{ $pasien->no_rkm_medis }}</td>
                                        <td style="padding-left: 100px; ">DPJP</td>
                                        <td style="padding-left: 50px;">: {{ $dataSpiro->nm_dokter }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding-left: 25px; border-bottom: 0px solid black;">Nama Pasien</td>
                                        <td style="border-bottom: 0px solid black;">: {{ $pasien->nm_pasien }}</td>
                                        <td style="padding-left: 100px;;">Dokter Pengirim</td>
                                        <td style="padding-left: 50px;">: {{ $dataSpiro->nm_dokter }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding-left: 25px; border-bottom: 0px solid black;">Umur</td>
                                        <td style="border-bottom: 0px solid black;">:
                                            {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->diff(\Carbon\Carbon::parse($dataSpiro->tanggal))->format('%y Th') }} </td>
                                        <td style="padding-left: 100px; ">Tinggi Badan</td>
                                        <td style="padding-left: 50px;">: {{ $dataSpiro->tb }} Cm</td>
                                    </tr>
                                    <tr>
                                        <td style="padding-left: 25px; border-bottom: 0px solid black;">Alamat</td>
                                        <td style="border-bottom: 0px solid black;">: {{$pasien->alamat}} </td>
                                        <td style="padding-left: 100px; ">Berat Badan</td>
                                        <td style="padding-left: 50px;">: {{ $dataSpiro->bb }} Cm</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="border-top: 1px solid black;" colspan="4">B. RIWAYAT PEKERJAAN / KEBIASAAN</td>
                                    </tr>
                                    <tr>
                                        <td style="padding-left: 25px;">Pekerjaan</td>
                                        <td colspan="4">: {{ $dataSpiro->pekerjaan }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding-left: 25px;">Merokok</td>
                                        <td>: {{ $dataSpiro->merokok }}</td>
                                        <td>Lama : {{ $dataSpiro->lama_merokok }}</td>
                                        <td>Jumlah : 20 Btg / Hari, Eks : {{ $dataSpiro->jumlah_merokok }} </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-left: 25px;">Pengobatan</td>
                                        <td colspan="4">: {{ $dataSpiro->pengobatan }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th rowspan="2" style="vertical-align: middle; text-align:center;">No</th>
                                        <th rowspan="2" style="vertical-align: middle; text-align:center;">Pemeriksaan</th>
                                        <th colspan="7" style="text-align: center">Nilai</th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: center;" colspan="2">Hasil</th>
                                        <th style="text-align: center;">Prediksi</th>
                                        <th style="text-align: center;">Normal</th>
                                        <th style="text-align: center;" colspan="2">Uji Bromkodilator</th>
                                        <th style="text-align: center;">Kenaikan Vep 1</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td rowspan="3" style="text-align:center;">1</td>
                                        <td rowspan="3">Kapasitas Vital</td>
                                        <td style="text-align:center;">1</td>
                                        <td style="text-align: center;">{{ $dataSpiro->pemeriksaan_1a }}</td>
                                        <td rowspan="3" style="width: 5%; text-align:center; vertical-align:middle;">{{ $dataSpiro->prediksi_1a }}</td>
                                        <td rowspan="3" style="background-color:beige"></td>
                                        <td rowspan="3" colspan="2" style="background-color:beige"></td>
                                        <td rowspan="4"></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:center;">2</td>
                                        <td  style="text-align:center;">{{ $dataSpiro->pemeriksaan_1b }}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:center;">3</td>
                                        <td style="text-align:center;">{{ $dataSpiro->pemeriksaan_1c }}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:center;">2</td>
                                        <td>% KV ( KV / KV Prediksi )</td>
                                        <td colspan="2" style="text-align:center;">{{ $dataSpiro->hasil_2a }}%</td>
                                        <td style="background-color:beige"></td>
                                        <td style="text-align:center;">80 %</td>
                                        <td colspan="2" style="background-color:beige"></td>
                                    </tr>
                                    <tr>
                                        <td rowspan="3" style="text-align:center;">3</td>
                                        <td rowspan="3">Kapasital Vital Paksa</td>
                                        <td style="text-align:center;">1</td>
                                        <td style="text-align:center;">{{ $dataSpiro->pemeriksaan_3a }}</td>
                                        <td rowspan="3" style="text-align:center; vertical-align:middle;">{{ $dataSpiro->prediksi_3a }}</td>
                                        <td rowspan="3" style="background-color:beige"></td>
                                        <td rowspan="3" colspan="2" style="background-color:beige"></td>
                                        <td rowspan="4"></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:center;">2</td>
                                        <td style="text-align:center;">{{ $dataSpiro->pemeriksaan_3b }}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:center;">3</td>
                                        <td style="text-align:center;">{{ $dataSpiro->pemeriksaan_3c }}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:center;">4</td>
                                        <td>% KV ( KV / KV Prediksi )</td>
                                        <td colspan="2" style="text-align:center;">{{ $dataSpiro->hasil_4a }}%</td>
                                        <td style="background-color:beige"></td>
                                        <td style="text-align:center;">80 %</td>
                                        <td colspan="2" style="background-color:beige"></td>
                                    </tr>
                                    <tr>
                                        <td rowspan="3" style="text-align:center;">5</td>
                                        <td rowspan="3">Volome Ekspirasi Paksa
                                            Detik 1 ( 1 VEP )</td>
                                        <td style="text-align:center;">1</td>
                                        <td style="text-align:center;">{{ $dataSpiro->pemeriksaan_5a }}</td>
                                        <td rowspan="3" style="text-align:center; vertical-align:middle;">{{ $dataSpiro->prediksi_5a }}</td>
                                        <td rowspan="3" style="background-color:beige"></td>
                                        <td style="width: 3%; text-align:center;">1</td>
                                        <td style="text-align:right;">{{ $dataSpiro->uji_5a }} Ml</td>
                                        <td rowspan="9" style="text-align:end;">%</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:center;">2</td>
                                        <td style="text-align:center;">{{ $dataSpiro->pemeriksaan_5b }}</td>
                                        <td style="text-align:center;">2</td>
                                        <td style="text-align:right;">{{ $dataSpiro->uji_5b }} Ml</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:center;">3</td>
                                        <td style="text-align:center;">{{ $dataSpiro->pemeriksaan_5c }}</td>
                                        <td style="text-align:center;">3</td>
                                        <td style="text-align:right;">{{ $dataSpiro->uji_5c }} Ml</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:center;">6</td>
                                        <td>% VEP 1( VEP 1 Prediksi )</td>
                                        <td colspan="2" style="text-align:center;">{{ $dataSpiro->hasil_6a }}%</td>
                                        <td style="background-color:beige"></td>
                                        <td style="text-align:center;">80 %</td>
                                        <td colspan="2" style="text-align:right;">%</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:center;">7</td>
                                        <td>VEP 1%( VEP 1 / KVP )</td>
                                        <td colspan="2" style="text-align:center;">{{ $dataSpiro->hasil_7a }}%</td>
                                        <td style="text-align: center;">%</td>
                                        <td style="text-align:center;">75 %</td>
                                        <td colspan="2" style="background-color:beige"></td>
                                    </tr>
                                    <tr>
                                        <td rowspan="3" style="text-align:center;">8</td>
                                        <td rowspan="3">Arus Puncak Ekspirasi</td>
                                        <td style="text-align:center;">1</td>
                                        <td style="text-align:center;">{{ $dataSpiro->pemeriksaan_8a }}</td>
                                        <td rowspan="3" style="background-color:beige"></td>
                                        <td rowspan="3" style="background-color:beige"></td>
                                        <td colspan="2" style="text-align:right;">Ml/detik</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:center;">2</td>
                                        <td style="text-align:center;">{{ $dataSpiro->pemeriksaan_8b }}</td>
                                        <td colspan="2" style="text-align:right;">Ml/detik</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:center;">3</td>
                                        <td style="text-align:center;">{{ $dataSpiro->pemeriksaan_8c }}</td>
                                        <td colspan="2" style="text-align:right;">Ml/detik</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:center;">9</td>
                                        <td>Air Trapping</td>
                                        <td colspan="2" style="background-color:beige"></td>
                                        <td style="background-color:beige"></td>
                                        <td  style="background-color:beige"></td>
                                        <td colspan="2" style="background-color:beige"></td>
                                    </tr>
                                </tbody>
                            </table>
                            @php
                                        $qr_dokter =
                                        'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara
                                        elektronik oleh' .
                                        "\n" .
                                        $dataSpiro->nama .
                                        "\n" .
                                        'ID ' .
                                        $dataSpiro->kd_dokter .
                                        "\n" .
                                        \Carbon\Carbon::parse($dataSpiro->tanggal)->format('d-m-Y');
                                    @endphp

                            <table style="width: 100%; margin-bottom:50px; margin-top:10px; border: 0px solid black" class="table table-borderless table-sm">
                                <tbody>
                                    <tr>
                                        <td colspan="2">C. KESIMPULAN / HASIL :</td>
                                        <td>CATATAN :</td>
                                    </tr>
                                    <tr>
                                        <td style="padding-left: 25px; width:5%;">1.</td>
                                        <td style="width:45%">{{ $dataSpiro->kesimpulan_hasil_a }}</td>
                                        <td style="width:50%" rowspan="3">{{ $dataSpiro->catatandokter }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding-left: 25px;">2.</td>
                                        <td>Restriksi : {{ $dataSpiro->kesimpulan_hasil_b }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding-left: 25px;">3.</td>
                                        <td>Obstruksi : {{ $dataSpiro->kesimpulan_hasil_c }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"></td>
                                        <td  style="text-align: center;">Surakarta, {{ \Carbon\Carbon::parse($dataSpiro->tanggal)->format('d-m-Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"></td>
                                        <td style="text-align: center;">Dokter Pemeriksa</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"></td>
                                        <td style="text-align: center;">{!! QrCode::size(100)->generate($qr_dokter) !!}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"></td>
                                        <td style="text-align: center;">{{ $dataSpiro->kd_dokter }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"></td>
                                        <td style="text-align: center;">{{ $dataSpiro->nm_dokter }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

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
                            @can('vedika-verif-create')
                                <a href="/vedika/rajal/{{ Crypt::encrypt($pasien->no_rawat) }}/downloadpdf"
                                class="btn btn-success btn-sm btn-block" target="_blank">
                                <i class="fas fa-sync-alt"></i></i> Gabung PDF</a>
                                @if($dataSep)
                                    <a href="/vedika/ranap/{{ !empty($dataSep->no_sep)? Crypt::encrypt($dataSep->no_sep):Crypt::encrypt($dataSep->noSep) }}/viewgabungpdf"
                                        class="btn btn-danger btn-sm btn-block" target="_blank">
                                        <i class="fas fa-file-download"></i> Buka PDF</a>
                                    <a href="/vedika/ranap/{{ !empty($dataSep->no_sep)? Crypt::encrypt($dataSep->no_sep):Crypt::encrypt($dataSep->noSep) }}/deletepdf"
                                        class="btn btn-secondary btn-sm btn-block">
                                        <i class="fas fa-trash"></i> Hapus File PDF</a>
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
                    <form method="POST" action="/vedika/{{ Crypt::encrypt($pasien->no_rawat) }}/verifikasi">
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
                                                Perbaikan
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
        $dataSep = App\Vedika::getSep($pasien->no_rawat,2);
        // dd($pasien);
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
                            <button type="button" class="btn btn-default float-left"
                                data-dismiss="modal">Tutup</button>
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
                            <button type="button" class="btn btn-default float-left"
                                data-dismiss="modal">Tutup</button>
                            <button type="Submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal pengajuan pending --}}
    <div class="modal fade" id="modal-pengajuan-pending">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="/vedika/pengajuanpending">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">Pengajuan Klaim Pending</h4>
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
                                        @foreach ($periodePending as $periodeUlang)
                                            <option value="{{ $periodeUlang->id }}">{{ $periodeUlang->periode }}</option>
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
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-bordered mb-0 table-sm">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 5%;">No</th>
                                            <th class="text-center" >Periode</th>
                                            <th class="text-center" style="width: 20%;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($dataPengajuanPending as $indek=>$listPengajuan)
                                            <tr>
                                                <td class="text-center">{{ ++$indek }}</td>
                                                <td>{{ \Carbon\Carbon::parse($listPengajuan->periodePengajuanUlang->periode)->format('F Y') }}</td>
                                                <td class="text-center">
                                                    <a href="/vedika/pengajuanpending/{{ Crypt::encrypt($listPengajuan->id) }}/delete"
                                                        class="btn btn-danger btn-sm delete-confirm @cannot('vedika-delete') disabled @endcannot"
                                                        data-toggle="tooltip" data-placement="bottom" title="Delete">
                                                        <i class="fas fa-ban"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center">Data tidak ditemukan</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
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
