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
                        <a href="/rsonline/pasienterlapor" class="btn btn-secondary btn-sm">Kembali</a>
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
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Diagnosa Pasien</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <table class="table table-bordered">
                                <thead class="text-center">
                                    <tr>
                                        <th colspan="5">Data SIMRS</th>
                                    </tr>
                                    <tr>
                                        <th>Kode ICD</th>
                                        <th class="align-middle">Nama Penyakit</th>
                                        <th class="align-middle">Prioritas</th>
                                        <th class="align-middle">Level Diagnosa</th>
                                        <th class="align-middle">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($diagnosa as $diagnosa)
                                        <form method="POST"
                                            action="/rsonline/pasienterlapor/diagnosa/{{ $pasien->lapId }}">
                                            @csrf
                                            <tr>
                                                <td>{{ $diagnosa->kd_penyakit }}
                                                    <input type="hidden" name="kd_diagnosa"
                                                        value="{{ $diagnosa->kd_penyakit }}" />
                                                </td>
                                                <td>{{ $diagnosa->nm_penyakit }}
                                                    <input type="hidden" name="nama_diagnosa"
                                                        value="{{ $diagnosa->nm_penyakit }}" />
                                                </td>
                                                <td>
                                                    {{ $diagnosa->prioritas }}
                                                </td>
                                                <td>
                                                    <select class="form-control form-control-sm" name="levelDiagnosa"
                                                        required>
                                                        <option value="1">Primary Diagnosa</option>
                                                        <option value="2">Secondary Diagnosa</option>
                                                    </select>
                                                </td>
                                                <td><button type="submit"
                                                        class="btn btn-sm btn-success {{ \App\DiagnosaLap::DiagnosaCek($diagnosa->kd_penyakit)->count() > 0 ? 'disabled' : '' }}"
                                                        data-toggle="tooltip" data-placement="bottom"
                                                        title="Tambah Laporan"><i class="fa fa-plus-circle"></i></button>
                                                </td>
                                            </tr>
                                        </form>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="col-6">
                            <table class="table table-bordered">
                                <thead class="text-center">
                                    <tr>
                                        <th colspan="4">Data Terlapor</th>
                                    </tr>
                                    <tr>
                                        <th class="align-middle">Id Lap</th>
                                        <th class="align-middle">Diagnosa Level</th>
                                        <th class="align-middle">Kode ICD</th>
                                        <th class="align-middle">Nama Penyakit</th>
                                        {{-- <th>Aksi</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pasien->diagnosalap as $lapdiagnosa)
                                        <tr>
                                            <td>{{ $lapdiagnosa->lapDiagnosaId }}</td>
                                            <td>{{ $lapdiagnosa->diagnosaLevelId }}</td>
                                            <td>{{ $lapdiagnosa->diagnosaId }}</td>
                                            <td>{{ $lapdiagnosa->namaDiagnosa }}</td>
                                            {{-- <td>Aksi</td> --}}
                                        </tr>
                                    @endforeach
                                    <tr>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-7">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Laporan Komorbid</h3>
                            <div class="float-right">
                                <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-komorbid"
                                    type="button">
                                    <i class="fa fa-plus-circle"></i> Tambah</a>
                                </button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="card-body">
                            <table class="table table-bordered table-hover" id="example">
                                <thead>
                                    <tr class="text-center">
                                        <td>ID Lap</td>
                                        <td>Komorbid ID</td>
                                        <td>Keterangan</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pasien->KomorbidLap as $data)
                                        <tr>
                                            <td>
                                                <a href="/rsonline/pasienterlapor/editkomorbid/{{ Crypt::encrypt($data->lapKomorbidId) }}"
                                                    class="btn btn-warning btn-sm" data-toggle="tooltip"
                                                    data-placement="bottom" title="Edit">
                                                    {{ $data->lapKomorbidId }}<i class="fas fa-pencil-alt"></i>
                                                </a>
                                            </td>
                                            <td>{{ $data->komorbidId }}</td>
                                            <td>{{ $data->desc }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <!-- text input -->
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
                <div class="col-5">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Laporan Vaksinasi</h3>
                            <div class="float-right">
                                <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-vaksinasi"
                                    type="button">
                                    <i class="fa fa-plus-circle"></i> Tambah</a>
                                </button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="card-body">
                            <table class="table table-bordered table-hover" id="tableVaksinasi">
                                <thead>
                                    <tr class="text-center">
                                        <td>ID Lap</td>
                                        <td>Dosis Vaksin</td>
                                        <td>Jenis Vaksin</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pasien->VaksinLap as $data)
                                        <tr>
                                            <td class="text-center">
                                                <a href="/rsonline/pasienterlapor/editvaksinasi/{{ Crypt::encrypt($data->lapVaksinId) }}"
                                                    class="btn btn-warning btn-sm" data-toggle="tooltip"
                                                    data-placement="bottom" title="Edit">
                                                    {{ $data->lapVaksinId }}
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                            </td>
                                            <td>{{ $data->namaDosis }}</td>
                                            <td>{{ $data->namaVaksin }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <!-- text input -->
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
                <div class="col-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Laporan Terapi</h3>
                            <div class="float-right">
                                <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-terapi"
                                    type="button">
                                    <i class="fa fa-plus-circle"></i> Tambah</a>
                                </button>
                            </div>
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
                                                    class="btn btn-warning btn-sm" data-toggle="tooltip"
                                                    data-placement="bottom" title="Edit">
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
                <div class="col-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Laporan Pemeriksaan Lab</h3>
                            <div class="float-right">
                                <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-pemeriksaan"
                                    type="button">
                                    <i class="fa fa-plus-circle"></i> Tambah</a>
                                </button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="card-body">
                            <table class="table table-bordered table-hover" id="tablePemeriksaan">
                                <thead>
                                    <tr class="text-center">
                                        <td>ID Lap</td>
                                        <td>Jenis Pemeriksaan</td>
                                        <td>Hasil</td>
                                        <td>Tanggal</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pasien->PemeriksaanLab as $data)
                                        <tr>
                                            <td>{{ $data->lapPemeriksaanId }}</td>
                                            <td>{{ $data->namaPemeriksaan }}</td>
                                            <td>{{ $data->hasilPemeriksaanId }}</td>
                                            <td>{{ $data->tgl_hasil }}</td>
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
    <div class="modal fade" id="modal-komorbid">
        <div class="modal-dialog">
            <div class="modal-content">
                <form role="form" action="/rsonline/pasienterlapor/komorbid/{{ $pasien->lapId }}" method="post">
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
                                    <label>Komorbid</label>
                                    <select name="komorbid" class="form-control select2" required>
                                        @foreach ($komorbid as $komorbid)
                                            <option value="{{ $komorbid->id }}-{{ $komorbid->nama }}">
                                                {{ $komorbid->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('komorbid'))
                                        <div class="text-danger">
                                            {{ $errors->first('komorbid') }}
                                        </div>
                                    @endif
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
    {{-- Modal Vaksinasi --}}
    <div class="modal fade" id="modal-vaksinasi">
        <div class="modal-dialog">
            <div class="modal-content">
                <form role="form" action="/rsonline/pasienterlapor/vaksinasi/{{ $pasien->lapId }}" method="post">
                    {{ csrf_field() }}
                    <div class="modal-header">
                        <h4 class="modal-title">Tambah Vaksinasi</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- text input -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Dosis Vaksinasi</label>
                                    <select name="dosisVaksin" class="form-control select2" required>
                                        @foreach ($dosisvaksin as $data)
                                            <option value="{{ $data->id }}_{{ $data->nama }}">
                                                {{ $data->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('dosisVaksin'))
                                        <div class="text-danger">
                                            {{ $errors->first('dosisVaksin') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Jenis Vaksinasi</label>
                                    <select name="jenisVaksin" class="form-control select2" required>
                                        @foreach ($jenisvaksin as $data)
                                            <option value="{{ $data->id }}_{{ $data->nama }}">
                                                {{ $data->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('jenisVaksin'))
                                        <div class="text-danger">
                                            {{ $errors->first('jenisVaksin') }}
                                        </div>
                                    @endif
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
    <div class="modal fade" id="modal-pemeriksaan">
        <div class="modal-dialog">
            <div class="modal-content">
                <form role="form" action="/rsonline/pasienterlapor/lab/{{ $pasien->lapId }}" method="post">
                    {{ csrf_field() }}
                    <div class="modal-header">
                        <h4 class="modal-title">Tambah Pemeriksaan Lab</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- text input -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Jenis Pemeriksaan Lab</label>
                                    <select name="jenisPemeriksaan" class="form-control select2" required>
                                        @foreach ($lab as $data)
                                            <option value="{{ $data->id }}-{{ $data->nama }}">
                                                {{ $data->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('jenisPemeriksaan'))
                                        <div class="text-danger">
                                            {{ $errors->first('jenisPemeriksaan') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12 col-form-label">
                                        <label>Hasil Pemeriksaan</label>
                                    </div>
                                    <div class="col-sm-6 col-form-label">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="hasilpemeriksaan" value="1"
                                                id="positif" required
                                                {{ old('hasilpemeriksaan') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="positif">Positif</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-form-label">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="hasilpemeriksaan" value="0"
                                                id="negatif" required
                                                {{ old('hasilpemeriksaan') == '0' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="negatif">Negatif</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Tanggal Hasil Pemeriksaan</label>
                                    <div class="input-group date" id="tgl_hasil" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input"
                                            data-target="#tgl_hasil" data-toggle="datetimepicker" name="tgl_hasil"
                                            value="{{ old('tgl_hasil') }}" autocomplete="off" required />
                                        <div class="input-group-append" data-target="#tgl_hasil"
                                            data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                    @if ($errors->has('tgl_hasil'))
                                        <div class="text-danger">
                                            {{ $errors->first('tgl_hasil') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default float-left" data-dismiss="modal">Tutup</button>
                        <button type="Submit" class="btn btn-success">Simpan</button>
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
    <script src="{{ asset('template/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}">
    </script>
    <!-- Select2 -->
    <script src="{{ asset('template/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(function() {
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
            //Initialize Select2 Elements
            $('.select2').select2();
        });
        //Date picker
        $('#tgl_hasil').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    </script>
@endsection
