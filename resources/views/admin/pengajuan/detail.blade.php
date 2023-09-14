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
                @if(Session::has('success'))
                <div class="alert alert-success">
                    {{ Session('success') }}
                </div>
                @endif
                <div class="card">

                    <div class="card-body">
                        {{-- <form class="row mt-3" method="POST" action="{{ route('produk_uji.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group col-md-6" hidden>
                            <label class="">ID Transaksi</label>
                            <div class="">
                                <input name="id_transaksi" value="{{ $all_data->id }}" placeholder="ID Transaksi..." type="number" class="form-control form-control-normal">
                            </div>
                        </div>

                        <div class="form-group col-md-12">
                            <label class="">Pilih Parameter Uji:</label>
                            <select class="form-select" data-control="select2" name="id_parameter_uji" id="">
                                @foreach($data_parameter_uji as $parameter_uji)
                                <option value="{{ $parameter_uji->id }}">{{ $parameter_uji->nama . ' | Rp ' . number_format($parameter_uji->tarif,0,',','.') . ' | ' . $parameter_uji->satuan . ' | ' . $parameter_uji->kode_sni }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-12">
                            <label class="">Nama Sampel Produk</label>
                            <div class="">
                                <input placeholder="Nama Sampel Produk..." name="nama" type="text" class="form-control form-control-normal">
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="">Jumlah</label>
                            <div class="">
                                <input placeholder="Jumlah..." name="jumlah" type="hidden" class="form-control form-control-normal" value="1">
                            </div>
                        </div>

                        <div class="form-group col-sm-12">
                            <button type="submit" class="btn btn-primary"><i class="far fa-save" style="margin-right: 8px;"></i> Tambah Parameter Uji </button>
                        </div>

                        </form> --}}

                        @php
                        $no = 1;
                        @endphp

                        <div class="table-responsive">
                            <table id="example" class="table table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        {{-- <th>No. Order</th>
                                        <th>Kode Sampel</th> --}}
                                        <th>Nama Produk</th>
                                        <th>Parameter Uji</th>
                                        <th>Tarif</th>
                                        <th>Jumlah</th>
                                        <th>Harga</th>
                                        <th>Satuan</th>
                                        <th>Kode SNI</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($all_data as $dt)
                                    <tr>
                                        <td><button class="btn btn-secondary btn-sm">{{ $no++ }}</button></td>
                                        {{-- <td>{{ '604.1/' . $dt->id }}</td>
                                        <td>{{ $dt->id . '/BB/' . date('Y', strtotime($dt->created_at)) }}</td> --}}
                                        {{-- <td>
                                            @if($dt->no_order != NULL)
                                            {{ $dt->no_order }}
                                        @else

                                        @endif
                                        </td>
                                        <td>
                                            @if($dt->kode_sampel != NULL)
                                            {{ $dt->kode_sampel }}
                                            @else

                                            @endif
                                        </td> --}}
                                        <td>{{ $dt->nama }}</td>
                                        <td>{{ $dt->parameter_uji->nama }}</td>
                                        <td>{{ $dt->parameter_uji->tarif }}</td>
                                        <td>{{ $dt->jumlah }}</td>
                                        <td>{{ $dt->parameter_uji->tarif * $dt->jumlah }}</td>
                                        <td>{{ $dt->parameter_uji->satuan }}</td>
                                        <td>{{ $dt->parameter_uji->kode_sni }}</td>

                                        <td class="">
                                            <div class="btn-group dropup">
                                                <button type="button" class="mb-0 px-3 border-radius-sm btn btn-info btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                                    Aksi
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#hapusData{{ $dt->id }}">Hapus</a>
                                                </div>
                                            </div>
                                        </td>

                                        <div class="modal fade" id="hapusData{{ $dt->id }}">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">

                                                    <div class="modal-header">
                                                        <h5 class="modal-title"><strong>Hapus Data {{ $dt->nama }} ?</strong></h5>
                                                    </div>

                                                    <div class="modal-body">
                                                        <form action="{{ route('produk_uji.destroy', $dt->id) }}" method="POST">
                                                            @csrf
                                                            @method('delete')

                                                            <div class="col text-center">
                                                                <p>Tekan <strong>Hapus</strong> untuk menghapus data {{ $dt->nama }} ! </p>

                                                                <button class="btn btn-danger btn-sm" type="submit">Hapus</button>
                                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </tr>
                                    @endforeach
                                </tbody>

                            </table>

                            {{-- <a href="{{ route('cetak_permintaan_pengujian.show', $all_data->id) }}" target="_blank" class="btn btn-success float-right"><i class="fa fa-download" style="margin-right: 8px;"></i> Unduh Permintaan Pengujian </a> --}}
                        </div>
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