<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage='procurements'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Penerimaan Barang"></x-navbars.navs.auth>
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
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-3">
                                <div>
                                    <h6 class="text-white text-capitalize ps-3 mb-0">Daftar Pengadaan Siap Diterima</h6>
                                    <p class="text-white text-xs ps-3 mb-0 opacity-8">Draf yang telah difinalisasi oleh Kaprodi</p>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-white text-dark px-3 py-2">
                                        <i class="material-icons text-sm me-1" style="vertical-align: middle;">local_shipping</i>
                                        {{ count($drafts) }} Draf
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Judul Draf</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Dibuat Oleh</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tahun</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Jml. Item</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal Dikunci</th>
                                            <th class="text-secondary opacity-7">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($drafts as $draft)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="icon icon-sm icon-shape bg-gradient-success shadow text-center border-radius-md me-2 d-flex align-items-center justify-content-center">
                                                        <i class="material-icons opacity-10 text-white" style="font-size: 16px;">inventory</i>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $draft['title'] }}</h6>
                                                        @if($draft['notes'] ?? false)
                                                            <p class="text-xs text-secondary mb-0">{{ Str::limit($draft['notes'], 50) }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $draft['createdBy']['name'] ?? 'Tidak diketahui' }}</p>
                                                <p class="text-xs text-secondary mb-0">{{ $draft['createdBy']['email'] ?? '' }}</p>
                                            </td>
                                            <td>
                                                <span class="text-secondary text-sm font-weight-bold">{{ $draft['year'] }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                @php
                                                    $itemCount = count($draft['items'] ?? []);
                                                @endphp
                                                <span class="badge bg-gradient-info">{{ $itemCount }} item</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="text-secondary text-xs font-weight-bold">
                                                    {{ isset($draft['lockedAt']) ? \Carbon\Carbon::parse($draft['lockedAt'])->format('d M Y') : '-' }}
                                                </span>
                                            </td>
                                            <td class="align-middle">
                                                <a href="{{ route('staf-admin.procurements.show', $draft['_id']) }}" class="btn btn-sm btn-outline-primary mb-0" title="Detail Penerimaan">
                                                    <i class="material-icons text-sm me-1">visibility</i> Lihat Detail
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <i class="material-icons text-secondary" style="font-size: 48px;">inbox</i>
                                                <p class="text-secondary text-sm mb-0 mt-2">Belum ada draf pengadaan yang difinalisasi.</p>
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
