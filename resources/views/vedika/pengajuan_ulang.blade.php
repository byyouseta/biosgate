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
                    if (!empty(Request::get('periode'))) {
                        $jenis = Request::get('jenis');
                    } else {
                        $jenis = null;
                    }
                @endphp
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="form-group row">
                                <div class="col-sm-4 mt-2">
                                    <label>Data Pasien</label>
                                </div>
                                <div class="col-sm-4 mb-2">
                                    <div class="float-right">
                                        @if (!empty($dataPengajuan))
                                            <button class="btn btn-primary btn-sm" data-toggle="modal"
                                                data-target="#modal-berkas-klaim">
                                                <i class="fas fa-file-download"></i> Download
                                            </button>
                                            <button class="btn btn-secondary btn-sm" data-toggle="modal"
                                                data-target="#modal-pengajuan-pending">
                                                <i class="fas fa-plus-circle"></i> Tambah
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <form action="/vedika/pengajuan/ulang" method="GET">
                                        <div class="input-group">
                                            <select name="jenis" class="form-control" required>
                                                <option value="">Pilih Pelayanan</option>
                                                <option value="Rawat Jalan" {{ $jenis == 'Rawat Jalan' ? 'selected' : '' }}>
                                                    Rawat Jalan</option>
                                                <option value="Rawat Inap" {{ $jenis == 'Rawat Inap' ? 'selected' : '' }}>
                                                    Rawat
                                                    Inap</option>
                                            </select>
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
                                            <th class="align-middle">No.SEP</th>
                                            <th class="align-middle">No.Kartu</th>
                                            <th class="align-middle">Nama Pasien</th>
                                            <th class="align-middle">Tgl Registrasi</th>
                                            <th class="align-middle">{{ $jenis == 'Rawat Jalan' ? 'Poli' : 'Kamar' }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!empty($dataPengajuan))
                                            @foreach ($dataPengajuan as $data)
                                                <tr>
                                                    <td class="align-middle">{{ $data->no_sep }}</td>
                                                    <td class="align-middle">{{ $data->no_kartu }}</td>
                                                    <td class="align-middle">{{ $data->nama_pasien }}
                                                        @if ($jenis == 'Rawat Inap')
                                                            <a href="/vedika/ranap/{{ Crypt::encrypt($data->no_rawat) }}/detail"
                                                                class="btn btn-sm " data-toggle="tooltip"
                                                                data-placement="bottom" title="Detail Informasi"
                                                                target="_blank">
                                                                <span class="badge badge-info"><i
                                                                        class="fas fa-search"></i></span></a>
                                                        @else
                                                            <a href="/vedika/rajal/{{ Crypt::encrypt($data->no_rawat) }}/detail"
                                                                class="btn btn-sm " data-toggle="tooltip"
                                                                data-placement="bottom" title="Detail Informasi"
                                                                target="_blank">
                                                                <span class="badge badge-info"><i
                                                                        class="fas fa-search"></i></span></a>
                                                        @endif
                                                        <a href="/vedika/pengajuanpending/{{ Crypt::encrypt($data->id) }}/delete"
                                                            class="btn btn-sm delete-confirm @cannot('vedika-delete') disabled @endcannot"
                                                            data-toggle="tooltip" data-placement="bottom" title="Delete">
                                                            <span class="badge badge-danger text-center"><i
                                                                    class="fas fa-ban"></i></span>
                                                        </a>
                                                    </td>
                                                    <td class="align-middle">{{ $data->tgl_registrasi }}</td>
                                                    <td class="align-middle">{{ $data->nama_poli }}</td>
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
                                    {{-- <a href="/vedika/pengajuan/{{ Crypt::encrypt(Request::get('periode')) }}/gabungberkasall"
                                    class="btn btn-success btn-sm btn-block">
                                    <i class="fas fa-sync-alt"></i> Kumpulkan & Gabung Berkas</a> --}}
                                    @if ($jenis == 'Rawat Inap')
                                        <a href="/vedika/pengajuanpending/{{ Crypt::encrypt(Request::get('periode')) }}/makezipranap"
                                            class="btn btn-warning btn-sm btn-block">
                                            <i class="far fa-file-archive"></i> Arsipkan Berkas Ranap</a>
                                    @else
                                        <a href="/vedika/pengajuanpending/{{ Crypt::encrypt(Request::get('periode')) }}/makeziprajal"
                                            class="btn btn-warning btn-sm btn-block">
                                            <i class="far fa-file-archive"></i> Arsipkan Berkas Rajal</a>
                                    @endif
                                @endcan
                                @if ($jenis == 'Rawat Inap')
                                    <a href="/vedika/pengajuanpending/ranap/{{ Crypt::encrypt(Request::get('periode')) }}/downloadzip"
                                        class="btn btn-primary btn-sm btn-block">
                                        <i class="fas fa-file-download"></i> Bulk Download</a>
                                @else
                                    <a href="/vedika/pengajuanpending/rajal/{{ Crypt::encrypt(Request::get('periode')) }}/downloadzip"
                                        class="btn btn-primary btn-sm btn-block">
                                        <i class="fas fa-file-download"></i> Bulk Download</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    @endif

    {{-- Modal pengajuan pending --}}
    <div class="modal fade" id="modal-pengajuan-pending">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="/vedika/pengajuanpending">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">Pengajuan Klaim Pending</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label>No Rawat pasien</label>
                                    {{-- <div class="input-group mb-3"> --}}
                                    <input type="text" class="form-control" value="" name="no_rawat"
                                        id="no_rawat" />
                                    {{-- <div class="input-group-append">
                                            <button class="btn btn-primary" id="btnAmbil">Ambil</button>
                                        </div>
                                    </div> --}}
                                </div>
                                <div class="form-group">
                                    <label>No SEP</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" value="" name="no_sep"
                                            id="no_sep" />
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" id="btnAmbil">Ambil</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>No Kartu</label>
                                    <input type="text" class="form-control" value="" name="no_bpjs" readonly
                                        id="no_bpjs" />
                                </div>
                                <div class="form-group">
                                    <label>Nama Pasien</label>
                                    <input type="text" class="form-control" value="" name="nama_pasien"
                                        id="nama_pasien" readonly />
                                </div>
                                <div class="form-group">
                                    <label>Jenis Rawat</label>
                                    <input type="text" class="form-control" value="" name="jenis_rawat"
                                        id="jenis_rawat" readonly />
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Tgl Lahir</label>
                                    <input type="text" class="form-control" value="" name="tgl_lahir"
                                        id="tgl_lahir" readonly />
                                </div>
                                <div class="form-group">
                                    <label>Jenis Kelamin</label>
                                    <input type="text" class="form-control" value="" name="jk" readonly
                                        id="jk" />
                                </div>
                                <div class="form-group">
                                    <label>Tgl Registrasi</label>
                                    <input type="text" class="form-control" value="" name="tgl_registrasi"
                                        id="tgl_registrasi" readonly />
                                </div>
                                <div class="form-group">
                                    <label>Poli Dituju</label>
                                    <input type="text" class="form-control" value="" name="nm_poli" readonly
                                        id="nm_poli" />
                                </div>
                                <div class="form-group">
                                    <label>Periode</label>
                                    <select name="periode" class="form-control" required>
                                        <option value="">Pilih</option>
                                        @foreach ($dataPeriode as $periodeUlang)
                                            <option value="{{ $periodeUlang->id }}">{{ $periodeUlang->periode }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
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
@section('get')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script>
        $('#btnAmbil').click(function() {

            let no_rawat = $('#no_rawat').val();
            let no_sep = $('#no_sep').val();

            if (no_sep == '') {
                alert('No SEP masih kosong');
                return;
            }

            $.ajax({
                url: "{{ route('vedika.getPasien') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    no_rawat: no_rawat,
                    no_sep: no_sep
                },
                beforeSend: function() {
                    $('#btnAmbil').prop('disabled', true).text('Loading...');
                },
                success: function(response) {

                    if (response.status) {

                        $('#no_rawat').val(response.data.no_rawat);
                        $('#no_sep').val(response.data.no_sep);
                        $('#no_bpjs').val(response.data.no_bpjs);
                        $('#nama_pasien').val(response.data.nama_pasien);
                        $('#jenis_rawat').val(response.data.jenis_rawat);
                        $('#tgl_lahir').val(response.data.tgl_lahir);
                        $('#jk').val(response.data.jk);
                        $('#tgl_registrasi').val(response.data.tgl_registrasi);
                        $('#nm_poli').val(response.data.nm_poli);

                    } else {
                        alert(response.message);
                    }

                },
                error: function() {
                    alert('Terjadi kesalahan');
                },
                complete: function() {
                    $('#btnAmbil').prop('disabled', false).text('Ambil');
                }

            });

        });
    </script>
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
