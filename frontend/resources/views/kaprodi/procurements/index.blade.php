<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage='procurements-review'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Review Draf Pengadaan"></x-navbars.navs.auth>
        <div class="container-fluid py-4">

            {{-- Flash message --}}
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

            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">Review Draf Pengadaan Barang</h6>
                            </div>
                        </div>

                        <div class="card-body px-4 pb-2 mt-3">
                            <div class="row align-items-end">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label mb-1 text-xs text-secondary">Pencarian</label>
                                    <div class="input-group border border-radius-md">
                                        <input type="text" id="searchInput" class="form-control px-3 border-0" placeholder="Cari nama draf...">
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label mb-1 text-xs text-secondary">Dari Tanggal</label>
                                    <div class="input-group border border-radius-md">
                                        <input type="date" id="startDate" class="form-control px-3 border-0">
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label mb-1 text-xs text-secondary">Sampai Tanggal</label>
                                    <div class="input-group border border-radius-md">
                                        <input type="date" id="endDate" class="form-control px-3 border-0">
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label mb-1 text-xs text-secondary">Urutkan</label>
                                    <div class="dropdown w-100">
                                        <button class="btn dropdown-toggle w-100 text-start d-flex justify-content-between align-items-center bg-white px-3 mb-0 border-radius-md" 
                                                style="border: 1px solid #dee2e6; color: #495057; text-transform: none; font-weight: normal; padding-top: 0.6rem; padding-bottom: 0.6rem;" 
                                                type="button" id="sortDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                            Terbaru
                                        </button>
                                        <ul class="dropdown-menu w-100 px-2 py-2" aria-labelledby="sortDropdownBtn">
                                            <li><a class="dropdown-item border-radius-md" href="#" data-val="newest">Terbaru</a></li>
                                            <li><a class="dropdown-item border-radius-md" href="#" data-val="oldest">Terlama</a></li>
                                            <li><a class="dropdown-item border-radius-md" href="#" data-val="az">Abjad (A-Z)</a></li>
                                            <li><a class="dropdown-item border-radius-md" href="#" data-val="za">Abjad (Z-A)</a></li>
                                        </ul>
                                        <input type="hidden" id="sortSelect" value="newest">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body px-0 pb-2 pt-0">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Judul Draf</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Diajukan Oleh</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tahun</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal Submit</th>
                                            <th class="text-secondary opacity-7">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="draftsTableBody">
                                        @forelse($drafts as $draft)
                                        <tr class="draft-item" 
                                            data-name="{{ strtolower($draft['title']) }}" 
                                            data-date="{{ isset($draft['submittedAt']) ? \Carbon\Carbon::parse($draft['submittedAt'])->format('Y-m-d') : '2000-01-01' }}">
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="icon icon-sm icon-shape bg-gradient-info shadow text-center border-radius-md me-2 d-flex align-items-center justify-content-center">
                                                        <i class="material-icons opacity-10 text-white" style="font-size: 16px;">assignment</i>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $draft['title'] }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $draft['createdBy']['name'] ?? 'Tidak diketahui' }}</p>
                                            </td>
                                            <td>
                                                <span class="text-secondary text-sm font-weight-bold">{{ $draft['year'] }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                @php
                                                    $statusColor = match($draft['status'] ?? 'submitted') {
                                                        'submitted' => 'bg-gradient-warning',
                                                        'locked' => 'bg-gradient-success',
                                                        default => 'bg-gradient-secondary'
                                                    };
                                                    $statusLabel = match($draft['status'] ?? 'submitted') {
                                                        'submitted' => 'Perlu Review',
                                                        'locked' => 'Final',
                                                        default => $draft['status']
                                                    };
                                                @endphp
                                                <span class="badge badge-sm {{ $statusColor }}">{{ $statusLabel }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="text-secondary text-xs font-weight-bold">
                                                    {{ isset($draft['submittedAt']) ? \Carbon\Carbon::parse($draft['submittedAt'])->format('d M Y') : '-' }}
                                                </span>
                                            </td>
                                            <td class="align-middle">
                                                <a href="{{ route('kaprodi.procurements.show', $draft['_id']) }}" class="btn btn-sm btn-outline-info mb-0" title="Review">
                                                    Lihat & Review
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <p class="text-secondary text-sm mb-0">Belum ada draf pengadaan yang perlu di-review.</p>
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
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const startDate = document.getElementById('startDate');
            const endDate = document.getElementById('endDate');
            const sortSelect = document.getElementById('sortSelect');
            const tableBody = document.getElementById('draftsTableBody');

            function filterAndSort() {
                const searchTxt = searchInput.value.toLowerCase();
                const start = startDate.value;
                const end = endDate.value;
                const sortBy = sortSelect.value;
                
                const rows = Array.from(tableBody.querySelectorAll('.draft-item'));

                rows.forEach(row => {
                    const name = row.getAttribute('data-name');
                    const date = row.getAttribute('data-date');
                    let show = true;

                    if (searchTxt && !name.includes(searchTxt)) {
                        show = false;
                    }
                    if (start && date < start) {
                        show = false;
                    }
                    if (end && date > end) {
                        show = false;
                    }

                    row.style.display = show ? '' : 'none';
                });

                const visibleRows = rows.filter(row => row.style.display !== 'none');
                visibleRows.sort((a, b) => {
                    if (sortBy === 'az') {
                        return a.getAttribute('data-name').localeCompare(b.getAttribute('data-name'));
                    } else if (sortBy === 'za') {
                        return b.getAttribute('data-name').localeCompare(a.getAttribute('data-name'));
                    } else if (sortBy === 'newest') {
                        return b.getAttribute('data-date').localeCompare(a.getAttribute('data-date'));
                    } else if (sortBy === 'oldest') {
                        return a.getAttribute('data-date').localeCompare(b.getAttribute('data-date'));
                    }
                    return 0;
                });

                visibleRows.forEach(row => tableBody.appendChild(row));
            }

            searchInput.addEventListener('keyup', filterAndSort);
            startDate.addEventListener('change', filterAndSort);
            endDate.addEventListener('change', filterAndSort);
            
            const sortDropdownBtn = document.getElementById('sortDropdownBtn');
            const dropdownItems = document.querySelectorAll('.dropdown-menu .dropdown-item');
            
            dropdownItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    const val = this.getAttribute('data-val');
                    const text = this.innerText;
                    
                    sortSelect.value = val;
                    sortDropdownBtn.childNodes[0].nodeValue = text + ' ';
                    
                    filterAndSort();
                });
            });
        });
    </script>
    @endpush
</x-layout>
