@extends('layouts.master')

<!-- isi bagian konten -->
<!-- cara penulisan isi section yang panjang -->
@section('head')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('template/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <!-- Tempusdominus|Datetime Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('template/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">
            @php
                $today = \Carbon\Carbon::now();
                // tahun
                $umur = $today->diff($data->tgl_lahir)->y;
            @endphp
            <form role="form" action="/operasi/booking" method="post">
                {{ csrf_field() }}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Booking Operasi</h3>
                    </div>
                    <!-- /.box-header -->

                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>No Rekam Medis</label>
                                    <input type="text" class="form-control" name="no_rm"
                                        value="{{ $data->no_rkm_medis }}" readonly>
                                    <input type="hidden" class="form-control" name="id_booking"
                                        value="{{ $data->id_booking }}">
                                </div>
                                <div class="form-group">
                                    <label>No Rawat</label>
                                    <input type="text" class="form-control" name="no_rawat" value="{{ $data->no_rawat }}"
                                        readonly>
                                </div>
                                <div class="form-group">
                                    <label>Nama Pasien/ JK/ Umur</label>
                                    <input type="text" class="form-control" name="nama_pasien"
                                        value="{{ $data->nm_pasien }}/ {{ $data->jk }}/ {{ $umur }} Tahun"
                                        readonly>
                                </div>
                                <div class="form-group">
                                    <label>Alamat Pasien</label>
                                    <input type="text" class="form-control" name="alamat" value="{{ $data->alamat }}"
                                        readonly>
                                </div>
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="Menunggu">Menunggu</option>
                                        <option value="Proses Operasi">Proses Operasi</option>
                                        <option value="Selesai">Selesai</option>
                                        <option value="Batal">Batal</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    <label>Kode Paket</label>
                                    <input type="text" class="form-control" name="kode_paket"
                                        value="{{ $data->kode_paket }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Nama Perawatan</label>
                                    <input type="text" class="form-control" name="nama_perawatan"
                                        value="{{ $data->nm_perawatan }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Kelas</label>
                                    <input type="text" class="form-control" name="kelas" value="{{ $data->kelas }}"
                                        readonly>
                                </div>
                                <div class="form-group">
                                    <label>Biaya</label>
                                    <input type="text" class="form-control" name="biaya"
                                        value="Rp {{ number_format($data->biaya, 2, ',', '.') }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Tanggal Operasi</label>
                                    <div class="input-group date" id="tanggal" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input "
                                            value="{{ $data->tanggal }}" data-target="#tanggal"
                                            data-toggle="datetimepicker" name="tanggal" value="" autocomplete="off"
                                            required />
                                        <div class="input-group-append" data-target="#tanggal" data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group ">
                                    <label>Operator</label>
                                    <select name="operator" class="form-control select2" required>
                                        @foreach ($dokter as $listDokter)
                                            <option value="{{ $listDokter->kd_dokter }}">{{ $listDokter->nm_dokter }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group ">
                                    <label>Ruang OK</label>
                                    <select name="ruang_ok" class="form-control">
                                        @foreach ($ruang as $listRuang)
                                            <option value="{{ $listRuang->kd_ruang_ok }}">{{ $listRuang->nm_ruang_ok }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label>Jam Mulai</label>
                                    <div class="input-group date" id="jam_mulai" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input "
                                            data-target="#jam_mulai" data-toggle="datetimepicker" name="jam_mulai"
                                            value="" autocomplete="off" required />
                                        <div class="input-group-append" data-target="#jam_mulai"
                                            data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label>Jam Selesai</label>
                                    <div class="input-group date" id="jam_selesai" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input "
                                            data-target="#jam_selesai" data-toggle="datetimepicker" name="jam_selesai"
                                            value="" autocomplete="off" required />
                                        <div class="input-group-append" data-target="#jam_selesai"
                                            data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- </div> --}}


                    <!-- /.box-body -->
                    <div class="card-footer">
                        <a href="/operasi/booking" class="btn btn-default">Kembali</a>
                        <button type="submit" class="btn btn-primary">Jadwalkan</button>
                    </div>

                </div>
            </form>

        </div>
    </section>
@endsection
@section('plugin')
    <!-- Select2 -->
    <script src="{{ asset('template/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{ asset('template/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('template/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <script>
        $(function() {
            //Initialize Select2 Elements
            $('.select2').select2()
        });
        $('#tanggal').datetimepicker({
            format: 'YYYY-MM-DD'
        });
        $('#jam_mulai').datetimepicker({
            format: 'HH:mm'
        });
        $('#jam_selesai').datetimepicker({
            format: 'HH:mm'
        });
    </script>
@endsection
