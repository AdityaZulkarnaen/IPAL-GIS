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
                        <form class="row mt-3" method="POST" action="{{ route('admin_produk_uji.update', $data_edit->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method("PUT")

                            <div class="form-group mt-3">
                                <select name="id_jenis" onchange="getParamUji(this)" class="form-select">
                                    <option value="">Pilih Jenis Pengujian</option>
                                    @foreach ($data_jenis_pengujian as $dt)
                                    <option value="{{ $dt->id }}" @if($dt->id==$data_edit->parameter_uji->jenis->id) selected @endif>{{ $dt->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mt-3">
                                <select name="id_param_uji" id="id_param_uji" class="form-select">
                                    <option value="{{ $data_edit->parameter_uji->id }}">{{ $data_edit->parameter_uji->nama .' ('. $data_edit->parameter_uji->kode_sni . ') | Rp ' . $data_edit->parameter_uji->tarif }}</option>
                                </select>
                            </div>

                            <div id="module-1" class="form-group mt-3">
                                <label for="role">Nama Produk</label>
                                <input class="form-control" name="nama" id="nama" placeholder="Nama Produk" value="{{ $data_edit->nama }}" required>
                            </div>

                            <!--<div id="module-1" class="form-group mt-3">-->
                            <!--    <label for="role">No Order</label>-->
                            <!--    <input class="form-control" name="no_order" id="no_order" placeholder="No Order" value="@if($data_edit->no_order==null){{ '604.1/' . $data_edit->id }}@else{{ $data_edit->no_order }}@endif">-->
                            <!--</div>-->

                            <!--<div id="module-1" class="form-group mt-3">-->
                            <!--    <label for="role">Kode Sampel</label>-->
                            <!--    <input class="form-control" name="kode_sampel" id="kode_sampel" placeholder="Kode Sampel" value="@if($data_edit->kode_sampel==null){{ $data_edit->id . '/BB/' . date('Y', strtotime($data_edit->created_at)) }}@else{{ $data_edit->kode_sampel }}@endif">-->
                            <!--</div>-->

                            <div id="module-1" class="form-group mt-3">
                                <label for="role">Jumlah Uji</label>
                                <input type="number" class="form-control" name="jumlah" id="jumlah" placeholder="Jumlah Uji" value="{{ $data_edit->jumlah }}" required>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-sm btn-primary"><i class="far fa-save" style="margin-right: 8px;"></i> Simpan </button>
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

<script>
    function getParamUji(element) {
        var idJenis = element.value;
        var parentRow = $(element).closest('.row');
        var idParamUji = parentRow.find('[name="id_param_uji"]');

        // Menghapus opsi sebelumnya
        idParamUji.find('option').remove();
        // idParamUji.innerHTML = '<option value="">Pilih Parameter Uji</option>';

        // Mengambil parameter uji berdasarkan jenis pengujian terpilih
        if (idJenis !== '') {
            var data_jenis_pengujian = @json($data_jenis_pengujian);

            data_jenis_pengujian.forEach(k => {
                if (k.id == idJenis) {
                    k.parameter_uji.forEach(d => {
                        var option = document.createElement('option');
                        option.value = d.id;
                        option.text = d.nama + " (" + d.kode_sni + ") | Rp " + d.tarif;
                        idParamUji.append(option);
                    });
                }
            });
        }
    }
</script>

@endsection