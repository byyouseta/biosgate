@extends('layouts.master')

@section('head')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('template/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- Tempusdominus|Datetime Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('template/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('template/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        @if (Request::get('tanggal'))
                            @php
                                $tanggal = Request::get('tanggal');
                            @endphp
                        @else
                            @php
                                $tanggal = \Carbon\Carbon::now()
                                    ->locale('id')
                                    ->format('Y-m-d');
                            @endphp
                        @endif
                        @php
                            $kemarin = \Carbon\Carbon::parse($tanggal)
                                ->yesterday()
                                ->format('Y-m-d');
                        @endphp
                        <div class="card-body">
                            <form action="/saldo/operasional/lihat" method="GET">
                                <div class="form-group row">
                                    <div class="col-sm-1 col-form-label">
                                        <label>Tanggal</label>
                                    </div>
                                    <div class="col-sm-2 col-form-label">
                                        <div class="input-group date" id="tanggal" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input "
                                                data-target="#tanggal" data-toggle="datetimepicker" name="tanggal"
                                                value="{{ $tanggal }}" autocomplete="off" />
                                            <div class="input-group-append" data-target="#tanggal"
                                                data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-1 col-form-label">
                                        <button type="Submit" class="btn btn-primary btn-block">Lihat</button>
                                    </div>

                                    <div class="col-sm-2 col-form-label">
                                        <a href="/saldokeuangan/client"
                                            class="btn btn-success @cannot('bios-saldo-client') disabled @endcannot"
                                            target="_blank" >Jalankan
                                            Client</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <button class="btn btn-primary btn-sm "
                                data-toggle="modal" data-target="#modal-default" @cannot('bios-saldo-create') disabled @endcannot>
                                <i class="fa fa-plus-circle"></i> Tambah</a>
                            </button>
                        </div>
                        <div class="card-body">
                            {{-- <div style="overflow-x:auto;"> --}}
                            <table class="table table-bordered table-hover" id="example">
                                <thead>
                                    <tr>
                                        <th class="align-middle">Kode Bank</th>
                                        <th class="align-middle">Nama Bank</th>
                                        <th class="align-middle">No Rekening</th>
                                        <th class="align-middle">Saldo</th>
                                        <th class="align-middle">Kode Rekening</th>
                                        <th class="align-middle">Tanggal Transaksi</th>
                                        <th class="align-middle">Status</th>
                                        <th class="align-middle">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $data)
                                        <tr>
                                            <td>{{ $data->bank->kd_bank }}</td>
                                            <td>{{ $data->bank->nama }}</td>
                                            <td>{{ $data->bank->norek }}</td>
                                            <td class="text-right">{{ number_format($data->saldo_akhir, 2, ',', '.') }}
                                            </td>
                                            <td>{{ $data->bank->Rekening->uraian }}</td>
                                            <td class="text-center">{{ $data->tgl_transaksi }}</td>
                                            <td>
                                                @if ($data->status == 1)
                                                    <span class="right badge badge-success">Sudah Terkirim</span>
                                                @else
                                                    <span class="right badge badge-danger">Belum Terkirim</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="col text-center">
                                                    <div class="btn-group">
                                                        <a href="/saldo/operasional/{{ Crypt::encrypt($data->id) }}/edit"
                                                            class="btn btn-warning btn-sm "
                                                            data-toggle="tooltip" data-placement="bottom" title="Edit" @cannot('bios-saldo-edit') disabled @endcannot>
                                                            <i class="fas fa-pen"></i>
                                                        </a>
                                                        <a href="/saldo/operasional/{{ Crypt::encrypt($data->id) }}/delete"
                                                            class="btn btn-danger btn-sm delete-confirm "
                                                            data-toggle="tooltip" data-placement="bottom" title="Delete" @if ($data->status == 1) disabled @endif @cannot('bios-saldo-delete') disabled @endcannot>
                                                            <i class="fas fa-ban"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{-- </div> --}}
                        </div>
                    </div>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
            {{-- </div> --}}
            <!-- /.container-fluid -->
    </section>
    {{-- Modal Add --}}
    <div class="modal fade" id="modal-default">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="/saldo/operasional/store">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">Tambah Data</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- text input -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Tgl Transaksi</label>
                                    <div class="input-group date" id="tanggal_transaksi" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input "
                                            data-target="#tanggal_transaksi" data-toggle="datetimepicker"
                                            name="tanggal_transaksi" value="{{ $kemarin }}" autocomplete="off" />
                                        <div class="input-group-append" data-target="#tanggal_transaksi"
                                            data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Bank</label>
                                    <select name="bank" class="form-control select2">
                                        @foreach ($bank as $bank)
                                            <option value="{{ $bank->id }}">
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
                                        <input type="number" class="form-control" name="saldo" required>
                                    </div>
                                    @if ($errors->has('saldo'))
                                        <div class="text-danger">
                                            {{ $errors->first('saldo') }}
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
    <!-- Select2 -->
    <script src="{{ asset('template/plugins/select2/js/select2.full.min.js') }}"></script>
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
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "order": [
                    [5, 'desc']
                ],
                "info": false,
                "autoWidth": false,
                "responsive": false,
                "scrollY": "auto",
            });
            //Initialize Select2 Elements
            $('.select2').select2()
        });
        //Date picker
        $('#tanggal').datetimepicker({
            format: 'YYYY-MM'
        });
        $('#tanggal_transaksi').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    </script>
@endsection
