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

                        {{-- <h3>Akun Bayar Khanza</h3>
                        @foreach ($data as $listbayar)
                            <p>{{ $listbayar->nama_bayar }}, kode {{ $listbayar->kd_rek }}</p>
                        @endforeach --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
