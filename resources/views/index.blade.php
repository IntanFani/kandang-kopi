{{-- <!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/navbar.css') }}" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
    />
    <title>Kandang Kopi - Dashboard</title>
  </head>
  <body>
    <aside class="sidebar">
      <div class="logo">
        <div class="logo-icon"><img src="{{ asset('assets/images/logo.png') }}"></div>
        <div class="logo-text">
          <h1>KANDANG KOPI</h1>
        </div>
      </div>

      <ul class="nav-menu">
        <li class="nav-item active">
          <a href="dashboard.html" class="bi bi-house-door"
            ><span>Dashboard</span></a
          >
        </li>
        <li class="nav-item">
          <a href="penjualan.html" class="bi bi-currency-dollar"
            ><span>Penjualan</span></a
          >
        </li>
        <li class="nav-item">
          <a href="pengeluaran.html" class="bi bi-wallet"
            ><span>Pengeluaran</span></a
          >
        </li>
        <li class="nav-item">
          <a href="laporan.html" class="bi bi-clipboard-data"
            ><span>Laporan</span></a
          >
        </li>
        <li class="nav-item">
          <a href="menu.html" class="bi bi-cup-hot"><span>Menu</span></a>
        </li>
        <li class="nav-item">
          <a href="stock.html" class="bi bi-database">
            <span>Stock</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="kelola_akun.html" class="bi bi-person-add"
            ><span>Kelola Akun</span></a
          >
        </li>
        <li class="nav-item">
          <a href="keluar.html" class="bi bi-box-arrow-right"
            ><span>Keluar</span></a
          >
        </li>
      </ul>
    </aside>

    <main class="main-content">
      <div class="topbar">
        <div>
          <h1>Hallo, Hesti Wahyuni 👋</h1>
          <p>Halo! Ini update keuangan harianmu</p>
        </div>

        <div class="top-right">
          <div class="bel-avatar">
            <i class="bi bi-bell" style="font-size: 25px"></i>
          </div>
          <div class="user">
            <i class="bi bi-person-circle" style="font-size: 25px"></i>
            <div class="profile">
              Hesti Wahyuni <br />
              whesti143@gmail.com
            </div>
          </div>
        </div>
      </div>

      <section class="stats-cards">
        <div class="stat-card income">
          <div class="stat-label">Total Pendapatan Hari Ini</div>
          <div class="stat-value">Rp. 1.250.000</div>
        </div>
        <div class="stat-card expense">
          <div class="stat-label">Total Pengeluaran Hari Ini</div>
          <div class="stat-value">Rp. 530.000</div>
        </div>
        <div class="stat-card profit">
          <div class="stat-label">Laba Bersih</div>
          <div class="stat-value">Rp. 720.000</div>
        </div>
      </section>

      <section class="chart-section">
        <h4>Pendapatan Vs Pengeluaran</h4>
        <canvas id="barChart"></canvas>
      </section>

      <section class="transactions-section">
        <h2 class="section-title">Transaksi Terbaru</h2>
        <table class="transactions-table">
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Jenis</th>
              <th>Kategori</th>
              <th>Nominal</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>02 Sept 2025</td>
              <td>Penjualan</td>
              <td>Espresso</td>
              <td>Rp. 125.000</td>
            </tr>
            <tr>
              <td>01 Sept 2025</td>
              <td>Pengeluaran</td>
              <td>Bahan Baku</td>
              <td>Rp. 100.000</td>
            </tr>
            <tr>
              <td>31 Ags 2025</td>
              <td>Penjualan</td>
              <td>Ice Latte</td>
              <td>Rp. 115.000</td>
            </tr>
            <tr>
              <td>30 Ags 2025</td>
              <td>Penjualan</td>
              <td>Ice Latte</td>
              <td>Rp. 115.000</td>
            </tr>
            <tr>
              <td>29 Ags 2025</td>
              <td>Penjualan</td>
              <td>Ice Latte</td>
              <td>Rp. 115.000</td>
            </tr>
            <tr>
              <td>28 Ags 2025</td>
              <td>Penjualan</td>
              <td>Ice Latte</td>
              <td>Rp. 115.000</td>
            </tr>
          </tbody>
        </table>
      </section>
    </main>
    <div class="modal-overlay" id="modalLogout">
        <div class="modal-content logout-card">
            <button class="close-x" id="closeLogoutX">&times;</button>
            <div class="logout-icon">
                <svg width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="#A68B6D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M13.8 12H3"/>
                </svg>
            </div>
            <h3>ANDA YAKIN INGIN KELUAR ?</h3>
            <button class="btn-primary btn-logout-confirm" id="confirmLogout">KELUAR</button>
        </div>
    </div>
    
    <script src="js/script.js"></script>
  </body>
</html>
 --}}