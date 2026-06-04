<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AssetsExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $assets;

    public function __construct(array $assets)
    {
        $this->assets = $assets;
    }

    public function array(): array
    {
        $rows = [];
        foreach ($this->assets as $index => $asset) {
            $rows[] = [
                $index + 1,
                $asset['assetCode'] ?? '-',
                $asset['name'] ?? '-',
                $asset['category'] ?? '-',
                $asset['room']['name'] ?? '-',
                ucwords(str_replace('_', ' ', $asset['condition'] ?? '-')),
                ucwords(str_replace('_', ' ', $asset['status'] ?? '-')),
                isset($asset['purchaseDate']) ? date('d M Y', strtotime($asset['purchaseDate'])) : '-',
                isset($asset['purchasePrice']) ? 'Rp ' . number_format($asset['purchasePrice'], 0, ',', '.') : '-',
                isset($asset['receivedDate']) ? date('d M Y', strtotime($asset['receivedDate'])) : '-',
            ];
        }
        return $rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Aset',
            'Nama Aset',
            'Kategori',
            'Lokasi/Ruangan',
            'Kondisi',
            'Status',
            'Tanggal Beli',
            'Harga Beli',
            'Tanggal Diterima'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }
}
