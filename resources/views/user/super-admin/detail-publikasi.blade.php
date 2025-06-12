@include('head')

<body>
    <div class="flex h-screen">
        <x-layout.sidebar />
        <x-layout.content-bar :title="$title" :subtitle="$subtitle">
            {{-- Slot utama di sini, bisa diganti sesuai konten --}}
            @includeWhen($title === 'Detail Publikasi ADN', 'components.content.main')

            {{-- Tulis kode di bawah ini untuk isi kontennya --}}
            <div class="container mx-auto py-6 px-4">
                <h1 class="text-3xl font-bold text-center mb-4">{{ $publications->title }}</h1>
                <div class="prose max-w-3xl mx-auto mb-6">
                    {!! $publications->content !!}
                </div>

                <div class="flex justify-center gap-4">
                    <a href="{{ route('superadmin.publikasi-adn') }}" class="btn btn-secondary">Kembali</a>

                    {{-- Modal Edit --}}
                    <button class="btn btn-warning" onclick="editPublikasi('{{ $publications->id }}')">Edit</button>
                    <x-modal id="modalEditPublikasi" title="Edit Publikasi Organisasi">
                        <form id="formEditPublikasi" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="mb-4">
                                <label for="edit_title" class="block font-semibold">Judul Publikasi</label>
                                <input type="text" name="title" id="edit_title" class="form-control">
                            </div>

                            <div class="mb-4">
                                <label for="edit_content" class="block font-semibold">Isi Publikasi</label>
                                <textarea name="content" id="edit_content" rows="10" class="form-control editor">{!! $publications->content !!}</textarea>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>
                    </x-modal>

                    <form id="deleteForm" action="{{ route('superadmin.publikasi-adn.delete', $publications->id) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                    <button class="btn btn-danger" onclick="hapusPublikasi()">Hapus</button>
                </div>
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

    @if (session('modal') === 'modalEditPublikasi')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('modalEditPublikasi').classList.remove('hidden');
            });
        </script>
    @endif
</body>
<script>
    function hapusPublikasi() {
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Data publikasi akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e3342f',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteForm').submit();
            }
        })
    }

    function editPublikasi(id) {
        const publikasi = @json($publications);

        document.getElementById('edit_title').value = publikasi.title;
        document.getElementById('edit_content').value = decodeHTMLEntities(publikasi.content);

        function decodeHTMLEntities(text) {
            const textarea = document.createElement('textarea');
            textarea.innerHTML = text;
            return textarea.value;
        }

        // ✅ Gunakan slug, bukan ID!
        const form = document.getElementById('formEditPublikasi');
        form.action = `/superadmin/publikasi-adn/detail-publikasi/${publikasi.slug}`;

        document.getElementById('modalEditPublikasi').classList.remove('hidden');
    }
</script>