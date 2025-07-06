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
                        <form class="row mt-3" method="POST" action="{{ route('pengajuan_uji.update', $data_edit->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method("PUT")

                            <div class="form-group col-md-4">
                                Nama Pelanggan:<br>
                                <strong>{{ strtoupper($data_edit->user->name) }}</strong><br><br>
                                Alamat Pelanggan:<br>
                                <strong>{{ strtoupper($data_edit->user->alamat) }}</strong><br><br>
                                No.HP Pelanggan:<br>
                                <strong>{{ strtoupper($data_edit->user->nomor_hp) }}</strong><br><br>
                                Kegiatan:<br>
                                <strong>{{ strtoupper($data_edit->kegiatan) }}</strong><br><br>
                                {{-- Tanggal:<br>
                                <strong>{{ strtoupper($data_edit->created_at) }}</strong><br> --}}
                                Tanggal Order:<br>
                                <strong>{{ date('d-m-Y', strtotime($data_edit->created_at)) }}</strong><br>
                            </div>

                            <div class="form-group col-md-4">
                                <div id="werwer" class="form-group">
                                    <label for="role">No.Pesanan</label>
                                    <input class="form-control" name="no_order" id="no_pesanan" placeholder="No.Pesanan" value="{{ $data_edit->no_order }}">
                                </div>

                                <div id="werwer" class="form-group mt-3">
                                    <label for="role">Kode Sampel</label>
                                    <input class="form-control" name="kode_sampel" id="kode_sampel" placeholder="Kode Sampel" value="{{ $data_edit->kode_sampel }}">
                                </div>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="role">Ubah Status Order</label>
                                <select class="form-select" data-control="select2" id="status_bayar" name="status_bayar" onchange="toggleInput()">
                                    <option @if($data_edit->status_bayar=='Ditolak' ) selected @endif>Ditolak</option>
                                    <option @if($data_edit->status_bayar=='Belum Dibayar' ) selected @endif>Belum Dibayar</option>
                                    <option @if($data_edit->status_bayar=='Sudah Dibayar' ) selected @endif value="Sudah Dibayar">Sudah Dibayar</option>
                                    <option @if($data_edit->status_bayar=='Disposisi' ) selected @endif>Disposisi</option>
                                    <option @if($data_edit->status_bayar=='Persiapan Uji' ) selected @endif>Persiapan Uji</option>
                                    <option @if($data_edit->status_bayar=='Proses Pengujian' ) selected @endif>Proses Pengujian</option>
                                    <option @if($data_edit->status_bayar=='Penyusunan LHU' ) selected @endif>Penyusunan LHU</option>
                                    <option @if($data_edit->status_bayar=='Penerbitan LHU' ) selected @endif>Penerbitan LHU</option>
                                    <option @if($data_edit->status_bayar=='Legalisasi' ) selected @endif>Legalisasi</option>
                                    <option @if($data_edit->status_bayar=='Order Selesai' ) selected @endif>Order Selesai</option>
                                </select>

                                {{-- <div class="d-flex flex-row justify-content-between">

                                    <div id="module-2" class="form-group1 mt-3 w-25 mr-3" id="jumlah_sampel">
                                        <label for="role">Jumlah Sampel</label>
                                        <input type="number" class="form-control" name="jumlah_sampel" id="jumlah_sampel" value="">
                                    </div>
    
                                    <div id="module-2" class="form-group1 mt-3 w-75" id="kode_sampel">
                                        <label for="role">Satuan</label>
                                        <input class="form-control" name="satuan_sampel" id="satuan_sampel" value="">
                                    </div>

                                </div>

                                <div id="module-2" class="form-group1 mt-3" id="kode_sampel">
                                    <label for="role">Bentuk Sampel</label>
                                    <input class="form-control" name="kode_sampel" id="bentuk_sampel" value="" required>
                                </div> --}}

                                <div class="form-group mt-3" id="area_catatan">
                                    <label for="role">Catatan</label>
                                    <textarea class="form-control" name="catatan" rows="3" placeholder="Catatan...">{{ $data_edit->catatan }}</textarea>
                                </div>

                                <div class="form-group mt-3" id="berkas">
                                    <label for="role">LHU</label>
                                    <input class="form-control" type="file" name="berkas" placeholder="LHU...">
                                </div>

                                <div class="mt-2">
                                    <button type="submit" class="btn btn-sm btn-primary"><i class="far fa-save" style="margin-right: 8px;"></i> Simpan </button>
                                </div>

                            </div>

                            <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>


                            <script>
                                @if($data_edit->status_bayar!='Order Selesai')
                                document.getElementById("berkas").style.display = "none";
                                @endif

                                function toggleInput() {
                                    var selectElement = document.getElementById("status_bayar");
                                    var inputElement = document.getElementById("berkas");

                                    console.log("" + selectElement.value);

                                    if (selectElement.value != "Order Selesai") {
                                        console.log("Okeeeee");
                                        inputElement.style.display = "none";
                                    } else {
                                        console.log("Tidak okeeee");
                                        inputElement.style.display = "block";
                                    }
                                }
                            </script>

                            <script>
                                let modules = $('.form-group1').hide();

                                $('#status_bayar').on('click', function x() {
                                    $('#status_bayar').click();
                                    // get value of selected option, which is id of module
                                    let value = $(this).val();
                                    // hide all modules
                                    if (value == 'Sudah Dibayar') {
                                        $("div.form-group1").show();
                                        // document.getElementById("no_pesanan").required = true;
                                        // document.getElementById("kode_sampel").required = true;
                                        document.getElementById("jumlah_sampel").required = true;
                                        document.getElementById("satuan_sampel").required = true;
                                        document.getElementById("bentuk_sampel").required = true;
                                    } else {
                                        modules.hide();
                                        // document.getElementById("no_pesanan").required = false;
                                        // document.getElementById("kode_sampel").required = false;
                                        document.getElementById("jumlah_sampel").required = false;
                                        document.getElementById("satuan_sampel").required = false;
                                        document.getElementById("bentuk_sampel").required = false;
                                    }
                                });
                            </script>
                        </form>

                        <hr>
                        <strong><u>Riwayat Status: </u></strong>
                        <br>
                        @foreach($data_edit->status as $dts)
                        - {{ explode(',', $dts->catatan)[0] .' | '. $dts->created_at }}
                        <!--@if($dts->berkas != null)-->
                        <!--| <a href="{{ asset($dts->berkas) }}" target="_blank" rel="berkas"><i><strong>Unduh LHU</strong></i></a>-->
                        <!--@endif-->
                        <br>
                        @endforeach
                        <hr>

                        <!--<form>-->
                            @php
                            $no = 1;
                            $total = 0;
                            @endphp

                            <a href="{{ route('admin_produk_uji.show', $data_edit->id) }}" class="btn btn-sm btn-primary">
                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr075.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor" />
                                        <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor" />
                                    </svg>
                                </span>
                                {{ 'Tambah Produk Uji' }}
                            </a>

                            <div class="table-responsive">
                                @csrf
                                <table id="example" class="table table-striped" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Benda Uji</th>
                                            <th>Jenis Pengujian</th>
                                            <th>Parameter Uji</th>
                                            <th>Jumlah Uji</th>
                                            <th>Satuan</th>
                                            <!--<th>No. Pesanan</th>-->
                                            <!--<th>Kode Sampel</th>-->
                                            <th>Biaya</th>
                                            <!--<th>Status Uji</th>-->
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @foreach($data_transaksi_produk as $dt)
                                        <tr>
                                            <td><button style="padding: 6px 12px 6px 12px;" class="btn btn-secondary btn-sm">{{ $no++ }}</button></td>
                                            <td width="120px">{{ $dt->nama }}</td>

                                            {{-- <td> --}}
                                            {{-- <select name="id_jenis[]" onchange="getParamUji(this)" class="form-select">
                                                    <option value="">Pilih Jenis Pengujian</option>
                                                    @foreach ($data_jenis_pengujian as $dt)
                                                    <option value="{{ $dt->id }}">{{ $dt->nama }}</option>
                                            @endforeach
                                            </select> --}}
                                            {{-- </td> --}}

                                            {{-- <td>{{ $dt->parameter_uji->id_jenis->nama }}</td> --}}
                                            <td>{{ App\Models\JenisModel::find($dt->parameter_uji->id_jenis)->nama }}</td>
                                            <td>{{ $dt->parameter_uji->nama }}</td>
                                            {{-- <td width="90px">{{ '604.1/' . $dt->id }}</td>
                                            <td width="90px">{{ $dt->id . '/BB/' . date('Y', strtotime($dt->created_at)) }}</td> --}}

                                            <td>{{ $dt->jumlah }}</td>
                                            <td>{{ $dt->parameter_uji->satuan }}</td>
                                            <!--<td>{{ $dt->no_order }}</td>-->
                                            <!--<td>{{ $dt->kode_sampel }}</td>-->
                                            <td width="120px">
                                                {{ "Rp " . number_format($dt->jumlah * $dt->parameter_uji->tarif,0,',','.') }}
                                            </td>

                                            <!--<td class="btn_proses" width="120px">-->
                                            <!--    <a class="btn btn-sm btn-info btn_order" id="btn_status_uji" style="padding: 6px 16px 6px 16px;" href="{{ route('status_pengujian.show', $dt->id) }}">PROSES</a>-->
                                            <!--</td>-->

                                            <td class="">
                                                <div class="btn-group dropup">
                                                    <button type="button" class="mb-0 px-3 border-radius-sm btn btn-info btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                                        Aksi
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        {{-- <a class="dropdown-item" href="{{ route('pengajuan_uji.edit', $dt->id) }}" data-bs-toggle="modal" data-bs-target="#editData">Edit</a> --}}
                                                        <a class="dropdown-item" href="{{ route('admin_produk_uji.edit', $dt->id) }}">Edit</a>

                                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#hapusData{{ $dt->id }}">Hapus</a>
                                                    </div>
                                                </div>
                                            </td>

                                            {{-- Modal Hapus --}}
                                            <div class="modal fade" id="hapusData{{ $dt->id }}">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">

                                                        <div class="modal-header">
                                                            <h5 class="modal-title"><strong>Hapus Data {{ $dt->kegiatan }} ?</strong></h5>
                                                        </div>

                                                        <div class="modal-body">
                                                            <form action="{{ route('admin_produk_uji.destroy', $dt->id) }}" method="POST" >
                                                                @csrf
                                                                @method('delete')

                                                                <div class="col text-center">
                                                                    <p>Tekan <strong>Hapus</strong> untuk menghapus data {{ $dt->kegiatan }} ! </p>

                                                                    <button class="btn btn-danger btn-sm" type="submit">Hapus</button>
                                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </tr>
                                        @php
                                        $total = $total + $dt->jumlah * $dt->parameter_uji->tarif;
                                        @endphp
                                        {{-- @endforeach --}}

                                        {{-- Modal Tindakan Edit--}}
                                        <div class="modal fade" id="editData">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">

                                                    {{-- @foreach($data_transaksi_produk as $data) --}}

                                                    <div class="modal-header">
                                                        <h5 class="modal-title"><strong>Edit Data Produk</strong></h5>
                                                    </div>

                                                    <div class="modal-body">
                                                        <form action="{{ route('produk_uji.update', $dt->id) }}" method="PUT">
                                                            @csrf

                                                            {{-- @foreach($data_edit_prod as $dt) --}}

                                                            <div class="form-group mt-3">
                                                                <input class="form-control" name="nama" id="nama" placeholder="Benda Uji" value="{{ $dt->nama }}" required>
                                                            </div>

                                                            {{-- <div class="col form-group mt-3">
                                                                <select name="id_jenis[]" class="form-select">
                                                                    <option value="">Pilih Jenis Pengujian</option>
                                                                    
                                                                </select>
                                                            </div>
                                    
                                                            <div class="col-md-5 form-group mt-3">
                                                                <select name="id_param_uji[]" id="id_param_uji[]" class="form-select">
                                                                    <option value="">Pilih Parameter Pengujian Dahulu..</option>
                                                                </select>
                                                            </div>
                                    
                                                            <div class="col-md-2 form-group mt-3">
                                                                <input type="number" class="form-control" min="1" name="jumlah_sampel[]" id="jumlah_sampel[]" placeholder="Jumlah" required>
                                                            </div> --}}

                                                            <div class="col text-center">
                                                                <button class="btn btn-danger btn-sm" type="submit">Simpan</button>
                                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                            </div>
                                                            {{-- @endforeach --}}

                                                        </form>
                                                    </div>
                                                    {{-- @endforeach --}}
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </tbody>

                                    <script>
                                        var status_uji = document.getElementById("btn_status_uji");

                                        // var status_uji = docuument.getElementsByClassName('btn_order');

                                        var status_bayar_dd = document.getElementById("status_bayar");

                                        // status_bayar_dd.click();
                                        $(document).ready(function() {
                                            // Tombol Proses set Inactive
                                            $('#status_bayar').click();
                                            $('#status_bayar').on('click', function a() {
                                                // get value of selected option, which is id of module
                                                let value = $(this).val();
                                                if (value == 'Sudah Dibayar') {
                                                    status_uji.className = "btn btn-sm btn-info";
                                                    status_uji.onclick = function() {
                                                        return true;
                                                    };
                                                } else {
                                                    status_uji.className = "btn btn-sm btn-secondary";
                                                    status_uji.onclick = function() {
                                                        return false;
                                                    };
                                                }
                                            });
                                        });
                                    </script>

                                </table>

                            </div>

                            {{-- <button type="submit">Simpan</button> --}}

                            <hr>
                            <div class="text-center">
                                <h3>Total: {{ "Rp " . number_format($total,0,',','.') }}</h3>
                            </div>
                            <hr>

                        <!--</form>-->
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