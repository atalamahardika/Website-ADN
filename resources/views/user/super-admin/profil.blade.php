@include('head')

<body>
    <div class="flex h-screen">
        <x-layout.sidebar />
        <x-layout.content-bar :title="$title" :subtitle="$subtitle">
            {{-- Slot utama di sini, bisa diganti sesuai konten --}}
            @includeWhen($title === 'Profil', 'components.content.main')

            {{-- Tulis kode di bawah ini untuk isi kontennya --}}
            <div class="profile p-4 flex flex-col gap-4">
                {{-- Header Informasi Pribadi --}}
                <div class="wrapper flex items-center gap-3">
                    <img src="{{ asset('icon/User.png') }}" alt="User Icon" class="w-[40px] h-[40px]">
                    <h4 class="fw-bold m-0 p-0">Informasi Pribadi</h4>
                </div>
                <form action="{{ route('superadmin.profile.update') }}" method="post" enctype="multipart/form-data"
                    class="space-y-6" id="update-profile-form">
                    @csrf
                    @method('PATCH')

                    <!-- Foto Profil -->
                    <div class="flex items-center gap-4">
                        <img src="{{ $user->profile_photo_url }}" alt="Profile Photo"
                            class="w-24 h-24 rounded-full object-cover" id="preview-photo" />

                        <div class="wrapper">
                            <label for="profile_photo" class="btn btn-primary py-2 px-4 rounded mb-2">
                                Edit Foto
                            </label>
                            <input id="profile_photo" name="profile_photo" type="file" class="hidden"
                                accept="image/*">
                            <p class="mb-0 text-sm text-gray-500">Upload foto anda dengan rasio 1:1. Maks 2 MB.</p>

                            {{-- Tampilkan error validasi --}}
                            @error('profile_photo')
                                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>


                    <!-- Hidden untuk menyimpan hasil crop -->
                    <input type="hidden" name="cropped_image" id="croppedImageBase64">

                    <!-- Modal Crop -->
                    <div id="cropperModal" style="display:none;"
                        class="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
                        <div class="bg-white p-4 rounded shadow-lg max-w-xl w-full">
                            <div class="mb-2 text-center font-semibold">Crop Foto Profil (1:1)</div>
                            <div class="w-full aspect-square relative overflow-hidden bg-gray-100">
                                <img id="image-to-crop" class="absolute inset-0 max-w-full max-h-full m-auto" />
                            </div>
                            <div class="flex justify-end gap-2 mt-4">
                                <button type="button" id="cancelCrop" class="btn btn-secondary">Batal</button>
                                <button type="button" id="confirmCrop" class="btn btn-primary">Simpan</button>
                            </div>
                        </div>
                    </div>

                    <!-- Nama -->
                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Nama')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                            :value="old('name', $user->name)" required autofocus autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" name="email" type="text" class="mt-1 block w-full"
                            :value="old('email', $user->email)" readonly autofocus autocomplete="email" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </x-layout.content-bar>
    </div>

    {{-- SweetAlert2 --}}
    @if (session('status'))
        <script>
            Swal.fire({
                title: 'Berhasil!',
                text: "{{ session('status') }}",
                icon: 'success',
                showConfirmButton: false,
                timer: 2000
            });
        </script>
    @endif

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
    // Cropper.js untuk foto profil
    let cropper;
    const inputPhoto = document.getElementById('profile_photo');
    const modal = document.getElementById('cropperModal');
    const preview = document.getElementById('preview-photo');
    const imageToCrop = document.getElementById('image-to-crop');
    const croppedInput = document.getElementById('croppedImageBase64');

    inputPhoto.addEventListener('change', function(e) {
        const file = e.target.files[0];

        if (!file) return;

        const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        const maxSize = 2 * 1024 * 1024; // 2 MB

        if (!validTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Format Tidak Valid',
                text: 'Hanya file JPG, JPEG, dan PNG yang diperbolehkan.',
            });
            inputPhoto.value = '';
            return;
        }

        if (file.size > maxSize) {
            Swal.fire({
                icon: 'error',
                title: 'Ukuran File Terlalu Besar',
                text: 'Ukuran foto tidak boleh lebih dari 2 MB.',
            });
            inputPhoto.value = '';
            return;
        }

        // Valid file, lanjut tampilkan cropper modal dan preview
        const reader = new FileReader();
        reader.onload = function(e) {
            imageToCrop.src = e.target.result;
            modal.style.display = 'flex';

            if (cropper) cropper.destroy();
            cropper = new Cropper(imageToCrop, {
                aspectRatio: 1,
                viewMode: 1,
                autoCropArea: 1,
                responsive: true
            });
        };
        reader.readAsDataURL(file);
    });


    document.getElementById('cancelCrop').addEventListener('click', () => {
        modal.style.display = 'none';
        inputPhoto.value = '';
        if (cropper) cropper.destroy();
    });

    document.getElementById('confirmCrop').addEventListener('click', () => {
        const canvas = cropper.getCroppedCanvas({
            width: 400,
            height: 400
        });
        croppedInput.value = canvas.toDataURL('image/jpeg');
        preview.src = canvas.toDataURL('image/jpeg');
        modal.style.display = 'none';
    });
</script>
