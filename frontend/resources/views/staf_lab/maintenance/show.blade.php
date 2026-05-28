<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage='maintenance'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Detail Pemeliharaan"></x-navbars.navs.auth>
        <div class="container-fluid py-4">

            @if($log)
            @php
                $typeLabel = match($log['type'] ?? 'rutin') {
                    'rutin'      => 'Rutin',
                    'perbaikan'  => 'Perbaikan',
                    'pengecekan' => 'Pengecekan',
                    default      => $log['type']
                };
                $typeColor = match($log['type'] ?? 'rutin') {
                    'rutin'      => 'bg-gradient-info',
                    'perbaikan'  => 'bg-gradient-warning',
                    'pengecekan' => 'bg-gradient-secondary',
                    default      => 'bg-gradient-secondary'
                };
                $condAfterLabel = match($log['conditionAfter'] ?? '') {
                    'baik'        => 'Baik',
                    'rusak_ringan'=> 'Rusak Ringan',
                    'rusak_berat' => 'Rusak Berat',
                    'tidak_aktif' => 'Tidak Aktif',
                    default       => '-'
                };
                $condAfterColor = match($log['conditionAfter'] ?? '') {
                    'baik'        => 'bg-gradient-success',
                    'rusak_ringan'=> 'bg-gradient-warning',
                    'rusak_berat' => 'bg-gradient-danger',
                    'tidak_aktif' => 'bg-gradient-secondary',
                    default       => 'bg-gradient-secondary'
                };
                $condBeforeLabel = match($log['conditionBefore'] ?? '') {
                    'baik'        => 'Baik',
                    'rusak_ringan'=> 'Rusak Ringan',
                    'rusak_berat' => 'Rusak Berat',
                    default       => '-'
                };
            @endphp

            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="{{ $typeColor }} shadow border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-3">
                                <div>
                                    <h6 class="text-white text-capitalize ps-3 mb-0">
                                        {{ $log['asset']['name'] ?? 'Aset tidak diketahui' }}
                                    </h6>
                                    <p class="text-white text-xs ps-3 mb-0 opacity-8">
                                        {{ isset($log['maintenanceDate']) ? \Carbon\Carbon::parse($log['maintenanceDate'])->format('d M Y') : '-' }}
                                        · Oleh {{ $log['performedBy']['name'] ?? '-' }}
                                    </p>
                                </div>
                                <span class="badge bg-white text-dark px-3 py-2">{{ $typeLabel }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="text-uppercase text-secondary text-xxs font-weight-bolder">Kode Aset</label>
                                    <p class="text-sm font-weight-bold mb-0">{{ $log['asset']['assetCode'] ?? '-' }}</p>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="text-uppercase text-secondary text-xxs font-weight-bolder">Kondisi Sebelum</label>
                                    <p class="mb-0"><span class="badge bg-gradient-secondary">{{ $condBeforeLabel }}</span></p>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="text-uppercase text-secondary text-xxs font-weight-bolder">Kondisi Sesudah</label>
                                    <p class="mb-0"><span class="badge {{ $condAfterColor }}">{{ $condAfterLabel }}</span></p>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="text-uppercase text-secondary text-xxs font-weight-bolder">Jenis</label>
                                    <p class="mb-0"><span class="badge {{ $typeColor }}">{{ $typeLabel }}</span></p>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="text-uppercase text-secondary text-xxs font-weight-bolder">Deskripsi Pekerjaan</label>
                                    <p class="text-sm mb-0 bg-gray-100 p-2 border-radius-md">{{ $log['description'] }}</p>
                                </div>
                                @if($log['notes'] ?? false)
                                <div class="col-12 mb-3">
                                    <label class="text-uppercase text-secondary text-xxs font-weight-bolder">Catatan</label>
                                    <p class="text-sm mb-0">{{ $log['notes'] }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(count($log['consumablesUsed'] ?? []) > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0">
                            <h6>BHP yang Digunakan ({{ count($log['consumablesUsed']) }})</h6>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Item</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Jumlah Digunakan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($log['consumablesUsed'] as $usage)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="icon icon-sm icon-shape bg-gradient-info shadow text-center border-radius-md me-2 d-flex align-items-center justify-content-center">
                                                        <i class="material-icons opacity-10 text-white" style="font-size: 16px;">science</i>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $usage['item']['name'] ?? 'Item tidak diketahui' }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="text-secondary text-sm font-weight-bold">
                                                    {{ $usage['quantityUsed'] }} {{ $usage['item']['unit'] ?? '' }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="d-flex justify-content-start mb-4">
                <a href="{{ route('staf-lab.maintenance.index') }}" class="btn btn-outline-secondary">
                    <i class="material-icons text-sm me-1">arrow_back</i> Kembali
                </a>
            </div>

            @else
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-body text-center py-5">
                            <i class="material-icons text-secondary" style="font-size: 64px;">search_off</i>
                            <p class="text-secondary mt-3">Log pemeliharaan tidak ditemukan.</p>
                            <a href="{{ route('staf-lab.maintenance.index') }}" class="btn bg-gradient-primary">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <x-footers.auth></x-footers.auth>
        </div>
    </main>
</x-layout>
