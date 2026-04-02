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
                            <div class="card_title">Data Mapping Obat</div>
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
                                        <th class="align-middle">Kode Barang</th>
                                        <th class="align-middle">Nama Barang</th>
                                        <th class="align-middle">Satuan</th>
                                        <th class="align-middle">Nama Industri</th>
                                        <th class="align-middle">Nama Kategori</th>
                                        <th class="align-middle">Nama Golongan</th>
                                        <th class="align-middle">Status</th>
                                        <th class="align-middle">Mapping KFA</th>
                                        <th class="align-middle">Kode Medication</th>
                                        <th class="align-middle">Kode UCUM</th>
                                        <th class="align-middle">Kode Ingredient</th>
                                        <th class="align-middle">Kode Route</th>

                                        <th class="align-middle">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($data)
                                        @foreach ($data as $list)
                                            <tr>
                                                <td>{{ $list->kode_brng }}</td>
                                                <td>{{ $list->nama_brng }}</td>
                                                <td>{{ $list->satuan }}</td>
                                                <td>{{ $list->nama_industri }}</td>
                                                <td>{{ $list->nama_kategori }}</td>
                                                <td>{{ $list->nama_golongan }}</td>
                                                <td class="text-center">
                                                    @if ($list->status == '1')
                                                        <span class="badge badge-success">Enable</span>
                                                    @else
                                                        <span class="badge badge-danger">Disable</span>
                                                    @endif
                                                </td>
                                                <td>{{ $list->id_ihs }}</td>
                                                <td>{{ $list->medication_display }}</td>
                                                <td>{{ $list->ucum_name }}</td>
                                                <td>{{ $list->ingredient_display }}</td>
                                                <td>{{ $list->route_display }}</td>
                                                <td>
                                                    <div class="col text-center">
                                                        <div class="btn-group">
                                                            <button class="btn btn-sm btn-primary btn-edit" title="Pilih"
                                                                data-id="{{ Crypt::encrypt($list->kode_brng) }}">
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

                    <form id="formEditPegawai" action="{{ route('satuSehat.mapingObatUpdate') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Kode Barang</label>
                                        <input type="text" id="kd_barang" name="kode_brng" class="form-control" readonly>
                                    </div>

                                    <div class="form-group">
                                        <label>Nama Barang</label>
                                        <input type="text" id="nm_barang" class="form-control" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>Satuan</label>
                                        <input type="text" id="satuan" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Nama Industri</label>
                                        <input type="text" id="nama_industri" class="form-control" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>Nama Kategori</label>
                                        <input type="text" id="nama_kategori" class="form-control" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>Nama Golongan</label>
                                        <input type="text" id="nama_golongan" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <hr>
                                    <h4>Data KFA</h4>

                                    <div style="overflow-x:auto;">
                                        <table class="table table-bordered table-hover table-sm" id="example6">
                                            <thead>
                                                <tr>
                                                    <th class="align-middle">Kode KFA</th>
                                                    <th class="align-middle">Display</th>
                                                    <th class="align-middle">Pilih</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($dataKfa as $item)
                                                    <tr>
                                                        <td>{{ $item->kd_kfa }}</td>
                                                        <td>{{ $item->display }}</td>
                                                        <td class="text-center">
                                                            <input type="radio" name="kd_kfa" class="form-check-input"
                                                                value="{{ $item->kd_kfa }}">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-12">
                                    {{-- <hr>
                                    <h4>Data Medication Form</h4>
                                    <div style="overflow-x:auto;">
                                        <table class="table table-bordered table-hover table-sm" id="example">
                                            <thead>
                                                <tr>
                                                    <th class="align-middle">Kode Medication</th>
                                                    <th class="align-middle">Display</th>
                                                    <th class="align-middle">Coding System</th>
                                                    <th class="align-middle">Pilih</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($dataMedicationForm as $item)
                                                    <tr>
                                                        <td>{{ $item->kd_medication }}</td>
                                                        <td>{{ $item->display }}</td>
                                                        <td>{{ $item->coding_system }}</td>
                                                        <td class="text-center">
                                                            <input type="radio" name="kd_loinc"
                                                                class="form-check-input"
                                                                value="{{ $item->kd_medication }}">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <hr>
                                    <h4>Data Ucum</h4>
                                    <div style="overflow-x:auto;">
                                        <table class="table table-bordered table-hover table-sm" id="example3">
                                            <thead>
                                                <tr>
                                                    <th class="align-middle">Kode Ucum</th>
                                                    <th class="align-middle">Nama Ucum</th>
                                                    <th class="align-middle">Sinonim</th>
                                                    <th class="align-middle">Coding System</th>
                                                    <th class="align-middle">Pilih</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($dataUcum as $item)
                                                    <tr>
                                                        <td>{{ $item->kd_ucum }}</td>
                                                        <td>{{ $item->name }}</td>
                                                        <td>{{ $item->sinonim }}</td>
                                                        <td>{{ $item->system }}</td>
                                                        <td class="text-center">
                                                            <input type="radio" name="kd_ucum" class="form-check-input"
                                                                value="{{ $item->kd_ucum }}">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div> --}}
                                    <hr>
                                    <h4>Data Ingredient</h4>
                                    <div style="overflow-x:auto;">
                                        <table class="table table-bordered table-hover table-sm" id="example4">
                                            <thead>
                                                <tr>
                                                    <th class="align-middle">Kode Ingredient</th>
                                                    <th class="align-middle">Display</th>
                                                    <th class="align-middle">System</th>
                                                    <th class="align-middle">Pilih</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($dataIngredient as $item)
                                                    <tr>
                                                        <td>{{ $item->kd_ingredient }}</td>
                                                        <td>{{ $item->display }}</td>
                                                        <td>{{ $item->system }}</td>
                                                        <td class="text-center">
                                                            <input type="radio" name="kd_ingredient"
                                                                class="form-check-input"
                                                                value="{{ $item->kd_ingredient }}">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    {{-- <hr>
                                    <h4>Data Route</h4>
                                    <div style="overflow-x:auto;">
                                        <table class="table table-bordered table-hover table-sm" id="example5">
                                            <thead>
                                                <tr>
                                                    <th class="align-middle">Kode Route</th>
                                                    <th class="align-middle">Display</th>
                                                    <th class="align-middle">Keterangan</th>
                                                    <th class="align-middle">Pilih</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($dataRoute as $item)
                                                    <tr>
                                                        <td>{{ $item->kd_route }}</td>
                                                        <td>{{ $item->display }}</td>
                                                        <td>{{ $item->keterangan }}</td>
                                                        <td class="text-center">
                                                            <input type="radio" name="kd_route"
                                                                class="form-check-input" value="{{ $item->kd_route }}">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div> --}}
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
                url: '/satusehat/mapingobat/' + id + '/edit',
                type: 'GET',
                success: function(res) {
                    console.log(res);

                    $('#kd_barang').val(res.kode_brng);
                    $('#nm_barang').val(res.nama_brng);
                    $('#satuan').val(res.satuan);
                    $('#nama_industri').val(res.nama_industri);
                    $('#nama_kategori').val(res.nama_kategori);
                    $('#nama_golongan').val(res.nama_golongan);
                    $('#kd_kfa').val(res.id_ihs);

                    $('input[name="kd_loinc"]').prop('checked', false); // reset
                    $('input[name="kd_loinc"][value="' + res.kd_loinc + '"]')
                        .prop('checked', true);
                    $('input[name="kd_ucum"]').prop('checked', false); // reset
                    $('input[name="kd_ucum"][value="' + res.kd_ucum + '"]')
                        .prop('checked', true);
                    $('input[name="kd_ingredient"]').prop('checked', false); // reset
                    setTimeout(() => {
                        $('input[name="kd_ingredient"][value="' + res.kd_ingredient + '"]')
                            .prop('checked', true);
                    }, 500);
                    $('input[name="kd_route"]').prop('checked', false); // reset
                    $('input[name="kd_route"][value="' + res.kd_route + '"]')
                        .prop('checked', true);

                    $('#modalEditPegawai').modal('show');
                }
            });
        });
    </script>

    {{-- <script>
        $('#formEditPegawai').on('submit', function(e) {
            e.preventDefault();

            let kdBarang = $('#kd_barang').val();
            let kdKfa = $('input[name="kd_kfa"]:checked').val();

            let kdIngredient = $('input[name="kd_ingredient"]:checked').val();
            // let kdRoute = $('input[name="kd_route"]:checked').val();
            // let kdLoinc = $('input[name="kd_loinc"]:checked').val();
            // let kdUcum = $('input[name="kd_ucum"]:checked').val();

            if (!kdIngredient || !kdKfa) {
                Swal.fire('Perhatian', 'Pilih kode KFA, dan Ingredient terlebih dahulu',
                    'warning');
                return;
            }

            $.ajax({
                url: '{{ route('satuSehat.mapingObatUpdate') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT',
                    kode_brng: kdBarang,
                    kd_kfa: kdKfa,
                    kd_ingredient: kdIngredient,
                    // kd_loinc: kdLoinc,
                    // kd_ucum: kdUcum,
                    // kd_route: kdRoute
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
    </script> --}}
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
                "responsive": false
            });
            $('#example4').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": false
            });
            $('#example5').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": false
            });
            $('#example6').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": false
            });
        });
    </script>
@endsection
