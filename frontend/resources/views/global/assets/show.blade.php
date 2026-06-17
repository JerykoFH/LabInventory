<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage='assets'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Detail Aset Inventaris"></x-navbars.navs.auth>
        <div class="container-fluid py-4">

            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10 text-white">devices</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Detail Barang</p>
                                <h4 class="mb-0">{{ $asset['name'] }}</h4>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6 class="mb-3">Informasi Umum</h6>
                                    <div class="table-responsive">
                                        <table class="table table-borderless table-sm">
                                            <tbody>
                                                <tr>
                                                    <th style="width: 30%" class="text-secondary text-sm">Kode Aset</th>
                                                    <td class="text-dark font-weight-bold text-sm">: {{ $asset['assetCode'] ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th class="text-secondary text-sm">Kategori</th>
                                                    <td class="text-dark text-sm">: {{ $asset['category'] ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th class="text-secondary text-sm">Ruangan</th>
                                                    <td class="text-dark text-sm">: {{ $asset['room']['name'] ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th class="text-secondary text-sm">Kondisi</th>
                                                    <td class="text-sm">: 
                                                        @php
                                                            $kondisiLabel = match($asset['condition'] ?? 'baik') {
                                                                'baik' => 'Baik',
                                                                'rusak_ringan' => 'Rusak Ringan',
                                                                'rusak_berat' => 'Rusak Berat',
                                                                'tidak_aktif' => 'Tidak Aktif',
                                                                default => $asset['condition']
                                                            };
                                                        @endphp
                                                        <span class="badge bg-gradient-{{ $asset['condition'] == 'baik' ? 'success' : ($asset['condition'] == 'rusak_ringan' ? 'warning' : 'danger') }}">{{ $kondisiLabel }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="text-secondary text-sm">Status</th>
                                                    <td class="text-sm">: 
                                                        @php
                                                            $statusLabel = match($asset['status'] ?? 'aktif') {
                                                                'aktif' => 'Aktif',
                                                                'dalam_pemeliharaan' => 'Pemeliharaan',
                                                                'dihapus' => 'Dihapus',
                                                                'diganti' => 'Diganti',
                                                                default => $asset['status']
                                                            };
                                                        @endphp
                                                        <span class="badge bg-gradient-{{ $asset['status'] == 'aktif' ? 'success' : ($asset['status'] == 'dalam_pemeliharaan' ? 'warning' : 'secondary') }}">{{ $statusLabel }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="text-secondary text-sm">Tgl. Diterima</th>
                                                    <td class="text-dark text-sm">: {{ isset($asset['receivedDate']) ? \Carbon\Carbon::parse($asset['receivedDate'])->format('d M Y') : '-' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <hr class="my-4">
                                    <h6 class="mb-3">Deskripsi / Spesifikasi Lengkap</h6>
                                    <div class="p-3 bg-gray-100 border-radius-lg text-sm text-dark">
                                        {!! nl2br(e($asset['notes'] ?? 'Tidak ada deskripsi tersedia.')) !!}
                                    </div>
                                </div>
                                <div class="col-md-4 text-center mt-4 mt-md-0">
                                    <h6 class="mb-3">QR Code Scanner Aplikasi</h6>
                                    @if($asset['assetCode'])
                                        <div class="p-3 bg-white border-radius-lg border text-center d-inline-block">
                                            <div id="qrcode-container"></div>
                                            <p class="text-xs text-secondary mt-2 mb-0">Kode: {{ $asset['assetCode'] }}</p>
                                        </div>
                                        <p class="text-xs text-secondary mt-3">Scan kode ini melalui menu <b>Scanner</b> di dalam aplikasi untuk melihat halaman ini.</p>
                                    @else
                                        <div class="p-4 bg-gray-100 border-radius-lg text-center">
                                            <i class="material-icons text-secondary mb-2" style="font-size: 36px">qr_code_scanner</i>
                                            <p class="text-sm text-secondary mb-0">Aset ini belum memiliki Kode Aset / QR Code.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-footer p-3 text-end border-top">
                            <button type="button" class="btn bg-gradient-secondary mb-0" onclick="history.back()">Kembali</button>
                        </div>
                    </div>
                </div>
            </div>

            <x-footers.auth></x-footers.auth>
        </div>
    </main>

    @push('js')
    @if($asset['assetCode'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var qrcode = new QRCode(document.getElementById("qrcode-container"), {
                text: "{{ $asset['assetCode'] }}",
                width: 150,
                height: 150,
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });
        });
    </script>
    @endif
    @endpush
</x-layout>
