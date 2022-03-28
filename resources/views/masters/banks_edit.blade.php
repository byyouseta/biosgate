@extends('layouts.master')

<!-- isi bagian konten -->
<!-- cara penulisan isi section yang panjang -->
@section('content')
    <section class="content">
        <div class="container-fluid">

            <form role="form" action="/master/bank/update/{{ $data->id }}" method="post">
                {{ csrf_field() }}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Bank</h3>
                    </div>
                    <!-- /.box-header -->

                    <div class="card-body">
                        <div class="row">
                            <!-- text input -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Nama Bank</label>
                                    <select name="nama" class="form-control select2" required>
                                        @foreach ($bank as $bank)
                                            <option value="{{ $bank->kode }}-{{ $bank->uraian }}"
                                                @if ($bank->kode == $data->kd_bank) selected @endif>
                                                {{ $bank->kode }}
                                                -
                                                {{ $bank->uraian }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('nama'))
                                        <div class="text-danger">
                                            {{ $errors->first('nama') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>No Rekening</label>
                                    <input type="text" class="form-control" placeholder="Masukkan No Rekening"
                                        name="norek" value="{{ $data->norek }}" required>
                                    @if ($errors->has('norek'))
                                        <div class="text-danger">
                                            {{ $errors->first('norek') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="card-footer">
                        <a href="/setting" class="btn btn-default">Kembali</a>
                        <button type="submit" class="btn btn-primary">Perbaharui</button>
                    </div>

                </div>
            </form>

        </div>
    </section>
@endsection
