@extends('layouts.master')

@section('head')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('template/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- Tempusdominus|Datetime Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('template/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('template/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-6">
                    <form method="POST" action="/pesan/kirim">
                        @csrf
                        <div class="card">
                            {{-- <div class="card-header">
                            Kirim Pesan
                        </div> --}}
                            <div class="card-body">
                                <div class="form-group">
                                    <label>No Penerima <small>(Dalam format 628XXX)</small></label>
                                    {{-- <input type="text" class="form-control" name="penerima" required /> --}}
                                    <select name="penerima" id="penerimaSelect" class="form-control select2">

                                    </select>
                                    @if ($errors->has('penerima'))
                                        <div class="text-danger">
                                            {{ $errors->first('penerima') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Pesan</label>
                                    <textarea name="pesan" class="form-control" rows="4" style="max-height: 500px; overflow-y: auto;" required>{{ $template->where('default',TRUE)->first()->pesan ?? '' }}</textarea>
                                    @if ($errors->has('pesan'))
                                        <div class="text-danger">
                                            {{ $errors->first('pesan') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Kirim</button>
                            </div>
                        </div>
                    </form>

                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Placeholder Template Pesan</h3>
                        </div>
                        <div class="card-body">
                            <p>Gunakan kode berikut di dalam pesan Anda. Sistem akan otomatis menggantinya saat mengirim pesan:</p>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <code>@nama_pasien</code> → Nama pasien (contoh: DWI KISWATI)
                                </li>
                                <li class="list-group-item">
                                    <code>@no_rm</code> → Nomor rekam medis (contoh: 005345)
                                </li>
                                <li class="list-group-item">
                                    <code>@tgl_kunjungan</code> → Tanggal kunjungan dalam format lokal (contoh: Rabu, 25 Juni 2025)
                                </li>
                                <li class="list-group-item">
                                    <code>@nama_poli</code> → Nama poli (contoh: POLI THT)
                                </li>
                                <li class="list-group-item">
                                    <code>@nama_dokter</code> → Nama dokter (contoh: dr. HERMAWAN SURYA DHARMA, Sp.THT)
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-6">

                        <div class="card">
                            <div class="card-header">
                                Template Pesan
                                <div class="float-right">
                                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-default">Tambah</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-hover table-sm" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <td style="width: 25%;">Nama Template</td>
                                            <td>Isi</td>
                                            <td style="width: 15%;">Aksi</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($template as $list)
                                            <tr>
                                                <td>{{ $list->nama }}</td>
                                                <td>{{ $list->pesan }}</td>
                                                <td>
                                                    <a href="{{ route('wa.defaultTemplate', Crypt::encrypt($list->id)) }}" ><span ><i class="fas fa-check-circle {{ $list->default == '1' ? 'text-success':'text-secondary' }}"></i></span></a>
                                                    <a href="#" id="editKomponen" data-id="{{ Crypt::encrypt($list->id) }}" data-toggle="tooltip" data-placement="bottom" title="Edit"><span ><i class="fas fa-pencil-alt text-warning"></i></span></a>
                                                    <a href="{{ route('wa.deleteTemplate', Crypt::encrypt($list->id)) }}" ><span ><i class="fas fa-times-circle text-danger"></i></span></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    {{-- Modal Add --}}
    <div class="modal fade" id="modal-default">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('wa.simpanTemplate') }}">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">Tambah Template</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Nama Template</label>
                                    <input type="text" class="form-control" name="nama" required>
                                    @if ($errors->has('nama'))
                                        <div class="text-danger">
                                            {{ $errors->first('nama') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Template Pesan</label>
                                    <textarea name="pesan" id="" cols="30" rows="10" class="form-control"></textarea>
                                    @if ($errors->has('pesan'))
                                        <div class="text-danger">
                                            {{ $errors->first('pesan') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Set Default Pesan</label>
                                    <div>
                                        <label>
                                            <input type="radio" name="default" value="0" checked> Tidak
                                        </label>
                                        <label class="ms-3">
                                            <input type="radio" name="default" value="1" > Ya
                                        </label>
                                    </div>

                                    @if ($errors->has('default'))
                                        <div class="text-danger">
                                            {{ $errors->first('default') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Tutup</button>
                        <button type="Submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal tombol aksi edit komponen -->
    <div class="modal fade" id="edit_modal">
        <div class="modal-dialog">
            <form action="{{ route('wa.updateTemplate') }}" method="POST">
                @csrf
                <div class="modal-content ">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Template</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Template</label>
                            <input type="hidden" class="form-control" name="template_id" id="template_id">
                            <input type="text" class="form-control" name="nama" required id="template_nama">
                            @if ($errors->has('nama'))
                                <div class="text-danger">
                                    {{ $errors->first('nama') }}
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label>Template Pesan</label>
                            <textarea name="pesan" id="template_pesan" cols="30" rows="10" class="form-control"></textarea>
                            @if ($errors->has('pesan'))
                                <div class="text-danger">
                                    {{ $errors->first('pesan') }}
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label>Set Default Pesan</label>
                            <div>
                                <label>
                                    <input type="radio" name="default" value="0" checked id="template_default_0"> Tidak
                                </label>
                                <label class="ms-3">
                                    <input type="radio" name="default" value="1" id="template_default_1"> Ya
                                </label>
                            </div>

                            @if ($errors->has('default'))
                                <div class="text-danger">
                                    {{ $errors->first('default') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- /.modal -->
@endsection
@section('get')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('body').on('click', '#editKomponen', function(event) {
                event.preventDefault();
                var id = $(this).data('id');
                console.log(id);

                $.get('/pesan/template/' + id, function(data) {
                    // alert(data);
                    // console.log(data);
                    $('#userCrudModal').html("Edit Template");
                    $('#submit').val("Simpan");
                    // Tampilkan modal
                    $('#edit_modal').modal('show');
                    $("input[name='default'][value='" + data.default + "']").prop('checked', true);
                    $('#template_pesan').val(data.pesan);
                    $('#template_nama').val(data.nama);
                    $('#template_id').val(data.id);
                })
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
    <!-- Select2 -->
    <script src="{{ asset('template/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(function() {
            $('#example2').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": false,
                "ordering": false,
                "info": false,
                "autoWidth": false,
                "responsive": false,
                "scrollY": "300px",
                "scrollX": false,
            });
            $('#example').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": false,
                "ordering": false,
                "info": false,
                "autoWidth": false,
                "responsive": false,
                "scrollY": "300px",
                "scrollX": false,
            });
        });
        //Date picker
        $('#tanggal').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#penerimaSelect').select2({
                placeholder: 'Pilih atau ketik nomor telepon..',
                tags: true, // aktifkan input manual
                minimumInputLength: 2, // mulai pencarian setelah 2 karakter
                ajax: {
                    url: '/pesan/getpenerima', // route Laravel kamu
                    //url: '{{ secure_url("/pesan/getpenerima") }}', // route Laravel kamu
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term // term pencarian
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.no_tlp,
                                    text: item.nm_pasien + ' (' + item.no_rkm_medis + ') - ' + item.no_tlp// tampil di dropdown
                                };
                            })
                        };
                    },
                    cache: true
                },
                createTag: function (params) {
                    var term = $.trim(params.term);
                    if (term === '') {
                        return null;
                    }
                    return {
                        id: term,
                        text: term,
                        newTag: true
                    };
                }
            });
        });
    </script>

@endsection
