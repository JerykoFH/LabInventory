<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage='history'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Riwayat Aktivitas"></x-navbars.navs.auth>
        <div class="container-fluid py-4">

            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-info shadow-info border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                                <h6 class="text-white text-capitalize ps-3 mb-0">Log Riwayat Sistem (Audit Log)</h6>
                                <a href="{{ route('history.pdf', request()->query()) }}" class="btn btn-sm btn-light me-3 mb-0">
                                    <i class="material-icons text-sm">picture_as_pdf</i> Export PDF
                                </a>
                            </div>
                        </div>

                        <div class="card-body px-0 pb-2 mt-3">
                            <div class="px-4 pb-3">
                                <form method="GET" action="{{ route('history.index') }}" class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label text-xs font-weight-bolder">Tipe Aksi</label>
                                        <select name="action" class="form-select border px-2 p-1">
                                            <option value="all" {{ request('action') == 'all' ? 'selected' : '' }}>Semua Aksi</option>
                                            <option value="LOGIN" {{ request('action') == 'LOGIN' ? 'selected' : '' }}>LOGIN</option>
                                            <option value="CREATE" {{ request('action') == 'CREATE' ? 'selected' : '' }}>CREATE</option>
                                            <option value="UPDATE" {{ request('action') == 'UPDATE' ? 'selected' : '' }}>UPDATE</option>
                                            <option value="DELETE" {{ request('action') == 'DELETE' ? 'selected' : '' }}>DELETE</option>
                                            <option value="APPROVE" {{ request('action') == 'APPROVE' ? 'selected' : '' }}>APPROVE</option>
                                            <option value="REJECT" {{ request('action') == 'REJECT' ? 'selected' : '' }}>REJECT</option>
                                            <option value="ADJUST_STOCK" {{ request('action') == 'ADJUST_STOCK' ? 'selected' : '' }}>ADJUST_STOCK</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label text-xs font-weight-bolder">Dari Tanggal</label>
                                        <input type="date" name="startDate" class="form-control border px-2 p-1" value="{{ request('startDate') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label text-xs font-weight-bolder">Sampai Tanggal</label>
                                        <input type="date" name="endDate" class="form-control border px-2 p-1" value="{{ request('endDate') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-info mb-0 w-100">Filter</button>
                                    </div>
                                </form>
                            </div>

                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Waktu</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Pengguna</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tipe Aksi</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Aktivitas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($logs as $log)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-3 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ \Carbon\Carbon::parse($log['createdAt'])->format('d M Y') }}</h6>
                                                        <p class="text-xs text-secondary mb-0">{{ \Carbon\Carbon::parse($log['createdAt'])->format('H:i') }} WIB</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $log['user']['name'] ?? 'Sistem' }}</p>
                                                <p class="text-xs text-secondary mb-0">
                                                    @php
                                                        $r = $log['user']['role'] ?? '';
                                                        echo match($r) {
                                                            'admin' => 'Admin Utama',
                                                            'kepala_lab' => 'Kepala Lab',
                                                            'kaprodi' => 'Kaprodi',
                                                            'staf_admin' => 'Staf Admin',
                                                            'staf_lab' => 'Staf Lab',
                                                            default => $r
                                                        };
                                                    @endphp
                                                </p>
                                            </td>
                                            <td>
                                                @php
                                                    $actionClass = match($log['action']) {
                                                        'CREATE' => 'bg-gradient-success',
                                                        'UPDATE' => 'bg-gradient-info',
                                                        'DELETE' => 'bg-gradient-danger',
                                                        'APPROVE' => 'bg-gradient-success',
                                                        'REJECT' => 'bg-gradient-danger',
                                                        'ADJUST_STOCK' => 'bg-gradient-warning',
                                                        default => 'bg-gradient-secondary'
                                                    };
                                                @endphp
                                                <span class="badge badge-sm {{ $actionClass }}">{{ $log['action'] }}</span>
                                            </td>
                                            <td>
                                                <span class="text-sm">{{ $log['description'] }}</span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5">
                                                <i class="material-icons text-secondary" style="font-size: 48px;">history</i>
                                                <p class="text-secondary text-sm mb-0 mt-2">Belum ada riwayat aktivitas yang tercatat.</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <x-footers.auth></x-footers.auth>
        </div>
    </main>
</x-layout>
