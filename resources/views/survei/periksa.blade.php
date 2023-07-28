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
                    <div class="card mb-5">
                        <div class="card-header text-center">
                            <h2>Periksa Status Pengaduan Pasien</h2>
                        </div>
                        <div class="card-body">
                            <div>
                                <p class="text-center">
                                    Terimakasih Anda telah meluangkan waktu untuk mengisi Formulir Pengaduan.
                                    Kami mohon maaf atas ketidaknyaman yang anda alami di RSUP Surakarta.
                                    Silahkan masukkan no Tiket yang sudah diberikan.
                                </p>
                            </div>
                            <form method="POST" action="/survei/pengaduan/periksa">
                                @csrf
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control rounded-0" name="no_tiket"
                                        value="{{ old('no_tiket') }}" placeholder="Masukkan no tiket Anda disini"
                                        maxlength="12" autofocus>
                                    <span class="input-group-append">
                                        <button type="submit" class="btn btn-info btn-flat">Cek Status</button>
                                    </span>
                                </div>
                                {!! RecaptchaV3::field('register') !!}
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
