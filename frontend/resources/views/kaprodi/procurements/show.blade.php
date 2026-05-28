<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage='procurements-review'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Review Detail Draf"></x-navbars.navs.auth>
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
            @if($errors->any())
            <div class="alert alert-danger text-white text-sm">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if($draft)
            @php
                $isLocked = $draft['status'] === 'locked';
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
                                    <p class="text-white text-xs ps-3 mb-0 opacity-8">Diajukan oleh {{ $draft['createdBy']['name'] ?? '-' }}</p>
                                </div>
                                <div>
                                    @if($isSubmitted)
                                    <form action="{{ route('kaprodi.procurements.finalize', $draft['_id']) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin memfinalisasi draf ini? Setelah finalisasi, keputusan tidak dapat diubah lagi.')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success mb-0">
                                            <i class="material-icons text-sm me-1">check_circle</i> Finalisasi & Kunci Draf
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="text-uppercase text-secondary text-xxs font-weight-bolder">Status Draf</label>
                                    <p class="mb-0">
                                        @php
                                            $statusColor = match($draft['status']) {
                                                'submitted' => 'bg-gradient-warning',
                                                'locked' => 'bg-gradient-success',
                                                default => 'bg-gradient-secondary'
                                            };
                                            $statusLabel = match($draft['status']) {
                                                'submitted' => 'Perlu Review',
                                                'locked' => 'Final (Dikunci)',
                                                default => $draft['status']
                                            };
                                        @endphp
                                        <span class="badge {{ $statusColor }}">{{ $statusLabel }}</span>
                                    </p>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="text-uppercase text-secondary text-xxs font-weight-bolder">Tahun</label>
                                    <p class="text-sm font-weight-bold mb-0">{{ $draft['year'] }}</p>
                                </div>
                                @if($draft['submittedAt'] ?? false)
                                <div class="col-md-3 mb-3">
                                    <label class="text-uppercase text-secondary text-xxs font-weight-bolder">Disubmit</label>
                                    <p class="text-sm mb-0">{{ \Carbon\Carbon::parse($draft['submittedAt'])->format('d M Y, H:i') }}</p>
                                </div>
                                @endif
                                @if($draft['lockedAt'] ?? false)
                                <div class="col-md-3 mb-3">
                                    <label class="text-uppercase text-secondary text-xxs font-weight-bolder">Difinalisasi Pada</label>
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

            {{-- Items Review List --}}
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                            <h6>Daftar Barang yang Diajukan ({{ count($draft['items'] ?? []) }})</h6>
                            @php
                                $totalPrice = collect($draft['items'] ?? [])->sum(function($item) {
                                    return ($item['estimatedPrice'] ?? 0) * ($item['quantity'] ?? 1);
                                });
                                $approvedPrice = collect($draft['items'] ?? [])->where('approvalStatus', 'approved')->sum(function($item) {
                                    return ($item['estimatedPrice'] ?? 0) * ($item['quantity'] ?? 1);
                                });
                            @endphp
                            <div class="text-end">
                                <span class="badge bg-gradient-secondary p-2 me-1">Total Pengajuan: Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                                <span class="badge bg-gradient-success p-2">Total Disetujui: Rp {{ number_format($approvedPrice, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Barang</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tipe</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Jumlah</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Subtotal Harga</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                            <th class="text-center text-secondary opacity-7">Aksi Review</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($draft['items'] ?? [] as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $item['name'] }}</h6>
                                                        <p class="text-xs text-secondary mb-0 mt-1">@ Rp {{ number_format($item['estimatedPrice'] ?? 0, 0, ',', '.') }}</p>
                                                        @if($item['purchaseLink'] ?? false)
                                                            <a href="{{ $item['purchaseLink'] }}" target="_blank" class="text-xs text-info">
                                                                <i class="material-icons text-xs">link</i> Link Pembelian
                                                            </a>
                                                        @endif
                                                        @if($item['replacedAsset'] ?? false)
                                                            <p class="text-xs text-warning mb-0 mt-1">
                                                                <i class="material-icons text-xs">swap_horiz</i>
                                                                Mengganti Aset Lama
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
                                                    <p class="text-xs text-danger mb-0 mt-1" style="max-width: 150px; white-space: normal; margin: 0 auto;">{{ $item['rejectionReason'] }}</p>
                                                @endif
                                            </td>
                                            <td class="align-middle text-center">
                                                @if($isSubmitted)
                                                    <button type="button" class="btn btn-sm btn-outline-success mb-0 px-2 py-1 me-1" data-bs-toggle="modal" data-bs-target="#reviewModal{{ $item['_id'] }}" onclick="setReviewMode('{{ $item['_id'] }}', 'approved')">
                                                        <i class="material-icons text-sm">thumb_up</i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger mb-0 px-2 py-1" data-bs-toggle="modal" data-bs-target="#reviewModal{{ $item['_id'] }}" onclick="setReviewMode('{{ $item['_id'] }}', 'rejected')">
                                                        <i class="material-icons text-sm">thumb_down</i>
                                                    </button>
                                                    
                                                    <!-- Review Modal -->
                                                    <div class="modal fade" id="reviewModal{{ $item['_id'] }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content text-start">
                                                                <form action="{{ route('kaprodi.procurements.items.review', [$draft['_id'], $item['_id']]) }}" method="POST">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title font-weight-normal">Review Barang: {{ $item['name'] }}</h5>
                                                                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <input type="hidden" name="approvalStatus" id="status_{{ $item['_id'] }}" value="approved">
                                                                        
                                                                        <p id="msg_approved_{{ $item['_id'] }}" class="text-success fw-bold d-none">
                                                                            <i class="material-icons align-middle text-sm">check_circle</i> Anda menyetujui pengadaan barang ini.
                                                                        </p>
                                                                        <p id="msg_rejected_{{ $item['_id'] }}" class="text-danger fw-bold d-none">
                                                                            <i class="material-icons align-middle text-sm">cancel</i> Anda menolak pengadaan barang ini.
                                                                        </p>
                                                                        
                                                                        <div id="reason_container_{{ $item['_id'] }}" class="mt-3 d-none">
                                                                            <div class="input-group input-group-dynamic">
                                                                                <textarea name="rejectionReason" id="reason_{{ $item['_id'] }}" class="form-control" rows="3" placeholder="Alasan Penolakan (Wajib jika ditolak)"></textarea>
                                                                            </div>
                                                                            <small id="reason_error_{{ $item['_id'] }}" class="text-danger d-none">Alasan penolakan wajib diisi.</small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                                                                        <button type="submit" class="btn bg-gradient-primary" id="submitBtn_{{ $item['_id'] }}">Simpan Keputusan</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-xs text-secondary">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <p class="text-secondary text-sm mb-0">Tidak ada item.</p>
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
                <a href="{{ route('kaprodi.procurements.index') }}" class="btn btn-outline-secondary">
                    <i class="material-icons text-sm me-1">arrow_back</i> Kembali
                </a>
            </div>

            @else
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-body text-center py-5">
                            <p class="text-secondary">Draf tidak ditemukan.</p>
                            <a href="{{ route('kaprodi.procurements.index') }}" class="btn bg-gradient-primary">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <x-footers.auth></x-footers.auth>
        </div>
    </main>

    @push('js')
    <script>
        function setReviewMode(itemId, status) {
            const statusInput = document.getElementById('status_' + itemId);
            const msgApproved = document.getElementById('msg_approved_' + itemId);
            const msgRejected = document.getElementById('msg_rejected_' + itemId);
            const reasonContainer = document.getElementById('reason_container_' + itemId);
            const reasonInput = document.getElementById('reason_' + itemId);
            const reasonError = document.getElementById('reason_error_' + itemId);
            
            statusInput.value = status;
            
            // Reset error state
            reasonError.classList.add('d-none');
            reasonInput.classList.remove('is-invalid');
            
            if (status === 'approved') {
                msgApproved.classList.remove('d-none');
                msgRejected.classList.add('d-none');
                reasonContainer.classList.add('d-none');
                reasonInput.removeAttribute('required');
            } else {
                msgApproved.classList.add('d-none');
                msgRejected.classList.remove('d-none');
                reasonContainer.classList.remove('d-none');
                reasonInput.setAttribute('required', 'required');
            }
        }

        // Validasi form sebelum submit — alasan wajib diisi kalau menolak
        document.querySelectorAll('form[action*="/review"]').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                const statusInput = form.querySelector('input[name="approvalStatus"]');
                if (statusInput && statusInput.value === 'rejected') {
                    const reasonInput = form.querySelector('textarea[name="rejectionReason"]');
                    const reasonError = reasonInput ? reasonInput.closest('.mt-3').querySelector('small') : null;

                    if (!reasonInput || !reasonInput.value.trim()) {
                        e.preventDefault();
                        if (reasonInput) {
                            reasonInput.classList.add('is-invalid');
                            reasonInput.style.borderColor = '#f44335';
                        }
                        if (reasonError) {
                            reasonError.classList.remove('d-none');
                        }
                        return false;
                    }
                }
            });
        });
    </script>
    @endpush
</x-layout>

