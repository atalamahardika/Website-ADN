@include('head')

<body>
    <div class="flex h-screen">
        <x-layout.sidebar />
        <x-layout.content-bar :title="$title" :subtitle="$subtitle">
            {{-- Slot utama di sini, bisa diganti sesuai konten --}}
            @includeWhen($title === 'Detail Berita', 'components.content.main')

            {{-- Tulis kode di bawah ini untuk isi kontennya --}}
            <div class="container mx-auto py-6 px-4">
                <h1 class="text-3xl font-bold text-center mb-4">{{ $news->title }}</h1>
                <p class="text-center text-gray-500 text-sm mb-4">
                    {{ \Carbon\Carbon::parse($news->created_at)->translatedFormat('d F Y') }}
                </p>

                <div class="flex justify-center mb-6">
                    <img src="{{ asset($news->image) }}" alt="Gambar Berita"
                        class="rounded shadow w-full max-w-3xl object-cover aspect-[3/2]">
                </div>

                <div class="prose max-w-3xl mx-auto mb-6">
                    {!! $news->content !!}
                </div>

                <div class="text-center text-sm text-gray-700 mb-6">
                    Sumber:
                    @if (Str::startsWith($news->source_link, ['http://', 'https://']))
                        <a href="{{ $news->source_link }}" target="_blank" class="text-blue-600 underline">
                            {{ $news->source_link }}
                        </a>
                    @else
                        {{ $news->source_link }}
                    @endif
                </div>

                <div class="flex justify-center gap-4">
                    <a href="{{ route('superadmin.berita') }}" class="btn btn-secondary">Kembali</a>

                    <button class="btn btn-warning" onclick="editBerita('{{ $news->id }}')">Edit</button>
                    <x-modal id="modalEditBerita" title="Edit Berita">
                        <form id="formEditBerita" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="mb-4">
                                <label for="edit_title" class="block font-semibold">Judul Berita</label>
                                <input type="text" name="title" id="edit_title" class="form-control">
                            </div>

                            <div class="mb-4">
                                <label for="edit_content" class="block font-semibold">Isi Berita</label>
                                <textarea name="content" id="edit_content" rows="6" class="form-control editor">{!! $news->content !!}</textarea>
                            </div>

                            <div class="mb-4">
                                <label for="edit_source_link" class="block font-semibold">Sumber Berita</label>
                                <input type="text" name="source_link" id="edit_source_link" class="form-control">
                            </div>

                            <div class="mb-4">
                                <label for="edit_image" class="block font-semibold">Gambar Berita</label>
                                <input type="file" name="image" id="edit_image" accept="image/*"
                                    class="form-control">
                                <div class="mt-2">
                                    <img id="edit_preview" class="max-w-full h-auto rounded border mt-2">
                                </div>
                                <input type="hidden" name="cropped_image" id="edit_cropped_image">
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>
                    </x-modal>


                    <form id="deleteForm" action="{{ route('superadmin.berita.delete', $news->id) }}" method="POST"
                        class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                    <button class="btn btn-danger" onclick="hapusBerita()">Hapus</button>
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

    @if (session('modal') === 'modalEditBerita')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('modalEditBerita').classList.remove('hidden');
            });
        </script>
    @endif


</body>
<script>
    function hapusBerita() {
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Data berita akan dihapus secara permanen!",
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

    let cropperEdit;
    const editImage = document.getElementById('edit_image');
    const editPreview = document.getElementById('edit_preview');
    const editCroppedInput = document.getElementById('edit_cropped_image');

    editImage.addEventListener('change', function(e) {
        const file = e.target.files[0];
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        const maxSize = 2 * 1024 * 1024; // 2 MB

        if (!allowedTypes.includes(file.type)) {
            Swal.fire('Format Gambar Tidak Didukung', 'Gunakan JPG, JPEG, atau PNG.', 'error');
            editImage.value = "";
            return;
        }

        if (file.size > maxSize) {
            Swal.fire('Ukuran Gambar Terlalu Besar', 'Maksimal 2MB.', 'error');
            editImage.value = "";
            return;
        }

        const reader = new FileReader();
        reader.onload = function(evt) {
            editPreview.src = evt.target.result;

            if (cropperEdit) cropperEdit.destroy();

            cropperEdit = new Cropper(editPreview, {
                aspectRatio: 3 / 2,
                viewMode: 1,
                autoCropArea: 1,
                crop(event) {
                    const canvas = cropperEdit.getCroppedCanvas();
                    canvas.toBlob(function(blob) {
                        const reader = new FileReader();
                        reader.onloadend = function() {
                            editCroppedInput.value = reader.result;
                        }
                        reader.readAsDataURL(blob);
                    }, 'image/jpeg');
                }
            });
        }
        reader.readAsDataURL(file);
    });

    function editBerita(id) {
        const berita = @json($news);

        document.getElementById('edit_title').value = berita.title;
        document.getElementById('edit_content').value = decodeHTMLEntities(berita.content);

        function decodeHTMLEntities(text) {
            const textarea = document.createElement('textarea');
            textarea.innerHTML = text;
            return textarea.value;
        }

        document.getElementById('edit_source_link').value = berita.source_link;
        document.getElementById('edit_preview').src = "{{ asset('') }}" + berita.image;

        // ✅ Gunakan slug, bukan ID!
        const form = document.getElementById('formEditBerita');
        form.action = `superadmin/berita/detail-berita/${berita.slug}`;

        document.getElementById('modalEditBerita').classList.remove('hidden');
    }
</script>
