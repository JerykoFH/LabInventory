<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage='consumables'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Barang Habis Pakai"></x-navbars.navs.auth>
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

            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-3">
                                <h6 class="text-white text-capitalize ps-3 mb-0">Daftar Barang Habis Pakai</h6>
                                <a href="{{ route('staf-lab.consumables.create') }}" class="btn btn-sm btn-white mb-0">
                                    <i class="material-icons text-sm me-1">add</i> Tambah Item
                                </a>
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
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Lokasi</th>
                                            <th class="text-secondary opacity-7">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($items as $item)
                                        @php
                                            $isLow = ($item['currentStock'] ?? 0) <= ($item['minimumStock'] ?? 5);
                                            $isOut = ($item['currentStock'] ?? 0) == 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="icon icon-sm icon-shape {{ $isOut ? 'bg-gradient-danger' : ($isLow ? 'bg-gradient-warning' : 'bg-gradient-info') }} shadow text-center border-radius-md me-2 d-flex align-items-center justify-content-center">
                                                        <i class="material-icons opacity-10 text-white" style="font-size: 16px;">science</i>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $item['name'] }}</h6>
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
                                                <span class="text-secondary text-xs">{{ $item['location'] ?? '-' }}</span>
                                            </td>
                                            <td class="align-middle">
                                                <button type="button"
                                                    class="btn btn-link text-info text-xs p-0 mb-0"
                                                    title="Sesuaikan Stok"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#stockModal{{ $item['_id'] }}">
                                                    <i class="material-icons text-sm">tune</i>
                                                </button>

                                                <div class="modal fade" id="stockModal{{ $item['_id'] }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content text-start">
                                                            <form action="{{ route('staf-lab.consumables.stock', $item['_id']) }}" method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title font-weight-normal">Sesuaikan Stok</h5>
                                                                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p class="text-sm text-secondary mb-1">Item: <strong>{{ $item['name'] }}</strong></p>
                                                                    <p class="text-sm mb-3">Stok sekarang: <strong>{{ $item['currentStock'] }} {{ $item['unit'] }}</strong></p>
                                                                    <div class="input-group input-group-outline my-3 is-filled">
                                                                        <label class="form-label">Penyesuaian (positif = tambah, negatif = kurangi)</label>
                                                                        <input type="number" name="adjustment" class="form-control" required placeholder="cth: 10 atau -5">
                                                                    </div>
                                                                    <div class="input-group input-group-dynamic my-3">
                                                                        <textarea name="reason" class="form-control" rows="2" placeholder="Alasan penyesuaian (opsional)"></textarea>
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
                                            <td colspan="6" class="text-center py-5">
                                                <i class="material-icons text-secondary" style="font-size: 48px;">science</i>
                                                <p class="text-secondary text-sm mb-0 mt-2">Belum ada item BHP. Tambah sekarang!</p>
                                                <a href="{{ route('staf-lab.consumables.create') }}" class="btn bg-gradient-info btn-sm mt-3">
                                                    <i class="material-icons text-sm me-1">add</i> Tambah Item BHP
                                                </a>
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
