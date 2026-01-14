<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 8px; }
        th { background-color: #f2f2f2; }
        .green { color: green; font-weight: bold; }
        .red { color: red; font-weight: bold; }
        .summary { margin-top: 30px; width: 40%; float: right; }
    </style>
</head>
<body>
    <h2 class="text-center">LAPORAN KEUANGAN</h2>
    <p class="text-center">Periode: {{ date('d/m/Y', strtotime($dari)) }} - {{ date('d/m/Y', strtotime($sampai)) }}</p>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Kategori / Keterangan</th>
                <th>Nominal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr>
                <td>{{ date('d/m/Y H:i', strtotime($item->tanggal)) }}</td>
                <td class="{{ $item->warna }}">{{ $item->jenis }}</td>
                <td>{{ $item->info }}</td>
                <td class="text-right">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <table>
            <tr>
                <td>Total Pemasukan</td>
                <td class="text-right">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Pengeluaran</td>
                <td class="text-right">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</td>
            </tr>
            <tr style="background: #eee; font-weight: bold;">
                <td>Profit/Saldo</td>
                <td class="text-right">Rp {{ number_format($saldo, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>
</body>
</html>