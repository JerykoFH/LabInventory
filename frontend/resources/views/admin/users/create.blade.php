<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage='users'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Tambah User"></x-navbars.navs.auth>
        <div class="container-fluid py-4">

            <div class="row">
                <div class="col-lg-8 col-md-10 mx-auto">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">Tambah User Baru</h6>
                            </div>
                        </div>
                        <div class="card-body">

                            {{-- Validation errors --}}
                            @if($errors->any())
                            <div class="alert alert-danger text-white text-sm">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            <form action="{{ route('admin.users.store') }}" method="POST">
                                @csrf

                                <div class="input-group input-group-outline my-3 {{ old('name') ? 'is-filled' : '' }}">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                </div>

                                <div class="input-group input-group-outline my-3 {{ old('email') ? 'is-filled' : '' }}">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                                </div>

                                <div class="input-group input-group-outline my-3 {{ old('password') ? 'is-filled' : '' }}">
                                    <label class="form-label">Password (min 8 karakter)</label>
                                    <input type="password" name="password" class="form-control" minlength="8" required>
                                </div>

                                <div class="input-group input-group-static my-3">
                                    <label class="ms-0">Role</label>
                                    <select name="role" class="form-control" required>
                                        <option value="">-- Pilih Role --</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>
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

                                <div class="d-flex justify-content-end mt-4">
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary me-2">Batal</a>
                                    <button type="submit" class="btn bg-gradient-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <x-footers.auth></x-footers.auth>
        </div>
    </main>
</x-layout>
