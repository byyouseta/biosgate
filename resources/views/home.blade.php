@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Dashboard</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        You are logged in!

                        Kemungkinan kamu sedang login dari Browser:
                        {{ $useragent->ua->toString() }}
                        {{-- OS: {{ $useragent->os->toString() }}
                        Device: {{ $useragent->device->toString() }} --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
