<div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
            <img src="{{ asset('template/dist/img/avatar5.png') }}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
            <a href="#" class="d-block">{{ Auth::user()->name }}</a>
        </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
   with font-awesome or any other icon font library -->
            <li class="nav-item">
                <a href="/home" class="nav-link">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>
                        Dashboard
                    </p>
                </a>
                {{-- <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="#" class="nav-link active">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Active Page</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Inactive Page</p>
                        </a>
                    </li>
                </ul> --}}
            </li>



            <li class="nav-item">
                <a href="/saldo" class="nav-link">
                    <i class="nav-icon fas fa-balance-scale"></i>
                    <p>
                        Saldo Awal
                        {{-- <span class="right badge badge-danger">New</span> --}}
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-exchange-alt"></i>
                    <p>
                        Data Transaksi
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="/layanan/kesehatan" class="nav-link">
                            <i class="nav-icon fas fa-hospital"></i>
                            <p>Data Layanan Kesehatan</p>
                        </a>
                    </li>
                    {{-- @can('approval-list') --}}
                    <li class="nav-item">
                        <a href="/layanan/bor" class="nav-link">
                            <i class="nav-icon far fa-chart-bar"></i>
                            <p>Data Statistik</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/layanan/sdm" class="nav-link">
                            <i class="nav-icon fas fa-hospital-user"></i>
                            <p>Data SDM</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/layanan/visit" class="nav-link">
                            <i class="nav-icon fas fa-procedures"></i>
                            <p>Data Visit/IKT</p>
                        </a>
                    </li>
                    {{-- @endcan --}}
                </ul>
            </li>
            <li class="nav-item">
                <a href="/setting" class="nav-link">
                    <i class="nav-icon fas fa-cog"></i>
                    <p>
                        Setting
                        {{-- <span class="right badge badge-danger">New</span> --}}
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" onclick="event.preventDefault();
                document.getElementById('logout-form').submit();">
                    <span style="color: Tomato;">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                    </span>
                    {{-- <i class="nav-icon fas fa-sign-out"></i> --}}
                    <p>
                        Logout
                    </p>
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</div>
