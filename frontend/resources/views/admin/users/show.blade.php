<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage='users'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Detail User"></x-navbars.navs.auth>
        <div class="container-fluid py-4">

            <div class="row">
                <div class="col-lg-8 col-md-10 mx-auto">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-3">
                                <h6 class="text-white text-capitalize ps-3 mb-0">Detail User</h6>
                                <div>
                                    <a href="{{ route('admin.users.edit', $user['_id']) }}" class="btn btn-sm btn-white mb-0 me-1">
                                        <i class="material-icons text-sm me-1">edit</i> Edit
                                    </a>
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-white mb-0">
                                        <i class="material-icons text-sm me-1">arrow_back</i> Kembali
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($user)
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="text-uppercase text-secondary text-xxs font-weight-bolder">Nama</label>
                                    <p class="text-sm mb-0">{{ $user['name'] }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-uppercase text-secondary text-xxs font-weight-bolder">Email</label>
                                    <p class="text-sm mb-0">{{ $user['email'] }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-uppercase text-secondary text-xxs font-weight-bolder">Role</label>
                                    <p class="mb-0">
                                        <span class="badge bg-gradient-info">
                                            {{ match($user['role'] ?? '') {
                                                'admin' => 'Administrator',
                                                'kepala_lab' => 'Kepala Laboratorium',
                                                'kaprodi' => 'Ketua Program Studi',
                                                'staf_admin' => 'Staf Administrasi',
                                                'staf_lab' => 'Staf Laboratorium',
                                                default => $user['role']
                                            } }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-uppercase text-secondary text-xxs font-weight-bolder">Status</label>
                                    <p class="mb-0">
                                        @if($user['isActive'] ?? true)
                                            <span class="badge bg-gradient-success">Aktif</span>
                                        @else
                                            <span class="badge bg-gradient-secondary">Nonaktif</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-uppercase text-secondary text-xxs font-weight-bolder">Dibuat</label>
                                    <p class="text-sm mb-0">
                                        {{ isset($user['createdAt']) ? \Carbon\Carbon::parse($user['createdAt'])->format('d M Y, H:i') : '-' }}
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-uppercase text-secondary text-xxs font-weight-bolder">Terakhir Diubah</label>
                                    <p class="text-sm mb-0">
                                        {{ isset($user['updatedAt']) ? \Carbon\Carbon::parse($user['updatedAt'])->format('d M Y, H:i') : '-' }}
                                    </p>
                                </div>
                            </div>
                            @else
                            <p class="text-secondary text-sm">User tidak ditemukan.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <x-footers.auth></x-footers.auth>
        </div>
    </main>
</x-layout>
