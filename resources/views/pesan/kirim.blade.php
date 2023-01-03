@extends('layouts.master')

@section('head')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('template/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- Tempusdominus|Datetime Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('template/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <form method="POST" action="/pesan/kirim">
                        @csrf
                        <div class="card">
                            {{-- <div class="card-header">
                            Kirim Pesan
                        </div> --}}
                            <div class="card-body">
                                <div class="form-group">
                                    <label>No Penerima <small>(Dalam format 628XXX)</small></label>
                                    <input type="text" class="form-control" name="penerima" required />
                                    @if ($errors->has('penerima'))
                                        <div class="text-danger">
                                            {{ $errors->first('penerima') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Pesan</label>
                                    <textarea name="pesan" class="form-control" required></textarea>
                                    @if ($errors->has('pesan'))
                                        <div class="text-danger">
                                            {{ $errors->first('pesan') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Kirim</button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    {{-- Modal Add --}}
    <div class="modal fade" id="modal-default">
        <div class="modal-dialog">
            {{-- <div class="modal-content">
                <form method="POST" action="/setting/store">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">Setting Api BIOS</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Nama APP</label>
                                    <input type="text" class="form-control" name="nama" required>
                                    @if ($errors->has('nama'))
                                        <div class="text-danger">
                                            {{ $errors->first('nama') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Base URL</label>
                                    <input type="text" class="form-control" name="base_url" required>
                                    @if ($errors->has('base_url'))
                                        <div class="text-danger">
                                            {{ $errors->first('base_url') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Kode Satker</label>
                                    <input type="text" name="kode_satker" class="form-control" required />
                                    @if ($errors->has('kode_satker'))
                                        <div class="text-danger">
                                            {{ $errors->first('kode_satker') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Satker Key</label>
                                    <input type="text" name="key" class="form-control" required />
                                    @if ($errors->has('key'))
                                        <div class="text-danger">
                                            {{ $errors->first('key') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Tutup</button>
                        <button type="Submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div> --}}
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    {{-- Modal Edit --}}
    {{-- @if ($data->count() > 0)
        <div class="modal fade" id="modal-edit">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="/setting/update">
                        @csrf
                        <div class="modal-header">
                            <h4 class="modal-title">Setting Api BIOS</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <!-- text input -->
                                <div class="col-12">
                                    @foreach ($data as $edit)
                                        <div class="form-group">
                                            <label>Base URL</label>
                                            <input type="text" class="form-control" name="base_url"
                                                value="{{ $edit->base_url }}" required>
                                            @if ($errors->has('base_url'))
                                                <div class="text-danger">
                                                    {{ $errors->first('base_url') }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label>Kode Satker</label>
                                            <input type="text" name="kode_satker" class="form-control"
                                                value="{{ $edit->satker }}" required />
                                            @if ($errors->has('kode_satker'))
                                                <div class="text-danger">
                                                    {{ $errors->first('kode_satker') }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label>Satker Key</label>
                                            <input type="text" name="key" class="form-control"
                                                value="{{ $edit->key }}" required />
                                            @if ($errors->has('key'))
                                                <div class="text-danger">
                                                    {{ $errors->first('key') }}
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Tutup</button>
                            <button type="Submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    @endif --}}

    <!-- /.modal -->
@endsection
@section('plugin')
    <script src="{{ asset('template/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('template/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('template/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('template/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{ asset('template/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('template/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <script>
        $(function() {
            $('#example2').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": false,
                "ordering": false,
                "info": false,
                "autoWidth": false,
                "responsive": false,
                "scrollY": "300px",
                "scrollX": false,
            });
            $('#example').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": false,
                "ordering": false,
                "info": false,
                "autoWidth": false,
                "responsive": false,
                "scrollY": "300px",
                "scrollX": false,
            });
        });
        //Date picker
        $('#tanggal').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    </script>
@endsection
