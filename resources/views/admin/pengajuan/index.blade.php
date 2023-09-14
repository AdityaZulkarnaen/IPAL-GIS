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
                        <a href="{{ route('pengajuan.create') }}" class="btn btn-sm btn-primary">
                            <span class="svg-icon svg-icon-2">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor" />
                                    <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor" />
                                </svg>
                            </span>
                            {{ 'Tambah ' . $subtitle }}
                        </a>

                        @php
                        $no = 1;
                        @endphp
                        <div class="table-responsive">
                            <table id="example" class="table table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>No. Order</th>
                                        <th>Kode Sampel</th>
                                        <th>Kegiatan</th>
                                        <th>Sumber</th>
                                        <th>Jenis Pengujian</th>
                                        <th>Parameter Uji</th>
                                        <th>Status Bayar</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($all_data as $dt)
                                    <tr>
                                        <td><button class="btn btn-secondary btn-sm" style="padding: 6px 12px 6px 12px;">{{ $no++ }}</button></td>
                                        <td>{{ date('d-m-Y', strtotime($dt->created_at)) }}</td>
                                        <td>{{ $dt->no_order }}</td>
                                        <td>{{ $dt->kode_sampel }}</td>
                                        <td>{{ $dt->kegiatan }}</td>
                                        <td>{{ $dt->sumber }}</td>
                                        <td>{{ $dt->nama }}</td>
                                        <td><a href="{{ route('pengajuan.show', $dt->id) }}" class="text-primary" style="font-style: italic;"><u>Lihat Detail</u></a></td>

                                        <td>
                                            <i class="
                                            @if($dt->status_bayar=='Sudah Dibayar') text-success 
                                            @elseif($dt->status_bayar=='Belum Dibayar') text-danger 
                                            @else text-danger 
                                            @endif"><u><strong>{{ $dt->status_bayar }}</strong></u></i>
                                            @if($dt->status_bayar==NULL)
                                                <i class="text-danger"><u><strong>Belum Dibayar</strong></u></i>
                                            @endif
                                            <br>
                                            @if($dt->status_bayar=='Ditolak')
                                            <i>
                                                <small>
                                                    <strong>Catatan:</strong>
                                                    {{ $dt->catatan }}
                                                </small>
                                            </i>
                                            @endif
                                        </td>

                                        <td class="">
                                            <div class="btn-group dropup">
                                                <button type="button" class="mb-0 px-3 border-radius-sm btn btn-info btn-sm dropdown-toggle" data-bs-toggle="dropdown" style="padding: 6px 12px 6px 12px;">
                                                    Aksi
                                                </button>
                                                <div class="dropdown-menu">
                                                    {{-- <a class="dropdown-item" href="{{ route('pengajuan.edit', $dt->id) }}">Ubah</a> --}}
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#hapusData{{ $dt->id }}">Hapus</a>
                                                </div>
                                            </div>
                                        </td>

                                        <div class="modal fade" id="hapusData{{ $dt->id }}">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">

                                                    <div class="modal-header">
                                                        <h5 class="modal-title"><strong>Hapus Data {{ $dt->kegiatan }} ?</strong></h5>
                                                    </div>

                                                    <div class="modal-body">
                                                        <form action="{{ route('pengajuan.destroy', $dt->id) }}" method="POST">
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
                                    @endforeach
                                </tbody>

                            </table>
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