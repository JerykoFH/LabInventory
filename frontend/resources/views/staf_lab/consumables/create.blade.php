<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage='consumables'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Tambah Item BHP"></x-navbars.navs.auth>
        <div class="container-fluid py-4">

            <div class="row">
                <div class="col-lg-8 col-md-10 mx-auto">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">Tambah Item Barang Habis Pakai</h6>
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

                            <form action="{{ route('staf-lab.consumables.store') }}" method="POST">
                                @csrf

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="input-group input-group-outline my-3 {{ old('name') ? 'is-filled' : '' }}">
                                            <label class="form-label">Nama Item <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required maxlength="200">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group input-group-outline my-3 {{ old('category') ? 'is-filled' : '' }}">
                                            <label class="form-label">Kategori</label>
                                            <input type="text" name="category" class="form-control" value="{{ old('category') }}" maxlength="100">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group input-group-outline my-3 {{ old('unit') ? 'is-filled' : '' }}">
                                            <label class="form-label">Satuan (botol, pack, liter…) <span class="text-danger">*</span></label>
                                            <input type="text" name="unit" class="form-control" value="{{ old('unit') }}" required maxlength="50">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group input-group-outline my-3 {{ old('currentStock') !== null ? 'is-filled' : '' }}">
                                            <label class="form-label">Stok Awal <span class="text-danger">*</span></label>
                                            <input type="number" name="currentStock" class="form-control" value="{{ old('currentStock', 0) }}" min="0" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group input-group-outline my-3 is-filled">
                                            <label class="form-label">Minimum Stok (untuk notif)</label>
                                            <input type="number" name="minimumStock" class="form-control" value="{{ old('minimumStock', 5) }}" min="0">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="input-group input-group-outline my-3 {{ old('location') ? 'is-filled' : '' }}">
                                            <label class="form-label">Lokasi Penyimpanan</label>
                                            <input type="text" name="location" class="form-control" value="{{ old('location') }}" maxlength="200">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="input-group input-group-dynamic my-3">
                                            <textarea name="notes" class="form-control" rows="3" placeholder="Catatan tambahan (opsional)">{{ old('notes') }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <a href="{{ route('staf-lab.consumables.index') }}" class="btn btn-outline-secondary me-2">Batal</a>
                                    <button type="submit" class="btn bg-gradient-primary">Simpan Item</button>
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
