@extends('layouts.main')

@section('title', 'Penjualan')

@section('container')

    {{-- CSS khusus halaman stok --}}
    <link rel="stylesheet" href="{{ asset('assets/css/stock.css') }}">

    {{-- CARD UTAMA --}}
    <div class="main-card">
        {{-- HEADER CARD --}}
        <div class="main-card-header">
            <h2>Manajemen Stok</h2>
            <p>Kelola stok bahan baku Kandang Kopi</p>
        </div>

        <div class="btn-row">
            <button class="btn" onclick="openStockModal('masuk')">
                <i class="bi bi-plus-circle"></i> Stok Masuk
            </button>

            <button class="btn" onclick="openStockModal('keluar')">
                <i class="bi bi-dash-circle"></i> Stok Keluar
            </button>
        </div>

        {{-- BODY CARD --}}
        <div class="main-card-body">
            {{-- ================= STOK SAAT INI ================= --}}
            <div class="card">
                <h3>Stok Saat Ini</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Bahan Baku</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stokSaatIni as $bahan => $total)
                            <tr>
                                <td>{{ $bahan }}</td>
                                <td>{{ $total }}</td>
                                <td>
                                    @if ($total > 0)
                                        <span class="status aman"></span> Aman
                                    @else
                                        <span class="status habis"></span> Habis
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">Belum ada data stok</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ================= RIWAYAT STOK ================= --}}
            <div class="card">
                <h3>Riwayat Stok</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Bahan</th>
                            <th>Masuk</th>
                            <th>Keluar</th>
                            <th>Total Harga</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($riwayat as $r)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($r->tanggal_masuk)->format('d/m/Y') }}</td>
                                <td>{{ $r->bahan_baku }}</td>
                                <td>{{ $r->jumlah > 0 ? '+' . $r->jumlah : '-' }}</td>
                                <td>{{ $r->jumlah < 0 ? abs($r->jumlah) : '-' }}</td>
                                <td>Rp {{ number_format($r->harga, 0, ',', '.') }}</td>
                                <td>{{ $r->jumlah > 0 ? 'Stok Masuk' : 'Stok Keluar' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ================= MODAL STOK ================= --}}
            <div class="modal-overlay" id="stokModal">
                <div class="modal-content">
                    <h2 id="modalTitle">Tambah Stok</h2>
                    <form method="POST" action="{{ route('stok.store') }}">
                        @csrf
                        <input type="hidden" name="tipe" id="tipeStok">

                        <div class="form-group">
                            <label>Bahan Baku</label>
                            {{-- Input teks untuk stok masuk, dropdown untuk stok keluar --}}
                            <div id="bahanBakuContainer">
                                <input type="text" name="bahan_baku" id="bahanBakuInput" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Jumlah</label>
                                <input type="number" name="jumlah" id="jumlahInput" step="any" required>
                            </div>
                            <div class="form-group">
                                <label>Satuan</label>
                                <select name="satuan" id="satuanSelect" class="form-control" required>
                                    <option value="">-- Pilih Satuan --</option>
                                    <option value="Kg">Kilogram (Kg)</option>
                                    <option value="Gram">Gram (gr)</option>
                                    <option value="Liter">Liter (L)</option>
                                    <option value="Ml">Mililiter (ml)</option>
                                    <option value="Pcs">Pieces (Pcs)</option>
                                    <option value="Pack">Pack</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Tanggal</label>
                                <input type="date" name="tanggal_masuk" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div id="expiredGroup" class="form-group">
                                <label>Tanggal Expired</label>
                                <input type="date" name="expired">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Harga Satuan (Rp)</label>
                                <input type="number" id="hargaSatuan" placeholder="Contoh: 5000" required>
                            </div>
                            <div class="form-group">
                                <label>Total Harga (Otomatis)</label>
                                <input type="number" name="harga" id="totalHarga" readonly class="readonly-input">
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn-secondary" onclick="closeModal()">Batal</button>
                            <button type="submit" class="btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
    // Data stok saat ini dari PHP ke JavaScript
    const stokSaatIni = @json($stokSaatIni);

    function openStockModal(type) {
        const modal = document.getElementById('stokModal');
        const title = document.getElementById('modalTitle');
        const tipe = document.getElementById('tipeStok');
        const bahanContainer = document.getElementById('bahanBakuContainer');
        const expiredGroup = document.getElementById('expiredGroup');
        const totalHargaInput = document.getElementById('totalHarga');
        const hargaSatuanInput = document.getElementById('hargaSatuan');
        const jumlahInput = document.getElementById('jumlahInput');

        modal.style.display = 'flex';
        tipe.value = type;
        
        // Reset hitungan
        totalHargaInput.value = '';
        hargaSatuanInput.value = '';

        if (type === 'masuk') {
            title.innerText = 'Tambah Stok Masuk';
            expiredGroup.style.display = 'block';
            // Input bebas untuk stok masuk
            bahanContainer.innerHTML = '<input type="text" name="bahan_baku" class="form-control" placeholder="Nama bahan baru..." required>';
        } else {
            title.innerText = 'Tambah Stok Keluar';
            expiredGroup.style.display = 'none';
            // Dropdown otomatis dari stok yang ada untuk stok keluar
            let options = '<option value="">-- Pilih Bahan Tersedia --</option>';
            Object.keys(stokSaatIni).forEach(bahan => {
                options += `<option value="${bahan}">${bahan} (Sisa: ${stokSaatIni[bahan]})</option>`;
            });
            bahanContainer.innerHTML = `<select name="bahan_baku" class="form-control" required>${options}</select>`;
        }
    }

    // Fungsi Hitung Otomatis
    function hitungTotal() {
        const jumlah = parseFloat(document.getElementById('jumlahInput').value) || 0;
        const hargaSatuan = parseFloat(document.getElementById('hargaSatuan').value) || 0;
        const total = jumlah * hargaSatuan;
        document.getElementById('totalHarga').value = Math.round(total);
    }

    // Event listener untuk perhitungan real-time
    document.getElementById('jumlahInput').addEventListener('input', hitungTotal);
    document.getElementById('hargaSatuan').addEventListener('input', hitungTotal);

    function closeModal() {
        document.getElementById('stokModal').style.display = 'none';
    }

    // Menutup modal jika klik di luar modal-content
    window.onclick = function(event) {
        const modal = document.getElementById('stokModal');
        if (event.target == modal) {
            closeModal();
        }
    }
</script>

<style>
    .readonly-input {
        background-color: #f0f0f0;
        cursor: not-allowed;
        font-weight: bold;
        color: #333;
    }
    .form-control {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    .status.aman { background-color: #28a745; display: inline-block; width: 10px; height: 10px; border-radius: 50%; }
    .status.habis { background-color: #dc3545; display: inline-block; width: 10px; height: 10px; border-radius: 50%; }
</style>
@endpush
@endsection