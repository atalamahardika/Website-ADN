<header style="background-color: #F2F5FC; position: sticky; top: 0; z-index: 9999;">
    <div class="container">
        <div class="navbar d-flex justify-content-between align-items-center">
            <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none" href="{{ url('/')}}">
                <img src="{{ asset('images/adn-hd-removebg-preview.png') }}" alt="Logo" class="logo" style="width: 48px; height: 48px;">
                <h6 class="fw-semibold mb-0" >Aliansi Dosen Nahada</h6>
            </a>
    
            <div class="navbar-menu">
                <ul class="nav gap-3">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/')}}">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/#tentang')}}">Tentang Kami</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/divisi')}}">Divisi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('guest.publication') }}">Publikasi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('guest.news.list') }}">Berita</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/tridharma')}}">Tri Dharma PT</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/#kontak')}}">Kontak</a>
                    </li>
                    <li class="nav-item">
                        <button class="btn btn-primary" onclick="window.location.href='{{ route('login') }}'">Masuk</button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>

<script>
    window.addEventListener('scroll', function () {
        const header = document.querySelector('header');
        if (window.scrollY > 10) {
            header.classList.add('shadow-sm');
        } else {
            header.classList.remove('shadow-sm');
        }
    });
</script>
