<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage='global-rooms'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Daftar Ruangan"></x-navbars.navs.auth>
        <div class="container-fluid py-4">

            {{-- Statistik ringkas --}}
            <div class="row mb-3">
                @php
                    $totalRooms = count($rooms);
                @endphp
                <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10 text-white">meeting_room</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Total Ruangan Aktif</p>
                                <h4 class="mb-0">{{ $totalRooms }}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <p class="mb-0 text-xs">Semua ruangan yang terdaftar dan aktif</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Daftar Ruangan --}}
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-3">
                                <h6 class="text-white text-capitalize ps-3 mb-0">Daftar Ruangan Laboratorium</h6>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="input-group input-group-outline" style="max-width: 220px;">
                                        <input type="text" id="searchRoom" class="form-control form-control-sm text-white" placeholder="Cari ruangan..." style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.3); color: white;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Ruangan</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kode</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Lokasi</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kapasitas</th>
                                            <th class="text-center text-secondary opacity-7">Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($rooms as $room)
                                        <tr class="room-row">
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="icon icon-sm icon-shape bg-gradient-primary shadow text-center border-radius-md me-2 d-flex align-items-center justify-content-center">
                                                        <i class="material-icons opacity-10 text-white" style="font-size: 16px;">meeting_room</i>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm room-name">{{ $room['name'] }}</h6>
                                                        @if($room['description'] ?? false)
                                                            <p class="text-xs text-secondary mb-0">{{ Str::limit($room['description'], 50) }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-gradient-dark">{{ $room['code'] ?? '-' }}</span>
                                            </td>
                                            <td>
                                                <span class="text-secondary text-xs">{{ $room['location'] ?? '-' }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="text-secondary text-xs">{{ $room['capacity'] ?? '-' }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <a href="{{ route('global.rooms.show', $room['_id']) }}"
                                                   class="btn btn-link text-info text-xs p-0 mb-0"
                                                   title="Lihat Aset & BHP di Ruangan Ini">
                                                    <i class="material-icons text-sm">visibility</i> Lihat Aset & BHP
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5">
                                                <i class="material-icons text-secondary" style="font-size: 48px;">meeting_room</i>
                                                <p class="text-secondary text-sm mb-0 mt-2">Belum ada ruangan terdaftar.</p>
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

    @push('js')
    <script>
        document.getElementById('searchRoom').addEventListener('input', function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.room-row').forEach(function(row) {
                const name = row.querySelector('.room-name')?.textContent.toLowerCase() ?? '';
                row.style.display = name.includes(query) ? '' : 'none';
            });
        });
    </script>
    @endpush
</x-layout>
