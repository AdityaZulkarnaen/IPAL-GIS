{{-- Sidebar Menu untuk Module IPAL --}}
{{-- Developer IPAL bisa menambahkan menu di sini --}}

<div class="menu menu-column menu-rounded menu-sub-indention px-3" id="#kt_app_sidebar_menu"
    data-kt-menu="true" data-kt-menu-expand="false">

    <!--begin:Menu item - Dashboard IPAL-->
    <a href="{{ route('ipal.dashboard') }}"
        class="menu-item here @if(isset($title) && $title == 'Dashboard IPAL') show @endif menu-accordion">
        <span class="menu-link">
            <span class="menu-icon">
                <span class="svg-icon svg-icon-2">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <rect x="2" y="2" width="9" height="9" rx="2" fill="currentColor" />
                        <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="currentColor" />
                        <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="currentColor" />
                        <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="currentColor" />
                    </svg>
                </span>
            </span>
            <span class="menu-title">Dashboard IPAL</span>
        </span>
    </a>
    <!--end:Menu item-->

    <!--begin:Section Header - Fitur IPAL-->
    <div class="menu-item pt-5">
        <div class="menu-content">
            <span class="menu-heading fw-bold text-uppercase fs-7">Fitur IPAL</span>
        </div>
    </div>
    <!--end:Section Header-->

    {{-- ====================================================================== --}}
    {{-- Developer IPAL: Tambahkan menu fitur IPAL di bawah ini                --}}
    {{-- Contoh:                                                                 --}}
    {{--                                                                         --}}
    {{-- <a href="{{ route('ipal.pengolahan.index') }}"                          --}}
    {{--     class="menu-item @if($title == 'Pengolahan') show @endif">          --}}
    {{--     <span class="menu-link">                                            --}}
    {{--         <span class="menu-icon">                                        --}}
    {{--             <span class="svg-icon svg-icon-2">...</span>                --}}
    {{--         </span>                                                         --}}
    {{--         <span class="menu-title">Data Pengolahan</span>                 --}}
    {{--     </span>                                                             --}}
    {{-- </a>                                                                    --}}
    {{-- ====================================================================== --}}

    <!--begin:Menu item - Upload Data Jaringan-->
    <a href="{{ route('ipal.upload.index') }}"
        class="menu-item @if(isset($title) && $title == 'Upload Data Jaringan') show @endif">
        <span class="menu-link">
            <span class="menu-icon">
                <span class="svg-icon svg-icon-2">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.3"
                            d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="currentColor" />
                        <path
                            d="M9.2 3H3C2.4 3 2 3.4 2 4V19C2 19.6 2.4 20 3 20H21C21.6 20 22 19.6 22 19V7C22 6.4 21.6 6 21 6H12L10.4 3.6C10.2 3.2 9.7 3 9.2 3Z"
                            fill="currentColor" />
                        <path
                            d="M12 16C12 16 11.2 15.2 10.4 14.4C9.6 13.6 12 11 12 11C12 11 14.4 13.6 13.6 14.4C12.8 15.2 12 16 12 16Z"
                            fill="white" opacity="0.6" />
                        <path d="M11.5 16V12.5L12.5 12.5V16H11.5Z" fill="white" opacity="0.6" />
                    </svg>
                </span>
            </span>
            <span class="menu-title">Upload Data Jaringan</span>
        </span>
    </a>
    <!--end:Menu item-->

    <!--begin:Menu item - Manajemen Aduan-->
    <a href="{{ route('ipal.aduan.index') }}"
        class="menu-item @if(isset($title) && $title == 'Manajemen Aduan') show @endif">
        <span class="menu-link">
            <span class="menu-icon">
                <span class="svg-icon svg-icon-2">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.3" d="M2 4C1.4 4 1 4.4 1 5V21C1 21.6 1.4 22 2 22H20C20.6 22 21 21.6 21 21V5C21 4.4 20.6 4 20 4H2Z" fill="currentColor"/>
                        <path d="M21 7H1V5C1 4.4 1.4 4 2 4H20C20.6 4 21 4.4 21 5V7ZM5 12H19C19.6 12 20 11.6 20 11C20 10.4 19.6 10 19 10H5C4.4 10 4 10.4 4 11C4 11.6 4.4 12 5 12ZM5 17H11C11.6 17 12 16.6 12 16C12 15.4 11.6 15 11 15H5C4.4 15 4 15.4 4 16C4 16.6 4.4 17 5 17Z" fill="currentColor"/>
                    </svg>
                </span>
            </span>
            <span class="menu-title">Manajemen Aduan</span>
        </span>
    </a>
    <!--end:Menu item-->

    <!--begin:Section Header - Navigasi-->
    <div class="menu-item pt-5">
        <div class="menu-content">
            <span class="menu-heading fw-bold text-uppercase fs-7">Navigasi</span>
        </div>
    </div>
    <!--end:Section Header-->

    <!--begin:Menu item - Kembali ke Menu Utama-->
    <a href="{{ route('dashboard.index') }}" class="menu-item menu-accordion">
        <span class="menu-link">
            <span class="menu-icon">
                <span class="svg-icon svg-icon-2">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M9.60001 11H21C21.6 11 22 11.4 22 12C22 12.6 21.6 13 21 13H9.60001V11Z"
                            fill="currentColor" />
                        <path opacity="0.3"
                            d="M9.6 20V4L2.3 11.3C1.9 11.7 1.9 12.3 2.3 12.7L9.6 20Z"
                            fill="currentColor" />
                    </svg>
                </span>
            </span>
            <span class="menu-title">Kembali ke Menu Utama</span>
        </span>
    </a>
    <!--end:Menu item-->

    <!--begin:Menu item - Logout-->
    <div class="menu-item">
        <form method="POST" action="{{ route('logout') }}" class="m-0">
            @csrf
            <button type="submit" class="menu-link w-100">
                <span class="menu-icon">
                    <span class="svg-icon svg-icon-2">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <rect opacity="0.3" width="12" height="2" rx="1"
                                transform="matrix(0 -1 -1 0 15.5 19.5)" fill="currentColor" />
                            <rect width="12" height="2" rx="1"
                                transform="matrix(0 -1 -1 0 12.5 16.5)" fill="currentColor" />
                            <path d="M17.5 13L9 5.5V3H6V5.5L10.5 10L4.5 16H11.5L17.5 10V13Z"
                                fill="currentColor" />
                        </svg>
                    </span>
                </span>
                <span class="menu-title">Logout</span>
            </button>
        </form>
    </div>
    <!--end:Menu item - Logout-->

</div>
