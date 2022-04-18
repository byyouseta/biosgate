@extends('layouts.master')

<!-- isi bagian konten -->
<!-- cara penulisan isi section yang panjang -->
@section('head')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('template/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('template/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <!-- Tempusdominus|Datetime Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('template/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="float-left">
                        <h4 class="card-title">Identitas Pasien</h4>
                    </div>
                    <div class="float-right">
                        <a href="{{ URL::previous() }}" class="btn btn-secondary btn-sm">Kembali</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>No LapId</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="lapId" value="{{ $pasien->lapId }}"
                                        readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Nama Lengkap Pasien</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="namaPasien"
                                        value="{{ $pasien->namaPasien }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>No RM / No Rawat</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="noRM" value="{{ $pasien->noRm }}"
                                        readonly>
                                    <input type="text" class="form-control" name="noRawat"
                                        value="{{ $pasien->noRawat }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Resep Pasien</h4>
                        </div>
                        <div class="card-body">

                            <table class="table table-bordered" id="example">
                                <thead class="text-center">
                                    <tr>
                                        <th colspan="6">Data SIMRS</th>
                                    </tr>
                                    <tr>
                                        {{-- <th>Kode ICD</th> --}}
                                        <th class="align-middle">No Resep</th>
                                        <th class="align-middle">Waktu Resep</th>

                                        <th class="align-middle">Nama Obat</th>
                                        <th class="align-middle">Jumlah</th>
                                        <th class="align-middle">Aturan Pakai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $noResep = '';
                                    @endphp
                                    @foreach ($dataobat as $obat)
                                        <tr>
                                            <td>
                                                @if ($noResep != $obat->no_resep)
                                                    {{ $obat->no_resep }}
                                                    @php
                                                        $noResep = $obat->no_resep;
                                                    @endphp
                                                @endif
                                            </td>
                                            <td>{{ $obat->tgl_perawatan }} {{ $obat->jam }}
                                            <td>
                                                {{ $obat->kode_brng }}
                                                {{ $obat->nama_brng }}
                                            </td>
                                            <td>
                                                {{ $obat->jml }}
                                            </td>
                                            <td>
                                                {{ $obat->aturan_pakai }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title mt-2">Laporan Terapi</h3>
                            @can('terapi-create')
                                <div class="float-right">
                                    <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-terapi"
                                        type="button">
                                        <i class="fa fa-plus-circle"></i> Tambah</a>
                                    </button>
                                </div>
                            @endcan
                        </div>
                        <!-- /.box-header -->
                        <div class="card-body">
                            <table class="table table-bordered table-hover" id="tableTerapi">
                                <thead>
                                    <tr class="text-center">
                                        <td>ID Lap</td>
                                        <td>Terapi ID</td>
                                        <td>Nama</td>
                                        <td>Jumlah</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pasien->TerapiLap as $data)
                                        <tr>
                                            <td class="text-center">
                                                <a href="/rsonline/pasienterlapor/editterapi/{{ Crypt::encrypt($data->lapTerapiId) }}"
                                                    class="btn btn-warning btn-sm @cannot('terapi-edit') disabled @endcannot"
                                                    data-toggle="tooltip" data-placement="bottom" title="Edit">
                                                    {{ $data->lapTerapiId }}
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                            </td>
                                            <td>{{ $data->TerapiId }}</td>
                                            <td>{{ $data->desc }}</td>
                                            <td>{{ $data->jumlah }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <!-- text input -->
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- Modal Komorbid --}}

    {{-- Modal terapi --}}
    <div class="modal fade" id="modal-terapi">
        <div class="modal-dialog">
            <div class="modal-content">
                <form role="form" action="/rsonline/pasienterlapor/terapi/{{ $pasien->lapId }}" method="post">
                    {{ csrf_field() }}
                    <div class="modal-header">
                        <h4 class="modal-title">Tambah Komorbid</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- text input -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Terapi</label>
                                    <select name="terapi" class="form-control select2" required>
                                        @foreach ($terapi as $terapi)
                                            <option value="{{ $terapi->id }}-{{ $terapi->nama }}">
                                                {{ $terapi->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('terapi'))
                                        <div class="text-danger">
                                            {{ $errors->first('terapi') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Jumlah</label>
                                    <input type="number" class="form-control" name="jumlah" placeholder="Jumlah dosis"
                                        required />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Tutup</button>
                        <button type="Submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    {{-- Pemeriksaan Lab --}}
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
    <script src="{{ asset('template/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}">
    </script>
    <!-- Select2 -->
    <script src="{{ asset('template/plugins/select2/js/select2.full.min.js') }}"></script>
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
                "scrollY": "300px",
                "scrollX": false,
            });
            //Initialize Select2 Elements
            $('.select2').select2();
        });
        //Date picker
        $('#tgl_hasil').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    </script>
@endsection
