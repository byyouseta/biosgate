<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Survei RSUP Surakarta</title>
    <link rel="icon" href="{{ asset('/image/LogoRSUP.png') }}">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('template/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="{{ asset('template/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
    <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('template/plugins/toastr/toastr.min.css') }}">

    @hasSection('head')
        @yield('head')
    @endif

    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('template/dist/css/adminlte.min.css') }}">
</head>

<body
    class="hold-transition layout-top-nav {{ session('anak') == null ? 'layout-footer-fixed' : '' }} {{ session('cucu') != null ? 'layout-footer-fixed' : '' }}">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
            <div class="container">
                <a href="/survei" class="navbar-brand">
                    <img src="{{ asset('image/LogoRSUP.png') }}" alt="RSUP Surakarta Logo"
                        class="brand-image img-circle elevation-3" style="opacity: .8">
                    <span class="brand-text font-weight-light">RSUP Surakarta</span>
                </a>

                <button class="navbar-toggler order-1" type="button" data-toggle="collapse"
                    data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse order-3" id="navbarCollapse">
                    <!-- Left navbar links -->
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a href="/survei/pengaduan" class="nav-link">Form Keluhan Pasien</a>
                        </li>
                        <li class="nav-item">
                            <a href="survei/kepuasan" class="nav-link">Form Kepuasan Pasien</a>
                        </li>

                    </ul>

                    <!-- SEARCH FORM -->

                </div>

                <!-- Right navbar links -->

            </div>
        </nav>
        <!-- /.navbar -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container">
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
            @yield('content')

            <!-- /.content -->

            <!-- /.content-wrapper -->
            @php
                if (session('anak') == null) {
                    echo '</div>';
                }
            @endphp

            <!-- Main Footer -->
            @include('layouts.footer')
            @php
                if (session('anak') != null) {
                    echo '</div>';
                }
            @endphp

        </div>
        <!-- ./wrapper -->

        <!-- REQUIRED SCRIPTS -->

        <!-- REQUIRED SCRIPTS -->
        @hasSection('get')
            @yield('get')
        @endif
        <!-- jQuery -->
        <script src="{{ asset('template/plugins/jquery/jquery.min.js') }}"></script>
        <!-- Bootstrap 4 -->
        <script src="{{ asset('template/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
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
