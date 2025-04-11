@extends('layouts.app')

@section('content')
<div class="container text-center mt-5">
    <img src="{{ asset('image/file-not-found.jpg') }}" alt="File Not Found" class="img-fluid mb-4" style="max-width: 300px;">
    <h1 class="text-danger">File Not Found</h1>
    <p class="text-muted">The file you are looking for does not exist or has been removed.</p>
    <a href="{{ url('http://123.231.165.35:9990/daftaronline') }}" class="btn btn-primary">Go Back Home</a>
</div>
@endsection
