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
                                            <a href="{{ $pub->link }}" target="_blank"
                                                class="text-blue-600 underline">Lihat publikasi</a>
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
                                        <input type="text" name="authors" value="{{ implode(', ', $pub->authors) }}"
                                            placeholder="Nama pengarang (pisah koma)" class="form-input rounded w-full"
                                            required>
                                        <input type="text" name="title" value="{{ $pub->title }}"
                                            class="form-input rounded w-full" placeholder="Judul publikasi" required>
                                        <input type="number" name="year" value="{{ $pub->year }}"
                                            class="form-input rounded w-full" placeholder="Tahun" required>
                                        <input type="text" name="journal_name" value="{{ $pub->journal_name }}"
                                            class="form-input rounded w-full" placeholder="Nama jurnal" required>
                                        <input type="text" name="volume" value="{{ $pub->volume }}"
                                            class="form-input rounded w-full" placeholder="Volume">
                                        <input type="text" name="pages" value="{{ $pub->pages }}"
                                            class="form-input rounded w-full" placeholder="Halaman">
                                        <input type="url" name="link" value="{{ $pub->link }}"
                                            class="form-input rounded w-full"
                                            placeholder="Contoh: https://dicoding.com">
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
            <input type="text" name="authors" placeholder="Nama pengarang (pisah koma)"
                class="form-input rounded w-full" required>
            <input type="text" name="title" placeholder="Judul publikasi" class="form-input rounded w-full"
                required>
            <input type="number" name="year" placeholder="Tahun" class="form-input rounded w-full" required>
            <input type="text" name="journal_name" placeholder="Nama jurnal" class="form-input rounded w-full"
                required>
            <input type="text" name="volume" placeholder="Volume (misal: 3)" class="form-input rounded w-full">
            <input type="text" name="pages" placeholder="Halaman (misal: 10–20)"
                class="form-input rounded w-full">
            <input type="url" name="link" placeholder="Contoh: https://dicoding.com"
                class="form-input rounded w-full">
            <div class="text-end">
                <button type="submit" class="btn btn-primary">Tambah Publikasi</button>
            </div>
        </form>
    </x-modal>

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
