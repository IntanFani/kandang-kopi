<aside class="sidebar">
    <div class="logo">
        <div class="logo-icon"><img src="{{ asset('assets/images/logo.png') }}" alt="Logo" /></div>
        <div class="logo-text"><h1>KANDANG KOPI</h1></div>
    </div>

    <ul class="nav-menu">
        <li class="nav-item {{ Request::is('admin/dashboard*') ? 'active' : '' }}">
            <a href="{{ url('/admin/dashboard') }}" class="bi bi-house-door"><span>Dashboard</span></a>
        </li>
        <li class="nav-item {{ Request::is('admin/pengeluaran') ? 'active' : '' }}">
            <a href="{{ url('/admin/pengeluaran') }}" class="bi bi-wallet">
                <span>Pengeluaran</span>
            </a>
        </li>
        <li class="nav-item {{ Request::is('admin/laporan') ? 'active' : '' }}">
            <a href="{{ url('/admin/laporan') }}" class="bi bi-clipboard-data">
                <span>Laporan</span>
            </a>
        </li>
        <li class="nav-item {{ Request::is('admin/menu') ? 'active' : '' }}">
            <a href="{{ url('/admin/menu') }}" class="bi bi-cup-hot">
                <span>Menu</span>
            </a>
        </li>
        <li class="nav-item {{ Request::is('admin/stock') ? 'active' : '' }}">
            <a href="{{ url('/admin/stock') }}" class="bi bi-database">
                <span>Stock</span>
            </a>
        </li>
        <li class="nav-item {{ Request::is('admin/kelola_akun') ? 'active' : '' }}">
            <a href="{{ url('/admin/kelola_akun') }}" class="bi bi-person-add">
                <span>Kelola Akun</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="bi bi-box-arrow-right" onclick="document.getElementById('modalLogout').style.display='flex'">
                <span>Keluar</span>
            </a>
        </li>
    </ul>
</aside>