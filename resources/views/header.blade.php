<header style="background-color: #F2F5FC; position: sticky; top: 0; z-index: 9999;">
    <div class="container">
        <!-- Mobile & Tablet Header -->
        <div class="d-flex d-xl-none justify-content-between align-items-center py-3">
            <!-- Hamburger Button (Left) -->
            <button class="btn btn-link p-0 border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-controls="mobileMenu">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 12H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M3 6H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M3 18H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>

            <!-- Logo & Title (Center) -->
            <div class="d-flex flex-column align-items-center text-center">
                <img src="{{ asset('images/adn-hd-removebg-preview.png') }}" alt="Logo" class="logo mobile-logo" style="width: 40px; height: 40px;">
                <h6 class="fw-semibold mb-0 mobile-title" style="font-size: 0.75rem; line-height: 1.2;">Aliansi Dosen<br>Nahada</h6>
            </div>

            <!-- Login Button (Right) -->
            <button class="btn btn-primary btn-sm px-3" onclick="window.location.href='{{ route('login') }}'">
                Masuk
            </button>
        </div>

        <!-- Desktop Header (≥1200px) -->
        <div class="navbar d-none d-xl-flex justify-content-between align-items-center py-3">
            <!-- Logo & Title -->
            <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none" href="{{ url('/') }}">
                <img src="{{ asset('images/adn-hd-removebg-preview.png') }}" alt="Logo" class="logo" style="width: 48px; height: 48px;">
                <h6 class="fw-semibold mb-0">Aliansi Dosen Nahada</h6>
            </a>
    
            <!-- Navigation Menu -->
            <div class="navbar-menu">
                <ul class="nav gap-3 align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/#tentang') }}">Tentang Kami</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/divisi') }}">Divisi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('guest.publication') }}">Publikasi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('guest.news.list') }}">Berita</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/tridharma') }}">Tri Dharma PT</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/#kontak') }}">Kontak</a>
                    </li>
                    <li class="nav-item">
                        <button class="btn btn-primary" onclick="window.location.href='{{ route('login') }}'">Masuk</button>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Mobile/Tablet Offcanvas Menu -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
        <div class="offcanvas-header" style="background-color: #F2F5FC;">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ asset('images/adn-hd-removebg-preview.png') }}" alt="Logo" style="width: 32px; height: 32px;">
                <h6 class="offcanvas-title fw-semibold mb-0" id="mobileMenuLabel">Aliansi Dosen Nahada</h6>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="nav flex-column gap-2">
                <li class="nav-item">
                    <a class="nav-link px-0 py-2" href="{{ url('/') }}">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-0 py-2" href="{{ url('/#tentang') }}">Tentang Kami</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-0 py-2" href="{{ url('/divisi') }}">Divisi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-0 py-2" href="{{ route('guest.publication') }}">Publikasi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-0 py-2" href="{{ route('guest.news.list') }}">Berita</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-0 py-2" href="{{ url('/tridharma') }}">Tri Dharma PT</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-0 py-2" href="{{ url('/#kontak') }}">Kontak</a>
                </li>
            </ul>
        </div>
    </div>
</header>

<style>
/* Custom responsive styles */
@media (max-width: 575.98px) {
    /* Mobile styles */
    .mobile-logo {
        width: 36px !important;
        height: 36px !important;
    }
    
    .mobile-title {
        font-size: 0.7rem !important;
        margin-top: 2px;
    }
    
    .btn-sm {
        font-size: 0.8rem;
        padding: 0.4rem 0.8rem;
    }
}

@media (min-width: 576px) and (max-width: 767.98px) {
    /* Small tablets */
    .mobile-logo {
        width: 42px !important;
        height: 42px !important;
    }
    
    .mobile-title {
        font-size: 0.8rem !important;
        margin-top: 3px;
    }
}

@media (min-width: 768px) and (max-width: 1199.98px) {
    /* Tablets */
    .mobile-logo {
        width: 44px !important;
        height: 44px !important;
    }
    
    .mobile-title {
        font-size: 0.85rem !important;
        margin-top: 3px;
    }
}

/* Offcanvas customization */
.offcanvas {
    width: 280px !important;
}

.offcanvas-body .nav-link {
    color: #333;
    font-weight: 500;
    border-bottom: 1px solid #eee;
    transition: all 0.3s ease;
}

.offcanvas-body .nav-link:hover {
    color: #0d6efd;
    background-color: #f8f9fa;
    border-radius: 6px;
    border-bottom-color: transparent;
}

/* Header shadow transition */
header {
    transition: box-shadow 0.3s ease;
}

/* Custom hamburger button */
.btn-link {
    color: #333 !important;
    text-decoration: none !important;
}

.btn-link:hover {
    color: #0d6efd !important;
}

/* Ensure proper spacing for mobile header */
@media (max-width: 1199.98px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
}
</style>

<script>
    // Header shadow on scroll
    window.addEventListener('scroll', function () {
        const header = document.querySelector('header');
        if (window.scrollY > 10) {
            header.classList.add('shadow-sm');
        } else {
            header.classList.remove('shadow-sm');
        }
    });

    // Simple navigation handler for mobile menu
    document.addEventListener('DOMContentLoaded', function() {
        const mobileLinks = document.querySelectorAll('#mobileMenu .nav-link');
        
        mobileLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                
                // Untuk link dengan hash, tutup offcanvas terlebih dahulu
                if (href.includes('#')) {
                    e.preventDefault();
                    
                    // Tutup offcanvas secara manual
                    const offcanvasElement = document.getElementById('mobileMenu');
                    const backdrop = document.querySelector('.offcanvas-backdrop');
                    
                    if (offcanvasElement) {
                        offcanvasElement.classList.remove('show');
                        document.body.classList.remove('offcanvas-open');
                        document.body.style.overflow = '';
                        document.body.style.paddingRight = '';
                        
                        if (backdrop) {
                            backdrop.remove();
                        }
                    }
                    
                    // Navigasi setelah delay
                    setTimeout(() => {
                        window.location.href = href;
                    }, 300);
                } else {
                    // Untuk link biasa, tutup offcanvas kemudian navigasi
                    const offcanvasElement = document.getElementById('mobileMenu');
                    const backdrop = document.querySelector('.offcanvas-backdrop');
                    
                    if (offcanvasElement) {
                        offcanvasElement.classList.remove('show');
                        document.body.classList.remove('offcanvas-open');
                        document.body.style.overflow = '';
                        document.body.style.paddingRight = '';
                        
                        if (backdrop) {
                            backdrop.remove();
                        }
                    }
                    
                    // Navigasi normal untuk link biasa
                    // Biarkan default behavior berjalan
                }
            });
        });
    });
</script>