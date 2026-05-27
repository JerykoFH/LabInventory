<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage='users'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Edit User"></x-navbars.navs.auth>
        <div class="container-fluid py-4">

            <div class="row">
                <div class="col-lg-8 col-md-10 mx-auto">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">Edit User — {{ $user['name'] ?? '' }}</h6>
                            </div>
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

                            @if($user)
                            <form action="{{ route('admin.users.update', $user['_id']) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="input-group input-group-outline my-3 is-filled">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $user['name']) }}" required>
                                </div>

                                <div class="input-group input-group-outline my-3 is-filled">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $user['email']) }}" required>
                                </div>

                                <div class="input-group input-group-static my-3">
                                    <label class="ms-0">Role</label>
                                    <select name="role" class="form-control" required>
                                        @foreach($roles as $role)
                                            <option value="{{ $role }}" {{ old('role', $user['role']) == $role ? 'selected' : '' }}>
                                                {{ match($role) {
                                                    'admin' => 'Administrator',
                                                    'kepala_lab' => 'Kepala Laboratorium',
                                                    'kaprodi' => 'Ketua Program Studi',
                                                    'staf_admin' => 'Staf Administrasi',
                                                    'staf_lab' => 'Staf Laboratorium',
                                                    default => $role
                                                } }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-check form-switch my-3">
                                    <input type="hidden" name="isActive" value="0">
                                    <input class="form-check-input" type="checkbox" name="isActive" value="1"
                                        {{ old('isActive', $user['isActive'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label">User Aktif</label>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary me-2">Batal</a>
                                    <button type="submit" class="btn bg-gradient-primary">Simpan Perubahan</button>
                                </div>
                            </form>
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
