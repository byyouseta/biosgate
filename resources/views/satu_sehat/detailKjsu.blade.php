@extends('layouts.master')

@section('head')
<meta http-equiv="refresh" content="600" />
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
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card_title">Detail KJSU
                            <div class="float-right">
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-default">
                                    <i class="fas fa-paper-plane"></i> Kirim</a>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6>Episode of Care</h6>
                        <table class="table table-bordered table-hover table-sm">
                            <tr>
                                <th width='30%'>No RM</th>
                                <td>: {!! $dataPasien? $dataPasien->no_rkm_medis:'<i class="fas fa-times-circle"
                                        style="color: red;"></i>' !!}</td>
                            </tr>
                            <tr>
                                <th width='30%'>No Rawat</th>
                                <td>: {!! $dataPasien? $dataPasien->no_rawat:'<i class="fas fa-times-circle"
                                        style="color: red;"></i>' !!}</td>
                            </tr>
                            <tr>
                                <th width='30%'>Nama</th>
                                <td>: {!! $dataPasien? $dataPasien->nm_pasien:'<i class="fas fa-times-circle"
                                        style="color: red;"></i>' !!}</td>
                            </tr>
                            <tr>
                                <th width='30%'>Waktu Registrasi</th>
                                <td>: {!! $dataPasien? "$dataPasien->tgl_registrasi $dataPasien->jam_reg":'<i class="fas fa-times-circle"
                                        style="color: red;"></i>' !!}</td>
                            </tr>
                            <tr>
                                <th width='30%'>Diagnosa</th>
                                <td>: {!! $dataPasien? "$dataPasien->kd_penyakit":'<i class="fas fa-times-circle"
                                        style="color: red;"></i>' !!}</td>
                            </tr>
                            <tr>
                                <th width='30%'>Jenis</th>
                                <td>: {!! $dataJenis? "$dataJenis->jenis":'<i class="fas fa-times-circle"
                                        style="color: red;"></i>' !!}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover table-sm mt-3" id="example2">
                            <thead>
                                <tr>
                                    <th class="align-middle">Periode</th>
                                    <th class="align-middle">Status</th>
                                    <th class="align-middle">Waktu Mulai</th>
                                    <th class="align-middle">Waktu Akhir</th>
                                    <th class="align-middle">Created Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dataCare as $list)
                                <tr>
                                    <td>{{ $list->periode }}</td>
                                    <td>{{ $list->status }}</td>
                                    <td>{{ $list->waktu_mulai }}</td>
                                    <td>{{ $list->waktu_selesai }}</td>
                                    <td>{{ $list->created_at }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-6">

            </div>

            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->

    <div class="modal fade" id="modal-default">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form method="POST" action="{{ route('satuSehat.kjsuKirimEoc') }}" >
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">Kirim Episode of Care</h4>
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
                                    <input type="number" class="form-control text-right" name="periode"  id="" value="{{ $dataCare->count() }}">
                                    <input type="hidden" class="form-control" name="jenis"  id="" value="{{ $dataJenis? "$dataJenis->jenis":'' }}">
                                    <input type="hidden" class="form-control" name="no_rawat"  id="" value="{{ $dataPasien? $dataPasien->no_rawat:'' }}">
                                    <input type="hidden" class="form-control" name="no_rm"  id="" value="{{ $dataPasien? $dataPasien->no_rkm_medis:'' }}">
                                </div>
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control" id="" required>
                                        <option value="">Pilih</option>
                                        <option value="waitlist">Waitlist</option>
                                        <option value="active">Active</option>
                                        <option value="finished">Finished</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Waktu Mulai</label>
                                    <input type="text" class="form-control datetimepicker-input w-10" id="tanggal_mulai" data-target-input="nearest"
                                        data-target="#tanggal_mulai" data-toggle="datetimepicker" name="tanggal_mulai"
                                        autocomplete="off" value="{{ "$dataPasien->tgl_registrasi $dataPasien->jam_reg" }}">
                                </div>
                                <div class="form-group">
                                    <label>Waktu Selesai</label>
                                    <input type="text" class="form-control datetimepicker-input w-10" id="tanggal_selesai" data-target-input="nearest"
                                        data-target="#tanggal_selesai" data-toggle="datetimepicker" name="tanggal_selesai"
                                        autocomplete="off" value="{{ "$dataPasien->tgl_registrasi $dataPasien->jam_reg" }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default float-left" data-dismiss="modal">Tutup</button>
                        <button type="Submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Kirim</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
</section>
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
            // $('#example2').DataTable({
            //     "paging": true,
            //     "lengthChange": false,
            //     "searching": true,
            //     "ordering": true,
            //     "info": false,
            //     "autoWidth": false,
            //     "responsive": false,
            //     "scrollY": true,
            //     "scrollX": true,
            // });
            $('#example,#example2,#example3,#example4,#example5').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "order": [[0, 'asc']],
                "info": false,
                "autoWidth": false,
                "responsive": false,
                "scrollY": true,
                "scrollX": true,
            });
        });
        //Date picker
        $('#tanggal_mulai, #tanggal_selesai').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss'
        });
</script>
@endsection
