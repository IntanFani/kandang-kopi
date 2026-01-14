@extends('layouts.main')

@section('title', 'Produk & Menu')

@section('container')
<link rel="stylesheet" href="{{ asset('assets/css/menu.css') }}" />

<div class="content">
    <div class="top-actions">
    {{-- Tambahkan Form di sini --}}
    <form action="{{ route('menu.index') }}" method="GET" style="display: flex; gap: 8px; flex: 1;">
        {{-- Jika sedang memfilter kategori, pastikan kategorinya tetap ikut terbawa saat mencari --}}
        @if(request('category'))
            <input type="hidden" name="category" value="{{ request('category') }}">
        @endif
        
        <input type="text" name="search" class="search-box" placeholder="Cari Menu..." value="{{ request('search') }}" />
        <button type="submit" class="btn-tambah" style="background: #a67c52;">
            <i class="bi bi-search"></i>
        </button>
    </form>
    
    <button class="btn-tambah" id="openModalMenu">+ Tambah Menu</button>
</div>

   <div class="filter-row">
    {{-- Tombol untuk menampilkan semua menu --}}
        <a href="{{ route('menu.index') }}" class="filter {{ !request('category') ? 'active' : '' }}">Semua</a>

        {{-- Tombol kategori dinamis --}}
        <a href="{{ route('menu.index', ['category' => 'Base Espresso']) }}" class="filter {{ request('category') == 'Base Espresso' ? 'active' : '' }}">Base Espresso</a>
        
        <a href="{{ route('menu.index', ['category' => 'Signature']) }}" class="filter {{ request('category') == 'Signature' ? 'active' : '' }}">Signature</a>
        
        <a href="{{ route('menu.index', ['category' => 'Non-Coffee']) }}" class="filter {{ request('category') == 'Non-Coffee' ? 'active' : '' }}">Non-Coffee</a>
        
        <a href="{{ route('menu.index', ['category' => 'Food']) }}" class="filter {{ request('category') == 'Food' ? 'active' : '' }}">Food</a>
    </div>

    <div class="menu-grid">
    @forelse($menus as $item)
        <div class="card-menu">
            <img src="{{ $item->foto ? asset('images/' . $item->foto) : asset('images/default-kopi.jpg') }}" alt="{{ $item->nama_menu }}" />
            
            <h3>{{ strtoupper($item->nama_menu) }}</h3>
            <p>Rp. {{ number_format($item->harga, 0, ',', '.') }}</p>

            <div class="card-footer">
                {{-- FORM UNTUK UBAH STATUS --}}
                <form action="{{ route('menu.status', $item->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" style="background: none; border: none; padding: 0; text-align: left;">
                        <div class="status {{ $item->status ? 'active' : 'inactive' }}" style="cursor: pointer;">
                            <span class="dot"></span> {{ $item->status ? 'Active' : 'Non-Active' }}
                        </div>
                    </button>
                </form>

                <div class="card-icons">
                    {{-- Icon Edit --}}
                   <i class="bi bi-pencil-square btn-edit" 
                        style="cursor: pointer; color: orange;" 
                        onclick="openEditModal({{ $item->id }}, '{{ $item->nama_menu }}', {{ $item->harga }}, '{{ $item->kategori }}')">
                    </i>

                    {{-- FORM UNTUK HAPUS --}}
                    <form action="{{ route('menu.destroy', $item->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus menu ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background: none; border: none; color: inherit; cursor: pointer;">
                            <i class="bi bi-trash btn-delete" style="color: red;"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="no-data">
            <p>Belum ada menu yang ditambahkan ke database.</p>
        </div>
    @endforelse
</div>
</div>

{{-- Modal Tambah Menu --}}
<div class="modal-overlay" id="modalMenu">
    <div class="modal-content">
        <h2>Tambahkan Menu</h2>
        
        <form action="{{ route('menu.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
        <label>Nama Menu</label>
        {{-- **WAJIB ADA name="nama_menu"** --}}
        <input type="text" name="nama_menu" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Kategori</label>
        {{-- **WAJIB ADA name="kategori"** --}}
        <select name="kategori" class="form-control" required>
            <option value="Base Espresso">Base Espresso</option>
            <option value="Non-Coffee">Non-Coffee</option>
            <option value="Signature">Signature</option>
            <option value="Food">Food</option>
        </select>
    </div>

    <div class="form-group">
        <label>Harga</label>
        {{-- **WAJIB ADA name="harga"** --}}
        <input type="number" name="harga" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Foto Menu</label>
        {{-- **WAJIB ADA name="foto"** --}}
        <input type="file" name="foto" class="form-control">
    </div>

    {{-- Input hidden untuk status dan stok karena tidak ada di form --}}
    <input type="hidden" name="status" value="1">
    <input type="hidden" name="stok" value="0">

    <div class="modal-footer">
        <button type="submit" class="btn-primary">Simpan Menu</button>
    </div>
</form>
    </div>
</div>

<div class="modal-overlay" id="modalEditMenu">
    <div class="modal-content">
        <h2>Edit Menu</h2>
        <form id="formEditMenu" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Nama Menu</label>
                <input type="text" name="nama_menu" id="edit_nama" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <select name="kategori" id="edit_kategori" class="form-control" required>
                    <option value="Base Espresso">Base Espresso</option>
                    <option value="Non-Coffee">Non-Coffee</option>
                    <option value="Signature">Signature</option>
                    <option value="Food">Food</option>
                </select>
            </div>
            <div class="form-group">
                <label>Harga</label>
                <input type="number" name="harga" id="edit_harga" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Foto Menu (Kosongkan jika tidak diganti)</label>
                <input type="file" name="foto" class="form-control">
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-primary">Update Menu</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Success --}}
@if(session('success'))
<div class="modal-overlay" id="modalMenuSuccess" style="display: flex;">
    <div class="modal-content success-modal">
        <div class="success-icon">✅</div>
        <h2>Berhasil!</h2>
        <p>{{ session('success') }}</p>
        <div class="modal-footer">
            <button type="button" class="btn-primary" onclick="this.closest('.modal-overlay').style.display='none'">Kembali</button>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalMenu = document.getElementById('modalMenu');
        const openBtn = document.getElementById('openModalMenu');
        const closeBtn = document.getElementById('closeModalMenu');
        const fileInput = document.getElementById('fileInput');
        const fileNameDisplay = document.getElementById('fileNameDisplay');

        if (openBtn) openBtn.onclick = () => modalMenu.style.display = 'flex';
        if (closeBtn) closeBtn.onclick = () => modalMenu.style.display = 'none';

        if (fileInput) {
            fileInput.onchange = function() {
                if (this.files && this.files[0]) {
                    fileNameDisplay.innerText = "Terpilih: " + this.files[0].name;
                }
            };
        }

        window.onclick = (event) => {
            if (event.target == modalMenu) modalMenu.style.display = 'none';
        };
    });

    function openEditModal(id, nama, harga, kategori) {
    const modal = document.getElementById('modalEditMenu');
    const form = document.getElementById('formEditMenu');
    
    // Set Action URL Form (mengarahkan ke route update)
    form.action = '/admin/menu/' + id;
    
    // Isi field input dengan data yang ada
    document.getElementById('edit_nama').value = nama;
    document.getElementById('edit_harga').value = harga;
    document.getElementById('edit_kategori').value = kategori;
    
    modal.style.display = 'flex';
    }

    // Tambahkan logika menutup modal edit jika klik di luar area modal
    window.onclick = (event) => {
        const modalTambah = document.getElementById('modalMenu');
        const modalEdit = document.getElementById('modalEditMenu');
        if (event.target == modalTambah) modalTambah.style.display = 'none';
        if (event.target == modalEdit) modalEdit.style.display = 'none';
    };
</script>
@endpush