<!DOCTYPE html>
<html lang="en">

<head>
    <base href="" />
    <title>{{ $service['data_konfig']->nama_sistem . ' | ' . $subtitle }}</title>
    <link rel="icon" href="{{ asset($service['data_konfig']->logo) }}" type="image/x-icon">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->

    <!--begin::Vendor Stylesheets-->
    <link href="{{ asset('tema') }}/dist/assets/plugins/custom/fullcalendar/fullcalendar.bundle.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('tema') }}/dist/assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
    <!--end::Vendor Stylesheets-->

    <!--begin::Global Stylesheets Bundle-->
    <link href="{{ asset('tema') }}/dist/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('tema') }}/dist/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->

    @vite(['resources/css/app.css'])

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

    <style>
        #kt_app_body,
        #kt_app_root,
        #kt_app_page,
        #kt_app_wrapper,
        #kt_app_main {
            background: #f5f7fb;
        }

        #kt_app_header {
            background: #ffffff;
            border-bottom: 1px solid #e8edf5;
            box-shadow: 0 2px 18px rgba(15, 23, 42, 0.03);
            z-index: 70;
        }

        #kt_app_sidebar {
            background: linear-gradient(180deg, #0b1120 0%, #020617 100%) !important;
            border-right: 1px solid rgba(148, 163, 184, 0.16);
        }

        #kt_app_sidebar .menu-link {
            border-radius: 8px;
            color: #94a3b8;
            font-weight: 500;
        }

        #kt_app_sidebar .menu-link .menu-icon,
        #kt_app_sidebar .menu-link .menu-title {
            color: inherit;
        }

        #kt_app_sidebar .menu-link:hover {
            background: rgba(30, 41, 59, 0.75);
            color: #e2e8f0;
        }

        #kt_app_sidebar .menu-link.active {
            background: rgba(26, 88, 242, 0.24);
            color: #60a5fa;
            border: 1px solid rgba(96, 165, 250, 0.34);
        }

        #kt_app_sidebar .menu-link.active .menu-icon {
            color: #60a5fa;
        }

        #kt_app_sidebar .menu-link.active .menu-icon .svg-icon,
        #kt_app_sidebar .menu-link.active .menu-icon .svg-icon svg,
        #kt_app_sidebar .menu-link.active .menu-icon .svg-icon svg path,
        #kt_app_sidebar .menu-link.active .menu-icon .svg-icon svg rect,
        #kt_app_sidebar .menu-link.active .menu-icon .svg-icon svg circle,
        #kt_app_sidebar .menu-link.active .menu-icon .svg-icon svg line,
        #kt_app_sidebar .menu-link.active .menu-icon .svg-icon svg polyline {
            color: #60a5fa !important;
            stroke: currentColor !important;
        }

        #kt_app_sidebar .menu-link.active .menu-icon .svg-icon svg [fill="currentColor"] {
            fill: currentColor !important;
        }

        .ipal-header-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 3px 6px 3px 3px;
            border-radius: 999px;
            border: 1px solid #dbe3ef;
            background: #ffffff;
        }

        .ipal-header-user-name {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            white-space: nowrap;
        }

        @media (max-width: 767.98px) {
            .ipal-header-user {
                border: 0;
                padding: 0;
            }

            .ipal-header-user-name {
                display: none;
            }
        }

        .menu-item button.menu-link {
            background-color: transparent;
            border: none;
            color: var(--kt-app-sidebar-dark-menu-link-color);
            padding: 0.65rem 1rem;
        }
        .menu-item button.menu-link:hover {
            background-color: var(--kt-app-sidebar-dark-menu-link-hover-bg-color);
            color: var(--kt-app-sidebar-dark-menu-link-hover-color);
        }
        [data-kt-app-sidebar-minimize="on"] #kt_app_sidebar_footer {
            width: 75px;
        }
        @media (max-width: 991.98px) {
            #kt_app_sidebar_footer {
                width: 265px;
            }
        }
    </style>

    {{-- Stack untuk CSS tambahan dari halaman IPAL --}}
    @stack('ipal-styles')
</head>

<body id="kt_app_body" data-kt-app-layout="dark-sidebar" data-kt-app-header-fixed="true"
    data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true"
    data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true"
    data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true" class="app-default">

    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">

            <!--begin::Header-->
            <div id="kt_app_header" class="app-header">
                <div class="app-container container-fluid d-flex align-items-stretch justify-content-between"
                    id="kt_app_header_container">
                    <div class="d-flex align-items-center d-lg-none ms-n3 me-1 me-md-2" title="Show sidebar menu">
                        <div class="btn btn-icon btn-active-color-primary w-35px h-35px"
                            id="kt_app_sidebar_mobile_toggle">
                            <span class="svg-icon svg-icon-2 svg-icon-md-1">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z"
                                        fill="currentColor" />
                                    <path opacity="0.3"
                                        d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z"
                                        fill="currentColor" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1"
                        id="kt_app_header_wrapper">
                        <div class="app-header-menu app-header-mobile-drawer align-items-stretch"></div>

                        <div class="app-navbar flex-shrink-0">
                            <!--begin::User menu-->
                            <div class="app-navbar-item ms-1 ms-md-3" id="kt_header_user_menu_toggle">
                                <div class="cursor-pointer ipal-header-user"
                                    data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                                    data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                                    <div class="symbol symbol-32px symbol-md-35px">
                                        <img src="{{ asset('logo/cogwheel.png') }}" alt="user" />
                                    </div>
                                    <span class="ipal-header-user-name">{{ auth()->user()->name }}</span>
                                </div>
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px"
                                    data-kt-menu="true">
                                    <div class="menu-item px-3">
                                        <div class="menu-content d-flex align-items-center px-3">
                                            <div class="symbol symbol-50px me-5">
                                                <img alt="Logo" src="{{ asset('logo/cogwheel.png') }}" />
                                            </div>
                                            <div class="d-flex flex-column">
                                                <div class="fw-bold d-flex align-items-center fs-5">
                                                    {{ auth()->user()->name }}</div>
                                                <a href="#"
                                                    class="fw-semibold text-muted text-hover-primary fs-7">{{ auth()->user()->email }}</a>
                                                <span
                                                    class="badge badge-light-success fw-bold fs-8 px-2 py-1 ms-2">{{ auth()->user()->role }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="separator my-2"></div>
                                    <div class="menu-item px-5">
                                        <a href="{{ route('profil.edit', auth()->user()->id) }}"
                                            class="menu-link px-5">Profil Saya</a>
                                    </div>
                                    <div class="menu-item px-5">
                                        <a href="{{ route('dashboard.index') }}" class="menu-link px-5">Kembali ke
                                            Menu Utama</a>
                                    </div>
                                    <div class="menu-item px-2">
                                        <div class="menu-link">
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <x-dropdown-link :href="route('logout')"
                                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                                    {{ __('Log Out') }}
                                                </x-dropdown-link>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::User menu-->
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Header-->

            <!--begin::Wrapper-->
            <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">

                <!--begin::Sidebar-->
                <div id="kt_app_sidebar" class="app-sidebar flex-column bg-[#0B0C10]" data-kt-drawer="true"
                    data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}"
                    data-kt-drawer-overlay="true" data-kt-drawer-width="265px"
                    data-kt-drawer-direction="start"
                    data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">

                    <!--begin::Logo-->
                    <div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
                        <a href="{{ route('ipal.dashboard') }}">
                            <img alt="Logo" src="{{ asset($service['data_konfig']->logo) }}" class="h-25px"
                                style="margin-right:8px;" />
                            <strong class="text-light app-sidebar-logo-default">BPALPJK IPAL</strong>
                        </a>
                        <div id="kt_app_sidebar_toggle"
                            class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary body-bg h-30px w-30px position-absolute top-50 start-100 translate-middle rotate"
                            data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
                            data-kt-toggle-name="app-sidebar-minimize">
                            <span class="svg-icon svg-icon-2 rotate-180">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.5"
                                        d="M14.2657 11.4343L18.45 7.25C18.8642 6.83579 18.8642 6.16421 18.45 5.75C18.0358 5.33579 17.3642 5.33579 16.95 5.75L11.4071 11.2929C11.0166 11.6834 11.0166 12.3166 11.4071 12.7071L16.95 18.25C17.3642 18.6642 18.0358 18.6642 18.45 18.25C18.8642 17.8358 18.8642 17.1642 18.45 16.75L14.2657 12.5657C13.9533 12.2533 13.9533 11.7467 14.2657 11.4343Z"
                                        fill="currentColor" />
                                    <path
                                        d="M8.2657 11.4343L12.45 7.25C12.8642 6.83579 12.8642 6.16421 12.45 5.75C12.0358 5.33579 11.3642 5.33579 10.95 5.75L5.40712 11.2929C5.01659 11.6834 5.01659 12.3166 5.40712 12.7071L10.95 18.25C11.3642 18.6642 12.0358 18.6642 12.45 18.25C12.8642 17.8358 12.8642 17.1642 12.45 16.75L8.2657 12.5657C7.95328 12.2533 7.95328 11.7467 8.2657 11.4343Z"
                                        fill="currentColor" />
                                </svg>
                            </span>
                        </div>
                    </div>
                    <!--end::Logo-->

                    <!--begin::sidebar menu-->
                    <div class="app-sidebar-menu overflow-hidden flex-column-fluid">
                        <div id="kt_app_sidebar_menu_wrapper"
                            class="app-sidebar-wrapper hover-scroll-overlay-y my-5" data-kt-scroll="true"
                            data-kt-scroll-activate="true" data-kt-scroll-height="auto"
                            data-kt-scroll-dependencies="#kt_app_sidebar_logo"
                            data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="0px"
                            data-kt-scroll-save-state="true">

                            {{-- Sidebar Menu IPAL --}}
                            @include('ipal::layouts.partials.sidebar')

                        </div>
                    </div>
                    <!--end::sidebar menu-->
                </div>
                <!--end::Sidebar-->

                <!--begin::Main-->
                <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                    <div class="d-flex flex-column flex-column-fluid">
                        <!--begin::Toolbar-->
                        {{-- <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                            <div id="kt_app_toolbar_container"
                                class="app-container container-fluid d-flex flex-stack">
                                <div
                                    class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                                    <h1
                                        class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                                        {{ $subtitle }}
                                    </h1>
                                    <ul
                                        class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                                        <li class="breadcrumb-item text-muted">
                                            <a href="{{ route('ipal.dashboard') }}"
                                                class="text-muted text-hover-primary">{{ $toptitle }}</a>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                                        </li>
                                        <li class="breadcrumb-item text-muted">{{ $title }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div> --}}
                        <!--end::Toolbar-->

                        @yield('content')

                    </div>

                    <!--begin::Footer-->
                    <div id="kt_app_footer" class="app-footer">
                        <div
                            class="app-container container-fluid d-flex flex-column flex-md-row flex-center flex-md-stack py-3">
                            <div class="text-dark order-2 order-md-1">
                                <span class="text-muted fw-semibold me-1">2023&copy;</span>
                                {{ $service['data_konfig']->nama_sistem }} - Module IPAL
                                <a href="#">{{ $service['data_konfig']->nama_instansi }}</a>
                            </div>
                        </div>
                    </div>
                    <!--end::Footer-->
                </div>
                <!--end::Main-->

            </div>
            <!--end::Wrapper-->
        </div>
    </div>

    <!--begin::Javascript-->
    <script>
        var hostUrl = "{{ asset('tema') }}/dist/assets/";
    </script>
    <script src="{{ asset('tema') }}/dist/assets/plugins/global/plugins.bundle.js"></script>
    <script src="{{ asset('tema') }}/dist/assets/js/scripts.bundle.js"></script>
    <script src="{{ asset('tema') }}/dist/assets/plugins/custom/fullcalendar/fullcalendar.bundle.js"></script>
    <script src="{{ asset('tema') }}/dist/assets/js/widgets.bundle.js"></script>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#example').DataTable();
        });
    </script>

    {{-- Stack untuk JS tambahan dari halaman IPAL --}}
    @stack('ipal-scripts')
    <!--end::Javascript-->
</body>

</html>
