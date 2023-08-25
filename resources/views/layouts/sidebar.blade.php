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
                <a href="/home" class="nav-link @if (@session('ibu') == 'Daskboard') active @endif">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>
                        Dashboard
                    </p>
                </a>
            </li>
            {{-- @if (Auth::user()->can('bios-kesehatan-list'))
                <li class="nav-item @if (@session('ibu') == 'BIOS G2') menu-open @endif">
                    <a href="#" class="nav-link @if (@session('ibu') == 'BIOS G2') active @endif">
                        <i class="nav-icon fas fa-exchange-alt "></i>
                        <p>
                            BIOS G2
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @can('bios-kesehatan-list')
                            <li class="nav-item">
                                <a href="/saldo" class="nav-link @if (@session('anak') == 'Layanan Kesehatan') active @endif">
                                    <i class="nav-icon fas fa-heartbeat"></i>
                                    <p>
                                        Layanan Kesehatan
                                    </p>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif --}}
            @if (Auth::user()->can('facelift-kesehatan-list') ||
                    Auth::user()->can('facelift-statistik-list') ||
                    Auth::user()->can('bios-pemasukan-list') ||
                    Auth::user()->can('bios-pengeluaran-list') ||
                    Auth::user()->can('bios-saldo-list'))
                <li class="nav-item @if (@session('ibu') == 'BIOS facelift') menu-open @endif">
                    <a href="#" class="nav-link @if (@session('ibu') == 'BIOS facelift') active @endif">
                        <i class="nav-icon fas fa-exchange-alt"></i>
                        <p>
                            BIOS facelift
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @can('facelift-kesehatan-list')
                            <li class="nav-item">
                                <a href="/layanan/kesehatan"
                                    class="nav-link @if (@session('anak') == 'Data Layanan Kesehatan') active @endif">
                                    <i class="nav-icon fas fa-hospital"></i>
                                    <p>Data Layanan Kesehatan</p>
                                </a>
                            </li>
                        @endcan
                        @can('facelift-statistik-list')
                            <li class="nav-item">
                                <a href="/layanan/bor" class="nav-link @if (@session('anak') == 'Data Statistik') active @endif">
                                    <i class="nav-icon far fa-chart-bar"></i>
                                    <p>Data Statistik</p>
                                </a>
                            </li>
                        @endcan
                        @can('facelift-sdm-list')
                            <li class="nav-item">
                                <a href="/layanan/sdm" class="nav-link @if (@session('anak') == 'Data SDM') active @endif">
                                    <i class="nav-icon fas fa-hospital-user"></i>
                                    <p>Data SDM</p>
                                </a>
                            </li>
                        @endcan
                        @if (Auth::user()->can('bios-pemasukan-list') ||
                                Auth::user()->can('bios-pengeluaran-list') ||
                                Auth::user()->can('bios-saldo-list'))
                            <li class="nav-item @if (@session('anak') == 'Layanan Keuangan') menu-open @endif">
                                <a href="#" class="nav-link @if (@session('anak') == 'Layanan Keuangan') active @endif">
                                    <i class="nav-icon fas fa-dollar-sign"></i>
                                    <p>
                                        Layanan Keuangan
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    @can('bios-pemasukan-list')
                                        <li class="nav-item">
                                            <a href="/penerimaan"
                                                class="nav-link @if (@session('cucu') == 'Data Penerimaan') active @endif">
                                                <i class="nav-icon fas fa-arrow-circle-down"></i>
                                                <p>
                                                    Data Penerimaan
                                                </p>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('bios-pengeluaran-list')
                                        <li class="nav-item">
                                            <a href="/pengeluaran"
                                                class="nav-link @if (@session('cucu') == 'Data Pengeluaran') active @endif">
                                                <i class="nav-icon fas fa-arrow-circle-up"></i>
                                                <p>
                                                    Data Pengeluaran
                                                </p>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('bios-saldo-list')
                                        <li class="nav-item">
                                            <a href="/saldo/operasional"
                                                class="nav-link @if (@session('cucu') == 'Saldo Operasional') active @endif">
                                                <i class="nav-icon fas fa-wallet"></i>
                                                <p>
                                                    Saldo Operasional
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="/saldo/pengelolaankas"
                                                class="nav-link @if (@session('cucu') == 'Saldo Pengelolaan Kas') active @endif">
                                                <i class="nav-icon fas fa-wallet"></i>
                                                <p>
                                                    Saldo Pengelolaan Kas
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="/saldo/kelolaan"
                                                class="nav-link @if (@session('cucu') == 'Saldo Dana Kelolaan') active @endif">
                                                <i class="nav-icon fas fa-wallet"></i>
                                                <p>
                                                    Saldo Dana Kelolaan
                                                </p>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('bios-saldo-chart')
                                        <li class="nav-item">
                                            <a href="/saldo/laporan"
                                                class="nav-link @if (@session('cucu') == 'Laporan Saldo') active @endif">
                                                <i class="nav-icon fas fa-chart-bar"></i>
                                                <p>
                                                    Laporan Saldo
                                                </p>
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        @endif
                        @can('facelift-ikt-list')
                            <li class="nav-item">
                                <a href="/layanan/visit" class="nav-link @if (@session('anak') == 'Data Visit/IKT') active @endif">
                                    <i class="nav-icon fas fa-procedures"></i>
                                    <p>Data Visit/IKT</p>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif
            @if (Auth::user()->can('pasienbaru-list') || Auth::user()->can('reff-pasien'))
                <li class="nav-item @if (@session('ibu') == 'RS Online') menu-open @endif">
                    <a href="#" class="nav-link  @if (@session('ibu') == 'RS Online') active @endif">
                        <i class="nav-icon fas fa-hospital-alt"></i>
                        <p>
                            RS Online
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    @if (Auth::user()->can('pasienbaru-list') || Auth::user()->can('pasienterlapor-list'))
                        <ul class="nav nav-treeview">
                            <li class="nav-item @if (@session('anak') == 'Data Pasien') menu-open @endif">
                                <a href="#" class="nav-link @if (@session('anak') == 'Data Pasien') active @endif">
                                    <i class="nav-icon fas fa-hospital-user"></i>
                                    <p>
                                        Data Pasien
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    @can('pasienbaru-list')
                                        <li class="nav-item">
                                            <a href="/rsonline/pasienbaru"
                                                class="nav-link @if (@session('cucu') == 'Pasien Ranap') active @endif">
                                                <i class="nav-icon fas fa-procedures"></i>
                                                <p>Pasien Ranap</p>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('pasienrajal-list')
                                        <li class="nav-item">
                                            <a href="/rsonline/pasienrajal"
                                                class="nav-link @if (@session('cucu') == 'Pasien Rajal/IGD') active @endif">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>Pasien Rajal/IGD</p>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('pasienterlapor-list')
                                        <li class="nav-item">
                                            <a href="/rsonline/pasienterlapor"
                                                class="nav-link @if (@session('cucu') == 'Pasien Terlapor') active @endif">
                                                <i class="fas fa-user-injured nav-icon"></i>
                                                <p>Pasien Terlapor</p>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('pasienkeluar-list')
                                        <li class="nav-item">
                                            <a href="/rsonline/pasienpulang"
                                                class="nav-link @if (@session('cucu') == 'Pasien Keluar') active @endif">
                                                <i class="nav-icon fas fa-walking"></i>
                                                <p>Pasien Keluar</p>
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        </ul>
                    @endif
                    @if (Auth::user()->can('reff-geo') || Auth::user()->can('reff-pasien'))
                        <ul class="nav nav-treeview">
                            <li class="nav-item @if (@session('anak') == 'Data Referensi') menu-open @endif">
                                <a href="#" class="nav-link @if (@session('anak') == 'Data Referensi') active @endif">
                                    <i class="nav-icon fas fa-heartbeat"></i>
                                    <p>
                                        Data Referensi
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item @if (@session('cucu') == 'Geografi') active @endif">
                                        <a href="/rsonline/geografi"
                                            class="nav-link @if (@session('cucu') == 'Geografi') active @endif">
                                            <i class="far fa-dot-circle nav-icon"></i>
                                            <p>Geografi</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="/rsonline/statuspasien"
                                            class="nav-link @if (@session('cucu') == 'Status Pasien') active @endif">
                                            <i class="fas fa-user-injured nav-icon"></i>
                                            <p>Status Pasien</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="/rsonline/vaksin"
                                            class="nav-link @if (@session('cucu') == 'Status Vaksin') active @endif">
                                            <i class="fas fa-syringe nav-icon"></i>
                                            <p>Status Vaksin</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    @endif
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/rsonline/antrian"
                                class="nav-link @if (@session('anak') == 'Antrian') active @endif">
                                <i class="nav-icon fas fa-user-clock"></i>
                                <p>
                                    Antrian
                                </p>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif
            @if (Auth::user()->can('kanker-rajal-list') || Auth::user()->can('kanker-ranap-list'))
                <li class="nav-item @if (@session('ibu') == 'Data Kanker') menu-open @endif">
                    <a href="#" class="nav-link @if (@session('ibu') == 'Data Kanker') active @endif">
                        <i class="nav-icon fas fa-disease"></i>
                        <p>
                            Data Kanker
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @can('kanker-ranap-list')
                            <li class="nav-item">
                                <a href="/kanker/ranap"
                                    class="nav-link @if (@session('anak') == 'Pasien Ranap') active @endif">
                                    <i class="nav-icon fas fa-procedures"></i>
                                    <p>
                                        Pasien Ranap
                                    </p>
                                </a>
                            </li>
                        @endcan
                        @can('kanker-rajal-list')
                            <li class="nav-item">
                                <a href="/kanker/rajal"
                                    class="nav-link @if (@session('anak') == 'Pasien Rajal/IGD') active @endif">
                                    <i class="nav-icon fas fa-user-injured"></i>
                                    <p>
                                        Pasien Rajal/IGD
                                    </p>
                                </a>
                            </li>
                        @endcan
                        @can('kanker-terlapor-list')
                            <li class="nav-item">
                                <a href="/kanker/terlapor"
                                    class="nav-link @if (@session('anak') == 'Pasien Terlapor') active @endif">
                                    <i class="nav-icon fas fa-desktop"></i>
                                    <p>
                                        Pasien Terlapor
                                    </p>
                                </a>
                            </li>
                        @endcan
                        @can('kanker-referensi-list')
                            <li class="nav-item">
                                <a href="/kanker/referensi"
                                    class="nav-link @if (@session('anak') == 'Referensi Data') active @endif">
                                    <i class="nav-icon far fa-bookmark"></i>
                                    <p>
                                        Referensi Data
                                    </p>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif
            @if (Auth::user()->can('vedika-list'))
                <li class="nav-item @if (@session('ibu') == 'Vedika') menu-open @endif">
                    <a href="#" class="nav-link @if (@session('ibu') == 'Vedika') active @endif">
                        <i class="nav-icon fas fa-book-medical"></i>
                        <p>
                            Vedika
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/vedika/ranap"
                                class="nav-link @if (@session('anak') == 'Pasien Ranap') active @endif">
                                <i class="nav-icon fas fa-procedures"></i>
                                <p>
                                    Pasien Ranap
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/vedika/rajal"
                                class="nav-link @if (@session('anak') == 'Pasien Rajal') active @endif">
                                <i class="nav-icon fas fa-user-injured"></i>
                                <p>
                                    Pasien Rajal/IGD
                                </p>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif
            @if (Auth::user()->can('survei-list'))
                <li class="nav-item @if (@session('ibu') == 'Survei') menu-open @endif">
                    <a href="#" class="nav-link @if (@session('ibu') == 'Survei') active @endif">
                        <i class="nav-icon fas fa-vote-yea"></i>
                        <p>
                            Survei
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        {{-- @can('user-list') --}}
                        <li class="nav-item">
                            <a href="/survei/datapengaduan"
                                class="nav-link @if (@session('anak') == 'Data Pengaduan') active @endif">
                                <i class="nav-icon fas fa-exclamation-triangle"></i>
                                <p>
                                    Data Pengaduan
                                </p>
                            </a>
                        </li>
                        {{-- @endcan
                        @can('bank-list') --}}
                        <li class="nav-item">
                            <a href="/survei/datakepuasan"
                                class="nav-link @if (@session('anak') == 'Data Kepuasan') active @endif">
                                <i class="nav-icon fas fa-check"></i>
                                <p>
                                    Data Kepuasan
                                </p>
                            </a>
                        </li>
                        {{-- @endcan --}}
                    </ul>
                </li>
            @endif
            @if (Auth::user()->can('operasi-booking-list') || Auth::user()->can('operasi-jadwal-list'))
                <li class="nav-item @if (@session('ibu') == 'Operasi') menu-open @endif">
                    <a href="#" class="nav-link @if (@session('ibu') == 'Operasi') active @endif">
                        <i class="nav-icon fas fa-heartbeat"></i>
                        <p>
                            Operasi
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @can('operasi-booking-list')
                            <li class="nav-item">
                                <a href="/operasi/booking"
                                    class="nav-link @if (@session('anak') == 'Booking Operasi') active @endif">
                                    <i class="nav-icon fas fa-book-medical"></i>
                                    <p>
                                        Booking Operasi
                                    </p>
                                </a>
                            </li>
                        @endcan
                        @can('operasi-jadwal-list')
                            <li class="nav-item">
                                <a href="/operasi/jadwal"
                                    class="nav-link @if (@session('anak') == 'Jadwal Operasi') active @endif">
                                    <i class="nav-icon fas fa-procedures"></i>
                                    <p>
                                        Jadwal Operasi
                                    </p>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif
            @can('pesan-list')
                <li class="nav-item @if (@session('ibu') == 'Pesan') menu-open @endif">
                    <a href="#" class="nav-link @if (@session('ibu') == 'Pesan') active @endif">
                        <i class="nav-icon fab fa-whatsapp"></i>
                        <p>
                            Pesan
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @can('pesan-setting')
                            <li class="nav-item">
                                <a href="/pesan" class="nav-link @if (@session('anak') == 'Setting') active @endif">
                                    <i class="nav-icon fas fa-cog"></i>
                                    <p>
                                        Setting
                                    </p>
                                </a>
                            </li>
                        @endcan
                        @can('pesan-kirim')
                            <li class="nav-item">
                                <a href="/pesan/kirim" class="nav-link @if (@session('anak') == 'Kirim Pesan') active @endif">
                                    <i class="nav-icon fas fa-envelope"></i>
                                    <p>
                                        Kirim Pesan
                                    </p>
                                </a>
                            </li>
                        @endcan

                    </ul>
                </li>
            @endcan

            {{-- @can('satu-sehat-create') --}}
            {{-- <li class="nav-item">
                <a href="/pesan" class="nav-link @if (@session('ibu') == 'Pesan') active @endif">
                    <i class="nav-icon fab fa-whatsapp"></i>
                    <p>
                        Pesan
                    </p>
                </a>
            </li> --}}
            {{-- @endcan --}}

            @can('satu-sehat-create')
                <li class="nav-item">
                    <a href="/satusehat" class="nav-link @if (@session('ibu') == 'Satu Sehat') active @endif">
                        <i class="nav-icon fas fa-stethoscope"></i>
                        <p>
                            Satu Sehat
                        </p>
                    </a>
                </li>
            @endcan
            @if (Auth::user()->can('setting-list') || Auth::user()->can('schedule-list'))
                <li class="nav-item @if (@session('ibu') == 'Setting') menu-open @endif">
                    <a href="#" class="nav-link @if (@session('ibu') == 'Setting') active @endif">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>
                            Setting
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @can('setting-list')
                            <li class="nav-item">
                                <a href="/setting" class="nav-link @if (@session('anak') == 'API Setting') active @endif">
                                    <i class="nav-icon fas fa-rocket"></i>
                                    <p>
                                        API Setting
                                        {{-- <span class="right badge badge-danger">New</span> --}}
                                    </p>
                                </a>
                            </li>
                        @endcan
                        @can('schedule-list')
                            <li class="nav-item">
                                <a href="/schedule" class="nav-link @if (@session('anak') == 'Schedule Update') active @endif">
                                    <i class="nav-icon far fa-calendar-check"></i>
                                    <p>
                                        Schedule Update
                                        {{-- <span class="right badge badge-danger">New</span> --}}
                                    </p>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif
            @if (Auth::user()->can('user-list') || Auth::user()->can('bank-list'))
                <li class="nav-item @if (@session('ibu') == 'Master Data') menu-open @endif">
                    <a href="#" class="nav-link @if (@session('ibu') == 'Master Data') active @endif">
                        <i class="nav-icon fas fa-database"></i>
                        <p>
                            Master Data
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @can('user-list')
                            <li class="nav-item">
                                <a href="/master/user" class="nav-link @if (@session('anak') == 'User') active @endif">
                                    <i class="nav-icon fas fa-users"></i>
                                    <p>
                                        User
                                    </p>
                                </a>
                            </li>
                        @endcan
                        @can('bank-list')
                            <li class="nav-item">
                                <a href="/master/bank" class="nav-link @if (@session('anak') == 'Data Bank BIOS') active @endif">
                                    <i class="nav-icon fas fa-money-check-alt"></i>
                                    <p>
                                        Data Bank BIOS
                                    </p>
                                </a>
                            </li>
                        @endcan
                        @can('vedika-berkas-list')
                            <li class="nav-item">
                                <a href="/master/vedika"
                                    class="nav-link @if (@session('anak') == 'Berkas Vedika') active @endif">
                                    <i class="nav-icon fas fa-file-medical"></i>
                                    <p>
                                        Berkas Vedika
                                    </p>
                                </a>
                            </li>
                        @endcan
                        @can('role-list')
                            <li class="nav-item">
                                <a href="/roles" class="nav-link @if (@session('anak') == 'Role Group') active @endif">
                                    <i class="nav-icon fas fa-user-cog"></i>
                                    <p>
                                        Role Group
                                    </p>
                                </a>
                            </li>
                        @endcan
                        @can('permission-list')
                            <li class="nav-item">
                                <a href="/permission" class="nav-link @if (@session('anak') == 'List Akses') active @endif">
                                    <i class="nav-icon far fa-folder-open"></i>
                                    <p>
                                        List Akses
                                    </p>
                                </a>
                            </li>
                        @endcan
                        @can('geografi-list')
                            <li class="nav-item">
                                <a href="/master/dummygeo"
                                    class="nav-link @if (@session('anak') == 'Dummy Geografi') active @endif">
                                    <i class="nav-icon fas fa-globe-asia"></i>
                                    <p>
                                        Dummy Geografi
                                    </p>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif

            @can('profil-edit')
                <li class="nav-item">
                    <a href="/profil" class="nav-link @if (@session('ibu') == 'Profil') active @endif">
                        <i class="nav-icon fas fa-user"></i>
                        <p>
                            Profil
                        </p>
                    </a>
                </li>
            @endcan
            <li class="nav-item">
                <a href="#" class="nav-link"
                    onclick="event.preventDefault();
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
