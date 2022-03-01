@extends('layouts.master')

<!-- isi bagian konten -->
<!-- cara penulisan isi section yang panjang -->
@section('content')
    <section class="content">
        <div class="container-fluid">

            <form role="form" action="/profil/update" method="post">
                {{ csrf_field() }}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Update Profile</h3>
                    </div>
                    <!-- /.box-header -->

                    <div class="card-body">
                        <div class="row">
                            <!-- text input -->
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Nama User</label>
                                    <input type="text" class="form-control" placeholder="Masukkan Nama User" name="name"
                                        value="{{ $data->name }}">
                                    @if ($errors->has('name'))
                                        <div class="text-danger">
                                            {{ $errors->first('name') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>NIP/PIN</label>
                                    <input type="text" class="form-control" placeholder="Masukkan NIP/PIN SIMADAM"
                                        name="username" value="{{ $data->username }}">
                                    @if ($errors->has('username'))
                                        <div class="text-danger">
                                            {{ $errors->first('username') }}
                                        </div>
                                    @endif
                                </div>


                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="text" class="form-control" placeholder="Masukkan Email User" name="email"
                                        value="{{ $data->email }}">
                                    @if ($errors->has('email'))
                                        <div class="text-danger">
                                            {{ $errors->first('email') }}
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
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="card-footer">
                        {{-- <a href="/user" class="btn btn-default">Kembali</a> --}}
                        <button type="submit" class="btn btn-primary">Perbaharui</button>
                    </div>

                </div>
            </form>
            <form action="/profil/password" method="POST">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Ganti Password</h3>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <!-- text input -->
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Password Lama</label>
                                    <input type="password" class="form-control" placeholder="Masukkan Password Lama"
                                        name="old_password" autocomplete="off">
                                    @if ($errors->has('old_password'))
                                        <div class="text-danger">
                                            {{ $errors->first('old_password') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Password Baru</label>
                                    <input type="password" class="form-control" placeholder="Masukkan Password Baru"
                                        name="password" autocomplete="off">
                                    @if ($errors->has('password'))
                                        <div class="text-danger">
                                            {{ $errors->first('password') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Konfirmasi Password Baru</label>
                                    <input type="password" class="form-control" placeholder="Konfirmasi Password Baru"
                                        name="password_confirmation" autocomplete="off">
                                    @if ($errors->has('password_confirmation'))
                                        <div class="text-danger">
                                            {{ $errors->first('password_confirmation') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        {{-- <a href="/user" class="btn btn-default">Kembali</a> --}}
                        <button type="submit" class="btn btn-primary">Ganti Password</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
