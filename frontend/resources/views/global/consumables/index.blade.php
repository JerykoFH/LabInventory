<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage='global-consumables'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Barang Habis Pakai (BHP)"></x-navbars.navs.auth>
        <div class="container-fluid py-4">

            {{-- Statistik ringkas --}}
            <div class="row mb-3">
                @php
                    $total    = count($items);
                    $lowStock = collect($items)->filter(fn($i) => ($i['currentStock'] ?? 0) <= ($i['minimumStock'] ?? 5))->count();
                    $outStock = collect($items)->filter(fn($i) => ($i['currentStock'] ?? 0) == 0)->count();
                @endphp
                <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10 text-white">science</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Total Item BHP</p>
                                <h4 class="mb-0">{{ $total }}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <p class="mb-0 text-xs">Semua item tercatat</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10 text-white">warning</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Stok Menipis</p>
                                <h4 class="mb-0">{{ $lowStock }}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <p class="mb-0 text-xs"><span class="text-warning text-sm font-weight-bolder">{{ $lowStock }}</span> item di bawah minimum stok</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-sm-6">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-danger shadow-danger text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10 text-white">remove_shopping_cart</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Habis</p>
                                <h4 class="mb-0">{{ $outStock }}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <p class="mb-0 text-xs"><span class="text-danger text-sm font-weight-bolder">{{ $outStock }}</span> item stok = 0</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabel BHP --}}
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-3">
                                <h6 class="text-white text-capitalize ps-3 mb-0">Daftar Barang Habis Pakai</h6>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="input-group input-group-outline" style="max-width: 220px;">
                                        <input type="text" id="searchBHP" class="form-control form-control-sm text-white" placeholder="Cari item..." style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.3); color: white;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Item</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kategori</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Stok Saat Ini</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Min. Stok</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Ruangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($items as $item)
                                        @php
                                            $isLow = ($item['currentStock'] ?? 0) <= ($item['minimumStock'] ?? 5);
                                            $isOut = ($item['currentStock'] ?? 0) == 0;
                                        @endphp
                                        <tr class="bhp-row">
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="icon icon-sm icon-shape {{ $isOut ? 'bg-gradient-danger' : ($isLow ? 'bg-gradient-warning' : 'bg-gradient-info') }} shadow text-center border-radius-md me-2 d-flex align-items-center justify-content-center">
                                                        <i class="material-icons opacity-10 text-white" style="font-size: 16px;">science</i>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm bhp-name">{{ $item['name'] }}</h6>
                                                        @if($item['notes'] ?? false)
                                                            <p class="text-xs text-secondary mb-0">{{ Str::limit($item['notes'], 40) }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-secondary text-xs">{{ $item['category'] ?? '-' }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                @if($isOut)
                                                    <span class="badge bg-gradient-danger">0 {{ $item['unit'] }}</span>
                                                @elseif($isLow)
                                                    <span class="badge bg-gradient-warning">{{ $item['currentStock'] }} {{ $item['unit'] }}</span>
                                                @else
                                                    <span class="text-success text-sm font-weight-bold">{{ $item['currentStock'] }} {{ $item['unit'] }}</span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="text-secondary text-xs">{{ $item['minimumStock'] ?? 5 }} {{ $item['unit'] }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $roomMatch = collect($rooms ?? [])->first(function($r) use ($item) {
                                                        $loc = strtolower($item['location'] ?? '');
                                                        $rName = strtolower($r['name'] ?? '');
                                                        
                                                        $keywords = ['jaringan', 'pemrograman', 'multimedia', 'basis data'];
                                                        foreach ($keywords as $keyword) {
                                                            if (str_contains($loc, $keyword) && str_contains($rName, $keyword)) {
                                                                return true;
                                                            }
                                                        }
                                                        return false;
                                                    });
                                                @endphp
                                                @if($roomMatch)
                                                    <a href="{{ route('global.rooms.show', $roomMatch['_id']) }}" class="text-info text-xs font-weight-bold d-flex align-items-center gap-1" title="Lihat detail ruangan">
                                                        <i class="material-icons text-sm">meeting_room</i> {{ $roomMatch['name'] }}
                                                    </a>
                                                @else
                                                    <span class="text-secondary text-xs">{{ $item['location'] ?? '-' }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5">
                                                <i class="material-icons text-secondary" style="font-size: 48px;">science</i>
                                                <p class="text-secondary text-sm mb-0 mt-2">Belum ada item BHP.</p>
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
        document.getElementById('searchBHP').addEventListener('input', function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.bhp-row').forEach(function(row) {
                const name = row.querySelector('.bhp-name')?.textContent.toLowerCase() ?? '';
                row.style.display = name.includes(query) ? '' : 'none';
            });
        });
    </script>
    @endpush
</x-layout>
