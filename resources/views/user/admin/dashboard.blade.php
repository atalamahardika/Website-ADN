@include('head')

<body>
    <div class="flex h-screen">
        <x-layout.sidebar />
        <x-layout.content-bar :title="$title" :subtitle="$subtitle">
            {{-- Slot utama di sini, bisa diganti sesuai konten --}}
            @includeWhen($title === 'Dashboard', 'components.content.main')

            {{-- Tulis kode di bawah ini untuk isi kontennya --}}
            @if (!$user->division)
                <div class="alert alert-warning">
                    <strong>Perhatian:</strong> Anda tidak mengelola divisi manapun. Silakan hubungi Super Admin untuk
                    penugasan.
                </div>
            @else
                <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3 mb-4">
                    <div class="col">
                        <x-card.stat title="Total Sub Divisi" :count="$totalSubDivisi" />
                    </div>

                    <div class="col">
                        <div class="p-4 rounded-xl text-white shadow-md h-100 d-flex flex-column justify-center align-items-center"
                            style="background: linear-gradient(to right, #79E133, #55C733);">
                            <h3 class="text-lg fw-semibold mb-2 text-center" style="color: white !important;">
                                {{ $user->division->title }}
                            </h3>
                            <p class="h5 fw-bold mb-0 text-center" style="color: white !important;">Wilayah
                                {{ $user->division->region }}</p>
                        </div>
                    </div>

                    <div class="col">
                        <x-card.stat title="Total Anggota" :count="$totalAnggota" />
                    </div>
                </div>
                {{-- Konten Beranda Divisi --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold">Deskripsi Divisi</h5>
                        <div class="prose">{!! $user->division->description !!}</div>
                    </div>
                </div>

                {{-- List Sub Divisi --}}
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3 fw-bold">Daftar Sub Divisi</h5>
                        @forelse ($subDivisions as $sub)
                            <div class="mb-3 p-3 border rounded">
                                <h6 class="fw-semibold mb-1">{{ $sub->title }}</h6>
                                <div class="prose mb-1">
                                    {!! $sub->description !!}
                                </div>
                                {{-- <p class="mb-1">{{ $sub->description }}</p> --}}
                                <span class="badge {{ $sub->is_approved ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $sub->is_approved ? 'Disetujui' : 'Belum Disetujui' }}
                                </span>
                            </div>
                        @empty
                            <p class="text-muted">Belum ada sub divisi yang ditambahkan.</p>
                        @endforelse
                    </div>
                </div>
            @endif

        </x-layout.content-bar>
    </div>
</body>
