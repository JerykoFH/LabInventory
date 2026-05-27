<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage='procurements'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Edit Draf Pengadaan"></x-navbars.navs.auth>
        <div class="container-fluid py-4">

            <div class="row">
                <div class="col-lg-8 col-md-10 mx-auto">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">Edit Draf — {{ $draft['title'] ?? '' }}</h6>
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

                            @if($draft)
                            <form action="{{ route('kepala-lab.procurements.update', $draft['_id']) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="input-group input-group-outline my-3 is-filled">
                                    <label class="form-label">Judul Draf</label>
                                    <input type="text" name="title" class="form-control" value="{{ old('title', $draft['title']) }}" required>
                                </div>

                                <div class="input-group input-group-outline my-3 is-filled">
                                    <label class="form-label">Tahun Pengadaan</label>
                                    <input type="number" name="year" class="form-control" value="{{ old('year', $draft['year']) }}" min="2020" max="2100" required>
                                </div>

                                <div class="input-group input-group-dynamic my-3">
                                    <textarea name="notes" class="form-control" rows="3" placeholder="Catatan (opsional)">{{ old('notes', $draft['notes'] ?? '') }}</textarea>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <a href="{{ route('kepala-lab.procurements.show', $draft['_id']) }}" class="btn btn-outline-secondary me-2">Batal</a>
                                    <button type="submit" class="btn bg-gradient-primary">Simpan Perubahan</button>
                                </div>
                            </form>
                            @else
                            <p class="text-secondary text-sm">Draf tidak ditemukan.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <x-footers.auth></x-footers.auth>
        </div>
    </main>
</x-layout>
