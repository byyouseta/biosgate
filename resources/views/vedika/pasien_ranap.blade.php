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
                @php
                    if (!empty(Request::get('tanggal'))) {
                        $tanggal = Request::get('tanggal');
                    } else {
                        $tanggal = \Carbon\Carbon::now()->format('Y-m-d');
                    }
                @endphp
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            {{-- <div class="card_title">Data Pasien Covid</div> --}}
                            {{-- <div class="float-right"> --}}
                            <div class="form-group row">
                                <div class="col-sm-9 mt-2">
                                    <label>Data Pasien Ranap</label>
                                </div>
                                <div class="col-sm-3">
                                    <form action="/vedika/ranap" method="GET">
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
                            {{-- </div> --}}
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered table-hover" id="example2">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">No.RM</th>
                                            <th class="align-middle">No.SEP</th>
                                            <th class="align-middle">Nama Pasien</th>
                                            <th class="align-middle">Alamat</th>
                                            <th class="align-middle">Tgl Registrasi</th>
                                            <th class="align-middle">Nama Poli</th>
                                            <th class="align-middle">Dokter</th>
                                            <th class="align-middle">No.Kartu</th>
                                            <th class="align-middle">D.U</th>
                                            <th class="align-middle">Berkas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $data)
                                            @if (\App\PelaporanCovid::cekLapor($data->no_rawat) == 0)
                                                <tr>
                                                    <td>{{ $data->no_rkm_medis }}</td>
                                                    <td>{{ App\Vedika::getSep($data->no_rawat) != null ? App\Vedika::getSep($data->no_rawat)->no_sep : '' }}
                                                    </td>
                                                    <td>{{ $data->nm_pasien }}, {{ $data->umurdaftar }}
                                                        {{ $data->sttsumur }},
                                                        {{ $data->jk == 'L' ? 'Laki-Laki' : 'Perempuan' }}</td>
                                                    <td>{{ $data->almt_pj }}</td>
                                                    <td>{{ $data->tgl_registrasi }} {{ $data->jam_reg }}</td>
                                                    <td>{{ $data->nm_poli }}</td>
                                                    <td>{{ $data->nm_dokter }}</td>
                                                    <td>{{ $data->no_peserta }}</td>
                                                    <td>{{ App\Vedika::getDiagnosa($data->no_rawat, 'Ranap') != null ? App\Vedika::getDiagnosa($data->no_rawat, 'Ranap')->kd_penyakit . '-' . App\Vedika::getDiagnosa($data->no_rawat, 'Ranap')->nm_penyakit : '' }}
                                                    </td>
                                                    <td>
                                                        <div class="col text-center">
                                                            <a href="/vedika/ranap/{{ Crypt::encrypt($data->no_rawat) }}/billing"
                                                                class="btn btn-sm {{ App\Vedika::cekBilling($data->no_rawat) > 0 ? '' : 'disabled' }}"
                                                                data-toggle="tooltip" data-placement="bottom"
                                                                title="Billing" target="_blank">
                                                                <span class="badge badge-success">Billing</span>
                                                            </a>
                                                            <a href="/vedika/ranap/{{ Crypt::encrypt($data->no_rawat) }}/lab"
                                                                class="btn btn-sm {{ App\Vedika::cekLab($data->no_rawat) > 0 ? '' : 'disabled' }}"
                                                                data-toggle="tooltip" data-placement="bottom" title="Lab"
                                                                target="_blank">
                                                                <span class="badge badge-danger">Lab</span>
                                                            </a>
                                                            <a href="/vedika/ranap/{{ Crypt::encrypt($data->no_rawat) }}/radiologi"
                                                                class="btn btn-sm {{ App\Vedika::cekrad($data->no_rawat) > 0 ? '' : 'disabled' }}"
                                                                data-toggle="tooltip" data-placement="bottom" title="Lab"
                                                                target="_blank">
                                                                <span class="badge badge-warning">Radiologi</span>
                                                            </a>
                                                            <a href="/vedika/ranap/{{ Crypt::encrypt($data->no_rawat) }}/berkas"
                                                                class="btn btn-sm" data-toggle="tooltip"
                                                                data-placement="bottom" title="Berkas Lainnya"
                                                                target="_blank">
                                                                <span class="badge badge-info">Lain-lain</span>
                                                            </a>

                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
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
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": false,
                "scrollY": "500px",
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
@endsection
