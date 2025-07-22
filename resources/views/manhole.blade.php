<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>{{ $service['data_konfig']->nama_sistem }}</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <link rel="icon" href="{{ asset($service['data_konfig']->logo) }}" type="image/x-icon">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Roboto:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <link href="{{ asset('tema_landing') }}/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('tema_landing') }}/assets/vendor/animate.css/animate.min.css" rel="stylesheet">
    <link href="{{ asset('tema_landing') }}/assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="{{ asset('tema_landing') }}/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('tema_landing') }}/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('tema_landing') }}/assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="{{ asset('tema_landing') }}/assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="{{ asset('tema_landing') }}/assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" integrity="sha512-vKMx8UnXk60zUwyUnUPM3HbQo8QfmNx7+ltw8Pm5zLusl1XIfwcxo8DbWCqMGKaWeNxWA8yrx5v3SaVpMvR3CA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link href="{{ asset('tema_landing') }}/assets/css/style.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body onload="javascript:hideTable()">

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
                    <li><a class="nav-link scrollto" href="{{ route('home') }}#hero">Beranda</a></li>
                    <li><a class="nav-link scrollto" href="{{ route('home') }}#lacak">Lacak</a></li>
                    <li class="dropdown">
                        <a href="#" class="nav-link scrollto active">
                            <span><strong>Layanan</strong></span>
                            <i class="bi bi-chevron-down"></i>
                        </a>
                        <ul>
                            <li><a class="nav-link" href="{{ route('manhole') }}">Manajemen Jaringan</a></li>
                            <li><a class="nav-link" href="https://stg-retripialam.simda.net/landing" target="_blank">Manajemen IPLT</a></li>
                            <li><a class="nav-link" href="https://klinikkonstruksi.jogjaprov.go.id/" target="_blank">Klinik Konstruksi</a></li>
                            <li><a class="nav-link scrollto" href="#jenis_pengujian">Pengujian Laboratorium</a></li>
                            <!-- Jika ingin menambahkan petunjuk penggunaan -->
                            <!-- <li><a class="nav-link" href="{{ asset($service['data_konfig']->petunjuk_penggunaan) }}" target="_blank">Petunjuk Penggunaan</a></li> -->
                        </ul>
                    </li>
                    <li><a class="nav-link scrollto" href="{{ route('home') }}#kontak">Kontak</a></li>
                </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>
            </nav>

            <a href="#buat_pengajuan" class="btn btn-sm btn-danger" style="margin-right: 8px; margin-left: 16px;"><span class="d-inline">ORDER</a>
            <a href="{{ route('login') }}" class="btn btn-sm btn-danger" style="margin-right: 8px;"><span class="d-inline">LOGIN</a>

        </div>
    </header><!-- End Header -->

    <main id="main">

        <section class="contact" style="margin-top: 91px;">

            <div>
                <div class='tableauPlaceholder' id='viz1748428806456' style='position: relative'><noscript><a href='#'><img alt='Visualisasi Manhole DIY ' src='https:&#47;&#47;public.tableau.com&#47;static&#47;images&#47;ma&#47;manholediy&#47;VisualisasiManholeDIY&#47;1_rss.png' style='border: none' /></a></noscript><object class='tableauViz' style='display:none;'>
                        <param name='host_url' value='https%3A%2F%2Fpublic.tableau.com%2F' />
                        <param name='embed_code_version' value='3' />
                        <param name='site_root' value='' />
                        <param name='name' value='manholediy&#47;VisualisasiManholeDIY' />
                        <param name='tabs' value='no' />
                        <param name='toolbar' value='yes' />
                        <param name='static_image' value='https:&#47;&#47;public.tableau.com&#47;static&#47;images&#47;ma&#47;manholediy&#47;VisualisasiManholeDIY&#47;1.png' />
                        <param name='animate_transition' value='yes' />
                        <param name='display_static_image' value='yes' />
                        <param name='display_spinner' value='yes' />
                        <param name='display_overlay' value='yes' />
                        <param name='display_count' value='yes' />
                        <param name='language' value='en-US' />
                    </object></div>
                <script type='text/javascript'>
                    var divElement = document.getElementById('viz1748428806456');
                    var vizElement = divElement.getElementsByTagName('object')[0];
                    if (divElement.offsetWidth > 800) {
                        vizElement.style.width = '100%';
                        vizElement.style.height = (divElement.offsetWidth * 0.75) + 'px';
                    } else if (divElement.offsetWidth > 500) {
                        vizElement.style.width = '100%';
                        vizElement.style.height = (divElement.offsetWidth * 0.75) + 'px';
                    } else {
                        vizElement.style.width = '100%';
                        vizElement.style.height = '977px';
                    }
                    var scriptElement = document.createElement('script');
                    scriptElement.src = 'https://public.tableau.com/javascripts/api/viz_v1.js';
                    vizElement.parentNode.insertBefore(scriptElement, vizElement);
                </script>
            </div>

        </section>

    </main><!-- End #main -->

    <!-- ======= Footer ======= -->
    <footer id="footer">
        <div class="container">
            <div class="copyright">
                &copy; 2022 - {{ date('Y', time()) }} Dikembangkan Oleh:
            </div>
            <div class="credits">
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