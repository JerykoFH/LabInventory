@props(['activePage'])

@php
    $role = session('api_user')['role'] ?? '';
@endphp

<aside
    class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-dark"
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0 d-flex text-wrap align-items-center" href="{{ url('/dashboard') }}">
            <img src="{{ asset('assets') }}/img/logo-ct.png" class="navbar-brand-img h-100" alt="main_logo">
            <span class="ms-2 font-weight-bold text-white">LabInventory</span>
        </a>
    </div>
    <hr class="horizontal light mt-0 mb-2">
    <div class="collapse navbar-collapse w-auto max-height-vh-100" id="sidenav-collapse-main">
        <ul class="navbar-nav">

            {{-- Dashboard --}}
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'dashboard' ? 'active bg-gradient-primary' : '' }}"
                    href="{{ url('/dashboard') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">dashboard</i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>

            {{-- ══════ ADMIN ══════ --}}
            @if($role === 'admin')
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Administrator</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'users' ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('admin.users.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">people</i>
                    </div>
                    <span class="nav-link-text ms-1">Kelola User</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'rooms' ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('admin.rooms.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">meeting_room</i>
                    </div>
                    <span class="nav-link-text ms-1">Kelola Ruangan</span>
                </a>
            </li>
            @endif

            {{-- ══════ KEPALA LAB ══════ --}}
            @if($role === 'kepala_lab')
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Kepala Lab</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'procurements' ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('kepala-lab.procurements.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">description</i>
                    </div>
                    <span class="nav-link-text ms-1">Draf Pengadaan</span>
                </a>
            </li>
            @endif

            {{-- ══════ KAPRODI ══════ --}}
            @if($role === 'kaprodi')
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Kaprodi</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'procurements' ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('kaprodi.procurements.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">fact_check</i>
                    </div>
                    <span class="nav-link-text ms-1">Review Pengadaan</span>
                </a>
            </li>
            @endif

            {{-- ══════ STAF ADMIN ══════ --}}
            @if($role === 'staf_admin')
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Staf Administrasi</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'procurements' ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('staf-admin.procurements.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">local_shipping</i>
                    </div>
                    <span class="nav-link-text ms-1">Penerimaan Barang</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'assets' ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('staf-admin.assets.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">inventory_2</i>
                    </div>
                    <span class="nav-link-text ms-1">Aset Inventaris</span>
                </a>
            </li>
            @endif

            {{-- ══════ STAF LAB ══════ --}}
            @if($role === 'staf_lab')
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Staf Laboratorium</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'consumables' ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('staf-lab.consumables.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">science</i>
                    </div>
                    <span class="nav-link-text ms-1">Barang Habis Pakai</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'maintenance' ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('staf-lab.maintenance.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">build</i>
                    </div>
                    <span class="nav-link-text ms-1">Pemeliharaan</span>
                </a>
            </li>
            @endif

        </ul>
    </div>

    {{-- Logout button --}}
    <div class="sidenav-footer position-absolute w-100 bottom-0">
        <div class="mx-3 mb-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn bg-gradient-primary w-100">
                    <i class="material-icons me-1">logout</i> Logout
                </button>
            </form>
        </div>
    </div>
</aside>
