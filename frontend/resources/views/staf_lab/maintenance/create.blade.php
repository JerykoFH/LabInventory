<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage='maintenance'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Catat Pemeliharaan"></x-navbars.navs.auth>
        <div class="container-fluid py-4">

            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">Formulir Log Pemeliharaan Aset</h6>
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

                            <form action="{{ route('staf-lab.maintenance.store') }}" method="POST" id="maintenanceForm">
                                @csrf

                                <h6 class="text-uppercase text-secondary text-xs font-weight-bolder mt-3 mb-3">Informasi Pemeliharaan</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group input-group-static my-3">
                                            <label class="ms-0">Aset yang Dipelihara <span class="text-danger">*</span></label>
                                            <select name="asset" class="form-control" required>
                                                <option value="">-- Pilih Aset --</option>
                                                @foreach($assets as $asset)
                                                    <option value="{{ $asset['_id'] }}" {{ old('asset') == $asset['_id'] ? 'selected' : '' }}>
                                                        {{ $asset['name'] }}{{ $asset['assetCode'] ? ' (' . $asset['assetCode'] . ')' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group input-group-outline my-3 is-filled">
                                            <label class="form-label">Tanggal Pemeliharaan <span class="text-danger">*</span></label>
                                            <input type="date" name="maintenanceDate" class="form-control" value="{{ old('maintenanceDate', date('Y-m-d')) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group input-group-static my-3">
                                            <label class="ms-0">Jenis Pemeliharaan <span class="text-danger">*</span></label>
                                            <select name="type" class="form-control" required>
                                                <option value="rutin"      {{ old('type') == 'rutin' ? 'selected' : '' }}>Rutin</option>
                                                <option value="perbaikan"  {{ old('type') == 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                                                <option value="pengecekan" {{ old('type') == 'pengecekan' ? 'selected' : '' }}>Pengecekan</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group input-group-static my-3">
                                            <label class="ms-0">Kondisi Aset Sebelum</label>
                                            <select name="conditionBefore" class="form-control">
                                                <option value="">-- Tidak dicatat --</option>
                                                <option value="baik"         {{ old('conditionBefore') == 'baik' ? 'selected' : '' }}>Baik</option>
                                                <option value="rusak_ringan" {{ old('conditionBefore') == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                                <option value="rusak_berat"  {{ old('conditionBefore') == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group input-group-static my-3">
                                            <label class="ms-0">Kondisi Aset Sesudah</label>
                                            <select name="conditionAfter" class="form-control">
                                                <option value="">-- Tidak dicatat --</option>
                                                <option value="baik"         {{ old('conditionAfter') == 'baik' ? 'selected' : '' }}>Baik</option>
                                                <option value="rusak_ringan" {{ old('conditionAfter') == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                                <option value="rusak_berat"  {{ old('conditionAfter') == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                                                <option value="tidak_aktif"  {{ old('conditionAfter') == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="input-group input-group-dynamic my-3">
                                            <textarea name="description" class="form-control" rows="3" placeholder="Deskripsi pekerjaan yang dilakukan *" required>{{ old('description') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="input-group input-group-dynamic my-3">
                                            <textarea name="notes" class="form-control" rows="2" placeholder="Catatan tambahan (opsional)">{{ old('notes') }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <hr class="horizontal dark my-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="text-uppercase text-secondary text-xs font-weight-bolder mb-0">BHP yang Digunakan</h6>
                                    <button type="button" class="btn btn-sm bg-gradient-info mb-0" id="addBhpRow">
                                        <i class="material-icons text-sm me-1">add</i> Tambah BHP
                                    </button>
                                </div>

                                <div id="bhpContainer">
                                    @if(old('consumablesUsed'))
                                        @foreach(old('consumablesUsed') as $i => $usage)
                                        <div class="row bhp-row align-items-center mb-2">
                                            <div class="col-md-7">
                                                <div class="input-group input-group-static">
                                                    <select name="consumablesUsed[{{ $i }}][item]" class="form-control">
                                                        <option value="">-- Pilih Item BHP --</option>
                                                        @foreach($consumables as $c)
                                                            <option value="{{ $c['_id'] }}" {{ $usage['item'] == $c['_id'] ? 'selected' : '' }}>
                                                                {{ $c['name'] }} (stok: {{ $c['currentStock'] }} {{ $c['unit'] }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group input-group-outline is-filled">
                                                    <label class="form-label">Jumlah Digunakan</label>
                                                    <input type="number" name="consumablesUsed[{{ $i }}][quantityUsed]" class="form-control" min="0" step="0.01" value="{{ $usage['quantityUsed'] }}">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-link text-danger p-0 remove-bhp-row">
                                                    <i class="material-icons">delete</i>
                                                </button>
                                            </div>
                                        </div>
                                        @endforeach
                                    @endif
                                </div>

                                <p class="text-xs text-secondary mt-2">Kosongkan bagian ini jika tidak ada BHP yang digunakan.</p>

                                <div class="d-flex justify-content-end mt-4">
                                    <a href="{{ route('staf-lab.maintenance.index') }}" class="btn btn-outline-secondary me-2">Batal</a>
                                    <button type="submit" class="btn bg-gradient-primary">Simpan Log</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <x-footers.auth></x-footers.auth>
        </div>
    </main>

    @push('js')
    <script>
        const consumables = @json($consumables);
        let bhpIndex = {{ old('consumablesUsed') ? count(old('consumablesUsed')) : 0 }};

        function buildBhpRow(index) {
            const options = consumables.map(c =>
                `<option value="${c._id}">${c.name} (stok: ${c.currentStock} ${c.unit})</option>`
            ).join('');

            return `
                <div class="row bhp-row align-items-center mb-2">
                    <div class="col-md-7">
                        <div class="input-group input-group-static">
                            <select name="consumablesUsed[${index}][item]" class="form-control">
                                <option value="">-- Pilih Item BHP --</option>
                                ${options}
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-outline is-filled">
                            <label class="form-label">Jumlah Digunakan</label>
                            <input type="number" name="consumablesUsed[${index}][quantityUsed]" class="form-control" min="0" step="0.01" value="0">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-link text-danger p-0 remove-bhp-row">
                            <i class="material-icons">delete</i>
                        </button>
                    </div>
                </div>`;
        }

        document.getElementById('addBhpRow').addEventListener('click', function () {
            document.getElementById('bhpContainer').insertAdjacentHTML('beforeend', buildBhpRow(bhpIndex++));
        });

        document.getElementById('bhpContainer').addEventListener('click', function (e) {
            if (e.target.closest('.remove-bhp-row')) {
                e.target.closest('.bhp-row').remove();
            }
        });
    </script>
    @endpush
</x-layout>
