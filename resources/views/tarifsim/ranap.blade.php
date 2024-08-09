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
                        <div class="card_title">{{ Session::get('anak') }}
                            <div class="float-right">
                                <a href="/tarifsimrs/ranap/exportexcel" class="btn btn-success btn-sm"
                                    target="_blank">EXPORT
                                    EXCEL</a>
                                <a href="/tarifsimrs/ranap/template" class="btn btn-secondary btn-sm"
                                    target="_blank">TEMPLATE
                                    EXCEL</a>
                                <button href="/tarifsimrs/ranap/importexcel" class="btn btn-info btn-sm"
                                    data-toggle="modal" data-target="#modal-import-excel">IMPORT
                                    EXCEL</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="overflow-x:auto;">
                            <table class="table table-bordered table-hover table-sm display nowrap" id="example"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="align-middle">Kode Tindakan</th>
                                        <th class="align-middle">Nama Tindakan</th>
                                        <th class="align-middle">Kategori</th>
                                        <th class="align-middle">Jasa RS</th>
                                        <th class="align-middle">BHP/Paket Obat</th>
                                        <th class="align-middle">Js Medis Dr</th>
                                        <th class="align-middle">Js Medis Pr</th>
                                        <th class="align-middle">KSO</th>
                                        <th class="align-middle">Menejemen</th>
                                        <th class="align-middle">Ttl Biaya Dr</th>
                                        <th class="align-middle">Ttl Biaya Pr</th>
                                        <th class="align-middle">Ttl Biaya Dr&Pr</th>
                                        <th class="align-middle">Jenis Bayar</th>
                                        <th class="align-middle">Kamar</th>
                                        <th class="align-middle">Kelas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $summary)
                                    <tr>
                                        <td>{{ $summary->kd_jenis_prw }}</td>
                                        <td>{{ $summary->nm_perawatan }}</td>
                                        <td>{{ $summary->nm_kategori }}</td>
                                        <td class="text-right">{{ $summary->material }}</td>
                                        <td class="text-right">{{ $summary->bhp }}</td>
                                        <td class="text-right">{{ $summary->tarif_tindakandr }}</td>
                                        <td class="text-right">{{ $summary->tarif_tindakanpr }}</td>
                                        <td class="text-right">{{ $summary->kso }}</td>
                                        <td class="text-right">{{ $summary->menejemen }}</td>
                                        <td class="text-right">{{ $summary->total_byrdr }}</td>
                                        <td class="text-right">{{ $summary->total_byrpr }}</td>
                                        <td class="text-right">{{ $summary->total_byrdrpr }}</td>
                                        <td>{{ $summary->png_jawab }}</td>
                                        <td>{{ $summary->nm_bangsal }}</td>
                                        <td>{{ $summary->kelas }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
    <div class="modal fade" id="modal-import-excel" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="/tarifsimrs/ranap/importexcel" class="form-prevent" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">Import Tarif Rajal</h4>
                        {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button> --}}
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- text input -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="exampleInputFile">File input</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="customFile" name="file">
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
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default button-tutup" data-dismiss="modal">Tutup</button>
                        {{-- <button type="Submit" class="btn btn-primary">Proses</button> --}}
                        <button class="btn btn-success button-prevent" type="submit">
                            <!-- spinner-border adalah component bawaan bootstrap untuk menampilakn roda berputar  -->
                            <div class="spinner" style="display:none;"><i role="status"
                                    class="spinner-border spinner-border-sm"></i>
                                Proses </div>
                            <div class="hide-text">Proses</div>
                        </button>
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
<!-- bs-custom-file-input -->
<script src="{{ asset('template/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
<script>
    $(function() {
            // $('#example2').DataTable({
            //     "paging": true,
            //     "lengthChange": false,
            //     "searching": true,
            //     "ordering": true,
            //     "order": [
            //         [5, 'desc']
            //     ],
            //     "info": true,
            //     "autoWidth": false,
            //     "responsive": false,
            //     "scrollY": false,
            //     "scrollX": true,
            //     "buttons": ["copy", "excel", "pdf", "colvis"]
            // }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
            $('#example').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": false,
                "info": true,
                "autoWidth": true,
                "fixedHeader": true,
                "responsive": false,
                // "scrollY": "300px",
                "scrollX": false,
            });
        });
        (function() {
            $('.form-prevent').on('submit', function() {
                $('.button-prevent').attr('disabled', 'true');
                $('.spinner').show();
                $('.hide-text').hide();
                $('.button-tutup').hide();
                $('#modal-default').modal({
                    backdrop: 'static',
                    keyboard: false
                })
            })
        })();
        $(function() {
            bsCustomFileInput.init();
        });
</script>
@endsection
