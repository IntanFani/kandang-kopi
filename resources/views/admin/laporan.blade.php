@extends('layouts.main')

@section('title', 'Laporan Keuangan')

@section('container')
<link rel="stylesheet" href="{{ asset('assets/css/laporan.css') }}" />

{{-- Statistik Cards Section --}}
    <div class="cards">
        <div class="card">
            <p>Total Pemasukan</p>
            <h2>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h2>
        </div>

        <div class="card">
            <p>Total Pengeluaran</p>
            <h2>Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h2>
        </div>

        <div class="card">
            <p>Profit / Laba Bersih</p>
            <h2 style="color: {{ $profit < 0 ? '#e74c3c' : '#ffffff' }}">
                Rp {{ number_format($profit, 0, ',', '.') }}
            </h2>
        </div>
    </div>

<div class="content">
    {{-- Search & Filter Section --}}
    <div class="search-filter">
        <form action="{{ route('laporan.index') }}" method="GET" class="search-form">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" name="search" placeholder="Cari Kode atau Catatan..." value="{{ request('search') }}" />
            </div>
            
            <div class="filter-wrapper">
                <button type="button" class="btn-filter" id="btnFilter">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                
                <div class="filter-popup" id="filterPopup">
                    <div class="filter-header">Filter Laporan</div>
                        <div class="filter-body">
                            <div class="filter-group">
                                <label>Tipe Transaksi:</label>
                                <select name="tipe" class="form-control">
                                    <option value="semua" {{ request('tipe') == 'semua' ? 'selected' : '' }}>Semua Transaksi</option>
                                    <option value="pemasukan" {{ request('tipe') == 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                                    <option value="pengeluaran" {{ request('tipe') == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                                </select>
                            </div>

                            <div class="filter-group">
                                <label>Metode Filter:</label>
                                <select name="filter_type" id="filterType" class="form-control" onchange="toggleFilterInputs()">
                                    <option value="range" {{ request('filter_type') == 'range' ? 'selected' : '' }}>Rentang Tanggal</option>
                                    <option value="bulan" {{ request('filter_type') == 'bulan' ? 'selected' : '' }}>Bulan</option>
                                    <option value="tahun" {{ request('filter_type') == 'tahun' ? 'selected' : '' }}>Tahun</option>
                                </select>
                            </div>

                            <div id="rangeInputs" class="filter-method">
                                <div class="filter-group">
                                    <label>Dari:</label>
                                    <input type="date" name="dari" value="{{ request('dari') }}" class="form-control">
                                </div>
                                <div class="filter-group">
                                    <label>Sampai:</label>
                                    <input type="date" name="sampai" value="{{ request('sampai') }}" class="form-control">
                                </div>
                            </div>

                            <div id="bulanInputs" class="filter-method" style="display: none;">
                                <div class="filter-group">
                                    <label>Pilih Bulan:</label>
                                    <select name="bulan" class="form-control">
                                        @foreach(range(1, 12) as $m)
                                            <option value="{{ $m }}" {{ request('bulan', date('m')) == $m ? 'selected' : '' }}>
                                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="filter-group">
                                    <label>Pilih Tahun:</label>
                                    <select name="tahun_bulan" class="form-control">
                                        @foreach(range(date('Y'), date('Y')-5) as $y)
                                            <option value="{{ $y }}" {{ request('tahun_bulan', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div id="tahunInputs" class="filter-method" style="display: none;">
                                <div class="filter-group">
                                    <label>Pilih Tahun:</label>
                                    <select name="tahun" class="form-control">
                                        @foreach(range(date('Y'), date('Y')-5) as $y)
                                            <option value="{{ $y }}" {{ request('tahun', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="btn-apply">Terapkan Filter</button>
                        </div>
                </div>
            </div>
        </form>

        <a href="{{ route('laporan.pdf', request()->query()) }}" class="btn-pdf" title="Export PDF" style="text-decoration: none;">
            <i class="bi bi-filetype-pdf"></i> PDF
        </a>
        <a href="{{ route('laporan.excel', request()->query()) }}" class="btn-excel" style="background-color: #27ae60; color: white; padding: 10px 19px; border-radius: 8px; text-decoration: none;">
            <i class="bi bi-file-earmark-excel"></i> Excel
        </a>
    </div>

    {{-- Tabel Riwayat --}}
    <div class="table-container">
        <h3 class="table-title">Riwayat Transaksi</h3>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Kategori / Info</th>
                    <th>Petugas</th>
                    <th>Nominal</th> 
                </tr>
            </thead>
            <tbody>
                @forelse($transaksiGabungan as $item)
                <tr>
                    <td>{{ date('d M Y', strtotime($item->tanggal)) }}</td>
                    <td>
                        <b class="text-{{ $item->warna }}">
                            {{ $item->jenis }}
                        </b>
                    </td>
                    <td>{{ $item->info }}</td>
                    <td>
                        <span class="badge-user">{{ $item->petugas }}</span> 
                    </td>
                    <td class="nominal-bold">
                        {{ $item->warna == 'green' ? '+' : '-' }} Rp {{ number_format($item->nominal, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">Tidak ada transaksi ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination-wrapper">
            {{ $transaksiGabungan->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const btnFilter = document.getElementById('btnFilter');
    const filterPopup = document.getElementById('filterPopup');
    
    btnFilter.onclick = (e) => {
        e.stopPropagation();
        filterPopup.classList.toggle('active');
    };

    window.onclick = (e) => {
        if (!e.target.closest('.filter-wrapper')) {
            filterPopup.classList.remove('active');
        }
    };

    function toggleFilterInputs() {
        const type = document.getElementById('filterType').value;
        document.querySelectorAll('.filter-method').forEach(el => el.style.display = 'none');
        
        if (type === 'range') {
            document.getElementById('rangeInputs').style.display = 'block';
        } else if (type === 'bulan') {
            document.getElementById('bulanInputs').style.display = 'block';
        } else if (type === 'tahun') {
            document.getElementById('tahunInputs').style.display = 'block';
        }
    }

    // Jalankan saat halaman load untuk menyesuaikan pilihan sebelumnya
    window.onload = toggleFilterInputs;
</script>
@endpush
