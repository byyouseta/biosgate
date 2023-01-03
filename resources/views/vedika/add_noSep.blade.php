@extends('layouts.master')

<!-- isi bagian konten -->
<!-- cara penulisan isi section yang panjang -->
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
            height: 200px;
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

            <form role="form" action="/vedika/rajal/simpansep" method="post">
                {{ csrf_field() }}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Tambah SEP</h3>
                    </div>
                    <!-- /.box-header -->

                    <div class="card-body">
                        <div class="row">
                            <!-- text input -->
                            <div class="col-6">
                                <div class="form-group">
                                    <label>No Rawat</label>
                                    <input type="text" class="form-control" name="noRawat" required
                                        value="{{ $data->no_rawat }}">
                                    @if ($errors->has('noRawat'))
                                        <div class="text-danger">
                                            {{ $errors->first('noRawat') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Nama Pasien</label>
                                    <input type="text" class="form-control" name="nama" required
                                        value="{{ $data->nm_pasien }}">
                                    @if ($errors->has('nama'))
                                        <div class="text-danger">
                                            {{ $errors->first('nama') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>No SEP</label>
                                    <input type="text" name="no_sep" class="form-control" required />
                                    @if ($errors->has('no_sep'))
                                        <div class="text-danger">
                                            {{ $errors->first('no_sep') }}
                                        </div>
                                    @endif
                                </div>

                            </div>
                            <div class="col-6">
                                {{-- <div class="col-md-12"> --}}
                                <label class="" for="">Tanda tangan</label>
                                <br />
                                <div id="sig"></div>
                                <br />
                                <button id="clear" class="btn btn-danger btn-sm">Ulang Tanda tangan</button>
                                <textarea id="signature64" name="signed" style="display: none"></textarea>
                                {{-- </div>
                            <br />
                            <button class="btn btn-success">Save</button> --}}

                            </div>
                        </div>
                    </div>

                    <!-- /.box-body -->
                    <div class="card-footer">
                        {{-- <a href="/vedika" class="btn btn-default">Kembali</a> --}}
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>

                </div>
            </form>

        </div>
    </section>
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
