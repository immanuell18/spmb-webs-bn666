<!-- Footer Start -->
<div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-lg-3 col-md-6">
                <h4 class="text-white mb-3">SMK BAKTI NUSANTARA 666</h4>
                <p class="mb-2">Sekolah Menengah Kejuruan terakreditasi A yang menghasilkan lulusan berkarakter, kompeten, dan siap kerja.</p>
                <p class="mb-2"><strong>NPSN:</strong> 20666777</p>
                <p class="mb-2"><strong>Akreditasi:</strong> A (Sangat Baik)</p>
                <p class="mb-2"><strong>Kepala Sekolah:</strong><br>Drs. H. Budi Santoso, M.Pd</p>
            </div>
            <div class="col-lg-3 col-md-6">
                <h4 class="text-white mb-3">Kontak Kami</h4>
                <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>Jl. Pendidikan Nusantara No. 666<br>Kota Nusantara, Jawa Barat 12345</p>
                <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>(021) 666-7777</p>
                <p class="mb-2"><i class="fa fa-envelope me-3"></i>info@smkbaktinusantara666.sch.id</p>
                <p class="mb-2"><i class="fa fa-globe me-3"></i>www.smkbaktinusantara666.sch.id</p>
                <div class="d-flex pt-2">
                    <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-instagram"></i></a>
                    <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-youtube"></i></a>
                    <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <h4 class="text-white mb-3">Jurusan Kami</h4>
                <a class="btn btn-link text-light" href="{{ route('jurusan') }}"><i class="fa fa-laptop-code me-2"></i>PPLG - Pengembangan Perangkat Lunak dan Gim</a>
                <a class="btn btn-link text-light" href="{{ route('jurusan') }}"><i class="fa fa-calculator me-2"></i>AKT - Akuntansi dan Keuangan Lembaga</a>
                <a class="btn btn-link text-light" href="{{ route('jurusan') }}"><i class="fa fa-paint-brush me-2"></i>DKV - Desain Komunikasi Visual</a>
                <a class="btn btn-link text-light" href="{{ route('jurusan') }}"><i class="fa fa-video me-2"></i>ANM - Animasi</a>
                <a class="btn btn-link text-light" href="{{ route('jurusan') }}"><i class="fa fa-shopping-cart me-2"></i>BDP - Bisnis Daring dan Pemasaran</a>
            </div>
            <div class="col-lg-3 col-md-6">
                <h4 class="text-white mb-3">Galeri Sekolah</h4>
                <div class="row g-2 pt-2">
                    <div class="col-4">
                        <img class="img-fluid bg-light p-1" src="{{ asset('assets/images/lab-komputer.jpg') }}" alt="Lab Komputer">
                    </div>
                    <div class="col-4">
                        <img class="img-fluid bg-light p-1" src="{{ asset('assets/images/lab-akuntansi.jpg') }}" alt="Lab Akuntansi">
                    </div>
                    <div class="col-4">
                        <img class="img-fluid bg-light p-1" src="{{ asset('assets/images/studio-dkv.jpg') }}" alt="Studio DKV">
                    </div>
                    <div class="col-4">
                        <img class="img-fluid bg-light p-1" src="{{ asset('assets/images/perpustakaan.jpg') }}" alt="Perpustakaan">
                    </div>
                    <div class="col-4">
                        <img class="img-fluid bg-light p-1" src="{{ asset('assets/images/kegiatan-sekolah.jpg') }}" alt="Kegiatan">
                    </div>
                    <div class="col-4">
                        <img class="img-fluid bg-light p-1" src="{{ asset('assets/images/siswa-belajar.jpg') }}" alt="Siswa Belajar">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="copyright">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    &copy; 2025 <a class="border-bottom" href="{{ route('beranda') }}">SMK BAKTI NUSANTARA 666</a>. All Rights Reserved.<br>
                    Jl. Pendidikan Nusantara No. 666, Kota Nusantara, Jawa Barat 12345
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="footer-menu">
                        <a href="{{ route('beranda') }}">Beranda</a>
                        <a href="{{ route('beranda') }}">Cookies</a>
                        <a href="{{ route('beranda') }}">Bantuan</a>
                        <a href="{{ route('beranda') }}">FAQ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Footer End -->

<!-- Back to Top -->
<a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>