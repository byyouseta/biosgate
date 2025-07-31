@extends('layouts.master')

@section('head')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('template/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('template/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    @php
                        if (!empty(Request::get('tanggal'))) {
                            $tanggal = Request::get('tanggal') . '-15';
                            $tanggal = new \Carbon\Carbon($tanggal);
                        } else {
                            $tanggal = \Carbon\Carbon::now();
                        }
                    @endphp
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><strong>{{ session('anak') }}</strong></h3>
                            <form action="{{ route('berkasrm.bookingMjkn') }}" method="GET">
                                <div class="d-flex float-right">
                                    <input type="text" class="form-control datetimepicker-input mr-2 ml-2"
                                        id="tanggal" data-target="#tanggal" data-toggle="datetimepicker"
                                        name="tanggal" autocomplete="off" value="{{ $tanggal }}"
                                        style="max-width: 130px">
                                    <button type="submit" class="btn btn-info btn-block btn-sm"
                                        style="max-width: 120px"><i class="fas fa-search"></i> Tampilkan</button>
                                </div>
                            </form>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table-bordered table-sm table-hover" style="width: 100%;" id="example">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="All"></th>
                                            <th>No.Booking</th>
                                            <th>Nama Pasien</th>
                                            <th style="width: 10%;">No.RM</th>
                                            <th style="width: 10%;">No.Reg</th>
                                            <th>Klinik</th>
                                            <th>Dokter</th>
                                            <th>Tgl Periksa</th>
                                            <th style="width: 10%;">Status</th>
                                            <th style="width: 5%;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($data as $book)
                                            <tr>
                                                <td class="text-center"><input type="checkbox" name="no_pasien[]" value="{{ $book->norm }}"></td>
                                                <td>{{ $book->nobooking }}</td>
                                                <td>{{ $book->nm_pasien }}</td>
                                                <td>{{ $book->norm }}</td>
                                                <td>{{ $book->nomorantrean }}</td>
                                                <td>{{ $book->nm_poli }}</td>
                                                <td>{{ $book->nm_dokter }}</td>
                                                <td>{{ \Carbon\Carbon::parse($book->tanggalperiksa)->format('Y-m-d') }}</td>
                                                <td>{{ $book->status }}</td>
                                                <td class="text-center"><a href="#" id="kirimPesan" data-id="{{ Crypt::encrypt($book->norm) }}" data-toggle="tooltip" data-placement="bottom" title="Kirim Pesan"><i class="fab fa-whatsapp text-success text-bold btn "></i></a></td>
                                                {{-- <td>{{ $book->limit_reg = 1? 'Online':'offline' }}</td> --}}
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No logs found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <button type="button" class="btn btn-success" id="btnOpenModal"><i class="fab fa-whatsapp"></i> Pesan Blast</button>
                        </div>
                    </div>

                    {{-- Modal Pilih Template --}}
                    <div class="modal fade" id="templateModal" tabindex="-1" aria-labelledby="templateModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <form method="POST" action="{{ route('berkasrm.bookingMKirimBlast') }}">
                            @csrf
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Kirim Pesan Blast</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="kirim_template_blast">Template</label>
                                            <input type="hidden" name="pasien_ids" id="pasien_ids">
                                            <input type="hidden" name="tgl_periksa" value="{{ $tanggal }}">
                                            <select name="template_id" id="kirim_template_blast" class="form-control" required>
                                                @foreach ($template as $listTemplate)
                                                    <option value="{{ Crypt::encrypt($listTemplate->id) }}" {{ $listTemplate->default == TRUE?'selected':'' }}>{{ $listTemplate->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Preview Template Pesan</label>
                                            <textarea id="template_pesan_blast" rows="5" class="form-control"></textarea>
                                            @if ($errors->has('pesan'))
                                                <div class="text-danger">
                                                    {{ $errors->first('pesan') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Kirim</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
        <!-- /.modal tombol aksi kirim pesan blast -->
        <div class="modal fade" id="kirim_modal">
            <div class="modal-dialog">
                <form action="{{ route('berkasrm.bookingMKirimPesan') }}" method="POST">
                    @csrf
                    <div class="modal-content ">
                        <div class="modal-header">
                            <h4 class="modal-title">Kirim Pesan</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>No.Rekam Medik</label>
                                <input type="hidden" class="form-control" name="tgl_periksa" value="{{ $tanggal }}">
                                <input type="text" class="form-control" name="no_rm" required id="kirim_no_rm">
                                @if ($errors->has('no_rm'))
                                    <div class="text-danger">
                                        {{ $errors->first('no_rm') }}
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>Nama Pasien</label>
                                <input type="text" class="form-control" name="nama" required id="kirim_nama">
                                @if ($errors->has('nama'))
                                    <div class="text-danger">
                                        {{ $errors->first('nama') }}
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>No.Telepon</label>
                                <input type="text" class="form-control" name="no_telp" required id="kirim_no_telp">
                                @if ($errors->has('no_telp'))
                                    <div class="text-danger">
                                        {{ $errors->first('no_telp') }}
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>Template Pesan</label>
                                <select name="template" id="kirim_template" class="form-control">
                                    @foreach ($template as $listTemplate)
                                        <option value="{{ Crypt::encrypt($listTemplate->id) }}" {{ $listTemplate->default == TRUE?'selected':'' }}>{{ $listTemplate->nama }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('template'))
                                    <div class="text-danger">
                                        {{ $errors->first('template') }}
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>Preview Template Pesan</label>
                                <textarea id="template_pesan" rows="5" class="form-control"></textarea>
                                @if ($errors->has('pesan'))
                                    <div class="text-danger">
                                        {{ $errors->first('pesan') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Kirim</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@section('get')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('body').on('click', '#kirimPesan', function(event) {
                event.preventDefault();
                var id = $(this).data('id');
                console.log(id);

                $.get('/berkasrm/booking/' + id + '/pasien', function(data) {
                    // alert(data);
                    console.log(data);
                    $('#userCrudModal').html("Kirim Pesan");
                    $('#submit').val("Simpan");
                    // Tampilkan modal
                    $('#kirim_modal').modal('show');
                    // $("input[name='default'][value='" + data.default + "']").prop('checked', true);
                    $('#kirim_no_rm').val(data.no_rkm_medis);
                    $('#kirim_nama').val(data.nm_pasien);
                    $('#kirim_no_telp').val(data.no_tlp);
                })
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#kirim_template').on('change', function () {
                var id = $(this).val();

                // Clear textarea dulu
                $('#template_pesan').val('Memuat template...');

                $.ajax({
                    url: '/pesan/template/' + id,
                    method: 'GET',
                    success: function (data) {
                        $('#template_pesan').val(data.pesan);
                    },
                    error: function () {
                        $('#template_pesan').val('Gagal mengambil template');
                    }
                });
            });

            // Trigger saat load pertama jika ingin default muncul
            $('#kirim_template').trigger('change');
        });
        $(document).ready(function () {
            $('#kirim_template_blast').on('change', function () {
                var id = $(this).val();

                // Clear textarea dulu
                $('#template_pesan_blast').val('Memuat template...');

                $.ajax({
                    url: '/pesan/template/' + id,
                    method: 'GET',
                    success: function (data) {
                        $('#template_pesan_blast').val(data.pesan);
                    },
                    error: function () {
                        $('#template_pesan_blast').val('Gagal mengambil template');
                    }
                });
            });

            // Trigger saat load pertama jika ingin default muncul
            $('#kirim_template_blast').trigger('change');
        });
    </script>

@endsection

@endsection
@section('plugin')
    {{-- <!-- jQuery -->
    <script src="{{ asset('template/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('template/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script> --}}
    <!-- DataTables  & Plugins -->
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
     <!-- Tempusdominus|Datetime Bootstrap 4 -->
     <link rel="stylesheet"
     href="{{ asset('template/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
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
            });
            $('#example').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "order": [[0, "asc"]],
                "info": true,
                "autoWidth": false,
                "responsive": false,
            });
        });
        //Date picker
        $('#tanggal,#tanggal_hapus').datetimepicker({
            format: 'YYYY-MM-DD'
        });

        $('#All').on('change', function() {
            $('input[name="no_pasien[]"]').prop('checked', this.checked);
        });

        $('#btnOpenModal').on('click', function () {
            let checkedData = [];
            $('input[name="no_pasien[]"]:checked').each(function () {
                checkedData.push($(this).val());
            });

            if (checkedData.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops!',
                    text: 'Silakan pilih minimal satu pasien terlebih dahulu.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Simpan ke input hidden
            $('#pasien_ids').val(checkedData.join(','));

            // Tampilkan modal
            $('#templateModal').modal('show');
        });
    </script>
@endsection
