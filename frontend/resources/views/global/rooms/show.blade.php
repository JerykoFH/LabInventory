<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage='global-rooms'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Aset & BHP di Ruangan: {{ $room['name'] ?? 'Tidak Ditemukan' }}"></x-navbars.navs.auth>
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
                <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10 text-white">science</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Total Item BHP</p>
                                <h4 class="mb-0">{{ count($consumables ?? []) }}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <p class="mb-0 text-xs">Jumlah item BHP di ruangan ini</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="row mb-3">
                <div class="col-12">
                    <a href="{{ route('global.rooms.index') }}" class="btn bg-gradient-secondary btn-sm">
                        <i class="material-icons text-sm me-1">arrow_back</i> Kembali ke Daftar Ruangan
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-3 flex-wrap">
                                <h6 class="text-white text-capitalize ps-3 mb-0">Daftar Item di {{ $room['name'] ?? 'Ruangan' }}</h6>
                                <div class="d-flex align-items-center gap-2 mt-2 mt-md-0 pe-3">
                                    <select id="filterItemType" class="form-select form-select-sm text-white px-2" style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.3); color: white; cursor: pointer; width: auto; appearance: auto;">
                                        <option value="all" style="color: black;">Semua Tipe</option>
                                        <option value="aset" style="color: black;">Aset</option>
                                        <option value="bhp" style="color: black;">BHP</option>
                                    </select>
                                    <div class="input-group input-group-outline" style="max-width: 220px;">
                                        <input type="text" id="searchRoomItem" class="form-control form-control-sm text-white" placeholder="Cari item..." style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.3); color: white;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0" id="roomItemsTable">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Item</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tipe</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kategori</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Info Utama</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kondisi / Info Tambahan</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($assets as $asset)
                                        <tr class="room-item-row" data-type="aset">
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="icon icon-sm icon-shape bg-gradient-primary shadow text-center border-radius-md me-2 d-flex align-items-center justify-content-center">
                                                        <i class="material-icons opacity-10 text-white" style="font-size: 16px;">devices</i>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm room-item-name">{{ $asset['name'] }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-gradient-dark">Aset</span>
                                            </td>
                                            <td>
                                                <span class="text-secondary text-xs">{{ $asset['category'] ?? '-' }}</span>
                                            </td>
                                            <td>
                                                @if($asset['assetCode'] ?? false)
                                                    <span class="text-xs font-weight-bold text-secondary">Kode: {{ $asset['assetCode'] }}</span>
                                                @else
                                                    <span class="text-xs text-secondary">—</span>
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
                                        @endforelse

                                        @forelse($consumables ?? [] as $item)
                                        @php
                                            $isLow = ($item['currentStock'] ?? 0) <= ($item['minimumStock'] ?? 5);
                                            $isOut = ($item['currentStock'] ?? 0) == 0;
                                        @endphp
                                        <tr class="room-item-row" data-type="bhp">
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="icon icon-sm icon-shape {{ $isOut ? 'bg-gradient-danger' : ($isLow ? 'bg-gradient-warning' : 'bg-gradient-info') }} shadow text-center border-radius-md me-2 d-flex align-items-center justify-content-center">
                                                        <i class="material-icons opacity-10 text-white" style="font-size: 16px;">science</i>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm room-item-name">{{ $item['name'] }}</h6>
                                                        @if($item['notes'] ?? false)
                                                            <p class="text-xs text-secondary mb-0">{{ Str::limit($item['notes'], 40) }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-gradient-success">BHP</span>
                                            </td>
                                            <td>
                                                <span class="text-secondary text-xs">{{ $item['category'] ?? '-' }}</span>
                                            </td>
                                            <td>
                                                @if($isOut)
                                                    <span class="text-xs font-weight-bold text-danger">Stok: 0 {{ $item['unit'] }}</span>
                                                @elseif($isLow)
                                                    <span class="text-xs font-weight-bold text-warning">Stok: {{ $item['currentStock'] }} {{ $item['unit'] }}</span>
                                                @else
                                                    <span class="text-xs font-weight-bold text-success">Stok: {{ $item['currentStock'] }} {{ $item['unit'] }}</span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="text-secondary text-xs">Min. Stok: {{ $item['minimumStock'] ?? 5 }} {{ $item['unit'] }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="text-secondary text-xs">—</span>
                                            </td>
                                        </tr>
                                        @empty
                                        @endforelse

                                        @if(count($assets) === 0 && count($consumables ?? []) === 0)
                                        <tr id="emptyStateRow">
                                            <td colspan="6" class="text-center py-5">
                                                <i class="material-icons text-secondary" style="font-size: 48px;">category</i>
                                                <p class="text-secondary text-sm mb-0 mt-2">Belum ada item di ruangan ini.</p>
                                            </td>
                                        </tr>
                                        @endif
                                        
                                        <tr id="noResultRow" style="display: none;">
                                            <td colspan="6" class="text-center py-5">
                                                <i class="material-icons text-secondary" style="font-size: 48px;">search_off</i>
                                                <p class="text-secondary text-sm mb-0 mt-2">Item tidak ditemukan berdasarkan filter/pencarian.</p>
                                            </td>
                                        </tr>
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
        const searchInput = document.getElementById('searchRoomItem');
        const filterSelect = document.getElementById('filterItemType');
        const rows = document.querySelectorAll('.room-item-row');
        const noResultRow = document.getElementById('noResultRow');

        function filterItems() {
            const query = searchInput ? searchInput.value.toLowerCase() : '';
            const typeFilter = filterSelect ? filterSelect.value : 'all';
            let visibleCount = 0;

            rows.forEach(function(row) {
                const name = row.querySelector('.room-item-name')?.textContent.toLowerCase() ?? '';
                const type = row.getAttribute('data-type');
                
                const matchesSearch = name.includes(query);
                const matchesType = (typeFilter === 'all') || (type === typeFilter);

                if (matchesSearch && matchesType) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (visibleCount === 0 && rows.length > 0 && noResultRow) {
                noResultRow.style.display = '';
            } else if (noResultRow) {
                noResultRow.style.display = 'none';
            }
        }

        if (searchInput) searchInput.addEventListener('input', filterItems);
        if (filterSelect) filterSelect.addEventListener('change', filterItems);
    </script>
    @endpush
</x-layout>
