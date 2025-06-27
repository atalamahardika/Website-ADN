@include('head')
<style>
    .berita-card:hover {
        transform: scale(1.01);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        border-color: #2e7d32;
    }

    @media (min-width: 1200px) {
        .berita-card img {
            width: 25%;
        }
    }

    @media (max-width: 1199.98px) {
        .card-img-top {
            width: 100% !important;
            height: auto !important;
            object-fit: cover !important;
            aspect-ratio: 3/2 !important;
            max-height: 250px;
        }
    }
</style>

<body>
    <div class="flex h-screen">
        <x-layout.sidebar />
        <x-layout.content-bar :title="$title" :subtitle="$subtitle">
            {{-- Slot utama di sini, bisa diganti sesuai konten --}}
            @includeWhen($title === 'Berita', 'components.content.main')

            {{-- Tulis kode di bawah ini untuk isi kontennya --}}
            <div class="mb-4">
                <div class="text-center mb-3">
                    <h4 class="fw-bold">List Berita</h4>
                </div>
                <div class="d-flex flex-column flex-md-row align-items-stretch gap-2 mb-3">
                    <form action="{{ route('superadmin.berita') }}" method="GET" class="d-flex flex-grow-1 gap-2">
                        <input type="text" name="search" class="form-control" placeholder="Cari judul berita..."
                            value="{{ request('search') }}">
                        <button type="submit" class="btn btn-outline-secondary">Cari</button>
                        @if (request('search'))
                            <a href="{{ route('superadmin.berita') }}" class="btn btn-outline-secondary">Reset</a>
                        @endif
                    </form>
                </div>
                <button class="btn btn-primary w-100 w-md-auto"
                    onclick="document.getElementById('modalTambahBerita').classList.remove('hidden')">Tambah
                    Berita</button>
            </div>



            {{-- List Berita --}}
            @if ($news->isEmpty())
                <p class="text-gray-500 italic">Belum ada berita yang ditambahkan.</p>
            @else
                <div class="row row-cols-1 g-4">
                    @foreach ($news as $item)
                        <div class="col">
                            <a href="{{ route('superadmin.berita.detail', $item->slug) }}"
                                class="text-decoration-none text-dark">
                                <div class="card berita-card h-100 flex-column flex-xl-row">
                                    <img src="{{ asset('storage/' . $item->image) }}" class="card-img-top"
                                        style="object-fit: cover;">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ \Illuminate\Support\Str::limit($item->title, 60) }}
                                        </h5>
                                        <p class="card-text">
                                            {{ \Illuminate\Support\Str::limit(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($item->content))), 150, '... (selengkapnya)') }}
                                        </p>
                                        <small class="text-muted">Sumber: {{ $item->source_link }}</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $news->links('vendor.pagination.bootstrap-5') }}
            </div>


            {{-- Modal Tambah Berita --}}
            <x-modal id="modalTambahBerita" title="Tambah Berita Baru">
                <form action="{{ route('superadmin.berita.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <div class="mb-4">
                        <label for="title" class="block font-semibold">Judul Berita</label>
                        <input type="text" name="title" id="title" placeholder="Judul berita"
                            value="{{ old('title') }}" class="form-control @error('title') border-red-500 @enderror">
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="content" class="block font-semibold">Isi Berita</label>
                        <textarea name="content" id="content" rows="6" placeholder="Isi konten berita"
                            class="form-control editor @error('content') border-red-500 @enderror">{{ old('content') }}</textarea>
                        @error('content')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="source_link" class="block font-semibold">Sumber Berita</label>
                        <input type="text" name="source_link" id="source_link" placeholder="Contoh : kompas.com"
                            value="{{ old('source_link') }}"
                            class="form-control @error('source_link') border-red-500 @enderror">
                        @error('source_link')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="image" class="block font-semibold">Gambar Berita</label>
                        <input type="file" name="image" id="image" accept="image/*"
                            class="form-control @error('image') border-red-500 @enderror">
                        <span class="text-muted small">Format yang didukung JPG, JPEG, dan PNG dengan maksimal ukuran
                            file 2MB.</span>
                        @error('image')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror

                        <div class="mt-2">
                            <img id="preview" class="max-w-full h-auto rounded border mt-2">
                        </div>

                        {{-- Hidden input for cropped image --}}
                        <input type="hidden" name="cropped_image" id="cropped_image">
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </x-modal>

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

    @if (session('modal') === 'modalTambahBerita')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('modalTambahBerita').classList.remove('hidden');
            });
        </script>
    @endif

    @if (session('status_warning'))
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Tidak Ditemukan',
                text: '{{ session('status_warning') }}',
                timer: 3000,
                showConfirmButton: false
            });
        </script>
    @endif


</body>
<script>
    let cropper;
    const image = document.getElementById('image');
    const preview = document.getElementById('preview');
    const croppedInput = document.getElementById('cropped_image');

    // Pastikan DOM sudah dimuat sebelum Cropper dan event listener
    document.addEventListener('DOMContentLoaded', function() {
        image.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            const maxSize = 2 * 1024 * 1024; // 2 MB

            if (!file) { // Tambahkan cek jika tidak ada file yang dipilih (user membatalkan)
                if (cropper) {
                    cropper.destroy();
                    preview.src = ''; // Hapus preview
                    croppedInput.value = ''; // Kosongkan hidden input
                }
                return;
            }

            if (!allowedTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Format Gambar Tidak Didukung',
                    text: 'Hanya diperbolehkan gambar dengan format JPG, JPEG, atau PNG.',
                });
                image.value = ""; // reset input
                if (cropper) cropper.destroy();
                preview.src = '';
                croppedInput.value = '';
                return;
            }

            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'Ukuran Gambar Terlalu Besar',
                    text: 'Ukuran maksimal gambar adalah 2MB.',
                });
                image.value = ""; // reset input
                if (cropper) cropper.destroy();
                preview.src = '';
                croppedInput.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(evt) {
                preview.src = evt.target.result;

                // Pastikan gambar sudah dimuat di preview sebelum Cropper diinisialisasi
                preview.onload = () => {
                    if (cropper) cropper.destroy();

                    cropper = new Cropper(preview, {
                        aspectRatio: 3 / 2,
                        viewMode: 1,
                        autoCropArea: 1,
                        crop(event) {
                            const canvas = cropper.getCroppedCanvas();
                            canvas.toBlob(function(blob) {
                                    const reader = new FileReader();
                                    reader.onloadend = function() {
                                        croppedInput.value = reader.result;
                                    }
                                    reader.readAsDataURL(blob);
                                }, 'image/jpeg',
                                0.9); // Tambahkan kualitas gambar (0.9 = 90%)
                        }
                    });
                }; // End preview.onload
            }
            reader.readAsDataURL(file);
        });
    });
</script>
