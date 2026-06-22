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
                            <div class="card_title">Data Mapping Vaksin</div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover table-sm" id="example2">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">Kode Perawatan</th>
                                            <th class="align-middle">Nama Perawatan</th>
                                            <th class="align-middle">Status Aktif</th>
                                            <th class="align-middle">Penjamin</th>
                                            <th class="align-middle">Unit</th>
                                            <th class="align-middle">Kode Barang</th>
                                            <th class="align-middle">Nama Barang</th>
                                            <th class="align-middle">Kode KFA</th>
                                            <th class="align-middle">Alasan Imunisasi</th>
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
                                                    <td>{{ $list->nm_poli }}</td>
                                                    <td>{{ $list->kode_barang }} </td>
                                                    <td>{{ $list->nama_barang }}</td>
                                                    <td>{{ $list->kfa }}</td>
                                                    <td>{{ $list->alasan }}</td>
                                                    <td>
                                                        <div class="col text-center">
                                                            <div class="btn-group">
                                                                <button class="btn btn-sm btn-primary btn-edit"
                                                                    title="Pilih"
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
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->

        <div class="modal fade" id="modalEditKfa" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Data Mapping</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <form method="POST" action="{{ route('satuSehat.mapingVaksinUpdate') }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Kode Perawatan</label>
                                        <input type="text" id="kd_jenis_prw" name="kd_jenis_prw" class="form-control"
                                            readonly>
                                    </div>

                                    <div class="form-group">
                                        <label>Nama Perawatan</label>
                                        <input type="text" id="nm_perawatan" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Penjamin</label>
                                        <input type="text" id="penjamin" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <hr>
                                    <h4>Data Vaksin</h4>
                                    <div style="overflow-x:auto;">
                                        <table class="table table-bordered table-hover table-sm" id="example3">
                                            <thead>
                                                <tr>
                                                    <th class="align-middle">Kode Barang</th>
                                                    <th class="align-middle">Nama Barang</th>
                                                    <th class="align-middle">Satuan</th>
                                                    <th class="align-middle">Expired</th>
                                                    <th class="align-middle">Status</th>
                                                    <th class="align-middle">Kode KFA</th>
                                                    <th class="align-middle">Pilih</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($masterBarang as $item)
                                                    <tr>
                                                        <td>{{ $item->kode_brng }}</td>
                                                        <td>{{ $item->nama_brng }}</td>
                                                        <td>{{ $item->kode_sat }}</td>
                                                        <td
                                                            class="{{ $item->expire == '0000-00-00' || $item->expire <= \Carbon\Carbon::now() ? 'text-danger' : '' }}">
                                                            {{ $item->expire }}</td>
                                                        <td class="text-center">
                                                            @if ($item->status == 1)
                                                                <span class="badge badge-success">Enable</span>
                                                            @else
                                                                <span class="badge badge-danger">Disable</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $item->kd_kfa }}</td>
                                                        <td class="text-center">
                                                            <input type="radio" name="kode_brng" class="form-check-input"
                                                                value="{{ $item->kode_brng }}">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <hr>
                                    <h4>Data Alasan Imunisasi</h4>
                                    <div style="overflow-x:auto;">
                                        <table class="table table-bordered table-hover table-sm">
                                            <thead>
                                                <tr>
                                                    <th class="align-middle">Kode</th>
                                                    <th class="align-middle">Nama Alasan</th>
                                                    <th class="align-middle">Pilih</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($masterAlasan as $item)
                                                    <tr>
                                                        <td>{{ $item->code }}</td>
                                                        <td>{{ $item->display }}</td>
                                                        <td class="text-center">
                                                            <input type="radio" name="kd_alasan" class="form-check-input"
                                                                value="{{ $item->id }}">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">
                                    Simpan
                                </button>
                            </div>
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
                url: '/satusehat/mapingvaksin/' + id + '/edit',
                type: 'GET',
                success: function(res) {
                    console.log(res);

                    $('#kd_jenis_prw').val(res.kd_jenis_prw);
                    $('#nm_perawatan').val(res.nm_perawatan);
                    $('#penjamin').val(res.png_jawab);

                    $('input[name="kd_kfa"]').prop('checked', false); // reset
                    $('input[name="kd_kfa"][value="' + res.kd_kfa + '"]')
                        .prop('checked', true);

                    $('#modalEditKfa').modal('show');
                }
            });
        });
    </script>

    <script>
        $('#formEditKfa').on('submit', function(e) {
            e.preventDefault();

            let kdJenisPrw = $('#kd_jenis_prw').val();
            let kdKfa = $('input[name="kd_kfa"]:checked').val();

            if (!kdKfa) {
                Swal.fire('Perhatian', 'Pilih kode KFA terlebih dahulu', 'warning');
                return;
            }

            $.ajax({
                url: '{{ route('satuSehat.mapingVaksinUpdate') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT',
                    kd_jenis_prw: kdJenisPrw,
                    kd_kfa: kdKfa
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
                "scrollY": "false",
                "scrollX": false,
            });
        });
    </script>
@endsection
