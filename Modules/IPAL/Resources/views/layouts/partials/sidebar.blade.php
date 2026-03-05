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
