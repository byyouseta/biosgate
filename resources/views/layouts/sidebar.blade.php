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
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-exchange-alt"></i>
                    <p>
                        BIOS G2
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="/saldo" class="nav-link">
                            <i class="nav-icon fas fa-heartbeat"></i>
                            <p>
                                Layanan Kesehatan
                                {{-- <span class="right badge badge-danger">New</span> --}}
                            </p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-dollar-sign"></i>
                            <p>
                                Layanan Keuangan
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/penerimaan" class="nav-link">
                                    <i class="nav-icon fas fa-arrow-circle-down"></i>
                                    <p>
                                        Data Penerimaan
                                    </p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/pengeluaran" class="nav-link">
                                    <i class="nav-icon fas fa-arrow-circle-up"></i>
                                    <p>
                                        Data Pengeluaran
                                    </p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/saldokeuangan" class="nav-link">
                                    <i class="nav-icon fas fa-wallet"></i>
                                    <p>
                                        Data Saldo
                                    </p>
                                </a>
                            </li>
                        </ul>
                    </li>

                </ul>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-exchange-alt"></i>
                    <p>
                        BIOS facelift
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
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-hospital-alt"></i>
                    <p>
                        RS Online
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-heartbeat"></i>
                            <p>
                                Data Referensi
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/rsonline/geografi" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Geografi</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/rsonline/statuspasien" class="nav-link">
                                    <i class="fas fa-user-injured nav-icon"></i>
                                    <p>Status Pasien</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/rsonline/vaksin" class="nav-link">
                                    <i class="fas fa-syringe nav-icon"></i>
                                    <p>Status Vaksin</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-cog"></i>
                    <p>
                        Setting
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="/setting" class="nav-link">
                            <i class="nav-icon fas fa-rocket"></i>
                            <p>
                                API Setting
                                {{-- <span class="right badge badge-danger">New</span> --}}
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/schedule" class="nav-link">
                            <i class="nav-icon far fa-calendar-check"></i>
                            <p>
                                Schedule Update
                                {{-- <span class="right badge badge-danger">New</span> --}}
                            </p>
                        </a>
                    </li>

                </ul>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-database"></i>
                    <p>
                        Master Data
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="/master/user" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                User
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/master/bank" class="nav-link">
                            <i class="nav-icon fas fa-money-check-alt"></i>
                            <p>
                                Data Bank BIOS
                                {{-- <span class="right badge badge-danger">New</span> --}}
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/roles" class="nav-link">
                            <i class="nav-icon fas fa-user-cog"></i>
                            <p>
                                Role Group
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/permission" class="nav-link">
                            <i class="nav-icon far fa-folder-open"></i>
                            <p>
                                List Akses
                            </p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="/profil" class="nav-link">
                    <i class="nav-icon fas fa-user"></i>
                    <p>
                        Profil
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
