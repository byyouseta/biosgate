@extends('layouts.master')

@section('head')
<meta http-equiv="refresh" content="600" />
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
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card_title">Check Data Pasien
                        </div>
                    </div>
                    <div class="card-body">
                        <h6>Data Pasien</h6>
                        <table class="table table-bordered table-hover table-sm">
                            <tr>
                                <th width='20%'>No KTP</th>
                                <td>: {!! $dataPasien? $dataPasien->ktp_pasien:'<i class="fas fa-times-circle"
                                        style="color: red;"></i>' !!}</td>
                            </tr>
                            <tr>
                                <th width='20%'>ID Satu Sehat</th>
                                <td>: {!! $idSatu ? $idSatu->satu_sehat_id:'<i class="fas fa-times-circle"
                                        style="color: red;"></i>' !!}</td>
                            </tr>
                            <tr>
                                <th width='20%'>Nama</th>
                                <td>: {!! $dataPasien? $dataPasien->nm_pasien:'<i class="fas fa-times-circle"
                                        style="color: red;"></i>' !!}</td>
                            </tr>
                            <tr>
                                <th width='20%'>No RM</th>
                                <td>: {!! $dataPasien? $dataPasien->no_rkm_medis:'<i class="fas fa-times-circle"
                                        style="color: red;"></i>' !!}</td>
                            </tr>
                            <tr>
                                <th width='20%'>Usia</th>
                                <td>: {!! $dataPasien? $dataPasien->tgl_lahir:'<i class="fas fa-times-circle"
                                        style="color: red;"></i>' !!}</td>
                            </tr>
                            <tr>
                                <th width='20%'>Penjamin</th>
                                <td>: {!! $dataPasien? $dataPasien->nama_perusahaan:'<i class="fas fa-times-circle"
                                        style="color: red;"></i>' !!}</td>
                            </tr>
                            @if (empty($idSatu) && ($dataPasien->nama_perusahaan == 'BPJS'))
                            @php
                            if($dataPasien->no_peserta != '-' || $dataPasien->no_peserta != null){
                            $dataPeserta =
                            \App\Http\Controllers\SepController::peserta($dataPasien->no_peserta,$dataPasien->tgl_registrasi);
                            }else{
                            $dataPeserta = null;
                            }

                            @endphp
                            <tr>
                                <th width='20%'>KTP dari Vklaim</th>
                                <td>: {!! $dataPeserta? $dataPeserta->nik:'<i class="fas fa-times-circle"
                                        style="color: red;"></i>' !!}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover table-sm mt-3" id="example2">
                            <thead>
                                <tr>
                                    <th class="align-middle">Subject</th>
                                    <th class="align-middle">Keterangan</th>
                                    <th class="align-middle">Created Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($logUser as $log)
                                <tr>
                                    <td>{{ $log->subject }}</td>
                                    <td>{{ $log->keterangan }}</td>
                                    <td>{{ $log->created_at }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card_title">Check Data Praktisi
                            {{-- Kirim data Encounter --}}
                            <div class="float-right">
                                @if($dataPasien->kd_poli == 'MCU' || $dataPasien->kd_poli == 'LAB')
                                <a href="{{ route('satuSehat.singleEncounter',Crypt::encrypt($dataPasien->no_rawat)) }}"
                                    class="btn btn-primary btn-sm"><i class="far fa-paper-plane"></i> Kirim
                                    Encounter</a>
                                @else
                                <a href="{{ route('satuSehat.checkRajalSend',Crypt::encrypt($dataPasien->no_rawat)) }}"
                                    class="btn btn-primary btn-sm"><i class="far fa-paper-plane"></i> Kirim
                                    Ulang</a>
                                @endif
                            </div>
                        </div>

                    </div>
                    <div class="card-body">
                        <h6>Data Praktisi</h6>

                        <table class="table table-bordered table-hover table-sm">
                            <tr>
                                <th width='20%'>No KTP</th>
                                <td>: {!! $dataPasien? $dataPasien->ktp_dokter:'<i class="fas fa-times-circle"
                                        style="color: red;"></i>' !!}</td>
                            </tr>
                            <tr>
                                <th width='20%'>ID Satu Sehat</th>
                                <td>: {!! $idSatuPraktisi ? $idSatuPraktisi->satu_sehat_id:'<i
                                        class="fas fa-times-circle" style="color: red;"></i>' !!}</td>
                            </tr>
                            <tr>
                                <th width='20%'>Nama</th>
                                <td>: {!! $dataPasien? $dataPasien->nama_dokter:'<i class="fas fa-times-circle"
                                        style="color: red;"></i>' !!}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover table-sm mt-3" id="example">
                            <thead>
                                <tr>
                                    <th class="align-middle">Subject</th>
                                    <th class="align-middle">Keterangan</th>
                                    <th class="align-middle">Created Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($logPraktisi as $log)
                                <tr>
                                    <td>{{ $log->subject }}</td>
                                    <td>{{ $log->keterangan }}</td>
                                    <td>{{ $log->created_at }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card_title">Check Data Diagnosa
                        </div>
                    </div>
                    <div class="card-body">
                        <h6>Data Diagnosa</h6>
                        <table class="table table-bordered table-hover table-sm">
                            <tr>
                                <th width='30%'>Diagnosa Primer</th>
                                <td>: {!! $cekDiagnosa->where('prioritas',1)->first()?
                                    $cekDiagnosa->where('prioritas',1)->first()->kd_penyakit:'<i
                                        class="fas fa-times-circle" style="color: red;"></i>' !!}
                                </td>
                            </tr>
                            <tr>
                                <th width='30%'>Nama Penyakit Primer</th>
                                <td>: {!! $cekDiagnosa->where('prioritas',1)->first()?
                                    $cekDiagnosa->where('prioritas',1)->first()->nm_penyakit:'<i
                                        class="fas fa-times-circle" style="color: red;"></i>' !!}
                                </td>
                            </tr>
                            <tr>
                                <th width='30%'>Diagnosa Sekunder</th>
                                <td>: {!! $cekDiagnosa->where('prioritas',2)->first()?
                                    $cekDiagnosa->where('prioritas',2)->first()->kd_penyakit:'<i
                                        class="fas fa-times-circle" style="color: red;"></i>' !!}
                                </td>
                            </tr>
                            <tr>
                                <th width='30%'>Nama Penyakit Sekunder</th>
                                <td>: {!! $cekDiagnosa->where('prioritas',2)->first()?
                                    $cekDiagnosa->where('prioritas',2)->first()->nm_penyakit:'<i
                                        class="fas fa-times-circle" style="color: red;"></i>' !!}
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover table-sm" id="example3">
                            <thead>
                                <tr>
                                    <th class="align-middle">Subject</th>
                                    <th class="align-middle">Keterangan</th>
                                    <th class="align-middle">Created Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($logDiagnosa as $log)
                                <tr>
                                    <td>{{ $log->subject }}</td>
                                    <td>{{ $log->keterangan }}</td>
                                    <td>{{ $log->created_at }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card_title">Check Data Poliklinik
                        </div>
                    </div>
                    <div class="card-body">
                        <h6>Data Poliklinik</h6>
                        <table class="table table-bordered table-hover table-sm">
                            <tr>
                                <th width='30%'>Nama Poli</th>
                                <td>: {{ $dataPasien? $dataPasien->nm_poli:'-' }}
                                </td>
                            </tr>
                            <tr>
                                <th width='30%'>Kode Poli</th>
                                <td>: {{ $dataPasien? $dataPasien->kd_poli:'-' }}
                                </td>
                            </tr>
                            <tr>
                                <th width='30%'>Kode Mapping</th>
                                <td>: {!! $cekPoliklinik? $cekPoliklinik->id_ihs:'<i class="fas fa-times-circle"
                                        style="color: red;"></i>' !!}
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover table-sm" id="example4">
                            <thead>
                                <tr>
                                    <th class="align-middle">Subject</th>
                                    <th class="align-middle">Keterangan</th>
                                    <th class="align-middle">Created Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($logPoliklinik as $log)
                                <tr>
                                    <td>{{ $log->subject }}</td>
                                    <td>{{ $log->keterangan }}</td>
                                    <td>{{ $log->created_at }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card_title">Log Error Other
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover table-sm" id="example5">
                            <thead>
                                <tr>
                                    <th class="align-middle">Subject</th>
                                    <th class="align-middle">Keterangan</th>
                                    <th class="align-middle">Created Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($logOther as $log)
                                <tr>
                                    <td>{{ $log->subject }}</td>
                                    <td>{{ $log->keterangan }}</td>
                                    <td>{{ $log->created_at }}</td>
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
            // $('#example2').DataTable({
            //     "paging": true,
            //     "lengthChange": false,
            //     "searching": true,
            //     "ordering": true,
            //     "info": false,
            //     "autoWidth": false,
            //     "responsive": false,
            //     "scrollY": true,
            //     "scrollX": true,
            // });
            $('#example,#example2,#example3,#example4,#example5').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "order": [[2, 'desc']],
                "info": false,
                "autoWidth": false,
                "responsive": false,
                "scrollY": true,
                "scrollX": true,
            });
        });
        //Date picker
        $('#tanggal').datetimepicker({
            format: 'YYYY-MM-DD'
        });
</script>
@endsection