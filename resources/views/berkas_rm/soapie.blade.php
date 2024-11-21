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
                <div class="col-md-12 mx-auto">
                    <div class="card ">
                        <div class="card-header">
                            <div class="card-title">PERAWATAN/TINDAKAN RAWAT JALAN</div>
                        </div>
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="no_hp">Nomor Rawat</label>
                                        <input type="text" class="form-control" name="noRawat"
                                            value="{{ $data->no_rawat }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="no_hp">Nomor RM</label>
                                        <input type="text" class="form-control" name="noRm"
                                            value="{{ $data->no_rkm_medis }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="no_hp">Nama Pasien</label>
                                        <input type="text" class="form-control" name="nama_pasien"
                                            value="{{ $data->nm_pasien }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="no_hp">Tgl Lahir</label>
                                        <input type="text" class="form-control" name="tgl_lahir"
                                            value="{{ $data->tgl_lahir }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <h4>
                                        <hr>
                                    </h4>
                                    <ul class="nav nav-tabs" id="custom-content-below-tab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link " id="custom-content-below-home-tab" data-toggle="pill"
                                                href="#custom-content-below-home" role="tab"
                                                aria-controls="custom-content-below-home" aria-selected="false">Penanganan
                                                Dokter</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="custom-content-below-profile-tab" data-toggle="pill"
                                                href="#custom-content-below-profile" role="tab"
                                                aria-controls="custom-content-below-profile"
                                                aria-selected="false">Penanganan Petugas</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="custom-content-below-messages-tab" data-toggle="pill"
                                                href="#custom-content-below-messages" role="tab"
                                                aria-controls="custom-content-below-messages"
                                                aria-selected="false">Penanganan Dokter dan Petugas</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link active" id="custom-content-below-settings-tab"
                                                data-toggle="pill" href="#custom-content-below-settings" role="tab"
                                                aria-controls="custom-content-below-settings"
                                                aria-selected="true">Pemeriksaan</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="custom-content-below-tabContent">
                                        <div class="tab-pane fade" id="custom-content-below-home" role="tabpanel"
                                            aria-labelledby="custom-content-below-home-tab">
                                            <h4>On Process</h4>
                                        </div>
                                        <div class="tab-pane fade" id="custom-content-below-profile" role="tabpanel"
                                            aria-labelledby="custom-content-below-profile-tab">
                                            <h4>On Process</h4>
                                        </div>
                                        <div class="tab-pane fade" id="custom-content-below-messages" role="tabpanel"
                                            aria-labelledby="custom-content-below-messages-tab">
                                            <h4>On Process</h4>
                                        </div>
                                        <div class="tab-pane fade  show active" id="custom-content-below-settings"
                                            role="tabpanel" aria-labelledby="custom-content-below-settings-tab">
                                            <form method="POST" action="{{ route('berkasrm.soapieStore') }}">
                                                @csrf
                                                {{-- hidden input --}}
                                                <input type="hidden" name="noRawat" value="{{ $data->no_rawat }}">
                                                <div class="row mt-2">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="no_hp">Dilakukan</label>
                                                            <select name="petugas" class="form-control select2">
                                                                @foreach ($data_pegawai as $listPetugas)
                                                                    <option value="{{ $listPetugas->nik }}"
                                                                        {{ $listPetugas->nik == Auth::user()->username ? 'selected' : '' }}>
                                                                        {{ $listPetugas->nama }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="no_hp">Tanggal</label>
                                                            <input type="text"
                                                                class="form-control datetimepicker-input" id="tanggal"
                                                                data-target="#tanggal" data-toggle="datetimepicker"
                                                                name="tanggal"
                                                                value="{{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="no_hp">Imunisasi ke-</label>
                                                            <select name="imun_ke" class="form-control">
                                                                <option value="-">-</option>
                                                                @for ($i = 1; $i <= 13; ++$i)
                                                                    <option value="{{ $i }}">
                                                                        {{ $i }}</option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="no_hp">Alergi</label>
                                                            <input type="text" class="form-control" name="alergi"
                                                                value="{{ $data_inputRalan ? $data_inputRalan->alergi : '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="subjeck">Subjek</label>
                                                            <textarea name="keluhan" id="" rows="3" class="form-control"></textarea>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="no_hp">Objek</label>
                                                            <textarea name="pemeriksaan" id="" rows="3" class="form-control"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="subjeck">Asesmen</label>
                                                            <textarea name="penilaian" id="" rows="3" class="form-control"></textarea>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="no_hp">Plan</label>
                                                            <textarea name="rtl" id="" rows="3" class="form-control"></textarea>
                                                        </div>

                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label for="no_hp">Suhu</label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control"
                                                                    name="suhu_tubuh"
                                                                    value="{{ $data_inputRalan ? $data_inputRalan->suhu : '' }}">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">&#8451;</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="no_hp">Tensi</label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control" name="tensi"
                                                                    value="{{ $data_inputRalan ? $data_inputRalan->td : '' }}">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">mmHg</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="no_hp">SpO2</label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control" name="spo2"
                                                                    value="{{ $data_inputRalan ? $data_inputRalan->spo2 : '' }}">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">&#37;</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label for="no_hp">Tinggi Badan</label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control" name="tinggi"
                                                                    value="{{ $data_inputRalan ? $data_inputRalan->tb : '' }}">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">cm</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="no_hp">Respirasi</label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control"
                                                                    name="respirasi"
                                                                    value="{{ $data_inputRalan ? $data_inputRalan->rr : '' }}">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">x/menit</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="no_hp">GCS(E,V,M)</label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control" name="gcs"
                                                                    value="{{ $data_inputRalan ? $data_inputRalan->gcs : '' }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label for="no_hp">Berat Badan</label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control" name="berat"
                                                                    value="{{ $data_inputRalan ? $data_inputRalan->bb : '' }}">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">Kg</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="no_hp">Nadi</label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control" name="nadi"
                                                                    value="{{ $data_inputRalan ? $data_inputRalan->nadi : '' }}">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">x/menit</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="no_hp">Kesadaran</label>
                                                            <select name="kesadaran" class="form-control">
                                                                <option value="Compos Mentis">Compos Mentis</option>
                                                                <option value="Somnolence">Somnolence</option>
                                                                <option value="Sopor">Sopor</option>
                                                                <option value="Coma">Coma</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="subjeck">Implementasi</label>
                                                            <textarea name="instruksi" id="" rows="3" class="form-control"></textarea>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="no_hp">Evaluasi</label>
                                                            <textarea name="evaluasi" id="" rows="3" class="form-control"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <hr>
                                                        Data Tersimpan
                                                        <table class="table table-bordered table-stripped table-sm"
                                                            id="example">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center align-top">Aksi</th>
                                                                    <th class="text-center align-top">No.Rawat</th>
                                                                    <th class="text-center align-top">No.RM</th>
                                                                    <th class="text-center align-top">Nama Pasien</th>
                                                                    <th class="text-center align-top">Tgl Rawat</th>
                                                                    <th class="text-center align-top">Jam</th>
                                                                    <th class="text-center align-top">Suhu</th>
                                                                    <th class="text-center align-top">Tensi</th>
                                                                    <th class="text-center align-top">Nadi</th>
                                                                    <th class="text-center align-top">Respirasi</th>
                                                                    <th class="text-center align-top">Tinggi</th>
                                                                    <th class="text-center align-top">Berat</th>
                                                                    <th class="text-center align-top">SpO2</th>
                                                                    <th class="text-center align-top">GCS(E,V,M)</th>
                                                                    <th class="text-center align-top">Kesadaran</th>
                                                                    <th class="text-center align-top">Subjek</th>
                                                                    <th class="text-center align-top">Objek</th>
                                                                    <th class="text-center align-top">Alergi</th>
                                                                    <th class="text-center align-top">Imun Ke</th>
                                                                    <th class="text-center align-top">Plan</th>
                                                                    <th class="text-center align-top">Asesmen</th>
                                                                    <th class="text-center align-top">Implementasi</th>
                                                                    <th class="text-center align-top">Evaluasi</th>
                                                                    <th class="text-center align-top">NIP</th>
                                                                    <th class="text-center align-top">Dokter/Paramedis</th>
                                                                    <th class="text-center align-top">Profesi/Jabatan</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($data_soapie as $noSoapie => $listSoapie)
                                                                    <tr>
                                                                        <td>
                                                                            <div class="col text-center">
                                                                                <div class="btn-group">
                                                                                    <a href="{{ route('berkasrm.soapieEdit', Crypt::encrypt($listSoapie->no_rawat . '-' . $listSoapie->jam_rawat)) }}"
                                                                                        class="btn btn-warning btn-sm"
                                                                                        data-toggle="tooltip"
                                                                                        data-placement="bottom"
                                                                                        title="Edit">
                                                                                        <i class="fas fa-pencil-alt"></i>
                                                                                    </a>
                                                                                    <a href="{{ route('berkasrm.soapieDelete', Crypt::encrypt($listSoapie->no_rawat . '-' . $listSoapie->jam_rawat)) }}"
                                                                                        class="btn btn-danger btn-sm delete-confirm"
                                                                                        data-toggle="tooltip"
                                                                                        data-placement="bottom"
                                                                                        title="Delete">
                                                                                        <i class="fas fa-ban"></i>
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td>{{ $listSoapie->no_rawat }}</td>
                                                                        <td>{{ $listSoapie->no_rkm_medis }}</td>
                                                                        <td>{{ $listSoapie->nm_pasien }}</td>
                                                                        <td>{{ $listSoapie->tgl_perawatan }}</td>
                                                                        <td>{{ $listSoapie->jam_rawat }}</td>
                                                                        <td>{{ $listSoapie->suhu_tubuh }}</td>
                                                                        <td>{{ $listSoapie->tensi }}</td>
                                                                        <td>{{ $listSoapie->nadi }}</td>
                                                                        <td>{{ $listSoapie->respirasi }}</td>
                                                                        <td>{{ $listSoapie->tinggi }}</td>
                                                                        <td>{{ $listSoapie->berat }}</td>
                                                                        <td>{{ $listSoapie->spo2 }}</td>
                                                                        <td>{{ $listSoapie->gcs }}</td>
                                                                        <td>{{ $listSoapie->kesadaran }}</td>
                                                                        <td>{{ $listSoapie->keluhan }}</td>
                                                                        <td>{{ $listSoapie->pemeriksaan }}</td>
                                                                        <td>{{ $listSoapie->alergi }}</td>
                                                                        <td>{{ $listSoapie->imun_ke }}</td>
                                                                        <td>{{ $listSoapie->rtl }}</td>
                                                                        <td>{{ $listSoapie->penilaian }}</td>
                                                                        <td>{{ $listSoapie->instruksi }}</td>
                                                                        <td>{{ $listSoapie->evaluasi }}</td>
                                                                        <td>{{ $listSoapie->nip }}</td>
                                                                        <td>{{ $listSoapie->nm_dokter }}</td>
                                                                        <td>{{ $listSoapie->jbtn }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                </div>
                                                {{-- <div class="card-footer"> --}}
                                                <div class="float-right mt-3">
                                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                                </div>
                                                {{-- </div> --}}
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
        <!-- /.row -->
    </section><!-- /.container-fluid -->
@endsection
@section('plugin')
    <script src="{{ asset('template/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{ asset('template/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('template/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
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
                "scrollY": "150px",
                "scrollX": true,
                "language": {
                    "url": "http://cdn.datatables.net/plug-ins/1.10.9/i18n/Indonesian.json",
                    "sEmptyTable": "Tidak ada data di database"
                }
            });
            //Initialize Select2 Elements
            $('.select2').select2()
        });
        //Date picker
        $('#tanggal').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            icons: {
                time: 'far fa-clock'
            }
        });
    </script>
@endsection
