@include('head')

<body>
    <div class="flex h-screen">
        <x-layout.sidebar />
        <x-layout.content-bar :title="$title" :subtitle="$subtitle">
            {{-- Slot utama di sini, bisa diganti sesuai konten --}}
            @includeWhen($title === 'Profil', 'components.content.main')

            <div class="profile p-4 flex flex-col gap-4">
                {{-- Header Informasi Pribadi --}}
                <div class="wrapper flex items-center gap-3">
                    <img src="{{ asset('icon/User.png') }}" alt="User Icon" class="w-[40px] h-[40px]">
                    <h4 class="fw-bold m-0 p-0">Informasi Pribadi</h4>
                </div>
                <form method="POST" enctype="multipart/form-data" class="space-y-6" id="update-profile-form"
                    action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')

                    <!-- Foto Profil -->
                    <div class="flex items-center gap-4">
                        <img src="{{ Auth::user()->profile_photo_url }}" alt="Profile Photo"
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
                        <x-text-input id="name" name="name" type="text" class="form-control"
                            :value="old('name', $user->name)" required autofocus autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" name="email" type="text" class="form-control"
                            :value="old('email', $user->email)" readonly />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Gelar Depan -->
                    <div class="mb-4">
                        <x-input-label for="gelar_depan" :value="__('Gelar Depan')" />
                        <x-text-input id="gelar_depan" name="gelar_depan" type="text" class="form-control"
                            :value="old('gelar_depan', $user->member->gelar_depan ?? '')" placeholder="Contoh: Dr. atau Prof." />
                        <x-input-error :messages="$errors->get('gelar_depan')" class="mt-2" />
                    </div>

                    <!-- Gelar Belakang -->
                    @for ($i = 1; $i <= 3; $i++)
                        <div class="mb-4">
                            <x-input-label for="gelar_belakang_{{ $i }}" :value="'Gelar Belakang ' . $i" />
                            <x-text-input id="gelar_belakang_{{ $i }}"
                                name="gelar_belakang_{{ $i }}" type="text" class="form-control"
                                :value="old('gelar_belakang_' . $i, $user->member->{'gelar_belakang_' . $i} ?? '')" placeholder="Contoh: S.Kom. atau M.Kom." />
                            <x-input-error :messages="$errors->get('gelar_belakang_' . $i)" class="mt-2" />
                        </div>
                    @endfor

                    <!-- NIK -->
                    <div class="mb-4">
                        <x-input-label for="nik" :value="__('NIK')" />
                        <x-text-input id="nik" name="nik" type="text" class="form-control"
                            :value="old('nik', $user->member->nik ?? '')" placeholder="Masukkan NIK anda sesuai KTP" required />
                        <x-input-error :messages="$errors->get('nik')" class="mt-2" />
                    </div>

                    <!-- Tempat Lahir -->
                    <div class="mb-4">
                        <x-input-label for="tempat_lahir" :value="__('Tempat Lahir')" />
                        <x-text-input id="tempat_lahir" name="tempat_lahir" type="text" class="form-control"
                            :value="old('tempat_lahir', $user->member->tempat_lahir ?? '')" placeholder="Masukkan tempat anda lahir" required />
                        <x-input-error :messages="$errors->get('tempat_lahir')" class="mt-2" />
                    </div>

                    <!-- Tanggal Lahir -->
                    <div class="mb-4">
                        <x-input-label for="tanggal_lahir" :value="__('Tanggal Lahir')" />
                        <x-text-input id="tanggal_lahir" name="tanggal_lahir" type="date" class="form-control"
                            :value="old('tanggal_lahir', ($user->member && $user->member->tanggal_lahir) ? $user->member->tanggal_lahir->format('Y-m-d') : '')" required />
                        <x-input-error :messages="$errors->get('tanggal_lahir')" class="mt-2" />
                    </div>

                    <!-- No HP -->
                    <div class="mb-4">
                        <x-input-label for="no_hp" :value="__('No HP')" />
                        <x-text-input id="no_hp" name="no_hp" type="text" class="form-control"
                            :value="old('no_hp', $user->member->no_hp ?? '')" placeholder="Masukkan nomor handphone yang aktif" required />
                        <x-input-error :messages="$errors->get('no_hp')" class="mt-2" />
                    </div>

                    <!-- No WhatsApp -->
                    <div class="mb-4">
                        <x-input-label for="no_wa" :value="__('No WhatsApp')" />
                        <x-text-input id="no_wa" name="no_wa" type="text" class="form-control"
                            :value="old('no_wa', $user->member->no_wa ?? '')" placeholder="Masukkan nomor whatsapp yang aktif" required />
                        <x-input-error :messages="$errors->get('no_wa')" class="mt-2" />
                    </div>

                    <!-- Email Institusi -->
                    <div class="mb-4">
                        <x-input-label for="email_institusi" :value="__('Email Institusi')" />
                        <x-text-input id="email_institusi" name="email_institusi" type="email"
                            class="form-control" :value="old('email_institusi', $user->member->email_institusi ?? '')"
                            placeholder="Masukkan email institusi anda" required />
                        <x-input-error :messages="$errors->get('email_institusi')" class="mt-2" />
                    </div>

                    <!-- Universitas -->
                    <div class="mb-4">
                        <x-input-label for="universitas" :value="__('Universitas')" />
                        <x-text-input id="universitas" name="universitas" type="text" class="form-control"
                            :value="old('universitas', $user->member->universitas ?? '')"
                            placeholder="Contoh: Brawijaya, Airlangga, Gajah Mada (tanpa universitas)" required />
                        <x-input-error :messages="$errors->get('universitas')" class="mt-2" />
                    </div>

                    <!-- Fakultas -->
                    <div class="mb-4">
                        <x-input-label for="fakultas" :value="__('Fakultas')" />
                        <x-text-input id="fakultas" name="fakultas" type="text" class="form-control"
                            :value="old('fakultas', $user->member->fakultas ?? '')"
                            placeholder="Contoh: Ilmu Komputer, Peternakan, Perikanan & Kelautan (tanpa fakultas)" required />
                        <x-input-error :messages="$errors->get('fakultas')" class="mt-2" />
                    </div>

                    <!-- Prodi -->
                    <div class="mb-4">
                        <x-input-label for="prodi" :value="__('Program Studi')" />
                        <x-text-input id="prodi" name="prodi" type="text" class="form-control"
                            :value="old('prodi', $user->member->prodi ?? '')" placeholder="Contoh: Teknik Informatika, Teknik Sipil" required />
                        <x-input-error :messages="$errors->get('prodi')" class="mt-2" />
                    </div>

                    <!-- Alamat -->
                    <div class="mb-4">
                        <x-input-label for="alamat_jalan" :value="__('Alamat Jalan')" />
                        <textarea id="alamat_jalan" name="alamat_jalan" rows="3"
                            class="form-control rounded-md shadow-sm border-gray-300" placeholder="Masukkan alamat jalan lengkap anda" required>{{ old('alamat_jalan', $user->member->alamat_jalan ?? '') }}</textarea>
                        <x-input-error :messages="$errors->get('alamat_jalan')" class="mt-2" />
                    </div>

                    <!-- Provinsi -->
                    <div class="mb-4">
                        <x-input-label for="provinsi" :value="__('Provinsi')" />
                        <select id="provinsi" name="provinsi" class="form-control"
                            data-selected="{{ $user->member->provinsi }}" required>
                            <option value="">Pilih Provinsi</option>
                            {{-- Options dimuat via JavaScript --}}
                        </select>
                        <x-input-error :messages="$errors->get('provinsi')" class="mt-2" />
                    </div>
                    <!-- Kabupaten/Kota -->
                    <div class="mb-4">
                        <x-input-label for="kabupaten" :value="__('Kabupaten/Kota')" />
                        <select id="kabupaten" name="kabupaten" class="form-control"
                            data-selected="{{ $user->member->kabupaten }}" required>
                            <option value="">Pilih Kabupaten</option>
                            {{-- Options dimuat via JavaScript --}}
                        </select>
                        <x-input-error :messages="$errors->get('kabupaten')" class="mt-2" />
                    </div>
                    <!-- Kecamatan -->
                    <div class="mb-4">
                        <x-input-label for="kecamatan" :value="__('Kecamatan')" />
                        <select id="kecamatan" name="kecamatan" class="form-control"
                            data-selected="{{ $user->member->kecamatan }}" required>
                            <option value="">Pilih Kecamatan</option>
                            {{-- Options dimuat via JavaScript --}}
                        </select>
                        <x-input-error :messages="$errors->get('kecamatan')" class="mt-2" />
                    </div>
                    <!-- Kelurahan -->
                    <div class="mb-4">
                        <x-input-label for="kelurahan" :value="__('Kelurahan')" />
                        <select id="kelurahan" name="kelurahan" class="form-control"
                            data-selected="{{ $user->member->kelurahan }}" required>
                            <option value="">Pilih Kelurahan</option>
                            {{-- Options dimuat via JavaScript --}}
                        </select>
                        <x-input-error :messages="$errors->get('kelurahan')" class="mt-2" />
                    </div>

                    <!-- Kode Pos -->
                    <div class="mb-4">
                        <x-input-label for="kode_pos" :value="__('Kode Pos')" />
                        <x-text-input id="kode_pos" name="kode_pos" type="text" class="form-control"
                            :value="old('kode_pos', $user->member->kode_pos ?? '')" readonly />
                        <x-input-error :messages="$errors->get('kode_pos')" class="mt-2" />
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

    // API untuk memuat data wilayah
    document.addEventListener('DOMContentLoaded', function() {
        const baseUrl = '/api/wilayah';

        const provinsiSelect = document.getElementById('provinsi');
        const kabupatenSelect = document.getElementById('kabupaten');
        const kecamatanSelect = document.getElementById('kecamatan');
        const kelurahanSelect = document.getElementById('kelurahan');
        const kodePosInput = document.getElementById('kode_pos');

        const selectedProvinsi = provinsiSelect.getAttribute('data-selected');
        const selectedKabupaten = kabupatenSelect.getAttribute('data-selected');
        const selectedKecamatan = kecamatanSelect.getAttribute('data-selected');
        const selectedKelurahan = kelurahanSelect.getAttribute('data-selected');

        function clearOptions(select, label) {
            select.innerHTML = `<option value="">Pilih ${label}</option>`;
        }

        function populateSelect(select, data, label) {
            clearOptions(select, label);
            const selectedName = select.getAttribute('data-selected');

            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.name;
                if (item.name === selectedName) option.selected = true;
                select.appendChild(option);
            });
        }

        // Prefill Provinsi -> Kabupaten -> Kecamatan -> Kelurahan -> Kode Pos
        fetch(`${baseUrl}/provinces`)
            .then(res => res.json())
            .then(data => {
                populateSelect(provinsiSelect, data, 'Provinsi');
                const prov = data.find(p => p.name === selectedProvinsi);
                if (prov) {
                    provinsiSelect.value = prov.id;

                    fetch(`${baseUrl}/cities/${prov.id}`)
                        .then(res => res.json())
                        .then(data => {
                            populateSelect(kabupatenSelect, data, 'Kabupaten');
                            const kab = data.find(c => c.name === selectedKabupaten);
                            if (kab) {
                                kabupatenSelect.value = kab.id;

                                fetch(`${baseUrl}/districts/${kab.id}`)
                                    .then(res => res.json())
                                    .then(data => {
                                        populateSelect(kecamatanSelect, data, 'Kecamatan');
                                        const kec = data.find(k => k.name === selectedKecamatan);
                                        if (kec) {
                                            kecamatanSelect.value = kec.id;

                                            fetch(`${baseUrl}/subdistricts/${kec.id}`)
                                                .then(res => res.json())
                                                .then(data => {
                                                    populateSelect(kelurahanSelect, data,
                                                        'Kelurahan');
                                                    const kel = data.find(k => k.name ===
                                                        selectedKelurahan);
                                                    if (kel) {
                                                        kelurahanSelect.value = kel.id;

                                                        fetch(`${baseUrl}/postalcode/${kel.id}`)
                                                            .then(res => res.json())
                                                            .then(data => {
                                                                if (data.postal_code) {
                                                                    kodePosInput.value =
                                                                        data.postal_code;
                                                                }
                                                            });
                                                    }
                                                });
                                        }
                                    });
                            }
                        });
                }
            });

        // Event handler untuk user action
        provinsiSelect.addEventListener('change', function() {
            fetch(`${baseUrl}/cities/${this.value}`)
                .then(res => res.json())
                .then(data => populateSelect(kabupatenSelect, data, 'Kabupaten/Kota'));

            clearOptions(kabupatenSelect, 'Kabupaten/Kota');
            clearOptions(kecamatanSelect, 'Kecamatan');
            clearOptions(kelurahanSelect, 'Kelurahan');
            kodePosInput.value = '';
        });

        kabupatenSelect.addEventListener('change', function() {
            fetch(`${baseUrl}/districts/${this.value}`)
                .then(res => res.json())
                .then(data => populateSelect(kecamatanSelect, data, 'Kecamatan'));

            clearOptions(kecamatanSelect, 'Kecamatan');
            clearOptions(kelurahanSelect, 'Kelurahan');
            kodePosInput.value = '';
        });

        kecamatanSelect.addEventListener('change', function() {
            fetch(`${baseUrl}/subdistricts/${this.value}`)
                .then(res => res.json())
                .then(data => populateSelect(kelurahanSelect, data, 'Kelurahan'));

            clearOptions(kelurahanSelect, 'Kelurahan');
            kodePosInput.value = '';
        });

        kelurahanSelect.addEventListener('change', function() {
            fetch(`${baseUrl}/postalcode/${this.value}`)
                .then(res => res.json())
                .then(data => {
                    kodePosInput.value = data.postal_code || '';
                });
        });
    });
</script>
