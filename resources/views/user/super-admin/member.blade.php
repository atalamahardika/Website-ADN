@include('head')

<body>
    <div class="flex h-screen">
        <x-layout.sidebar />
        <x-layout.content-bar :title="$title" :subtitle="$subtitle">
            {{-- Slot utama di sini, bisa diganti sesuai konten --}}
            @includeWhen($title === 'Member', 'components.content.main')

            {{-- Tulis kode di bawah ini untuk isi kontennya --}}
            {{-- Konten Member --}}
            <div class="container-fluid">
                {{-- Header dengan Search dan Action Button --}}
                <div class="row mb-4">
                    <div class="col">
                        <form method="GET" action="{{ route('superadmin.member') }}" class="d-flex">
                            <div class="input-group">
                                <input type="text" class="form-control"
                                    placeholder="Cari member berdasarkan nama, email, NIK, universitas..."
                                    name="search" value="{{ $search }}" style="min-width: 300px;">
                                <button class="btn btn-outline-primary" type="submit">
                                    <i class="fas fa-search"></i> Cari
                                </button>
                                @if ($search)
                                    <a href="{{ route('superadmin.member') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Reset
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Info Total Member --}}
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Total: {{ $members->total() }} member
                            @if ($search)
                                (menampilkan hasil pencarian untuk "{{ $search }}")
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Member Cards --}}
                @if ($members->count() > 0)
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 d-flex align-items-stretch">
                        @foreach ($members as $member)
                            <div class="col">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body d-flex flex-column">
                                        {{-- Header dengan foto dan info dasar --}}
                                        <div class="d-flex align-items-start mb-3">
                                            <div class="flex-shrink-0 me-3">
                                                @if ($member->user && $member->user->profile_photo)
                                                    <img src="{{ $member->user->profile_photo_url }}"
                                                        alt="Profile" class="rounded-circle" width="60"
                                                        height="60" style="object-fit: cover;">
                                                @else
                                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center"
                                                        style="width: 60px; height: 60px;">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="card-title mb-0">{{ $member->full_name }}</h5>
                                            </div>
                                        </div>

                                        {{-- Email --}}
                                        <div class="mb-3">
                                            <p class="text-muted mb-0 d-flex align-items-baseline">
                                                <i class="fas fa-envelope me-1" style="flex-shrink: 0;"></i><span
                                                    class="text-break flex-grow-1">{{ $member->user?->email ?? 'Email tidak tersedia' }}</span>

                                            </p>
                                            @if ($member->email_institusi)
                                                <p class="text-muted mb-0 d-flex align-items-baseline">
                                                    <i class="fas fa-building me-1" style="flex-shrink: 0;"></i><span
                                                        class="text-break flex-grow-1">{{ $member->email_institusi }}</span>

                                                </p>
                                            @endif
                                        </div>

                                        {{-- Informasi Akademik --}}
                                        @if ($member->universitas)
                                            <div class="mb-3">
                                                <h6 class="text-primary mb-2">
                                                    <i class="fas fa-graduation-cap me-1"></i> Informasi Akademik
                                                </h6>
                                                <p class="mb-1"><strong>Universitas:</strong>
                                                    {{ $member->universitas }}</p>
                                                @if ($member->fakultas)
                                                    <p class="mb-1"><strong>Fakultas:</strong>
                                                        {{ $member->fakultas }}</p>
                                                @endif
                                                @if ($member->prodi)
                                                    <p class="mb-1"><strong>Program Studi:</strong>
                                                        {{ $member->prodi }}</p>
                                                @endif
                                            </div>
                                        @endif

                                        {{-- Informasi Personal --}}
                                        <div class="mb-3">
                                            <h6 class="text-success mb-2">
                                                <i class="fas fa-user me-1"></i> Informasi Personal
                                            </h6>
                                            @if ($member->tempat_lahir && $member->tanggal_lahir)
                                                <p class="mb-1">
                                                    <strong>TTL:</strong> {{ $member->tempat_lahir }},
                                                    {{ optional($member->tanggal_lahir)->format('d/m/Y') }}
                                                </p>
                                            @endif
                                            @if ($member->no_hp)
                                                <p class="mb-1">
                                                    <i class="fas fa-phone me-1"></i> {{ $member->no_hp }}
                                                </p>
                                            @endif
                                            @if ($member->no_wa)
                                                <p class="mb-1">
                                                    <i class="fab fa-whatsapp me-1"></i> {{ $member->no_wa }}
                                                </p>
                                            @endif
                                        </div>

                                        {{-- Alamat --}}
                                        @if ($member->provinsi)
                                            <div class="mb-3">
                                                <h6 class="text-warning mb-2">
                                                    <i class="fas fa-map-marker-alt me-1"></i> Alamat
                                                </h6>
                                                <p class="text-muted mb-0 text-break">
                                                    {{ $member->full_address ?? 'Alamat tidak tersedia' }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Card Footer --}}
                                    <div class="card-footer bg-transparent">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                Bergabung: {{ $member->created_at->format('d/m/Y') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-center">
                                {{ $members->appends(['search' => $search])->links() }}
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Empty State --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">Tidak ada member ditemukan</h4>
                                @if ($search)
                                    <p class="text-muted">Tidak ada hasil untuk pencarian "{{ $search }}"</p>
                                    <a href="{{ route('superadmin.member') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-arrow-left"></i> Kembali ke semua member
                                    </a>
                                @else
                                    <p class="text-muted">Belum ada member yang terdaftar</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </x-layout.content-bar>
    </div>
</body>