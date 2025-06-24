@include('head')

<body>
    <div class="flex h-screen">
        <x-layout.sidebar />
        <x-layout.content-bar :title="$title" :subtitle="$subtitle">
            {{-- Slot utama di sini, bisa diganti sesuai konten --}}
            @includeWhen($title === 'Beranda Divisi', 'components.content.main')

            {{-- Tulis kode di bawah ini untuk isi kontennya --}}
            @if (!$user->division)
                <div class="alert alert-warning">
                    <strong>Perhatian:</strong> Anda tidak mengelola divisi manapun. Silakan hubungi Super Admin untuk
                    penugasan.
                </div>
            @else
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Konten Beranda Divisi</h5>
                    <button class="btn btn-primary"
                        onclick="document.getElementById('modalEditDeskripsi').classList.remove('hidden')">
                        Edit Deskripsi
                    </button>
                </div>

                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="fw-bold mb-1">{{ $user->division->title }}</h4>
                        <p class="text-muted mb-4">Wilayah {{ $user->division->region }}</p>

                        <div class="text-start prose">
                            {!! $user->division->description !!}
                        </div>
                    </div>
                </div>


                {{-- Modal Edit Deskripsi --}}
                <x-modal id="modalEditDeskripsi" title="Edit Deskripsi Divisi">
                    <form action="{{ route('admin.beranda-divisi.update') }}" method="POST" id="formEditDeskripsi">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="description" class="block font-semibold">Deskripsi Divisi</label>
                            <textarea name="description" id="description" class="form-control editor" rows="10">{{ old('description', $user->division->description ?? '') }}</textarea>
                            @error('description')
                                <div class="text-danger mt-1">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </x-modal>
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
    document.getElementById('formEditDeskripsi').addEventListener('submit', function(e) {
        e.preventDefault(); // Cegah submit default

        // Ambil konten dari TinyMCE
        let content = tinymce.get('description').getContent({
            format: 'text'
        }).trim();

        if (!content) {
            Swal.fire({
                icon: 'warning',
                title: 'Validasi Gagal',
                text: 'Deskripsi divisi tidak boleh kosong!',
            });
        } else {
            // Submit form jika valid
            this.submit();
        }
    });
</script>
