@extends('layouts.master')

<!-- isi bagian konten -->
<!-- cara penulisan isi section yang panjang -->
@section('content')
    <section class="content">
        <div class="container-fluid">

            <form role="form" action="/setting/update/{{ $data->id }}" method="post">
                {{ csrf_field() }}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Kategori</h3>
                    </div>
                    <!-- /.box-header -->

                    <div class="card-body">
                        <div class="row">
                            <!-- text input -->
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Nama App</label>
                                    <input type="text" class="form-control" name="nama" required
                                        value="{{ $data->nama }}">
                                    @if ($errors->has('nama'))
                                        <div class="text-danger">
                                            {{ $errors->first('nama') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Kode Satker</label>
                                    <input type="text" name="kode_satker" class="form-control" required
                                        value="{{ $data->satker }}" />
                                    @if ($errors->has('kode_satker'))
                                        <div class="text-danger">
                                            {{ $errors->first('kode_satker') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Satker Key</label>
                                    <input type="text" name="key" class="form-control" required
                                        value="{{ $data->key }}" />
                                    @if ($errors->has('key'))
                                        <div class="text-danger">
                                            {{ $errors->first('key') }}
                                        </div>
                                    @endif
                                </div>
                            </div>


                            <div class="col-6">
                                <div class="form-group">
                                    <label>Base URL</label>
                                    <input type="text" class="form-control" name="base_url" required
                                        value="{{ $data->base_url }}">
                                    @if ($errors->has('base_url'))
                                        <div class="text-danger">
                                            {{ $errors->first('base_url') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Pembaharuan Terakhir</label>

                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ $data->updated_at }}"
                                            readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Dibuat</label>

                                    <div class="input-group">
                                        <input type="text" class="form-control" name="created_at"
                                            value="{{ $data->created_at }}" readonly>
                                    </div>
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
