<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>{{ $service['data_konfig']->nama_sistem }}</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link rel="icon" href="{{ asset($service['data_konfig']->logo) }}" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Roboto:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('tema_landing') }}/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('tema_landing') }}/assets/vendor/animate.css/animate.min.css" rel="stylesheet">
    <link href="{{ asset('tema_landing') }}/assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="{{ asset('tema_landing') }}/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('tema_landing') }}/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('tema_landing') }}/assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="{{ asset('tema_landing') }}/assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="{{ asset('tema_landing') }}/assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" integrity="sha512-vKMx8UnXk60zUwyUnUPM3HbQo8QfmNx7+ltw8Pm5zLusl1XIfwcxo8DbWCqMGKaWeNxWA8yrx5v3SaVpMvR3CA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Template Main CSS File -->
    <link href="{{ asset('tema_landing') }}/assets/css/style.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css"> -->

    <!-- =======================================================
  * Template Name: Medicio
  * Updated: Mar 10 2023 with Bootstrap v5.2.3
  * Template URL: https://bootstrapmade.com/medicio-free-bootstrap-theme/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body onload="javascript:hideTable()">

    <!-- ======= Top Bar ======= -->
    <!-- <div id="topbar" class="d-flex align-items-center fixed-top">
        <div class="container d-flex align-items-center justify-content-center justify-content-md-between">
            <div class="align-items-center d-none d-md-flex">
                <i class="bi bi-arrow-up-right-square"></i>
                <small>Selamat Datang di {{ $service['data_konfig']->nama_sistem }} | {{ $service['data_konfig']->nama_instansi }}</small>
            </div>
            <div class="d-flex align-items-center">
                <i class="bi bi-phone"></i>
                <small>CS : {{ $service['data_konfig']->kontak }}</small>
            </div>
        </div>
    </div> -->

    <!-- ======= Header ======= -->
    <header id="header" class="fixed-top" style="margin-top: 0px auto; padding: 16px 16px 16px 16px;">
        <div class="d-flex align-items-center">

            <a href="#" class="me-auto">
                <div class=" d-flex">
                    <img class="" style="height: 64px; margin-right: 8px;" src="{{ asset($service['data_konfig']->logo) }}" alt="">
                    <strong class="d-none d-md-block align-self-center" style="color:#333333; font-size: 16px; margin-left: 8px;">{{ $service['data_konfig']->nama_sistem }}</strong>
                </div>
            </a>

            <!-- Uncomment below if you prefer to use an image logo -->
            <!-- <h1 class="logo me-auto"><a href="index.html">Medicio</a></h1> -->

            <nav id="navbar" class="navbar order-last order-lg-0">
                <ul>
                    <li><a class="nav-link scrollto " href="#hero">Beranda</a></li>
                    <li><a class="nav-link scrollto" href="#lacak">Lacak</a></li>
                    <li><a class="nav-link scrollto" href="#jenis_pengujian">Jenis Pengujian</a></li>
                    <!-- <li><a class="nav-link scrollto" href="{{ asset($service['data_konfig']->petunjuk_penggunaan) }}" target="_blank">Petunjuk Penggunaan</a></li> -->
                    <li><a class="nav-link scrollto" href="{{ 'https://klinikkonstruksi.jogjaprov.go.id/' }}" target="_blank">Klinik Konstruksi</a></li>
                    <li><a class="nav-link scrollto" href="#kontak">Kontak</a></li>
                    <!-- <li><a class="nav-link scrollto" href="#kontak">ORDER</a></li>
                    <li><a class="nav-link scrollto" href="#kontak">LOGIN</a></li> -->
                    <!-- <li><a class="nav-link scrollto" href="#contact">Contact</a></li> -->
                </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>
            </nav><!-- .navbar -->

            <a href="#buat_pengajuan" class="btn btn-sm btn-danger" style="margin-right: 8px; margin-left: 16px;"><span class="d-inline">ORDER</a>
            <a href="{{ route('login') }}" class="btn btn-sm btn-danger" style="margin-right: 8px;"><span class="d-inline">LOGIN</a>

        </div>
    </header><!-- End Header -->

    <!-- ======= Hero Section ======= -->
    <section id="hero">
        <div id="heroCarousel" data-bs-interval="5000" class="carousel slide carousel-fade" data-bs-ride="carousel">

            <ol class="carousel-indicators" id="hero-carousel-indicators"></ol>

            <div class="carousel-inner" role="listbox">

                @php
                $active = 0;
                @endphp

                @foreach($data_banner as $dt_banner)

                <div class="carousel-item @if($active == 0) active @endif" style="background-image: url('{{ asset($dt_banner->gambar) }}')">
                    <!-- <div class="container"> -->
                    <!-- <p>{{ $active }}</p> -->
                    <!-- </div> -->
                    @php
                    $active = 1;
                    @endphp
                </div>
                @endforeach
            </div>

            <a class="carousel-control-prev" href="#heroCarousel" role="button" data-bs-slide="prev">
                <span class="carousel-control-prev-icon bi bi-chevron-left" aria-hidden="true"></span>
            </a>

            <a class="carousel-control-next" href="#heroCarousel" role="button" data-bs-slide="next">
                <span class="carousel-control-next-icon bi bi-chevron-right" aria-hidden="true"></span>
            </a>

        </div>
    </section><!-- End Hero -->

    <main id="main">

        <!-- ======= Cta Section ======= -->
        <section id="lacak" class="cta">
            <div class="container" data-aos="zoom-in">

                <div class="text-center">
                    <h3>LACAK PESANAN ANDA!</h3>
                    <p> Masukan kode sampel anda disini!</p>
                    <br><br>
                    <form class="text-center" method="GET" action="">
                        <center>
                            <div class="col-md-4 form-group">
                                <input type="text" value="{{ $param_kode_sample }}" name="kode_sampel" class="form-control text-center" id="kode_sampel" placeholder="Kode Sampel" required>
                            </div>
                        </center>
                        <br>
                        <button class="btn btn-outline-light" type="submit">LACAK PENGUJIAN</button>

                        {{-- <button type="button" class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                            LACAK PENGUJIAN
                        </button> --}}
                    </form>
                </div>
                <div class="m-4" id="table_lacak">
                    <table class="table table-light table-bordered m-auto rounded rounded-3 overflow-hidden" style="width: 50%; border-radius:30px">
                        <thead>
                            <tr>
                                <th scope="col">Status Pengujian</th>
                                <th scope="col">Waktu Pengujian</th>
                            </tr>
                        </thead>
                        <tbody>

                            {{-- @if($dt_lacak->tanggal == null)
                            @foreach($status_sama as $dt_lacak_all)
                            <tr>
                                <th><strong><li style="color: rgb(156, 157, 158)"> {{ $dt_lacak_all->nama }}</strong><br></th>
                            <td><small><i> - </i></u></small></td>
                            </tr>
                            @endforeach
                            @endif --}}


                            @foreach($status_pengujian as $dt_lacak)
                            {{-- @if($dt_lacak->tanggal != null) --}}
                            <tr>
                                <td><strong>
                                        <li style="color: rgb(0, 0, 0)"> {{ $dt_lacak->catatan }}
                                    </strong><br></th>
                                <td><small><i> {{ date('d-m-Y', strtotime($dt_lacak->created_at)) }}</i></small></td>
                            </tr>
                            {{-- @endif --}}
                            @endforeach

                        </tbody>

                        <script>
                            var kode_sampel = document.getElementById('kode_sampel');

                            function hideTable() {
                                if (kode_sampel.value.length == 0) {
                                    document.getElementById('table_lacak').style.visibility = "hidden";
                                }
                            }
                        </script>

                    </table>
                </div>

            </div>
        </section><!-- End Cta Section -->

        <!-- Modal -->
        <div class="modal fade" id="staticBackdrop" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Progress Pesanan</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col">Status Pengujian</th>
                                        <th scope="col">Waktu Pengujian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($status_pengujian as $dt_lacak)
                                    <tr>

                                        <th><strong>
                                                <li style="color: rgb(156, 157, 158)"> {{ $dt_lacak->nama_status }}
                                            </strong><br></th>
                                        <td><small><u><i>Tgl: {{ date('Y-m-d', strtotime($dt_lacak->tanggal)) }}</i></u></small></td>
                                    </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        {{-- <button type="button" class="btn btn-primary">Understood</button> --}}
                    </div>
                </div>
            </div>
        </div>

        <!-- ======= Departments Section ======= -->
        <section id="jenis_pengujian" class="departments">
            <div class="container" data-aos="fade-up">

                <div class="section-title">
                    <h2>Jenis Pengujian</h2>
                    <p>Berikut adalah jenis pengujian yang dapat anda pilih.</p>
                </div>

                <div class="row" data-aos="fade-up" data-aos-delay="100">
                    <div class="col-lg-4 mb-5 mb-lg-0">
                        <ul class="nav nav-tabs flex-column">

                            @php
                            $status_jenis = 0;
                            @endphp

                            @foreach($data_jenis_pengujian as $dt_jenis)
                            <li class="nav-item">
                                <a class="d-flex nav-link @if($status_jenis == 0) @php $status_jenis = 1; @endphp active show @endif" data-bs-toggle="tab" data-bs-target="#tab-{{ $dt_jenis->id }}">
                                    <img class="" style="margin-right: 16px;" src="{{ asset($dt_jenis->icon) }}" alt="" width="36px">
                                    <strong class="align-middle my-auto">{{ $dt_jenis->nama }}</strong>
                                    <!-- {!! $dt_jenis->deskripsi !!} -->
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-lg-8 card">
                        <div class="tab-content">

                            @php
                            $status_jenis_d = 0;
                            @endphp

                            @foreach($data_jenis_pengujian as $dt_jenis_d)
                            <div class="tab-pane @if($status_jenis_d == 0) @php $status_jenis_d = 1; @endphp active show @endif" id="tab-{{ $dt_jenis_d->id }}">
                                <h3 style="font-size: 18px;" class="mt-3">{{ $dt_jenis_d->nama }}</h3>
                                <div width="100%" style="height: 592px; overflow-y: scroll; padding:10px; border-style: solid; border-width: 0px;">
                                    <table id="example1" class="table table-sm table-striped">
                                        <!-- <thead class="bg-danger" style="position: sticky; top: 0; z-index: 1;">
                                            <tr>
                                                <th class="text-light">Data Parameter Uji</th>
                                            </tr>
                                        </thead> -->
                                        <tbody>
                                            @foreach($dt_jenis_d->parameter_uji as $dt_param_uji)
                                            <tr>
                                                <td>
                                                    <ul style="padding: 0 auto; margin: 0 auto;">
                                                        <li>
                                                            {{ $dt_param_uji->nama }} ({{ $dt_param_uji->kode_sni }})
                                                            <br>
                                                            {{ "Rp " . number_format($dt_param_uji->tarif,0,',','.') }} ({{ $dt_param_uji->satuan }})
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </section><!-- End Departments Section -->

        <!-- ======= Appointment Section ======= -->
        <section id="buat_pengajuan" class="appointment bg-danger">
            <div class="container" data-aos="fade-up">

                <div class="section-title">
                    <h2 class="text-light ">Order</h2>
                    <p class="text-light ">Isi form berikut dengan benar dan lengkap!</p>

                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>

                <form method="POST" action="{{ route('pengajuan.store') }}" enctype="multipart/form-data" target="_blank">
                    @csrf

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <input type="text" name="name" class="form-control" id="name" placeholder="Instansi / Nama Lengkap" required>
                        </div>
                        <div class="col-md-4 form-group mt-3 mt-md-0">
                            <input type="email" class="form-control" name="email" id="email" placeholder="Email" required>
                        </div>
                        <div class="col-md-4 form-group mt-3 mt-md-0">
                            <input type="number" class="form-control" min="1" name="nomor_hp" id="nomor_hp" placeholder="No. HP" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group mt-3">
                            <input type="text" name="alamat" class="form-control" id="alamat" placeholder="Alamat" required>
                        </div>
                        <div class="col-md-4 form-group mt-3">
                            <input type="text" name="kegiatan" class="form-control" id="kegiatan" placeholder="Jenis Pekerjaan" required>
                        </div>

                        <div class="col-md-4 form-group mt-3">
                            <select name="sumber" id="sumber" class="form-select" onchange="toggleInput()">
                                <option value="">Sumber Pembiayaan..</option>
                                <option value="APBN">APBN</option>
                                <option value="APBD Provinsi">APBD Provinsi</option>
                                <option value="APBD Kabupaten/Kota">APBD Kabupaten/Kota</option>
                                <option value="Swasta">Swasta</option>
                                <option value="Perorangan">Perorangan</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group mt-3" id="no_dok" style="display: none;">
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
                    </div>

                    <hr class="text-light">
                    <div class="text-light text-center mb-3">
                        <h5>Tambah Parameter Uji</h5>
                        <small>Tambahkan sesuai dengan yang dibutuhkan. Dapat lebih dari 1 dengan menekan tombol <strong>"Tambah"</strong></small>
                    </div>

                    <div class="row after-add-more">
                        <div class="col-md-4 form-group mt-3">
                            <select name="id_jenis[]" onchange="getParamUji(this)" class="form-select">
                                <option value="">Pilih Jenis Pengujian</option>
                                @foreach ($data_jenis_pengujian as $dt)
                                <option value="{{ $dt->id }}">{{ $dt->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 form-group mt-3">
                            <select name="id_param_uji[]" id="id_param_uji[]" class="form-select">
                                <option value="">Pilih Parameter Pengujian Dahulu..</option>
                            </select>
                        </div>

                        {{-- <div class="col-md-3 form-group mt-3"> --}}
                        <input type="hidden" class="form-control" name="nama_produk[]" value="" id="nama_produk[]" placeholder="Nama Produk" required>
                        {{-- </div> --}}

                        <div class="col-md-1 form-group mt-3">
                            <input type="number" class="form-control" name="jumlah_sampel[]" min="1" id="jumlah_sampel[]" placeholder="Jumlah" required style="font-size: 0.9rem">
                        </div>

                        <div class="col-md-1 form-group mt-3">
                            <button class="btn btn-warning add-more" type="button">Tambah</button>
                        </div>
                    </div>

                    <!-- <div class="form-group mt-3">
                        <textarea class="form-control" name="message" rows="5" placeholder="Catatan..."></textarea>
                    </div> -->
                    <!-- <div class="my-3">
                        <div class="loading">Loading</div>
                        <div class="error-message"></div>
                        <div class="sent-message">Pengajuan Pengujian Anda Telah Terkirim!</div>
                    </div> -->
                    <div class="text-center mt-2">
                        <button type="submit" class="btn btn-warning mt-3 col-md-4">ORDER</button>
                    </div>
                </form>

                <div class="copy invisible" data-index="" hidden>
                    <div class="row">
                        <div class="col-md-4 form-group mt-3">
                            <select name="id_jenis[]" onchange="getParamUji(this)" class="form-select">
                                <option value="">Pilih Jenis Pengujian</option>
                                @foreach ($data_jenis_pengujian as $dt)
                                <option value="{{ $dt->id }}">{{ $dt->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 form-group mt-3">
                            <select name="id_param_uji[]" id="id_param_uji[]" class="form-select">
                                <option value="">Pilih Parameter Pengujian Dahulu..</option>
                            </select>
                        </div>

                        {{-- <div class="col-md-3 form-group mt-3"> --}}
                        <input type="hidden" class="form-control" name="nama_produk[]" id="nama_produk[]" placeholder="Nama Produk" value=" " required>
                        {{-- </div> --}}

                        <div class="col-md-1 form-group mt-3">
                            <input type="number" class="form-control" min="1" name="jumlah_sampel[]" id="jumlah_sampel[]" placeholder="Jumlah" required style="font-size: 0.9rem">
                        </div>

                        <div class="col-md-1 form-group mt-3">
                            <button class="btn btn-warning remove" type="button">
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
        </section><!-- End Appointment Section -->

        <!-- ======= Contact Section ======= -->
        <section id="kontak" class="contact">
            <div class="container">

                <div class="section-title">
                    <h2>Kontak</h2>
                    <p>Jabat erat dapat dilakukan disini.</p>
                </div>

            </div>

            <div>
                <!-- <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15812.57480477484!2d110.42240746977538!3d-7.774583100000003!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a5a1e97ec5713%3A0x7baa03d8ace53729!2sBalai%20Pengembangan%20Jasa%20Konstruksi%20(BPJK)!5e0!3m2!1sid!2sid!4v1680105577008!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe> -->
                <iframe style="border:0; width: 100%; height: 350px;" src="{{ $service['data_konfig']->maps }}" frameborder="0" allowfullscreen></iframe>
                <!-- <iframe style="border:0; width: 100%; height: 350px;" src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15812.57480477484!2d110.42240746977538!3d-7.774583100000003!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a5a1e97ec5713%3A0x7baa03d8ace53729!2sBalai%20Pengembangan%20Jasa%20Konstruksi%20(BPJK)!5e0!3m2!1sid!2sid!4v1680105577008!5m2!1sid!2sid" frameborder="0" allowfullscreen></iframe> -->
            </div>

            <div class="container">

                <div class="row mt-5">

                    <div class="col-lg-6">

                        <div class="row">
                            <div class="col-md-12" hidden>
                                <div class="info-box">
                                    <i class="bx bx-map"></i>
                                    <h3>Alamat</h3>
                                    <p>{{ $service['data_konfig']->alamat }}</p>
                                </div>
                            </div>
                            <!-- <div class="col-md-6">
                                <div class="info-box mt-4">
                                    <i class="bx bx-envelope"></i>
                                    <h3>Email Us</h3>
                                    <p>info@example.com<br>contact@example.com</p>
                                </div>
                            </div> -->
                            <div class="col-md-6">
                                <div class="info-box">
                                    <i class='bx bxl-chrome'></i>
                                    <h3>Website</h3>
                                    <a href="{{ $service['data_konfig']->website }}" target="_blank">{{ $service['data_konfig']->website }}</a>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-box">
                                    <i class='bx bxl-youtube'></i>
                                    <h3>Youtube</h3>
                                    <a href="{{ $service['data_konfig']->youtube }}" target="_blank">{{ $service['data_konfig']->youtube }}</a>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-lg-6">
                        <!-- <form action="forms/contact.php" method="post" role="form" class="php-email-form">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <input type="text" name="nama" class="form-control" id="nama" placeholder="Nama Anda" required="">
                                </div>
                                <div class="col-md-6 form-group mt-3 mt-md-0">
                                    <input type="email" class="form-control" name="email" id="email" placeholder="Email" required="">
                                </div>
                            </div>
                            <div class="form-group mt-3">
                                <input type="text" class="form-control" name="keperluan" id="keperluan" placeholder="Keperluan" required="">
                            </div>
                            <div class="form-group mt-3">
                                <textarea class="form-control" name="message" rows="7" placeholder="Message" required=""></textarea>
                            </div>
                            <div class="my-3">
                                <div class="loading">Loading</div>
                                <div class="error-message"></div>
                                <div class="sent-message">Pesan Anda telah dikirim. Terima kasih!</div>
                            </div>
                            <div class="text-center"><button type="submit">Kirim Pesan</button></div>
                        </form> -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="info-box">
                                    <!-- <i class="bx bxl-instagram"></i>
                                    <h3>Instagram</h3> -->
                                    <iframe class="col-md-12 px-5" height="300" src="{{ $service['data_konfig']->instagram }}" frameborder="0"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </section><!-- End Contact Section -->

    </main><!-- End #main -->

    <!-- ======= Footer ======= -->
    <footer id="footer">
        <div class="container">
            <div class="copyright">
                &copy; 2022 - {{ date('Y', time()) }} Dikembangkan Oleh:
            </div>
            <div class="credits">
                <!-- All the links in the footer should remain intact. -->
                <!-- You can delete the links only if you purchased the pro version. -->
                <!-- Licensing information: https://bootstrapmade.com/license/ -->
                <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/medicio-free-bootstrap-theme/ -->
                {{ $service['data_konfig']->nama_sistem }} <a href="#">{{ $service['data_konfig']->nama_instansi }}</a>
            </div>
        </div>
    </footer><!-- End Footer -->

    <div id="preloader"></div>
    <a href="https://api.whatsapp.com/send?phone={{ $service['data_konfig']->kontak }}&text=" target="_blank" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-whatsapp"></i></a>

    <!-- Vendor JS Files -->
    <script src="{{ asset('tema_landing') }}/assets/vendor/purecounter/purecounter_vanilla.js"></script>
    <script src="{{ asset('tema_landing') }}/assets/vendor/aos/aos.js"></script>
    <script src="{{ asset('tema_landing') }}/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('tema_landing') }}/assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="{{ asset('tema_landing') }}/assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="{{ asset('tema_landing') }}/assets/vendor/php-email-form/validate.js"></script>

    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    @if (Session::has('message'))

    <script>
        toastr.success("{{ Session::get('message') }}");
    </script>

    @endif

    <!-- Template Main JS File -->
    <script src="{{ asset('tema_landing') }}/assets/js/main.js"></script>
    <script>
        $(document).ready(function() {
            $('#example').DataTable();
        });
    </script>

</body>

</html>