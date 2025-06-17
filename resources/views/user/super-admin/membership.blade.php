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
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

</body>
