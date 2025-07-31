@extends('layouts.master')

@section('head')
    <meta http-equiv="refresh" content="120;" />
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
                            Kotak Pesan
                        </div>
                        <div class="card-body">
                            <div style="overflow-x:auto;">
                                {{-- <div class="list-group">
                                    @forelse($conversations as $chat)
                                        <a href="#" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h5 class="mb-1">{{ str_replace('@c.us', '', $chat['chat_with']) }}</h5>
                                                <small>{{ \Carbon\Carbon::parse($chat['last_message_time'])->diffForHumans() }}</small>
                                            </div>
                                            <p class="mb-1 text-muted">{{ $chat['last_message'] }}</p>
                                        </a>
                                    @empty
                                        <div class="alert alert-info">No chats available.</div>
                                    @endforelse
                                </div> --}}
                                <table id="example" class="table table-bordered table-hover table-sm">
                                    <thead>
                                        <tr >
                                            {{-- <th>No</th> --}}
                                            <th>Chat With</th>
                                            <th>Last Message</th>
                                            <th style="vertical-align:center;">Last Message Time</th>
                                            <th style="vertical-align:center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($conversations as $index => $chat)
                                            <tr>
                                                {{-- <td>{{ $index + 1 }}</td> --}}
                                                <td>{{ str_replace('@c.us', '', $chat['chat_with']) }}</td>
                                                <td>{{ $chat['last_message'] }}</td>
                                                <td>{{ \Carbon\Carbon::parse($chat['last_message_time'])->format('d-m-Y H:i') }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('wa.percakapan', str_replace('@c.us', '', $chat['chat_with'])) }}" class="btn btn-sm btn-primary">View</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-4">
                    {{-- @if($percakapan)
                        <div class="card card-default card-outline direct-chat direct-chat-primary">
                            <div class="card-header">
                                <h3 class="card-title">Detail Percakapan</h3>
                                <div class="card-tools">
                                    <span title="{{ count($chat) }} Pesan" class="badge bg-primary">{{ count($chat) }}</span>
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-tool" title="Contacts" data-widget="chat-pane-toggle">
                                        <i class="fas fa-comments"></i>
                                    </button>
                                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <!-- Conversations -->
                                <div class="direct-chat-messages">
                                    @foreach($chat as $message)
                                        @php
                                            $isMe = $message->from === $myNumber;
                                            $name = $isMe ? 'Saya' : $message->from;
                                            $side = $isMe ? 'right' : '';
                                        @endphp

                                        <div class="direct-chat-msg {{ $side }}">
                                            <div class="direct-chat-infos clearfix">
                                                <span class="direct-chat-name float-{{ $isMe ? 'right' : 'left' }}">{{ $name }}</span>
                                                <span class="direct-chat-timestamp float-{{ $isMe ? 'left' : 'right' }}">
                                                    {{ \Carbon\Carbon::parse($message->timestamp)->format('d M Y H:i') }}
                                                </span>
                                            </div>
                                            <img class="direct-chat-img" src="{{ asset('image/' . ($isMe ? 'me.png' : 'user.png')) }}" alt="User Image">
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
                                <form action="#" method="POST">
                                    @csrf
                                    <input type="hidden" name="to" value="{{ $targetNumber }}">
                                    <div class="input-group">
                                        <input type="text" name="message" placeholder="Ketik pesan ..." class="form-control">
                                        <span class="input-group-append">
                                            <button type="submit" class="btn btn-primary">Kirim</button>
                                        </span>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif --}}
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
                "info": true,
                "autoWidth": false,
                "responsive": false,
                //"scrollY": "300px",
                //"scrollX": false,
            });
        });
    </script>
@endsection
