@include('head')

<body>
    <div class="flex h-screen">
        <x-layout.sidebar />
        <x-layout.content-bar :title="$title" :subtitle="$subtitle">
            {{-- Slot utama di sini, bisa diganti sesuai konten --}}
            @includeWhen($title === 'Ganti Password', 'components.content.main')

            {{-- Tulis kode di bawah ini untuk isi kontennya --}}
            <div class="card w-full max-w-xl mx-auto" style="background-color: #EDF4EA; padding: 24px;">
                <form method="POST" action="{{ route('superadmin.ganti-password.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    {{-- Password lama --}}
                    <div>
                        <label for="current_password" class="block font-semibold">Password Lama</label>
                        <input type="password" name="current_password" id="current_password"
                            class="form-input w-full rounded" required>
                        <x-input-error :messages="$errors->get('current_password')" class="text-sm text-red-600 mt-1" />
                    </div>

                    {{-- Password baru --}}
                    <div>
                        <label for="password" class="block font-semibold">Password Baru</label>
                        <input type="password" name="password" id="password" class="form-input w-full rounded"
                            required>
                        <x-input-error :messages="$errors->get('password')" class="text-sm text-red-600 mt-1" />
                    </div>

                    {{-- Konfirmasi password --}}
                    <div>
                        <label for="password_confirmation" class="block font-semibold">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="form-input w-full rounded" required>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </x-layout.content-bar>
    </div>

    {{-- SweetAlert2 jika sukses --}}
    @if (session('status'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('status') }}',
                timer: 3000,
                showConfirmButton: false
            });
        </script>
    @endif
</body>
