<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage='dashboard'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Dashboard"></x-navbars.navs.auth>
        <div class="container-fluid py-4">

            {{-- Welcome --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-3">
                            <h5 class="mb-1">Selamat datang, {{ $user['name'] ?? 'User' }}!</h5>
                            <p class="text-sm mb-0 text-muted">
                                Anda login sebagai
                                <span class="badge bg-gradient-primary">
                                    {{ match($user['role'] ?? '') {
                                        'admin' => 'Administrator',
                                        'kepala_lab' => 'Kepala Laboratorium',
                                        'kaprodi' => 'Ketua Program Studi',
                                        'staf_admin' => 'Staf Administrasi',
                                        'staf_lab' => 'Staf Laboratorium',
                                        default => $user['role'] ?? '-'
                                    } }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stat Cards --}}
            <div class="row">
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10">inventory_2</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Total Aset</p>
                                <h4 class="mb-0">{{ $stats['totalAssets'] ?? 0 }}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <p class="mb-0"><span class="text-success text-sm font-weight-bolder">Aktif </span>di semua lab</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10">science</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Barang Habis Pakai</p>
                                <h4 class="mb-0">{{ $stats['totalConsumables'] ?? 0 }}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <p class="mb-0"><span class="text-warning text-sm font-weight-bolder">{{ $stats['lowStockConsumables'] ?? 0 }} </span>stok menipis</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10">meeting_room</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Ruangan Lab</p>
                                <h4 class="mb-0">{{ $stats['totalRooms'] ?? 0 }}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <p class="mb-0"><span class="text-success text-sm font-weight-bolder">Terdaftar </span>di sistem</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10">description</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Draf Pengadaan</p>
                                <h4 class="mb-0">{{ $stats['totalDrafts'] ?? 0 }}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <p class="mb-0"><span class="text-info text-sm font-weight-bolder">{{ $stats['submittedDrafts'] ?? 0 }} </span>menunggu review</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions per Role --}}
            <div class="row mt-4">
                <div class="col-lg-8 col-md-6 mb-md-0 mb-4">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h6>Menu Cepat</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="row">
                                @if(($user['role'] ?? '') === 'admin')
                                <div class="col-md-6 mb-3">
                                    <a href="{{ route('admin.users.index') }}" class="btn bg-gradient-dark w-100">
                                        <i class="material-icons me-1">people</i> Kelola User
                                    </a>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <a href="{{ route('admin.rooms.index') }}" class="btn bg-gradient-dark w-100">
                                        <i class="material-icons me-1">meeting_room</i> Kelola Ruangan
                                    </a>
                                </div>
                                @elseif(($user['role'] ?? '') === 'kepala_lab')
                                <div class="col-md-6 mb-3">
                                    <a href="{{ route('kepala-lab.procurements.index') }}" class="btn bg-gradient-dark w-100">
                                        <i class="material-icons me-1">description</i> Lihat Draf Pengadaan
                                    </a>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <a href="{{ route('kepala-lab.procurements.create') }}" class="btn bg-gradient-primary w-100">
                                        <i class="material-icons me-1">add</i> Buat Draf Baru
                                    </a>
                                </div>
                                @elseif(($user['role'] ?? '') === 'kaprodi')
                                <div class="col-md-6 mb-3">
                                    <a href="{{ route('kaprodi.procurements.index') }}" class="btn bg-gradient-dark w-100">
                                        <i class="material-icons me-1">fact_check</i> Review Pengadaan
                                    </a>
                                </div>
                                @elseif(($user['role'] ?? '') === 'staf_admin')
                                <div class="col-md-6 mb-3">
                                    <a href="{{ route('staf-admin.procurements.index') }}" class="btn bg-gradient-dark w-100">
                                        <i class="material-icons me-1">local_shipping</i> Penerimaan Barang
                                    </a>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <a href="{{ route('staf-admin.assets.index') }}" class="btn bg-gradient-dark w-100">
                                        <i class="material-icons me-1">inventory_2</i> Kelola Aset
                                    </a>
                                </div>
                                @elseif(($user['role'] ?? '') === 'staf_lab')
                                <div class="col-md-6 mb-3">
                                    <a href="{{ route('staf-lab.consumables.index') }}" class="btn bg-gradient-dark w-100">
                                        <i class="material-icons me-1">science</i> Barang Habis Pakai
                                    </a>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <a href="{{ route('staf-lab.maintenance.index') }}" class="btn bg-gradient-dark w-100">
                                        <i class="material-icons me-1">build</i> Log Pemeliharaan
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Info Panel --}}
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100">
                        <div class="card-header pb-0">
                            <h6>Informasi Sistem</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="timeline timeline-one-side">
                                <div class="timeline-block mb-3">
                                    <span class="timeline-step">
                                        <i class="material-icons text-success text-gradient">check_circle</i>
                                    </span>
                                    <div class="timeline-content">
                                        <h6 class="text-dark text-sm font-weight-bold mb-0">Sistem Aktif</h6>
                                        <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">Backend & Frontend terhubung</p>
                                    </div>
                                </div>
                                <div class="timeline-block mb-3">
                                    <span class="timeline-step">
                                        <i class="material-icons text-info text-gradient">person</i>
                                    </span>
                                    <div class="timeline-content">
                                        <h6 class="text-dark text-sm font-weight-bold mb-0">{{ $stats['totalUsers'] ?? 0 }} User Terdaftar</h6>
                                        <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">Dengan 5 level akses</p>
                                    </div>
                                </div>
                                <div class="timeline-block mb-3">
                                    <span class="timeline-step">
                                        <i class="material-icons text-warning text-gradient">warning</i>
                                    </span>
                                    <div class="timeline-content">
                                        <h6 class="text-dark text-sm font-weight-bold mb-0">{{ $stats['maintenanceNeeded'] ?? 0 }} Aset Perlu Perhatian</h6>
                                        <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">Rusak ringan / dalam pemeliharaan</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <x-footers.auth></x-footers.auth>
        </div>
    </main>
</x-layout>
