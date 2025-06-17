@include('head')

<body>
    <div class="flex h-screen">
        <x-layout.sidebar />
        <x-layout.content-bar :title="$title" :subtitle="$subtitle">
            {{-- Slot utama di sini, bisa diganti sesuai konten --}}
            @includeWhen($title === 'Tri Dharma Perguruan Tinggi', 'components.content.main')

            {{-- Tulis kode di bawah ini untuk isi kontennya --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">Tri Dharma Perguruan Tinggi</h5>
                <button class="btn btn-primary"
                    onclick="document.getElementById('modalTambahTridharma').classList.remove('hidden')">
                    Tambah
                </button>
            </div>

            {{-- Daftar Tri Dharma --}}
            @if ($tridharma->count())
                @foreach ($tridharma as $index => $item)
                    <div class="mb-6 border-b pb-4">
                        <div class="flex justify-between items-start mb-1">
                            <h3 class="text-lg font-semibold">
                                {{ $index + 1 }}. {{ $item->title }}
                            </h3>
                            <div class="flex gap-2">
                                <button onclick="editTridharma('{{ $item->id }}')" class="btn btn-warning">
                                    Edit
                                </button>
                                <form id="deleteForm-{{ $item->id }}"
                                    action="{{ route('superadmin.tridharma.delete', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger"
                                        onclick="hapusTridharma({{ $item->id }})">Hapus</button>
                                </form>

                            </div>
                        </div>
                        <div class="prose mb-6">
                            {!! $item->content !!}
                        </div>
                    </div>

                    {{-- Modal Edit --}}
                    <x-modal id="modalEditTridharma" title="Edit Tri Dharma">
                        <form id="formEditTridharma" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="mb-4">
                                <label for="edit_title" class="block font-semibold">Judul Tri Dharma</label>
                                <input type="text" name="title" id="edit_title" class="form-control">
                            </div>
                            <div class="mb-4">
                                <label for="edit_content" class="block font-semibold">Isi Konten</label>
                                <textarea name="content" id="edit_content" class="form-control editor" rows="10">{!! $item->content !!}</textarea>
                            </div>
                            <div class="text-center">
                                <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
                            </div>
                        </form>
                    </x-modal>
                @endforeach
            @else
                <p class="text-gray-500 text-center">Belum ada tri dharma perguruan tinggi yang ditambahkan</p>
            @endif

            {{-- Modal Tambah Tri Dharma --}}
            <x-modal id="modalTambahTridharma" title="Tambah Tri Dharma">
                <form action="{{ route('superadmin.tridharma.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <div class="mb-4">
                        <label for="title" class="block font-semibold">Judul Tri Dharma</label>
                        <input type="text" name="title" id="title"
                            placeholder="Contoh : Pendidikan dan Pengajaran" value="{{ old('title') }}"
                            class="form-control @error('title') border-red-500 @enderror">
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="content" class="block font-semibold">Isi Konten</label>
                        <textarea name="content" id="content" rows="10" placeholder="Isi konten tri dharma"
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

    @if (session('modal') === 'modalTambahTridharma')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('modalTambahTridharma').classList.remove('hidden');
            });
        </script>
    @endif

    @if (session('modal') === 'modalEditTridharma')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('modalEditTridharma').classList.remove('hidden');
            });
        </script>
    @endif
</body>
<script>
    function hapusTridharma(id) {
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Data akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e3342f',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`deleteForm-${id}`).submit();
            }
        })
    }


    const allTridharma = @json($tridharma);

    function editTridharma(id) {
        const tridharma = allTridharma.find(item => item.id == id);

        if (!tridharma) return;

        document.getElementById('edit_title').value = tridharma.title;
        // document.getElementById('edit_content').value = decodeHTMLEntities(tridharma.content);

        if (tinymce.get("edit_content")) {
            tinymce.get("edit_content").setContent(tridharma.content);
        }

        function decodeHTMLEntities(text) {
            const textarea = document.createElement('textarea');
            textarea.innerHTML = text;
            return textarea.value;
        }

        const form = document.getElementById('formEditTridharma');
        form.action = `/superadmin/tridharma/update/${tridharma.id}`;

        document.getElementById('modalEditTridharma').classList.remove('hidden');

        // const tridharma = @json($tridharma)

        // document.getElementById('edit_title').value = tridharma.title;
        // document.getElementById('edit_content').value = decodeHTMLEntities(tridharma.content);



        // const form = document.getElementById('formEditTridharma');
        // form.action = `superadmin/tridharma/${tridharma.id}`;

        // document.getElementById('modalEditTridharma').classList.remove('hidden');
    }
</script>
