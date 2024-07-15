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
                            <div class="form-group row">
                                <div class="col-sm-7">
                                    <label>Data Fraud Rajal/IGD</label>
                                </div>
                                <div class="col-sm-2">
                                    @if (!empty(Request::get('periode')))
                                        <div class="float-right">
                                            <a href="/vedika/fraud/{{ Crypt::encrypt(Request::get('periode')) }}/export"
                                                class="btn btn-success btn-flat" target="_blank">
                                                <i class="far fa-file-excel"></i> Export</a>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-sm-3">
                                    <form action="/vedika/fraud/rajal" method="GET">
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
                            <div>
                                <table class="table table-bordered table-hover table-sm" id="example2">
                                    <thead>
                                        <tr>
                                            {{-- <th class="align-middle">No.RM</th> --}}
                                            <th class="align-middle">No.RM</th>
                                            <th class="align-middle">Nama Pasien</th>
                                            <th class="align-middle">No.SEP</th>
                                            {{-- <th class="align-middle">Alamat</th> --}}
                                            {{-- <th class="align-middle">Tgl Registrasi</th>
                                            <th class="align-middle">Nama Poli</th> --}}
                                            <th class="align-middle">Kode ICD X</th>
                                            <th class="align-middle">Kode ICD IX</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!empty($dataFraud))
                                            @foreach ($dataFraud as $data)
                                                @php
                                                    if (!empty($data->dataPengajuan->no_rawat)) {
                                                        //Ambil data untuk Bukti Pelayanan
                                                        $buktiPelayanan = \App\Http\Controllers\VedikaController::buktiPelayanan(
                                                            $data->dataPengajuan->no_rawat
                                                        );
                                                        $diagnosa = $buktiPelayanan[0];
                                                        $prosedur = $buktiPelayanan[1];
                                                        $norm_pasien = $buktiPelayanan[2]->no_rkm_medis;
                                                    }

                                                @endphp
                                                @if (!empty($data->dataPengajuan->no_rawat))
                                                    <tr>
                                                        <td class="align-middle">{{ $norm_pasien }}</td>
                                                        <td class="align-middle">{{ $data->dataPengajuan->nama_pasien }}
                                                        </td>
                                                        <td class="align-middle">{{ $data->dataPengajuan->no_sep }}
                                                            <a href="/vedika/rajal/{{ Crypt::encrypt($data->dataPengajuan->no_rawat) }}/detail"
                                                                class="btn btn-sm " data-toggle="tooltip"
                                                                data-placement="bottom" title="Detail Informasi"
                                                                target="_blank">
                                                                <span class="badge badge-info"><i
                                                                        class="fas fa-search"></i></span></a>
                                                            <a href="/vedika/eklaim/{{ $data->dataPengajuan->no_sep }}/printout"
                                                                class="btn btn-sm " data-toggle="tooltip"
                                                                data-placement="bottom" title="Data Eklaim" target="_blank">
                                                                <span class="badge bg-purple"><i
                                                                        class="fas fa-check-circle"></i> Eklaim
                                                                    Form</span></a>
                                                            <a href="/vedika/fraud/{{ Crypt::encrypt($data->id) }}/detail"
                                                                class="btn btn-sm " data-toggle="tooltip"
                                                                data-placement="bottom" title="Check List Fraud"
                                                                target="_blank">
                                                                <span class="badge bg-primary"><i
                                                                        class="fas fa-check-double"></i></span></a>
                                                        </td>
                                                        <td class="align-middle">
                                                            @if (!empty($diagnosa))
                                                                @foreach ($diagnosa as $index => $dataDiagnosa)
                                                                    {{ $dataDiagnosa->kd_penyakit }},
                                                                @endforeach
                                                            @endif
                                                        </td>
                                                        <td class="align-middle">
                                                            @if (!empty($prosedur))
                                                                @foreach ($prosedur as $index => $dataProsedur)
                                                                    {{ $dataProsedur->kode }},
                                                                @endforeach
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
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
    {{-- Modal untuk add pasien --}}
    <div class="modal fade" id="modal-tambah">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="/vedika/fraud/store">
                    @csrf
                    <div class="modal-header">
                        <div class="modal-title">Penambahan Pasien</div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm unwrap" id="example" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Select</th>
                                            <th class="align-middle">No.RM</th>
                                            <th class="align-middle">No.SEP</th>
                                            <th class="align-middle">No.Kartu</th>
                                            <th class="align-middle">Nama Pasien</th>
                                            {{-- <th class="align-middle">Alamat</th> --}}
                                            <th class="align-middle">Tgl Registrasi</th>
                                            <th class="align-middle">Nama Poli</th>
                                            {{-- <th class="align-middle">Dokter</th> --}}
                                            {{-- <th class="align-middle">D.U</th> --}}
                                            {{-- <th class="align-middle">Berkas</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                                {{-- <div class="col-6">
                                <div class="form-group">
                                    <label>No Rawat pasien</label>
                                    <input type="text" class="form-control" value="{{ $pasien->no_rawat }}"
                                        name="no_rawat" readonly />
                                </div>
                                <div class="form-group">
                                    <label>No SEP</label>
                                    <input type="text" class="form-control"
                                        value="{{ !empty($dataSep->no_sep) ? $dataSep->no_sep : '' }}" name="no_sep"
                                        {{ !empty($dataSep->no_sep) ? 'readonly' : 'required' }} />
                                </div>
                                <div class="form-group">
                                    <label>No Kartu</label>
                                    <input type="text" class="form-control" value="{{ $pasien->no_peserta }}"
                                        name="no_bpjs" readonly />
                                </div>
                                <div class="form-group">
                                    <label>Nama Pasien</label>
                                    <input type="text" class="form-control" value="{{ $pasien->nm_pasien }}"
                                        name="nama_pasien" readonly />
                                </div>
                                <div class="form-group">
                                    <label>Jenis Rawat</label>
                                    <input type="text" class="form-control" value="Rawat Jalan" name="jenis_rawat"
                                        readonly />
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Tgl Lahir</label>
                                    <input type="text" class="form-control" value="{{ $pasien->tgl_lahir }}"
                                        name="tgl_lahir" readonly />
                                </div>
                                <div class="form-group">
                                    <label>Jenis Kelamin</label>
                                    <input type="text" class="form-control" value="{{ $pasien->jk }}" name="jk"
                                        readonly />
                                </div>
                                <div class="form-group">
                                    <label>Tgl Registrasi</label>
                                    <input type="text" class="form-control" value="{{ $pasien->tgl_registrasi }}"
                                        name="tgl_registrasi" readonly />
                                </div>
                                <div class="form-group">
                                    <label>Poli Dituju</label>
                                    <input type="text" class="form-control" value="{{ $pasien->nm_poli }}"
                                        name="nm_poli" readonly />
                                </div>
                                <div class="form-group">
                                    <label>Periode</label>
                                    <select name="periode" class="form-control" required>
                                        <option value="">Pilih</option>
                                        @foreach ($periodeKlaim as $periode)
                                            <option value="{{ $periode->id }}">{{ $periode->periode }}</option>
                                        @endforeach
                                    </select>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default float-left" data-dismiss="modal">Tutup</button>
                        <button type="Submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('plugin')
    <script src="{{ asset('template/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.4.0/js/dataTables.fixedHeader.min.js"></script>
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
                "fixedHeader": true,
                "oLanguage": {
                    "sSearch": "Cari:"
                }
            });
        });
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
