@include('head')

<body>
    <div class="flex h-screen">
        <x-layout.sidebar />
        <x-layout.content-bar :title="$title" :subtitle="$subtitle">
            {{-- Slot utama di sini, bisa diganti sesuai konten --}}
            @includeWhen($title === 'Keanggotaan', 'components.content.main')

            {{-- Tulis kode di bawah ini untuk isi kontennya --}}
            
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