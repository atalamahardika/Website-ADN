@include('head')

<body>
    <div class="flex h-screen">
        <x-layout.sidebar />
        <x-layout.content-bar :title="$title" :subtitle="$subtitle">
            {{-- Slot utama di sini, bisa diganti sesuai konten --}}
            @includeWhen($title === 'Landing Page', 'components.content.main')

            {{-- Tulis kode di bawah ini untuk isi kontennya --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>List Section</h4>
                <button class="btn btn-primary"
                    onclick="document.getElementById('modalTambahSection').classList.remove('hidden')">
                    Tambah
                </button>
            </div>

            {{-- Modal Tambah --}}
            <x-modal id="modalTambahSection" title="Tambah Section">
                <form action="{{ route('superadmin.landing.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    {{-- Section --}}
                    <div class="mb-4">
                        <label for="section" class="block font-semibold">Nama Section</label>
                        <input type="text" name="section" id="section" placeholder="Contoh: hero, about, contact"
                            class="form-control" required>
                    </div>

                    {{-- Key --}}
                    <div class="mb-4">
                        <label for="key" class="block font-semibold">Key</label>
                        <select name="key" id="key" class="form-control" required
                            onchange="handleKeyChange()">
                            <option value="">-- Pilih Key --</option>
                            <option value="title">Title</option>
                            <option value="content">Content</option>
                            <option value="image">Image</option>
                        </select>
                    </div>

                    {{-- Value --}}
                    <div id="value-field" class="mb-4"></div>

                    {{-- Preview Gambar --}}
                    <div id="image-preview-container" class="mb-4 hidden">
                        <label class="block font-semibold">Preview Gambar</label>
                        <img id="image-preview" src="#" class="max-w-sm mt-2 border rounded"
                            alt="Preview Gambar" />
                    </div>

                    {{-- Icon (opsional) --}}
                    <div class="mb-4">
                        <label for="icon" class="block font-semibold">Upload Icon (opsional)</label>
                        <input type="file" name="icon" id="icon" class="form-control"
                            accept="image/png, image/jpeg, image/jpg">
                        <span class="text-muted small">Format yang didukung JPG, JPEG, dan PNG dengan maksimal ukuran
                            file 2MB.</span>
                    </div>

                    {{-- Preview Icon --}}
                    <div id="icon-preview-container" class="mb-4 hidden">
                        <label class="block font-semibold">Preview Icon</label>
                        <img id="icon-preview" src="#" class="max-w-xs mt-2 border rounded" alt="Preview Icon" />
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-success w-full">Simpan</button>
                    </div>
                </form>
            </x-modal>

            {{-- List Section --}}
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Section</th>
                            <th>Key</th>
                            <th>Value</th>
                            <th>Icon</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sections as $index => $section)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $section->section }}</td>
                                <td>{{ $section->key }}</td>
                                <td>
                                    @if ($section->key === 'image')
                                        <img src="{{ asset('storage/' . $section->value) }}" width="100"
                                            class="rounded shadow">
                                    @elseif ($section->key === 'content')
                                        <div class="prose">
                                            {!! Str::limit(strip_tags($section->value), 50) !!}
                                        </div>
                                    @else
                                        {{ $section->value }}
                                    @endif
                                </td>
                                <td>
                                    @if ($section->icon)
                                        <img src="{{ asset('storage/' . $section->icon) }}" width="40"
                                            class="rounded">
                                    @else
                                        <span class="text-muted">Tidak ada</span>
                                    @endif
                                </td>
                                <td class="flex gap-2">
                                    {{-- Tombol Edit --}}
                                    <button class="btn btn-warning"
                                        onclick="openEditModal(
                                                {{ $section->id }},
                                                '{{ $section->section }}',
                                                '{{ $section->key }}',
                                                '{{ $section->key === 'image' ? asset('storage/' . $section->value) : addslashes($section->value) }}',
                                                '{{ $section->icon ? asset('storage/' . $section->icon) : '' }}')">
                                        Edit
                                    </button>


                                    {{-- Tombol Hapus --}}
                                    <form action="{{ route('superadmin.landing.destroy', $section->id) }}"
                                        method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data section</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>

            {{-- Modal Edit --}}
            <x-modal id="modalEditSection" title="Edit Section">
                <form id="formEditSection" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="id" id="edit-id">

                    {{-- Section --}}
                    <div class="mb-4">
                        <label class="block font-semibold">Nama Section</label>
                        <input type="text" name="section" id="edit-section" class="form-control" required>
                    </div>

                    {{-- Key --}}
                    <div class="mb-4">
                        <label class="block font-semibold">Key</label>
                        <input type="text" name="key" id="edit-key" class="form-control" readonly>
                    </div>

                    {{-- Value --}}
                    <div id="edit-value-field" class="mb-4"></div>

                    {{-- Preview Gambar --}}
                    <div id="edit-image-preview-container" class="mb-4 hidden">
                        <label class="block font-semibold">Preview Gambar Saat Ini</label>
                        <img id="edit-image-preview" src="#" alt="Preview Gambar"
                            class="w-40 h-auto rounded border">
                    </div>

                    {{-- Icon --}}
                    <div class="mb-4">
                        <label class="block font-semibold">Ganti Icon (opsional)</label>
                        <input type="file" name="icon" class="form-control" accept="image/*">
                        <span class="text-muted small">Format yang didukung JPG, JPEG, dan PNG dengan maksimal ukuran
                            file 2MB.</span>
                    </div>

                    {{-- Preview Icon --}}
                    <div id="edit-icon-preview-container" class="mb-4 hidden">
                        <label class="block font-semibold">Preview Icon Saat Ini</label>
                        <img id="edit-icon-preview" src="#" alt="Preview Icon"
                            class="w-12 h-auto rounded border">
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-success w-full">Perbarui</button>
                    </div>
                </form>
            </x-modal>

            {{-- Carousel Section --}}
            <div class="card shadow-sm rounded">
                <div class="card-body">
                    <h5 class="card-title mb-3 fw-bold">🎞️ Carousel Section</h5>
                    <p class="text-muted mb-3">Pilih berita yang ingin ditampilkan pada carousel di landing page.</p>
                    <span id="counter" class="badge bg-primary">0 / 5</span>
                    <button class="btn btn-primary"
                        onclick="document.getElementById('modalCarousel').classList.remove('hidden')">
                        Pilih Berita Carousel
                    </button>
                </div>
            </div>

            <!-- Modal Carousel -->
            <x-modal id="modalCarousel" title="Pilih Berita untuk Carousel">
                <form method="POST" action="{{ route('superadmin.landing.carousel.update') }}">
                    @csrf

                    <div class="mt-4 text-right">
                        <button class="btn btn-success" type="submit">Simpan</button>
                    </div>
                    <div>
                        <h6>Berita Terpilih</h6>
                        <div id="selected-news" class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                            @foreach ($selectedNews as $news)
                                <div class="selected-item border rounded-lg p-2 relative flex flex-col"
                                    data-id="{{ $news->id }}">
                                    <input type="hidden" name="selected_news_ids[]" value="{{ $news->id }}"
                                        data-title="{{ $news->title }}">
                                    <img src="{{ asset('storage/' . $news->image) }}"
                                        class="w-full h-36 object-cover rounded">
                                    <h6 class="font-semibold mt-2">{{ $news->title }}</h6>
                                    <button type="button"
                                        class="absolute top-2 right-2 bg-red-600 text-white text-xs px-2 py-1 rounded remove-news">Hapus</button>
                                </div>
                            @endforeach
                        </div>

                        <hr class="my-4">

                        <h6>Semua Berita</h6>
                        <div id="all-news" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @php
                                $selectedIds = $selectedNews->pluck('id')->toArray();
                            @endphp

                            @foreach ($allNews as $news)
                                @if (!in_array($news->id, $selectedIds))
                                    <div class="news-card border rounded-lg p-2 flex flex-col justify-between"
                                        data-id="{{ $news->id }}">
                                        <img src="{{ asset('storage/' . $news->image) }}"
                                            class="w-full h-36 object-cover rounded">
                                        <h6 class="font-semibold mt-2">{{ $news->title }}</h6>
                                        <button type="button"
                                            class="mt-2 bg-green-600 text-white text-xs px-2 py-1 rounded add-news"
                                            data-id="{{ $news->id }}" data-title="{{ $news->title }}"
                                            data-image="{{ asset('storage/' . $news->image) }}">
                                            Tambahkan
                                        </button>
                                    </div>
                                @endif
                            @endforeach

                        </div>
                    </div>
                    <script>
                        document.querySelector('form').addEventListener('submit', function() {
                            const selected = document.querySelectorAll('#selected-news .selected-item');
                            selected.forEach((item, index) => {
                                const input = item.querySelector('input[name="selected_news_ids[]"]');
                                input.setAttribute('name', `selected_news_ids[${index}]`);
                            });
                        });
                    </script>
                </form>
            </x-modal>

            {{-- Preview Berita Terpilih --}}
            <div class="card shadow-sm rounded">
                <div class="card-body">
                    <h6 class="fw-semibold mb-2">🔍 Urutan Carousel Saat Ini</h6>

                    <form method="POST" action="{{ route('superadmin.landing.carousel.update') }}">
                        @csrf

                        <div id="preview-selected-news" class="grid grid-cols-1 gap-3">
                            @foreach ($selectedNews as $news)
                                <div class="selected-item border rounded p-2 flex items-center justify-between bg-light"
                                    data-id="{{ $news->id }}">
                                    <div class="flex items-center gap-3">
                                        <span class="drag-handle cursor-move text-muted me-2">
                                            <i class="bi bi-arrows-move"></i>
                                        </span>
                                        <img src="{{ asset('storage/' . $news->image) }}"
                                            class="w-16 h-16 object-cover rounded" alt="preview">
                                        <span class="fw-semibold">{{ $news->title }}</span>
                                    </div>
                                    <input type="hidden" name="selected_news_ids[]" value="{{ $news->id }}">
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 text-end">
                            <button class="btn btn-success" type="submit">Simpan Urutan</button>
                        </div>
                    </form>

                    <script>
                        Sortable.create(document.getElementById('preview-selected-news'), {
                            animation: 150,
                            handle: '.drag-handle',
                        });

                        document.querySelector('#preview-selected-news form')?.addEventListener('submit', function() {
                            const items = document.querySelectorAll('#preview-selected-news .selected-item');
                            items.forEach((item, index) => {
                                const input = item.querySelector('input[name^="selected_news_ids"]');
                                input.setAttribute('name', `selected_news_ids[${index}]`);
                            });
                        });
                    </script>
                </div>
            </div>

            {{-- Preview Website --}}
            <h2 class="text-center fw-bold my-5">Preview Website</h2>
            <div class="preview-website">

                {{-- Carousel Section --}}
                @if ($selectedNews->isNotEmpty())
                    <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel"
                        data-bs-interval="4000">
                        <div class="carousel-indicators">
                            @foreach ($selectedNews as $index => $news)
                                <button type="button" data-bs-target="#carouselExampleCaptions"
                                    data-bs-slide-to="{{ $index }}"
                                    class="{{ $loop->first ? 'active' : '' }}"
                                    aria-current="{{ $loop->first ? 'true' : 'false' }}"
                                    aria-label="Slide {{ $index + 1 }}"></button>
                            @endforeach
                        </div>
                        <div class="carousel-inner">
                            @foreach ($selectedNews as $index => $news)
                                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                    <a href="{{ route('superadmin.berita.detail', $news->slug ?? $news->id) }}">
                                        <div class="carousel-img-wrapper">
                                            <img src="{{ asset('storage/' . $news->image) }}"
                                                alt="{{ $news->title }}"
                                                class="d-block w-100 img-fluid custom-carousel-height">
                                        </div>
                                        <div
                                            class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-2">
                                            <h5 class="text-white mb-0">{{ $news->title }}</h5>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                        <button class="carousel-control-prev" type="button"
                            data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button"
                            data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                    <style>
                        .carousel-img-wrapper {
                            aspect-ratio: 3 / 2;
                            width: 100%;
                            overflow: hidden;
                        }

                        .carousel-img-wrapper img {
                            object-fit: cover;
                            width: 100%;
                            height: 100%;
                        }

                        .carousel-inner {
                            min-height: 100px;
                        }
                    </style>
                @else
                    <div class="alert alert-warning">Belum ada berita yang dipilih untuk Carousel Section.</div>
                @endif

                {{-- Hero Section --}}
                @php
                    $heroTitle = $sections->where('section', 'hero')->where('key', 'title')->first();
                    $heroContent = $sections->where('section', 'hero')->where('key', 'content')->first();
                    $heroImage = $sections->where('section', 'hero')->where('key', 'image')->first();
                @endphp

                @if ($heroTitle && $heroContent && $heroImage)
                    <div class="hero-section position-relative d-flex align-items-center min-vh-75 py-5 py-md-5"
                        style="background-image: url('{{ asset('images/bg-hero.svg') }}');
        background-size: cover; background-position: center; background-repeat: no-repeat; ">

                        <!-- Overlay transparan -->
                        <div class="position-absolute top-0 start-0 w-100 h-100"
                            style="background-color: rgba(248, 251, 247, 0.4); z-index: 1;">
                        </div>

                        <!-- Konten di atas background -->
                        <div class="container py-5 position-relative" style="z-index: 2;">
                            <div class="row align-items-center g-4">
                                <!-- Gambar Kiri -->
                                <div class="col d-flex justify-content-center">
                                    <img src="{{ asset('storage/' . $heroImage->value) }}" alt="image"
                                        class="img-fluid" style="max-width: 550px; object-fit: cover;">
                                </div>

                                <!-- Teks Kanan -->
                                <div class="col text-center text-xl-start hero-text">
                                    <h2 class="fw-bold display-5" style="color: #14532d;">
                                        {{ $heroTitle->value }}
                                    </h2>
                                    <div class="prose text-justify">
                                        {!! $heroContent->value !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <style>
                        @media (max-width: 575.98px) {
                            .hero-section {
                                height: 120vh !important;
                            }

                            .hero-section img {
                                max-width: 350px !important;
                            }

                            .contact-section img {
                                width: 30px !important;
                                height: 30px !important;
                            }

                            .contact-section .prose p {
                                margin-bottom: 0px !important;
                            }
                        }
                    </style>
                @else
                    <div class="alert alert-warning">Data Hero Section belum lengkap.</div>
                @endif

                {{-- About Section --}}
                @php
                    $aboutTitle = $sections->where('section', 'about')->where('key', 'title')->first();
                    $aboutContent = $sections->where('section', 'about')->where('key', 'content')->first();
                    $aboutImage = $sections->where('section', 'about')->where('key', 'image')->first();
                @endphp

                @if ($aboutTitle && $aboutContent && $aboutImage)
                    <div class="about-section py-5 mt-4" id="tentang"
                        style="background-color: rgba(219, 234, 213, 0.2);">
                        <div class="container">
                            <!-- Gunakan flex-row hanya di xl (desktop), sisanya tetap kolom -->
                            <div class="row gy-4 d-flex flex-column flex-xl-row align-items-center">
                                <!-- Gambar (default duluan, desktop pindah ke kanan) -->
                                <div
                                    class="col-12 col-xl-6 text-center order-1 order-xl-2 d-flex justify-content-center">
                                    <img src="{{ asset('storage/' . $aboutImage->value) }}" alt="about"
                                        class="img-fluid rounded" style="object-fit: cover; max-height: 480px;">
                                </div>

                                <!-- Teks (default di bawah, desktop pindah ke kiri) -->
                                <div class="col-12 col-xl-6 order-2 order-xl-1 text-center text-xl-start">
                                    <h2 class="display-5 fw-bold">{{ $aboutTitle->value }}</h2>
                                    <div class="prose mt-3 text-justify">
                                        {!! $aboutContent->value !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning">Data About Section belum lengkap.</div>
                @endif

                {{-- Count Section --}}
                <style>
                    .counter {
                        font-size: 2.5rem;
                        color: #14532d;
                        font-weight: bold;
                    }

                    .count-section img {
                        margin-bottom: 10px;
                    }
                </style>

                <div class="count-section py-5 my-4"
                    style="background-color: rgba(219, 234, 213, 0.35); margin-bottom: 40px;">
                    <div class="container">
                        <div class="row text-center justify-content-center">
                            <div class="col-md-4 flex flex-column align-items-center">
                                <img src="{{ asset('icon/group.png') }}" alt="Member" style="height: 50px;">
                                <h2 class="counter mt-2" data-target="{{ $memberCount }}">0</h2>
                                <p>Member</p>
                            </div>
                            <div class="col-md-4 flex flex-column align-items-center">
                                <img src="{{ asset('icon/file.png') }}" alt="Publication" style="height: 50px;">
                                <h2 class="counter mt-2" data-target="{{ $publicationCount }}">0</h2>
                                <p>Publikasi Member</p>
                            </div>
                            <div class="col-md-4 flex flex-column align-items-center">
                                <img src="{{ asset('icon/newspaper-folded.png') }}" alt="News"
                                    style="height: 50px;">
                                <h2 class="counter mt-2" data-target="{{ $newsCount }}">0</h2>
                                <p>Berita</p>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener("DOMContentLoaded", () => {
                        const counters = document.querySelectorAll('.counter');

                        const startCounting = (counter) => {
                            const target = +counter.getAttribute('data-target');
                            let count = 0;

                            const updateCount = () => {
                                const increment = target / 200;
                                if (count < target) {
                                    count += increment;
                                    counter.innerText = Math.ceil(count);
                                    setTimeout(updateCount, 10);
                                } else {
                                    counter.innerText = target;
                                }
                            };

                            updateCount();
                        };

                        const observer = new IntersectionObserver((entries, obs) => {
                            entries.forEach(entry => {
                                if (entry.isIntersecting) {
                                    const counter = entry.target;
                                    startCounting(counter);
                                    obs.unobserve(counter); // Hanya jalan sekali
                                }
                            });
                        }, {
                            threshold: 0.6 // saat 60% elemen terlihat
                        });

                        counters.forEach(counter => {
                            observer.observe(counter);
                        });
                    });
                </script>

                {{-- Portal Berita Section --}}
                <style>
                    .news-card {
                        transition: transform 0.3s, box-shadow 0.3s;
                        border-radius: 10px;
                        overflow: hidden;
                    }

                    .news-card:hover {
                        transform: translateY(-5px);
                        box-shadow: 0 6px 20px rgba(21, 64, 14, 0.3);
                        border: 1px solid #15400E;
                    }

                    .news-card .card-title {
                        font-weight: 600;
                        font-size: 1rem;
                        color: #15400E;
                    }

                    .object-fit-cover {
                        object-fit: cover;
                    }
                </style>

                <div class="news-section my-5">
                    <div class="container">
                        <h2 class="text-center fw-bold mb-4">Portal Berita</h2>

                        <div class="row justify-content-center g-4">
                            @foreach ($latestNews as $item)
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex">
                                    <div class="card news-card flex-fill shadow-sm border-0">
                                        <div class="ratio ratio-4x3">
                                            <img src="{{ asset('storage/' . $item->image) }}"
                                                class="card-img-top object-fit-cover"
                                                alt="Thumbnail {{ $item->title }}">
                                        </div>
                                        <div class="card-body d-flex flex-column">
                                            <h6 class="card-title flex-grow-1">{{ $item->title }}</h6>
                                            <a href="{{ route('superadmin.berita.detail', $news->slug ?? $news->id) }}" class="btn btn-sm btn-outline-success mt-2">
                                                Baca Selengkapnya
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="text-center mt-4">
                            <a href="{{ route('superadmin.berita') }}" class="btn btn-outline-success btn-sm">
                                Lihat Semua Berita
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Contact Section --}}
                @php
                    $contactTitle = $sections->where('section', 'contact')->where('key', 'title')->first();
                    $contactContents = $sections->where('section', 'contact')->where('key', 'content');
                @endphp

                @if ($contactTitle && $contactContents->isNotEmpty())
                    <div class="contact-section py-5" id="kontak" style="border-top: 1px solid #30BA7F;">
                        <div class="container">
                            <h2 class="text-center fw-bold mb-4">{{ $contactTitle->value }}</h2>
                            <div class="row justify-content-center">
                                <div class="col-md-5">
                                    @foreach ($contactContents as $contact)
                                        <div class="d-flex align-items-center mb-3">
                                            @if ($contact->icon)
                                                <img src="{{ asset('storage/' . $contact->icon) }}" alt="icon"
                                                    style="width: 40px; height: 40px;" class="me-3">
                                            @endif
                                            <div class="prose mb-0">
                                                {!! $contact->value !!}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </x-layout.content-bar>
    </div>

    {{-- SweetAlert2 --}}
    @if (session('status'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: '{{ session('status') }}',
                timer: 2500,
                showConfirmButton: false
            });
        </script>
    @endif
</body>
<script>
    // Counter untuk maksimal carousel
    function updateCounter() {
        const count = document.querySelectorAll('#selected-news .selected-item').length;
        document.getElementById('counter').textContent = `${count} / 5`;
    }
    setInterval(updateCounter, 300);

    // Script untuk memilih berita di carousel
    document.querySelectorAll('.add-news').forEach(button => {
        button.addEventListener('click', () => {
            const selectedContainer = document.getElementById('selected-news');
            const selectedItems = selectedContainer.querySelectorAll('.selected-item');

            if (selectedItems.length >= 5) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Maksimal 5 berita!',
                    text: 'Kamu hanya bisa memilih maksimal 5 berita untuk carousel.',
                    timer: 2500,
                    showConfirmButton: false
                });
                return;
            }

            const id = button.dataset.id;
            const title = button.dataset.title;
            const image = button.dataset.image;

            if (selectedContainer.querySelector(`[data-id="${id}"]`)) return;

            const container = document.createElement('div');
            container.className = 'selected-item border rounded-lg p-2 relative';
            container.dataset.id = id;
            container.innerHTML = `
            <input type="hidden" name="selected_news_ids[]" value="${id}" data-title="${title}">
            <img src="${image}" class="w-full h-36 object-cover rounded">
            <h6 class="font-semibold mt-2">${title}</h6>
            <button type="button" class="absolute top-2 right-2 bg-red-600 text-white text-xs px-2 py-1 rounded remove-news">Hapus</button>
        `;
            selectedContainer.appendChild(container);

            const card = document.querySelector(`#all-news [data-id="${id}"]`);
            if (card) card.remove();
        });
    });


    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-news')) {
            const item = e.target.closest('.selected-item');
            const id = item.dataset.id;
            const title = item.querySelector('h6').innerText;
            const image = item.querySelector('img').src;

            const newCard = document.createElement('div');
            newCard.className = 'news-card border rounded-lg p-2 flex flex-col justify-between';
            newCard.dataset.id = id;
            newCard.innerHTML = `
            <img src="${image}" class="w-full h-36 object-cover rounded">
            <h6 class="font-semibold mt-2">${title}</h6>
            <button type="button" class="mt-2 bg-green-600 text-white text-xs px-2 py-1 rounded add-news" data-id="${id}" data-title="${title}" data-image="${image}">
                Tambahkan
            </button>
        `;

            const allNewsContainer = document.getElementById('all-news');
            const allCards = Array.from(allNewsContainer.children);

            // Sisipkan berdasarkan ID descending
            let inserted = false;
            for (let i = 0; i < allCards.length; i++) {
                const currentId = parseInt(allCards[i].dataset.id);
                if (parseInt(id) > currentId) {
                    allNewsContainer.insertBefore(newCard, allCards[i]);
                    inserted = true;
                    break;
                }
            }
            if (!inserted) {
                allNewsContainer.appendChild(newCard);
            }

            item.remove();

            // Tambahkan ulang listener untuk tombol "Tambahkan"
            newCard.querySelector('.add-news').addEventListener('click', () => {
                const btn = newCard.querySelector('.add-news');
                const id = btn.dataset.id;
                const title = btn.dataset.title;
                const image = btn.dataset.image;

                if (document.querySelector(`#selected-news [data-id="${id}"]`)) return;

                const container = document.createElement('div');
                container.className = 'selected-item border rounded-lg p-2 relative';
                container.dataset.id = id;
                container.innerHTML = `
                <input type="hidden" name="selected_news_ids[]" value="${id}" data-title="${title}">
                <img src="${image}" class="w-full h-36 object-cover rounded">
                <h6 class="font-semibold mt-2">${title}</h6>
                <button type="button" class="absolute top-2 right-2 bg-red-600 text-white text-xs px-2 py-1 rounded remove-news">Hapus</button>
            `;
                document.getElementById('selected-news').appendChild(container);
                newCard.remove();
            });
        }
    });

    // Script untuk input key dinamis
    function handleKeyChange() {
        const key = document.getElementById('key').value;
        const container = document.getElementById('value-field');
        const previewContainer = document.getElementById('image-preview-container');
        const preview = document.getElementById('image-preview');

        container.innerHTML = '';
        previewContainer.classList.add('hidden');
        preview.src = '#';

        if (key === 'title') {
            container.innerHTML = `
                <label for="value" class="block font-semibold">Value</label>
                <input type="text" name="value" id="value" class="form-control" placeholder="Masukkan judul" required>
            `;
        } else if (key === 'content') {
            container.innerHTML = `
                <label for="value" class="block font-semibold">Value</label>
                <textarea name="value" id="value" class="form-control editor" placeholder="Masukkan konten"></textarea>
            `;
            tinymce.init({
                selector: 'textarea.editor', // pakai class "editor"
                plugins: 'lists link image code',
                toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | removeformat',
                menubar: false,
                height: 300,
                license_key: 'gpl',
                branding: false,
                content_style: "body { font-family:Nunito,Helvetica; font-size:14px }"
            });
        } else if (key === 'image') {
            container.innerHTML = `
                <label for="value" class="block font-semibold">Upload Gambar</label>
                <input type="file" name="value" id="value-image" class="form-control" accept="image/jpeg,image/png,image/jpg" required>
                <span class="text-muted small">Format yang didukung JPG, JPEG, dan PNG dengan maksimal ukuran file 2MB.</span>
            `;

            // Sweetalert validasi gambar
            setTimeout(() => {
                const imageInput = document.getElementById('value-image');
                imageInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                    const maxSize = 2 * 1024 * 1024; // 2 MB

                    if (!allowedTypes.includes(file.type)) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Format Gambar Tidak Didukung',
                            text: 'Hanya diperbolehkan gambar JPG, JPEG, atau PNG.',
                        });
                        imageInput.value = "";
                        return;
                    }

                    if (file.size > maxSize) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Ukuran Gambar Terlalu Besar',
                            text: 'Ukuran maksimal gambar adalah 2MB.',
                        });
                        imageInput.value = "";
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        previewContainer.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                });
            }, 100);
        }
    }

    // Sweetalert validasi icon
    document.getElementById('icon').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        const maxSize = 2 * 1024 * 1024; // 2MB

        const previewIconContainer = document.getElementById('icon-preview-container');
        const previewIcon = document.getElementById('icon-preview');

        previewIconContainer.classList.add('hidden');
        previewIcon.src = "#";

        if (!allowedTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Format Icon Tidak Didukung',
                text: 'Hanya diperbolehkan gambar JPG, JPEG, atau PNG.',
            });
            e.target.value = "";
            return;
        }

        if (file.size > maxSize) {
            Swal.fire({
                icon: 'error',
                title: 'Ukuran Icon Terlalu Besar',
                text: 'Ukuran maksimal icon adalah 2MB.',
            });
            e.target.value = "";
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            previewIcon.src = e.target.result;
            previewIconContainer.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    });


    // SweetAlert konfirmasi hapus
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data ini akan dihapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Fungsi preview dan validasi edit gambar
    document.getElementById('formEditSection').addEventListener('change', function(e) {
        const target = e.target;
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        const maxSize = 2 * 1024 * 1024;

        if (target.name === 'value') {
            const file = target.files[0];
            if (!file) return;

            if (!allowedTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Format Gambar Tidak Didukung',
                    text: 'Hanya diperbolehkan gambar JPG, JPEG, atau PNG.'
                });
                target.value = "";
                return;
            }

            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'Ukuran Gambar Terlalu Besar',
                    text: 'Ukuran maksimal gambar adalah 2MB.'
                });
                target.value = "";
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('edit-image-preview');
                preview.src = e.target.result;
                document.getElementById('edit-image-preview-container').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }

        if (target.name === 'icon') {
            const file = target.files[0];
            if (!file) return;

            if (!allowedTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Format Icon Tidak Didukung',
                    text: 'Hanya diperbolehkan icon JPG, JPEG, atau PNG.'
                });
                target.value = "";
                return;
            }

            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'Ukuran Icon Terlalu Besar',
                    text: 'Ukuran maksimal icon adalah 2MB.'
                });
                target.value = "";
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('edit-icon-preview');
                preview.src = e.target.result;
                document.getElementById('edit-icon-preview-container').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    });

    // Modal edit
    function openEditModal(id, section, key, value, icon) {
        document.getElementById('edit-id').value = id;
        document.getElementById('edit-section').value = section;
        document.getElementById('edit-key').value = key;

        let fieldContainer = document.getElementById('edit-value-field');
        fieldContainer.innerHTML = '';

        const imagePreview = document.getElementById('edit-image-preview');
        const iconPreview = document.getElementById('edit-icon-preview');
        const imagePreviewContainer = document.getElementById('edit-image-preview-container');
        const iconPreviewContainer = document.getElementById('edit-icon-preview-container');

        imagePreviewContainer.classList.add('hidden');
        iconPreviewContainer.classList.add('hidden');

        if (key === 'title') {
            fieldContainer.innerHTML =
                `<label class="block font-semibold">Value</label><input type="text" name="value" class="form-control" value="${value}" required>`;
        } else if (key === 'content') {
            fieldContainer.innerHTML =
                `<label class="block font-semibold">Value</label><textarea name="value" class="form-control editor">${value}</textarea>`;
            tinymce.init({
                selector: 'textarea.editor', // pakai class "editor"
                plugins: 'lists link image code',
                toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | removeformat',
                menubar: false,
                height: 300,
                license_key: 'gpl',
                branding: false,
                content_style: "body { font-family:Nunito,Helvetica; font-size:14px }"
            });
        } else if (key === 'image') {
            fieldContainer.innerHTML =
                `<label class="block font-semibold">Upload Gambar Baru (opsional)</label><input type="file" name="value" class="form-control" accept="image/jpeg,image/png,image/jpg">
                <span class="text-muted small">Format yang didukung JPG, JPEG, dan PNG dengan maksimal ukuran file 2MB.</span>`;
            if (value) {
                imagePreview.src = `${value}`;
                imagePreviewContainer.classList.remove('hidden');
            }
        }

        if (icon) {
            iconPreview.src = `${icon}`;
            iconPreviewContainer.classList.remove('hidden');
        }

        const form = document.getElementById('formEditSection');
        form.action = `/superadmin/landing-page/update/${id}`;
        form.querySelector('input[name="_method"]').value = "POST";

        document.getElementById('modalEditSection').classList.remove('hidden');
    }
</script>
