@extends('layouts.main')

@section('title', 'Pengeluaran')

@section('container')
<link rel="stylesheet" href="{{ asset('assets/css/pengeluaran.css') }}" />

<section class="pengeluaran">
    <button class="btn-add" id="openModal">
        <i class="bi bi-plus"></i> Tambah Pengeluaran 
    </button>

    <div class="content">
        <div class="cards">
            <div class="card">
                <p>Pengeluaran Hari Ini</p>
                <h3>Rp {{ number_format($hariIni, 0, ',', '.') }}</h3>
            </div>
            <div class="card">
                <p>Pengeluaran Bulan Ini</p>
                <h3>Rp {{ number_format($bulanIni, 0, ',', '.') }}</h3>
            </div>
            <div class="card">
                <p>Total Pengeluaran</p>
                <h3>Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h3>
            </div>
        </div>

        <div class="search-filter">
            <form action="{{ route('pengeluaran.index') }}" method="GET" style="display: flex; gap: 10px; width: 100%;">
                <div class="search-box" style="flex: 1; display: flex; align-items: center; background: #fff; border: 1px solid #ddd; padding: 5px 15px; border-radius: 8px;">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Cari kategori atau catatan..." name="search" value="{{ request('search') }}" style="border: none; outline: none; width: 100%; padding: 5px 10px;" />
                    <button type="submit" style="background: none; border: none; color: #a67c52; cursor: pointer; font-weight: bold;">Cari</button>
                </div>

                <div class="filter-wrapper">
                    <button type="button" class="btn-filter" id="btnFilter">
                        <i class="bi bi-funnel"></i> Filter
                    </button>

                    <div class="filter-popup" id="filterPopup" style="display: none; position: absolute; right: 0; background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 15px; border-radius: 8px; z-index: 100; margin-top: 10px; border: 1px solid #eee; width: 300px;">
                        <div class="filter-header" style="font-weight: bold; margin-bottom: 10px;">Opsi Filter</div>
                        <div class="filter-body">
                            <div class="form-group" style="margin-bottom: 15px;">
                                <label style="font-size: 12px; display: block; margin-bottom: 5px;">Tipe Filter:</label>
                                <select name="filter_type" id="filterTypeSelect" class="form-control" style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd;">
                                    <option value="range" {{ request('filter_type') == 'range' ? 'selected' : '' }}>Rentang Tanggal</option>
                                    <option value="hari" {{ request('filter_type') == 'hari' ? 'selected' : '' }}>Hari</option>
                                    <option value="bulan" {{ request('filter_type') == 'bulan' ? 'selected' : '' }}>Bulan</option>
                                    <option value="tahun" {{ request('filter_type') == 'tahun' ? 'selected' : '' }}>Tahun</option>
                                </select>
                            </div>

                            <div id="inputRange" class="filter-input-group">
                                <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                                    <div style="flex: 1;">
                                        <label style="font-size: 11px;">Dari:</label>
                                        <input type="date" name="dari" value="{{ request('dari') }}" style="width: 100%;">
                                    </div>
                                    <div style="flex: 1;">
                                        <label style="font-size: 11px;">Sampai:</label>
                                        <input type="date" name="sampai" value="{{ request('sampai') }}" style="width: 100%;">
                                    </div>
                                </div>
                            </div>

                            <div id="inputHari" class="filter-input-group" style="display: none; margin-bottom: 10px;">
                                <label style="font-size: 11px;">Pilih Tanggal:</label>
                                <input type="date" name="tgl_hari" value="{{ request('tgl_hari', date('Y-m-d')) }}" style="width: 100%;">
                            </div>

                            <div id="inputBulan" class="filter-input-group" style="display: none; margin-bottom: 10px;">
                                <label style="font-size: 11px;">Pilih Bulan:</label>
                                <input type="month" name="tgl_bulan" value="{{ request('tgl_bulan', date('Y-m')) }}" style="width: 100%;">
                            </div>

                            <div id="inputTahun" class="filter-input-group" style="display: none; margin-bottom: 10px;">
                                <label style="font-size: 11px;">Pilih Tahun:</label>
                                <input type="number" name="tgl_tahun" min="2020" max="2099" value="{{ request('tgl_tahun', date('Y')) }}" style="width: 100%; padding: 5px; border: 1px solid #ddd; border-radius: 5px;">
                            </div>

                            <div style="display: flex; gap: 5px; margin-top: 15px;">
                                <a href="{{ route('pengeluaran.index') }}" style="text-decoration: none; font-size: 12px; background: #f4f4f4; padding: 10px; border-radius: 5px; color: #333; flex: 1; text-align: center;">Reset</a>
                                <button type="submit" style="flex: 2; background: #a67c52; color: #fff; border: none; padding: 10px; border-radius: 5px; cursor: pointer; font-weight: bold;">Terapkan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-container">
        {{-- Info Filter Aktif --}}
        <p style="margin-bottom: 10px; color: #666; font-size: 14px;">
            <i class="bi bi-info-circle"></i> 
            Menampilkan riwayat: <b>
                @if(request('filter_type') == 'hari') Tanggal {{ request('tgl_hari') }}
                @elseif(request('filter_type') == 'bulan') Bulan {{ request('tgl_bulan') }}
                @elseif(request('filter_type') == 'tahun') Tahun {{ request('tgl_tahun') }}
                @elseif(request('filter_type') == 'range') {{ request('dari') }} s/d {{ request('sampai') }}
                @else Semua Data
                @endif
            </b>
        </p>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Catatan</th>
                    <th>Nominal</th>
                    <th>Aksi</th> {{-- Tambah kolom ini --}}
                </tr>
            </thead>
            <tbody>
                @forelse($data as $p)
                <tr>
                    <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                    <td>{{ \Carbon\Carbon::parse($p->tgl_pengeluaran)->format('d/m/Y') }}</td>
                    <td>{{ ucfirst($p->kategori) }}</td>
                    <td>{{ $p->catatan ?? '-' }}</td>
                    <td style="font-weight: bold; color: #d9534f;">Rp {{ number_format($p->nominal, 0, ',', '.') }}</td>
                    <td>
                        {{-- Tombol Edit dengan Data Attributes --}}
                        <button class="btn-edit-action" 
                                data-id="{{ $p->id }}"
                                data-tgl="{{ $p->tgl_pengeluaran }}"
                                data-kategori="{{ $p->kategori }}"
                                data-catatan="{{ $p->catatan }}"
                                data-nominal="{{ $p->nominal }}"
                                style="background: #ffc107; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">
                            <i class="bi bi-pencil-square"></i>
                        </button>

                        {{-- Tombol Hapus --}}
                        <button class="btn-delete-action" 
                                data-id="{{ $p->id }}"
                                style="background: #dc3545; border: none; padding: 5px 10px; border-radius: 4px; color: white; cursor: pointer;">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">Belum ada data pengeluaran.</td></tr>
                @endforelse
            </tbody>
        </table>

        {{-- Link Paginasi Laravel --}}
        <div style="margin-top: 20px;">
            {{ $data->appends(request()->query())->links() }}
        </div>
    </div>
</section>

{{-- Modal Tambah --}}
<div class="modal-overlay" id="modalPengeluaran" style="display: none;">
    <div class="modal-content">
        <h2>Tambah Pengeluaran</h2>
        <form id="formPengeluaran">
            @csrf
            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" name="tgl_pengeluaran" required value="{{ date('Y-m-d') }}">
            </div>
            
            <div class="form-group">
                <label>Kategori</label>
                <select name="kategori" required>
                    <option value="" disabled selected>Pilih Kategori</option>
                    <option value="bahan-baku">Bahan Baku</option>
                    <option value="operasional">Operasional</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Catatan</label>
                <input type="text" name="catatan" placeholder="Contoh: Beli Kopi 3kg">
            </div>
            
            <div class="form-group">
                <label>Nominal</label>
                <input type="number" name="nominal" required>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" id="closeModal">Batal</button>
                <button type="submit" class="btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Sukses --}}
<div class="modal-overlay" id="modalPengeluaranSuccess" style="display: none;">
    <div class="modal-content text-center">
        <i class="bi bi-check-circle-fill" style="font-size: 80px; color: #28a745;"></i>
        <h3 style="margin: 20px 0;">Pengeluaran Berhasil Ditambahkan</h3>
        <button type="button" class="btn-primary-rounded" onclick="location.reload()">Kembali</button>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal-overlay" id="modalEditPengeluaran" style="display: none;">
    <div class="modal-content">
        <h2>Edit Pengeluaran</h2>
        <form id="formEditPengeluaran">
            @csrf
            @method('PUT') {{-- Method PUT untuk Update --}}
            <input type="hidden" name="id" id="edit_id">
            
            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" name="tgl_pengeluaran" id="edit_tgl" required>
            </div>
            
            <div class="form-group">
                <label>Kategori</label>
                <select name="kategori" id="edit_kategori" required>
                    <option value="bahan-baku">Bahan Baku</option>
                    <option value="operasional">Operasional</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Catatan</label>
                <input type="text" name="catatan" id="edit_catatan">
            </div>
            
            <div class="form-group">
                <label>Nominal</label>
                <input type="number" name="nominal" id="edit_nominal" required>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" id="closeEditModal">Batal</button>
                <button type="submit" class="btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Elements
    const modalAdd = document.getElementById('modalPengeluaran');
    const modalEdit = document.getElementById('modalEditPengeluaran');
    const modalSuccess = document.getElementById('modalPengeluaranSuccess');

    // Logic Modal Tambah
    document.getElementById('openModal').onclick = () => modalAdd.style.display = 'flex';
    document.getElementById('closeModal').onclick = () => modalAdd.style.display = 'none';

    // Logic Simpan (Tambah) AJAX
    document.getElementById('formPengeluaran').onsubmit = async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const response = await fetch("{{ route('pengeluaran.store') }}", {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const res = await response.json();
        if(res.success) {
            modalAdd.style.display = 'none';
            modalSuccess.querySelector('h3').innerText = "Pengeluaran Berhasil Ditambahkan";
            modalSuccess.style.display = 'flex';
        }
    };

    // Logic Edit (Populate Data & Show Modal)
    document.querySelectorAll('.btn-edit-action').forEach(btn => {
        btn.onclick = function() {
            document.getElementById('edit_id').value = this.dataset.id;
            document.getElementById('edit_tgl').value = this.dataset.tgl;
            document.getElementById('edit_kategori').value = this.dataset.kategori;
            document.getElementById('edit_catatan').value = this.dataset.catatan;
            document.getElementById('edit_nominal').value = this.dataset.nominal;
            
            modalEdit.style.display = 'flex';
        }
    });

    document.getElementById('closeEditModal').onclick = () => modalEdit.style.display = 'none';

    // Logic Update AJAX
    document.getElementById('formEditPengeluaran').onsubmit = async function(e) {
        e.preventDefault();
        const id = document.getElementById('edit_id').value;
        const formData = new FormData(this);
        
        // Laravel membutuhkan spoofing method PUT via AJAX FormData
        const response = await fetch(`/admin/pengeluaran/${id}`, {
            method: 'POST', // Tetap POST karena membawa file/FormData
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        const res = await response.json();
        if(res.success) {
            modalEdit.style.display = 'none';
            modalSuccess.querySelector('h3').innerText = "Pengeluaran Berhasil Diperbarui";
            modalSuccess.style.display = 'flex';
        }
    };

    // Logic Delete AJAX
    document.querySelectorAll('.btn-delete-action').forEach(btn => {
        btn.onclick = async function() {
            if(confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                const id = this.dataset.id;
                const response = await fetch(`/admin/pengeluaran/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const res = await response.json();
                if(res.success) {
                    location.reload();
                }
            }
        }
    });

    // --- Script Filter  ---
    const btnFilter = document.getElementById('btnFilter');
    const filterPopup = document.getElementById('filterPopup');
    const filterTypeSelect = document.getElementById('filterTypeSelect');

    if(btnFilter) {
        btnFilter.onclick = (e) => {
            e.stopPropagation();
            filterPopup.style.display = filterPopup.style.display === 'block' ? 'none' : 'block';
        };
    }

    document.addEventListener('click', (e) => {
        if (filterPopup && !e.target.closest('.filter-wrapper')) {
            filterPopup.style.display = 'none';
        }
    });

    function toggleFilterInputs() {
        const selectedType = filterTypeSelect.value;
        document.querySelectorAll('.filter-input-group').forEach(group => group.style.display = 'none');
        const activeGroup = document.getElementById('input' + selectedType.charAt(0).toUpperCase() + selectedType.slice(1));
        if(activeGroup) activeGroup.style.display = 'block';
    }

    if(filterTypeSelect) {
        filterTypeSelect.addEventListener('change', toggleFilterInputs);
        window.addEventListener('DOMContentLoaded', toggleFilterInputs);
    }
</script>
@endpush