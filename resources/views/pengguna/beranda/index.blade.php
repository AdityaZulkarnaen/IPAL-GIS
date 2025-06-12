@extends('layouts.main')
@section('content')


<!--begin::Content-->
<div id="kt_app_content" class="app-content flex-column-fluid">
    <!--begin::Content container-->
    <div id="kt_app_content_container" class="app-container container-fluid">
        <!--begin::Row-->
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <!--begin::Col-->
            <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3 mb-md-5 mb-xl-10">
                <div class="card bg-primary">
                    <div class="card-body text-light">
                        <h1 class="text-light">{{ $jumlah_pengajuan }}+</h1>
                        <span>Jumlah Pengajuan Pengujian</span>
                    </div>
                </div>
            </div>

        </div>
        <!--end::Row-->
    </div>
    <!--end::Content container-->

</div>
<!--end::Content-->

@endsection