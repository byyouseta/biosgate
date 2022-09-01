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
                            <div class="float-right">
                                <a class="btn btn-primary btn-sm" href="#" onclick="printDiv()"><i
                                        class="fas fa-print"></i> Print</a>
                            </div>
                        </div>
                    </div>
                    <div class="card" id="printArea">
                        <div class="card-body">
                            <table class="table table-borderless mb-0">
                                <thead>
                                    <tr>
                                        <td style="width:3%" class="pr-0 align-middle"><img
                                                src="{{ asset('image/logorsup.jpg') }}" alt="Logo RSUP" width="30"
                                                class="px-0 py-0">
                                        </td>
                                        <td class="pt-2 pb-0 pl-1 align-middle">
                                            <h5 class="px-0 py-0">RSUP SURAKARTA</h5>
                                        </td>
                                    </tr>
                                </thead>
                            </table>
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th class="pt-0 pb-0 text-center align-middle border border-dark" rowspan="5"
                                            style="width: 40%">
                                            <h4>RINGKASAN PASIEN<br> GAWAT DARURAT</h4>
                                        </th>
                                        <th class="pt-0 pb-0 border border-dark border-right-0 border-bottom-0"
                                            style="width: 10%">No. RM
                                        </th>
                                        <th class="pt-0 pb-0 border border-dark border-left-0 border-bottom-0">:
                                            {{ $data->no_rkm_medis }}</th>
                                    </tr>
                                    <tr>
                                        <th class="pt-0 pb-0 border-0">NIK </th>
                                        <th class="pt-0 pb-0 border border-dark border-left-0 border-bottom-0 border-top-0">
                                            : {{ $data->no_ktp }}</th>
                                    </tr>
                                    <tr>
                                        <th class="pt-0 pb-0 border-0">Nama Pasien </th>
                                        <th class="pt-0 pb-0 border border-dark border-left-0 border-bottom-0 border-top-0">
                                            : {{ $data->nm_pasien }}</th>
                                    </tr>
                                    <tr>
                                        <th class="pt-0 pb-0 border-0">Tanggal Lahir </th>
                                        <th class="pt-0 pb-0 border border-dark border-left-0 border-bottom-0 border-top-0">
                                            : {{ $data->tgl_lahir }}</th>
                                    </tr>
                                    <tr>
                                        <th class="pt-0 pb-0 border border-dark border-right-0 border-top-0">Alamat</th>
                                        <th class="pt-0 pb-0 border border-dark border-left-0 border-top-0">:
                                            {{ $data->alamat }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="border border-dark border-bottom-0 border-top-0" colspan="3"><b>Waktu
                                                Kedatangan</b> Tanggal :
                                            {{ \Carbon\Carbon::parse($data->tgl_registrasi)->format('d-m-Y') }} Jam :
                                            {{ $data->jam_reg }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="border border-dark border-bottom-0 border-top-0" colspan="3">
                                            <b>Diagnosis:</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="pl-5 border border-dark border-bottom-0 border-top-0" colspan="3">
                                            {{ $data->diagnosis }}</td>
                                    </tr>
                                    <tr>
                                        <td class="border border-dark border-bottom-0 border-top-0" colspan="3">
                                            <b>Kondisi Pada Saat Keluar:</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="pl-5 border border-dark border-bottom-0 border-top-0" colspan="3">
                                            {{ $resume->kondisi_pulang }}</td>
                                    </tr>
                                    <tr>
                                        <td class="border border-dark border-bottom-0 border-top-0" colspan="3"><b>Tindak
                                                Lanjut:</b></td>
                                    </tr>
                                    <tr>
                                        <td class="pl-5 border border-dark border-bottom-0 border-top-0" colspan="3">
                                            {{ $resume->tindak_lanjut }}</td>
                                    </tr>
                                    <tr>
                                        <td class="border border-dark border-bottom-0 border-top-0" colspan="3"><b>Obat
                                                yang dibawa pulang:</b></td>
                                    </tr>
                                    @php
                                        $obat = explode("\n", $resume->obat_pulang);
                                    @endphp
                                    @foreach ($obat as $obatPulang)
                                        <tr>
                                            <td class="pl-5 pt-0 pb-0 border border-dark border-bottom-0 border-top-0"
                                                colspan="3">
                                                {{ $obatPulang }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td class="border border-dark border-bottom-0 border-top-0" colspan="3">
                                            <b>Edukasi:</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="pl-5 pt-0 pb-0 border border-dark border-bottom-0 border-top-0"
                                            colspan="3">
                                            {{ $resume->edukasi }}</td>
                                    </tr>
                                    <tr>
                                        <td class="border border-dark border-bottom-0 border-top-0" colspan="3">Waktu
                                            Selesai Pelayanan IGD Tanggal:
                                            {{ \Carbon\Carbon::parse($resume->tgl_selesai)->format('d-m-Y') }} Jam:
                                            {{ \Carbon\Carbon::parse($resume->tgl_selesai)->format('H:i:s') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="border border-dark border-bottom-0 border-top-0" colspan="3">Tanda
                                            Tangan Dokter</td>
                                    </tr>
                                    <tr>
                                        @php
                                            $qr_dokter = 'Dikeluarkan di RSUP SURAKARTA, Kabupaten/Kota Surakarta Ditandatangani secara elektronik oleh' . "\n" . $data->nm_dokter . "\n" . 'ID ' . $data->kd_dokter . "\n" . \Carbon\Carbon::parse($resume->tgl_selesai)->format('d-m-Y');
                                        @endphp
                                        <td class="pt-0 pb-0 pl-5 border border-dark border-bottom-0 border-top-0"
                                            colspan="3"> {!! QrCode::size(100)->generate($qr_dokter) !!} </td>
                                    </tr>
                                    <tr>
                                        <td class="border border-dark border-top-0" colspan="3">Nama :
                                            {{ $data->nm_dokter }}</td>
                                    </tr>
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
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
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
                "scrollY": "300px",
                "scrollX": false,
            });
        });
        //Date picker
        $('#tanggal').datetimepicker({
            format: 'YYYY-MM-DD'
        });

        function printInfo() {
            var prtContent = document.getElementById("printArea");
            var WinPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');
            WinPrint.document.write(
                '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"><html>'
            );
            WinPrint.document.write(prtContent.innerHTML);
            WinPrint.document.close();
            WinPrint.focus();
            WinPrint.print();
            WinPrint.close();
        }
    </script>
    <script>
        function printDiv() {
            var divContents = document.getElementById("printArea").innerHTML;
            var a = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');
            a.document.write(
                '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"><html>'
            );
            a.document.write('<body >');
            a.document.write(divContents);
            a.document.write('</body></html>');
            a.document.close();
            // a.print();
        }
    </script>
@endsection
