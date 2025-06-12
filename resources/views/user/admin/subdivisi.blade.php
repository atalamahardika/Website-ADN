@include('head')

<body>
    <div class="flex h-screen">
        <x-layout.sidebar />
        <x-layout.content-bar :title="$title" :subtitle="$subtitle">
            {{-- Slot utama di sini, bisa diganti sesuai konten --}}
            @includeWhen($title === 'Subdivisi', 'components.content.main')

            {{-- Tulis kode di bawah ini untuk isi kontennya --}}
            @if (!$user->division)
                <div class="alert alert-warning">
                    <strong>Perhatian:</strong> Anda belum ditugaskan sebagai penanggung jawab divisi manapun.
                </div>
            @else
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Daftar Sub Divisi</h5>
                    <button class="btn btn-primary"
                        onclick="document.getElementById('modalTambahSubDivisi').classList.remove('hidden')">
                        Tambah Sub Divisi
                    </button>
                </div>

                @forelse ($subDivisions as $sub)
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="fw-bold mb-1">{{ $sub->title }}</h6>
                            <div class="prose">
                                {!! $sub->description !!}
                            </div>
                            <span class="badge {{ $sub->is_approved ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ $sub->is_approved ? 'Disetujui' : 'Menunggu Persetujuan' }}
                            </span>

                            <div class="mt-3">
                                <button class="btn btn-warning"
                                    onclick="document.getElementById('modalEditSubDivisi{{ $sub->id }}').classList.remove('hidden')">
                                    Edit
                                </button>

                                {{-- Modal Edit --}}
                                <x-modal id="modalEditSubDivisi{{ $sub->id }}" title="Edit Sub Divisi">
                                    <form action="{{ route('admin.subdivisi.update', $sub->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="mb-4">
                                            <label for="title{{ $sub->id }}" class="form-label">Wilayah Sub
                                                Divisi</label>
                                            <input type="text" name="title" id="title{{ $sub->id }}"
                                                class="form-control" required value="{{ $sub->title }}">
                                        </div>
                                        <div class="mb-4">
                                            <label for="description{{ $sub->id }}"
                                                class="form-label">Deskripsi</label>
                                            <textarea name="description" id="description{{ $sub->id }}" class="form-control editor" rows="5">{{ $sub->description }}</textarea>
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </x-modal>

                                <form action="{{ route('admin.subdivisi.destroy', $sub->id) }}" method="POST"
                                    class="d-inline" onsubmit="return confirmDelete(event)">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted">Belum ada sub divisi.</p>
                @endforelse

                {{-- Modal Tambah Sub Divisi --}}
                <x-modal id="modalTambahSubDivisi" title="Tambah Sub Divisi">
                    <form action="{{ route('admin.subdivisi.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="title" class="form-label">Wilayah Sub Divisi</label>
                            <input type="text" name="title" id="title" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea name="description" id="description" class="form-control editor" rows="5"></textarea>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-success">Simpan</button>
                        </div>
                    </form>
                </x-modal>

                {{-- Modal Edit --}}
                {{-- <x-modal id="modalEditSubDivisi{{ $sub->id }}" title="Edit Sub Divisi">
                    <form action="{{ route('admin.subdivisi.update', $sub->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="title{{ $sub->id }}" class="form-label">Wilayah Sub Divisi</label>
                            <input type="text" name="title" id="title{{ $sub->id }}" class="form-control"
                                required value="{{ $sub->title }}">
                        </div>
                        <div class="mb-4">
                            <label for="description{{ $sub->id }}" class="form-label">Deskripsi</label>
                            <textarea name="description" id="description{{ $sub->id }}" class="form-control editor" rows="5">{{ $sub->description }}</textarea>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                        </div>
                    </form>
                </x-modal> --}}
            @endif
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

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('error') }}',
            });
        </script>
    @endif
</body>
<script>
    function confirmDelete(event) {
        event.preventDefault();
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Data tidak dapat dikembalikan setelah dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                event.target.submit();
            }
        });
    }
</script>
