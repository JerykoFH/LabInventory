<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage='global-assets'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Inventaris Global"></x-navbars.navs.auth>
        <div class="container-fluid py-4">

            {{-- Statistik ringkas --}}
            <div class="row mb-3">
                @php
                    $totalAssets   = count($assets);
                    $activeAssets  = collect($assets)->where('status', 'aktif')->count();
                    $rusakRingan   = collect($assets)->where('condition', 'rusak_ringan')->count();
                    $rusakBerat    = collect($assets)->where('condition', 'rusak_berat')->count();
                @endphp
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10 text-white">inventory_2</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Total Aset</p>
                                <h4 class="mb-0">{{ $totalAssets }}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <p class="mb-0 text-xs"><span class="text-success text-sm font-weight-bolder">Semua</span> aset tercatat</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10 text-white">check_circle</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Aset Aktif</p>
                                <h4 class="mb-0">{{ $activeAssets }}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <p class="mb-0 text-xs"><span class="text-success text-sm font-weight-bolder">{{ $totalAssets > 0 ? round($activeAssets / $totalAssets * 100) : 0 }}%</span> dari total aset</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10 text-white">warning</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Rusak Ringan</p>
                                <h4 class="mb-0">{{ $rusakRingan }}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <p class="mb-0 text-xs"><span class="text-warning text-sm font-weight-bolder">{{ $rusakRingan }}</span> aset perlu perhatian</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-danger shadow-danger text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10 text-white">error</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Rusak Berat</p>
                                <h4 class="mb-0">{{ $rusakBerat }}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <p class="mb-0 text-xs"><span class="text-danger text-sm font-weight-bolder">{{ $rusakBerat }}</span> aset rusak berat</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabel Inventaris --}}
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-3">
                                <h6 class="text-white text-capitalize ps-3 mb-0">Daftar Aset Inventaris Laboratorium</h6>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="input-group input-group-outline" style="max-width: 220px;">
                                        <input type="text" id="searchAsset" class="form-control form-control-sm text-white" placeholder="Cari aset..." style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.3); color: white;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0" id="assetTable">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Aset</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kode / QR</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kategori</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ruangan</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kondisi</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($assets as $asset)
                                        <tr class="asset-row">
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="icon icon-sm icon-shape {{ empty($asset['assetCode']) ? 'bg-gradient-secondary' : 'bg-gradient-primary' }} shadow text-center border-radius-md me-2 d-flex align-items-center justify-content-center">
                                                        <i class="material-icons opacity-10 text-white" style="font-size: 16px;">devices</i>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm asset-name">{{ $asset['name'] }}</h6>
                                                        @if($asset['notes'] ?? false)
                                                            <p class="text-xs text-secondary mb-0">{{ Str::limit($asset['notes'], 40) }}</p>
                                                        @endif
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
                                                <span class="text-secondary text-xs">{{ $asset['room']['name'] ?? '-' }}</span>
                                                @if($asset['room']['code'] ?? false)
                                                    <p class="text-xs text-secondary mb-0">{{ $asset['room']['code'] }}</p>
                                                @endif
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
                                            <td colspan="6" class="text-center py-5">
                                                <i class="material-icons text-secondary" style="font-size: 48px;">inventory_2</i>
                                                <p class="text-secondary text-sm mb-0 mt-2">Belum ada aset inventaris.</p>
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
        document.getElementById('searchAsset').addEventListener('input', function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.asset-row').forEach(function(row) {
                const name = row.querySelector('.asset-name')?.textContent.toLowerCase() ?? '';
                row.style.display = name.includes(query) ? '' : 'none';
            });
        });
    </script>
    @endpush
</x-layout>
