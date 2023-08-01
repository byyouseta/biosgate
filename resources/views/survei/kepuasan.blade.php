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
                    <form method="POST" action="/survei/kepuasan/store">
                        @csrf
                        <div class="card ">
                            <div class="card-header text-center">
                                <h2>Formulir Kepuasan Pasien</h2>
                            </div>
                            <div class="card-body">
                                <div>
                                    <p>
                                        Dalam rangka meningkatkan mutu pelayanan kepada masyarakat, RSUP Surakarta ingin
                                        mengetahui persepsi atau penilaian masyarakat yang dilayani
                                        terhadap kinerja pelayanan publik. Hasil dari keseluruhan persepsi atau penilaian
                                        masyarakat yang dilayani tersebut, akan dirangkum dalam Indeks Kepuasan Masyarakat
                                        (IKM). IKM merupakan data dan informasi tentang tingkat kepuasan masyarakat yang
                                        diperoleh dari hasil pengukuran secara kuantitatif dan kualitatif atas pendapat
                                        masyarakat dalam memperoleh pelayanan di RSUP Surakarta.
                                    </p>
                                    <p>
                                        Maksud :<br>
                                        Untuk mengetahui sejauh mana gambaran kinerja pelayanan publik yang diselenggarakan
                                        oleh RSUP Surakarta.</p>
                                    <p>
                                        Kerahasiaan dan Informasi Penting :
                                    <ul>
                                        1. Persepsi dan penilaian Bapak/Ibu pada kuesioner ini dijaga kerahasiannya dan
                                        tidak akan berpengaruh pada perilaku aktivitas pelayanan.
                                    </ul>
                                    <ul>2. Kuesioner ini adalah untuk tujuan evaluasi peningkatan dan penilaian kinerja
                                        pelayanan publik, namun demikian hasil analisis dari survei ini kiranya dapat
                                        menjadi informasi dan feedback yang perlu diperhatikan dalam upaya peningkatan
                                        kualitas pelayanan publik
                                    </ul>
                                    <ul> 3. Setiap jawaban Bapak/Ibu merupakan informasi yang sangat penting dan menentukan
                                        obyektivitas hasil evaluasi peningkatan dan penilaian kinerja layanan RSUP
                                        Surakarta.
                                    </ul>

                                    </p>
                                </div>
                                <hr>

                                <div class="form-group" id="no-hp-form">
                                    <label for="no_hp">Nomor Handphone / Whatsapp</label>
                                    <input type="text" class="form-control" id="no_hp" name="no_hp" minlength="10"
                                        value="{{ old('no_hp') }}" maxlength="15"
                                        placeholder="Ketikan Nomor Handphone / Whatsapp">
                                </div>
                                <div class="form-group" id="umur-form">
                                    <label for="umur">Umur <b>(jika ada)</b></label>
                                    <input type="number" class="form-control" id="umur" value="{{ old('umur') }}"
                                        placeholder="Ketikan Umur Anda dalam tahun" name="umur" max="100"
                                        step="1">
                                </div>
                                <div id="jk" class="form-group">
                                    <label for="subject_keluhan">Jenis Kelamin? </label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="1" id="check_laki"
                                            {{ old('jk') == '1' ? 'checked' : '' }} name="jk">
                                        <label class="form-check-label" for="check_laki">
                                            1. Laki-laki
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="0" id="check_perempuan"
                                            name="jk" {{ old('penerima_keluhan') == '0' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="check_perempuan">
                                            2. Perempuan
                                        </label>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Pendidikan Terakhir</label>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="1" name="pendidikan"
                                                id="sd" {{ old('pendidikan') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="sd">1. SD</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="2" name="pendidikan"
                                                id="sltp" {{ old('pendidikan') == '2' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="sltp">2. SLTP</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="3" name="pendidikan"
                                                id="slta" {{ old('pendidikan') == '3' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="slta">3. SLTA</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="4" name="pendidikan"
                                                id="diploma" {{ old('pendidikan') == '4' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="diploma">4. D1-D2-D3</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="5" name="pendidikan"
                                                id="sarjana" {{ old('pendidikan') == '5' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="sarjana">5. D4-S1
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="6" name="pendidikan"
                                                id="magister" {{ old('pendidikan') == '6' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="magister">6. S2 ke atas
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <label>Pekerjaan Utama</label>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="1"
                                                name="pekerjaan" id="pns"
                                                {{ old('pekerjaan') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="pns">1. PNS/TNI/POLRI</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="2"
                                                name="pekerjaan" id="swasta"
                                                {{ old('pekerjaan') == '2' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="swasta">2. Pegawai Swasta</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="3"
                                                name="pekerjaan" id="usahawan"
                                                {{ old('pekerjaan') == '3' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="usahawan">3. Wiraswasta/ Usahawan</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="4"
                                                name="pekerjaan" id="pelajar"
                                                {{ old('pekerjaan') == '4' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="pelajar">4. Pelajar/Mahasiswa</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="5"
                                                name="pekerjaan" id="lainnya"
                                                {{ old('pekerjaan') == '5' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lainnya">5. Lainnya
                                            </label>
                                        </div>

                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <label>Debitur</label>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="1"
                                                name="penjamin" id="bpjs"
                                                {{ old('penjamin') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="bpjs">1. BPJS</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="2"
                                                name="penjamin" id="asuransi"
                                                {{ old('penjamin') == '2' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="asuransi">2. Asuransi</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="3"
                                                name="penjamin" id="pribadi"
                                                {{ old('penjamin') == '3' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="pribadi">3. Tanggungan Pribadi</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <label>Unit Pelayanan</label>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="1" name="unit"
                                                id="rajal" {{ old('unit') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="rajal">1. Rawat Jalan</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="2" name="unit"
                                                id="ranap" {{ old('unit') == '2' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="ranap">2. Rawat Inap</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="3" name="unit"
                                                id="igd" {{ old('unit') == '3' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="igd">3. IGD</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="4" name="unit"
                                                id="farmasi" {{ old('unit') == '4' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="farmasi">4. Farmasi</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="5" name="unit"
                                                id="lab" {{ old('unit') == '5' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lab">5. Laboratorium</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="6" name="unit"
                                                id="radiologi" {{ old('unit') == '6' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="radiologi">6. Radiologi</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="7" name="unit"
                                                id="icu" {{ old('unit') == '7' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="icu">7. ICU; NICU; PICU</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="8" name="unit"
                                                id="jenazah" {{ old('unit') == '8' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="jenazah">8. Pemulasaran Jenazah</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="9" name="unit"
                                                id="rehap" {{ old('unit') == '9' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="rehap">9. Rehabilitasi medik</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="10" name="unit"
                                                id="radioterapi" {{ old('unit') == '10' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="radioterapi">10. Radioterapi</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="11" name="unit"
                                                id="jantung" {{ old('unit') == '11' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="jantung">11. Jantung</label>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card mb-5">
                            <div class="card-header">
                                <div class="card-title"><b>PENDAPAT RESPONDEN TENTANG PELAYANAN PUBLIK</b>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>1. Bagaimana pendapat Bapak/Ibu tentang persyaratan administrasi untuk
                                            mendapatkan pelayanan di rumah sakit ini?</label>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="1"
                                                name="pertanyaan1" id="tidak_mudah"
                                                {{ old('pertanyaan1') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="tidak_mudah">Tidak mudah</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="2"
                                                name="pertanyaan1" id="kurang_mudah"
                                                {{ old('pertanyaan1') == '2' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="kurang_mudah">Kurang mudah</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="3"
                                                name="pertanyaan1" id="mudah"
                                                {{ old('pertanyaan1') == '3' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="mudah">Mudah</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="4"
                                                name="pertanyaan1" id="sangat_mudah"
                                                {{ old('pertanyaan1') == '4' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="sangat_mudah">Sangat mudah</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-3">
                                        <label>2. Bagaimana pemahaman Bapak/Ibu tentang kemudahan akses dalam mendapatkan
                                            pelayanan?</label>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="1"
                                                name="pertanyaan2" id="2tidak_mudah"
                                                {{ old('pertanyaan2') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="2tidak_mudah">Tidak mudah</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="2"
                                                name="pertanyaan2" id="2kurang_mudah"
                                                {{ old('pertanyaan2') == '2' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="2kurang_mudah">Kurang mudah</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="3"
                                                name="pertanyaan2" id="2mudah"
                                                {{ old('pertanyaan2') == '3' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="2mudah">Mudah</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="4"
                                                name="pertanyaan2" id="2sangat_mudah"
                                                {{ old('pertanyaan2') == '4' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="2sangat_mudah">Sangat mudah</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-3">
                                        <label>3. Bagaimana pendapat Bapak/Ibu tentang kecepatan waktu dalam memberikan
                                            pelayanan? (waktu tunggu)</label>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="1"
                                                name="pertanyaan3" id="3tidak_mudah"
                                                {{ old('pertanyaan3') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="3tidak_mudah">Tidak cepat</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="2"
                                                name="pertanyaan3" id="3kurang_mudah"
                                                {{ old('pertanyaan3') == '2' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="3kurang_mudah">Kurang cepat</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="3"
                                                name="pertanyaan3" id="3mudah"
                                                {{ old('pertanyaan3') == '3' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="3mudah">Cepat</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="4"
                                                name="pertanyaan3" id="3sangat_mudah"
                                                {{ old('pertanyaan3') == '4' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="3sangat_mudah">Sangat cepat</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-3">
                                        <label>4. Bagaimana pendapat Bapak/Ibu tentang kewajaran biaya dalam mendapatkan
                                            pelayanan? (Bila pasien JKN/asuransi tidak perlu mengisi)</label>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="1"
                                                name="pertanyaan4" id="4tidak_mudah"
                                                {{ old('pertanyaan4') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="4tidak_mudah">Tidak sesuai</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="2"
                                                name="pertanyaan4" id="4kurang_mudah"
                                                {{ old('pertanyaan4') == '2' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="4kurang_mudah">Kurang sesuai</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="3"
                                                name="pertanyaan4" id="4mudah"
                                                {{ old('pertanyaan4') == '3' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="4mudah">Sesuai</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="4"
                                                name="pertanyaan4" id="4sangat_mudah"
                                                {{ old('pertanyaan4') == '4' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="4sangat_mudah">Sangat sesuai</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <label>5. Bagaimana pendapat Bapak/Ibu tentang kesesuaian informasi pelayanan antara
                                            yang dijelaskan oleh petugas dengan yang diterima?</label>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="1"
                                                name="pertanyaan5" id="5tidak_mudah"
                                                {{ old('pertanyaan5') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="5tidak_mudah">Tidak sesuai</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="2"
                                                name="pertanyaan5" id="5kurang_mudah"
                                                {{ old('pertanyaan5') == '2' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="5kurang_mudah">Kurang sesuai</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="3"
                                                name="pertanyaan5" id="5mudah"
                                                {{ old('pertanyaan5') == '3' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="5mudah">Sesuai</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="4"
                                                name="pertanyaan5" id="5sangat_mudah"
                                                {{ old('pertanyaan5') == '4' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="5sangat_mudah">Sangat sesuai</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <label>6. Bagaimana pendapat Bapak/Ibu tentang kemampuan/keterampilan petugas dalam
                                            memberikan pelayanan?</label>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="1"
                                                name="pertanyaan6" id="6tidak_mudah"
                                                {{ old('pertanyaan6') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="6tidak_mudah">Tidak
                                                mampu/terampil</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="2"
                                                name="pertanyaan6" id="6kurang_mudah"
                                                {{ old('pertanyaan6') == '2' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="6kurang_mudah">Kurang
                                                mampu/terampil</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="3"
                                                name="pertanyaan6" id="6mudah"
                                                {{ old('pertanyaan6') == '3' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="6mudah">Mampu/terampil</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="4"
                                                name="pertanyaan6" id="6sangat_mudah"
                                                {{ old('pertanyaan6') == '4' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="6sangat_mudah">Sangat
                                                mampu/terampil</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <label>7. Bagaimana pendapat Bapak/Ibu tentang sikap petugas (sopan, ramah, tanggap,
                                            dll) dalam memberikan pelayanan?</label>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="1"
                                                name="pertanyaan7" id="7tidak_mudah"
                                                {{ old('pertanyaan7') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="7tidak_mudah">Buruk sekali</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="2"
                                                name="pertanyaan7" id="7kurang_mudah"
                                                {{ old('pertanyaan7') == '2' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="7kurang_mudah">Buruk</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="3"
                                                name="pertanyaan7" id="7mudah"
                                                {{ old('pertanyaan7') == '3' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="7mudah">Baik</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="4"
                                                name="pertanyaan7" id="7sangat_mudah"
                                                {{ old('pertanyaan7') == '4' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="7sangat_mudah">Sangat baik</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <label>8. Bagaimana pendapat Bapak/Ibu tentang mekanisme/prosedur penanganan keluhan
                                            di rumah sakit ini?</label>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="1"
                                                name="pertanyaan8" id="8tidak_mudah"
                                                {{ old('pertanyaan8') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="8tidak_mudah">Tidak ada</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="2"
                                                name="pertanyaan8" id="8kurang_mudah"
                                                {{ old('pertanyaan8') == '2' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="8kurang_mudah">Ada tetapi tidak
                                                berfungsi</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="3"
                                                name="pertanyaan8" id="8mudah"
                                                {{ old('pertanyaan8') == '3' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="8mudah">Berfungsi kurang
                                                maksimal</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="4"
                                                name="pertanyaan8" id="8sangat_mudah"
                                                {{ old('pertanyaan8') == '4' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="8sangat_mudah">Dikelola dengan
                                                baik</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <label>9. Bagaimana pendapat Bapak/Ibu tentang kualitas kamar mandi/toilet?
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="1"
                                                name="pertanyaan9" id="9tidak_mudah"
                                                {{ old('pertanyaan9') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="9tidak_mudah">Buruk</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="2"
                                                name="pertanyaan9" id="9kurang_mudah"
                                                {{ old('pertanyaan9') == '2' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="9kurang_mudah">Cukup</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="3"
                                                name="pertanyaan9" id="9mudah"
                                                {{ old('pertanyaan9') == '3' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="9mudah">Baik</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="4"
                                                name="pertanyaan9" id="9sangat_mudah"
                                                {{ old('pertanyaan9') == '4' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="9sangat_mudah">Sangat baik</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <label>Apakah Bapak/Ibu bersedia merekomendasikan RSUP Surakarta kepada teman dan
                                            kerabat?</label>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="1"
                                                name="pertanyaan10" id="ya"
                                                {{ old('pertanyaan10') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="ya">Ya</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="0"
                                                name="pertanyaan10" id="tidak"
                                                {{ old('pertanyaan10') == '2' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="tidak">Tidak</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3 mt-3 form-group" id="keterangan-keluhan-form">
                                            <label for="keterangan-keluhan">KRITIK DAN SARAN UNTUK PERBAIKAN PELAYANAN RSUP
                                                SURAKARTA
                                            </label>
                                            <textarea class="form-control" placeholder="Isikan kritik dan saran Anda" name="saran" id="keterangan_keluhan"
                                                style="height: 100px">{{ old('saran') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                {!! RecaptchaV3::field('kepuasan') !!}
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
