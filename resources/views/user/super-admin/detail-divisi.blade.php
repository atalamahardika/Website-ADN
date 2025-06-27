@include('head')

<body>
    <div class="flex h-screen">
        <x-layout.sidebar />
        <x-layout.content-bar :title="$title" :subtitle="$subtitle">
            {{-- Slot utama di sini, bisa diganti sesuai konten --}}
            @includeWhen($title === 'Detail Divisi', 'components.content.main')

            {{-- Tulis kode di bawah ini untuk isi kontennya --}}
            <div class="grid grid-cols-1 gap-6">
                {{-- Informasi Divisi --}}
                <div class="bg-white rounded-2xl shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 text-center">Informasi Divisi</h2>
                    <div class="space-y-3">
                        <div>
                            <label class="font-medium text-gray-600">Judul Divisi:</label>
                            <p class="text-gray-800">{{ $division->title }}</p>
                        </div>
                        <div>
                            <label class="font-medium text-gray-600">Wilayah:</label>
                            <p class="text-gray-800">{{ $division->region }}</p>
                        </div>
                        <div>
                            <label class="font-medium text-gray-600">Deskripsi:</label>
                            <div class="text-gray-800 prose pt-0">
                                {!! $division->description !!}
                            </div>
                        </div>
                        <div>
                            <label class="font-medium text-gray-600">Admin Penanggung Jawab:</label>
                            @if ($division->admin)
                                <p class="text-gray-800">{{ $division->admin->name }} ({{ $division->admin->email }})
                                </p>
                            @else
                                <p class="text-gray-500 italic">Belum ditugaskan</p>
                            @endif
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex flew-col sm:flex-row gap-2">
                            <button class="btn btn-warning w-full sm:w-auto" onclick="document.getElementById('modalEditDivisi-{{ $division->id }}').classList.remove('hidden')">Edit</button>
                            <form action="{{ route('superadmin.divisi.destroy', $division->id) }}" method="post"
                                onsubmit="return confirmDeleteDivisi(event)" class="w-full sm:w-auto">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger w-full sm:w-auto" type="submit">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Modal Edit Divisi --}}
                <x-modal id="modalEditDivisi-{{ $division->id }}" title="Edit Divisi">
                    <form method="POST" action="{{ route('superadmin.divisi.update', $division->id) }}"
                        class="space-y-4">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label for="edit_title_{{ $division->id }}"
                                class="block font-medium text-sm text-gray-700">Judul Divisi</label>
                            <input type="text" name="title" id="edit_title_{{ $division->id }}"
                                value="{{ $division->title }}" placeholder="Contoh : Divisi I, Divisi II, dst"
                                class="form-control">
                        </div>
                        <div>
                            <label for="edit_region_{{ $division->id }}"
                                class="block font-medium text-sm text-gray-700">Wilayah Divisi</label>
                            <input type="text" name="region" id="edit_region_{{ $division->id }}"
                                value="{{ $division->region }}"
                                placeholder="Contoh : Jawa Timur, Jawa Barat, dst (provinsi)" class="form-control">
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
                            <select name="admin_id" id="edit_admin_id_{{ $division->id }}" class="form-control">
                                <option value="">-- Pilih Admin (Opsional) --</option>
                                @foreach ($availableAdminsForEdit as $admin)
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

                {{-- Daftar Sub Divisi --}}
                <div class="bg-white rounded-2xl shadow p-6">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3 md:gap-0 mb-6">
                        <h2 class="text-xl font-semibold text-gray-800 leading-none">Daftar Sub Divisi</h2>
                        <button onclick="document.getElementById('modalTambahSub').classList.remove('hidden')"
                            class="btn btn-primary">
                            Tambah Sub Divisi
                        </button>
                    </div>


                    {{-- Modal Tambah Sub Divisi --}}
                    <x-modal id="modalTambahSub" title="Tambah Sub Divisi">
                        <form action="{{ route('superadmin.subdivisi.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="division_id" value="{{ $division->id }}">

                            <div class="mb-4">
                                <label class="block mb-1 font-semibold text-gray-700">Wilayah Sub Divisi</label>
                                <input type="text" name="title" class="form-control"
                                    placeholder="Contoh: Surabaya, Malang, Sidoarjo, dst." required>
                            </div>

                            <div class="mb-4">
                                <label class="block mb-1 font-semibold text-gray-700">Deskripsi</label>
                                <textarea name="description" class="form-control editor" placeholder="Masukkan isi konten sub divisi"></textarea>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </x-modal>

                    {{-- Modal Edit Sub Divisi --}}
                    <x-modal id="modalEditSub" title="Edit Sub Divisi">
                        <form id="formEditSub" method="POST">
                            @csrf
                            @method('POST')

                            <div class="mb-4">
                                <label class="block mb-1 font-semibold text-gray-700">Wilayah Sub Divisi</label>
                                <input type="text" name="title" id="editTitle" class="form-control" required>
                            </div>

                            <div class="mb-4">
                                <label class="block mb-1 font-semibold text-gray-700">Deskripsi</label>
                                <textarea name="description" id="editDescription" class="form-control editor">{!! $division->description !!}</textarea>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </x-modal>

                    @if ($division->subDivisions->isEmpty())
                        <p class="text-gray-500 italic">Belum ada sub divisi.</p>
                    @else
                        <ul class="divide-y divide-gray-200 pl-0">
                            @foreach ($division->subDivisions as $sub)
                                <li class="py-4">
                                    <div class="space-y-3">
                                        {{-- Title --}}
                                        <p class="font-semibold text-gray-800 text-base mb-0">Wilayah Sub Divisi:
                                            {{ $sub->title }}</p>

                                        {{-- Status --}}
                                        <span
                                            class="text-xs inline-block px-2 py-1 mt-1 rounded-full
            {{ $sub->is_approved ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                            {{ $sub->is_approved ? 'Disetujui' : 'Menunggu' }}
                                        </span>

                                        {{-- Description --}}
                                        <div class="text-gray-600 text-sm prose">
                                            {!! $sub->description !!}
                                        </div>

                                        {{-- Tombol Aksi --}}
                                        <div class="flex flex-col sm:flex-row gap-2">
                                            {{-- Toggle Approval --}}
                                            <form
                                                action="{{ route('superadmin.subdivisi.toggleApproval', $sub->id) }}"
                                                method="POST" class="w-full sm:w-auto">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-info w-full sm:w-auto">
                                                    {{ $sub->is_approved ? 'Batalkan Persetujuan' : 'Setujui' }}
                                                </button>
                                            </form>

                                            {{-- Edit --}}
                                            <button class="btn btn-warning w-full sm:w-auto"
                                                onclick="handleEdit(this)" data-id="{{ $sub->id }}"
                                                data-title="{{ $sub->title }}"
                                                data-description="{{ $sub->description }}"
                                                data-approved="{{ $sub->is_approved ? 'true' : 'false' }}">
                                                Edit
                                            </button>

                                            {{-- Hapus --}}
                                            <form action="{{ route('superadmin.subdivisi.delete', $sub->id) }}"
                                                method="POST" class="w-full sm:w-auto"
                                                onsubmit="return confirmDelete(event)">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-danger w-full sm:w-auto">Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                    @endif
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
    function handleEdit(button) {
        const id = button.dataset.id;
        const title = button.dataset.title;
        const description = button.dataset.description;

        document.getElementById('editTitle').value = title;

        if (tinymce.get('editDescription')) {
            tinymce.get('editDescription').setContent(description);
        } else {
            document.getElementById('editDescription').value = description;
        }

        const form = document.getElementById('formEditSub');
        form.action = `/superadmin/detail/sub-divisi/update/${id}`;

        document.getElementById('modalEditSub').classList.remove('hidden');
    }

    function confirmDelete(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Data sub divisi akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                e.target.submit();
            }
        });
    }
    function confirmDeleteDivisi(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Data divisi akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                e.target.submit();
            }
        });
    }
</script>
