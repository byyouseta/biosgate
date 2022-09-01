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
                            <div class="card-title">Berkas Tambahan Pasien
                            </div>
                            <div class="float-right">
                                @can('vedika-upload')
                                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-default">
                                        <i class="fas fa-upload"></i> Unggah</a>
                                    </button>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless py-2">
                                <tbody>
                                    <tr>
                                        <td class="pt-1 pb-0">No.RM</td>
                                        <td class="pt-1 pb-0">: {{ $data->no_rkm_medis }}</td>
                                        <td class="pt-1 pb-0">NIK/No.Peserta BPJS</td>
                                        <td class="pt-1 pb-0">: {{ $data->no_ktp }} / {{ $data->no_peserta }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-0">Nama Pasien</td>
                                        <td class="pt-0 pb-0">: {{ $data->nm_pasien }}</td>
                                        <td class="pt-0 pb-0">Tgl.Registrasi</td>
                                        <td class="pt-0 pb-0">:
                                            {{ \Carbon\Carbon::parse($data->tgl_registrasi)->format('d-m-Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-0">JK/Umur</td>
                                        <td class="pt-0 pb-0">: {{ $data->jk == 'L' ? 'Laki-laki' : 'Perempuan' }} /

                                            {{ \Carbon\Carbon::parse($data->tgl_lahir)->diff(\Carbon\Carbon::now())->format('%y Th %m Bl %d Hr') }}
                                        </td>
                                        <td class="pt-0 pb-0">Jam Registrasi</td>
                                        <td class="pt-0 pb-0">: {{ $data->jam_reg }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-0">No.Rawat</td>
                                        <td class="pt-0 pb-0">: {{ $data->no_rawat }}</td>
                                        <td class="pt-0 pb-0">Dokter Poli</td>
                                        <td class="pt-0 pb-0">: {{ $data->nm_dokter }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pt-0 pb-3" style="width: 10%">Alamat</td>
                                        <td class="pt-0 pb-3" style="width: 40%">: {{ $data->almt_pj }}</td>
                                        <td class="pt-0 pb-3" style="width: 15%">Poli</td>
                                        <td class="pt-0 pb-3" style="width: 35%">: {{ $data->nm_poli }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table table-bordered table-hover">
                                <thead class="text-center">
                                    <tr>
                                        <th style="width: 5%">No</th>
                                        <th>Nama Berkas</th>
                                        {{-- <th>Keterangan</th> --}}
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($berkas as $index => $berkas)
                                        <tr>
                                            <td class="text-center">{{ ++$index }}</td>
                                            <td>{{ $berkas->nama }}</td>
                                            {{-- <td></td> --}}
                                            <td>
                                                <div class="col text-center">
                                                    <div class="btn-group">
                                                        {{-- <a href="{{ $path->base_url }}{{ $berkas->lokasi_file }}" --}}
                                                        <a href="/vedika/berkas/{{ Crypt::encrypt($berkas->lokasi_file) }}/view"
                                                            target="_blank" class="btn btn-info btn-sm"
                                                            data-toggle="tooltip" data-placement="bottom"
                                                            title="Lihat Berkas">
                                                            <i class="far fa-eye"></i>
                                                        </a>
                                                        <a href="/vedika/berkas/{{ Crypt::encrypt($berkas->lokasi_file) }}/delete"
                                                            class="btn btn-danger btn-sm delete-confirm @cannot('vedika-delete') disabled @endcannot"
                                                            data-toggle="tooltip" data-placement="bottom" title="Delete">
                                                            <i class="fas fa-ban"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="4">Belum ada berkas yang diunggah</td>
                                        </tr>
                                    @endforelse
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

    <div class="modal fade" id="modal-default">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="/vedika/berkas/store" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">Tambah Berkas Pasien</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- text input -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Nama Berkas</label>
                                    <input type="hidden" class="form-control" value="{{ $data->no_rawat }}"
                                        name="no_rawat" />
                                    <input type="hidden" class="form-control" value="{{ $data->tgl_registrasi }}"
                                        name="tgl_registrasi" />
                                    <select name="master_berkas" class="form-control select2" required>
                                        <option value="">Pilih</option>
                                        @foreach ($master as $master)
                                            <option value="{{ $master->kode }}-{{ $master->nama }}">
                                                {{ $master->nama }} </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('master_berkas_id'))
                                        <div class="text-danger">
                                            {{ $errors->first('master_berkas_id') }}
                                        </div>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label>File Berkas</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="customFile"
                                                name="file" required>
                                            <label class="custom-file-label" for="customFile">Pilih atau drop file
                                                disini</label>
                                        </div>
                                    </div>
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
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
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
    <!-- bs-custom-file-input -->
    <script src="{{ asset('template/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <script>
        $(function() {
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": false,
                "scrollY": "400px",
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
            });
        });
        //Date picker
        $('#tanggal').datetimepicker({
            format: 'YYYY-MM-DD'
        });
        $(function() {
            bsCustomFileInput.init();
            //Date picker
            $('#tanggal').datetimepicker({
                format: 'DD-MM-YYYY'
            });
        });
    </script>
@endsection
