<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage='scanner'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Barcode & QR Scanner"></x-navbars.navs.auth>
        <div class="container-fluid py-4">

            <div class="row">
                <div class="col-lg-6 col-12 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10 text-white">document_scanner</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Pemindai</p>
                                <h5 class="mb-0">Arahkan Kamera ke Barcode/QR</h5>
                            </div>
                        </div>
                        <div class="card-body px-3 pb-2 text-center">
                            <div id="reader" style="width: 100%; border-radius: 8px; overflow: hidden; border: 2px dashed #ccc;"></div>

                            <div class="mt-4 px-3">
                                <p class="text-xs text-secondary mb-2">Atau masukkan kode secara manual jika sulit ter-scan:</p>
                                <div class="input-group input-group-outline mb-2">
                                    <label class="form-label">Ketik Kode Barcode...</label>
                                    <input type="text" class="form-control" id="manual-code-input">
                                </div>
                                <button type="button" class="btn bg-gradient-info w-100" id="btn-manual-search">Cari Manual</button>
                            </div>

                            <p class="text-xs text-secondary mt-3 mb-1">
                                * Pastikan Anda memberikan izin akses kamera pada browser.
                            </p>
                            <p class="text-xs text-secondary mb-0">
                                * Fitur ini berfungsi optimal di koneksi HTTPS (jika dari HP).
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-12">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10 text-white">analytics</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Hasil Pencarian</p>
                                <h5 class="mb-0">Detail Barang</h5>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div id="scan-waiting" class="text-center py-5">
                                <i class="material-icons text-secondary" style="font-size: 48px;">qr_code_scanner</i>
                                <p class="text-secondary text-sm mb-0 mt-2">Menunggu hasil scan...</p>
                            </div>

                            <div id="scan-loading" class="text-center py-5 d-none">
                                <div class="spinner-border text-info" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="text-secondary text-sm mb-0 mt-2">Mencari data di server...</p>
                            </div>

                            <div id="scan-result" class="d-none">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon icon-md icon-shape bg-gradient-primary shadow text-center border-radius-md me-3">
                                        <i class="material-icons opacity-10 text-white">devices</i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0" id="res-name">Nama Barang</h5>
                                        <span class="badge bg-gradient-dark" id="res-code">KODE-ASET</span>
                                    </div>
                                </div>
                                <hr class="horizontal dark mt-2 mb-3">

                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span class="text-sm text-secondary">Kategori:</span>
                                        <span class="text-dark font-weight-bold" id="res-category">-</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span class="text-sm text-secondary">Ruangan:</span>
                                        <span class="text-dark font-weight-bold" id="res-room">-</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span class="text-sm text-secondary">Kondisi:</span>
                                        <span class="badge" id="res-condition">-</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span class="text-sm text-secondary">Status:</span>
                                        <span class="badge" id="res-status">-</span>
                                    </li>
                                </ul>

                                <div class="mt-4 text-center" id="action-buttons">
                                    @if($rolePrefix === 'staf-admin')
                                        <a href="{{ route('staf-admin.assets.index') }}" class="btn btn-outline-info w-100">Lihat di Daftar Aset</a>
                                    @else
                                        <a href="{{ route('dashboard') }}" class="btn btn-outline-info w-100">Kembali ke Dashboard</a>
                                    @endif
                                </div>
                            </div>

                            <div id="scan-not-found" class="text-center py-5 d-none">
                                <i class="material-icons text-danger" style="font-size: 48px;">error_outline</i>
                                <h6 class="text-danger mt-2">Barang Tidak Ditemukan</h6>
                                <p class="text-sm text-secondary mb-3">Barcode <strong id="not-found-code"></strong> belum terdaftar di sistem inventaris.</p>

                                <button type="button" class="btn bg-gradient-primary w-100" data-bs-toggle="modal" data-bs-target="#addAssetModal">
                                    <i class="material-icons me-1">add</i> Tambahkan sebagai Barang Baru
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <x-footers.auth></x-footers.auth>
        </div>
    </main>

    <style>
        #reader {
            border: 2px dashed #ccc !important;
            border-radius: 8px;
            overflow: hidden;
            padding: 15px;
            background-color: #f8f9fa;
        }
        #reader button {
            background-color: #e91e63 !important; 
            color: #fff !important;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            margin: 10px 5px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        #reader button:hover {
            background-color: #d81b60 !important;
            box-shadow: 0 6px 8px rgba(0,0,0,0.15);
        }
        #reader select {
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid #ced4da;
            margin: 10px 0;
            font-size: 14px;
            max-width: 100%;
        }

        #reader a {
            display: none !important;
        }
    </style>

    <div class="modal fade" id="addAssetModal" tabindex="-1" role="dialog" aria-labelledby="addAssetModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAssetModalLabel">Tambah Barang Baru</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addAssetForm">
                        <div class="input-group input-group-outline is-filled mb-3">
                            <label class="form-label">Kode Aset / Barcode</label>
                            <input type="text" class="form-control" id="input-assetCode" required readonly style="background-color: #f8f9fa;">
                        </div>
                        <div class="input-group input-group-outline mb-3">
                            <label class="form-label">Nama Barang (wajib)</label>
                            <input type="text" class="form-control" id="input-name" required>
                        </div>
                        <div class="mb-3">
                            <label class="text-sm text-secondary">Kategori</label>
                            <select class="form-control border px-2" id="input-category" style="background-color: white;">
                                <option value="Elektronik">Elektronik</option>
                                <option value="Furniture">Furniture</option>
                                <option value="Alat Lab">Alat Lab</option>
                                <option value="Buku / Modul">Buku / Modul</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="text-sm text-secondary">Ruangan / Lokasi</label>
                            <select class="form-control border px-2" id="input-room" style="background-color: white;">
                                <option value="">Loading ruangan...</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <label class="text-sm text-secondary">Kondisi</label>
                                <select class="form-control border px-2" id="input-condition">
                                    <option value="baik">Baik</option>
                                    <option value="rusak_ringan">Rusak Ringan</option>
                                    <option value="rusak_berat">Rusak Berat</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="text-sm text-secondary">Status</label>
                                <select class="form-control border px-2" id="input-status">
                                    <option value="aktif">Aktif</option>
                                    <option value="dalam_pemeliharaan">Pemeliharaan</option>
                                    <option value="tidak_aktif">Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn bg-gradient-primary" id="btnSaveAsset">Simpan Barang</button>
                </div>
            </div>
        </div>
    </div>

    @push('js')

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        const API_BASE_URL = "{{ env('API_BASE_URL', 'http://localhost:3000') }}";
        const API_TOKEN = "{{ $apiToken }}";
        const ROLE_PREFIX = "{{ $rolePrefix }}";

        let scanner = null;
        let isScanning = false;
        let lastScannedCode = null;

        const stateWaiting = document.getElementById('scan-waiting');
        const stateLoading = document.getElementById('scan-loading');
        const stateResult = document.getElementById('scan-result');
        const stateNotFound = document.getElementById('scan-not-found');

        function switchState(state) {
            stateWaiting.classList.add('d-none');
            stateLoading.classList.add('d-none');
            stateResult.classList.add('d-none');
            stateNotFound.classList.add('d-none');

            if(state === 'waiting') stateWaiting.classList.remove('d-none');
            if(state === 'loading') stateLoading.classList.remove('d-none');
            if(state === 'result') stateResult.classList.remove('d-none');
            if(state === 'notfound') stateNotFound.classList.remove('d-none');
        }

        function getConditionBadge(condition) {
            const map = {
                'baik': { label: 'Baik', color: 'bg-gradient-success' },
                'rusak_ringan': { label: 'Rusak Ringan', color: 'bg-gradient-warning' },
                'rusak_berat': { label: 'Rusak Berat', color: 'bg-gradient-danger' },
                'tidak_aktif': { label: 'Tidak Aktif', color: 'bg-gradient-secondary' }
            };
            const mapped = map[condition] || map['baik'];
            return `<span class="badge ${mapped.color}">${mapped.label}</span>`;
        }

        function getStatusBadge(status) {
            const map = {
                'aktif': { label: 'Aktif', color: 'bg-gradient-success' },
                'dalam_pemeliharaan': { label: 'Pemeliharaan', color: 'bg-gradient-warning' },
                'dihapus': { label: 'Dihapus', color: 'bg-gradient-danger' },
                'diganti': { label: 'Diganti', color: 'bg-gradient-secondary' }
            };
            const mapped = map[status] || map['aktif'];
            return `<span class="badge ${mapped.color}">${mapped.label}</span>`;
        }

        function onScanSuccess(decodedText, decodedResult) {

            const validCodeRegex = /^[A-Za-z0-9\-_]+$/;

            if (!validCodeRegex.test(decodedText) || decodedText.length > 50) {

                if (!isScanning) {
                    isScanning = true;
                    if (scanner) scanner.pause(true);

                    alert("Format Barcode tidak valid! Pastikan Anda men-scan kode aset yang benar (bukan berupa Link URL/Website).");

                    setTimeout(() => {
                        isScanning = false;
                        if (scanner) scanner.resume();
                    }, 2500);
                }
                return;
            }

            if(isScanning && lastScannedCode === decodedText) return;

            lastScannedCode = decodedText;
            isScanning = true;

            if (scanner) {
                scanner.pause(true);
            }

            switchState('loading');
            console.log(`Scan result: ${decodedText}`);

            fetch(`${API_BASE_URL}/api/${ROLE_PREFIX}/assets/scan/${encodeURIComponent(decodedText)}`, {
                headers: {
                    'Authorization': `Bearer ${API_TOKEN}`,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(res => {
                if(res.success && res.data) {

                    const asset = res.data;
                    document.getElementById('res-name').innerText = asset.name || '-';
                    document.getElementById('res-code').innerText = asset.assetCode || asset.qrCode || '-';
                    document.getElementById('res-category').innerText = asset.category || '-';
                    document.getElementById('res-room').innerText = (asset.room && asset.room.name) ? asset.room.name : '-';

                    document.getElementById('res-condition').outerHTML = `<span id="res-condition">${getConditionBadge(asset.condition)}</span>`;
                    document.getElementById('res-status').outerHTML = `<span id="res-status">${getStatusBadge(asset.status)}</span>`;

                    switchState('result');
                } else {

                    document.getElementById('not-found-code').innerText = decodedText;
                    document.getElementById('input-assetCode').value = decodedText; // Pre-fill form
                    switchState('notfound');
                }
            })
            .catch(err => {
                console.error("Error fetching scan result", err);
                alert("Terjadi kesalahan koneksi saat mencari data barang.");
                switchState('waiting');
            })
            .finally(() => {

                setTimeout(() => {
                    isScanning = false;
                    if (scanner) scanner.resume();
                }, 2000);
            });
        }

        function onScanFailure(error) {

        }

        document.addEventListener("DOMContentLoaded", function() {

            scanner = new Html5QrcodeScanner(
                "reader",
                { 
                    fps: 10, 
                    qrbox: {width: 300, height: 150}, 
                    aspectRatio: 1.0,

                },
                 false
            );
            scanner.render(onScanSuccess, onScanFailure);

            document.getElementById('btn-manual-search').addEventListener('click', function() {
                const manualCode = document.getElementById('manual-code-input').value.trim();
                if(manualCode) {
                    onScanSuccess(manualCode, null);
                } else {
                    alert('Harap masukkan kode barcode terlebih dahulu.');
                }
            });

            fetch(`${API_BASE_URL}/api/${ROLE_PREFIX}/assets`, {
                headers: { 'Authorization': `Bearer ${API_TOKEN}` }
            })
            .then(r => r.json())
            .then(res => {
                if(res.success && res.data) {

                    let rooms = {};
                    res.data.forEach(a => {
                        if(a.room && a.room._id) {
                            rooms[a.room._id] = a.room.name;
                        }
                    });

                    const select = document.getElementById('input-room');
                    select.innerHTML = '<option value="">(Pilih Ruangan)</option>';
                    for (const [id, name] of Object.entries(rooms)) {
                        select.innerHTML += `<option value="${id}">${name}</option>`;
                    }
                }
            }).catch(e => console.log('Error fetching rooms for dropdown', e));

            document.getElementById('btnSaveAsset').addEventListener('click', function() {
                const payload = {
                    assetCode: document.getElementById('input-assetCode').value,
                    name: document.getElementById('input-name').value,
                    category: document.getElementById('input-category').value,
                    condition: document.getElementById('input-condition').value,
                    status: document.getElementById('input-status').value,
                    itemType: 'devices'
                };

                const roomId = document.getElementById('input-room').value;
                if(roomId) payload.room = roomId;

                if(!payload.name) {
                    alert('Nama barang wajib diisi!');
                    return;
                }

                const btn = this;
                const originalText = btn.innerHTML;
                btn.innerHTML = 'Menyimpan...';
                btn.disabled = true;

                fetch(`${API_BASE_URL}/api/${ROLE_PREFIX}/assets`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${API_TOKEN}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                .then(r => r.json())
                .then(res => {
                    if(res.success) {
                        alert('Barang baru berhasil ditambahkan!');

                        const modal = bootstrap.Modal.getInstance(document.getElementById('addAssetModal'));
                        if(modal) modal.hide();

                        document.getElementById('addAssetForm').reset();

                        onScanSuccess(payload.assetCode, null);
                    } else {
                        alert('Gagal menyimpan: ' + (res.message || 'Error'));
                    }
                })
                .catch(err => {
                    alert('Terjadi kesalahan server.');
                })
                .finally(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
            });
        });
    </script>
    @endpush
</x-layout>
