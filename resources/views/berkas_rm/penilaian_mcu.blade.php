@extends('layouts.master')

@section('head')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('template/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 mx-auto">
                    @if ($data_update)
                        <form method="POST" action="{{ route('berkasrm.penilaianralanUpdate') }}">
                            @csrf
                            <div class="card ">
                                <div class="card-header">
                                    <div class="card-title">PENILAIAN AWAL KEPERAWATAN UMUM</div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="no_hp">Nomor Rawat</label>
                                                <input type="text" class="form-control" name="noRawat"
                                                    value="{{ $data->no_rawat }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="no_hp">Tgl Lahir</label>
                                                <input type="text" class="form-control" name="tgl_lahir"
                                                    value="{{ $data->tgl_lahir }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="no_hp">Tanggal</label>
                                                <input type="text" class="form-control" name="tanggal"
                                                    value="{{ \Carbon\Carbon::parse($data_update->tanggal)->format('d-m-Y H:i:s') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="no_hp">Nomor RM</label>
                                                <input type="text" class="form-control" name="noRm"
                                                    value="{{ $data->no_rkm_medis }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="no_hp">Jenis Kelamin</label>
                                                <input type="text" class="form-control" name="jenis_kelamin"
                                                    value="{{ $data->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="no_hp">Informasi didapat dari</label>
                                                <select name="informasi" class="form-control">
                                                    <option value="Autoanamnesis"
                                                        {{ $data_update->informasi == 'Autoanamnesis' ? 'selected' : '' }}>
                                                        Autoanamnesis</option>
                                                    <option value="Alloanamnesis"
                                                        {{ $data_update->informasi == 'Alloanamnesis' ? 'selected' : '' }}>
                                                        Alloanamnesis</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="no_hp">Nama Pasien</label>
                                                <input type="text" class="form-control" name="nama_pasien"
                                                    value="{{ $data->nm_pasien }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="no_hp">Petugas</label>
                                                <select name="petugas" class="form-control">
                                                    @foreach ($data_petugas as $listPetugas)
                                                        <option value="{{ $listPetugas->nip }}"
                                                            {{ $listPetugas->nip == $data_update->nip ? 'selected' : '' }}>
                                                            {{ $listPetugas->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <hr>
                                            <h6>I. KEADAAN UMUM</h6>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="no_hp">Tekanan Darah</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="td"
                                                        value="{{ $data_update->td }}">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">mmHg</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="no_hp">Nadi</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="nadi"
                                                        value="{{ $data_update->nadi }}">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">x/menit</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="no_hp">RR</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="rr"
                                                        value="{{ $data_update->rr }}">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">x/menit</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="no_hp">Suhu</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="suhu"
                                                        value="{{ $data_update->suhu }}">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">&#8451;</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="no_hp">GCS(E,V,M)</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="gcs"
                                                        value="{{ $data_update->gcs }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                            <h6>II. STATUS NUTRISI</h6>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="no_hp">Berat Badan</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="bb"
                                                        value="{{ $data_update->bb }}">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">Kg</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="no_hp">Tinggi Badan</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="tb"
                                                        value="{{ $data_update->tb }}">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">cm</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="no_hp">BMI</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="bmi"
                                                        value="{{ $data_update->bmi }}">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">Kg/m&#178;</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="no_hp">SpO2</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="spo2"
                                                        value="{{ $data_update->spo2 }}">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">&#37;</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="no_hp">Lingkar Perut</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="lingkar_perut"
                                                        value="{{ $data_update->lingkar_perut }}">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">cm</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                            <h6>III. RIWAYAT KESEHATAN</h6>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="no_hp">Keluhan Utama</label>
                                                <textarea name="keluhan_utama" id="" rows="3" class="form-control">{{ $data_update->keluhan_utama }}</textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="no_hp">Riwayat Penyakit Dahulu</label>
                                                <textarea name="rpd" id="" rows="3" class="form-control">{{ $data_update->rpd }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="no_hp">Riwayat Penyakit Keluarga</label>
                                                <textarea name="rpk" id="" rows="3" class="form-control">{{ $data_update->rpk }}</textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="no_hp">Riwayat Pengobatan</label>
                                                <textarea name="rpo" id="" rows="3" class="form-control">{{ $data_update->rpo }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="no_hp">Riwayat Alergi</label>
                                                <input type="text" class="form-control" name="alergi"
                                                    value="{{ $data_update->alergi }}">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                            <h6>IV. FUNGSIONAL</h6>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="no_hp">Alat Bantu</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <select name="alat_bantu" id="" class="form-control">
                                                            <option value="Tidak"
                                                                {{ $data_update->alat_bantu == 'Tidak' ? 'selected' : '' }}>
                                                                Tidak</option>
                                                            <option value="Ya"
                                                                {{ $data_update->alat_bantu == 'Ya' ? 'selected' : '' }}>Ya
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <input type="text" class="form-control" name="ket_bantu"
                                                        value="{{ $data_update->ket_bantu }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="no_hp">Prothesa</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <select name="prothesa" id="" class="form-control">
                                                            <option value="Tidak"
                                                                {{ $data_update->prothesa == 'Tidak' ? 'selected' : '' }}>
                                                                Tidak</option>
                                                            <option value="Ya"
                                                                {{ $data_update->prothesa == 'Ya' ? 'selected' : '' }}>Ya
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <input type="text" class="form-control" name="ket_pro"
                                                        value="{{ $data_update->ket_pro }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="no_hp">Cacat Fisik</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $data->nama_cacat }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="no_hp">Aktifitas Kehidupan Sehari-hari(ADL)</label>
                                                <select name="adl" id="" class="form-control">
                                                    <option value="Mandiri"
                                                        {{ $data_update->adl == 'Mandiri' ? 'selected' : '' }}>Mandiri
                                                    </option>
                                                    <option value="Dibantu"
                                                        {{ $data_update->adl == 'Dibantu' ? 'selected' : '' }}>Dibantu
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                            <h6>IV. RIWAYAT PSIKO-SOSIAL, SPIRITUAL DAN BUDAYA</h6>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="no_hp">Status Psikologis</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <select name="status_psiko" id="" class="form-control">
                                                            <option value="Tenang"
                                                                {{ $data_update->status_psiko == 'Tenang' ? 'selected' : '' }}>
                                                                Tenang</option>
                                                            <option value="Takut"
                                                                {{ $data_update->status_psiko == 'Takut' ? 'selected' : '' }}>
                                                                Takut
                                                            </option>
                                                            <option value="Cemas"
                                                                {{ $data_update->status_psiko == 'Cemas' ? 'selected' : '' }}>
                                                                Cemas
                                                            </option>
                                                            <option value="Depresi"
                                                                {{ $data_update->status_psiko == 'Depresi' ? 'selected' : '' }}>
                                                                Depresi
                                                            </option>
                                                            <option value="Lain-lain"
                                                                {{ $data_update->status_psiko == 'Lain-lain' ? 'selected' : '' }}>
                                                                Lain-lain
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <input type="text" class="form-control" name="ket_psico"
                                                        value="{{ $data_update->ket_psiko }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="no_hp">Bahasa yang digunakan sehari-hari</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $data->nama_bahasa }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-12 text-bold">
                                            Status Sosial dan Ekonomi
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="no_hp">a. Hub pasien dengan anggota keluarga</label>
                                                <div class="input-group">
                                                    <select name="hub_keluarga" id="" class="form-control">
                                                        <option value="Baik"
                                                            {{ $data_update->hub_keluarga == 'Baik' ? 'selected' : '' }}>
                                                            Baik
                                                        </option>
                                                        <option value="Tidak Baik"
                                                            {{ $data_update->hub_keluarga == 'Tidak Baik' ? 'selected' : '' }}>
                                                            Tidak Baik</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="no_hp">Kepercayaan/Budaya/Nilai2 khusus yang
                                                    diperhatikan</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <select name="budaya" id="" class="form-control">
                                                            <option value="Tidak Ada"
                                                                {{ $data_update->budaya == 'Tidak Ada' ? 'selected' : '' }}>
                                                                Tidak Ada</option>
                                                            <option value="Ada"
                                                                {{ $data_update->budaya == 'Ada' ? 'selected' : '' }}>
                                                                Ada</option>
                                                        </select>
                                                    </div>
                                                    <input type="text" class="form-control" name="ket_budaya"
                                                        value="{{ $data_update->ket_budaya }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="no_hp">b. Tinggal dengan </label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <select name="tinggal_dengan" id=""
                                                            class="form-control">
                                                            <option value="Sendiri"
                                                                {{ $data_update->tinggal_dengan == 'Sendiri' ? 'selected' : '' }}>
                                                                Sendiri</option>
                                                            <option value="Orang Tua"
                                                                {{ $data_update->tinggal_dengan == 'Orang Tua' ? 'selected' : '' }}>
                                                                Orang Tua</option>
                                                            <option value="Suami / Istri"
                                                                {{ $data_update->tinggal_dengan == 'Suami / Istri' ? 'selected' : '' }}>
                                                                Suami / Istri</option>
                                                            <option value="Lainnya"
                                                                {{ $data_update->tinggal_dengan == 'Lainnya' ? 'selected' : '' }}>
                                                                Lainnya</option>
                                                        </select>
                                                    </div>
                                                    <input type="text" class="form-control" name="ket_tinggal"
                                                        value="{{ $data_update->ket_tinggal }}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="no_hp">Agama</label>
                                                <input type="text" class="form-control" value="{{ $data->agama }}"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="no_hp">Ekonomi</label>
                                                <div class="input-group">
                                                    <select name="ekonomi" id="" class="form-control">
                                                        <option value="Baik"
                                                            {{ $data_update->ekonomi == 'Baik' ? 'selected' : '' }}>Baik
                                                        </option>
                                                        <option value="Cukup"
                                                            {{ $data_update->ekonomi == 'Cukup' ? 'selected' : '' }}>Cukup
                                                        </option>
                                                        <option value="Kurang"
                                                            {{ $data_update->ekonomi == 'Kurang' ? 'selected' : '' }}>
                                                            Kurang</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="no_hp">Edukasi diberikan kepada</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <select name="edukasi" id="" class="form-control">
                                                            <option value="Pasien"
                                                                {{ $data_update->edukasi == 'Pasien' ? 'selected' : '' }}>
                                                                Pasien</option>
                                                            <option value="Keluarga"
                                                                {{ $data_update->edukasi == 'Keluarga' ? 'selected' : '' }}>
                                                                Keluarga</option>
                                                        </select>
                                                    </div>
                                                    <input type="text" class="form-control" name="ket_edukasi"
                                                        value="{{ $data_update->ket_edukasi }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                            <h6>VI. PENILAIAN RESIKO JATUH</h6>
                                            <div class="text-bold">a. Cara Berjalan</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="no_hp">1. Tidak seimbang/sempoyongan/limbung</label>
                                                <div class="input-group">
                                                    <select name="berjalan_a" id="" class="form-control">
                                                        <option
                                                            value="Tidak"{{ $data_update->berjalan_a == 'Tidak' ? 'selected' : '' }}>
                                                            Tidak</option>
                                                        <option
                                                            value="Ya"{{ $data_update->berjalan_a == 'Ya' ? 'selected' : '' }}>
                                                            Ya</option>
                                                        <option value="-"
                                                            {{ $data_update->berjalan_a == '-' ? 'selected' : '' }}>-
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="no_hp">2. Jalan dengan menggunakan alat bantu(kruk, tripot,
                                                    kursi
                                                    roda,
                                                    orang lain)</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <select name="berjalan_b" id="" class="form-control">
                                                            <option
                                                                value="Tidak"{{ $data_update->berjalan_b == 'Tidak' ? 'selected' : '' }}>
                                                                Tidak</option>
                                                            <option
                                                                value="Ya"{{ $data_update->berjalan_b == 'Ya' ? 'selected' : '' }}>
                                                                Ya</option>
                                                            <option value="-"
                                                                {{ $data_update->berjalan_b == '-' ? 'selected' : '' }}>-
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="no_hp">b. Menopang saat akan duduk, tampak memegang
                                                    pinggiran
                                                    kursi
                                                    atau meja/ benda lain sebagai penopang</label>
                                                <div class="input-group">
                                                    <select name="berjalan_c" id="" class="form-control">
                                                        <option
                                                            value="Tidak"{{ $data_update->berjalan_c == 'Tidak' ? 'selected' : '' }}>
                                                            Tidak</option>
                                                        <option
                                                            value="Ya"{{ $data_update->berjalan_c == 'Ya' ? 'selected' : '' }}>
                                                            Ya</option>
                                                        <option value="-"
                                                            {{ $data_update->berjalan_c == '-' ? 'selected' : '' }}>-
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="no_hp">Hasil</label>
                                                <div class="input-group">
                                                    <select name="hasil" id="" class="form-control">
                                                        <option value="-" selected>-</option>
                                                        <option value="Tidak beresiko (tidak ditemukan a dan b)"
                                                            {{ $data_update->hasil == 'Tidak beresiko (tidak ditemukan a dan b)' ? 'selected' : '' }}>
                                                            Tidak
                                                            beresiko
                                                            (tidak ditemukan a dan b)</option>
                                                        <option value="Resiko rendah (ditemukan a/b)"
                                                            {{ $data_update->hasil == 'Resiko rendah (ditemukan a/b)' ? 'selected' : '' }}>
                                                            Resiko rendah
                                                            (ditemukan
                                                            a/b)
                                                        </option>
                                                        <option value="Resiko tinggi (ditemukan a dan b)"
                                                            {{ $data_update->hasil == 'Resiko tinggi (ditemukan a dan b)' ? 'selected' : '' }}>
                                                            Resiko tinggi
                                                            (ditemukan
                                                            a dan b)</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="no_hp">Dilaporkan kepada dokter?</label>
                                                <div class="input-group">
                                                    <select name="lapor" id="" class="form-control">
                                                        <option value="-" selected>-</option>
                                                        <option value="Tidak"
                                                            {{ $data_update->lapor == 'Tidak' ? 'selected' : '' }}>Tidak
                                                        </option>
                                                        <option value="Ya"
                                                            {{ $data_update->lapor == 'Ya' ? 'selected' : '' }}>Ya</option>

                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="no_hp">Jam dilaporkan</label>
                                                <input type="text" class="form-control" name="ket_lapor"
                                                    value="{{ $data_update->ket_lapor }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-stripped table-sm">
                                                <tr>
                                                    <th class="text-center">TINDAKAN/INTERVENSI</th>
                                                </tr>
                                                @foreach ($data_resiko as $noResiko => $listResiko)
                                                    <tr>
                                                        <td>
                                                            <div class="form-check">
                                                                <input type="checkbox" class="form-check-input"
                                                                    name="resikojatuhPlan[{{ $noResiko }}]"
                                                                    value="{{ $listResiko->kode_intervensi }}"
                                                                    id="resikojatuhPlan{{ $noResiko }}"
                                                                    {{ $data_resikoPlan->where('kode_intervensi', $listResiko->kode_intervensi)->first() ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="resikojatuhPlan{{ $noResiko }}">{{ $listResiko->nama_intervensi }}</label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-stripped table-sm">
                                                <tr>
                                                    <th class="text-center">IMPLEMENTASI</th>
                                                </tr>
                                                @foreach ($data_resiko as $noResikoJatuh => $listResikoJatuh)
                                                    <tr>
                                                        <td>
                                                            <div class="form-check">
                                                                <input type="checkbox" class="form-check-input"
                                                                    value="{{ $listResikoJatuh->kode_intervensi }}"
                                                                    name="resikojatuhImplementasi[{{ $noResikoJatuh }}]"
                                                                    id="resikojatuhImplementasi{{ $noResikoJatuh }}"
                                                                    {{ $data_resikoImplementasi->where('kode_implementasi', $listResikoJatuh->kode_intervensi)->first() ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="resikojatuhImplementasi{{ $noResikoJatuh }}">{{ $listResikoJatuh->nama_intervensi }}</label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>

                                        <div class="col-md-12">
                                            <hr>
                                            VII. SKRINING GIZI
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="no_hp">1. Apakah ada penurunan berat badan yang tidak
                                                    diinginkan
                                                    selama
                                                    6 bulan terakhir?</label>
                                                <div class="input-group">
                                                    <select name="sg1" id="" class="form-control">
                                                        <option value="Tidak"
                                                            {{ $data_update->sg1 == 'Tidak' ? 'selected' : '' }}>Tidak
                                                        </option>
                                                        <option value="Tidak Yakin"
                                                            {{ $data_update->sg1 == 'Tidak Yakin' ? 'selected' : '' }}>
                                                            Tidak Yakin</option>
                                                        <option value="Ya, 1-5 Kg"
                                                            {{ $data_update->sg1 == '1-5 Kg' ? 'selected' : '' }}>Ya,
                                                            1-5 Kg</option>
                                                        <option value="Ya, 6-10 Kg"
                                                            {{ $data_update->sg1 == '6-10 Kg' ? 'selected' : '' }}>
                                                            Ya, 6-10 Kg</option>
                                                        <option value="Ya, 11-15 Kg"
                                                            {{ $data_update->sg1 == '11-15 Kg' ? 'selected' : '' }}>
                                                            Ya, 11-15 Kg</option>
                                                        <option value="Ya, >15 Kg"
                                                            {{ $data_update->sg1 == '>15 Kg' ? 'selected' : '' }}>Ya,
                                                            >15 Kg</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="no_hp">2. Apakah nafsu makan berkurang karena tidak nafsu
                                                    makan?</label>
                                                <div class="input-group">
                                                    <select name="sg2" id="" class="form-control">
                                                        <option value="Tidak"
                                                            {{ $data_update->sg2 == 'Tidak' ? 'selected' : '' }}>Tidak
                                                        </option>
                                                        <option value="Ya"
                                                            {{ $data_update->sg2 == 'ya' ? 'selected' : '' }}>Ya</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group  ">
                                                <label for="no_hp">Nilai</label>
                                                <div class="input-group">
                                                    <select name="nilai1" id="nilai1_edit" class="form-control"
                                                        onchange="findTotalEdit()">
                                                        <option value="0"
                                                            {{ $data_update->nilai1 == '0' ? 'selected' : '' }}>0</option>
                                                        <option value="1"
                                                            {{ $data_update->nilai1 == '1' ? 'selected' : '' }}>1</option>
                                                        <option value="2"
                                                            {{ $data_update->nilai1 == '2' ? 'selected' : '' }}>2</option>
                                                        <option value="3"
                                                            {{ $data_update->nilai1 == '3' ? 'selected' : '' }}>3</option>
                                                        <option value="4"
                                                            {{ $data_update->nilai1 == '4' ? 'selected' : '' }}>4</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group ">
                                                <label for="nilai2">Nilai</label>
                                                <div class="input-group">
                                                    <select name="nilai2" id="nilai2_edit" class="form-control"
                                                        onchange="findTotalEdit()">
                                                        <option value="0"
                                                            {{ $data_update->nilai2 == '0' ? 'selected' : '' }}>0</option>
                                                        <option value="1"
                                                            {{ $data_update->nilai2 == '1' ? 'selected' : '' }}>1</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group ">
                                                <label for="total_hasil">Total Skor</label>
                                                <input type="text" class="form-control" name="total_hasil"
                                                    id="total_edit" value="{{ $data_update->total_hasil }}">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                            VII. PENILAIAN TINGKAT NYERI
                                        </div>
                                        <div class="col-md-6">
                                            <img src="{{ asset('image/painratescaleg539684888_1333907.jpg') }}"
                                                alt="" width="500" class="mt-2">
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group ">
                                                <label for="no_hp">&nbsp;</label>
                                                <div class="input-group">
                                                    <select name="nyeri" id="" class="form-control">
                                                        <option value="Tidak Ada Nyeri"
                                                            {{ $data_update->nyeri == 'Tidak Ada Nyeri' ? 'selected' : '' }}>
                                                            Tidak Ada Nyeri</option>
                                                        <option value="Nyeri Akut"
                                                            {{ $data_update->nyeri == 'Nyeri Akut' ? 'selected' : '' }}>
                                                            Nyeri Akut</option>
                                                        <option value="Nyeri Kronis"
                                                            {{ $data_update->nyeri == 'Nyeri Kronis' ? 'selected' : '' }}>
                                                            Nyeri Kronis</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="text-bold mt-4 pt-5">Wilayah</div>

                                            <div class="form-group mt-2">
                                                <label for="no_hp">Lokasi</label>
                                                <input type="text" class="form-control" name="lokasi"
                                                    value="{{ $data_update->lokasi }}">
                                            </div>
                                            <div class="form-group mt-2">
                                                <label for="no_hp">Saverity: Skala Nyeri</label>
                                                <div class="input-group">
                                                    <select name="skala_nyeri" id="" class="form-control">
                                                        <option value="0"
                                                            {{ $data_update->skala_nyeri == '0' ? 'selected' : '' }}>0
                                                        </option>
                                                        <option value="1"
                                                            {{ $data_update->skala_nyeri == '1' ? 'selected' : '' }}>1
                                                        </option>
                                                        <option value="2"
                                                            {{ $data_update->skala_nyeri == '2' ? 'selected' : '' }}>2
                                                        </option>
                                                        <option value="3"
                                                            {{ $data_update->skala_nyeri == '3' ? 'selected' : '' }}>3
                                                        </option>
                                                        <option value="4"
                                                            {{ $data_update->skala_nyeri == '4' ? 'selected' : '' }}>4
                                                        </option>
                                                        <option value="5"
                                                            {{ $data_update->skala_nyeri == '5' ? 'selected' : '' }}>5
                                                        </option>
                                                        <option value="6"
                                                            {{ $data_update->skala_nyeri == '6' ? 'selected' : '' }}>6
                                                        </option>
                                                        <option value="7"
                                                            {{ $data_update->skala_nyeri == '7' ? 'selected' : '' }}>7
                                                        </option>
                                                        <option value="8"
                                                            {{ $data_update->skala_nyeri == '8' ? 'selected' : '' }}>8
                                                        </option>
                                                        <option value="9"
                                                            {{ $data_update->skala_nyeri == '9' ? 'selected' : '' }}>9
                                                        </option>
                                                        <option value="10"
                                                            {{ $data_update->skala_nyeri == '10' ? 'selected' : '' }}>10
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group ">
                                                <label for="no_hp">Penyebab</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <select name="provokes" id="" class="form-control">
                                                            <option value="Proses Penyakit"
                                                                {{ $data_update->provokes == 'Proses Penyakit' ? 'selected' : '' }}>
                                                                Proses Penyakit</option>
                                                            <option value="Benturan"
                                                                {{ $data_update->provokes == 'Benturan' ? 'selected' : '' }}>
                                                                Benturan</option>
                                                            <option value="Lain-lain"
                                                                {{ $data_update->provokes == 'Lain-lain' ? 'selected' : '' }}>
                                                                Lain-lain</option>
                                                        </select>
                                                    </div>
                                                    <input type="text" class="form-control" name="ket_provokes"
                                                        value="{{ $data_update->ket_provokes }}" />
                                                </div>
                                            </div>
                                            <div class="form-group ">
                                                <label for="no_hp">Kualitas</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <select name="quality" id="" class="form-control">
                                                            <option value="Seperti Tertusuk"
                                                                {{ $data_update->quality == 'Seperti Tertusuk' ? 'selected' : '' }}>
                                                                Seperti Tertusuk</option>
                                                            <option value="Berdenyut"
                                                                {{ $data_update->quality == 'Berdenyut' ? 'selected' : '' }}>
                                                                Berdenyut</option>
                                                            <option value="Teriris"
                                                                {{ $data_update->quality == 'Teriris' ? 'selected' : '' }}>
                                                                Teriris</option>
                                                            <option value="Tertindih"
                                                                {{ $data_update->quality == 'Tertindih' ? 'selected' : '' }}>
                                                                Tertindih</option>
                                                            <option value="Tertiban"
                                                                {{ $data_update->quality == 'Tertiban' ? 'selected' : '' }}>
                                                                Tertiban</option>
                                                            <option value="Lain-lain"
                                                                {{ $data_update->quality == 'Lain-lain' ? 'selected' : '' }}>
                                                                Lain-lain</option>
                                                        </select>
                                                    </div>
                                                    <input type="text" class="form-control" name="ket_quality"
                                                        value="{{ $data_update->ket_quality }}" />
                                                </div>
                                            </div>
                                            <div class="form-group ">
                                                <label for="no_hp">Menyebar</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <select name="menyebar" id="" class="form-control">
                                                            <option value="Tidak"
                                                                {{ $data_update->menyebar == 'Tidak' ? 'selected' : '' }}>
                                                                Tidak</option>
                                                            <option value="Ya"
                                                                {{ $data_update->menyebar == 'Ya' ? 'selected' : '' }}>Ya
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group ">
                                                <label for="no_hp">Waktu/Durasi</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="durasi"
                                                        value="{{ $data_update->durasi }}">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">Menit</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group ">
                                                <label for="no_hp">Nyeri Hilang Bila</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <select name="nyeri_hilang" id="" class="form-control">
                                                            <option value="Istirahat"
                                                                {{ $data_update->nyeri_hilang == 'Istirahat' ? 'selected' : '' }}>
                                                                Istirahat</option>
                                                            <option value="Mendengarkan Musik"
                                                                {{ $data_update->nyeri_hilang == 'Mendengarkan Musik' ? 'selected' : '' }}>
                                                                Mendengarkan Musik</option>
                                                            <option value="Minum Obat"
                                                                {{ $data_update->nyeri_hilang == 'Minum Obat' ? 'selected' : '' }}>
                                                                Minum Obat</option>
                                                        </select>
                                                    </div>
                                                    <input type="text" class="form-control" name="ket_nyeri"
                                                        value="{{ $data_update->ket_nyeri }}" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group ">
                                                <label for="no_hp">Diberitahukan pada dokter?</label>
                                                <div class="input-group">
                                                    <select name="pada_dokter" id="" class="form-control">
                                                        <option value="Tidak"
                                                            {{ $data_update->pada_dokter == 'Tidak' ? 'selected' : '' }}>
                                                            Tidak</option>
                                                        <option value="Ya"
                                                            {{ $data_update->pada_dokter == 'Ya' ? 'selected' : '' }}>Ya
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group ">
                                                <label for="no_hp">Jam</label>
                                                <input type="text" class="form-control" name="ket_dokter"
                                                    value="{{ $data_update->ket_dokter }}" />
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-bordered table-hover table-sm" id="example">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">MASALAH KEPERAWATAN</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($data_masalah as $no => $listMasalah)
                                                        <tr>
                                                            <td>
                                                                <div class="form-check">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="masalah[{{ $no }}]"
                                                                        value="{{ $listMasalah->kode_masalah }}"
                                                                        id="masalah{{ $no }}"
                                                                        {{ $data_masalahList->where('kode_masalah', $listMasalah->kode_masalah)->first() ? 'checked' : '' }}>
                                                                    <label class="form-check-label"
                                                                        for="masalah{{ $no }}">{{ $listMasalah->nama_masalah }}</label>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group ml-5">
                                                <label for="no_hp">Keterangan</label>
                                                <select name="waktu_tunggu" id="" class="form-control">
                                                    <option value="-"
                                                        {{ $data_update->waktu_tunggu == '-' ? 'selected' : '' }}>-
                                                    </option>
                                                    <option value="Langsung Bertemu Dokter"
                                                        {{ $data_update->waktu_tunggu == 'Langsung Bertemu Dokter' ? 'selected' : '' }}>
                                                        Langsung Bertemu Dokter
                                                    </option>
                                                    <option value="Pemeriksaan Penunjang"
                                                        {{ $data_update->waktu_tunggu == 'Pemeriksaan Penunjang' ? 'selected' : '' }}>
                                                        Pemeriksaan Penunjang</option>
                                                    <option value="Tindakan Medis"
                                                        {{ $data_update->waktu_tunggu == 'Tindakan Medis' ? 'selected' : '' }}>
                                                        Tindakan Medis</option>
                                                </select>
                                            </div>
                                            <div class="form-group ml-5">
                                                <label for="no_hp">Intervensi Masalah Keperawatan(Rencana
                                                    Keperawatan)</label>
                                                <textarea name="rencana" id="" rows="3" class="form-control">{{ $data_update->rencana }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="float-right">
                                        <button type="submit" class="btn btn-primary">Perbaharui</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @else
                        <form method="POST" action="{{ route('berkasrm.penilaianralanStore') }}">
                            @csrf
                            <div class="card ">
                                <div class="card-header">
                                    <div class="card-title">PENILAIAN MEDICAL CHECK UP (MCU)</div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="no_hp">Nomor Rawat</label>
                                                <input type="text" class="form-control" name="noRawat"
                                                    value="{{ $data->no_rawat }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="no_hp">Tgl Lahir</label>
                                                <input type="text" class="form-control" name="tgl_lahir"
                                                    value="{{ $data->tgl_lahir }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="no_hp">Tanggal</label>
                                                <input type="text" class="form-control" name="nama"
                                                    value="{{ \Carbon\Carbon::now()->format('d-m-Y H:i:s') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="no_hp">Nomor RM</label>
                                                <input type="text" class="form-control" name="noRm"
                                                    value="{{ $data->no_rkm_medis }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="no_hp">Jenis Kelamin</label>
                                                <input type="text" class="form-control" name="jenis_kelamin"
                                                    value="{{ $data->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="no_hp">Informasi didapat dari</label>
                                                <select name="informasi" class="form-control">
                                                    <option value="Autoanamnesis">Autoanamnesis</option>
                                                    <option value="Alloanamnesis">Alloanamnesis</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="no_hp">Nama Pasien</label>
                                                <input type="text" class="form-control" name="nama_pasien"
                                                    value="{{ $data->nm_pasien }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="no_hp">Petugas</label>
                                                <select name="petugas" class="form-control">
                                                    @foreach ($data_petugas as $listPetugas)
                                                        <option value="{{ $listPetugas->nip }}"
                                                            {{ $listPetugas->nip == Auth::user()->username ? 'selected' : '' }}>
                                                            {{ $listPetugas->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <hr>
                                            <h6>I. KEADAAN UMUM</h6>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="no_hp">Tekanan Darah</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="td">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">mmHg</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="no_hp">Nadi</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="nadi">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">x/menit</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="no_hp">RR</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="rr">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">x/menit</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="no_hp">Suhu</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="suhu">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">&#8451;</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="no_hp">GCS(E,V,M)</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="gcs">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                            <h6>II. STATUS NUTRISI</h6>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="no_hp">Berat Badan</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="bb">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">Kg</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="no_hp">Tinggi Badan</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="tb">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">cm</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="no_hp">BMI</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="bmi">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">Kg/m&#178;</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="no_hp">SpO2</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="spo2">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">&#37;</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="no_hp">Lingkar Perut</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="lingkar_perut">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">cm</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                            <h6>III. RIWAYAT KESEHATAN</h6>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="no_hp">Keluhan Utama</label>
                                                <textarea name="keluhan_utama" id="" rows="3" class="form-control"></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="no_hp">Riwayat Penyakit Dahulu</label>
                                                <textarea name="rpd" id="" rows="3" class="form-control"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="no_hp">Riwayat Penyakit Keluarga</label>
                                                <textarea name="rpk" id="" rows="3" class="form-control"></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="no_hp">Riwayat Pengobatan</label>
                                                <textarea name="rpo" id="" rows="3" class="form-control"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="no_hp">Riwayat Alergi</label>
                                                <input type="text" class="form-control" name="alergi">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                            <h6>IV. FUNGSIONAL</h6>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="no_hp">Alat Bantu</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <select name="alat_bantu" id="" class="form-control">
                                                            <option value="Tidak">Tidak</option>
                                                            <option value="Ya">Ya</option>
                                                        </select>
                                                    </div>
                                                    <input type="text" class="form-control" name="ket_bantu"
                                                        value="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="no_hp">Prothesa</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <select name="prothesa" id="" class="form-control">
                                                            <option value="Tidak">Tidak</option>
                                                            <option value="Ya">Ya </option>
                                                        </select>
                                                    </div>
                                                    <input type="text" class="form-control" name="ket_pro">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="no_hp">Cacat Fisik</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $data->nama_cacat }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="no_hp">Aktifitas Kehidupan Sehari-hari(ADL)</label>
                                                <select name="adl" id="" class="form-control">
                                                    <option value="Mandiri">Mandiri</option>
                                                    <option value="Dibantu">Dibantu</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                            <h6>IV. RIWAYAT PSIKO-SOSIAL, SPIRITUAL DAN BUDAYA</h6>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="no_hp">Status Psikologis</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <select name="status_psiko" id="" class="form-control">
                                                            <option value="Tenang">
                                                                Tenang</option>
                                                            <option value="Takut">
                                                                Takut
                                                            </option>
                                                            <option value="Cemas">
                                                                Cemas
                                                            </option>
                                                            <option value="Depresi">
                                                                Depresi
                                                            </option>
                                                            <option value="Lain-lain">
                                                                Lain-lain
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <input type="text" class="form-control" name="ket_psico"
                                                        value="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="no_hp">Bahasa yang digunakan sehari-hari</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $data->nama_bahasa }}">
                                            </div>
                                        </div>
                                        <div class="col-md-12 text-bold">
                                            Status Sosial dan Ekonomi
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="no_hp">a. Hub pasien dengan anggota keluarga</label>
                                                <div class="input-group">
                                                    <select name="hub_keluarga" id="" class="form-control">
                                                        <option value="Baik">Baik</option>
                                                        <option value="Tidak Baik">Tidak Baik</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="no_hp">Kepercayaan/Budaya/Nilai2 khusus yang
                                                    diperhatikan</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <select name="budaya" id="" class="form-control">
                                                            <option value="Tidak Ada">Tidak Ada</option>
                                                            <option value="Ada">Ada</option>
                                                        </select>
                                                    </div>
                                                    <input type="text" class="form-control" name="ket_budaya">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="no_hp">b. Tinggal dengan </label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <select name="tinggal_dengan" id=""
                                                            class="form-control">
                                                            <option value="Sendiri">Sendiri</option>
                                                            <option value="Orang Tua">Orang Tua</option>
                                                            <option value="Suami / Istri">Suami / Istri</option>
                                                            <option value="Lainnya">Lainnya</option>
                                                        </select>
                                                    </div>
                                                    <input type="text" class="form-control" name="ket_tinggal">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="no_hp">Agama</label>
                                                <input type="text" class="form-control" value="{{ $data->agama }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="no_hp">Ekonomi</label>
                                                <div class="input-group">
                                                    <select name="ekonomi" id="" class="form-control">
                                                        <option value="Baik">Baik</option>
                                                        <option value="Cukup">Cukup</option>
                                                        <option value="Kurang">Kurang</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="no_hp">Edukasi diberikan kepada</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <select name="edukasi" id="" class="form-control">
                                                            <option value="Pasien">Pasien</option>
                                                            <option value="Keluarga">Keluarga</option>
                                                        </select>
                                                    </div>
                                                    <input type="text" class="form-control" name="ket_edukasi">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                            <h6>VI. PENILAIAN RESIKO JATUH</h6>
                                            <div class="text-bold">a. Cara Berjalan</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="no_hp">1. Tidak seimbang/sempoyongan/limbung</label>
                                                <div class="input-group">
                                                    <select name="berjalan_a" id="" class="form-control">
                                                        <option value="Tidak">Tidak</option>
                                                        <option value="Ya">Ya</option>
                                                        <option value="-" selected>-</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="no_hp">2. Jalan dengan menggunakan alat bantu(kruk, tripot,
                                                    kursi
                                                    roda,
                                                    orang lain)</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <select name="berjalan_b" id="" class="form-control">
                                                            <option value="Tidak">Tidak</option>
                                                            <option value="Ya">Ya</option>
                                                            <option value="-" selected>-</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="no_hp">b. Menopang saat akan duduk, tampak memegang
                                                    pinggiran
                                                    kursi
                                                    atau meja/ benda lain sebagai penopang</label>
                                                <div class="input-group">
                                                    <select name="berjalan_c" id="" class="form-control">
                                                        <option value="Tidak">Tidak</option>
                                                        <option value="Ya">Ya</option>
                                                        <option value="-" selected>-</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="no_hp">Hasil</label>
                                                <div class="input-group">
                                                    <select name="hasil" id="" class="form-control">
                                                        <option value="Tidak beresiko (tidak ditemukan a dan b)">Tidak
                                                            Beresiko
                                                            (tidak ditemukan a dan b)</option>
                                                        <option value="Resiko rendah (ditemukan a/b)">Resiko rendah
                                                            (ditemukan
                                                            a/b)
                                                        </option>
                                                        <option value="Resiko tinggi (ditemukan a dan b)">Resiko tinggi
                                                            (ditemukan
                                                            a dan b)</option>
                                                        <option value="-" selected>-</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="no_hp">Dilaporkan kepada dokter?</label>
                                                <div class="input-group">
                                                    <select name="lapor" id="" class="form-control">
                                                        <option value="Tidak">Tidak</option>
                                                        <option value="Ya">Ya</option>
                                                        <option value="-" selected>-</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="no_hp">Jam dilaporkan</label>
                                                <input type="text" class="form-control" name="ket_lapor">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-stripped table-sm">
                                                <tr>
                                                    <th class="text-center">TINDAKAN/INTERVENSI</th>
                                                </tr>
                                                @foreach ($data_resiko as $noResiko => $listResiko)
                                                    <tr>
                                                        <td>
                                                            <div class="form-check">
                                                                <input type="checkbox" class="form-check-input"
                                                                    name="resikojatuhPlan[{{ $noResiko }}]"
                                                                    value="{{ $listResiko->kode_intervensi }}"
                                                                    id="resikojatuhPlan{{ $noResiko }}">
                                                                <label class="form-check-label"
                                                                    for="resikojatuhPlan{{ $noResiko }}">{{ $listResiko->nama_intervensi }}</label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-stripped table-sm">
                                                <tr>
                                                    <th class="text-center">IMPLEMENTASI</th>
                                                </tr>
                                                @foreach ($data_resiko as $noResikoJatuh => $listResikoJatuh)
                                                    <tr>
                                                        <td>
                                                            <div class="form-check">
                                                                <input type="checkbox" class="form-check-input"
                                                                    value="{{ $listResikoJatuh->kode_intervensi }}"
                                                                    name="resikojatuhImplementasi[{{ $noResikoJatuh }}]"
                                                                    id="resikojatuhImplementasi{{ $noResikoJatuh }}">
                                                                <label class="form-check-label"
                                                                    for="resikojatuhImplementasi{{ $noResikoJatuh }}">{{ $listResikoJatuh->nama_intervensi }}</label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>

                                        <div class="col-md-12">
                                            <hr>
                                            VII. SKRINING GIZI
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="no_hp">1. Apakah ada penurunan berat badan yang tidak
                                                    diinginkan
                                                    selama
                                                    6 bulan terakhir?</label>
                                                <div class="input-group">
                                                    <select name="sg1" id="" class="form-control">
                                                        <option value="Tidak">Tidak</option>
                                                        <option value="Tidak Yakin">Tidak Yakin</option>
                                                        <option value="Ya, 1-5 Kg">Ya, 1-5 Kg</option>
                                                        <option value="Ya, 6-10 Kg">Ya, 6-10 Kg</option>
                                                        <option value="Ya, 11-15 Kg">Ya, 11-15 Kg</option>
                                                        <option value="Ya, >15 Kg">Ya, >15 Kg</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="no_hp">2. Apakah nafsu makan berkurang karena tidak nafsu
                                                    makan?</label>
                                                <div class="input-group">
                                                    <select name="sg2" id="" class="form-control">
                                                        <option value="Tidak">Tidak</option>
                                                        <option value="Ya">Ya</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group  ">
                                                <label for="no_hp">Nilai</label>
                                                <div class="input-group">
                                                    <select name="nilai1" id="nilai1" class="form-control"
                                                        onchange="findTotal()">
                                                        <option value="0">0</option>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group ">
                                                <label for="nilai2">Nilai</label>
                                                <div class="input-group">
                                                    <select name="nilai2" id="nilai2" class="form-control"
                                                        onchange="findTotal()">
                                                        <option value="0">0</option>
                                                        <option value="1">1</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group ">
                                                <label for="total_hasil">Total Skor</label>
                                                <input type="text" class="form-control" name="total_hasil"
                                                    id="total">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                            VII. PENILAIAN TINGKAT NYERI
                                        </div>
                                        <div class="col-md-6">
                                            <img src="{{ asset('image/painratescaleg539684888_1333907.jpg') }}"
                                                alt="" width="500" class="mt-2">
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group ">
                                                <label for="no_hp">&nbsp;</label>
                                                <div class="input-group">
                                                    <select name="nyeri" id="" class="form-control">
                                                        <option value="Tidak Ada Nyeri">Tidak Ada Nyeri</option>
                                                        <option value="Nyeri Akut">Nyeri Akut</option>
                                                        <option value="Nyeri Kronis">Nyeri Kronis</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="text-bold mt-4 pt-5">Wilayah</div>

                                            <div class="form-group mt-2">
                                                <label for="no_hp">Lokasi</label>
                                                <input type="text" class="form-control" name="lokasi">
                                            </div>
                                            <div class="form-group mt-2">
                                                <label for="no_hp">Saverity: Skala Nyeri</label>
                                                <div class="input-group">
                                                    <select name="skala_nyeri" id="" class="form-control">
                                                        <option value="0">0</option>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                        <option value="6">6</option>
                                                        <option value="7">7</option>
                                                        <option value="8">8</option>
                                                        <option value="9">9</option>
                                                        <option value="10">10</option>
                                                        <option value="Nyeri Akut">Nyeri Akut</option>
                                                        <option value="Nyeri Kronis">Nyeri Kronis</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group ">
                                                <label for="no_hp">Penyebab</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <select name="provokes" id="" class="form-control">
                                                            <option value="Proses Penyakit">Proses Penyakit</option>
                                                            <option value="Benturan">Benturan</option>
                                                            <option value="Lain-lain">Lain-lain</option>
                                                        </select>
                                                    </div>
                                                    <input type="text" class="form-control" name="ket_provokes" />
                                                </div>
                                            </div>
                                            <div class="form-group ">
                                                <label for="no_hp">Kualitas</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <select name="quality" id="" class="form-control">
                                                            <option value="Seperti Tertusuk">Seperti Tertusuk</option>
                                                            <option value="Berdenyut">Berdenyut</option>
                                                            <option value="Teriris">Teriris</option>
                                                            <option value="Tertindih">Tertindih</option>
                                                            <option value="Tertiban">Tertiban</option>
                                                            <option value="Lain-lain">Lain-lain</option>
                                                        </select>
                                                    </div>
                                                    <input type="text" class="form-control" name="ket_quality" />
                                                </div>
                                            </div>
                                            <div class="form-group ">
                                                <label for="no_hp">Menyebar</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <select name="menyebar" id="" class="form-control">
                                                            <option value="Tidak">Tidak</option>
                                                            <option value="Ya">Ya</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group ">
                                                <label for="no_hp">Waktu/Durasi</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="durasi"
                                                        value="">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">Menit</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group ">
                                                <label for="no_hp">Nyeri Hilang Bila</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <select name="nyeri_hilang" id=""
                                                            class="form-control">
                                                            <option value="Istirahat">Istirahat</option>
                                                            <option value="Mendengarkan Musik">Mendengarkan Musik</option>
                                                            <option value="Minum Obat">Minum Obat</option>
                                                        </select>
                                                    </div>
                                                    <input type="text" class="form-control" name="ket_nyeri" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group ">
                                                <label for="no_hp">Diberitahukan pada dokter?</label>
                                                <div class="input-group">
                                                    <select name="pada_dokter" id="" class="form-control">
                                                        <option value="Tidak">Tidak</option>
                                                        <option value="Ya">Ya</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group ">
                                                <label for="no_hp">Jam</label>
                                                <input type="text" class="form-control" name="ket_dokter" />
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-bordered table-hover table-sm" id="example">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">MASALAH KEPERAWATAN</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($data_masalah as $no => $listMasalah)
                                                        <tr>
                                                            <td>
                                                                <div class="form-check">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="masalah[{{ $no }}]"
                                                                        value="{{ $listMasalah->kode_masalah }}"
                                                                        id="masalah{{ $no }}">
                                                                    <label class="form-check-label"
                                                                        for="masalah{{ $no }}">{{ $listMasalah->nama_masalah }}</label>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group ml-5">
                                                <label for="no_hp">Keterangan</label>
                                                <select name="waktu_tunggu" id="" class="form-control">
                                                    <option value="-">-</option>
                                                    <option value="Langsung Bertemu Dokter">Langsung Bertemu Dokter
                                                    </option>
                                                    <option value="Pemeriksaan Penunjang">Pemeriksaan Penunjang</option>
                                                    <option value="Tindakan Medis">Tindakan Medis</option>
                                                </select>
                                            </div>
                                            <div class="form-group ml-5">
                                                <label for="no_hp">Intervensi Masalah Keperawatan(Rencana
                                                    Keperawatan)</label>
                                                <textarea name="rencana" id="" rows="3" class="form-control"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="float-right">
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        <!-- /.row -->
    </section><!-- /.container-fluid -->
@endsection
@section('plugin')
    <script src="{{ asset('template/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script>
        $(function() {
            $('#example').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": true,
                "ordering": false,
                "info": false,
                "autoWidth": false,
                "responsive": false,
                "scrollY": "200px",
                "scrollX": true
            });
        });
    </script>
    @if ($data_update)
        <script>
            function findTotalEdit() {
                var nilai1 = $(this).attr('nilai1_edit');
                var nilai2 = $(this).attr('nilai2_edit');
                var tot = 0;
                tot = parseInt(nilai1.value) + parseInt(nilai2.value);
                // console.log(tot, nilai1, nilai2);

                document.getElementById('total_edit').value = tot;
            };
            document.addEventListener("DOMContentLoaded", function(event) {
                findTotalEdit();
            });
        </script>
    @else
        <script>
            function findTotal() {
                var nilai1 = $(this).attr('nilai1');
                var nilai2 = $(this).attr('nilai2');
                var tot = 0;
                tot = parseInt(nilai1.value) + parseInt(nilai2.value);
                // console.log(tot, nilai1, nilai2);

                document.getElementById('total').value = tot;
            };
            document.addEventListener("DOMContentLoaded", function(event) {
                findTotal();
            });
        </script>
    @endif
@endsection
