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
                <div class="col-md-12 mx-auto">
                    <div class="card ">
                        <div class="card-header">
                            Checklist Fraud Rajal
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group" id="no-hp-form">
                                        <label for="no_hp">Nomor RM Pasien</label>
                                        <input type="text" class="form-control" id="no_hp" name="no_hp"
                                            value="{{ $norm_pasien }}" readonly>
                                    </div>
                                    <div class="form-group" id="no-hp-form">
                                        <label for="no_hp">Nama Pasien</label>
                                        <input type="text" class="form-control" id="no_hp" name="no_hp"
                                            value="{{ $data->dataPengajuan->nama_pasien }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group" id="no-hp-form">
                                        <label for="no_hp">Kode ICD X</label>
                                        <input type="text" class='form-control'
                                            value="@foreach ($diagnosa as $index => $dataDiagnosa)
{{ $dataDiagnosa->kd_penyakit }}, @endforeach "
                                            readonly>
                                    </div>
                                    <div class="form-group" id="no-hp-form">
                                        <label for="no_hp">Kode ICD IX</label>
                                        <input type="text" class="form-control" id="no_hp" name="no_hp"
                                            value="@foreach ($prosedur as $index => $dataProsedur){{ $dataProsedur->kode }}, @endforeach "
                                            readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card mb-5">
                        <div class="card-header">
                            <div class="card-title">CHECKLIST DATA PASIEN </div>
                        </div>
                        <div class="card-body">
                            <form action="/vedika/fraud/{{ Crypt::encrypt($data->id) }}/store" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        {{-- <div class="col-md-12"> --}}
                                        <label>1. UP-CODING</label>
                                        {{-- </div> --}}
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" value="1"
                                                    name="up_coding" id="up_coding1"
                                                    {{ $data->up_coding == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="up_coding1">YA</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" value="0"
                                                    name="up_coding" id="up_coding0"
                                                    {{ $data->up_coding == '0' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="up_coding0">TIDAK</label>
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-12"> --}}
                                        <label>2. PHANTOM BILLING</label>
                                        {{-- </div> --}}
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" value="1"
                                                    name="phantom_billing" id="phantom_billing1"
                                                    {{ $data->phantom_billing == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="phantom_billing1">YA</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" value="0"
                                                    name="phantom_billing" id="phantom_billing0"
                                                    {{ $data->phantom_billing == '0' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="phantom_billing0">TIDAK</label>
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-12"> --}}
                                        <label>3. CLONING</label>
                                        {{-- </div> --}}
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" value="1" name="cloning"
                                                    id="cloning1" {{ $data->cloning == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="cloning1">YA</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" value="0"
                                                    name="cloning" id="cloning0"
                                                    {{ $data->cloning == '0' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="cloning0">TIDAK</label>
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-12"> --}}
                                        <label>4. INFLATED BILLS</label>
                                        {{-- </div> --}}
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" value="1"
                                                    name="inflated_bills" id="inflated_bills1"
                                                    {{ $data->inflated_bills == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="inflated_bills1">YA</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" value="0"
                                                    name="inflated_bills" id="inflated_bills0"
                                                    {{ $data->inflated_bills == '0' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="inflated_bills0">TIDAK</label>
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-12"> --}}
                                        <label>5. PEMECAHAN EPISODE</label>
                                        {{-- </div> --}}
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" value="1"
                                                    name="pemecahan" id="pemecahan1"
                                                    {{ $data->pemecahan == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="pemecahan1">YA</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" value="0"
                                                    name="pemecahan" id="pemecahan0"
                                                    {{ $data->pemecahan == '0' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="pemecahan0">TIDAK</label>
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-12"> --}}
                                        <label>6. RUJUKAN SEMU</label>
                                        {{-- </div> --}}
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" value="1"
                                                    name="rujukan_semu" id="rujukan_semu1"
                                                    {{ $data->rujukan_semu == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="rujukan_semu1">YA</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" value="0"
                                                    name="rujukan_semu" id="rujukan_semu0"
                                                    {{ $data->rujukan_semu == '0' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="rujukan_semu0">TIDAK</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        {{-- <div class="col-md-12"> --}}
                                        <label>7. REPEAT BILLING</label>
                                        {{-- </div> --}}
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" value="1"
                                                    name="repeat_billing" id="repeat_billing1"
                                                    {{ $data->repeat_billing == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="repeat_billing1">YA</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" value="0"
                                                    name="repeat_billing" id="repeat_billing0"
                                                    {{ $data->repeat_billing == '0' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="repeat_billing0">TIDAK</label>
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-12"> --}}
                                        <label>8. PROLONGED LOS</label>
                                        {{-- </div> --}}
                                        <div class="col-md-6">
                                            <div class="form-check mr-3">
                                                <input class="form-check-input" type="radio" value="1"
                                                    name="prolonged_los" id="prolonged_los1"
                                                    {{ $data->prolonged_los == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="prolonged_los1">YA</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" value="0"
                                                    name="prolonged_los" id="prolonged_los0"
                                                    {{ $data->prolonged_los == '0' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="prolonged_los0">TIDAK</label>
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-12"> --}}
                                        <label>9. MANIPULASI KELS PERAWATAN</label>
                                        {{-- </div> --}}
                                        <div class="col-md-6">
                                            <div class="form-check mr-3">
                                                <input class="form-check-input" type="radio" value="1"
                                                    name="manipulasi_kels" id="manipulasi_kels1"
                                                    {{ $data->manipulasi_kels == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="manipulasi_kels1">YA</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" value="0"
                                                    name="manipulasi_kels" id="manipulasi_kels0"
                                                    {{ $data->manipulasi_kels == '0' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="manipulasi_kels0">TIDAK</label>
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-12"> --}}
                                        <label>10. RE-ADMISI</label>
                                        {{-- </div> --}}
                                        <div class="col-md-6">
                                            <div class="form-check mr-3">
                                                <input class="form-check-input" type="radio" value="1"
                                                    name="re_admisi" id="re_admisi1"
                                                    {{ $data->re_admisi == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="re_admisi1">YA</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" value="0"
                                                    name="re_admisi" id="re_admisi0"
                                                    {{ $data->re_admisi == '0' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="re_admisi0">TIDAK</label>
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-12"> --}}
                                        <label>11. TINDAKAN TDK SESUAI INDIKASI</label>
                                        {{-- </div> --}}
                                        <div class="col-md-6">
                                            <div class="form-check mr-3">
                                                <input class="form-check-input" type="radio" value="1"
                                                    name="kesesuaian_tindakan" id="kesesuaian_tindakan1"
                                                    {{ $data->kesesuaian_tindakan == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="kesesuaian_tindakan1">YA</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" value="0"
                                                    name="kesesuaian_tindakan" id="kesesuaian_tindakan0"
                                                    {{ $data->kesesuaian_tindakan == '0' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="kesesuaian_tindakan0">TIDAK</label>
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-12"> --}}
                                        <label>12. MENAGIHKAN TINDAKAN YG TDK DILAKUKAN</label>
                                        {{-- </div> --}}
                                        <div class="col-md-6">
                                            <div class="form-check mr-3">
                                                <input class="form-check-input" type="radio" value="1"
                                                    name="tagihan_tindakan" id="tagihan_tindakan1"
                                                    {{ $data->tagihan_tindakan == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="tagihan_tindakan1">YA</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" value="0"
                                                    name="tagihan_tindakan" id="tagihan_tindakan0"
                                                    {{ $data->tagihan_tindakan == '0' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="tagihan_tindakan0">TIDAK</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3 mt-3 form-group" id="keterangan-keluhan-form">
                                            <label for="klarifikasi">KLARIFIKASI </label>
                                            <textarea class="form-control" placeholder="Isikan Klarifikasi" name="klarifikasi" id="klarrifikasi"
                                                style="height: 100px">{{ $data->klarifikasi }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3 form-group" id="keterangan-keluhan-form">
                                            <label for="keterangan">KETERANGAN </label>
                                            <textarea class="form-control" placeholder="Isikan Keterangan" name="keterangan" id="keterangan"
                                                style="height: 100px">{{ $data->keterangan }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="float-right">
                                    <input type="Submit" class="btn btn-primary">
                                </div>
                            </form>
                        </div>
                    </div>
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
