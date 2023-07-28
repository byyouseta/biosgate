@extends('survei.layout')

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
                    <div class="card">
                        <div class="card-header text-center">
                            <h2>Formulir Pengaduan Pasien</h2>
                        </div>
                        <div class="card-body">
                            <div>
                                <p class="text-center">
                                    Terimakasih Anda telah meluangkan waktu untuk mengisi Formulir Pengaduan.
                                    Kami mohon maaf atas ketidaknyaman yang anda alami di RSUP Surakarta.
                                    Silahkan simpan no tiket dibawah ini untuk memeriksa status aduan Anda.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="card card-info">
                        <div class="card-header text-center ">
                            <b>
                                <h5>Status Nomor Tiket Pengaduan <b><u>{{ $data->no_tiket }}</u></b></h5>
                            </b>
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <h1><i class="fas fa-vote-yea fa-lg" style="color: #3ab2d6;"></i></h1>
                            </div>
                            <div class="text-center">
                                <h5><b>Detail dikeluhkan</b></h5>
                                <p>
                                    "{{ $data->deskripsi }}"
                                </p>
                            </div>
                            <div class="text-center">
                                <h5><b> Tanggal Pengaduan</b></h5>
                                <p>
                                    {{ \Carbon\Carbon::parse($data->created_at)->format('d-m-Y H:i:s') }}
                                </p>
                            </div>
                            <div class="text-center">
                                <h5><b>Status Pengaduan</b></h5>
                                @if ($data->status_keluhan_id == '0')
                                    <button class="btn btn-block btn-outline-warning btn-flat mb-3">Sedang diproses</button>
                                @elseif($data->status_keluhan_id == '1')
                                    <button class="btn-block btn-outline-success btn-flat mb-3">Keluhan sudah ditindak
                                        lanjuti</button>
                                @endif
                            </div>

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
