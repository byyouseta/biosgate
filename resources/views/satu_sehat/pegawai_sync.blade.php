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
                            <div class="card_title">Nakes Sehat Sync
                                <div class="float-right">
                                    <a href="/satusehat/nakessehat/sync" class="btn btn-info btn-flat btn-sm"><i
                                            class="fas fa-sync-alt"></i> Sync Data Nama Pegawai</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover table-sm display nowrap" id="example2">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">NIP</th>
                                            <th class="align-middle">Nama Pegawai</th>
                                            <th class="align-middle">Status Aktif</th>
                                            <th class="align-middle">No KTP</th>
                                            <th class="align-middle">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($data)
                                            @foreach ($data as $list)
                                                <tr>
                                                    <td>{{ $list->nik }}</td>
                                                    <td>{{ $list->nama }}</td>
                                                    <td>{{ $list->stts_aktif }}</td>
                                                    <td>{{ $list->no_ktp }}</td>
                                                    <td>
                                                        <div class="col text-center">
                                                            <div class="btn-group">
                                                                <button class="btn btn-sm btn-warning btn-edit"
                                                                    data-id="{{ Crypt::encrypt($list->id) }}">
                                                                    <i class="fas fa-user-edit"></i>
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
    </section>

    <div class="modal fade" id="modalEditPegawai" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Pegawai</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="formEditPegawai">
                    @csrf
                    <input type="hidden" id="pegawai_id">

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" id="nama" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>NIP(Nomor Induk Pegawai)</label>
                            <input type="text" id="nik" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>No KTP</label>
                            <input type="text" id="no_ktp" class="form-control">
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

@endsection
@section('get')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script>
        $(document).on('click', '.btn-edit', function() {
            let id = $(this).data('id');

            $.ajax({
                url: '/satusehat/nakessehat/' + id + '/edit',
                type: 'GET',
                success: function(res) {
                    console.log(res);

                    $('#pegawai_id').val(res.id);
                    $('#nama').val(res.nama);
                    $('#nik').val(res.nik);
                    $('#no_ktp').val(res.no_ktp);

                    $('#modalEditPegawai').modal('show');
                }
            });
        });
    </script>
    <script>
        $('#formEditPegawai').submit(function(e) {
            e.preventDefault();

            let id = $('#pegawai_id').val();

            $.ajax({
                url: '{{ route('satuSehat.pegawaiSyncUpdate', '') }}/' + id,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT',
                    nama: $('#nama').val(),
                    nik: $('#nik').val(),
                    no_ktp: $('#no_ktp').val(),
                },
                success: function(res) {
                    $('#modalEditPegawai').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data pegawai berhasil diperbarui',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat menyimpan data'
                    });
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
        });
    </script>
@endsection
