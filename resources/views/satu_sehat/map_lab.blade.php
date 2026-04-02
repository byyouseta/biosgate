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
        @php
            if (!empty(Request::get('cari'))) {
                $cari = Request::get('cari');
            } else {
                $cari = null;
            }
        @endphp
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Data Mapping Laboratorium</div>
                            {{-- <div class="float-right">
                                    <a href="/satusehat/nakessehat/sync" class="btn btn-info btn-flat btn-sm"><i
                                            class="fas fa-sync-alt"></i> Sync Data Nama Pegawai</a>
                                </div> --}}
                        </div>
                        <div class="card-body">
                            {{-- <div style="overflow-x:auto;"> --}}
                            <table class="table table-bordered table-hover table-sm" id="example2">
                                <thead>
                                    <tr>
                                        <th class="align-middle">Kode Perawatan</th>
                                        <th class="align-middle">Nama Perawatan</th>
                                        <th class="align-middle">Status Aktif</th>
                                        <th class="align-middle">Penjamin</th>
                                        <th class="align-middle">Kelas</th>
                                        <th class="align-middle">Kategori</th>
                                        <th class="align-middle">Nama Pemeriksaan</th>
                                        <th class="align-middle">Permintaan Hasil</th>
                                        <th class="align-middle">Spesimen</th>
                                        <th class="align-middle">Tipe Hasil Pemeriksaan</th>
                                        <th class="align-middle">Jenis Sampel</th>
                                        <th class="align-middle">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($data)
                                        @foreach ($data as $list)
                                            <tr>
                                                <td>{{ $list->kd_jenis_prw }}</td>
                                                <td>{{ $list->nm_perawatan }}</td>
                                                <td class="text-center">
                                                    @if ($list->status == '1')
                                                        <span class="badge badge-success">Enable</span>
                                                    @else
                                                        <span class="badge badge-danger">Disable</span>
                                                    @endif
                                                </td>
                                                <td>{{ $list->png_jawab }}</td>
                                                <td>{{ $list->kelas }}</td>
                                                <td>{{ $list->kategori }}</td>
                                                <td @if ($list->nama_pemeriksaan == null) class="bg-danger" @endif>
                                                    {{ $list->nama_pemeriksaan }}</td>
                                                <td>{{ $list->permintaan_hasil }}</td>
                                                <td>{{ $list->spesimen }}</td>
                                                <td>{{ $list->tipe_hasil_pemeriksaan }}</td>
                                                <td>{{ $list->display }}</td>
                                                <td>
                                                    <div class="col text-center">
                                                        <div class="btn-group">
                                                            <button class="btn btn-sm btn-primary btn-edit" title="Pilih"
                                                                data-id="{{ Crypt::encrypt($list->kd_jenis_prw) }}">
                                                                <i class="fas fa-hand-pointer"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
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

        <div class="modal fade" id="modalEditPegawai" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Data Mapping</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <form id="formEditPegawai">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Kode Perawatan</label>
                                        <input type="text" id="kd_jenis_prw" class="form-control" readonly>
                                    </div>

                                    <div class="form-group">
                                        <label>Nama Perawatan</label>
                                        <input type="text" id="nm_perawatan" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Kelas</label>
                                        <input type="text" id="kelas" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <hr>
                                    <h4>Data LOINC</h4>
                                    <div style="overflow-x:auto;">
                                        <table class="table table-bordered table-hover table-sm" id="example">
                                            <thead>
                                                <tr>
                                                    {{-- <th class="align-middle">Kode LOINC</th> --}}
                                                    <th class="align-middle">Nama Pemeriksaan</th>
                                                    <th class="align-middle">Permintaan Hasil</th>
                                                    <th class="align-middle">Spesimen</th>
                                                    <th class="align-middle">Tipe Hasil Pemeriksaan</th>
                                                    <th class="align-middle">Satuan</th>
                                                    <th class="align-middle">Metode</th>
                                                    <th class="align-middle">Display</th>
                                                    <th class="align-middle">Component</th>
                                                    <th class="align-middle">Pilih</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($dataLoinc as $item)
                                                    <tr>
                                                        <td>{{ $item->nama_pemeriksaan }}</td>
                                                        <td>{{ $item->permintaan_hasil }}</td>
                                                        <td>{{ $item->spesimen }}</td>
                                                        <td>{{ $item->tipe_hasil_pemeriksaan }}</td>
                                                        <td>{{ $item->satuan }}</td>
                                                        <td>{{ $item->metode_analisis }}</td>
                                                        <td>{{ $item->display }}</td>
                                                        <td>{{ $item->component }}</td>
                                                        <td class="text-center">
                                                            <input type="radio" name="kd_loinc" class="form-check-input"
                                                                value="{{ $item->kd_loinc }}">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <hr>
                                    <h4>Data Spesimen</h4>
                                    <div style="overflow-x:auto;">
                                        <table class="table table-bordered table-hover table-sm" id="example3">
                                            <thead>
                                                <tr>
                                                    <th class="align-middle">Kode Spesimen</th>
                                                    <th class="align-middle">Nama Spesimen</th>
                                                    <th class="align-middle">Pilih</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($dataSpecimen as $item)
                                                    <tr>
                                                        <td>{{ $item->kd_snomed }}</td>
                                                        <td>{{ $item->display }}</td>
                                                        <td class="text-center">
                                                            <input type="radio" name="kd_snomed" class="form-check-input"
                                                                value="{{ $item->kd_snomed }}">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">
                                    Simpan
                                </button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('get')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script>
        $(document).on('click', '.btn-edit', function() {
            let id = $(this).data('id');

            $.ajax({
                url: '/satusehat/mapinglab/' + id + '/edit',
                type: 'GET',
                success: function(res) {
                    console.log(res);

                    $('#kd_jenis_prw').val(res.kd_jenis_prw);
                    $('#nm_perawatan').val(res.nm_perawatan);
                    $('#kelas').val(res.kelas);

                    $('input[name="kd_loinc"]').prop('checked', false); // reset
                    $('input[name="kd_loinc"][value="' + res.kd_loinc + '"]')
                        .prop('checked', true);
                    $('input[name="kd_snomed"]').prop('checked', false); // reset
                    $('input[name="kd_snomed"][value="' + res.kd_snomed + '"]')
                        .prop('checked', true);

                    $('#modalEditPegawai').modal('show');
                }
            });
        });
    </script>

    <script>
        $('#formEditPegawai').on('submit', function(e) {
            e.preventDefault();

            let kdJenisPrw = $('#kd_jenis_prw').val();
            let kdLoinc = $('input[name="kd_loinc"]:checked').val();
            let kdSnomed = $('input[name="kd_snomed"]:checked').val();

            if (!kdLoinc || !kdSnomed) {
                Swal.fire('Perhatian', 'Pilih kode LOINC dan Spesimen terlebih dahulu', 'warning');
                return;
            }

            $.ajax({
                url: '{{ route('satuSehat.mapingLabUpdate') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT',
                    kd_jenis_prw: kdJenisPrw,
                    kd_loinc: kdLoinc,
                    kd_snomed: kdSnomed
                },
                success: function() {
                    Swal.fire('Berhasil', 'Data tersimpan', 'success')
                        .then(() => location.reload());
                },
                error: function(err) {
                    console.error(err);
                    Swal.fire('Error', err.responseJSON?.message ?? 'Server error', 'error');
                }
            });
        });
    </script>
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
    <script>
        $(function() {
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": false,
                "scrollY": false,
                "scrollX": true,
            });
            $('#example').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": false,
                "scrollY": "300px",
                "scrollX": false,
            });
            $('#example3').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": false,
                "scrollY": "300px",
                "scrollX": false,
            });
        });
    </script>
@endsection
