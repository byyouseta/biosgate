@extends('layouts.master')

@section('head')
    <meta http-equiv="refresh" content="60;" />
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
                    <!-- DIRECT CHAT PRIMARY -->
                    <div class="card card-default card-outline direct-chat direct-chat-info" >
                        <div class="card-header">
                            <h3 class="card-title">Detail Percakapan</h3>
                            <div class="card-tools">
                                {{-- <span title="{{ count($chat) }} Pesan" class="badge bg-primary">{{ count($chat) }}</span>
                                <a href="#" >
                                    <i class="far fa-bell"></i>
                                    <span class="badge badge-warning navbar-badge">15</span>
                                </a> --}}

                                <a href="{{ route('wa.kotakPesan') }}" class="btn btn-sm btn-secondary">Kembali</a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body" >
                            <!-- Conversations -->
                            <div class="direct-chat-messages" id="chat-container" style="height: 400px; overflow-y: auto;">
                                @foreach($chat as $message)
                                    @php
                                        $isMe = $message->from === $myNumber;
                                        $name = $isMe ? 'Saya' : str_replace('@c.us', '', $message->from);
                                        $side = $isMe ? 'right' : '';
                                    @endphp

                                    <div class="direct-chat-msg {{ $side }}">
                                        <div class="direct-chat-infos clearfix">
                                            <span class="direct-chat-name float-{{ $isMe ? 'right' : 'left' }}">{{ $name }}</span>
                                            <span class="direct-chat-timestamp float-{{ $isMe ? 'left' : 'right' }}">
                                                {{ \Carbon\Carbon::parse($message->timestamp)->format('d M Y H:i') }}
                                            </span>
                                        </div>
                                        <img class="direct-chat-img" src="{{ asset('image/' . ($isMe ? 'doctor.png' : 'user.png')) }}" alt="User Image">
                                        <div class="direct-chat-text">
                                            {{ $message->body }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <!-- /.direct-chat-messages -->
                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer">
                            <form action="{{ route('wa.kirim') }}" method="POST">
                                @csrf
                                <input type="hidden" name="penerima" value="{{ str_replace('@c.us', '', $targetNumber) }}">
                                <div class="input-group">
                                    <input type="text" name="pesan" placeholder="Ketik pesan ..." class="form-control">
                                    <span class="input-group-append">
                                        <button type="submit" class="btn btn-primary">Kirim</button>
                                    </span>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!--/.direct-chat -->
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
    <script>
        $(function() {
            $('#example').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "order": [[2, 'desc']],
                "info": false,
                "autoWidth": false,
                "responsive": false,
                //"scrollY": "300px",
                //"scrollX": false,
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var chatContainer = document.getElementById("chat-container");
            chatContainer.scrollTop = chatContainer.scrollHeight;
        });
    </script>

@endsection
