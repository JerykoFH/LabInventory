<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Aset Laboratorium</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 18px; }
        .header p { margin: 5px 0 0 0; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
        .mb-0 { margin-bottom: 0; }
    </style>
</head>
<body>

    <div class="header">
        <h2>SISTEM INVENTARIS LABORATORIUM</h2>
        <p>Laporan Daftar Aset Keseluruhan</p>
        <p>Tanggal Cetak: {{ date('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="5%">No</th>
                <th width="15%">Kode Aset</th>
                <th width="20%">Nama Aset</th>
                <th width="15%">Kategori</th>
                <th width="15%">Lokasi</th>
                <th width="10%">Kondisi</th>
                <th width="10%">Status</th>
                <th width="10%">Tgl Diterima</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assets as $index => $asset)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $asset['assetCode'] ?? '-' }}</td>
                <td>{{ $asset['name'] ?? '-' }}</td>
                <td>{{ $asset['category'] ?? '-' }}</td>
                <td>{{ $asset['room']['name'] ?? '-' }}</td>
                <td>{{ ucwords(str_replace('_', ' ', $asset['condition'] ?? '-')) }}</td>
                <td>{{ ucwords(str_replace('_', ' ', $asset['status'] ?? '-')) }}</td>
                <td>{{ isset($asset['receivedDate']) ? date('d-m-Y', strtotime($asset['receivedDate'])) : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Tidak ada data aset.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
