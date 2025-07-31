<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">

<head>
    <meta charset="utf-8">
    <META NAME="robots" CONTENT="noindex,nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('/image/LogoRSUP.png') }}">
    <title>{{ config('app.name') }}</title>

    <!-- Google Font: Source Sans Pro -->
    {{-- <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"> --}}
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('template/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{ asset('template/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="{{ asset('template/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
    <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('template/plugins/toastr/toastr.min.css') }}">

    @hasSection('head')
    @yield('head')
    @endif
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('template/dist/css/adminlte.min.css') }}">
    <!-- Custom Theme for Kemenkes -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

</head>

{{-- <body class="hold-transition sidebar-mini"> --}}
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        @include('layouts.header')
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-light-primary elevation-4">
            <!-- Brand Logo -->
            <a href="/home" class="brand-link text-left d-flex align-items-left justify-content-left">
                <img src="{{ asset('image/GateLogo3.png') }}" alt="Gate Logo"
                    class="brand-image img-circle elevation-3" style="opacity: 1; width: 30px; height: 30px; margin-right: 10px;">
                <span class="brand-text font-weight-dark mb-0">{{ config('app.name') }}</span>
            </a>


            <!-- Sidebar -->
            @include('layouts.sidebar')
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">
                                @if (!empty(session('cucu')))
                                {{ session('cucu') }}
                                @elseif(!empty(session('anak')))
                                {{ session('anak') }}
                                @else
                                {{ session('ibu') }}
                                @endif
                            </h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">{{ session('ibu') }}</a></li>
                                @if (!empty(session('anak')))
                                <li class="breadcrumb-item active">{{ session('anak') }}</li>
                                @endif
                                @if (!empty(session('cucu')))
                                <li class="breadcrumb-item active">{{ session('cucu') }}</li>
                                @endif
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            {{-- <div class="content"> --}}
                @yield('content')
                {{-- </div> --}}
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
            <div class="p-3">
                <h5>Title</h5>
                <p>Sidebar content</p>
            </div>
        </aside>
        <!-- /.control-sidebar -->

        <!-- Main Footer -->
        @include('layouts.footer')
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->
    @hasSection('get')
    @yield('get')
    @endif
    <!-- jQuery -->
    <script src="{{ asset('template/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('template/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- overlayScrollbars -->
    <script src="{{ asset('template/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <!-- SweetAlert2 -->
    <script src="{{ asset('template/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Toastr -->
    <script src="{{ asset('template/plugins/toastr/toastr.min.js') }}"></script>
    @hasSection('plugin')
    @yield('plugin')
    @endif
    <!-- AdminLTE App -->
    <script src="{{ asset('template/dist/js/adminlte.min.js') }}"></script>

    {{-- Session Sweet Alert --}}
    @if (session()->has('sukses'))
    <script>
        // swal.fire({
            //     title: "{{ __('Sukses!') }}",
            //     text: "{{ Session::get('sukses') }}",
            //     icon: "success",
            // });
            // Pakai Toast
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            toastr.success("{{ Session::get('sukses') }}");
    </script>
    @endif

    @if (session()->has('sukses2'))
    <script>
        const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: false,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })

            Toast.fire({
                icon: 'success',
                title: "{{ Session::get('sukses2') }}"
            })
    </script>
    @endif

    @if (session()->has('error'))
    <script>
        // swal.fire({
            //     title: "{{ __('Error!') }}",
            //     text: "{{ Session::get('error') }}",
            //     type: "error",
            //     icon: "warning",
            // });
            toastr.error("{{ Session::get('error') }}");
    </script>
    @endif

    @if ($errors->any())
    <script>
        // swal.fire({
            //     title: "{{ __('Error dalam pengisian form!') }}",
            //     text: "{{ implode(' ', $errors->all()) }}",
            //     type: "error",
            //     icon: "warning",
            // });
            // Pakai toastr
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            toastr.error("Kesalahan dalam pengisian data. {{ implode(' ', $errors->all()) }}");
    </script>
    @endif

    <script>
        $('.delete-confirm').on('click', function(event) {
            event.preventDefault();
            const url = $(this).attr('href');
            Swal.fire({
                title: 'Apa kamu yakin?',
                text: "Data akan dihapus dari sistem",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            })
        });
    </script>
</body>

</html>
