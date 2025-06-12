@include('head')

<body>
    <div class="flex h-screen">
        <x-layout.sidebar />
        <x-layout.content-bar :title="$title" :subtitle="$subtitle">
            @includeWhen($title === 'Divisi', 'components.content.main')

            {{-- Konten CRUD --}}
            <div class="p-6 space-y-6">
                <div class="bg-white shadow-lg rounded-xl p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-green-700">Daftar Divisi</h2>
                        <button class="btn btn-primary"
                            onclick="document.getElementById('modalTambahDivisi').classList.remove('hidden')">
                            Tambah Divisi
                        </button>
                    </div>

                    {{-- Modal Tambah Divisi --}}
                    <x-modal id="modalTambahDivisi" title="Tambah Divisi">
                        <form method="POST" action="{{ route('superadmin.divisi.store') }}" class="space-y-4">
                            @csrf
                            @method('POST')
                            <div>
                                <label for="title" class="block font-medium text-sm text-gray-700">Judul
                                    Divisi</label>
                                <input type="text" name="title" id="title"
                                    placeholder="Contoh : Divisi I, Divisi II, dst" class="form-control" required>
                            </div>
                            <div>
                                <label for="region" class="block font-medium text-sm text-gray-700">Wilayah
                                    Divisi</label>
                                <input type="text" name="region" id="region"
                                    placeholder="Contoh : Jawa Timur, Jawa Barat, dst (provinsi)" class="form-control"
                                    required>
                            </div>
                            <div>
                                <label for="description" class="block font-medium text-sm text-gray-700">Deskripsi
                                    Divisi</label>
                                <textarea name="description" id="description" rows="5" placeholder="Isi konten/deskripsi divisi"
                                    class="form-control editor"></textarea>
                            </div>
                            <div>
                                <label for="admin_id" class="block font-medium text-sm text-gray-700">Admin Divisi
                                    (opsional)</label>
                                <select name="admin_id" id="admin_id" class="form-control">
                                    <option value="">-- Pilih Admin (Opsional) --</option>
                                    @foreach ($availableAdminsForAdd as $admin)
                                        <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </x-modal>

                    {{-- List Divisi --}}
                    <div class="space-y-4">
                        @if ($divisions->count())
                            @foreach ($divisions as $division)
                                <a href="{{ route('superadmin.divisi.detail', $division->id) }}"
                                    class="block bg-green-50 border border-green-200 rounded-lg p-5 hover:bg-green-100 transition-all duration-200 relative group">
                                    <div class="flex justify-between items-start">
                                        <div class="text-gray-800">
                                            <h3 class="text-lg font-bold mb-1 text-green-800">
                                                {{ $division->title }}
                                                <span class="text-sm text-gray-500">({{ $division->region }})</span>
                                            </h3>
                                            <p class="text-sm text-gray-600 mb-1 prose">
                                                {{ \Illuminate\Support\Str::limit(strip_tags($division->description), 200) }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                <strong>Admin:</strong>
                                                {{ $division->admin?->name ?? 'Belum ditugaskan' }}
                                            </p>
                                        </div>

                                        <div class="ml-4 flex flex-col space-y-2 z-0 relative">
                                            <button type="button"
                                                onclick="event.preventDefault(); event.stopPropagation(); document.getElementById('modalEditDivisi-{{ $division->id }}').classList.remove('hidden')"
                                                class="btn btn-warning text-sm">
                                                Edit
                                            </button>
                                            <form method="POST"
                                                action="{{ route('superadmin.divisi.destroy', $division->id) }}"
                                                onsubmit="event.preventDefault(); event.stopPropagation(); return confirmDelete(this)">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger text-sm">Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </a>


                                {{-- Modal Edit Divisi --}}
                                <x-modal id="modalEditDivisi-{{ $division->id }}" title="Edit Divisi">
                                    <form method="POST"
                                        action="{{ route('superadmin.divisi.update', $division->id) }}"
                                        class="space-y-4">
                                        @csrf
                                        @method('PATCH')
                                        <div>
                                            <label for="edit_title_{{ $division->id }}"
                                                class="block font-medium text-sm text-gray-700">Judul Divisi</label>
                                            <input type="text" name="title" id="edit_title_{{ $division->id }}"
                                                value="{{ $division->title }}"
                                                placeholder="Contoh : Divisi I, Divisi II, dst" class="form-control">
                                        </div>
                                        <div>
                                            <label for="edit_region_{{ $division->id }}"
                                                class="block font-medium text-sm text-gray-700">Wilayah Divisi</label>
                                            <input type="text" name="region" id="edit_region_{{ $division->id }}"
                                                value="{{ $division->region }}"
                                                placeholder="Contoh : Jawa Timur, Jawa Barat, dst (provinsi)"
                                                class="form-control">
                                        </div>
                                        <div>
                                            <label for="edit_description_{{ $division->id }}"
                                                class="block font-medium text-sm text-gray-700">Deskripsi Divisi</label>
                                            <textarea name="description" id="edit_description_{{ $division->id }}" rows="5"
                                                placeholder="Isi konten/deskripsi divisi" class="form-control editor">{!! $division->description !!}</textarea>
                                        </div>
                                        <div>
                                            <label for="edit_admin_id_{{ $division->id }}"
                                                class="block font-medium text-sm text-gray-700">Admin Divisi
                                                (opsional)
                                            </label>
                                            <select name="admin_id" id="edit_admin_id_{{ $division->id }}"
                                                class="form-control">
                                                <option value="">-- Pilih Admin (Opsional) --</option>
                                                @foreach ($availableAdminsForEdit[$division->id] ?? [] as $admin)
                                                    <option value="{{ $admin->id }}"
                                                        {{ $division->admin_id == $admin->id ? 'selected' : '' }}>
                                                        {{ $admin->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="text-right">
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </x-modal>
                            @endforeach
                        @else
                            {{-- <p class="text-gray-500">Belum ada divisi yang ditambahkan.</p> --}}
                            <div class="text-center text-gray-500 p-6 border border-dashed border-gray-300 rounded-lg">
                                Tidak ada data divisi yang ditambahkan.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </x-layout.content-bar>
    </div>

    {{-- SweetAlert2 --}}
    @if (session('status'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
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
            title: 'Yakin ingin menghapus?',
            text: "Data divisi akan dihapus permanen!",
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
