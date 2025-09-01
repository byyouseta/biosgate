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
                        $tanggalSelesai = Request::get('tanggalSelesai');
                    } else {
                        $tanggal = \Carbon\Carbon::now()->format('Y-m-d');
                        $tanggalSelesai = \Carbon\Carbon::now()->format('Y-m-d');
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
                                            data-target="#modal-import" @cannot('vedika-klaim-compare') disabled @endcannot>
                                            <i class="fas fa-file-upload"></i> Import</a>
                                        </button>
                                    </div>
                                    <div class="btn-group">
                                        {{-- <a href="/vedika/klaimcompare/template" class="btn btn-sm  btn-default"><i
                                                class="fas fa-file-download"></i> Ambil Respon Vklaim </a> --}}
                                        <button class="btn btn-info btn-sm " data-toggle="modal"
                                            data-target="#modal-ambil-respon"
                                            @cannot('vedika-klaim-compare') disabled @endcannot>
                                            <i class="fas fa-cloud-download-alt"></i> Ambil Respon Vklaim</a>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-sm-4 mt-2">

                                </div>
                                <div class="col-sm-3">
                                    <form action="/vedika/klaimcompare" method="GET">
                                        <div class="input-group input-group" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input"
                                                id="tanggalMulai" data-target="#tanggalMulai" data-toggle="datetimepicker"
                                                name="tanggal" autocomplete="off" value="{{ $tanggal }}">
                                            <input type="text" class="form-control datetimepicker-input"
                                                id="tanggalSelesai" data-target="#tanggalSelesai"
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
                        </div>
                        <div class="card-body">
                            {{-- <div style="width:100%; overflow:auto;"> --}}
                            <table class="table table-bordered table-sm" id="example">
                                <thead>
                                    <tr>
                                        <th class="align-middle">No.SEP</th>
                                        <th class="align-middle">No.Rawat/No.RM</th>
                                        <th class="align-middle">Nama Pasien</th>
                                        <th class="align-middle">Tgl Registrasi</th>
                                        <th class="align-middle">Tgl Keluar</th>
                                        <th class="align-middle">DPJP</th>
                                        <th class="align-middle">Diagnosa</th>
                                        <th class="align-middle">Procedure</th>
                                        <th class="align-middle">Biil RS</th>
                                        <th class="align-middle">Bill tanpa Kronis</th>
                                        <th class="align-middle">Cair</th>
                                        <th class="align-middle">Pending</th>
                                        <th class="align-middle">Status</th>
                                        <th class="align-middle">Pengajuan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // dd('masuk');
                                    @endphp
                                    @foreach ($data as $listData)
                                        <tr>
                                            @php
                                                $no_sep = null;
                                                if ($listData->no_sep) {
                                                    $no_sep = $listData->no_sep;
                                                }
                                            @endphp
                                            <td>{{ $no_sep }} </td>
                                            <td>{{ $listData->no_rawat }}, {{ $listData->no_rkm_medis }}</td>
                                            <td>{{ $listData->nm_pasien }}, {{ $listData->umurdaftar }}
                                                {{ $listData->sttsumur }},
                                                {{ $listData->jk == 'L' ? 'Laki-Laki' : 'Perempuan' }}
                                                @if ($listData->status_lanjut == 'Ralan')
                                                    <a href="/vedika/rajal/{{ Crypt::encrypt($listData->no_rawat) }}/detail"
                                                        class="btn btn-sm " data-toggle="tooltip" data-placement="bottom"
                                                        title="Detail Informasi" target="_blank">
                                                        <span class="badge badge-info"><i
                                                                class="fas fa-search"></i></span></a>
                                                @elseif ($listData->status_lanjut == 'Ranap')
                                                    <a href="/vedika/ranap/{{ Crypt::encrypt($listData->no_rawat) }}/detail"
                                                        class="btn btn-sm " data-toggle="tooltip" data-placement="bottom"
                                                        title="Detail Informasi" target="_blank">
                                                        <span class="badge badge-info"><i class="fas fa-search"></i>
                                                        </span>
                                                    </a>
                                                @endif

                                            </td>
                                            <td>{{ $listData->tgl_registrasi }} {{ $listData->jam_reg }}</td>
                                            <td>
                                                @if ($listData->status_lanjut == 'Ralan')
                                                    {{ $listData->tgl_registrasi }}
                                                @elseif($listData->status_lanjut == 'Ranap')
                                                    {{ isset($waktu[$listData->no_rawat]) ? $waktu[$listData->no_rawat]->waktuKeluar : '-' }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($listData->status_lanjut == 'Ralan')
                                                    {{ $listData->nm_dokter }}
                                                @elseif($listData->status_lanjut == 'Ranap')
                                                    {{ isset($waktu[$listData->no_rawat]) ? $waktu[$listData->no_rawat]->nm_dokter : '-' }}
                                                @endif
                                            </td>
                                            @php
                                                $billKronis = null;
                                                if ($no_sep) {
                                                    if (empty($cair[$no_sep])) {
                                                        $pending = isset($dataPending[$no_sep])
                                                            ? $dataPending[$no_sep]
                                                            : null;

                                                        if ($pending) {
                                                            $danaCair = $pending->biaya_disetujui;
                                                        }
                                                    } else {
                                                        $pending = (object) [];
                                                        $pending->status = 'Cair';
                                                    }

                                                    if (isset($bill[$listData->no_rawat])) {
                                                        $billKronis =
                                                            $bill[$listData->no_rawat]->total_biaya -
                                                            (isset($kronis[$listData->no_rawat]->total_biaya)
                                                                ? $kronis[$listData->no_rawat]->total_biaya
                                                                : 0);
                                                    }

                                                    $klaimCair = isset($cair[$no_sep]) ? $cair[$no_sep] : 0;
                                                } else {
                                                    $billKronis = 0; //
                                                    $pending = $klaimCair = null; //
                                                }

                                                if ($listData->status_lanjut == 'Ralan') {
                                                    $jenis_rawat = 'Rawat Jalan';
                                                } else {
                                                    $jenis_rawat = 'Rawat Inap';
                                                }
                                                $statusPengajuan = null;
                                                if (strlen($no_sep) > 5) {
                                                    $statusPengajuan = isset($dataPengajuan[$no_sep])
                                                        ? $dataPengajuan[$no_sep]
                                                        : null;
                                                }
                                            @endphp
                                            <td>
                                                @if (!empty($diagnosa->where('no_rawat', $listData->no_rawat)->where('status', $listData->status_lanjut)))
                                                    @foreach ($diagnosa->where('no_rawat', $listData->no_rawat)->where('status', $listData->status_lanjut) as $index => $dataDiagnosa)
                                                        @if (!$loop->last)
                                                            {{ $dataDiagnosa->kd_penyakit }},
                                                        @else
                                                            {{ $dataDiagnosa->kd_penyakit }}
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>
                                                @if (!empty($prosedur->where('no_rawat', $listData->no_rawat)->where('status', $listData->status_lanjut)))
                                                    @foreach ($prosedur->where('no_rawat', $listData->no_rawat)->where('status', $listData->status_lanjut) as $index => $dataProsedur)
                                                        @if (!$loop->last)
                                                            {{ $dataProsedur->kode }},
                                                        @else
                                                            {{ $dataProsedur->kode }}
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                {{ isset($bill[$listData->no_rawat]) ? number_format($bill[$listData->no_rawat]->total_biaya, 0, '.', ',') : '0' }}
                                            </td>
                                            <td class="text-right">{{ number_format($billKronis, 0, '.', ',') }}</td>
                                            <td class="text-right">
                                                {{ $klaimCair >= 0 ? number_format($klaimCair, 0, '.', ',') : '-' }}
                                                @if ($klaimCair > $billKronis)
                                                    <span class="badge badge-success"><i class="fas fa-arrow-up"></i></span>
                                                @elseif($klaimCair < $billKronis && $klaimCair != null)
                                                    <span class="badge badge-danger"><i
                                                            class="fas fa-arrow-down"></i></span>
                                                @endif
                                            </td>
                                            <td>{{ isset($pending) ? $pending->status : '-' }} </td>
                                            <td>{{ $listData->status_lanjut }} </td>
                                            <td>
                                                @if ($statusPengajuan)
                                                    <span class="badge badge-success">
                                                        {{ \Carbon\Carbon::parse($statusPengajuan->periodeKlaim->periode)->format('F Y') }}</span>
                                                @else
                                                    <span class="badge badge-danger">Belum diajukan</span>
                                                @endif
                                            </td>
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
    <div class="modal fade" id="modal-ambil-respon">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form method="POST" action="/vedika/klaimcompare/ambilresponevklaim">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">Ambil Respon Vklaim</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- text input -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="bulanTarik">Periode Bulan</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control datetimepicker-input" id="periodeBulan"
                                            data-target="#periodeBulan" data-toggle="datetimepicker" name="periodeBulan"
                                            autocomplete="off" value="{{ $tanggal }}" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="far fa-calendar-check"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            {{-- <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="jenis">Jenis Pelayanan</label>
                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="jenis" value="1" id="jenisRanap">
                                    <label class="form-check-label" for="jenisRanap">Rawat Inap</label>
                                  </div>
                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="jenis" value="2" id="jenisRajal">
                                    <label class="form-check-label" for="jenisRajal">Rawat Jalan</label>
                                  </div>
                                </div>
                            </div> --}}
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="jenis">Status Klaim</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" value="2"
                                            id="pending" required>
                                        <label class="form-check-label" for="pending">Pending Verifikasi</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" value="3"
                                            id="klaim" required>
                                        <label class="form-check-label" for="klaim">Klaim</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                        <button type="Submit" class="btn btn-primary">Ambil</button>
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
        var tglMulai = "<?php echo $tanggal; ?>";
        var tglSelesai = "<?php echo $tanggalSelesai; ?>";
        var namaFile = 'Data Compare ' + tglMulai + ' sampai ' + tglSelesai;
        // $(function() {
        //     $('#example').DataTable({
        //         "paging": true,
        //         "lengthChange": false,
        //         "searching": true,
        //         "ordering": true,
        //         "info": true,
        //         "autoWidth": true,
        //         "responsive": true,
        //         "scrollY": true,
        //         "scrollX": true,
        //         "buttons": ["excel", "pdf", "print", "pageLength"]
        //     }).buttons().container().appendTo('#example_wrapper .col-md-6:eq(0)');

        //     // Menambahkan margin pada pagination melalui jQuery
        //     $('#example_wrapper .dataTables_paginate').css('margin-top', '14px');
        // });
        $(document).ready(function() {
            $('#example').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": true,
                "responsive": true,
                scrollX: true,
                buttons: [{
                        extend: 'excelHtml5',
                        text: 'Excel',
                        filename: namaFile, // Nama file untuk Excel
                    },
                    {
                        extend: 'pdfHtml5',
                        text: 'PDF',
                        filename: namaFile, // Nama file untuk PDF
                    },
                    {
                        extend: 'print',
                        text: 'Print'
                    },
                    'pageLength'
                ]
            }).buttons().container().appendTo('#example_wrapper .col-md-6:eq(0)');

            // Menambahkan margin pada pagination melalui jQuery
            $('#example_wrapper .dataTables_paginate').css('margin-top', '14px');
            // tambahkan opsi lain jika perlu
        });
        //Date picker
        $('#tanggalMulai,#tanggalSelesai').datetimepicker({
            format: 'YYYY-MM-DD'
        });
        $('#periodeBulan').datetimepicker({
            format: 'YYYY-MM'
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
