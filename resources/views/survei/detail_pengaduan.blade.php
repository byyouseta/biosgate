@extends('layouts.master')

@section('head')
    <!-- Tempusdominus|Datetime Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('template/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    {{-- card detail keluhan --}}
                    <div class="card">
                        <div class="card-header">
                            Data Pengaduan Pasien
                        </div>
                        <div class="card-body">
                            <div class="form-group" id="nama-form">
                                <label for="nama">Nama Anda</label>
                                <input type="text" class="form-control" id="nama" name="nama"
                                    value="{{ $data->nama }}" placeholder="Ketikan Nama Anda" readonly>
                            </div>
                            <div class="form-group" id="no-hp-form">
                                <label for="no_hp">Nomor Handphone / Whatsapp</label>
                                <input type="text" class="form-control" id="no_hp" name="no_hp" minlength="10"
                                    value="{{ $data->no_hp }}" maxlength="15"
                                    placeholder="Ketikan Nomor Handphone / Whatsapp" readonly>
                            </div>
                            <div class="form-group" id="email-form">
                                <label for="email">Email <b>(jika ada)</b></label>
                                <input type="email" class="form-control" id="email" value="{{ $data->email }}"
                                    placeholder="Ketikan Email Anda (jika ada)" name="email" maxlength="128" readonly>
                            </div>
                            <div class="mb-3" id="subjek-keluhan-form" class="form-group">
                                <label for="subject_keluhan">Siapa yang mengalami keluhan? </label>
                                <div class="form-check">
                                    <input class="form-check-input disabled" type="radio" value="1" id="check_pasien"
                                        {{ $data->penerima == '1' ? 'checked' : '' }} name="penerima_keluhan" disabled>
                                    <label class="form-check-label" for="check_pasien">
                                        Pasien
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input " type="radio" value="2" id="check_pendamping"
                                        name="penerima_keluhan" {{ $data->penerima == '2' ? 'checked' : '' }} disabled>
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
                                        name="punya_norm" id="punya_norm" {{ $data->punya_rm == '1' ? 'checked' : '' }}
                                        disabled>
                                    <label class="form-check-label" for="punya_norm">
                                        Ya, punya.
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input radio-norm" type="radio" value="0"
                                        name="punya_norm" id="tidak_punya_norm"
                                        {{ $data->punya_rm == '0' ? 'checked' : '' }} disabled>
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
                                        value="{{ $data->no_rm }}" placeholder="Inputkan Nomor Rekam Medis Pasien"
                                        maxlength="8" name="no_rm" readonly>
                                </div>
                                <div class="form-group" id="name-form">
                                    <label for="nama_pasien">Nama Pasien</label>
                                    <input type="text" class="form-control" id="nama_pasien"
                                        value="{{ $data->nama_pasien }}" placeholder="Inputkan Nama Pasien"
                                        name="nama_pasien" readonly>
                                </div>
                                <div class="form-group" id="dob-form">
                                    <label for="dob_pasien">Tanggal Lahir Pasien</label>
                                    <input type="date" class="form-control" id="dob_pasien" name="lahir_pasien"
                                        value="{{ $data->lahir_pasien }}" maxlength="10" readonly>
                                </div>
                            </div>

                            <div class="form-group" id="detail-timestamp-keluhan">
                                <div class="form-group" id="tgl-kejadian-form">
                                    <label for="tgl_kejadian">Tanggal Kejadian</label>
                                    <input type="text" class="form-control" id="tgl_kejadian" name="tgl_kejadian"
                                        value="{{ \Carbon\Carbon::parse($data->waktu_kejadian)->format('d/m/Y') }}"
                                        maxlength="10" readonly>
                                </div>
                                <div class="form-group" id="jam-kejadian-form">
                                    <label for="jam_kejadian">Jam Kejadian</label>
                                    <input type="time" class="form-control" id="jam_kejadian" name="jam_kejadian"
                                        value="{{ \Carbon\Carbon::parse($data->waktu_kejadian)->format('H:i:s') }}"
                                        autocomplete="off" readonly>
                                </div>
                                <div class="form-group" id="tempat-kejadian-form">
                                    <label for="tempat_kejadian">Tempat Kejadian</label>
                                    <input type="text" class="form-control" id="tempat_kejadian"
                                        name="tempat_kejadian" value="{{ $data->tempat_kejadian }}" autocomplete="off"
                                        maxlength="100" readonly>
                                </div>
                                <div class="form-group" id="status-pembiayaan-form">
                                    <label for="cara_bayar">Status Pembiayaan</label>
                                    <select name="pembiayaan" class="form-control" disabled>
                                        <option disabled selected value="">--Pilih Status Pembiayaan--</option>
                                        <option value="1" {{ $data->pembiayaan == '1' ? 'selected' : '' }}>BPJS
                                        </option>
                                        <option value="3" {{ $data->pembiayaan == '3' ? 'selected' : '' }}>Umum
                                        </option>
                                        <option value="2" {{ $data->pembiayaan == '2' ? 'selected' : '' }}>
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
                                            {{ $data->pendaftaran_online == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="pendaftaran_online">Pendaftaran
                                            Online</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="pendaftaran_rajal" id="pendaftaran_rajal"
                                            {{ $data->pendaftaran_rajal == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="pendaftaran_rajal">Pendaftaran
                                            Rawat Jalan</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="pendaftaran_ranap" id="pendaftaran_ranap"
                                            {{ $data->pendaftaran_ranap == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="pendaftaran_ranap">Pendaftaran
                                            Rawat Inap</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="pendaftaran_igd" id="pendaftaran_igd"
                                            {{ $data->pendaftaran_igd == '1' ? 'checked' : '' }}disabled>
                                        <label class="form-check-label" for="pendaftaran_igd">Pendaftaran
                                            IGD</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" name="admin_bpjs"
                                            id="admin_bpjs" {{ $data->admin_bpjs == '1' ? 'checked' : '' }}disabled>
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
                                            {{ $data->petugas_dr_sp == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="petugas_dokter_spesialis">Dokter
                                            Spesialis</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="petugas_dr_umum" id="petugas_dokter_umum"
                                            {{ $data->petugas_dr_umum == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="petugas_dokter_umum">Dokter
                                            Umum</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="petugas_dr_gigi" id="petugas_dokter_gigi"
                                            {{ $data->petugas_dr_gigi == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="petugas_dokter_gigi">Dokter
                                            Gigi</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="petugas_perawat" id="petugas_perawat"
                                            {{ $data->petugas_perawat == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="petugas_perawat">Perawat</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="petugas_bidan" id="petugas_bidan"
                                            {{ $data->petugas_bidan == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="petugas_bidan">Bidan</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="petugas_psikolog" id="petugas_psikologi"
                                            {{ $data->petugas_psikolog == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="petugas_psikologi">Psikolog</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="petugas_apoteker" id="petugas_apoteker"
                                            {{ $data->petugas_apoteker == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="petugas_apoteker">Apoteker</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="petugas_radiografer" id="petugas_radio"
                                            {{ $data->petugas_radiografer == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="petugas_radio">Radiografer</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="petugas_fisioterapi" id="petugas_fisio"
                                            {{ $data->petugas_fisioterapi == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="petugas_fisio">Fisioterapi</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="petugas_konselor" id="petugas_konselor"
                                            {{ $data->petugas_konselor == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="petugas_konselor">Konselor</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="petugas_ahli_gizi" id="petugas_gizi"
                                            {{ $data->petugas_ahli_gizi == '1' ? 'checked' : '' }} disabled>
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
                                            {{ $data->petugas_administrasi == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="petugas_admin">Administrasi</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="petugas_kebersihan" id="petugas_kebersihan"
                                            {{ $data->petugas_kebersihan == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="petugas_kebersihan">Tenaga
                                            Kebersihan</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="petugas_parkir" id="petugas_parkir"
                                            {{ $data->petugas_parkir == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="petugas_parkir">Petugas
                                            Parkir</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="petugas_lainnya" id="petugas_lainnya"
                                            {{ $data->petugas_lainnya == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="petugas_lainnya">Lainnya,
                                            Sebutkan.</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="petugas_satpam" id="petugas_satpam"
                                            {{ $data->petugas_satpam == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="petugas_satpam">Satpam</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="petugas_kasir" id="petugas_kasir"
                                            {{ $data->petugas_kasir == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="petugas_kasir">Kasir</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="petugas_rohaniawan" id="petugas_rohani"
                                            {{ $data->petugas_rohaniawan == '1' ? 'checked' : '' }} disabled>
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
                                            {{ $data->layanan_poli_reg == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="layanan_poliregu">Poliklinik
                                            Reguler</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="layanan_poli_eks" id="layanan_poliekse"
                                            {{ $data->layanan_poli_eks == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="layanan_poliekse">Poliklinik
                                            Eksekutif</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="layanan_ranap" id="layanan_ranap"
                                            {{ $data->layanan_ranap == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="layanan_ranap">Rawat Inap</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="layanan_igd" id="layanan_igd"
                                            {{ $data->layanan_igd == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="layanan_igd">IGD</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="layanan_icu" id="layanan_icu"
                                            {{ $data->layanan_icu == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="layanan_icu">ICU</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="layanan_farmasi" id="layanan_farmasi"
                                            {{ $data->layanan_farmasi == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="layanan_farmasi">Farmasi</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="layanan_jenazah" id="layanan_jenazah"
                                            {{ $data->layanan_jenazah == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="layanan_jenazah">Kamar
                                            Jenazah</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="layanan_lab" id="layanan_lab"
                                            {{ $data->layanan_lab == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="layanan_lab">Laboratorium</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="layanan_mcu" id="layanan_mcu"
                                            {{ $data->layanan_mcu == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="layanan_mcu">Medical Check
                                            Up</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="layanan_hemodialisa" id="layanan_hemo"
                                            {{ $data->layanan_hemodialisa == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="layanan_hemo">Hemodialisa</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="layanan_fisioterapi" id="layanan_fisio"
                                            {{ $data->layanan_fisioterapi == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="layanan_fisio">Fisioterapi</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="layanan_radioterapi" id="layanan_radioterapi"
                                            {{ $data->layanan_radioterapi == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="layanan_radioterapi">Radioterapi</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="layanan_radiologi" id="layanan_radiologi"
                                            {{ $data->layanan_radiologi == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="layanan_radiologi">Radiologi</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="layanan_lainnya" id="layanan_lainnya"
                                            {{ $data->layanan_lainnya == '1' ? 'checked' : '' }} disabled>
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
                                            {{ $data->fasilitas_parkir == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="fasilitas_parkir">Parkir</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="fasilitas_taman" id="fasilitas_taman"
                                            {{ $data->fasilitas_taman == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="fasilitas_taman">Taman</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="fasilitas_ambulan" id="fasilitas_ambulan"
                                            {{ $data->fasilitas_ambulan == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="fasilitas_ambulan">Ambulan</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="fasilitas_toilet" id="fasilitas_toilet"
                                            {{ $data->fasilitas_toilet == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="fasilitas_toilet">Toilet</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="fasilitas_tunggu" id="fasilitas_tunggu"
                                            {{ $data->fasilitas_tunggu == '1' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="fasilitas_tunggu">Ruang
                                            Tunggu</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="fasilitas_lainnya" id="fasilitas_lainnya"
                                            {{ $data->fasilitas_lainnya == '1' ? 'checked' : '' }} disabled>
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
                                    style="height: 100px" readonly>{{ $data->deskripsi }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="nilai-terganggu">Seberapa besar hal ini mengganggu Anda?</label>
                                <input class="custom-range" onchange=updateValue(this) type="range" id="rate"
                                    min="1" max="100" step="1" name="nilai_gangguan"
                                    value="{{ $data->nilai_gangguan }}" disabled>
                                <label>
                                    (Geser untuk merubah Nilai)</b> Nilai yang anda pilih: </label>
                                <span id="rate_val">{{ $data->nilai_gangguan }}</span>
                            </div>


                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    {{-- card RTL --}}
                    <form action="/survei/datapengaduan/{{ Crypt::encrypt($data->id) }}/status" method="POST">
                        @csrf
                        <div class="card">

                            <div class="card-header">Rencana Tindak Lanjut</div>
                            <div class="card-body">
                                <div class="form-group" id="nama-form">
                                    <label for="no_pelaporan">No Pelaporan</label>
                                    <input type="text" class="form-control" id="no_pelaporan" value=""
                                        placeholder="No Pelaporan dari respon Kemenkes" readonly>
                                </div>
                                <div class="form-group" id="nama-form">
                                    <label for="no_tiket">No Tiket</label>
                                    <input type="text" class="form-control" id="no_tiket" placeholder="No Tiket"
                                        value="{{ $data->no_tiket }}" readonly>
                                </div>
                                <div class="form-group" id="status-pelaporan">
                                    <label for="cara_bayar">Status Pelaporan</label>
                                    <select name="status_pelaporan" class="form-control">
                                        <option disabled selected value="">--Pilih Status Pelaporan--</option>
                                        <option value="1" {{ $data->status_keluhan_id == '1' ? 'selected' : '' }}>
                                            Proses
                                        </option>
                                        <option value="2" {{ $data->status_keluhan_id == '2' ? 'selected' : '' }}>
                                            Selesai</option>
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer">
                                <!-- submit button -->
                                <button type="submit" class="btn btn-primary" id="submit">Submit</button>
                                <a href="/survei/datapengaduan" class="btn btn-secondary">Kembali</a>
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
            var detailPasien = $("#detail-pasien");

            // begin: hide form
            // detailPasien.hide();
            // end: hide form

            // if radio button with id="radio_norm" value="1" is clicked
            // punyaRm.click(function() {
            // var rmValue = $(this).val();

            if (document.getElementById("punya_norm").value == '1') {
                detailPasien.show();
            } else {
                detailPasien.hide();
            }
            // });


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
