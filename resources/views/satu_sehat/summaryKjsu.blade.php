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
        @php
            if (!empty(Request::get('tanggal'))) {
                $tanggal = Request::get('tanggal');
            } else {
                $tanggal = \Carbon\Carbon::now()->format('Y-m-d');
            }
        @endphp
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Summary data Diagnosa KJSU
                                {{-- <a href="/satusehat/ranap/kirimencounter" class="btn btn-sm btn-primary"
                                    target="_blank">Encounter</a>
                                <a href="/satusehat/ranap/encounterupdate" class="btn btn-sm btn-primary"
                                    target="_blank">Update
                                    Encounter</a> --}}
                                <div class="float-right">
                                    <form action="/satusehat/kjsu" method="GET">
                                        <div class="input-group input-group" id="tanggal" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input"
                                                data-target="#tanggal" data-toggle="datetimepicker" name="tanggal"
                                                autocomplete="off" value="{{ $tanggal }}">
                                            <span class="input-group-append">
                                                <button type="submit" class="btn btn-info btn-flat btn-sm"><i
                                                        class="fas fa-search"></i> Tampilkan</button>
                                            </span>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- <div style="overflow-x:auto;"> --}}
                            <table class="table table-bordered table-hover table-sm display nowrap" id="example2">
                                <thead>
                                    <tr>
                                        <th class="align-middle">No Rawat</th>
                                        <th class="align-middle">No RM</th>
                                        <th class="align-middle">Nama Pasien</th>
                                        <th class="align-middle">Tgl Registrasi</th>
                                        <th class="align-middle">Pelayanan</th>
                                        <th class="align-middle">Diagnosa</th>
                                        <th class="align-middle">Jenis</th>
                                        <th class="align-middle">Encounter ID</th>
                                        <th class="align-middle">Data KJSU</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dataFilter as $summary)
                                        <tr>
                                            <td>{{ $summary->no_rawat }}</td>
                                            <td>{{ $summary->no_rkm_medis }}</td>
                                            <td>{{ $summary->nm_pasien }}</td>
                                            <td>{{ $summary->tgl_registrasi }}</td>
                                            <td>{{ $summary->status }}</td>
                                            <td>{{ $summary->kd_penyakit }}</td>
                                            <td>{{ $summary->jenis }}</td>
                                            <td>
                                                @if($summary->status == 'Ralan')
                                                    @if ($summary->encounter_id)
                                                        {{ $summary->encounter_id }}
                                                    @else
                                                        <a href="{{ route('satuSehat.checkRajalDetail', Crypt::encrypt($summary->no_rawat)) }}"
                                                            class='badge badge-info badge-sm' target="_blank">Check</a>
                                                    @endif
                                                @elseif($summary->status == 'Ranap')
                                                    {{ $summary->encounter_id ? $summary->encounter_id:'-' }}
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('satuSehat.kjsuDetail', Crypt::encrypt($summary->no_rawat.'_'.$summary->kd_penyakit)) }}"
                                                    class='badge badge-primary badge-sm' target="_blank">Detail</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{-- </div> --}}
                        </div>
                    </div>
                    {{-- <div class="card">
                        <div class="card-header">
                            Log Error
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-hover table-sm display nowrap" id="example">
                                <thead>
                                    <tr>
                                        <th class="align-middle">No</th>
                                        <th class="align-middle">Subject</th>
                                        <th class="align-middle">Keterangan</th>
                                        <th class="align-middle">Created Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($errorLog as $index => $log)
                                        <tr>
                                            <td class="text-center">{{ ++$index }}</td>
                                            <td>{{ $log->subject }}</td>
                                            <td>{{ $log->keterangan }}</td>
                                            <td>{{ $log->created_at }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> --}}
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
        //Date picker
        $('#tanggal').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    </script>
@endsection
