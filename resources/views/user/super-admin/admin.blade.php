@include('head')

<body>
    <div class="flex h-screen">
        <x-layout.sidebar />
        <x-layout.content-bar :title="$title" :subtitle="$subtitle">
            {{-- Slot utama di sini, bisa diganti sesuai konten --}}
            @includeWhen($title === 'Admin', 'components.content.main')

            {{-- Tulis kode di bawah ini untuk isi kontennya --}}
            <div class="card" style="background-color: #EDF4EA; padding: 24px;">
                <div class="flex justify-between items-center mb-4">
                    <h5 class="font-semibold">Daftar Admin</h5>
                    <button class="btn btn-primary"
                        onclick="document.getElementById('modalTambahAdmin').classList.remove('hidden')">Tambah
                        Admin</button>
                </div>

                <ul class="space-y-2">
                    @forelse ($admins as $admin)
                        <li class="flex justify-between items-center bg-white p-3 rounded shadow-sm">
                            <div>
                                <strong>{{ $admin->name }}</strong><br>
                                <small>{{ $admin->email }}</small>
                            </div>
                            <form method="POST" action="{{ route('superadmin.admin.destroy', $admin->id) }}"
                                onsubmit="return confirmDelete(this)">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </li>
                    @empty
                        <li class="bg-yellow-50 text-yellow-700 text-center p-4 rounded shadow-sm">
                            Tidak ada data admin yang ditambahkan.
                        </li>
                    @endforelse
                </ul>


                {{-- Modal Tambah Admin --}}
                <x-modal id="modalTambahAdmin" title="Tambah Admin Baru">
                    <form method="POST" action="{{ route('superadmin.admin.store') }}" class="space-y-3">
                        @csrf
                        @method('POST')
                        <div class="mb-4">
                            <label for="name" class="block font-semibold">Nama Admin</label>
                            <input type="text" name="name" placeholder="Nama" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block font-semibold">Email Admin</label>
                            <input type="email" name="email" placeholder="Email" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="block font-semibold">Password</label>
                            <input type="password" name="password" placeholder="Password" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label for="password_confirmation" class="block font-semibold">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" placeholder="Konfirmasi Password"
                                class="form-control" required>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </x-modal>
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
    function confirmDelete(form) {
        event.preventDefault();
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Admin ini akan dihapus permanen.",
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
