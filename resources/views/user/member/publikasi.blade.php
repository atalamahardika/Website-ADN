@include('head')

<body>
    <div class="flex h-screen">
        <x-layout.sidebar />
        <x-layout.content-bar :title="$title" :subtitle="$subtitle">
            @includeWhen($title === 'Publikasi', 'components.content.main')

            <div class="publikasi">
                <div class="card" style="background-color: #EDF4EA; padding: 24px;">
                    {{-- Header dan tombol tambah --}}
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="font-semibold">List Publikasi</h4>
                        <button onclick="document.getElementById('tambahModal').classList.remove('hidden')"
                            class="btn btn-primary">+ Tambah Publikasi</button>
                    </div>

                    {{-- Search Bar --}}
                    <form method="GET" action="{{ route('publikasi') }}" class="mb-4">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari judul atau penulis..." class="form-input rounded w-full">
                    </form>

                    {{-- List Publikasi --}}
                    @if ($publications->count())
                        <ul class="space-y-3">
                            @foreach ($publications as $pub)
                                <li class="bg-white p-4 rounded shadow-sm flex justify-between items-start">
                                    <div>
                                        {{-- Judul --}}
                                        <h5 class="font-semibold mb-1">{{ $pub->title }}</h5>
                                        {{-- Format Harvard --}}
                                        <p class="text-gray-700">
                                            {{ $pub->formatted_authors }} ({{ $pub->year }}), ‘{{ $pub->title }}’,
                                            <em>{{ $pub->journal_name }}</em>
                                            @if ($pub->volume)
                                                , vol. {{ $pub->volume }}
                                            @endif
                                            @if ($pub->pages)
                                                , hh. {{ $pub->pages }}
                                            @endif.
                                        </p>
                                        {{-- Link --}}
                                        @if ($pub->link)
                                            <p class="text-sm text-gray-700 mt-2">
                                                Sumber:
                                                @if (Str::startsWith($pub->link, ['http://', 'https://']))
                                                    <a href="{{ $pub->link }}" target="_blank"
                                                        class="text-blue-600 underline">
                                                        {{ $pub->link }}
                                                    </a>
                                                @else
                                                    {{ $pub->link }}
                                                @endif
                                            </p>
                                        @endif

                                    </div>
                                    <div class="flex gap-2 mt-1">
                                        <button
                                            onclick="document.getElementById('editModal-{{ $pub->id }}').classList.remove('hidden')"
                                            class="btn btn-warning">Edit</button>

                                        <form method="POST" action="{{ route('publikasi.destroy', $pub->id) }}"
                                            onsubmit="return confirmDelete(this)">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Hapus</button>
                                        </form>
                                    </div>
                                </li>

                                {{-- Modal Edit --}}
                                <x-modal id="editModal-{{ $pub->id }}" title="Edit Publikasi">
                                    <form method="POST" action="{{ route('publikasi.update', $pub->id) }}"
                                        class="space-y-3">
                                        @csrf
                                        @method('PATCH')
                                        <div class="mb-4">
                                            <label for="edit_authors" class="block font-semibold">Nama Penulis</label>
                                            <input type="text" name="authors"
                                                value="{{ old('authors', implode(', ', $pub->authors)) }}"
                                                placeholder="Nama pengarang (pisah koma)"
                                                class="form-control @error('authors') is-invalid @enderror" required>
                                            @error('authors')
                                                <div class="text-danger mt-1">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="edit_title" class="block font-semibold">Judul Publikasi</label>
                                            <input type="text" name="title" value="{{ old('title', $pub->title) }}"
                                                class="form-control @error('title') is-invalid @enderror"
                                                placeholder="Judul publikasi" required>
                                            @error('title')
                                                <div class="text-danger mt-1">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="edit_year" class="block font-semibold">Tahun Publikasi</label>
                                            <input type="number" name="year" value="{{ old('year', $pub->year) }}"
                                                class="form-control @error('year') is-invalid @enderror"
                                                placeholder="Tahun" required>
                                            @error('year')
                                                <div class="text-danger mt-1">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="edit_journal_name" class="block font-semibold"></label>
                                            <input type="text" name="journal_name" value="{{ old('journal_name', $pub->journal_name) }}"
                                                class="form-control @error('journal_name') is-invalid @enderror"
                                                placeholder="Nama jurnal" required>
                                            @error('journal_name')
                                                <div class="text-danger mt-1">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="edit_volume" class="block font-semibold">Volume</label>
                                            <input type="text" name="volume" value="{{ old('volume', $pub->volume) }}"
                                                class="form-control @error('volume') is-invalid @enderror"
                                                placeholder="Volume">
                                            @error('volume')
                                                <div class="text-danger mt-1">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="edit_pages" class="block font-semibold">Halaman</label>
                                            <input type="text" name="pages" value="{{ old('pages', $pub->pages) }}"
                                                class="form-control @error('pages') is-invalid @enderror"
                                                placeholder="Halaman">
                                            @error('pages')
                                                <div class="text-danger mt-1">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="edit_link" class="block font-semibold">Link Publikasi</label>
                                            <input type="text" name="link" value="{{ old('link', $pub->link) }}"
                                                class="form-control @error('link') is-invalid @enderror"
                                                placeholder="Contoh: elsevier.com">
                                            @error('link')
                                                <div class="text-danger mt-1">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="text-end">
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </x-modal>
                            @endforeach
                        </ul>

                        {{-- Pagination --}}
                        <div class="mt-4">
                            {{ $publications->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    @else
                        <p class="text-gray-500 italic">Belum ada publikasi yang ditambahkan.</p>
                    @endif
                </div>
            </div>
        </x-layout.content-bar>
    </div>

    {{-- Modal Tambah Publikasi --}}
    <x-modal id="tambahModal" title="Tambah Publikasi">
        <form method="POST" action="{{ route('publikasi.store') }}" class="space-y-3">
            @csrf

            <div class="mb-4">
                <label for="authors" class="block font-semibold">Nama Penulis</label>
                <input type="text" name="authors" placeholder="Nama pengarang (pisah koma)"
                    value="{{ old('authors') }}"
                    class="form-control @error('authors') is-invalid @enderror" required>
                @error('authors')
                    <div class="text-danger mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="mb-4">
                <label for="title" class="block font-semibold">Judul Publikasi</label>
                <input type="text" name="title" placeholder="Judul publikasi" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror" required>
                @error('title')
                    <div class="text-danger mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="mb-4">
                <label for="year" class="block font-semibold">Tahun Publikasi</label>
                <input type="number" name="year" placeholder="Tahun"
                    value="{{ old('year') }}"
                    class="form-control @error('year') is-invalid @enderror" required>
                @error('year')
                    <div class="text-danger mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="mb-4">
                <label for="journal_name" class="block font-semibold">Nama Jurnal</label>
                <input type="text" name="journal_name" placeholder="Nama jurnal"
                    value="{{ old('journal_name') }}"
                    class="form-control @error('journal_name') is-invalid @enderror" required>
                @error('journal_name')
                    <div class="text-danger mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="mb-4">
                <label for="volume" class="block font-semibold">Volume</label>
                <input type="number" name="volume" placeholder="misal: 3"
                    value="{{ old('volume') }}"
                    class="form-control @error('volume') is-invalid @enderror">
                @error('volume')
                    <div class="text-danger mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="mb-4">
                <label for="pages" class="block font-semibold">Halaman</label>
                <input type="text" name="pages" placeholder="misal: 10–20"
                    value="{{ old('pages') }}"
                    class="form-control @error('pages') is-invalid @enderror">
                @error('pages')
                    <div class="text-danger mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="mb-4">
                <label for="link" class="block font-semibold">Link Publikasi</label>
                <input type="text" name="link" placeholder="Contoh: elsevier.com"
                    value="{{ old('link') }}"
                    class="form-control @error('link') is-invalid @enderror">
                @error('link')
                    <div class="text-danger mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-primary">Tambah Publikasi</button>
            </div>
        </form>
    </x-modal>

    {{-- SweetAlert2 --}}
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: '{{ session('success') }}',
                timer: 2500,
                showConfirmButton: false
            });
        </script>
    @endif
    
    {{-- Error Handling Untuk Tetap Membuka Modal Tambah Publikasi --}}
    @if (session('modal') === 'tambahModal')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('tambahModal').classList.remove('hidden');
            });
        </script>
    @endif

    {{-- Error Handling Untuk Tetap Membuka Modal Edit Publikasi --}}
    @if (session('modal') === 'editModal' && session('publication_id'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('editModal-{{ session('publication_id') }}').classList.remove('hidden');
            });
        </script>
    @endif

    {{-- SweetAlert2 untuk error --}}
    @if ($errors->any())
        <script>
            Swal.fire({
                title: 'Gagal!',
                text: 'Terjadi kesalahan saat menyimpan data. Silakan periksa kembali isian Anda.',
                icon: 'error',
                confirmButtonText: 'Oke'
            });
        </script>
    @endif
</body>

<script>
    function confirmDelete(form) {
        event.preventDefault();
        Swal.fire({
            title: 'Hapus publikasi ini?',
            text: "Data akan dihapus secara permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
        return false;
    }
</script>
