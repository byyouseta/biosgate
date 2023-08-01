@extends('survei.layout')

@section('head')
    <!-- Tempusdominus|Datetime Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('template/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    {!! RecaptchaV3::initJs() !!}
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <form method="POST" action="/survei/pengaduan/store">
                        @csrf
                        <div class="card mb-5">
                            <div class="card-header text-center">
                                <h2>Formulir Pengaduan Pasien</h2>
                            </div>
                            <div class="card-body">
                                <div>
                                    <p>
                                        Terimakasih Anda telah meluangkan waktu untuk mengisi Formulir Pengaduan.
                                        Kami mohon maaf atas ketidaknyaman yang anda alami di RSUP Surakarta.
                                        Silahkan mengisi form ini untuk menyampaikan pengaduan Anda.
                                    </p>
                                </div>
                                <div class="form-group" id="nama-form">
                                    <label for="nama">Nama Anda</label>
                                    <input type="text" class="form-control" id="nama" name="nama"
                                        value="{{ old('nama') }}" placeholder="Ketikan Nama Anda">
                                </div>
                                <div class="form-group" id="no-hp-form">
                                    <label for="no_hp">Nomor Handphone / Whatsapp</label>
                                    <input type="text" class="form-control" id="no_hp" name="no_hp" minlength="10"
                                        value="{{ old('no_hp') }}" maxlength="15"
                                        placeholder="Ketikan Nomor Handphone / Whatsapp">
                                </div>
                                <div class="form-group" id="email-form">
                                    <label for="email">Email <b>(jika ada)</b></label>
                                    <input type="email" class="form-control" id="email" value="{{ old('email') }}"
                                        placeholder="Ketikan Email Anda (jika ada)" name="email" maxlength="128">
                                </div>
                                <div class="mb-3" id="subjek-keluhan-form" class="form-group">
                                    <label for="subject_keluhan">Siapa yang mengalami keluhan? </label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="1" id="check_pasien"
                                            {{ old('penerima_keluhan') == '1' ? 'checked' : '' }} name="penerima_keluhan">
                                        <label class="form-check-label" for="check_pasien">
                                            Pasien
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="2" id="check_pendamping"
                                            name="penerima_keluhan" {{ old('penerima_keluhan') == '2' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="check_pendamping">
                                            Pendamping Pasien
                                        </label>
                                    </div>
                                </div>
                                <div id="punya-rm-form" class="form-group">
                                    <label for="subject_keluhan">Apakah Anda memiliki Nomor Rekam Medis di RSUP
                                        Surakarta?</label>
                                    <div class="form-check">
                                        <input class="form-check-input radio-norm" type="radio" value="1"
                                            name="punya_norm" id="punya_norm"
                                            {{ old('punya_norm') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="punya_norm">
                                            Ya, punya.
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input radio-norm" type="radio" value="0"
                                            name="punya_norm" id="tidak_punya_norm"
                                            {{ old('punya_norm') == '0' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="tidak_punya_norm">
                                            Belum punya.
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group" id="detail-pasien">
                                    <h5>Detail Pasien</h5>
                                    <div class="form-group" id="no-rm-form">
                                        <label for="no_rekam_medis">Nomor Rekam Medis</label>
                                        <input type="text" class="form-control" id="no_rekam_medis"
                                            value="{{ old('no_rm') }}" placeholder="Inputkan Nomor Rekam Medis Pasien"
                                            maxlength="8" name="no_rm">
                                    </div>
                                    <div class="form-group" id="name-form">
                                        <label for="nama_pasien">Nama Pasien</label>
                                        <input type="text" class="form-control" id="nama_pasien"
                                            value="{{ old('nama_pasien') }}" placeholder="Inputkan Nama Pasien"
                                            name="nama_pasien">
                                    </div>
                                    <div class="form-group" id="dob-form">
                                        <label for="dob_pasien">Tanggal Lahir Pasien</label>
                                        <input type="date" class="form-control" id="dob_pasien" name="lahir_pasien"
                                            value="{{ old('lahir_pasien') }}" maxlength="10">
                                    </div>
                                </div>

                                <div class="form-group" id="detail-timestamp-keluhan">
                                    <div class="form-group" id="tgl-kejadian-form">
                                        <label for="tgl_kejadian">Tanggal Kejadian</label>
                                        <input type="date" class="form-control" id="tgl_kejadian" name="tgl_kejadian"
                                            value="{{ old('tgl_kejadian') }}" maxlength="10">
                                    </div>
                                    <div class="form-group" id="jam-kejadian-form">
                                        <label for="jam_kejadian">Jam Kejadian</label>
                                        <input type="time" class="form-control" id="jam_kejadian" name="jam_kejadian"
                                            value="{{ old('jam_kejadian') }}" autocomplete="off">
                                    </div>
                                    <div class="form-group" id="tempat-kejadian-form">
                                        <label for="tempat_kejadian">Tempat Kejadian</label>
                                        <input type="text" class="form-control" id="tempat_kejadian"
                                            name="tempat_kejadian" value="{{ old('tempat_kejadian') }}"
                                            autocomplete="off" maxlength="100">
                                    </div>
                                    <div class="form-group" id="status-pembiayaan-form">
                                        <label for="cara_bayar">Status Pembiayaan</label>
                                        <select name="pembiayaan" class="form-control">
                                            <option disabled selected value="">--Pilih Status Pembiayaan--</option>
                                            <option value="1" {{ old('pembiayaan') == '1' ? 'selected' : '' }}>BPJS
                                            </option>
                                            <option value="3" {{ old('pembiayaan') == '3' ? 'selected' : '' }}>Umum
                                            </option>
                                            <option value="2" {{ old('pembiayaan') == '2' ? 'selected' : '' }}>
                                                Asuransi
                                                Non BPJS</option>
                                        </select>
                                    </div>
                                </div>
                                <hr>
                                <!-- checkbox keluhan -->
                                {{-- <div class="form-group" id="keluhan-form"> --}}
                                <h5 class="mb-0">Anda memiliki keluhan terhadap:</h5>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="mt-3">Pendaftaran Administrasi</label>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="pendaftaran_online" id="pendaftaran_online"
                                                {{ old('pendaftaran_online') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="pendaftaran_online">Pendaftaran
                                                Online</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="pendaftaran_rajal" id="pendaftaran_rajal"
                                                {{ old('pendaftaran_rajal') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="pendaftaran_rajal">Pendaftaran
                                                Rawat Jalan</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="pendaftaran_ranap" id="pendaftaran_ranap"
                                                {{ old('pendaftaran_ranap') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="pendaftaran_ranap">Pendaftaran
                                                Rawat Inap</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="pendaftaran_igd" id="pendaftaran_igd"
                                                {{ old('pendaftaran_igd') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="pendaftaran_igd">Pendaftaran
                                                IGD</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="admin_bpjs" id="admin_bpjs"
                                                {{ old('admin_bpjs') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="admin_bpjs">Administrasi
                                                BPJS</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="mt-3">Petugas Medis</label>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="petugas_dr_sp" id="petugas_dokter_spesialis"
                                                {{ old('petugas_dr_sp') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="petugas_dokter_spesialis">Dokter
                                                Spesialis</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="petugas_dr_umum" id="petugas_dokter_umum"
                                                {{ old('petugas_dr_umum') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="petugas_dokter_umum">Dokter
                                                Umum</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="petugas_dr_gigi" id="petugas_dokter_gigi"
                                                {{ old('petugas_dr_gigi') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="petugas_dokter_gigi">Dokter
                                                Gigi</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="petugas_perawat" id="petugas_perawat"
                                                {{ old('petugas_perawat') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="petugas_perawat">Perawat</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="petugas_bidan" id="petugas_bidan"
                                                {{ old('petugas_bidan') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="petugas_bidan">Bidan</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="petugas_psikolog" id="petugas_psikologi"
                                                {{ old('petugas_psikolog') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="petugas_psikologi">Psikolog</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="petugas_apoteker" id="petugas_apoteker"
                                                {{ old('petugas_apoteker') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="petugas_apoteker">Apoteker</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="petugas_radiografer" id="petugas_radio"
                                                {{ old('petugas_radiografer') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="petugas_radio">Radiografer</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="petugas_fisioterapi" id="petugas_fisio"
                                                {{ old('petugas_fisioterapi') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="petugas_fisio">Fisioterapi</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="petugas_konselor" id="petugas_konselor"
                                                {{ old('petugas_konselor') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="petugas_konselor">Konselor</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="petugas_ahli_gizi" id="petugas_gizi"
                                                {{ old('petugas_ahli_gizi') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="petugas_gizi">Ahli
                                                Gizi</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="mt-3">Petugas Non Medis</label>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="petugas_administrasi" id="petugas_admin"
                                                {{ old('petugas_administrasi') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="petugas_admin">Administrasi</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="petugas_kebersihan" id="petugas_kebersihan"
                                                {{ old('petugas_kebersihan') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="petugas_kebersihan">Tenaga
                                                Kebersihan</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="petugas_parkir" id="petugas_parkir"
                                                {{ old('petugas_parkir') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="petugas_parkir">Petugas
                                                Parkir</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="petugas_lainnya" id="petugas_lainnya"
                                                {{ old('petugas_lainnya') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="petugas_lainnya">Lainnya,
                                                Sebutkan.</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="petugas_satpam" id="petugas_satpam"
                                                {{ old('petugas_satpam') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="petugas_satpam">Satpam</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="petugas_kasir" id="petugas_kasir"
                                                {{ old('petugas_kasir') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="petugas_kasir">Kasir</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="petugas_rohaniawan" id="petugas_rohani"
                                                {{ old('petugas_rohaniawan') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="petugas_rohani">Rohaniawan</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="mt-3">Layanan</label>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="layanan_poli_reg" id="layanan_poliregu"
                                                {{ old('layanan_poli_reg') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="layanan_poliregu">Poliklinik
                                                Reguler</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="layanan_poli_eks" id="layanan_poliekse"
                                                {{ old('layanan_poli_eks') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="layanan_poliekse">Poliklinik
                                                Eksekutif</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="layanan_ranap" id="layanan_ranap"
                                                {{ old('layanan_ranap') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="layanan_ranap">Rawat Inap</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="layanan_igd" id="layanan_igd"
                                                {{ old('layanan_igd') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="layanan_igd">IGD</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="layanan_icu" id="layanan_icu"
                                                {{ old('layanan_icu') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="layanan_icu">ICU</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="layanan_farmasi" id="layanan_farmasi"
                                                {{ old('layanan_farmasi') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="layanan_farmasi">Farmasi</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="layanan_jenazah" id="layanan_jenazah"
                                                {{ old('layanan_jenazah') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="layanan_jenazah">Kamar
                                                Jenazah</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="layanan_lab" id="layanan_lab"
                                                {{ old('layanan_lab') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="layanan_lab">Laboratorium</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="layanan_mcu" id="layanan_mcu"
                                                {{ old('layanan_mcu') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="layanan_mcu">Medical Check
                                                Up</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="layanan_hemodialisa" id="layanan_hemo"
                                                {{ old('layanan_hemodialisa') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="layanan_hemo">Hemodialisa</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="layanan_fisioterapi" id="layanan_fisio"
                                                {{ old('layanan_fisioterapi') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="layanan_fisio">Fisioterapi</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="layanan_radioterapi" id="layanan_radioterapi"
                                                {{ old('layanan_radioterapi') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="layanan_radioterapi">Radioterapi</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="layanan_radiologi" id="layanan_radiologi"
                                                {{ old('layanan_radiologi') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="layanan_radiologi">Radiologi</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="layanan_lainnya" id="layanan_lainnya"
                                                {{ old('layanan_lainnya') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="layanan_lainnya">Lainnya,
                                                Sebutkan.</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="mt-3">Fasilitas</label>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="fasilitas_parkir" id="fasilitas_parkir"
                                                {{ old('fasilitas_parkir') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="fasilitas_parkir">Parkir</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="fasilitas_taman" id="fasilitas_taman"
                                                {{ old('fasilitas_taman') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="fasilitas_taman">Taman</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="fasilitas_ambulan" id="fasilitas_ambulan"
                                                {{ old('fasilitas_ambulan') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="fasilitas_ambulan">Ambulan</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="fasilitas_toilet" id="fasilitas_toilet"
                                                {{ old('fasilitas_toilet') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="fasilitas_toilet">Toilet</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="fasilitas_tunggu" id="fasilitas_tunggu"
                                                {{ old('fasilitas_tunggu') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="fasilitas_tunggu">Ruang
                                                Tunggu</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="fasilitas_lainnya" id="fasilitas_lainnya"
                                                {{ old('fasilitas_lainnya') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="fasilitas_lainnya">Lainnya,
                                                Sebutkan</label>
                                        </div>
                                    </div>
                                </div>
                                {{-- </div> --}}
                                <div class="mb-3 mt-3 form-group" id="keterangan-keluhan-form">
                                    <label for="keterangan-keluhan">Deskripsi Pengaduan <b>(tulis dengan lengkap kronologis
                                            yang dialami)</b></label>
                                    <textarea class="form-control" placeholder="Isikan detail pengaduan Anda" name="deskripsi" id="keterangan_keluhan"
                                        style="height: 100px">{{ old('deskripsi') }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label for="nilai-terganggu">Seberapa besar hal ini mengganggu Anda?</label>
                                    <input class="custom-range" onchange=updateValue(this) type="range" id="rate"
                                        min="1" max="100" step="1" name="nilai_gangguan"
                                        value="{{ old('nilai_gangguan') }}">
                                    <label>
                                        (Geser untuk merubah Nilai)</b> Nilai yang anda pilih: </label>
                                    <span id="rate_val">{{ old('nilai_gangguan') }}</span>
                                </div>

                                {!! RecaptchaV3::field('pengaduan') !!}
                                <!-- submit button -->
                                <button type="submit" class="btn btn-primary" id="submit">Submit</button>
                                <input type="reset" class="btn btn-secondary">
                                <!-- /survey form -->
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </section><!-- /.container-fluid -->
@endsection
@section('plugin')
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{ asset('template/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('template/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <script>
        //Date picker
        $('#tanggal').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    </script>
    <script>
        function updateValue(event) {

            document.getElementById("rate_val").innerText = event.value;
        }
        $(document).ready(function() {
            var detailPasien = $("#detail-pasien"),
                punyaRm = $(".radio-norm");

            // begin: hide form
            detailPasien.hide();
            // end: hide form

            // if radio button with id="radio_norm" value="1" is clicked
            punyaRm.click(function() {
                var rmValue = $(this).val();
                if (rmValue == '1') {
                    detailPasien.show();
                } else {
                    detailPasien.hide();
                }
            });


        });

        // phone number input mask only accept number
        $('#no_hp').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        $('#no_rekam_medis').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
@endsection
