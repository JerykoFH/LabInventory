<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage='global-rooms'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Aset di Ruangan: {{ $room['name'] ?? 'Tidak Ditemukan' }}"></x-navbars.navs.auth>
        <div class="container-fluid py-4">

            {{-- Informasi ruangan --}}
            @if($room)
            <div class="row mb-3">
                <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10 text-white">meeting_room</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Ruangan</p>
                                <h4 class="mb-0">{{ $room['name'] }}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <p class="mb-0 text-xs">Kode: <span class="font-weight-bolder">{{ $room['code'] ?? '-' }}</span> &bull; Lokasi: <span class="font-weight-bolder">{{ $room['location'] ?? '-' }}</span></p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10 text-white">inventory_2</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Total Aset</p>
                                <h4 class="mb-0">{{ count($assets) }}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <p class="mb-0 text-xs">Jumlah aset di ruangan ini</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Tombol kembali --}}
            <div class="row mb-3">
                <div class="col-12">
                    <a href="{{ route('global.rooms.index') }}" class="btn bg-gradient-secondary btn-sm">
                        <i class="material-icons text-sm me-1">arrow_back</i> Kembali ke Daftar Ruangan
                    </a>
                </div>
            </div>

            {{-- Tabel Aset --}}
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-3">
                                <h6 class="text-white text-capitalize ps-3 mb-0">Aset di {{ $room['name'] ?? 'Ruangan' }}</h6>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="input-group input-group-outline" style="max-width: 220px;">
                                        <input type="text" id="searchRoomAsset" class="form-control form-control-sm text-white" placeholder="Cari aset..." style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.3); color: white;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Aset</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kode Aset</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kategori</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kondisi</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($assets as $asset)
                                        <tr class="room-asset-row">
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="icon icon-sm icon-shape bg-gradient-primary shadow text-center border-radius-md me-2 d-flex align-items-center justify-content-center">
                                                        <i class="material-icons opacity-10 text-white" style="font-size: 16px;">devices</i>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm room-asset-name">{{ $asset['name'] }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($asset['assetCode'] ?? false)
                                                    <span class="badge bg-gradient-dark">{{ $asset['assetCode'] }}</span>
                                                @else
                                                    <span class="text-xs text-secondary">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-secondary text-xs">{{ $asset['category'] ?? '-' }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                @php
                                                    $kondisiColor = match($asset['condition'] ?? 'baik') {
                                                        'baik' => 'bg-gradient-success',
                                                        'rusak_ringan' => 'bg-gradient-warning',
                                                        'rusak_berat' => 'bg-gradient-danger',
                                                        'tidak_aktif' => 'bg-gradient-secondary',
                                                        default => 'bg-gradient-secondary'
                                                    };
                                                    $kondisiLabel = match($asset['condition'] ?? 'baik') {
                                                        'baik' => 'Baik',
                                                        'rusak_ringan' => 'Rusak Ringan',
                                                        'rusak_berat' => 'Rusak Berat',
                                                        'tidak_aktif' => 'Tidak Aktif',
                                                        default => $asset['condition']
                                                    };
                                                @endphp
                                                <span class="badge badge-sm {{ $kondisiColor }}">{{ $kondisiLabel }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                @php
                                                    $statusColor = match($asset['status'] ?? 'aktif') {
                                                        'aktif' => 'bg-gradient-success',
                                                        'dalam_pemeliharaan' => 'bg-gradient-warning',
                                                        'dihapus' => 'bg-gradient-danger',
                                                        'diganti' => 'bg-gradient-secondary',
                                                        default => 'bg-gradient-secondary'
                                                    };
                                                    $statusLabel = match($asset['status'] ?? 'aktif') {
                                                        'aktif' => 'Aktif',
                                                        'dalam_pemeliharaan' => 'Pemeliharaan',
                                                        'dihapus' => 'Dihapus',
                                                        'diganti' => 'Diganti',
                                                        default => $asset['status']
                                                    };
                                                @endphp
                                                <span class="badge badge-sm {{ $statusColor }}">{{ $statusLabel }}</span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5">
                                                <i class="material-icons text-secondary" style="font-size: 48px;">inventory_2</i>
                                                <p class="text-secondary text-sm mb-0 mt-2">Belum ada aset di ruangan ini.</p>
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

    @push('js')
    <script>
        document.getElementById('searchRoomAsset').addEventListener('input', function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.room-asset-row').forEach(function(row) {
                const name = row.querySelector('.room-asset-name')?.textContent.toLowerCase() ?? '';
                row.style.display = name.includes(query) ? '' : 'none';
            });
        });
    </script>
    @endpush
</x-layout>
