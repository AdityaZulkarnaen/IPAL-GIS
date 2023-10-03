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
                        <form class="row mt-3" method="POST" action="{{ route('pengajuan.store') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group col-md-12">
                                <label class="text-right align-middle">Instansi / Nama Lengkap</label>
                                <div class="">
                                    <input type="text" name="name" value="@if(auth()->user()->role == 'Pengguna'){{ auth()->user()->name }}@endif" class="form-control form-control-normal" placeholder="Instansi / Nama Lengkap">
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                                <label class="text-right align-middle">Email</label>
                                <div class="">
                                    <input type="email" name="email" value="@if(auth()->user()->role == 'Pengguna'){{ auth()->user()->email }}@endif" class="form-control form-control-normal" placeholder="Email...">
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                                <label class="text-right align-middle">No. HP</label>
                                <div class="">
                                    <input type="text" name="nomor_hp" value="@if(auth()->user()->role == 'Pengguna'){{ auth()->user()->nomor_hp }}@endif" class="form-control form-control-normal" placeholder="No. HP...">
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                                <label class="text-right align-middle">Alamat</label>
                                <div class="">
                                    <input type="text" name="alamat" value="@if(auth()->user()->role == 'Pengguna'){{ auth()->user()->alamat }}@endif" class="form-control form-control-normal" placeholder="Alamat...">
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                                <label class="text-right align-middle">Jenis Pekerjaan</label>
                                <div class="">
                                    <input type="text" name="kegiatan" value="" class="form-control form-control-normal" placeholder="Jenis Pekerjaan...">
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                                <label class="text-right align-middle">Sumber/Satker</label>
                                <div class="">
                                    <select name="sumber" id="sumber" class="form-select" onchange="toggleInput()">
                                        <option value="APBN">APBN</option>
                                        <option value="APBD Provinsi">APBD Provinsi</option>
                                        <option value="APBD Kabupaten/Kota">APBD Kabupaten/Kota</option>
                                        <option value="Swasta">Swasta</option>
                                        <option value="Perorangan">Perorangan</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                                <label class="">Kegiatan/Paket Pekerjaan</label>
                                <div class="">
                                    <input placeholder="Kegiatan..." name="kegiatan" type="text" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-12 form-group" id="no_dok">
                                <label class="">Surat Permohonan/Tgl.</label>
                                <input type="text" name="no_dokumen" class="form-control" id="no_dokumen" placeholder="Surat Permohonan/Tgl.">
                            </div>

                            <script>
                                function toggleInput() {
                                    var selectElement = document.getElementById("sumber");
                                    var inputElement = document.getElementById("no_dok");

                                    if (selectElement.value != "APBD") {
                                        console.log("Okeeeee");
                                        inputElement.style.display = "none";
                                    } else {
                                        console.log("Tidak okeeee");
                                        inputElement.style.display = "block";
                                    }
                                }
                            </script>

                            <div class="text-center">
                                <h4>Tambah Parameter Uji</h4>
                                <p>Tambahkan sesuai dengan yang dibutuhkan. Dapat lebih dari 1 dengan menekan tombol <strong>"Tambah"</strong></p>
                            </div>

                            <div class="row after-add-more">
                                <div class="col-md-3 form-group mt-3">
                                    <select name="id_jenis[]" onchange="getParamUji(this)" class="form-select">
                                        <option value="">Pilih Jenis Pengujian</option>
                                        @foreach ($data_jenis_pengujian as $dt)
                                        <option value="{{ $dt->id }}">{{ $dt->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-5 form-group mt-3">
                                    <select name="id_param_uji[]" id="id_param_uji[]" class="form-select">
                                        <option value="">Pilih Parameter Pengujian Dahulu..</option>
                                    </select>
                                </div>

                                <div class="col-md-3 form-group mt-3" hidden>
                                    <input type="text" class="form-control" value="-" name="nama_produk[]" id="nama_produk[]" placeholder="Nama Produk" required>
                                </div>

                                <div class="col-md-2 form-group mt-3">
                                    <input type="number" class="form-control" name="jumlah_sampel[]" id="jumlah_sampel[]" placeholder="Jumlah" required>
                                </div>

                                <div class="col-md-1 form-group mt-3">
                                    <button class="btn btn-sm btn-primary add-more" type="button">Tambah</button>
                                </div>
                            </div>

                            <div class="form-group col-sm-12">
                                <div class="">
                                    <button type="submit" class="btn btn-primary"><i class="far fa-save" style="margin-right: 8px;"></i> Simpan dan Lanjutkan </button>
                                </div>
                            </div>
                        </form>


                        <div class="copy invisible" data-index="">
                            <div class="row">
                                <div class="col-md-3 form-group mt-3">
                                    <select name="id_jenis[]" onchange="getParamUji(this)" class="form-select">
                                        <option value="">Pilih Jenis Pengujian</option>
                                        @foreach ($data_jenis_pengujian as $dt)
                                        <option value="{{ $dt->id }}">{{ $dt->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-5 form-group mt-3">
                                    <select name="id_param_uji[]" id="id_param_uji[]" class="form-select">
                                        <option value="">Pilih Jenis Pengujian Dahulu..</option>
                                    </select>
                                </div>

                                <div class="col-md-3 form-group mt-3">
                                    <input type="text" class="form-control" name="nama_produk[]" id="nama_produk[]" placeholder="Nama Produk" required>
                                </div>

                                {{-- <div class="col-md-2 form-group mt-3"> --}}
                                <input type="hidden" class="form-control" value="1" name="jumlah_sampel[]" id="jumlah_sampel[]" placeholder="Jumlah" required>
                                {{-- </div> --}}

                                <div class="col-md-1 form-group mt-3">
                                    <button class="btn btn-danger remove" type="button">
                                        Hapus</button>
                                </div>

                                <!-- <div class="col-md-1 form-group mt-3">
                            <button class="btn btn-primary">Tambah</button>
                        </div> -->

                            </div>
                        </div>

                        <script>
                            function getParamUji(element) {
                                var idJenis = element.value;
                                var parentRow = $(element).closest('.row');
                                var idParamUji = parentRow.find('[name="id_param_uji[]"]');

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

                        <script type="text/javascript">
                            $(document).ready(function() {
                                $(".add-more").click(function() {
                                    var html = $(".copy").html();
                                    var index = $(".after-add-more .row").length;
                                    html = $(html).attr('data-index', index);
                                    $(".after-add-more").after(html);
                                    getParamUji($('[name="id_jenis[]"]').last()[0]);
                                });

                                // saat tombol remove dklik control group akan dihapus 
                                $("body").on("click", ".remove", function() {
                                    $(this).parents(".row").remove();
                                    reindex();
                                });

                                function reindex() {
                                    $(".after-add-more .row").each(function(index) {
                                        $(this).attr('data-index', index);
                                        $(this).find('[id^="id_jenis"]').attr('id', 'id_jenis[' + index + ']');
                                        $(this).find('[id^="id_param_uji"]').attr('id', 'id_param_uji[' + index + ']');
                                    });
                                }
                            });
                        </script>

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