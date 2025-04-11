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
                        @if (Request::get('tanggal'))
                            @php
                                $tanggal = Request::get('tanggal');
                            @endphp
                        @else
                            @php
                                $tanggal = \Carbon\Carbon::now()->locale('id')->format('Y-m');
                            @endphp
                        @endif
                        <div class="card-header">
                            <div class="row">
                                <div class="col-sm-5">
                                    <form action="/vedika/tidaklayak" method="GET" class="form-inline d-flex align-items-center">
                                        <label class="mr-2">Tanggal</label>
                                        <div class="input-group date mr-2" id="tanggal" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input" data-target="#tanggal"
                                                data-toggle="datetimepicker" name="tanggal" value="{{ $tanggal }}" autocomplete="off" />
                                            <div class="input-group-append" data-target="#tanggal" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Cari</button>
                                    </form>
                                </div>
                                <div class="col-sm-7">
                                    <div class="btn-group float-right">
                                        <a href="/vedika/tidaklayak/template" class="btn btn-sm btn-default"><i
                                                class="fas fa-file-download"></i> Download
                                            Template </a>
                                        <button class="btn btn-success btn-sm " data-toggle="modal"
                                            data-target="#modal-import">
                                            <i class="fas fa-file-upload"></i> Import</a>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            {{-- <div style="overflow-x:auto;"> --}}
                            <table class="table table-bordered table-hover table-sm" id="example" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>Nama Pasien</th>
                                        <th>No SEP</th>
                                        <th>No Rawat</th>
                                        <th>Jenis Rawat</th>
                                        <th>Tanggal SEP</th>
                                        <th>Tanggal Pulang</th>
                                        <th>Nama DPJP</th>
                                        <th>Pengajuan</th>
                                        <th>Tarif INACBG</th>
                                        <th>Tarif RS</th>
                                        <th>Alasan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $index => $klaim)
                                        <tr>
                                            <td>{{ $klaim->nama_pasien }}</td>
                                            <td>{{ $klaim->no_sep }}</td>
                                            <td>{{ $klaim->no_rawat }}</td>
                                            <td>{{ $klaim->jenis_rawat }}</td>
                                            <td>{{ $klaim->tgl_sep }}</td>
                                            <td>{{ $klaim->tgl_pulang }}</td>
                                            <td>{{ $klaim->dpjp }}</td>
                                            <td class="text-right">{{ $klaim->biaya_pengajuan }}</td>
                                            <td class="text-right">{{ $klaim->biaya_tarif_grouper }}</td>
                                            <td class="text-right">{{ $klaim->biaya_tarif_rs }}</td>
                                            <td>{{ $klaim->alasan? $klaim->alasan:'-' }}</td>
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
        </div>
        <!-- /.container-fluid -->
    </section>

    <div class="modal fade" id="modal-import">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="/vedika/tidaklayak/import" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">Import Data Penerimaan</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- text input -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="exampleInputFile">File input</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="customFile"
                                                name="file">
                                            <label class="custom-file-label" for="customFile">Choose file</label>
                                        </div>

                                    </div>
                                    <div>
                                        <small><i>* File berformat xls/xlsx</i></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                        <button type="Submit" class="btn btn-primary">Upload</button>
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
        let periode = "<?php echo $tanggal; ?>";
        $(function() {
            $('#example').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": false,
                "scrollY": "500px",
                "scrollX": true,
                "oLanguage": {
                    "sSearch": "Cari:"
                },
                "dom": 'Bfrtip', // Mengaktifkan tombol pada posisi tertentu, 'Bfrtip' adalah pilihan default untuk di atas tabel
                "buttons": [{
                    extend: 'excelHtml5',
                    text: 'Export Excel',
                    title: 'Data Tidak Layak DPJP ' + periode,
                    exportOptions: {
                        columns: ':visible' // Ekspor semua kolom yang terlihat
                    }
                }]
            });
        });
        // $(function() {
        //     $('#example').DataTable({
        //         "paging": true,
        //         "lengthChange": false,
        //         "searching": true,
        //         "ordering": true,
        //         "order": [
        //             [2, 'desc']
        //         ],
        //         "info": true,
        //         "autoWidth": false,
        //         "responsive": false,
        //         "scrollY": "Auto",
        //         "scrollX": true,
        //         "fixedHeader": true
        //         "dom": 'Bfrtip', // Mengaktifkan tombol pada posisi tertentu, 'Bfrtip' adalah pilihan default untuk di atas tabel
        //         "buttons": [{
        //             extend: 'excelHtml5',
        //             text: 'Export Excel',
        //             title: 'Data Tindakan Pending ' + periode,
        //             exportOptions: {
        //                 columns: ':visible' // Ekspor semua kolom yang terlihat
        //             }
        //         }],
        //     });
        // });
        //Date picker
        $('#tanggal').datetimepicker({
            format: 'YYYY-MM'
        });
        $(function() {
            bsCustomFileInput.init();
        });
    </script>
@endsection
