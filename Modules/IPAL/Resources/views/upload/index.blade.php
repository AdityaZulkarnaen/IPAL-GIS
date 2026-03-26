@extends('ipal::layouts.main')

@include('ipal::components.data-jaringan.tailwind-assets')

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">

        @include('ipal::components.data-jaringan.page-header')

        @if (session('success'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
            {{ session('success') }}
        </div>
        @endif

        @if (session('error'))
        <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-rose-700">
            {{ session('error') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-rose-700">
            <ul class="mb-0 list-disc pl-5">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="row g-5 mb-8">
            <div class="col-xl-7">
                @include('ipal::components.data-jaringan.upload-panel')
            </div>

            <div class="col-xl-5">
                @include('ipal::components.data-jaringan.upload-history')
            </div>
        </div>

        @include('ipal::components.data-jaringan.pipes-table')

        @include('ipal::components.data-jaringan.manholes-table')

    </div>
</div>
@endsection

@include('ipal::components.data-jaringan.scripts')
