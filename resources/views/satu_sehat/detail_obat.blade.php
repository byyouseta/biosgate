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
                            <div class="card_title">Data Kunjungan
                                <div class="float-right">
                                    @if (empty($dataResponse && $dataResponse->medicationDispence))
                                        <form action="{{ route('satuSehat.repeatSendMedication') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="no_resep" value="{{ $dataPengunjung->no_resep }}">
                                            <input type="hidden" name="no_rawat" value="{{ $dataPengunjung->no_rawat }}">
                                            <input type="hidden" name="kode_barang"
                                                value="{{ $dataPengunjung->kode_brng }}">
                                            <input type="hidden" name="idSehatPasien"
                                                value="{{ $dataPengunjung->idSehat }}">
                                            <input type="hidden" name="idIhsDokter"
                                                value="{{ $dataPengunjung->idSehatPractition }}">
                                            <input type="hidden" name="idEncounter"
                                                value="{{ $dataPengunjung->dataEncounter }}">
                                            <button type="submit" class="btn btn-info btn-sm"><i
                                                    class="far fa-paper-plane"></i> Kirim Ulang</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6>Kunjungan Pasien</h6>
                            <table class="table table-bordered table-hover table-sm">
                                <tr>
                                    <th width='30%'>No Rawat</th>
                                    <td>: {!! $dataPengunjung ? $dataPengunjung->no_rawat : '<i class="fas fa-times-circle" style="color: red;"></i>' !!}</td>
                                </tr>
                                <tr>
                                    <th width='30%'>Nama Pasien</th>
                                    <td>: {!! $dataPengunjung ? $dataPengunjung->nm_pasien : '<i class="fas fa-times-circle" style="color: red;"></i>' !!}</td>
                                </tr>
                                <tr>
                                    <th width='30%'>ID Sehat Pasien</th>
                                    <td>: {!! $dataPengunjung ? $dataPengunjung->idSehat : '<i class="fas fa-times-circle" style="color: red;"></i>' !!}</td>
                                </tr>
                                <tr>
                                    <th width='30%'>Encounter ID</th>
                                    <td>: {!! $dataPengunjung && $dataPengunjung->dataEncounter
                                        ? $dataPengunjung->dataEncounter
                                        : '<i class="fas fa-times-circle" style="color: red;"></i>' !!}</td>
                                </tr>
                                <tr>
                                    <th width='30%'>MedicationRequest</th>
                                    <td>: {!! $dataResponse && $dataResponse->medicationRequest
                                        ? $dataResponse->medicationRequest
                                        : '<i class="fas fa-times-circle" style="color: red;"></i>' !!}</td>
                                </tr>
                                <tr>
                                    <th width='30%'>MedicationDispence</th>
                                    <td>: {!! $dataResponse && $dataResponse->medicationDispence
                                        ? $dataResponse->medicationDispence
                                        : '<i class="fas fa-times-circle" style="color: red;"></i>' !!}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Mapping Data
                            </div>
                        </div>
                        <div class="card-body">
                            <h6>Maping Data Obat</h6>
                            <table class="table table-bordered table-hover table-sm">
                                <tr>
                                    <th width='30%' rowspan="2" class="align-middle">Mapping Kode KFA</th>
                                    <td>: {!! $dataMapping ? $dataMapping->id_ihs : '<i class="fas fa-times-circle" style="color: red;"></i>' !!}</td>
                                </tr>
                                <tr>
                                    <td>: {!! $dataMapping ? $dataMapping->kfa_display : '<i class="fas fa-times-circle" style="color: red;"></i>' !!}</td>
                                </tr>
                                <tr>
                                    <th width='30%'>Maping Medication</th>

                                    <td>: {!! $dataMapping
                                        ? $dataMapping->medicationform_display
                                        : '<i class="fas fa-times-circle" style="color: red;"></i>' !!}</td>
                                </tr>
                                <tr>
                                    <th width='30%'>Mapping Ucum</th>
                                    <td>: {!! $dataMapping && $dataMapping->ucum_name
                                        ? $dataMapping->ucum_name
                                        : '<i class="fas fa-times-circle" style="color: red;"></i>' !!}</td>
                                </tr>
                                <tr>
                                    <th width='30%'>Mapping Ingredient</th>
                                    <td>: {!! $dataMapping && $dataMapping->ingredient_display
                                        ? $dataMapping->ingredient_display
                                        : '<i class="fas fa-times-circle" style="color: red;"></i>' !!}</td>
                                </tr>
                                <tr>
                                    <th width='30%'>Mapping Kode Route</th>
                                    <td>: {!! $dataMapping && $dataMapping->route_display
                                        ? $dataMapping->route_display
                                        : '<i class="fas fa-times-circle" style="color: red;"></i>' !!}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="card_title">Data Permintaan
                            </div>
                        </div>
                        <div class="card-body">
                            <h6>Data Permintaan Obat</h6>
                            <table class="table table-bordered table-hover table-sm">
                                <tr>
                                    <th width='30%'>No Resep</th>
                                    <td>: {!! $dataPengunjung ? $dataPengunjung->no_resep : '<i class="fas fa-times-circle" style="color: red;"></i>' !!}</td>
                                </tr>
                                <tr>
                                    <th width='30%'>Dokter Peresep</th>
                                    <td>: {!! $dataPengunjung ? $dataPengunjung->nama_dokter : '<i class="fas fa-times-circle" style="color: red;"></i>' !!}</td>
                                </tr>
                                <tr>
                                    <th width='30%'>ID IHS Dokter Peresep</th>
                                    <td>: {!! $dataPengunjung
                                        ? $dataPengunjung->idSehatPractition
                                        : '<i class="fas fa-times-circle" style="color: red;"></i>' !!}</td>
                                </tr>
                                <tr>
                                    <th width='30%'>Waktu Peresepan</th>
                                    <td>: {!! $dataPengunjung
                                        ? \Carbon\Carbon::parse("$dataPengunjung->tgl_peresepan $dataPengunjung->jam_peresepan")->format('Y-m-d H:i:s')
                                        : '<i class="fas fa-times-circle" style="color: red;"></i>' !!}</td>
                                </tr>
                                <tr>
                                    <th width='30%'>Waktu Penyerahan</th>
                                    <td>: {!! $dataPengunjung
                                        ? \Carbon\Carbon::parse("$dataPengunjung->tgl_penyerahan $dataPengunjung->jam_penyerahan")->format('Y-m-d H:i:s')
                                        : '<i class="fas fa-times-circle" style="color: red;"></i>' !!}</td>
                                </tr>
                                <tr>
                                    <th width='30%'>Jenis Permintaan</th>
                                    <td>: {!! $dataPengunjung ? $dataPengunjung->status_obat : '<i class="fas fa-times-circle" style="color: red;"></i>' !!}</td>
                                </tr>
                                <tr>
                                    <th width='30%'>Kode Obat</th>
                                    <td>: {!! $dataPengunjung ? $dataPengunjung->kode_brng : '<i class="fas fa-times-circle" style="color: red;"></i>' !!}</td>
                                </tr>
                                <tr>
                                    <th width='30%'>Nama Obat</th>
                                    <td>: {!! $dataPengunjung ? $dataPengunjung->nama_brng : '<i class="fas fa-times-circle" style="color: red;"></i>' !!}</td>
                                </tr>
                                <tr>
                                    <th width='30%'>Jumlah Obat</th>
                                    <td>: {!! $dataPengunjung ? $dataPengunjung->jml_obat : '<i class="fas fa-times-circle" style="color: red;"></i>' !!}</td>
                                </tr>
                                <tr>
                                    <th width='30%'>Aturan Pakai</th>
                                    <td>: {!! $dataPengunjung ? $dataPengunjung->aturan_pakai : '<i class="fas fa-times-circle" style="color: red;"></i>' !!}</td>
                                </tr>
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
            $('#example,#example2,#example3,#example4,#example5').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "order": [
                    [2, 'desc']
                ],
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
