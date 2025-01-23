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
                            <form action="/vedika/pendingdpjp" method="GET">
                                <div class="form-group row">
                                    <div class="col-sm-1 col-form-label">
                                        <label>Tanggal</label>
                                    </div>
                                    <div class="col-sm-2 col-form-label">
                                        <div class="input-group date" id="tanggal" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input"
                                                data-target="#tanggal" data-toggle="datetimepicker" name="tanggal"
                                                value="{{ $tanggal }}" autocomplete="off" />
                                            <div class="input-group-append" data-target="#tanggal"
                                                data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2 col-form-label">
                                        <button type="submit" class="btn btn-primary">Cari</button>
                                    </div>
                            </form>
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
                    title: 'Data Tindakan Pending DPJP ' + periode,
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
    </script>
@endsection
