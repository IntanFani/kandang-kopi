<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="{{ asset('assets/css/transaksi.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/kasir.css') }}"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"/>
    <title>Kandang Kopi - Dashboard Kasir</title>
</head>
<body>
    <aside class="sidebar">
        <div class="logo">
            <div class="logo-icon"><img src="{{ asset('assets/images/logo.png') }}" alt="Logo" /></div>
            <div class="logo-text">
                <h1>KANDANG KOPI</h1>
            </div>
        </div>

        <ul class="nav-menu">
            <li class="nav-item active">
                <a href="{{ route('kasir.dashboard') }}" class="bi bi-currency-dollar"><span>Penjualan</span></a>
            </li>
            <li class="nav-item logout-item">
                <a href="#" class="bi bi-box-arrow-right" id="openLogout"><span>Keluar</span></a>
            </li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="topbar">
            <div>
                <h1>Hallo, {{ auth()->user()->name }} 👋</h1>
                <p>Transaksi</p>
            </div>
            <div class="top-right" >
                <div class="user" style="padding: 10px 40px;">
                    <i class="bi bi-person-circle"></i>
                    <div class="profile" style="font-size: 18px;">
                        {{ auth()->user()->name }} <br />
                        <small>{{ auth()->user()->email }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="transaction-card">
            <h2 class="card-title">Transaksi</h2>
            <hr class="divider">
            <div class="transaction-form">
                <div class="form-row">
                    <div class="input-group">
                        <label>Kode Transaksi</label>
                        <span class="colon">:</span>
                        <input type="text" id="kodeOtomatis" value="TRS-{{ time() }}" readonly>
                    </div>
                    <div class="input-group">
                        <label>Nama Customer</label>
                        <span class="colon">:</span>
                        <input type="text" id="namaCustomer" placeholder="Masukkan nama">
                    </div>
                </div>
                <div class="form-row">
                    <div class="input-group">
                        <label>Tanggal</label>
                        <span class="colon">:</span>
                        <input type="text" value="{{ date('d/m/Y') }}" readonly>
                    </div>
                    <div class="input-group">
                        <label>Nama Kasir</label>
                        <span class="colon">:</span>
                        <input type="text" value="{{ auth()->user()->name }}" readonly>
                    </div>
                </div>
            </div>

            <table class="pos-table" id="tablePesanan">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Menu</th>
                        <th>Harga Menu</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <div class="transaction-footer">
                <div class="calc-box gray">Total : 
                    <div class="display-value"><span>Rp.</span><span id="totalHarga">0</span></div>
                </div>
                <div class="calc-box white">Tunai : 
                    <div class="input-with-prefix"><span>Rp.</span><input type="number" id="inputTunai" placeholder="0"></div>
                </div>
                <div class="calc-box gray">Kembalian : 
                    <div class="display-value"><span>Rp.</span><span id="totalKembalian">0</span></div>
                </div>
                <button class="btn-print" onclick="prosesTransaksi()">Cetak Nota</button>
            </div>
        </div>

        <div class="selection-bar">
            <div class="select-title"><i class="bi bi-cart3"></i> Pilih Menu</div>
        </div>

        <div class="filter-container">
            <button class="filter-btn active" onclick="filterMenu('semua', this)">Semua</button>
            
            @foreach($categories as $cat)
                <button class="filter-btn" onclick="filterMenu('{{ $cat }}', this)">
                    {{ $cat }}
                </button>
            @endforeach
        </div>

        <div class="menu-grid">
            @forelse($menus as $menu)
                <div class="menu-item" data-kategori="{{ $menu->kategori }}" onclick="tambahKeKeranjang('{{ $menu->nama_menu }}', {{ $menu->harga }})">
                    <div class="menu-img">
                        <img src="{{ $menu->foto ? asset('images/' . $menu->foto) : asset('images/default.png') }}" alt="{{ $menu->nama_menu }}">
                    </div>
                    <div class="menu-desc">
                        <h4>{{ strtoupper($menu->nama_menu) }}</h4>
                        <p>Rp. {{ number_format($menu->harga, 0, ',', '.') }}</p>
                    </div>
                </div>
            @empty
                <p class="empty-msg">Belum ada menu yang aktif.</p>
            @endforelse
        </div>    
    </main>

    <div class="modal-overlay" id="modalCetakNota">
        <div class="modal-content nota-modal">
            <div class="nota-header">
                <h3>Preview Nota</h3>
                <button class="close-x" onclick="tutupPreview()">&times;</button>
            </div>
            
            <div class="nota-body">
                <div class="nota-paper" id="areaCetakStruk">
                    <div class="nota-logo">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo">
                        <p class="brand-name">KANDANG KOPI</p>
                    </div>
                    <div class="nota-info">
                        <div class="info-row"><span>Kode</span><span id="previewKode">...</span></div>
                        <div class="info-row"><span>Customer</span><span id="previewCustomer">-</span></div> <div class="info-row"><span>Tanggal</span><span id="previewTgl"></span></div>
                        <div class="info-row"><span>Kasir</span><span>{{ auth()->user()->name }}</span></div>
                    </div>  
                    <hr class="dashed-line">
                    <table class="nota-table">
                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th class="text-center">Qty</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody id="previewItemTable"></tbody>
                    </table>
                    <hr class="dashed-line">
                    <div class="nota-total-section">
                        <div class="info-row"><span>Total :</span><span id="previewTotal"></span></div>
                        <div class="info-row"><span>Tunai :</span><span id="previewTunai"></span></div>
                        <div class="info-row bold-row">
                            <span>Kembali :</span><span id="previewKembali"></span>
                        </div>
                    </div>
                    <p class="thanks-msg">Terimakasih Atas Kunjungan Anda</p>
                </div>
            </div>

            <div class="nota-footer">
                <button class="btn-cancel" onclick="tutupPreview()">Cancel</button>
                <button class="btn-confirm-print" onclick="printStruk()">Print Sekarang</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="modalPrintSuccess">
        <div class="modal-content success-card">
            <div class="success-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <h3>Berhasil Dicetak</h3>
            <p>Transaksi Sudah Tersimpan</p>
            <button class="btn-primary" onclick="resetHalaman()">Oke</button>
        </div>
    </div>

    <div class="modal-overlay" id="modalLogout">
        <div class="modal-content logout-card">
            <h3>ANDA YAKIN INGIN KELUAR ?</h3>
            <div class="logout-actions">
                <button type="button" class="btn-secondary" id="closeLogout">BATAL</button>
                
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-primary">KELUAR</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Logika Logout
        const modalLogout = document.getElementById('modalLogout');
        document.getElementById('openLogout').onclick = () => modalLogout.style.display = 'flex';
        document.getElementById('closeLogout').onclick = () => modalLogout.style.display = 'none';

        let keranjang = [];

        function tambahKeKeranjang(nama, harga) {
            const index = keranjang.findIndex(item => item.nama === nama);
            if (index !== -1) {
                keranjang[index].qty++;
                keranjang[index].subtotal = keranjang[index].qty * harga;
            } else {
                keranjang.push({ nama, harga, qty: 1, subtotal: harga });
            }
            updateTabel();
        }

        function updateTabel() {
            const tbody = document.querySelector('#tablePesanan tbody');
            tbody.innerHTML = '';
            let total = 0;
            keranjang.forEach((item, index) => {
                total += item.subtotal;
                tbody.innerHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.nama}</td>
                        <td>Rp. ${item.harga.toLocaleString('id-ID')}</td>
                        <td>
                            <div class="qty-control">
                                <button class="qty-btn" onclick="ubahQty(${index}, -1)">-</button>
                                <span class="qty-val">${item.qty}</span>
                                <button class="qty-btn" onclick="ubahQty(${index}, 1)">+</button>
                            </div>
                        </td>
                        <td>Rp. ${item.subtotal.toLocaleString('id-ID')}</td>
                        <td><button class="delete-btn" onclick="hapusItem(${index})"><i class="bi bi-trash"></i></button></td>
                    </tr>`;
            });
            document.getElementById('totalHarga').innerText = total.toLocaleString('id-ID');
            hitungKembalian();
        }

        function ubahQty(index, delta) {
            keranjang[index].qty += delta;
            if (keranjang[index].qty < 1) hapusItem(index);
            else {
                keranjang[index].subtotal = keranjang[index].qty * keranjang[index].harga;
                updateTabel();
            }
        }

        function hapusItem(index) {
            keranjang.splice(index, 1);
            updateTabel();
        }

        document.getElementById('inputTunai').addEventListener('input', hitungKembalian);

        function hitungKembalian() {
            const total = parseInt(document.getElementById('totalHarga').innerText.replace(/\./g, '')) || 0;
            const tunai = parseInt(document.getElementById('inputTunai').value) || 0;
            const kembalian = tunai - total;
            document.getElementById('totalKembalian').innerText = (kembalian < 0 ? 0 : kembalian).toLocaleString('id-ID');
        }

        async function prosesTransaksi() {
            const tunai = parseInt(document.getElementById('inputTunai').value) || 0;
            const total = parseInt(document.getElementById('totalHarga').innerText.replace(/\./g, '')) || 0;
            const namaCust = document.getElementById('namaCustomer').value; // Ambil nama

            if (keranjang.length === 0) return alert("Pilih menu dulu!");
            if (tunai < total) return alert("Uang tunai tidak mencukupi!");

            const dataKirim = {
                total_harga: total,
                tunai: tunai,
                kembalian: tunai - total,
                nama_customer: namaCust, // Kirim ke controller
                items: keranjang,
                _token: '{{ csrf_token() }}'
            };

            try {
                const response = await fetch("{{ route('transaksi.store') }}", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(dataKirim)
                });
                const result = await response.json();
                if (result.success) {
                    // FIX UNDEFINED: Ambil kode dari response controller
                    dataKirim.kode = result.kode;
                    tampilkanPreview(dataKirim);
                } else {
                    alert("Gagal: " + result.message);
                }
            } catch (e) { alert("Kesalahan koneksi ke server."); }
        }

        function tampilkanPreview(data) {
            // 1. Mengisi Info Header Nota
            document.getElementById('previewKode').innerText = data.kode || "N/A";
            document.getElementById('previewCustomer').innerText = data.nama_customer || "-";
            document.getElementById('previewTgl').innerText = new Date().toLocaleDateString('id-ID');

            // 2. MENGISI NOMINAL UANG (Tambahkan Bagian Ini)
            // Gunakan .toLocaleString('id-ID') agar muncul format titik ribuan yang rapi
            document.getElementById('previewTotal').innerText = "Rp. " + data.total_harga.toLocaleString('id-ID');
            document.getElementById('previewTunai').innerText = "Rp. " + data.tunai.toLocaleString('id-ID');
            document.getElementById('previewKembali').innerText = "Rp. " + data.kembalian.toLocaleString('id-ID');

            // 3. Mengisi Tabel Item
            const tbody = document.getElementById('previewItemTable');
            tbody.innerHTML = data.items.map(i => `
                <tr>
                    <td style="padding: 5px 0;">${i.nama}</td>
                    <td style="text-align: center;">${i.qty}</td>
                    <td style="text-align: right;">${i.subtotal.toLocaleString('id-ID')}</td>
                </tr>`).join('');
            
            // 4. Munculkan Modal
            document.getElementById('modalCetakNota').style.display = 'flex';
        }
        
        function printStruk() {
            const content = document.getElementById('areaCetakStruk').innerHTML;
            const win = window.open('', '', 'width=400,height=600');
            win.document.write(`
                <html>
                <head>
                    <title>Print Nota</title>
                    <style>
                        body { font-family: 'Courier New', monospace; width: 300px; padding: 10px; font-size: 12px; }
                        .nota-logo { text-align: center; margin-bottom: 10px; }
                        .nota-logo img { width: 50px; }
                        .brand-name { font-weight: bold; text-align: center; display: block; }
                        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
                        th { border-bottom: 1px dashed #000; text-align: left; }
                        .info-row { display: flex; justify-content: space-between; margin-bottom: 2px; }
                        .dashed-line { border-top: 1px dashed #000; margin: 10px 0; }
                        .text-right { text-align: right; }
                        .text-center { text-align: center; }
                        .thanks-msg { text-align: center; margin-top: 15px; }
                    </style>
                </head>
                <body>${content}</body>
                </html>
            `);
            win.document.close();
            win.focus();
            setTimeout(() => {
                win.print();
                win.close();
            }, 500);

            document.getElementById('modalCetakNota').style.display = 'none';
            document.getElementById('modalPrintSuccess').style.display = 'flex';
        }

        function tutupPreview() { document.getElementById('modalCetakNota').style.display = 'none'; }
        function resetHalaman() { location.reload(); }

        function filterMenu(kategori, btn) {
            // 1. Ubah status tombol aktif
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            // 2. Lakukan penyaringan item
            const items = document.querySelectorAll('.menu-item');
            
            items.forEach(item => {
                const kategoriMenu = item.getAttribute('data-kategori');
                
                if (kategori === 'semua' || kategoriMenu === kategori) {
                    item.style.display = 'block'; // Tampilkan jika cocok
                } else {
                    item.style.display = 'none';  // Sembunyikan jika tidak cocok
                }
            });
        }
    </script>
</body>
</html>