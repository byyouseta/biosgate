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
                    if (!empty(Request::get('tanggalMulai')) && !empty(Request::get('tanggalSelesai'))) {
                        $tanggalMulai = Request::get('tanggalMulai');
                        $tanggalSelesai = Request::get('tanggalSelesai');
                    } else {
                        $tanggalMulai = \Carbon\Carbon::now()->format('Y-m-d');
                        $tanggalSelesai = \Carbon\Carbon::now()->format('Y-m-d');
                    }
                @endphp
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            {{-- <div class="card_title">Data Pasien Covid</div> --}}
                            {{-- <div class="float-right"> --}}
                            <div class="form-group row">
                                <div class="col-sm-9 mt-2">
                                    <label>Data Pasien Booking Operasi</label>
                                </div>
                                <div class="col-sm-3">
                                    <form action="/operasi/booking" method="GET">
                                        <div class="input-group input-group" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input"
                                                id="tanggalMulai" data-target="#tanggalMulai" data-toggle="datetimepicker"
                                                name="tanggalMulai" autocomplete="off" value="{{ $tanggalMulai }}">
                                            <input type="text" class="form-control datetimepicker-input"
                                                id='tanggalSelesai' data-target="#tanggalSelesai"
                                                data-toggle="datetimepicker" name="tanggalSelesai" autocomplete="off"
                                                value="{{ $tanggalSelesai }}">
                                            <span class="input-group-append">
                                                <button type="submit" class="btn btn-info btn-flat btn-sm"><i
                                                        class="fas fa-search"></i> Tampilkan</button>
                                            </span>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            {{-- </div> --}}
                        </div>
                        <div class="card-body">
                            <div>
                                <table class="table table-bordered table-hover table-sm" id="example2">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">No.RM</th>
                                            <th class="align-middle">No.Rawat</th>
                                            <th class="align-middle">Nama Pasien</th>
                                            <th class="align-middle">Nama Perawatan</th>
                                            <th class="align-middle">Kelas</th>
                                            <th class="align-middle">Biaya</th>
                                            <th class="align-middle">Tanggal Booking</th>
                                            <th class="align-middle">Status</th>
                                            <th class="align-middle">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $dataOperasi)
                                            <tr>
                                                <td class="align-middle">{{ $dataOperasi->no_rkm_medis }}</td>
                                                <td class="align-middle">{{ $dataOperasi->no_rawat }}</td>
                                                <td class="align-middle">{{ $dataOperasi->nm_pasien }}</td>
                                                <td class="align-middle">{{ $dataOperasi->nm_perawatan }}</td>
                                                <td class="align-middle">{{ $dataOperasi->kelas }}</td>
                                                <td class="align-middle">{{ $dataOperasi->biaya }}</td>
                                                <td class="align-middle">{{ $dataOperasi->tanggal }}</td>
                                                <td class="align-middle">{{ $dataOperasi->status }}</td>
                                                <td>

                                                    <a href="/operasi/{{ Crypt::encrypt($dataOperasi->id_booking) }}/booking"
                                                        class="btn btn-success btn-sm {{ $dataOperasi->status == 'Booking' ? 'disabled' : '' }} @cannot('operasi-booking-create')
                                                            disabled
                                                        @endcannot"
                                                        data-toggle="tooltip" data-placement="bottom" title="Daftar">
                                                        <i class="fas fa-paper-plane"></i>
                                                    </a>

                                                </td>
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
        $(function() {
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": false,
                "scrollY": "Auto",
                "scrollX": true,
                "oLanguage": {
                    "sSearch": "Cari:"
                }
            });

        });
        //Date picker
        $('#tanggalMulai,#tanggalSelesai').datetimepicker({
            format: 'YYYY-MM-DD'
        });
        // $(document).ready(function() {
        //     // Setup - add a text input to each footer cell
        //     $('#example2 thead tr')
        //         .clone(true)
        //         .addClass('filters')
        //         .appendTo('#example2 thead');

        //     var table = $('#example2').DataTable({
        //         orderCellsTop: true,
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
