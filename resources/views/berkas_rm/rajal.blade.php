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
                                <div class="col-sm-10 mt-2">
                                    <label>Pasien Rajal/IGD</label>
                                </div>
                                <div class="col-sm-2">
                                    <form action="/berkasrm/rajal" method="GET">
                                        <div class="input-group input-group" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input" id="tanggal"
                                                data-target="#tanggal" data-toggle="datetimepicker" name="tanggal"
                                                autocomplete="off" value="{{ $tanggal }}">

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
                                <table class="table table-bordered table-hover table-sm" id="example2">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">No.RM</th>
                                            <th class="align-middle">No.Rawat</th>
                                            <th class="align-middle">Nama Pasien</th>
                                            {{-- <th class="align-middle">Alamat</th> --}}
                                            <th class="align-middle">Tgl Registrasi</th>
                                            <th class="align-middle">Nama Poli</th>
                                            <th class="align-middle">Dokter</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $dataPasien)
                                            <tr>

                                                <td>
                                                    <div class="btn-group text-center">
                                                        <button type="button"
                                                            class="btn btn-default btn-sm">{{ $dataPasien->no_rkm_medis }}</button>
                                                        <button type="button"
                                                            class="btn btn-default dropdown-toggle dropdown-icon"
                                                            data-toggle="dropdown">
                                                            <span class="sr-only">Toggle Dropdown</span>
                                                        </button>
                                                        <div class="dropdown-menu" role="menu">
                                                            <a class="dropdown-item"
                                                                href="/berkasrm/berkas/{{ Crypt::encrypt($dataPasien->no_rawat) }}/kewajiban"
                                                                target="_blank">Berkas
                                                                Hak/Kewajiban </a>
                                                            @if (App\HakKewajibanPasien::cekHakKewajiban(Crypt::encrypt($dataPasien->no_rawat)) == true)
                                                                <a class="dropdown-item delete-confirm"
                                                                    href="/berkasrm/berkas/{{ Crypt::encrypt($dataPasien->no_rawat) }}/kewajiban/delete">
                                                                    <i class="fas fa-times-circle text-danger"></i>
                                                                    Berkas Hak/
                                                                    Kewajiban
                                                                </a>
                                                            @endif
                                                            <a class="dropdown-item"
                                                                href="/berkasrm/berkas/{{ Crypt::encrypt($dataPasien->no_rawat) }}/generalconsent"
                                                                target="_blank">Berkas
                                                                General Consent </a>
                                                            {{-- <a class="dropdown-item" href="#">Something else
                                                                    here</a>
                                                                <div class="dropdown-divider"></div>
                                                                <a class="dropdown-item" href="#">Separated link</a> --}}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $dataPasien->no_rawat }}</td>
                                                <td>{{ $dataPasien->nm_pasien }},
                                                    {{ $dataPasien->jk == 'L' ? 'Laki-Laki' : 'Perempuan' }}
                                                    {{-- <a href="/vedika/rajal/{{ Crypt::encrypt($data->no_rawat) }}/detail"
                                                            class="btn btn-sm " data-toggle="tooltip"
                                                            data-placement="bottom" title="Detail Informasi"
                                                            target="_blank">
                                                            <span class="badge badge-info"><i
                                                                    class="fas fa-search"></i></span></a> --}}

                                                </td>
                                                <td>{{ $dataPasien->tgl_registrasi }} {{ $dataPasien->jam_reg }}</td>
                                                <td>{{ $dataPasien->nm_poli }}</td>
                                                <td>{{ $dataPasien->nama_dokter }}</td>
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
@endsection
@section('plugin')
    <script src="{{ asset('template/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.2.3/js/dataTables.fixedHeader.min.js"></script>
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
        // $(function() {
        //     $('#example2').DataTable({
        //         "paging": true,
        //         "lengthChange": false,
        //         "searching": true,
        //         "ordering": true,
        //         "info": true,
        //         "autoWidth": false,
        //         "responsive": false,
        //         "scrollY": "500px",
        //         "scrollX": true,
        //         "oLanguage": {
        //             "sSearch": "Cari:"
        //         }
        //     });

        // });
        //Date picker
        $('#tanggal').datetimepicker({
            format: 'YYYY-MM-DD'
        });
        $(document).ready(function() {
            // Setup - add a text input to each footer cell
            $('#example2 thead tr')
                .clone(true)
                .addClass('filters')
                .appendTo('#example2 thead');

            var table = $('#example2').DataTable({
                orderCellsTop: true,
                // paging: true,
                // lengthChange: true,
                // searching: false,
                // ordering: true,
                // info: true,
                // autoWidth: true,
                // responsive: false,
                // scrollY: '500px',
                // scrollX: true,
                // fixedHeader: true,
                initComplete: function() {
                    var api = this.api();

                    // For each column
                    api
                        .columns()
                        .eq(0)
                        .each(function(colIdx) {
                            // Set the header cell to contain the input element
                            var cell = $('.filters th').eq(
                                $(api.column(colIdx).header()).index()
                            );
                            var title = $(cell).text();
                            $(cell).html('<input type="text" placeholder="' + title + '" />');

                            // On every keypress in this input
                            $(
                                    'input',
                                    $('.filters th').eq($(api.column(colIdx).header()).index())
                                )
                                .off('keyup change')
                                .on('change', function(e) {
                                    // Get the search value
                                    $(this).attr('title', $(this).val());
                                    var regexr =
                                        '({search})'; //$(this).parents('th').find('select').val();

                                    var cursorPosition = this.selectionStart;
                                    // Search the column for that value
                                    api
                                        .column(colIdx)
                                        .search(
                                            this.value != '' ?
                                            regexr.replace('{search}', '(((' + this.value +
                                                ')))') :
                                            '',
                                            this.value != '',
                                            this.value == ''
                                        )
                                        .draw();
                                })
                                .on('keyup', function(e) {
                                    e.stopPropagation();

                                    $(this).trigger('change');
                                    $(this)
                                        .focus()[0]
                                        .setSelectionRange(cursorPosition, cursorPosition);
                                });
                        });
                },
            });
        });
    </script>
@endsection
