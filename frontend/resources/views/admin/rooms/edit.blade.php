<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage='rooms'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Edit Ruangan"></x-navbars.navs.auth>
        <div class="container-fluid py-4">

            <div class="row">
                <div class="col-lg-8 col-md-10 mx-auto">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">Edit Ruangan — {{ $room['name'] ?? '' }}</h6>
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

                            @if($room)
                            <form action="{{ route('admin.rooms.update', $room['_id']) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="input-group input-group-outline my-3 is-filled">
                                    <label class="form-label">Nama Ruangan</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $room['name']) }}" required>
                                </div>

                                <div class="input-group input-group-outline my-3 is-filled">
                                    <label class="form-label">Kode Ruangan</label>
                                    <input type="text" name="code" class="form-control" value="{{ old('code', $room['code']) }}" required>
                                </div>

                                <div class="input-group input-group-outline my-3 {{ ($room['location'] ?? '') ? 'is-filled' : '' }}">
                                    <label class="form-label">Lokasi</label>
                                    <input type="text" name="location" class="form-control" value="{{ old('location', $room['location'] ?? '') }}">
                                </div>

                                <div class="input-group input-group-outline my-3 is-filled">
                                    <label class="form-label">Kapasitas (orang)</label>
                                    <input type="number" name="capacity" class="form-control" value="{{ old('capacity', $room['capacity'] ?? 0) }}" min="0">
                                </div>

                                <div class="input-group input-group-dynamic my-3">
                                    <textarea name="description" class="form-control" rows="3" placeholder="Deskripsi">{{ old('description', $room['description'] ?? '') }}</textarea>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <a href="{{ route('admin.rooms.index') }}" class="btn btn-outline-secondary me-2">Batal</a>
                                    <button type="submit" class="btn bg-gradient-primary">Simpan Perubahan</button>
                                </div>
                            </form>
                            @else
                            <p class="text-secondary text-sm">Ruangan tidak ditemukan.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <x-footers.auth></x-footers.auth>
        </div>
    </main>
</x-layout>
