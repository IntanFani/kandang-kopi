@extends('layouts.main')

@section('title', 'Dashboard')

@section('container')
<link rel="stylesheet" href="{{ asset('assets/css/kelola_akun.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/css/dashboard_style.css') }}" />

<div class="main-content">
    {{-- Card Statistik --}}
    <section class="stats-cards">
        <div class="stat-card income">
            <div class="stat-label">Pendapatan Bulan Ini</div>
            <div class="stat-value">{{ $totalPenjualan }}</div>
        </div>
        <div class="stat-card menu">
            <div class="stat-label">Total Menu Aktif</div>
            <div class="stat-value">{{ $totalMenu }}</div>
        </div>
        <div class="stat-card users">
            <div class="stat-label">Total Akun Kasir</div>
            <div class="stat-value">{{ $totalKasir }}</div>
        </div>
    </section>

    {{-- Grafik Perbandingan --}}
    <div class="content">
        <section class="chart-section">
            <h4 class="chart-title">Analisis Arus Kas</h4>
            <div class="chart-header">
                <div class="filter-wrapper">
                    {{-- Tombol Pemicu Popup --}}
                    <button type="button" class="btn-filter" id="btnFilterDashboard">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    
                    {{-- Konten Popup Filter --}}
                    <div class="filter-popup" id="filterPopupDashboard">
                        <div class="filter-header">Filter Analisis</div>
                        <div class="filter-body">
                            <form action="{{ route('admin.dashboard') }}" method="GET" id="filterForm">
                                
                                {{-- Dropdown Utama --}}
                                <div class="filter-group">
                                    <label>Metode Filter:</label>
                                    <select name="filter_type" id="filterType" class="form-control" onchange="toggleFilterInputs()">
                                        <option value="range" {{ request('filter_type') == 'range' ? 'selected' : '' }}>Rentang Tanggal</option>
                                        <option value="bulan" {{ request('filter_type') == 'bulan' ? 'selected' : '' }}>Bulan</option>
                                        <option value="tahun" {{ request('filter_type') == 'tahun' ? 'selected' : '' }}>Tahun</option>
                                    </select>
                                </div>

                                {{-- Input Rentang Tanggal --}}
                                <div id="input-range" class="filter-method">
                                    <div class="filter-group">
                                        <label>Dari:</label>
                                        <input type="date" name="dari" value="{{ request('dari', date('Y-m-d', strtotime('-7 days'))) }}" class="form-control">
                                    </div>
                                    <div class="filter-group">
                                        <label>Sampai:</label>
                                        <input type="date" name="sampai" value="{{ request('sampai', date('Y-m-d')) }}" class="form-control">
                                    </div>
                                </div>

                                {{-- Input Per Bulan --}}
                                <div id="input-bulan" class="filter-method" style="display: none;">
                                    <div class="filter-group">
                                        <label>Bulan:</label>
                                        <select name="bulan" class="form-control">
                                            @foreach(range(1, 12) as $m)
                                                <option value="{{ sprintf('%02d', $m) }}" {{ request('bulan', date('m')) == $m ? 'selected' : '' }}>
                                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="filter-group">
                                        <label>Tahun:</label>
                                        <select name="tahun_bulan" class="form-control">
                                            @foreach(range(date('Y'), date('Y')-5) as $y)
                                                <option value="{{ $y }}" {{ request('tahun_bulan', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Input Per Tahun --}}
                                <div id="input-tahun" class="filter-method" style="display: none;">
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
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="chart-container">
                <canvas id="dashboardBarChart"></canvas>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 1. LOGIKA POPUP FILTER 
    const btnFilter = document.getElementById('btnFilterDashboard');
    const filterPopup = document.getElementById('filterPopupDashboard');
    const filterTypeSelect = document.getElementById('filterType');

    if (btnFilter) {
        btnFilter.onclick = (e) => {
            e.stopPropagation();
            filterPopup.classList.toggle('active');
        };
    }

    window.onclick = (e) => {
        if (filterPopup && !e.target.closest('.filter-wrapper')) {
            filterPopup.classList.remove('active');
        }
    };

    function toggleFilterInputs() {
        if (!filterTypeSelect) return;
        const type = filterTypeSelect.value;
        const inputRange = document.getElementById('input-range');
        const inputBulan = document.getElementById('input-bulan');
        const inputTahun = document.getElementById('input-tahun');

        if (inputRange) inputRange.style.display = type === 'range' ? 'block' : 'none';
        if (inputBulan) inputBulan.style.display = type === 'bulan' ? 'block' : 'none';
        if (inputTahun) inputTahun.style.display = type === 'tahun' ? 'block' : 'none';
    }

    // 2. INISIALISASI GRAFIK
    document.addEventListener('DOMContentLoaded', function() {
        toggleFilterInputs();

        const canvas = document.getElementById('dashboardBarChart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        const filterType = "{{ $filter_type ?? 'range' }}"; 
        const pendapatanRaw = @json($grafikPendapatan);
        const pengeluaranRaw = @json($grafikPengeluaran);

        // Sinkronisasi Label Sumbu X
        const allLabels = [...new Set([
            ...pendapatanRaw.map(item => item.tanggal),
            ...pengeluaranRaw.map(item => item.tanggal)
        ])].sort();

        const displayLabels = allLabels.map(tgl => {
            const date = new Date(tgl + (filterType === 'tahun' ? "-01" : ""));
            if (filterType === 'tahun') {
                return date.toLocaleDateString('id-ID', { month: 'long' });
            }
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
        });

        const mapData = (sourceData) => allLabels.map(label => {
            const found = sourceData.find(item => item.tanggal === label);
            return found ? found.total : 0;
        });

        // 3. KONFIGURASI CHART.JS 
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: displayLabels,
                datasets: [
                    {
                        label: 'Pendapatan',
                        data: mapData(pendapatanRaw),
                        backgroundColor: '#a67c52',
                        borderRadius: 5
                    },
                    {
                        label: 'Pengeluaran',
                        data: mapData(pengeluaranRaw),
                        backgroundColor: '#e74c3c',
                        borderRadius: 5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Penting: Agar chart mengikuti tinggi container CSS
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            // --- BAGIAN YANG DIRUBAH UNTUK ANGKA BESAR ---
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return (value / 1000000) + ' jt';
                                } else if (value >= 1000) {
                                    return (value / 1000) + ' rb';
                                }
                                return 'Rp ' + value;
                            }
                            // --------------------------------------------
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            // --- AGAR DETAIL SAAT DI-HOVER TETAP MUNCUL RUPIAH LENGKAP ---
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) label += ': ';
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', { 
                                        style: 'currency', 
                                        currency: 'IDR', 
                                        maximumFractionDigits: 0 
                                    }).format(context.parsed.y);
                                }
                                return label;
                            }
                            // ---------------------------------------------------------
                        }
                    }
                }
            }
        });
    });
</script>
@endpush