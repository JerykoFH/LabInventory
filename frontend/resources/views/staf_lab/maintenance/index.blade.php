<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage='maintenance'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Log Pemeliharaan"></x-navbars.navs.auth>
        <div class="container-fluid py-4">

            @if(session('success'))
            <div class="alert alert-success alert-dismissible text-white fade show" role="alert">
                <span class="text-sm">{{ session('success') }}</span>
                <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert"></button>
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible text-white fade show" role="alert">
                <span class="text-sm">{{ session('error') }}</span>
                <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-3">
                                <h6 class="text-white text-capitalize ps-3 mb-0">Riwayat Pemeliharaan Aset</h6>
                                <a href="{{ route('staf-lab.maintenance.create') }}" class="btn btn-sm btn-white mb-0">
                                    <i class="material-icons text-sm me-1">add</i> Catat Pemeliharaan
                                </a>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aset</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Jenis</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Dilakukan Oleh</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kondisi Sesudah</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
                                            <th class="text-secondary opacity-7">Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($logs as $log)
                                        @php
                                            $typeColor = match($log['type'] ?? 'rutin') {
                                                'rutin'      => 'bg-gradient-info',
                                                'perbaikan'  => 'bg-gradient-warning',
                                                'pengecekan' => 'bg-gradient-secondary',
                                                default      => 'bg-gradient-secondary'
                                            };
                                            $typeLabel = match($log['type'] ?? 'rutin') {
                                                'rutin'      => 'Rutin',
                                                'perbaikan'  => 'Perbaikan',
                                                'pengecekan' => 'Pengecekan',
                                                default      => $log['type']
                                            };
                                            $condColor = match($log['conditionAfter'] ?? '') {
                                                'baik'        => 'bg-gradient-success',
                                                'rusak_ringan'=> 'bg-gradient-warning',
                                                'rusak_berat' => 'bg-gradient-danger',
                                                'tidak_aktif' => 'bg-gradient-secondary',
                                                default       => 'bg-gradient-secondary'
                                            };
                                            $condLabel = match($log['conditionAfter'] ?? '') {
                                                'baik'        => 'Baik',
                                                'rusak_ringan'=> 'Rusak Ringan',
                                                'rusak_berat' => 'Rusak Berat',
                                                'tidak_aktif' => 'Tidak Aktif',
                                                default       => '-'
                                            };
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="icon icon-sm icon-shape {{ $typeColor }} shadow text-center border-radius-md me-2 d-flex align-items-center justify-content-center">
                                                        <i class="material-icons opacity-10 text-white" style="font-size: 16px;">build</i>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $log['asset']['name'] ?? 'Aset tidak ditemukan' }}</h6>
                                                        @if($log['asset']['assetCode'] ?? false)
                                                            <p class="text-xs text-secondary mb-0">{{ $log['asset']['assetCode'] }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-sm {{ $typeColor }}">{{ $typeLabel }}</span>
                                            </td>
                                            <td>
                                                <span class="text-secondary text-xs">{{ $log['performedBy']['name'] ?? '-' }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="badge badge-sm {{ $condColor }}">{{ $condLabel }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="text-secondary text-xs font-weight-bold">
                                                    {{ isset($log['maintenanceDate']) ? \Carbon\Carbon::parse($log['maintenanceDate'])->format('d M Y') : '-' }}
                                                </span>
                                            </td>
                                            <td class="align-middle">
                                                <a href="{{ route('staf-lab.maintenance.show', $log['_id']) }}" class="text-info font-weight-bold text-xs" title="Lihat Detail">
                                                    <i class="material-icons text-sm">visibility</i>
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <i class="material-icons text-secondary" style="font-size: 48px;">build_circle</i>
                                                <p class="text-secondary text-sm mb-0 mt-2">Belum ada log pemeliharaan.</p>
                                                <a href="{{ route('staf-lab.maintenance.create') }}" class="btn bg-gradient-info btn-sm mt-3">
                                                    <i class="material-icons text-sm me-1">add</i> Catat Pemeliharaan
                                                </a>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <x-footers.auth></x-footers.auth>
        </div>
    </main>
</x-layout>
