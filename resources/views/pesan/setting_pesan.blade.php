@extends('layouts.master')

@section('head')
    <meta http-equiv="refresh" content="60;url=/pesan" />
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
                    <div class="card">
                        <div class="card-header">
                            {{-- @can('setting-create')
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-default">
                                    <i class="fa fa-plus-circle"></i> Tambah</a>
                                </button>
                            @endcan --}}
                            Server Status
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Server Pesan</th>
                                            <th class="align-middle">Port</th>
                                            <th class="align-middle">Status</th>
                                            {{-- <th class="align-middle">Key</th> --}}
                                            <th class="align-middle">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $status[0] != null ? $status[0] : '-' }}</td>
                                            <td>{{ $status[1] != null ? $status[1] : '-' }}</td>
                                            <td>{{ $status[2] }}</td>
                                            {{-- <td>{{ $setting->key }}</td> --}}
                                            <td>
                                                @can('setting-update')
                                                    <div class="col text-center">
                                                        <div class="btn-group">
                                                            {{-- {{ dd($sessionApp) }} --}}


                                                            {{-- @if ($status[2] == 'online') --}}
                                                            {{-- @if ($sessionApp == false) --}}
                                                            <a href="/pesan/createsession"
                                                                class="btn {{ $sessionApp == 'true' ? 'btn-success' : 'btn-danger' }} btn-sm"
                                                                data-toggle="tooltip" data-placement="bottom"
                                                                title="{{ $sessionApp == 'true' ? 'Session App Found' : 'Session App Not Found' }}">
                                                                <i class="fas fa-link"></i>
                                                            </a>
                                                            {{-- @elseif ($sessionApp == true) --}}
                                                            <a href="/pesan/deletesession"
                                                                class="btn btn-success btn-sm delete-confirm"
                                                                data-toggle="tooltip" data-placement="bottom"
                                                                title="Delete Session App">
                                                                <i class="fas fa-unlink"></i>
                                                            </a>
                                                            {{-- @endif --}}
                                                            {{-- @else
                                                                <a href="/pesan/createsession"
                                                                    class="btn {{ $sessionApp == 'true' ? 'btn-success' : 'btn-danger' }} btn-sm disabled"
                                                                    data-toggle="tooltip" data-placement="bottom"
                                                                    title="{{ $sessionApp == 'true' ? 'Session App Found' : 'Session App Not Found' }}">
                                                                    <i class="fas fa-ban"></i>
                                                                </a>
                                                            @endif --}}


                                                        </div>
                                                    </div>
                                                @endcan
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @if (!empty($qrCode))
                        <div class="card">
                            <div class="card-header">Scan Barcode</div>
                            <div class="card-body">
                                Disini
                                <img src="{{ $qrCode->qr }}" alt="Silahkan scan no Whatsapp yang dipakai" />
                            </div>
                        </div>
                    @endif

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
