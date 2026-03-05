@extends('ipal::layouts.main')
@section('content')

<!--begin::Content-->
<div id="kt_app_content" class="app-content flex-column-fluid">
    <!--begin::Content container-->
    <div id="kt_app_content_container" class="app-container container-fluid">

        <!--begin::Row-->
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <!--begin::Col - Info Card-->
            <div class="col-12">
                <div class="card bg-primary mb-5">
                    <div class="card-body">
                        <div class="text-light">
                            <h3 class="text-light mb-3">Selamat Datang di Module IPAL</h3>
                            <p class="mb-0">
                                Module Instalasi Pengolahan Air Limbah (IPAL) siap digunakan.<br>
                                Developer IPAL dapat mulai mengembangkan fitur di module ini.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Col-->
        </div>
        <!--end::Row-->

        <!--begin::Row - Statistik-->
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <!--begin::Col-->
            <div class="col-md-4">
                <div class="card card-flush h-xl-100">
                    <div class="card-header pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-dark">Total Data IPAL</span>
                        </h3>
                    </div>
                    <div class="card-body d-flex align-items-center">
                        <span class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2">0</span>
                        <span class="badge badge-light-success fs-base">Data</span>
                    </div>
                </div>
            </div>
            <!--end::Col-->

            <!--begin::Col-->
            <div class="col-md-4">
                <div class="card card-flush h-xl-100">
                    <div class="card-header pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-dark">Status Aktif</span>
                        </h3>
                    </div>
                    <div class="card-body d-flex align-items-center">
                        <span class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2">0</span>
                        <span class="badge badge-light-primary fs-base">Aktif</span>
                    </div>
                </div>
            </div>
            <!--end::Col-->

            <!--begin::Col-->
            <div class="col-md-4">
                <div class="card card-flush h-xl-100">
                    <div class="card-header pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-dark">Status Tidak Aktif</span>
                        </h3>
                    </div>
                    <div class="card-body d-flex align-items-center">
                        <span class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2">0</span>
                        <span class="badge badge-light-danger fs-base">Tidak Aktif</span>
                    </div>
                </div>
            </div>
            <!--end::Col-->
        </div>
        <!--end::Row-->

    </div>
    <!--end::Content container-->
</div>
<!--end::Content-->

@endsection
