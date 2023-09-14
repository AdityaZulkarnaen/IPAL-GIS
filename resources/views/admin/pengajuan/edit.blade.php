@extends('layouts.main')
@section('content')

<!--begin::Content-->
<div id="kt_app_content" class="app-content flex-column-fluid">
    <!--begin::Content container-->
    <div id="kt_app_content_container" class="app-container container-fluid">
        <!--begin::Row-->
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <!--begin::Col-->

            <div class="col-md-12 col-lg-12 col-xl-12 col-xxl-12 mb-md-5 mb-xl-10">

                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <form class="row mt-3" method="POST" action="{{ route('pengajuan.update', $data_edit->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method("PUT")

                            <div class="form-group col-md-6" hidden>
                                <label class="">Email</label>
                                <div class="">
                                    <input type="text" name="email" value="{{ auth()->user()->email }}" class="form-control form-control-normal" placeholder="Email...">
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                                <label class="">Pilih Jenis Pengujian:</label>
                                <select class="form-select" data-control="select2" name="id_jenis" id="">
                                    @foreach($data_jenis as $data_jenis)
                                    <option value="{{ $data_jenis->id }}" @if($data_jenis->id == $data_edit->id_jenis) selected @endif>{{ $data_jenis->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="">Kegiatan/Paket Pekerjaan</label>
                                <div class="">
                                    <input placeholder="Kegiatan..." name="kegiatan" value="{{ $data_edit->kegiatan }}" type="text" class="form-control form-control-normal">
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="">Sumber/Satker</label>
                                <div class="">
                                    <input placeholder="Sumber/Satker..." name="sumber" value="{{ $data_edit->sumber }}" type="text" class="form-control form-control-normal">
                                </div>
                            </div>

                            <div class="form-group col-sm-12">
                                <div class="">
                                    <button type="submit" class="btn btn-primary"><i class="far fa-save" style="margin-right: 8px;"></i> Simpan </button>
                                </div>
                            </div>
                        </form>
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