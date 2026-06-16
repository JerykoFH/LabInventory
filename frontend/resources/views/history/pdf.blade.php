<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Riwayat Aktivitas</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0; padding: 0; }
        .header p { margin: 5px 0 0 0; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table, th, td { border: 1px solid #aaa; }
        th { background-color: #f2f2f2; padding: 8px; text-align: left; }
        td { padding: 6px 8px; vertical-align: top; }
        .badge { display: inline-block; padding: 3px 6px; border-radius: 4px; color: white; font-size: 10px; font-weight: bold; }
        .bg-create { background-color: #4CAF50; }
        .bg-update { background-color: #2196F3; }
        .bg-delete { background-color: #f44336; }
        .bg-approve { background-color: #4CAF50; }
        .bg-reject { background-color: #f44336; }
        .bg-adjust { background-color: #ff9800; }
        .bg-login { background-color: #9c27b0; }
        .bg-default { background-color: #9e9e9e; }
        .meta-info { margin-bottom: 15px; }
    </style>
</head>
<body>

    <div class="header">
        <h2>Laporan Riwayat Aktivitas Sistem</h2>
        <p>LabInventory - {{ date('d F Y') }}</p>
    </div>

    <div class="meta-info">
        <strong>Dicetak Oleh:</strong> {{ session('api_user')['name'] ?? 'Pengguna' }} ({{ $role }})<br>
        <strong>Waktu Cetak:</strong> {{ date('H:i:s WIB') }}<br>
        @if(request('startDate') || request('endDate'))
            <strong>Periode:</strong> {{ request('startDate') ?? 'Awal' }} s/d {{ request('endDate') ?? 'Sekarang' }}<br>
        @endif
        @if(request('action') && request('action') !== 'all')
            <strong>Filter Aksi:</strong> {{ request('action') }}<br>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th width="15%">Waktu</th>
                <th width="20%">Pengguna</th>
                <th width="15%">Aksi</th>
                <th width="50%">Deskripsi Aktivitas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                @php
                    $actionClass = match($log['action'] ?? '') {
                        'CREATE' => 'bg-create',
                        'UPDATE' => 'bg-update',
                        'DELETE' => 'bg-delete',
                        'APPROVE' => 'bg-approve',
                        'REJECT' => 'bg-reject',
                        'ADJUST_STOCK' => 'bg-adjust',
                        'LOGIN' => 'bg-login',
                        default => 'bg-default'
                    };
                    
                    $roleLabel = match($log['user']['role'] ?? '') {
                        'admin' => 'Admin Utama',
                        'kepala_lab' => 'Kepala Lab',
                        'kaprodi' => 'Kaprodi',
                        'staf_admin' => 'Staf Admin',
                        'staf_lab' => 'Staf Lab',
                        default => ($log['user']['role'] ?? '')
                    };
                @endphp
                <tr>
                    <td>
                        {{ \Carbon\Carbon::parse($log['createdAt'])->format('d M Y') }}<br>
                        <small style="color:#777;">{{ \Carbon\Carbon::parse($log['createdAt'])->format('H:i:s') }} WIB</small>
                    </td>
                    <td>
                        <strong>{{ $log['user']['name'] ?? 'Sistem' }}</strong><br>
                        <small style="color:#555;">{{ $roleLabel }}</small>
                    </td>
                    <td style="text-align: center;">
                        <span class="badge {{ $actionClass }}">{{ $log['action'] ?? '-' }}</span>
                    </td>
                    <td>{{ $log['description'] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px;">Belum ada riwayat aktivitas yang tercatat.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
