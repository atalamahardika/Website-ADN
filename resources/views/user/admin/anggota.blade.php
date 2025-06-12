@include('head')

<body>
    <div class="flex h-screen">
        <x-layout.sidebar />
        <x-layout.content-bar :title="$title" :subtitle="$subtitle">
            {{-- Slot utama di sini, bisa diganti sesuai konten --}}
            @includeWhen($title === 'Anggota', 'components.content.main')

            {{-- Tulis kode di bawah ini untuk isi kontennya --}}
        </x-layout.content-bar>
    </div>
</body>