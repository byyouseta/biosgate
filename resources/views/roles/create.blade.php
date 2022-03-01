@extends('layouts.master')
@section('head')
    <!-- Bootstrap4 Duallistbox -->
    <link rel="stylesheet" href="{{ asset('template/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css') }}">
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="card">
                        <div class="card-header">
                            <div class="float-left">
                                <h4>Buat Grub Akses</h4>
                            </div>
                            <div class="float-right">
                                <a class="btn btn-secondary btn-sm" href="{{ route('roles.index') }}"> Kembali</a>
                            </div>
                        </div>
                        <div class="card-body">
                            {!! Form::open(['route' => 'roles.store', 'method' => 'POST']) !!}
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <strong>Nama Grup Akses:</strong>
                                        {!! Form::text('name', null, ['placeholder' => 'Nama Grup Akses', 'class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    {{-- <div class="form-group">
                                        <strong>Permission:</strong>
                                        <br />
                                        @foreach ($permission as $value)
                                            <label>{{ Form::checkbox('permission[]', $value->id, false, ['class' => 'name']) }}
                                                {{ $value->name }}</label>
                                            <br />
                                        @endforeach
                                    </div> --}}
                                    <div class="form-group">
                                        <label>Akses:</label>
                                        <select class="duallistbox" multiple="multiple" name="permission[]">
                                            @foreach ($permission as $value)
                                                <option value="{{ $value->id }}">
                                                    {{ $value->name }}</option>

                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>

            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

        </div>
    </section>

@endsection
@section('plugin')
    <!-- Bootstrap4 Duallistbox -->
    <script src="{{ asset('template/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js') }}"></script>
    <script>
        $(function() {
            //Bootstrap Duallistbox
            $('.duallistbox').bootstrapDualListbox()
        })
    </script>
@endsection
