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
                            <div class="form-group row">
                                <div class="col-sm-6 mt-2">
                                    <label>Data Pasien Rajal/IGD</label>
                                </div>
                                <div class="col-sm-3">
                                    <div class="float-right">
                                        @if (!empty($dataPengajuan))
                                            <button class="btn btn-primary btn-sm" data-toggle="modal"
                                                data-target="#modal-berkas-klaim">
                                                <i class="fas fa-file-download"></i> Download
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <form action="/vedika/pengajuan/rajal" method="GET">
                                        <div class="input-group">
                                            <select name="periode" class="form-control" required>
                                                <option value="">Pilih Periode</option>
                                                @foreach ($dataPeriode as $periode)
                                                    <option value="{{ $periode->id }}"
                                                        {{ $periode->id == Request::get('periode') ? 'selected' : '' }}>
                                                        {{ \Carbon\Carbon::parse($periode->periode)->format('F Y') }}
                                                    </option>
                                                @endforeach
                                            </select>
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
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover table-sm" id="example2">
                                    <thead>
                                        <tr>
                                            {{-- <th class="align-middle">No.RM</th> --}}
                                            <th class="align-middle">No.SEP</th>
                                            <th class="align-middle">No.Kartu</th>
                                            <th class="align-middle">Nama Pasien</th>
                                            <th class="align-middle">Tgl Registrasi</th>
                                            <th class="align-middle">Nama Poli</th>
                                            <th class="align-middle">Diagnosa</th>
                                            <th class="align-middle">Procedur</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!empty($dataPengajuan))
                                            @foreach ($dataPengajuan as $data)
                                                <tr>
                                                    <td class="align-middle">{{ $data->no_sep }}</td>
                                                    <td class="align-middle">{{ $data->no_kartu }}</td>
                                                    <td class="align-middle">{{ $data->nama_pasien }}
                                                        <a href="/vedika/rajal/{{ Crypt::encrypt($data->no_rawat) }}/detail"
                                                            class="btn btn-sm " data-toggle="tooltip"
                                                            data-placement="bottom" title="Detail Informasi"
                                                            target="_blank">
                                                            <span class="badge badge-info"><i
                                                                    class="fas fa-search"></i></span></a>
                                                        @can('vedika-fraud-create')
                                                            @if (\App\FraudRajal::checkFraud($data->id) == false)
                                                                {{-- @can('fraud-tambah') --}}
                                                                <a href="/vedika/fraud/{{ Crypt::encrypt($data->id) }}/{{ Request::get('periode') }}/store"
                                                                    class="btn btn-sm " data-toggle="tooltip"
                                                                    data-placement="bottom" title="Tambah data Fraud">
                                                                    <span class="badge badge-warning"><i
                                                                            class="fas fa-plus-circle"></i>
                                                                        Fraud</span></a>
                                                                {{-- @endcan --}}
                                                            @else
                                                                <a href="/vedika/fraud/{{ Crypt::encrypt($data->id) }}/delete"
                                                                    class="btn btn-sm delete-confirm" data-toggle="tooltip"
                                                                    data-placement="bottom" title="Delete data Fraud">
                                                                    <span class="badge badge-danger"><i
                                                                            class="fas fa-times-circle"></i>
                                                                        Fraud</span></a>
                                                            @endif
                                                        @endcan
                                                    </td>
                                                    <td class="align-middle">{{ $data->tgl_registrasi }}</td>
                                                    <td class="align-middle">{{ $data->nama_poli }}</td>
                                                    <td>
                                                        {{ collect(\Illuminate\Support\Arr::get($diagnosaGrouped, $data->no_rawat, []))->pluck('kd_penyakit')->implode(', ') }}
                                                    </td>
                                                    <td>
                                                        {{ collect(\Illuminate\Support\Arr::get($prosedurGrouped, $data->no_rawat, []))->pluck('kode')->implode(', ') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
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
    @if (!empty($dataPengajuan))
        <div class="modal fade" id="modal-berkas-klaim">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Berkas Klaim</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- text input -->
                            <div class="col-12">
                                @can('vedika-upload')
                                    <a href="/vedika/pengajuan/{{ Crypt::encrypt(Request::get('periode')) }}/gabungberkasall"
                                        class="btn btn-success btn-sm btn-block">
                                        <i class="fas fa-sync-alt"></i> Kumpulkan & Gabung Berkas</a>
                                    <a href="/vedika/pengajuan/{{ Crypt::encrypt(Request::get('periode')) }}/makeziprajal"
                                        class="btn btn-warning btn-sm btn-block">
                                        <i class="far fa-file-archive"></i> Arsipkan Berkas Rajal</a>
                                @endcan
                                <a href="/vedika/pengajuan/rajal/{{ Crypt::encrypt(Request::get('periode')) }}/downloadzip"
                                    class="btn btn-primary btn-sm btn-block">
                                    <i class="fas fa-file-download"></i> Bulk Download</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    @endif
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
        $('#tanggalMulai,#tanggalSelesai').datetimepicker({
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
