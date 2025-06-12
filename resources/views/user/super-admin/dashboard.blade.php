@include('head')

<body>
    <div class="flex h-screen">
        <x-layout.sidebar />
        <x-layout.content-bar :title="$title" :subtitle="$subtitle">
            {{-- Slot utama di sini, bisa diganti sesuai konten --}}
            @includeWhen($title === 'Dashboard', 'components.content.main')

            {{-- Tulis kode di bawah ini untuk isi kontennya --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                <x-card.stat title="User Member" count="{{ $totalMembers }}" />
                <x-card.stat title="User Admin" count="{{ $totalAdmins }}" />
                <x-card.stat title="Publikasi Member" count="{{ $totalPublikasiMember }}" />
                <x-card.stat title="Publikasi ADN" count="{{ $totalPublikasiADN }}" />
                <x-card.stat title="Divisi" count="{{ $totalDivisi }}" />
                <x-card.stat title="Berita" count="{{ $totalBerita }}" />
            </div>
        </x-layout.content-bar>
    </div>
</body>
