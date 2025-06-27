@include('head')

<body>
    <div class="flex h-screen">
        <x-layout.sidebar />
        <x-layout.content-bar :title="$title" :subtitle="$subtitle">
            {{-- Slot utama di sini, bisa diganti sesuai konten --}}
            @includeWhen($title === 'Anggota', 'components.content.main')

            {{-- Tulis kode di bawah ini untuk isi kontennya --}}
            @if (!$user->division)
                <div class="alert alert-warning">
                    <strong>Perhatian:</strong> Anda tidak mengelola divisi manapun. Silakan hubungi Super Admin untuk
                    penugasan.
                </div>
            @else
                <div class="container-fluid">
                    {{-- Konten Anggota Divisi --}}
                    <div class="container-fluid">
                        {{-- Header dengan Search --}}
                        <div class="mb-4">
                            <div class="d-flex flex-column flex-md-row align-items-stretch gap-2 mb-3">
                                <form action="{{ route('admin.anggota') }}" method="GET" class="d-flex flex-grow-1 gap-2" style="height: 37px;">
                                    <input type="text" name="search" class="form-control" placeholder="Cari anggota berdasarkan nama, email, universitas, fakultas..." value="{{ $search }}">
                                    <button class="btn btn-outline-primary" type="submit">Cari</button>
                                    @if ($search)
                                        <a href="{{ route('admin.anggota') }}" class="btn btn-outline-secondary">Reset</a>
                                    @endif
                                </form>
                            </div>
                        </div>

                        {{-- Info Total Anggota --}}
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    Total: {{ $memberships->total() }} anggota divisi
                                    {{-- Menampilkan nama divisi admin yang sedang login --}}
                                    @if ($user->division)
                                        {{-- Pastikan user memiliki divisi --}}
                                        <span class="fw-bold">"{{ $user->division->title }}"</span>
                                    @endif
                                    @if ($search)
                                        (menampilkan hasil pencarian untuk "{{ $search }}")
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Anggota Cards (bagian ini sama seperti sebelumnya) --}}
                        @if ($memberships->count() > 0)
                            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 d-flex align-items-stretch">
                                @foreach ($memberships as $membership)
                                    <div class="col">
                                        <div class="card h-100 shadow-sm">
                                            <div class="card-body d-flex flex-column">
                                                {{-- Header dengan foto dan info dasar --}}
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="flex-shrink-0 me-3">
                                                        @if ($membership->member->user && $membership->member->user->profile_photo)
                                                            <img src="{{ $membership->member->user->profile_photo_url }}"
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
                                                        <h5 class="card-title mb-0">{{ $membership->member->full_name }}
                                                        </h5>
                                                        @if ($membership->id_member_organization)
                                                            <small class="text-muted">ID:
                                                                {{ $membership->id_member_organization }}</small>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- Email --}}
                                                <div class="mb-3">
                                                    <p class="text-muted mb-0 d-flex align-items-baseline">
                                                        <i class="fas fa-envelope me-1" style="flex-shrink: 0;"></i>
                                                        <span
                                                            class="text-break flex-grow-1">{{ $membership->member->user?->email ?? 'Email tidak tersedia' }}</span>
                                                    </p>
                                                    @if ($membership->member->email_institusi)
                                                        <p class="text-muted mb-0 d-flex align-items-baseline">
                                                            <i class="fas fa-building me-1" style="flex-shrink: 0;"></i>
                                                            <span
                                                                class="text-break flex-grow-1">{{ $membership->member->email_institusi }}</span>
                                                        </p>
                                                    @endif
                                                </div>

                                                {{-- Informasi Akademik --}}
                                                @if ($membership->member->universitas)
                                                    <div class="mb-3">
                                                        <h6 class="text-primary mb-2">
                                                            <i class="fas fa-graduation-cap me-1"></i> Informasi
                                                            Akademik
                                                        </h6>
                                                        <p class="mb-1"><strong>Universitas:</strong>
                                                            {{ $membership->member->universitas }}</p>
                                                        @if ($membership->member->fakultas)
                                                            <p class="mb-1"><strong>Fakultas:</strong>
                                                                {{ $membership->member->fakultas }}</p>
                                                        @endif
                                                        @if ($membership->member->prodi)
                                                            <p class="mb-1"><strong>Program Studi:</strong>
                                                                {{ $membership->member->prodi }}</p>
                                                        @endif
                                                    </div>
                                                @endif

                                                {{-- Informasi Personal --}}
                                                <div class="mb-3">
                                                    <h6 class="text-success mb-2">
                                                        <i class="fas fa-user me-1"></i> Informasi Personal
                                                    </h6>
                                                    @if ($membership->member->tempat_lahir && $membership->member->tanggal_lahir)
                                                        <p class="mb-1">
                                                            <strong>TTL:</strong>
                                                            {{ $membership->member->tempat_lahir }},
                                                            {{ optional($membership->member->tanggal_lahir)->format('d/m/Y') }}
                                                        </p>
                                                    @endif
                                                    @if ($membership->member->no_hp)
                                                        <p class="mb-1">
                                                            <i class="fas fa-phone me-1"></i>
                                                            {{ $membership->member->no_hp }}
                                                        </p>
                                                    @endif
                                                    @if ($membership->member->no_wa)
                                                        <p class="mb-1">
                                                            <i class="fab fa-whatsapp me-1"></i>
                                                            {{ $membership->member->no_wa }}
                                                        </p>
                                                    @endif
                                                </div>

                                                {{-- Alamat --}}
                                                @if ($membership->member->provinsi)
                                                    <div class="mb-3">
                                                        <h6 class="text-warning mb-2">
                                                            <i class="fas fa-map-marker-alt me-1"></i> Alamat
                                                        </h6>
                                                        <p class="text-muted mb-0 text-break">
                                                            {{ $membership->member->full_address ?? 'Alamat tidak tersedia' }}
                                                        </p>
                                                    </div>
                                                @endif

                                                {{-- Status Membership --}}
                                                <div class="mb-3">
                                                    <h6 class="text-info mb-2">
                                                        <i class="fas fa-id-card me-1"></i> Status Keanggotaan
                                                    </h6>
                                                    <span
                                                        class="badge
                                            @if ($membership->status === 'active') bg-success
                                            @elseif($membership->status === 'pending') bg-warning
                                            @elseif($membership->status === 'inactive') bg-secondary
                                            @else bg-danger @endif">
                                                        {{ $membership->status_label }}
                                                    </span>
                                                </div>
                                            </div> {{-- Akhir dari .card-body --}}

                                            {{-- Card Footer --}}
                                            <div class="card-footer bg-transparent mt-auto">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        Bergabung: {{ $membership->created_at->format('d/m/Y') }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div> {{-- Akhir dari .card --}}
                                    </div> {{-- Akhir dari .col --}}
                                @endforeach
                            </div>

                            {{-- Pagination --}}
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-center">
                                        {{ $memberships->appends(['search' => $search])->links('vendor.pagination.bootstrap-5') }}
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Empty State --}}
                            <div class="row">
                                <div class="col-12">
                                    <div class="text-center py-5">
                                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                        <h4 class="text-muted">Tidak ada anggota ditemukan</h4>
                                        @if ($search)
                                            <p class="text-muted">Tidak ada hasil untuk pencarian "{{ $search }}"
                                            </p>
                                            <a href="{{ route('admin.anggota') }}" class="btn btn-outline-primary">
                                                <i class="fas fa-arrow-left"></i> Kembali ke semua anggota
                                            </a>
                                        @else
                                            <p class="text-muted">Belum ada anggota yang terdaftar di divisi ini</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </x-layout.content-bar>
    </div>
</body>
