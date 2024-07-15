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
                @php
                    if (!empty(Request::get('tanggal'))) {
                        $tanggal = Request::get('tanggal');
                    } else {
                        $tanggal = \Carbon\Carbon::now()->format('Y-m-d');
                    }
                @endphp
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="form-group row">
                                <div class="col-sm-5 mt-2">
                                    <div class="btn-group">
                                        <a href="/vedika/klaimcompare/template" class="btn btn-sm  btn-default"><i
                                                class="fas fa-file-download"></i> Download
                                            Template </a>
                                        <button class="btn btn-success btn-sm " data-toggle="modal"
                                            data-target="#modal-import"
                                            @cannot('vedika-klaim-compare') disabled @endcannot>
                                            <i class="fas fa-file-upload"></i> Import</a>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-sm-4">

                                </div>
                                <div class="col-sm-3">
                                    <form action="/vedika/klaimcompare" method="GET">
                                        <div class="input-group input-group" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input"
                                                id="tanggalMulai" data-target="#tanggalMulai" data-toggle="datetimepicker"
                                                name="tanggal" autocomplete="off" value="{{ $tanggal }}">
                                            <span class="input-group-append">
                                                <button type="submit" class="btn btn-info btn-flat btn-sm"><i
                                                        class="fas fa-search"></i> Tampilkan</button>
                                            </span>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div>
                                <table class="table table-bordered table-sm" id="example" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">No.SEP</th>
                                            <th class="align-middle">No.Rawat</th>
                                            <th class="align-middle">Nama Pasien</th>
                                            <th class="align-middle">Tgl Registrasi</th>
                                            <th class="align-middle">Tgl Keluar</th>
                                            <th class="align-middle">DPJP</th>
                                            <th class="align-middle">Diagnosa</th>
                                            <th class="align-middle">Biil RS</th>
                                            <th class="align-middle">Cair</th>
                                            <th class="align-middle">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $data)
                                            <tr>
                                                @php
                                                    if ($data->no_sep) {
                                                        $no_sep = $data->no_sep;
                                                    } elseif (!empty(\App\Vedika::getHapusSep($data->no_rawat))) {
                                                        $no_sep = \App\Vedika::getHapusSep($data->no_rawat)->no_sep;
                                                    } else {
                                                        $no_sep = '-';
                                                    }
                                                @endphp
                                                <td>{{ $no_sep }} </td>
                                                <td>{{ $data->no_rawat }}</td>
                                                <td>{{ $data->nm_pasien }}, {{ $data->umurdaftar }}
                                                    {{ $data->sttsumur }},
                                                    {{ $data->jk == 'L' ? 'Laki-Laki' : 'Perempuan' }}
                                                    @if ($data->status_lanjut == 'Ralan')
                                                        <a href="/vedika/rajal/{{ Crypt::encrypt($data->no_rawat) }}/detail"
                                                            class="btn btn-sm " data-toggle="tooltip"
                                                            data-placement="bottom" title="Detail Informasi"
                                                            target="_blank">
                                                            <span class="badge badge-info"><i
                                                                    class="fas fa-search"></i></span></a>
                                                    @elseif ($data->status_lanjut == 'Ranap')
                                                        <a href="/vedika/ranap/{{ Crypt::encrypt($data->no_rawat) }}/detail"
                                                            class="btn btn-sm " data-toggle="tooltip"
                                                            data-placement="bottom" title="Detail Informasi"
                                                            target="_blank">
                                                            <span class="badge badge-info"><i
                                                                    class="fas fa-search"></i></span></a>
                                                    @endif

                                                </td>
                                                <td>{{ $data->tgl_registrasi }} {{ $data->jam_reg }}</td>
                                                <td>
                                                    @if ($data->status_lanjut == 'Ralan')
                                                        {{ $data->tgl_registrasi }}
                                                    @elseif($data->status_lanjut == 'Ranap')
                                                        {{ \App\Vedika::getWaktuKeluar($data->no_rawat) }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($data->status_lanjut == 'Ralan')
                                                        {{ $data->nm_dokter }}
                                                    @elseif($data->status_lanjut == 'Ranap')
                                                        {{ \App\Vedika::getDpjp($data->no_rawat) }}
                                                    @endif
                                                </td>
                                                @php
                                                    $bill = \App\KlaimCair::getBill($data->no_rawat);
                                                    if ($no_sep) {
                                                        $cair = \App\KlaimCair::getCair($no_sep);
                                                    }

                                                    $diag = \App\Vedika::getDiagnosaAll(
                                                        $data->no_rawat,
                                                        $data->status_lanjut
                                                    );
                                                @endphp
                                                <td>
                                                    @if ($diag)
                                                        @foreach ($diag as $listDiag)
                                                            {{ $listDiag }},
                                                        @endforeach
                                                    @endif
                                                </td>
                                                <td class="text-right">{{ $bill }}</td>
                                                <td class="text-right">
                                                    @if ($cair > $bill)
                                                        <span class="badge badge-success"><i
                                                                class="fas fa-arrow-up"></i></span>
                                                    @elseif($cair < $bill && $cair != null)
                                                        <span class="badge badge-danger"><i
                                                                class="fas fa-arrow-down"></i></span>
                                                    @endif
                                                    {{ $cair ? $cair : '-' }}
                                                </td>
                                                <td>{{ $data->status_lanjut }} </td>
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
    </section>
    <div class="modal fade" id="modal-import">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="/vedika/klaimcompare/import" enctype="multipart/form-data">
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
    {{-- <script src="https://cdn.datatables.net/fixedheader/3.2.3/js/dataTables.fixedHeader.min.js"></script> --}}
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
            $('#example').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "scrollY": true,
                "scrollX": true,
                "buttons": ["excel", "pdf", "print", "pageLength"]
            }).buttons().container().appendTo('#example_wrapper .col-md-6:eq(0)');

        });
        //Date picker
        $('#tanggalMulai,#tanggalSelesai').datetimepicker({
            format: 'YYYY-MM-DD'
        });
        $(function() {
            bsCustomFileInput.init();
        });
        // $(document).ready(function() {
        //     // Setup - add a text input to each footer cell
        //     $('#example2 thead tr')
        //         .clone(true)
        //         .addClass('filters')
        //         .appendTo('#example2 thead');

        //     var table = $('#example2').DataTable({
        //         orderCellsTop: true,
        //         // paging: true,
        //         // lengthChange: true,
        //         // searching: false,
        //         // ordering: true,
        //         // info: true,
        //         // autoWidth: true,
        //         // responsive: false,
        //         // scrollY: '500px',
        //         scrollX: true,
        //         // fixedHeader: true,
        //         initComplete: function() {
        //             var api = this.api();

        //             // For each column
        //             api
        //                 .columns()
        //                 .eq(0)
        //                 .each(function(colIdx) {
        //                     // Set the header cell to contain the input element
        //                     var cell = $('.filters th').eq(
        //                         $(api.column(colIdx).header()).index()
        //                     );
        //                     var title = $(cell).text();
        //                     $(cell).html('<input type="text" placeholder="' + title + '" />');

        //                     // On every keypress in this input
        //                     $(
        //                             'input',
        //                             $('.filters th').eq($(api.column(colIdx).header()).index())
        //                         )
        //                         .off('keyup change')
        //                         .on('change', function(e) {
        //                             // Get the search value
        //                             $(this).attr('title', $(this).val());
        //                             var regexr =
        //                                 '({search})'; //$(this).parents('th').find('select').val();

        //                             var cursorPosition = this.selectionStart;
        //                             // Search the column for that value
        //                             api
        //                                 .column(colIdx)
        //                                 .search(
        //                                     this.value != '' ?
        //                                     regexr.replace('{search}', '(((' + this.value +
        //                                         ')))') :
        //                                     '',
        //                                     this.value != '',
        //                                     this.value == ''
        //                                 )
        //                                 .draw();
        //                         })
        //                         .on('keyup', function(e) {
        //                             e.stopPropagation();

        //                             $(this).trigger('change');
        //                             $(this)
        //                                 .focus()[0]
        //                                 .setSelectionRange(cursorPosition, cursorPosition);
        //                         });
        //                 });
        //         },
        //     });
        // });
    </script>
@endsection
