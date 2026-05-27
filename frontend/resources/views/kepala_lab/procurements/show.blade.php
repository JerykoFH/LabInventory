<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage='procurements'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Detail Draf Pengadaan"></x-navbars.navs.auth>
        <div class="container-fluid py-4">

            {{-- Flash messages --}}
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
            @php
                $isLocked = $draft['status'] === 'locked';
                $isDraft = $draft['status'] === 'draft';
                $isSubmitted = $draft['status'] === 'submitted';
            @endphp

            {{-- Draft Info Card --}}
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-3">
                                <div>
                                    <h6 class="text-white text-capitalize ps-3 mb-0">{{ $draft['title'] }}</h6>
                                    <p class="text-white text-xs ps-3 mb-0 opacity-8">Tahun {{ $draft['year'] }}</p>
                                </div>
                                <div>
                                    @if($isDraft)
                                    <a href="{{ route('kepala-lab.procurements.edit', $draft['_id']) }}" class="btn btn-sm btn-white mb-0 me-1">
                                        <i class="material-icons text-sm me-1">edit</i> Edit
                                    </a>
                                    <form action="{{ route('kepala-lab.procurements.submit', $draft['_id']) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin submit draf ini ke Kaprodi? Setelah disubmit, draf tidak bisa diubah lagi.')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-white mb-0">
                                            <i class="material-icons text-sm me-1">send</i> Submit ke Kaprodi
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="text-uppercase text-secondary text-xxs font-weight-bolder">Status</label>
                                    <p class="mb-0">
                                        @php
                                            $statusColor = match($draft['status']) {
                                                'draft' => 'bg-gradient-secondary',
                                                'submitted' => 'bg-gradient-warning',
                                                'locked' => 'bg-gradient-success',
                                                default => 'bg-gradient-secondary'
                                            };
                                            $statusLabel = match($draft['status']) {
                                                'draft' => 'Draft',
                                                'submitted' => 'Disubmit',
                                                'locked' => 'Dikunci (Final)',
                                                default => $draft['status']
                                            };
                                        @endphp
                                        <span class="badge {{ $statusColor }}">{{ $statusLabel }}</span>
                                    </p>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="text-uppercase text-secondary text-xxs font-weight-bolder">Dibuat</label>
                                    <p class="text-sm mb-0">{{ isset($draft['createdAt']) ? \Carbon\Carbon::parse($draft['createdAt'])->format('d M Y, H:i') : '-' }}</p>
                                </div>
                                @if($draft['submittedAt'] ?? false)
                                <div class="col-md-3 mb-3">
                                    <label class="text-uppercase text-secondary text-xxs font-weight-bolder">Disubmit</label>
                                    <p class="text-sm mb-0">{{ \Carbon\Carbon::parse($draft['submittedAt'])->format('d M Y, H:i') }}</p>
                                </div>
                                @endif
                                @if($draft['lockedAt'] ?? false)
                                <div class="col-md-3 mb-3">
                                    <label class="text-uppercase text-secondary text-xxs font-weight-bolder">Dikunci</label>
                                    <p class="text-sm mb-0">{{ \Carbon\Carbon::parse($draft['lockedAt'])->format('d M Y, H:i') }}</p>
                                </div>
                                @endif
                                @if($draft['notes'] ?? false)
                                <div class="col-12 mb-3">
                                    <label class="text-uppercase text-secondary text-xxs font-weight-bolder">Catatan</label>
                                    <p class="text-sm mb-0">{{ $draft['notes'] }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Add Item Form (only if draft status) --}}
            @if($isDraft)
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0">
                            <h6>Tambah Item Pengadaan</h6>
                        </div>
                        <div class="card-body">
                            @if($errors->any())
                            <div class="alert alert-danger text-white text-sm">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            <form action="{{ route('kepala-lab.procurements.items.add', $draft['_id']) }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group input-group-static my-3">
                                            <label class="ms-0">Tipe Item</label>
                                            <select name="itemType" class="form-control" required>
                                                <option value="asset" {{ old('itemType') == 'asset' ? 'selected' : '' }}>Aset (Inventaris)</option>
                                                <option value="consumable" {{ old('itemType') == 'consumable' ? 'selected' : '' }}>BHP (Barang Habis Pakai)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group input-group-outline my-3 {{ old('name') ? 'is-filled' : '' }}">
                                            <label class="form-label">Nama Barang</label>
                                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group input-group-outline my-3 {{ old('quantity') ? 'is-filled' : '' }}">
                                            <label class="form-label">Jumlah</label>
                                            <input type="number" name="quantity" class="form-control" value="{{ old('quantity') }}" min="1" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group input-group-outline my-3 {{ old('unit') ? 'is-filled' : '' }}">
                                            <label class="form-label">Satuan (unit, pcs, box)</label>
                                            <input type="text" name="unit" class="form-control" value="{{ old('unit') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group input-group-outline my-3 {{ old('estimatedPrice') ? 'is-filled' : '' }}">
                                            <label class="form-label">Estimasi Harga (Rp)</label>
                                            <input type="number" name="estimatedPrice" class="form-control" value="{{ old('estimatedPrice') }}" min="0" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group input-group-outline my-3 {{ old('purchaseLink') ? 'is-filled' : '' }}">
                                            <label class="form-label">Link Pembelian (URL)</label>
                                            <input type="url" name="purchaseLink" class="form-control" value="{{ old('purchaseLink') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group input-group-static my-3">
                                            <label class="ms-0">Menggantikan Aset (opsional)</label>
                                            <select name="replacedAsset" class="form-control">
                                                <option value="">-- Tidak ada --</option>
                                                @foreach($assets as $asset)
                                                    <option value="{{ $asset['_id'] }}" {{ old('replacedAsset') == $asset['_id'] ? 'selected' : '' }}>
                                                        {{ $asset['name'] }} {{ $asset['assetCode'] ? '(' . $asset['assetCode'] . ')' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="input-group input-group-dynamic my-3">
                                            <textarea name="notes" class="form-control" rows="2" placeholder="Catatan (opsional)">{{ old('notes') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn bg-gradient-info">
                                        <i class="material-icons text-sm me-1">add</i> Tambah Item
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Items List --}}
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                            <h6>Daftar Item ({{ count($draft['items'] ?? []) }})</h6>
                            @php
                                $totalPrice = collect($draft['items'] ?? [])->sum(function($item) {
                                    return ($item['estimatedPrice'] ?? 0) * ($item['quantity'] ?? 1);
                                });
                            @endphp
                            <span class="badge bg-gradient-dark p-2">Total: Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Barang</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tipe</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Jumlah</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Harga</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Subtotal</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                            @if($isDraft)
                                            <th class="text-secondary opacity-7"></th>
                                            @endif
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
                                                                <i class="material-icons text-xs">link</i> Link pembelian
                                                            </a>
                                                        @endif
                                                        @if($item['replacedAsset'] ?? false)
                                                            <p class="text-xs text-warning mb-0">
                                                                <i class="material-icons text-xs">swap_horiz</i>
                                                                Mengganti: {{ $item['replacedAsset']['name'] ?? $item['replacedAsset'] }}
                                                            </p>
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
                                                @php
                                                    $approvalColor = match($item['approvalStatus'] ?? 'pending') {
                                                        'pending' => 'bg-gradient-secondary',
                                                        'approved' => 'bg-gradient-success',
                                                        'rejected' => 'bg-gradient-danger',
                                                        default => 'bg-gradient-secondary'
                                                    };
                                                    $approvalLabel = match($item['approvalStatus'] ?? 'pending') {
                                                        'pending' => 'Menunggu',
                                                        'approved' => 'Disetujui',
                                                        'rejected' => 'Ditolak',
                                                        default => $item['approvalStatus']
                                                    };
                                                @endphp
                                                <span class="badge badge-sm {{ $approvalColor }}">{{ $approvalLabel }}</span>
                                                @if(($item['approvalStatus'] ?? '') === 'rejected' && ($item['rejectionReason'] ?? false))
                                                    <p class="text-xs text-danger mb-0 mt-1">{{ $item['rejectionReason'] }}</p>
                                                @endif
                                            </td>
                                            @if($isDraft)
                                            <td class="align-middle">
                                                <form action="{{ route('kepala-lab.procurements.items.remove', [$draft['_id'], $item['_id']]) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus item ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link text-danger text-xs p-0 mb-0">
                                                        <i class="material-icons text-sm">delete</i>
                                                    </button>
                                                </form>
                                            </td>
                                            @endif
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="{{ $isDraft ? 7 : 6 }}" class="text-center py-4">
                                                <p class="text-secondary text-sm mb-0">Belum ada item dalam draf ini.</p>
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
                <a href="{{ route('kepala-lab.procurements.index') }}" class="btn btn-outline-secondary">
                    <i class="material-icons text-sm me-1">arrow_back</i> Kembali
                </a>
            </div>

            @else
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-body text-center py-5">
                            <p class="text-secondary">Draf tidak ditemukan.</p>
                            <a href="{{ route('kepala-lab.procurements.index') }}" class="btn bg-gradient-primary">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <x-footers.auth></x-footers.auth>
        </div>
    </main>
</x-layout>
