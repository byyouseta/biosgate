@extends('layouts.master')

@section('head')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('template/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('template/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
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
                            {{-- <h3 class="card-title">{{ session('anak') }}</h3> --}}
                            @can('bank-create')
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-default">
                                    <i class="fa fa-plus-circle"></i> Tambah</a>
                                </button>
                            @endcan
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Kode Bank</th>
                                        <th>Nama Bank</th>
                                        <th>Nama Rekening</th>
                                        <th>No Rekening</th>
                                        <th>Kode Rekening</th>
                                        <th>Default</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $data)
                                        <tr>
                                            <td>{{ $data->kd_bank }}</td>
                                            <td>{{ $data->nama }}</td>
                                            <td>{{ $data->namaRek }}</td>
                                            <td>{{ $data->norek }}</td>
                                            <td>{{ $data->Rekening->uraian }}</td>
                                            <td>{{ $data->default == '1' ? 'Ya' : 'Tidak' }}</td>
                                            <td>
                                                <div class="col text-center">
                                                    <div class="btn-group">
                                                        <a href="/master/bank/edit/{{ Crypt::encrypt($data->id) }}"
                                                            class="btn btn-warning btn-sm @cannot('bank-edit') disabled @endcannot"
                                                            data-toggle="tooltip" data-placement="bottom" title="Edit">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                        <a href="/master/bank/delete/{{ Crypt::encrypt($data->id) }}"
                                                            class="btn btn-danger btn-sm delete-confirm @cannot('bank-delete') disabled @endcannot"
                                                            data-toggle="tooltip" data-placement="bottom" title="Delete">
                                                            <i class="fas fa-ban"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    @php
        if (!empty(Request::get('tanggal'))) {
            $tanggal = Request::get('tanggal');
        } else {
            $tanggal = \Carbon\Carbon::now()->format('Y-m-d');
        }
    @endphp
    <div class="modal fade" id="modal-default">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="/master/bank/store">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">Tambah Bank</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- text input -->
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Nama Bank</label>
                                    <select name="nama" class="form-control select2" required>
                                        @foreach ($bank as $daftarBank)
                                            <option value="{{ $daftarBank->kode }}-{{ $daftarBank->uraian }}">
                                                {{ $daftarBank->kode }}
                                                -
                                                {{ $daftarBank->uraian }}</option>
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
                                        name="norek" value="{{ old('norek') }}" required>
                                    @if ($errors->has('norek'))
                                        <div class="text-danger">
                                            {{ $errors->first('norek') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Nama Rekening</label>
                                    <input type="text" class="form-control" placeholder="Masukkan Nama Rekening"
                                        name="namaRek" value="{{ old('namaRek') }}" required>
                                    @if ($errors->has('namaRek'))
                                        <div class="text-danger">
                                            {{ $errors->first('namaRek') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Bank Cabang</label>
                                    <input type="text" class="form-control" placeholder="Masukkan Cabang Bank"
                                        name="cabang" value="{{ old('cabang') }}" required>
                                    @if ($errors->has('cabang'))
                                        <div class="text-danger">
                                            {{ $errors->first('cabang') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6">

                                <div class="form-group">
                                    <label>No Bilyet</label>
                                    <input type="text" class="form-control" placeholder="Masukkan No Bilyet jika Ada"
                                        name="noBilyet" value="{{ old('noBilyet') }}">
                                    @if ($errors->has('noBilyet'))
                                        <div class="text-danger">
                                            {{ $errors->first('noBilyet') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Kode Rekening</label>
                                    <select name="kd_rek" class="form-control select2">
                                        @foreach ($rekening as $rekening)
                                            <option value="{{ $rekening->kode }}">
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
                                    <label>Default Bank</label>
                                    <select name="default" class="form-control">
                                        <option value="0">Tidak</option>
                                        <option value="1">Ya</option>
                                    </select>
                                    @if ($errors->has('default'))
                                        <div class="text-danger">
                                            {{ $errors->first('default') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group" id="tanggal" data-target-input="nearest">
                                    <label>Tanggal Buka</label>
                                    <input type="text" class="form-control datetimepicker-input"
                                        data-target="#tanggal" data-toggle="datetimepicker" name="tanggal"
                                        autocomplete="off" value="{{ $tanggal }}">
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
    <!-- /.modal -->
@endsection
@section('plugin')
    {{-- <!-- jQuery -->
    <script src="{{ asset('template/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('template/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script> --}}
    <!-- DataTables  & Plugins -->
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
    <!-- Select2 -->
    <script src="{{ asset('template/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{ asset('template/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('template/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <script>
        $(function() {
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": false,
            });
        });
        $(function() {
            //Initialize Select2 Elements
            $('.select2').select2()
        });
        //Date picker
        $('#tanggal').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    </script>
@endsection
