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

                            <form action="{{ route('staf-lab.maintenance.store') }}" method="POST" id="maintenanceForm" enctype="multipart/form-data">
                                @csrf

                                <h6 class="text-uppercase text-secondary text-xs font-weight-bolder mt-3 mb-3">Informasi Pemeliharaan</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group input-group-static my-3">
                                            <label class="ms-0">Ruangan yang Dimaintain <span class="text-danger">*</span></label>
                                            <select name="room" class="form-control" id="roomSelect" required>
                                                <option value="">-- Pilih Ruangan --</option>
                                                @foreach($rooms as $room)
                                                    <option value="{{ $room['_id'] }}" {{ old('room') == $room['_id'] ? 'selected' : '' }}>
                                                        {{ $room['name'] }}{{ $room['code'] ? ' (' . $room['code'] . ')' : '' }}
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
                                </div>

                                <hr class="horizontal dark my-4">

                                <h6 class="text-uppercase text-secondary text-xs font-weight-bolder mb-3">Aset yang Dipelihara & Kondisi</h6>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <small class="text-secondary">Tambahkan aset-aset yang akan dipelihara (opsional)</small>
                                    <button type="button" class="btn btn-sm bg-gradient-info mb-0" id="addAssetRow">
                                        <i class="material-icons text-sm me-1">add</i> Tambah Aset
                                    </button>
                                </div>

                                <div id="assetsContainer">
                                    @if(old('assets') && is_array(old('assets')))
                                        @foreach(old('assets') as $i => $assetData)
                                        <div class="card card-body border card-plain border-radius-lg mb-3 asset-row">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0 text-sm">Detail Aset</h6>
                                                <button type="button" class="btn btn-link text-danger p-0 m-0 remove-asset-row">
                                                    <i class="material-icons">delete</i>
                                                </button>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <div class="input-group input-group-static">
                                                        <!-- Aset option dipopulate via JS, tapi kita akan set data ID-nya ke value -->
                                                        <select name="assets[{{ $i }}][asset]" class="form-control asset-select" data-selected="{{ $assetData['asset'] ?? '' }}" required>
                                                            <option value="{{ $assetData['asset'] ?? '' }}">{{ $assetData['asset'] ?? '-- Aset Terpilih --' }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <div class="input-group input-group-static">
                                                        <label class="ms-0">Kondisi Sebelum</label>
                                                        <select name="assets[{{ $i }}][conditionBefore]" class="form-control">
                                                            <option value="">-- Tidak dicatat --</option>
                                                            <option value="baik" {{ ($assetData['conditionBefore'] ?? '') == 'baik' ? 'selected' : '' }}>Baik</option>
                                                            <option value="rusak_ringan" {{ ($assetData['conditionBefore'] ?? '') == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                                            <option value="rusak_berat" {{ ($assetData['conditionBefore'] ?? '') == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <div class="input-group input-group-static">
                                                        <label class="ms-0">Kondisi Sesudah</label>
                                                        <select name="assets[{{ $i }}][conditionAfter]" class="form-control">
                                                            <option value="">-- Tidak dicatat --</option>
                                                            <option value="baik" {{ ($assetData['conditionAfter'] ?? '') == 'baik' ? 'selected' : '' }}>Baik</option>
                                                            <option value="rusak_ringan" {{ ($assetData['conditionAfter'] ?? '') == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                                            <option value="rusak_berat" {{ ($assetData['conditionAfter'] ?? '') == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                                                            <option value="tidak_aktif" {{ ($assetData['conditionAfter'] ?? '') == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <div class="input-group input-group-static">
                                                        <label class="ms-0">Foto Kondisi Sebelum</label>
                                                        <input type="file" name="assets[{{ $i }}][photoBefore]" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <div class="input-group input-group-static">
                                                        <label class="ms-0">Foto Kondisi Sesudah</label>
                                                        <input type="file" name="assets[{{ $i }}][photoAfter]" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    @endif
                                </div>

                                <hr class="horizontal dark my-4">

                                <h6 class="text-uppercase text-secondary text-xs font-weight-bolder mt-3 mb-3">Dokumentasi Pekerjaan</h6>
                                <div class="row">
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
                                                    <input type="number" name="consumablesUsed[{{ $i }}][quantityUsed]" class="form-control" min="0" value="{{ $usage['quantityUsed'] }}">
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
        let assets = [];
        const consumables = @json($consumables ?? []);
        
        let bhpIndex = {{ old('consumablesUsed') ? count(old('consumablesUsed')) : 0 }};
        let assetIndex = {{ old('assets') && is_array(old('assets')) ? count(old('assets')) : 0 }};

        // Fetch assets when room changes
        document.getElementById('roomSelect').addEventListener('change', async function() {
            const roomId = this.value;
            assets = [];
            // Optional: clear container if room changes? 
            // document.getElementById('assetsContainer').innerHTML = ''; 
            
            if (roomId) {
                try {
                    const response = await fetch(`/staf-lab/rooms/${roomId}/assets`);
                    if (response.ok) {
                        assets = await response.json();
                        updateExistingAssetSelects();
                    }
                } catch (e) {
                    console.error("Failed to fetch assets", e);
                }
            }
        });

        // Initialize assets if room is already selected (e.g., from old input)
        const initialRoomId = document.getElementById('roomSelect').value;
        if (initialRoomId) {
            document.getElementById('roomSelect').dispatchEvent(new Event('change'));
        }

        function updateExistingAssetSelects() {
            const selects = document.querySelectorAll('.asset-select');
            selects.forEach(select => {
                const selectedValue = select.getAttribute('data-selected') || select.value;
                let options = '<option value="">-- Pilih Aset --</option>';
                assets.forEach(a => {
                    const isSelected = (a._id === selectedValue) ? 'selected' : '';
                    options += `<option value="${a._id}" ${isSelected}>${a.name}${a.assetCode ? ' (' + a.assetCode + ')' : ''}</option>`;
                });
                select.innerHTML = options;
            });
        }

        function buildAssetRow(index) {
            const options = assets.map(a =>
                `<option value="${a._id}">${a.name}${a.assetCode ? ' (' + a.assetCode + ')' : ''}</option>`
            ).join('');

            return `
                <div class="card card-body border card-plain border-radius-lg mb-3 asset-row">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 text-sm">Detail Aset</h6>
                        <button type="button" class="btn btn-link text-danger p-0 m-0 remove-asset-row">
                            <i class="material-icons">delete</i>
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="input-group input-group-static">
                                <select name="assets[${index}][asset]" class="form-control asset-select" required>
                                    <option value="">-- Pilih Aset --</option>
                                    ${options}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-static">
                                <label class="ms-0">Kondisi Sebelum</label>
                                <select name="assets[${index}][conditionBefore]" class="form-control">
                                    <option value="">-- Tidak dicatat --</option>
                                    <option value="baik">Baik</option>
                                    <option value="rusak_ringan">Rusak Ringan</option>
                                    <option value="rusak_berat">Rusak Berat</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-static">
                                <label class="ms-0">Kondisi Sesudah</label>
                                <select name="assets[${index}][conditionAfter]" class="form-control">
                                    <option value="">-- Tidak dicatat --</option>
                                    <option value="baik">Baik</option>
                                    <option value="rusak_ringan">Rusak Ringan</option>
                                    <option value="rusak_berat">Rusak Berat</option>
                                    <option value="tidak_aktif">Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="input-group input-group-static">
                                <label class="ms-0">Foto Kondisi Sebelum</label>
                                <input type="file" name="assets[${index}][photoBefore]" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="input-group input-group-static">
                                <label class="ms-0">Foto Kondisi Sesudah</label>
                                <input type="file" name="assets[${index}][photoAfter]" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                            </div>
                        </div>
                    </div>
                </div>`;
        }

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
                            <input type="number" name="consumablesUsed[${index}][quantityUsed]" class="form-control" min="0" value="0">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-link text-danger p-0 remove-bhp-row">
                            <i class="material-icons">delete</i>
                        </button>
                    </div>
                </div>`;
        }

        // Tambah aset row
        document.getElementById('addAssetRow').addEventListener('click', function () {
            if (!document.getElementById('roomSelect').value) {
                alert('Pilih ruangan terlebih dahulu untuk melihat aset.');
                return;
            }
            document.getElementById('assetsContainer').insertAdjacentHTML('beforeend', buildAssetRow(assetIndex++));
        });

        // Tambah BHP row
        document.getElementById('addBhpRow').addEventListener('click', function () {
            document.getElementById('bhpContainer').insertAdjacentHTML('beforeend', buildBhpRow(bhpIndex++));
        });

        // Hapus aset row
        document.getElementById('assetsContainer').addEventListener('click', function (e) {
            if (e.target.closest('.remove-asset-row')) {
                e.target.closest('.asset-row').remove();
            }
        });

        // Validasi duplikat aset
        document.getElementById('assetsContainer').addEventListener('change', function(e) {
            if (e.target.classList.contains('asset-select')) {
                const selectedValue = e.target.value;
                if (!selectedValue) return;

                const selects = document.querySelectorAll('.asset-select');
                let count = 0;
                selects.forEach(select => {
                    if (select.value === selectedValue) {
                        count++;
                    }
                });

                if (count > 1) {
                    alert('Aset ini sudah dipilih! Silakan pilih aset yang berbeda.');
                    e.target.value = ''; 
                }
            }
        });

        // Hapus BHP row
        document.getElementById('bhpContainer').addEventListener('click', function (e) {
            if (e.target.closest('.remove-bhp-row')) {
                e.target.closest('.bhp-row').remove();
            }
        });
    </script>
    @endpush
</x-layout>
