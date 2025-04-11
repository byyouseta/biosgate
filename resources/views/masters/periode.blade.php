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
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Tambah Periode Klaim
                            </div>
                            <div class="float-right">
                                @can('vedika-upload')
                                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-default">
                                        <i class="fas fa-plus-circle"></i> Tambah</a>
                                    </button>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">

                            <table class="table table-bordered table-hover table-sm">
                                <thead class="text-center">
                                    <tr>
                                        <th style="width: 5%">No</th>
                                        <th>Periode</th>
                                        <th>Keterangan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data as $index => $dataPeriode)
                                        <tr>
                                            <td class="text-center">{{ ++$index }}</td>
                                            <td>{{ \Carbon\Carbon::parse($dataPeriode->periode)->format('F Y') }}</td>
                                            <td>{{ $dataPeriode->keterangan }}</td>
                                            <td>{{ $dataPeriode->status == '0' ? 'Open' : 'Close' }}</td>
                                            <td>
                                                <div class="col text-center">
                                                    <div class="btn-group">
                                                        <a href="/master/vedika/klaim/{{ Crypt::encrypt($dataPeriode->id) }}/edit"
                                                            class="btn btn-warning btn-sm" data-toggle="tooltip"
                                                            data-placement="bottom" title="Edit Periode">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                        <a href="/master/vedika/klaim/{{ Crypt::encrypt($dataPeriode->id) }}/delete"
                                                            class="btn btn-danger btn-sm delete-confirm @cannot('vedika-delete') disabled @endcannot"
                                                            data-toggle="tooltip" data-placement="bottom" title="Delete">
                                                            <i class="fas fa-ban"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="5">Belum ada periode yang ditambahkan</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>

    <div class="modal fade" id="modal-default">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="/master/vedika/klaim">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">Tambah Periode</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- text input -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Periode</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control datetimepicker-input" id="tanggal"
                                            data-target="#tanggal" data-toggle="datetimepicker" name="periode"
                                            autocomplete="off" placeholder="Tanggal periode" required>
                                        <span class="input-group-append">
                                            <button type="button" class="btn btn-default btn-flat btn-sm"
                                                data-target="#tanggal" data-toggle="datetimepicker"><i
                                                    class="far fa-calendar-plus"></i></button>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Keterangan</label>
                                    <textarea class="form-control" name="keterangan" placeholder="Silahkan diisi jika ada"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control" required>
                                        <option value="0">Open</option>
                                        <option value="1">Close</option>
                                    </select>
                                    @if ($errors->has('status'))
                                        <div class="text-danger">
                                            {{ $errors->first('status') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default float-left" data-dismiss="modal">Tutup</button>
                        <button type="Submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
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
    <!-- bs-custom-file-input -->
    <script src="{{ asset('template/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <script>
        $(function() {
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": false,
                "scrollY": "400px",
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
            });
        });
        //Date picker
        $('#tanggal').datetimepicker({
            format: 'YYYY-MM-01'
        });
        $(function() {
            bsCustomFileInput.init();
            //Date picker
            $('#tanggal').datetimepicker({
                format: 'DD-MM-YYYY'
            });
        });
    </script>
@endsection
