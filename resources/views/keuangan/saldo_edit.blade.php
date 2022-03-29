@extends('layouts.master')

<!-- isi bagian konten -->
<!-- cara penulisan isi section yang panjang -->
@section('head')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('template/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">

            <form role="form" action="/saldokeuangan/update/{{ $data->id }}" method="post">
                {{ csrf_field() }}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Saldo</h3>
                    </div>
                    <!-- /.box-header -->

                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>Bank</label>
                                    <select name="bank" class="form-control select2">
                                        @foreach ($bank as $bank)
                                            <option value="{{ $bank->id }}"
                                                @if ($bank->id == $data->bank_id) selected @endif>
                                                {{ $bank->nama }} - {{ $bank->norek }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('bank'))
                                        <div class="text-danger">
                                            {{ $errors->first('bank') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Jumlah</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="number" class="form-control" name="saldo"
                                            value="{{ $data->saldo }}" required>
                                    </div>
                                    @if ($errors->has('saldo'))
                                        <div class="text-danger">
                                            {{ $errors->first('saldo') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Kode Rekening</label>
                                    <select name="kd_rek" class="form-control select2">
                                        @foreach ($rekening as $rekening)
                                            <option value="{{ $rekening->kode }}"
                                                @if ($rekening->kode == $data->kd_rek) selected @endif>
                                                {{ $rekening->uraian }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('kd_rek'))
                                        <div class="text-danger">
                                            {{ $errors->first('kd_rek') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Tanggal Transaksi</label>
                                    <input type="date" class="form-control" name="tgl_transaksi"
                                        value="{{ $data->tgl_transaksi }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- /.box-body -->
                    <div class="card-footer">
                        <a href="/saldokeuangan" class="btn btn-default">Kembali</a>
                        <button type="submit" class="btn btn-primary">Perbaharui</button>
                    </div>

                </div>
            </form>

        </div>
    </section>
@endsection
@section('plugin')
    <!-- Select2 -->
    <script src="{{ asset('template/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(function() {
            //Initialize Select2 Elements
            $('.select2').select2()
        });
    </script>
@endsection
