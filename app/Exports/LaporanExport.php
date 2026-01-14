<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return ['Tanggal', 'Jenis', 'Kategori / Info', 'Nominal'];
    }

    public function map($item): array
    {
        return [
            date('d M Y', strtotime($item->tanggal)),
            $item->jenis,
            $item->info,
            $item->nominal,
        ];
    }

    // 1. MEMBERIKAN WARNA BERBEDA PADA TIAP JUDUL KOLOM
    public function styles(Worksheet $sheet)
    {
        return [
            // Mewarnai seluruh baris 1 (Header)
            1 => [
                'font' => [
                    'bold' => true, 
                    'color' => ['rgb' => 'FFFFFF'] // Teks Putih
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '8B4513'] // Cokelat Tua (Tema Kandang Kopi)
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }

    // 2. MENAMBAHKAN TOTAL PROFIT DI BARIS PALING BAWAH
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $lastRow = $this->data->count() + 2; // +1 heading, +1 untuk baris baru
                $summaryRow = $lastRow + 1;

                // Hitung Total
                $totalMasuk = $this->data->where('jenis', 'Pemasukan')->sum('nominal');
                $totalKeluar = $this->data->where('jenis', 'Pengeluaran')->sum('nominal');
                $profit = $totalMasuk - $totalKeluar;

                // Tulis di baris bawah
                $event->sheet->getDelegate()->setCellValue('C' . $summaryRow, 'Total Profit (Laba Bersih):');
                $event->sheet->getDelegate()->setCellValue('D' . $summaryRow, $profit);

                // Styling Baris Profit
                $event->sheet->getStyle('C' . $summaryRow . ':D' . $summaryRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFFF00'] // Kuning Stabilo
                    ]
                ]);

                // Format mata uang untuk kolom nominal
                $event->sheet->getStyle('D2:D' . $summaryRow)
                    ->getNumberFormat()
                    ->setFormatCode('"Rp "#,##0');
            },
        ];
    }
}