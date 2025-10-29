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
                                <div class="col-sm-9 mt-2">
                                    <label>Data Pasien Rajal/IGD</label>
                                </div>
                                <div class="col-sm-3">
                                    <form action="/vedika/rajal" method="GET">
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
                        </div>
                        <div class="card-body">
                            <div>
                                <table class="table table-bordered table-hover table-sm" id="example2">
                                    <thead>
                                        <tr>
                                            {{-- <th class="align-middle">No.RM</th> --}}
                                            <th class="align-middle">No.SEP</th>
                                            <th class="align-middle">No.Kartu</th>
                                            <th class="align-middle">Nama Pasien</th>
                                            {{-- <th class="align-middle">Alamat</th> --}}
                                            <th class="align-middle">Tgl Registrasi</th>
                                            <th class="align-middle">Nama Poli</th>
                                            {{-- <th class="align-middle">D.U</th> --}}
                                            <th class="align-middle">Status</th>
                                            <th class="align-middle">Diagnosa</th>
                                            <th class="align-middle">Prosedur</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $data)
                                            @if (\App\PelaporanCovid::cekLapor($data->no_rawat) == 0)
                                                <tr>
                                                    @php
                                                        $dataSep = App\Vedika::getSep($data->no_rawat, 2);
                                                        $getManualSep = App\Vedika::getHapusSep($data->no_rawat);
                                                        // if($data->no_peserta == '0002761296423'){
                                                        // dd($dataSep, $getManualSep);
                                                        // }
                                                        //Ambil data untuk Bukti Pelayanan
                                                        // $buktiPelayanan = \App\Http\Controllers\VedikaController::buktiPelayanan(
                                                        //     $data->no_rawat
                                                        // );
                                                        // $diagnosa = $buktiPelayanan[0];
                                                        // $prosedur = $buktiPelayanan[1];
                                                    @endphp
                                                    {{-- <td>{{ $data->no_rkm_medis }}</td> --}}
                                                    <td>{{ $dataSep != null ? $dataSep->no_sep : '' }}
                                                        @if ($getManualSep != null)
                                                            <a href="/vedika/rajal/{{ Crypt::encrypt($data->no_rawat) }}/hapusSep"
                                                                class="btn btn-sm" data-toggle="tooltip"
                                                                data-placement="bottom" title="Hapus SEP">
                                                                <span class="badge bg-danger"><i
                                                                        class="fas fa-ban"></i></span>
                                                            </a>
                                                        @endif
                                                        @if ($dataSep == null && Auth::user()->can('vedika-upload'))
                                                            <a href="/vedika/rajal/{{ Crypt::encrypt($data->no_rawat) }}/sepmanual"
                                                                class="btn btn-sm " data-toggle="tooltip"
                                                                data-placement="bottom" title="Input no SEP"
                                                                target="_blank">
                                                                <span class="badge badge-primary">Tambah <i
                                                                        class="fas fa-pen-nib"></i></span></a>
                                                        @endif
                                                    </td>
                                                    <td>{{ $data->no_peserta }}</td>
                                                    <td>{{ $data->nm_pasien }}, {{ $data->umurdaftar }}
                                                        {{ $data->sttsumur }},
                                                        {{ $data->jk == 'L' ? 'Laki-Laki' : 'Perempuan' }}
                                                        <a href="/vedika/rajal/{{ Crypt::encrypt($data->no_rawat) }}/detail"
                                                            class="btn btn-sm " data-toggle="tooltip"
                                                            data-placement="bottom" title="Detail Informasi"
                                                            target="_blank">
                                                            <span class="badge badge-info"><i
                                                                    class="fas fa-search"></i></span></a>

                                                    </td>
                                                    {{-- <td>{{ $data->alamat }}</td> --}}
                                                    <td>{{ $data->tgl_registrasi }} {{ $data->jam_reg }}</td>
                                                    <td>{{ $data->nm_poli }}</td>

                                                    {{-- <td>
                                                        {{ App\Vedika::getDiagnosa($data->no_rawat, 'Ralan') != null ? App\Vedika::getDiagnosa($data->no_rawat, 'Ralan')->kd_penyakit . '-' . App\Vedika::getDiagnosa($data->no_rawat, 'Ralan')->nm_penyakit : '' }}
                                                    </td> --}}
                                                    <td>
                                                        <div class="col text-center">
                                                            @php
                                                                // $statusVerif = App\VedikaVerif::cekVerif(
                                                                //     $data->no_rawat,
                                                                //     'Rajal'
                                                                // );
                                                                if (!empty($dataSep)) {
                                                                    $cekKlaim = App\Vedika::cekEklaim($dataSep->no_sep);
                                                                } else {
                                                                    $cekKlaim = null;
                                                                }
                                                                // $statusPengajuan = App\DataPengajuanKlaim::cekPengajuan(
                                                                //     $data->no_rawat,
                                                                //     'Rawat Jalan'
                                                                // );
                                                                // $cekDiagnosa = App\Vedika::getDiagnosa(
                                                                //     $data->no_rawat,
                                                                //     'Ralan'
                                                                // );
                                                                // $cekBokingOp = App\Vedika::getBookingOperasi(
                                                                //     $data->no_rawat
                                                                // );
                                                                // if ($data->no_rawat == '2025/09/01/000296') {
                                                                //     dd($diagnosaGrouped->get($data->no_rawat)->count());
                                                                // }
                                                            @endphp
                                                            @can('vedika-upload')
                                                                @if (!empty($statusVerif[$data->no_rawat]))
                                                                    @if ($statusVerif[$data->no_rawat]->status == 0)
                                                                        <span class="badge badge-warning" data-toggle="tooltip"
                                                                            data-placement="bottom"
                                                                            title="Periksa Verifikasi"><i
                                                                                class="fas fa-exclamation-triangle"></i> </span>
                                                                    @elseif ($statusVerif[$data->no_rawat]->status == 1)
                                                                        <span class="badge badge-success" data-toggle="tooltip"
                                                                            data-placement="bottom"
                                                                            title="Verifikasi Selesai"><i
                                                                                class="fas fa-check-circle"></i></span>
                                                                    @endif
                                                                @endif
                                                                @if (!empty($cekKlaim) && $cekKlaim == true)
                                                                    <span class="badge bg-lime" data-toggle="tooltip"
                                                                        data-placement="bottom" title="Berkas ditemukan">Eklaim
                                                                        <i class="fas fa-check-circle"></i></span>
                                                                @endif
                                                                @if ($diagnosaGrouped->get($data->no_rawat) && $diagnosaGrouped->get($data->no_rawat)->count() > 0)
                                                                    <span class="badge bg-purple" data-toggle="tooltip"
                                                                        data-placement="bottom" title="Berkas ditemukan">Diag
                                                                        <i class="fas fa-check-circle"></i></span>
                                                                @endif
                                                                @if (!empty($cekBokingOp[$data->no_rawat]))
                                                                    <span class="badge bg-info" data-toggle="tooltip"
                                                                        data-placement="bottom" title="Berkas ditemukan">PreOP
                                                                        <i class="fas fa-check-circle"></i></span>
                                                                @endif
                                                            @endcan
                                                            @if (!empty($statusPengajuan[$data->no_rawat]))
                                                                <span class="badge badge-success"><i
                                                                        class="fas fa-paper-plane"></i>
                                                                    {{ \Carbon\Carbon::parse($statusPengajuan[$data->no_rawat]->periodeKlaim->periode)->format('F Y') }}
                                                                </span>
                                                            @else
                                                                <span class="badge badge-danger"><i
                                                                        class="fas fa-paper-plane"></i>Belum
                                                                    diajukan</span>
                                                            @endif
                                                            @if (isset($dataSep->no_sep))
                                                                @if (file_exists(public_path("pdfklaim/$dataSep->no_sep/$dataSep->no_sep.pdf")))
                                                                    <span class="badge badge-danger" data-toggle="tooltip"
                                                                        data-placement="bottom"
                                                                        title="File Gabung ditemukan"><i
                                                                            class="fas fa-file-pdf"></i></span>
                                                                @endif
                                                            @elseif(isset($dataSep->noSep))
                                                                @if (file_exists(public_path("pdfklaim/$dataSep->noSep/$dataSep->noSep.pdf")))
                                                                    <span class="badge badge-danger" data-toggle="tooltip"
                                                                        data-placement="bottom"
                                                                        title="File Gabung ditemukan"><i
                                                                            class="fas fa-file-pdf"></i></span>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        {{ collect(\Illuminate\Support\Arr::get($diagnosaGrouped, $data->no_rawat, []))->pluck('kd_penyakit')->implode(', ') }}
                                                    </td>
                                                    <td>
                                                        {{ collect(\Illuminate\Support\Arr::get($prosedurGrouped, $data->no_rawat, []))->pluck('kode')->implode(', ') }}
                                                    </td>
                                                    {{-- <td>
                                                        @if (!empty($diagnosa->where('no_rawat', $data->no_rawat)))
                                                            @foreach ($diagnosa->where('no_rawat', $data->no_rawat) as $index => $dataDiagnosa)
                                                                @if (!$loop->last)
                                                                    {{ $dataDiagnosa->kd_penyakit }},
                                                                @else
                                                                    {{ $dataDiagnosa->kd_penyakit }}
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (!empty($prosedur->where('no_rawat', $data->no_rawat)))
                                                            @foreach ($prosedur->where('no_rawat', $data->no_rawat) as $index => $dataProsedur)
                                                                @if (!$loop->last)
                                                                    {{ $dataProsedur->kode }},
                                                                @else
                                                                    {{ $dataProsedur->kode }}
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </td> --}}
                                                </tr>
                                            @endif
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
                scrollX: true,
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
                    setTimeout(() => {
                        api.columns.adjust().draw();
                    }, 100)
                },
            });
        });
    </script>
@endsection
