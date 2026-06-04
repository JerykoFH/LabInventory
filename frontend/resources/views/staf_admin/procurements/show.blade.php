<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage='procurements'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Detail Penerimaan Barang"></x-navbars.navs.auth>
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

            @if($draft)

            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-3">
                                <div>
                                    <h6 class="text-white text-capitalize ps-3 mb-0">{{ $draft['title'] }}</h6>
                                    <p class="text-white text-xs ps-3 mb-0 opacity-8">Tahun {{ $draft['year'] }} · Dibuat oleh {{ $draft['createdBy']['name'] ?? '-' }}</p>
                                </div>
                                <span class="badge bg-gradient-success px-3 py-2">
                                    <i class="material-icons text-xs me-1" style="vertical-align: middle;">lock</i> Final / Dikunci
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="text-uppercase text-secondary text-xxs font-weight-bolder">Status</label>
                                    <p class="mb-0">
                                        <span class="badge bg-gradient-success">Dikunci (Final)</span>
                                    </p>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="text-uppercase text-secondary text-xxs font-weight-bolder">Direview Oleh</label>
                                    <p class="text-sm font-weight-bold mb-0">{{ $draft['reviewedBy']['name'] ?? '-' }}</p>
                                </div>
                                @if($draft['submittedAt'] ?? false)
                                <div class="col-md-3 mb-3">
                                    <label class="text-uppercase text-secondary text-xxs font-weight-bolder">Tanggal Disubmit</label>
                                    <p class="text-sm mb-0">{{ \Carbon\Carbon::parse($draft['submittedAt'])->format('d M Y, H:i') }}</p>
                                </div>
                                @endif
                                @if($draft['lockedAt'] ?? false)
                                <div class="col-md-3 mb-3">
                                    <label class="text-uppercase text-secondary text-xxs font-weight-bolder">Tanggal Dikunci</label>
                                    <p class="text-sm font-weight-bold text-success mb-0">{{ \Carbon\Carbon::parse($draft['lockedAt'])->format('d M Y, H:i') }}</p>
                                </div>
                                @endif
                                @if($draft['notes'] ?? false)
                                <div class="col-12 mt-2">
                                    <label class="text-uppercase text-secondary text-xxs font-weight-bolder">Catatan Kepala Lab</label>
                                    <p class="text-sm mb-0 bg-gray-100 p-2 border-radius-md">{{ $draft['notes'] }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                            <h6>Daftar Barang yang Disetujui ({{ count($draft['items'] ?? []) }})</h6>
                            @php
                                $totalPrice = collect($draft['items'] ?? [])->sum(function($item) {
                                    return ($item['estimatedPrice'] ?? 0) * ($item['quantity'] ?? 1);
                                });
                            @endphp
                            <span class="badge bg-gradient-success p-2">Total Disetujui: Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Barang</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tipe</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Jumlah</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Harga Satuan</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Subtotal</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tgl. Diterima</th>
                                            <th class="text-center text-secondary opacity-7">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($draft['items'] ?? [] as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $item['name'] }}</h6>
                                                        @if($item['purchaseLink'] ?? false)
                                                            <a href="{{ $item['purchaseLink'] }}" target="_blank" class="text-xs text-info">
                                                                <i class="material-icons text-xs">link</i> Link Pembelian
                                                            </a>
                                                        @endif
                                                        @if($item['notes'] ?? false)
                                                            <p class="text-xs text-secondary mb-0">{{ $item['notes'] }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge {{ $item['itemType'] === 'asset' ? 'bg-gradient-primary' : 'bg-gradient-info' }}">
                                                    {{ $item['itemType'] === 'asset' ? 'Aset' : 'BHP' }}
                                                </span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="text-secondary text-sm font-weight-bold">{{ $item['quantity'] }} {{ $item['unit'] ?? '' }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="text-secondary text-xs">Rp {{ number_format($item['estimatedPrice'] ?? 0, 0, ',', '.') }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="text-dark text-xs font-weight-bold">Rp {{ number_format(($item['estimatedPrice'] ?? 0) * ($item['quantity'] ?? 1), 0, ',', '.') }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                @if($item['itemType'] === 'asset' && ($item['asset']['receivedDate'] ?? false))
                                                    <span class="badge bg-gradient-success text-xs">
                                                        {{ \Carbon\Carbon::parse($item['asset']['receivedDate'])->format('d M Y') }}
                                                    </span>
                                                @elseif($item['itemType'] === 'asset')
                                                    <span class="badge bg-gradient-warning text-xs">Belum diterima</span>
                                                @else
                                                    <span class="text-xs text-secondary">-</span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="text-xs text-secondary">-</span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <i class="material-icons text-secondary" style="font-size: 48px;">inventory_2</i>
                                                <p class="text-secondary text-sm mb-0 mt-2">Tidak ada item yang disetujui.</p>
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

            <div class="d-flex justify-content-start mb-4">
                <a href="{{ route('staf-admin.procurements.index') }}" class="btn btn-outline-secondary">
                    <i class="material-icons text-sm me-1">arrow_back</i> Kembali
                </a>
            </div>

            @else
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-body text-center py-5">
                            <i class="material-icons text-secondary" style="font-size: 64px;">search_off</i>
                            <p class="text-secondary mt-3">Draf tidak ditemukan.</p>
                            <a href="{{ route('staf-admin.procurements.index') }}" class="btn bg-gradient-primary">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <x-footers.auth></x-footers.auth>
        </div>
    </main>
</x-layout>
