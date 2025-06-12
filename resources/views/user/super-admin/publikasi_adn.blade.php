@include('head')
<style>
    .publikasi-card:hover {
        transform: scale(1.01);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        border-color: #2e7d32;
    }
</style>

<body>
    <div class="flex h-screen">
        <x-layout.sidebar />
        <x-layout.content-bar :title="$title" :subtitle="$subtitle">
            {{-- Slot utama di sini, bisa diganti sesuai konten --}}
            @includeWhen($title === 'Publikasi ADN', 'components.content.main')

            {{-- Tulis kode di bawah ini untuk isi kontennya --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">List Publikasi ADN</h5>
                <button class="btn btn-primary"
                    onclick="document.getElementById('modalTambahPublikasi').classList.remove('hidden')">
                    Tambah Publikasi
                </button>
            </div>

            {{-- Daftar Publikasi --}}
            <div class="row">
                @forelse ($publications as $publication)
                    <div class="col-md-12 mb-3">
                        <a href="{{ route('superadmin.publikasi-adn.detail', $publication->slug) }}"
                            class="text-decoration-none text-dark">
                            <div class="card d-flex flex-row publikasi-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ \Illuminate\Support\Str::limit($publication->title, 60) }}
                                    </h5>
                                    <p class="card-text prose">
                                        {{ \Illuminate\Support\Str::limit(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($publication->content))), 300, '... (selengkapnya)') }}
                                        <span class="text-primary"></span>
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-md-12 text-center text-muted mt-4">
                        Tidak ada publikasi organisasi ADN yang ditambahkan.
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $publications->links('vendor.pagination.bootstrap-5') }}
            </div>

            {{-- Modal Tambah Publikasi --}}
            <x-modal id="modalTambahPublikasi" title="Tambah Publikasi ADN Baru">
                <form action="{{ route('superadmin.publikasi-adn.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <div class="mb-4">
                        <label for="title" class="block font-semibold">Judul Publikasi</label>
                        <input type="text" name="title" id="title" placeholder="Judul Publikasi"
                            value="{{ old('title') }}" class="form-control @error('title') border-red-500 @enderror">
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="content" class="block font-semibold">Isi Publikasi</label>
                        <textarea name="content" id="content" rows="10" placeholder="Isi konten publikasi"
                            class="form-control editor @error('content') border-red-500 @enderror">{{ old('content') }}</textarea>
                        @error('content')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
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
                title: 'Berhasil',
                text: '{{ session('status') }}',
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    @endif

    @if (session('modal') === 'modalTambahPublikasi')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('modalTambahPublikasi').classList.remove('hidden');
            });
        </script>
    @endif
</body>
