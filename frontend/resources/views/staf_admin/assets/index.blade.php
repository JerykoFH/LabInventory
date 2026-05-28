<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage='assets'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Aset Inventaris"></x-navbars.navs.auth>
        <div class="container-fluid py-4">

            
            @if(session('success'))
            <div class="alert alert-success alert-dismissible text-white fade show" role="alert">
                <span class="text-sm">{{ session('success') }}</span>
                <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert"></button>
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible text-white fade show" role="alert">
                <span class="text-sm">{{ session('error') }}</span>
                <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert"></button>
            </div>
            @endif

            
            <div class="row mb-3">
                @php
                    $totalAssets   = count($assets);
                    $activeAssets  = collect($assets)->where('status', 'aktif')->count();
                    $labelledAssets = collect($assets)->filter(fn($a) => !empty($a['assetCode']))->count();
                    $receivedAssets = collect($assets)->filter(fn($a) => !empty($a['receivedDate']))->count();
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
                            <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10 text-white">qr_code_2</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Sudah Berlabel</p>
                                <h4 class="mb-0">{{ $labelledAssets }}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <p class="mb-0 text-xs"><span class="text-info text-sm font-weight-bolder">{{ $totalAssets > 0 ? round($labelledAssets / $totalAssets * 100) : 0 }}%</span> sudah punya kode aset</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10 text-white">local_shipping</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Sudah Diterima</p>
                                <h4 class="mb-0">{{ $receivedAssets }}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <p class="mb-0 text-xs"><span class="text-warning text-sm font-weight-bolder">{{ $totalAssets > 0 ? round($receivedAssets / $totalAssets * 100) : 0 }}%</span> sudah ada tanggal terima</p>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-3">
                                <h6 class="text-white text-capitalize ps-3 mb-0">Daftar Aset Inventaris Laboratorium</h6>
                                <div class="d-flex align-items-center gap-2">
                                    {{-- Search Filter (client-side) --}}
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
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tgl. Diterima</th>
                                            <th class="text-secondary opacity-7">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($assets as $asset)
                                        <tr class="asset-row">
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="icon icon-sm icon-shape {{ empty($asset['assetCode']) ? 'bg-gradient-secondary' : 'bg-gradient-primary' }} shadow text-center border-radius-md me-2 d-flex align-items-center justify-content-center">
                                                        <i class="material-icons opacity-10 text-white" style="font-size: 16px;">{{ $asset['itemType'] ?? 'devices' === 'consumable' ? 'science' : 'devices' }}</i>
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
                                                    <span class="text-xs text-danger">
                                                        <i class="material-icons text-xs">warning</i> Belum berlabel
                                                    </span>
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
                                            <td class="align-middle text-center">
                                                @if($asset['receivedDate'] ?? false)
                                                    <span class="text-success text-xs font-weight-bold">
                                                        {{ \Carbon\Carbon::parse($asset['receivedDate'])->format('d M Y') }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-gradient-warning text-xs">Belum</span>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                {{-- Edit Label --}}
                                                <button type="button"
                                                    class="btn btn-link text-info text-xs p-0 mb-0 me-2"
                                                    title="Update Label/QR"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#labelModal{{ $asset['_id'] }}">
                                                    <i class="material-icons text-sm">qr_code</i>
                                                </button>

                                                {{-- Edit Tanggal Terima --}}
                                                <button type="button"
                                                    class="btn btn-link text-warning text-xs p-0 mb-0"
                                                    title="Input Tanggal Penerimaan"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#receiveModal{{ $asset['_id'] }}">
                                                    <i class="material-icons text-sm">edit_calendar</i>
                                                </button>

                                                {{-- Modal Label --}}
                                                <div class="modal fade" id="labelModal{{ $asset['_id'] }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content text-start">
                                                            <form action="{{ route('staf-admin.assets.label', $asset['_id']) }}" method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title font-weight-normal">Update Label / Kode Aset</h5>
                                                                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p class="text-sm text-secondary mb-3">Aset: <strong>{{ $asset['name'] }}</strong></p>
                                                                    <div class="input-group input-group-outline my-3 {{ $asset['assetCode'] ?? false ? 'is-filled' : '' }}">
                                                                        <label class="form-label">Kode Aset <span class="text-danger">*</span></label>
                                                                        <input type="text" name="assetCode" class="form-control" maxlength="50"
                                                                            value="{{ $asset['assetCode'] ?? '' }}" required>
                                                                    </div>
                                                                    <div class="input-group input-group-outline my-3 {{ $asset['qrCode'] ?? false ? 'is-filled' : '' }}">
                                                                        <label class="form-label">Kode QR/Barcode (opsional)</label>
                                                                        <input type="text" name="qrCode" class="form-control"
                                                                            value="{{ $asset['qrCode'] ?? '' }}">
                                                                    </div>
                                                                    <div class="input-group input-group-outline my-3 {{ $asset['labelPhoto'] ?? false ? 'is-filled' : '' }}">
                                                                        <label class="form-label">URL Foto Label (opsional)</label>
                                                                        <input type="text" name="labelPhoto" class="form-control"
                                                                            value="{{ $asset['labelPhoto'] ?? '' }}">
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn bg-gradient-primary">Simpan Label</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Modal Tanggal Penerimaan --}}
                                                <div class="modal fade" id="receiveModal{{ $asset['_id'] }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content text-start">
                                                            <form action="{{ route('staf-admin.assets.receive', $asset['_id']) }}" method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title font-weight-normal">Input Tanggal Penerimaan</h5>
                                                                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p class="text-sm text-secondary mb-3">Aset: <strong>{{ $asset['name'] }}</strong></p>
                                                                    <div class="input-group input-group-outline is-filled">
                                                                        <label class="form-label">Tanggal Diterima <span class="text-danger">*</span></label>
                                                                        <input type="date" name="receivedDate" class="form-control" required
                                                                            value="{{ $asset['receivedDate'] ? \Carbon\Carbon::parse($asset['receivedDate'])->format('Y-m-d') : '' }}">
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn bg-gradient-primary">Simpan</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5">
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
