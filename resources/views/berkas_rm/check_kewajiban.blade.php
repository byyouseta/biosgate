@extends('layouts.master')

@section('head')
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.css">

    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <link type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/south-street/jquery-ui.css"
        rel="stylesheet">
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script type="text/javascript" src="//keith-wood.name/js/jquery.signature.js"></script>

    <link rel="stylesheet" type="text/css" href="//keith-wood.name/css/jquery.signature.css">

    <style>
        .kbw-signature {
            width: 100%;
            height: 220px;
        }

        #sig canvas {
            width: 100% !important;
            height: auto;
        }
    </style>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 mx-auto">
                    <div class="card ">
                        <div class="card-header">
                            <div class="card-title">FORM HAK DAN KEWAJIBAN PASIEN DAN KELUARGANYA </div>
                            <div class="float-right">RM PP 02 Rev. 1</div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="no_hp">Nomor RM</label>
                                        <input type="text" class="form-control" name="noRm"
                                            value="{{ $data->no_rkm_medis }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="no_hp">NIK</label>
                                        <input type="text" class="form-control" name="nik"
                                            value="{{ $data->ktp_pasien }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="no_hp">Nama Pasien</label>
                                        <input type="text" class="form-control" name="nama"
                                            value="{{ $data->nm_pasien }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="no_hp">Tanggal Lahir</label>
                                        <input type="text" class="form-control" name="tglLahir"
                                            value="{{ $data->tgl_lahir }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="no_hp">Alamat</label>
                                        <input type="text" class="form-control" name="alamat"
                                            value="{{ $data->alamat }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-12">
                    <form action="/berkasrm/hakkewajiban/store" method="POST">
                        @csrf
                        <div class="card card-primary card-outline card-outline-tabs">
                            <div class="card-header p-0 border-bottom-0">
                                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link " id="custom-tabs-hak-tab" data-toggle="pill"
                                            href="#custom-tabs-four-hak" role="tab"
                                            aria-controls="custom-tabs-four-home" aria-selected="true">HAK</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="custom-tabs-kewajiban-tab" data-toggle="pill"
                                            href="#custom-tabs-four-kewajiban" role="tab"
                                            aria-controls="custom-tabs-four-kewajiban" aria-selected="false">KEWAJIBAN</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link active" id="custom-tabs-tandatangan-tab" data-toggle="pill"
                                            href="#custom-tabs-four-tandatangan" role="tab"
                                            aria-controls="custom-tabs-four-tandatangan" aria-selected="false">TANDA
                                            TANGAN</a>
                                    </li>
                                    <li class="pt-0 px-0 text-right">
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="maximize"><i
                                                    class="fas fa-expand"></i>
                                            </button>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                            <div class="card-body">
                                <div class="tab-content" id="custom-tabs-four-tabContent">
                                    <div class="tab-pane fade " id="custom-tabs-four-hak" role="tabpanel"
                                        aria-labelledby="custom-tabs-four-hak-tab">
                                        <table class="table table-bordered table-sm">
                                            <thead>
                                                <tr class="text-center">
                                                    <th style="width:5%">No </th>
                                                    <th>Hak Pasien dan Keluarganya</th>
                                                    <th style="width:10%">
                                                        <div class="custom-control custom-checkbox">
                                                            <!-- select all boxes -->
                                                            <input type="checkbox" name="select-all" id="select-all"
                                                                class="custom-control-input" />
                                                            <label for="select-all" class="custom-control-label">Check
                                                                List</label>
                                                        </div>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-center">1</td>
                                                    <td>Memperoleh informasi mengenai tata tertib dan peraturan yang berlaku
                                                        di
                                                        rumah sakit</td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox"
                                                                name="hak1" id="checkHak1" value="1">
                                                            <label for="checkHak1" class="custom-control-label"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">2</td>
                                                    <td>Memperoleh informasi tentang hak dan kewajiban pasien</td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox"
                                                                name="hak2" id="checkHak2" value="1">
                                                            <label for="checkHak2" class="custom-control-label"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">3</td>
                                                    <td>Memperoleh pelayanan yang manusiawi, adil, dan jujur</td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox"
                                                                name="hak3" id="checkHak3" value="1">
                                                            <label for="checkHak3" class="custom-control-label"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">4</td>
                                                    <td>Memperoleh pelayanan medis yang bermutu sesuai dengan standart
                                                        profesi
                                                        dan standar prosedur operasional</td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox"
                                                                name="hak4" id="checkHak4" value="1">
                                                            <label for="checkHak4" class="custom-control-label"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">5</td>
                                                    <td>Memperoleh layanan yang efektif dan efisien sehingga pasien
                                                        terhindar
                                                        dari kerugian fisik dan materi</td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox"
                                                                name="hak5" id="checkHak5" value="1">
                                                            <label for="checkHak5" class="custom-control-label"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">6</td>
                                                    <td>Mengajukan pengaduan atas kealitas pelayanan yang didapatkan</td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox"
                                                                name="hak6" id="checkHak6" value="1">
                                                            <label for="checkHak6" class="custom-control-label"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">7</td>
                                                    <td>Memilih dokter dan kelas perawatan sesuai dengan keinginannya dan
                                                        peraturan yang berlaku di rumah sakit</td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox"
                                                                name="hak7" id="checkHak7" value="1">
                                                            <label for="checkHak7" class="custom-control-label"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">8</td>
                                                    <td>Meminta konsultasi tentang penyakit yang dideritanya kepada dokter
                                                        lain
                                                        yang mempunyai Surat Izin Praktek (SIP) baik didalam maupun diluar
                                                        rumah
                                                        sakit</td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox"
                                                                name="hak8" id="checkHak8" value="1">
                                                            <label for="checkHak8" class="custom-control-label"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">9</td>
                                                    <td>Mendapatkan privasi dan kerahasiaan penyakit yang diderita termasuk
                                                        data-data medisnya</td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox"
                                                                name="hak9" id="checkHak9" value="1">
                                                            <label for="checkHak9" class="custom-control-label"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">10</td>
                                                    <td>Mendapat informasi yang meliputi diagnosis dan tata cara tindakan
                                                        medis,
                                                        tujuan tindakan medis, alternatif tindakan, resiko dan komplikasi
                                                        yang
                                                        mungkin terjadi,
                                                        dan prognosis terhadap tindakan yang dilakukan serta perkiraan biaya
                                                        pengobatan</td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox"
                                                                name="hak10" id="checkHak10" value="1">
                                                            <label for="checkHak10" class="custom-control-label"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">11</td>
                                                    <td>Memberika persetujuan atau penolakan atas tindakan yang akan
                                                        dilakukan
                                                        oleh tenaga kesehatan terhadap penyakit yang dideritanya</td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox"
                                                                name="hak11" id="checkHak11" value="1">
                                                            <label for="checkHak11" class="custom-control-label"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">12</td>
                                                    <td>Didampingi keluarganya dalam keadaan kritis</td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox"
                                                                name="hak12" id="checkHak12" value="1">
                                                            <label for="checkHak12" class="custom-control-label"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">13</td>
                                                    <td>Menjalankan ibadah sesuai agama dan kepercayaan yang dianutnya
                                                        selama
                                                        hal itu tidak mengganggu pasien lainnya</td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox"
                                                                name="hak13" id="checkHak13" value="1">
                                                            <label for="checkHak13" class="custom-control-label"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">14</td>
                                                    <td>Memperoleh keamanan dan keselamatan dirinya selama dalam perawatan
                                                        di
                                                        rumah sakit</td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox"
                                                                name="hak14" id="checkHak14" value="1">
                                                            <label for="checkHak14" class="custom-control-label"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">15</td>
                                                    <td>Mengajukan usul, saran, perbaikan atas perlakuan rumah sakit
                                                        terhadap
                                                        dirinya</td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox"
                                                                name="hak15" id="checkHak15" value="1">
                                                            <label for="checkHak15" class="custom-control-label"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">16</td>
                                                    <td>Menolak pelayanan bimbingan rohani yang tidak sesuai dengan agama
                                                        dan
                                                        kepercayaan yang dianutnya</td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox"
                                                                name="hak16" id="checkHak16" value="1">
                                                            <label for="checkHak16" class="custom-control-label"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">17</td>
                                                    <td>Menggugat dan / atau menuntut rumah sakit apabila rumah sakit diduga
                                                        memberikan pelayanan yang tidak sesuai dengan standart
                                                        baik secara perdata ataupun pidana</td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox"
                                                                name="hak17" id="checkHak17" value="1">
                                                            <label for="checkHak17" class="custom-control-label"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">18</td>
                                                    <td>Mengeluhkan pelayanan rumah sakit yang tidak sesuai dengan standar
                                                        pelayanan melalui media cetak dan elektronik
                                                        sesuai dengan ketentuan perundang-undangan</td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox"
                                                                name="hak18" id="checkHak18" value="1">
                                                            <label for="checkHak18" class="custom-control-label"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade" id="custom-tabs-four-kewajiban" role="tabpanel"
                                        aria-labelledby="custom-tabs-four-kewajiban-tab">
                                        <table class="table table-bordered table-sm">
                                            <thead>
                                                <tr class="text-center">
                                                    <th style="width:5%">No </th>
                                                    <th>Kewajiban Pasien dan Keluarganya</th>
                                                    <th style="width:10%">
                                                        <div class="custom-control custom-checkbox">
                                                            <!-- select all boxes -->
                                                            <input type="checkbox" name="select-all" id="select-all"
                                                                class="custom-control-input" />
                                                            <label for="select-all" class="custom-control-label">Check
                                                                List</label>
                                                        </div>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-center">1</td>
                                                    <td>Setiap Pasien dan Keluarga berkewajiban untuk mentaati peraturan dan
                                                        tata tertib RSUP Surakarta</td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox"
                                                                name="kewajiban1" id="checkWajib1" value="1">
                                                            <label for="checkWajib1" class="custom-control-label"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">2</td>
                                                    <td>Memperlakukan staf rumah sakit, pasien lainnya dan pengunjung dengan
                                                        sopan dan hormat</td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox"
                                                                name="kewajiban2" id="checkWajib2" value="1">
                                                            <label for="checkWajib2" class="custom-control-label"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">3</td>
                                                    <td>Bertanggung jawab atas keamanan barang-barang berharga selama di
                                                        rumah
                                                        sakit</td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox"
                                                                name="kewajiban3" id="checkWajib3" value="1">
                                                            <label for="checkWajib3" class="custom-control-label"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">4</td>
                                                    <td>Menyelesaikan tanggung jawab keuangan</td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox"
                                                                name="kewajiban4" id="checkWajib4" value="1">
                                                            <label for="checkWajib4" class="custom-control-label"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">5</td>
                                                    <td>Memberikan informasi yang diperlukan untuk pengobatan dengan benar,
                                                        jelas dan jujur tentang masalah kesehatannya</td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox"
                                                                name="kewajiban5" id="checkWajib5" value="1">
                                                            <label for="checkWajib5" class="custom-control-label"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">6</td>
                                                    <td>Berpartisipasi aktif dan patuh terhadap pengobatan, termasuk
                                                        keputusan
                                                        mengenai rencana pengobatan yang diberikan</td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox"
                                                                name="kewajiban6" id="checkWajib6" value="1">
                                                            <label for="checkWajib6" class="custom-control-label"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">7</td>
                                                    <td>Bertanggung jawab atas semua konsekuensi yang ada apabila menolak
                                                        pengobatan medis</td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox"
                                                                name="kewajiban7" id="checkWajib7" value="1">
                                                            <label for="checkWajib7" class="custom-control-label"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">8</td>
                                                    <td>Memberika imbalan jasa atas pelayanan yang diterima</td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox"
                                                                name="kewajiban8" id="checkWajib8" value="1">
                                                            <label for="checkWajib8" class="custom-control-label"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade show active" id="custom-tabs-four-tandatangan"
                                        role="tabpanel" aria-labelledby="custom-tabs-four-tandatangan-tab">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="informan">Pemberi Informasi</label>
                                                    <input type="text" class="form-control" name="informan"
                                                        value="{{ Auth::user()->name }}" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label for="statusPj">Penanggung Jawab</label>
                                                    <div class="form-group">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio"
                                                                name="statusPj" id="pjPasien" value="pasien">
                                                            <label class="form-check-label" for="pjPasien">Pasien</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio"
                                                                name="statusPj" id="pjKeluarga" value="keluarga" checked>
                                                            <label class="form-check-label"
                                                                for="pjKeluarga">Keluarga</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="namaPj">Nama</label>
                                                    <input type="text" class="form-control" name="namaPj"
                                                        value="{{ $data->nm_pasien }}" required>
                                                    <input type="hidden" class="form-control" name="noRawat"
                                                        value="{{ $data->no_rawat }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <label class="" for="">Tanda tangan</label>
                                                <br />
                                                <div id="sig"></div>
                                                <br />
                                                <button id="clear" class="btn btn-danger btn-sm">Ulang Tanda
                                                    tangan</button>
                                                <textarea id="signature64" name="signed" style="display: none" required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card -->
                            </div>
                            <div class="card-footer">
                                {{-- <a href="/vedika" class="btn btn-default">Kembali</a> --}}
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </section><!-- /.container-fluid -->
    <script type="text/javascript">
        var sig = $('#sig').signature({
            syncField: '#signature64',
            syncFormat: 'PNG'
        });
        $('#clear').click(function(e) {
            e.preventDefault();
            sig.signature('clear');
            $("#signature64").val('');
        });
    </script>
@endsection
@section('plugin')
    <script>
        // Listen for click on toggle checkbox
        $('#select-all').click(function(event) {
            if (this.checked) {
                // Iterate each checkbox
                $(':checkbox').each(function() {
                    this.checked = true;
                });
            } else {
                $(':checkbox').each(function() {
                    this.checked = false;
                });
            }
        });
    </script>
@endsection
