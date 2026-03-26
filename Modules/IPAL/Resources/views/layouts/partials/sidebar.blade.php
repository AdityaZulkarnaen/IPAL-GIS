@php
    $isDashboard = request()->routeIs('ipal.dashboard');
    $isDataJaringan = request()->routeIs('ipal.upload.*');
    $isAduan = request()->routeIs('ipal.aduan.*');
@endphp

<div class="menu menu-column menu-rounded px-3 h-100 d-flex flex-column" id="kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false">
    <div class="menu-item mb-1">
        <a href="{{ route('ipal.dashboard') }}" class="menu-link {{ $isDashboard ? 'active' : '' }}">
            <span class="menu-icon">
                <span class="svg-icon svg-icon-2">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="3" y="3" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/>
                        <rect x="14" y="3" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/>
                        <rect x="3" y="14" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/>
                        <rect x="14" y="14" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/>
                    </svg>
                </span>
            </span>
            <span class="menu-title">Dashboard</span>
        </a>
    </div>

    <div class="menu-item mb-1">
        <a href="{{ route('ipal.upload.index') }}" class="menu-link {{ $isDataJaringan ? 'active' : '' }}">
            <span class="menu-icon">
                <span class="svg-icon svg-icon-2">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3.5 6.5C3.5 5.67 4.17 5 5 5H9.18C9.68 5 10.15 5.25 10.43 5.67L11.2 6.8C11.39 7.07 11.69 7.23 12.02 7.23H19C19.83 7.23 20.5 7.9 20.5 8.73V17.5C20.5 18.33 19.83 19 19 19H5C4.17 19 3.5 18.33 3.5 17.5V6.5Z" stroke="currentColor" stroke-width="1.7"/>
                    </svg>
                </span>
            </span>
            <span class="menu-title">Data Jaringan</span>
        </a>
    </div>

    <div class="menu-item mb-1">
        <a href="{{ route('ipal.aduan.index') }}" class="menu-link {{ $isAduan ? 'active' : '' }}">
            <span class="menu-icon">
                <span class="svg-icon svg-icon-2">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="5" y="4" width="14" height="16" rx="2" stroke="currentColor" stroke-width="1.7"/>
                        <path d="M8.5 9H15.5M8.5 13H15.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                    </svg>
                </span>
            </span>
            <span class="menu-title">Aduan</span>
        </a>
    </div>

    <div class="mt-auto pt-4">
        <div class="menu-item mb-1">
            <a href="{{ route('dashboard.index') }}" class="menu-link">
                <span class="menu-icon">
                    <span class="svg-icon svg-icon-2">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.25 7.5L5.75 12L10.25 16.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6.5 12H18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </span>
                </span>
                <span class="menu-title">Kembali ke Menu Utama</span>
            </a>
        </div>

        <div class="menu-item">
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="menu-link w-100 border-0 bg-transparent text-start">
                    <span class="menu-icon">
                        <span class="svg-icon svg-icon-2">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 7V5.5C9 4.67 9.67 4 10.5 4H18.5C19.33 4 20 4.67 20 5.5V18.5C20 19.33 19.33 20 18.5 20H10.5C9.67 20 9 19.33 9 18.5V17" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                <path d="M13 12H4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                <path d="M6.75 9.25L4 12L6.75 14.75" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </span>
                    <span class="menu-title">Logout</span>
                </button>
            </form>
        </div>
    </div>
</div>
