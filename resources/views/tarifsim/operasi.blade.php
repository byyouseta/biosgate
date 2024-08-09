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
                                <a href="/tarifsimrs/operasi/exportexcel" class="btn btn-success btn-sm"
                                    target="_blank">EXPORT
                                    EXCEL</a>
                                <a href="/tarifsimrs/operasi/template" class="btn btn-secondary btn-sm"
                                    target="_blank">TEMPLATE
                                    EXCEL</a>
                                <button href="/tarifsimrs/operasi/importexcel" class="btn btn-info btn-sm"
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
                                        <th class="align-middle">Kode Paket</th>
                                        <th class="align-middle">Nama Operasi</th>
                                        <th class="align-middle">Kategori</th>
                                        <th class="align-middle">Operator 1</th>
                                        <th class="align-middle">Operator 2</th>
                                        <th class="align-middle">Operator 3</th>
                                        <th class="align-middle">Asisten Op 1</th>
                                        <th class="align-middle">Asisten Op 2</th>
                                        <th class="align-middle">Asisten Op 3</th>
                                        <th class="align-middle">Instrumen</th>
                                        <th class="align-middle">dr Anestesi</th>
                                        <th class="align-middle">Asisten Anes 1</th>
                                        <th class="align-middle">Asisten Anes 2</th>
                                        <th class="align-middle">dr Anak</th>
                                        <th class="align-middle">Perawat Resus</th>
                                        <th class="align-middle">Bidan 1</th>
                                        <th class="align-middle">Bidan 2</th>
                                        <th class="align-middle">Bidan 3</th>
                                        <th class="align-middle">Perawat Luar</th>
                                        <th class="align-middle">Alat</th>
                                        <th class="align-middle">Sewa OK/VK</th>
                                        <th class="align-middle">Akomodasi</th>
                                        <th class="align-middle">N.M.S</th>
                                        <th class="align-middle">Onloop 1</th>
                                        <th class="align-middle">Onloop 2</th>
                                        <th class="align-middle">Onloop 3</th>
                                        <th class="align-middle">Onloop 4</th>
                                        <th class="align-middle">Onloop 5</th>
                                        <th class="align-middle">Sarpras</th>
                                        <th class="align-middle">dr PJ Anak</th>
                                        <th class="align-middle">dr Umum</th>
                                        <th class="align-middle">Total</th>
                                        <th class="align-middle">Jenis Bayar</th>
                                        <th class="align-middle">Kelas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $summary)
                                    <tr>
                                        <td>{{ $summary->kode_paket }}</td>
                                        <td>{{ $summary->nm_perawatan }}</td>
                                        <td>{{ $summary->kategori }}</td>
                                        <td class="text-right">{{ $summary->operator1 }}</td>
                                        <td class="text-right">{{ $summary->operator2 }}</td>
                                        <td class="text-right">{{ $summary->operator3 }}</td>
                                        <td class="text-right">{{ $summary->asisten_operator1 }}</td>
                                        <td class="text-right">{{ $summary->asisten_operator2 }}</td>
                                        <td class="text-right">{{ $summary->asisten_operator3 }}</td>
                                        <td class="text-right">{{ $summary->instrumen }}</td>
                                        <td class="text-right">{{ $summary->dokter_anak }}</td>
                                        <td class="text-right">{{ $summary->perawaat_resusitas }}</td>
                                        <td class="text-right">{{ $summary->dokter_anestesi }}</td>
                                        <td class="text-right">{{ $summary->asisten_anestesi }}</td>
                                        <td class="text-right">{{ $summary->asisten_anestesi2 }}</td>
                                        <td class="text-right">{{ $summary->bidan }}</td>
                                        <td class="text-right">{{ $summary->bidan2 }}</td>
                                        <td class="text-right">{{ $summary->bidan3}}</td>
                                        <td class="text-right">{{ $summary->perawat_luar}}</td>
                                        <td class="text-right">{{ $summary->sewa_ok}}</td>
                                        <td class="text-right">{{ $summary->alat}}</td>
                                        <td class="text-right">{{ $summary->akomodasi}}</td>
                                        <td class="text-right">{{ $summary->bagian_rs}}</td>
                                        <td class="text-right">{{ $summary->omloop}}</td>
                                        <td class="text-right">{{ $summary->omloop2}}</td>
                                        <td class="text-right">{{ $summary->omloop3}}</td>
                                        <td class="text-right">{{ $summary->omloop4}}</td>
                                        <td class="text-right">{{ $summary->omloop5}}</td>
                                        <td class="text-right">{{ $summary->sarpras}}</td>
                                        <td class="text-right">{{ $summary->dokter_pjanak}}</td>
                                        <td class="text-right">{{ $summary->dokter_umum}}</td>
                                        <td class="text-right">{{ $summary->jml_tarif }}</td>
                                        <td>{{ $summary->png_jawab }}</td>
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
                <form method="POST" action="/tarifsimrs/operasi/importexcel" class="form-prevent" enctype="multipart/form-data">
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
