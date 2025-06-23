@include('head')

<body>
    <div class="flex h-screen">
        <x-layout.sidebar />
        <x-layout.content-bar :title="$title" :subtitle="$subtitle">
            {{-- Slot utama di sini, bisa diganti sesuai konten --}}
            @includeWhen($title === 'Keanggotaan', 'components.content.main')

            {{-- Payment Settings CRUD Section --}}
            <div class="mb-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold text-primary">
                        <i class="fas fa-credit-card me-2"></i>Pengaturan Pembayaran
                    </h4>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#addPaymentSettingModal">
                        <i class="fas fa-plus me-2"></i>Tambah Pengaturan
                    </button>
                </div>

                {{-- Payment Settings Table --}}
                <div class="card shadow-sm">
                    <div class="card-body">
                        @if ($paymentSettings->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Bank</th>
                                            <th>No. Rekening</th>
                                            <th>Atas Nama</th>
                                            <th>Jumlah Pembayaran</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($paymentSettings as $setting)
                                            <tr class="{{ $setting->is_active ? 'table-success' : '' }}">
                                                <td class="fw-medium">{{ $setting->bank_name }}</td>
                                                <td>{{ $setting->account_number }}</td>
                                                <td>{{ $setting->account_holder }}</td>
                                                <td class="fw-bold text-primary">Rp
                                                    {{ number_format($setting->payment_amount, 0, ',', '.') }}</td>
                                                <td>
                                                    @if ($setting->is_active)
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check-circle me-1"></i>Aktif
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">
                                                            <i class="fas fa-pause-circle me-1"></i>Nonaktif
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        @if (!$setting->is_active)
                                                            <form
                                                                action="{{ route('superadmin.payment-settings.activate', $setting->id) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-outline-success"
                                                                    title="Aktifkan">
                                                                    <i class="fas fa-play"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                        <button type="button" class="btn btn-outline-primary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editPaymentSettingModal{{ $setting->id }}"
                                                            title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        @if (!$setting->is_active)
                                                            <form
                                                                action="{{ route('superadmin.payment-settings.delete', $setting->id) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-outline-danger"
                                                                    onclick="return confirm('Yakin ingin menghapus pengaturan ini?')"
                                                                    title="Hapus">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-credit-card text-muted" style="font-size: 3rem;"></i>
                                <h5 class="text-muted mt-3">Belum Ada Pengaturan Pembayaran</h5>
                                <p class="text-muted">Klik tombol "Tambah Pengaturan" untuk menambahkan pengaturan
                                    pembayaran pertama.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Divider --}}
            <hr class="my-5">

            {{-- Membership Management Section --}}
            <div>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold text-primary">
                        <i class="fas fa-users me-2"></i>Daftar Membership
                    </h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('superadmin.membership.export', request()->query()) }}"
                            class="btn btn-success btn-sm">
                            <i class="fas fa-download me-2"></i>Export Excel
                        </a>
                    </div>
                </div>

                {{-- Filter Section --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('superadmin.membership') }}" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Cari</label>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Nama, email, atau ID member..." value="{{ $filters['search'] }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="pending" {{ $filters['status'] == 'pending' ? 'selected' : '' }}>
                                        Menunggu Konfirmasi</option>
                                    <option value="active" {{ $filters['status'] == 'active' ? 'selected' : '' }}>
                                        Aktif</option>
                                    <option value="inactive" {{ $filters['status'] == 'inactive' ? 'selected' : '' }}>
                                        Tidak Aktif</option>
                                    <option value="rejected" {{ $filters['status'] == 'rejected' ? 'selected' : '' }}>
                                        Ditolak</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Divisi</label>
                                <select name="division" class="form-select">
                                    <option value="">Semua Divisi</option>
                                    @foreach ($divisions as $division)
                                        <option value="{{ $division->id }}"
                                            {{ $filters['division'] == $division->id ? 'selected' : '' }}>
                                            {{ $division->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="btn-group w-100">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <a href="{{ route('superadmin.membership') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-undo"></i>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Membership List --}}
                @if ($memberships->count() > 0)
                    <div class="row">
                        @foreach ($memberships as $membership)
                            <div class="col-lg-6 col-xl-4 mb-4">
                                <a href="{{ route('superadmin.membership.detail', $membership->id) }}"
                                    class="text-decoration-none">
                                    <div class="card h-100 shadow-sm hover-card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                {{-- Cek apakah ada foto profil yang valid --}}
                                                @if ($membership->member->user->profile_photo)
                                                    {{-- Jika ada profile_photo, gunakan URL dari accessor --}}
                                                    <img src="{{ $membership->member->user->profile_photo_url }}"
                                                        alt="Profile Photo" class="rounded-circle me-3"
                                                        style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    {{-- Jika tidak ada profile_photo, tampilkan avatar inisial --}}
                                                    <div class="avatar-circle bg-primary text-white me-3"
                                                        style="width: 50px; height: 50px; font-size: 1.25rem; display: flex; align-items: center; justify-content: center;">
                                                        {{ strtoupper(substr($membership->member->user->name, 0, 2)) }}
                                                    </div>
                                                @endif
                                                <div class="flex-grow-1">
                                                    <h6 class="card-title fw-bold mb-1 text-dark">
                                                        {{ $membership->member->user->name }}
                                                    </h6>
                                                    <p class="text-muted small mb-0">
                                                        <i class="fas fa-envelope me-1"></i>
                                                        {{ $membership->member->user->email }}
                                                    </p>
                                                    <span
                                                        class="badge bg-{{ $membership->status === 'active' ? 'success' : ($membership->status === 'pending' ? 'warning' : ($membership->status === 'rejected' ? 'danger' : 'secondary')) }}">
                                                        {{ $membership->status_label }}
                                                    </span>
                                                </div>

                                            </div>

                                            <div class="row g-2 text-sm">
                                                <div class="col-6">
                                                    <small class="text-muted d-block">ID Member</small>
                                                    <span class="fw-medium">
                                                        {{ $membership->id_member_organization ?? 'Belum diset' }}
                                                    </span>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted d-block">Divisi</small>
                                                    <span class="fw-medium">
                                                        {{ $membership->division->title ?? 'Belum dipilih' }}
                                                    </span>
                                                </div>
                                            </div>

                                            <hr class="my-3">

                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Masa Aktif</small>
                                                    <div class="fw-medium">
                                                        {{ optional($membership->active_until)->format('d M Y') ?? '-' }}</div>
                                                </div>
                                                <div class="text-end">
                                                    <small class="text-muted">Total Pembayaran</small>
                                                    <div class="fw-bold text-primary">
                                                        {{ $membership->payments->count() }}</div>
                                                </div>
                                            </div>

                                            @if ($membership->payments->where('status', 'pending')->count() > 0)
                                                <div class="mt-3">
                                                    <div class="alert alert-warning py-2 mb-0">
                                                        <i class="fas fa-clock me-2"></i>
                                                        <small>{{ $membership->payments->where('status', 'pending')->count() }}
                                                            pembayaran menunggu verifikasi</small>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-center mt-4">
                        {{ $memberships->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="card shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-users text-muted" style="font-size: 3rem;"></i>
                            <h5 class="text-muted mt-3">Tidak Ada Data Membership</h5>
                            <p class="text-muted">Belum ada data membership yang sesuai dengan filter yang dipilih.</p>
                        </div>
                    </div>
                @endif
            </div>

        </x-layout.content-bar>
    </div>

    {{-- Add Payment Setting Modal --}}
    <div class="modal fade" id="addPaymentSettingModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('superadmin.payment-settings.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Pengaturan Pembayaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Bank <span class="text-danger">*</span></label>
                            <input type="text" name="bank_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nomor Rekening <span class="text-danger">*</span></label>
                            <input type="text" name="account_number" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Atas Nama <span class="text-danger">*</span></label>
                            <input type="text" name="account_holder" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jumlah Pembayaran <span class="text-danger">*</span></label>
                            <input type="number" name="payment_amount" class="form-control" min="0"
                                step="1000" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Payment Setting Modals --}}
    @foreach ($paymentSettings as $setting)
        <div class="modal fade" id="editPaymentSettingModal{{ $setting->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('superadmin.payment-settings.update', $setting->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Pengaturan Pembayaran</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nama Bank <span class="text-danger">*</span></label>
                                <input type="text" name="bank_name" class="form-control"
                                    value="{{ $setting->bank_name }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nomor Rekening <span class="text-danger">*</span></label>
                                <input type="text" name="account_number" class="form-control"
                                    value="{{ $setting->account_number }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Atas Nama <span class="text-danger">*</span></label>
                                <input type="text" name="account_holder" class="form-control"
                                    value="{{ $setting->account_holder }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jumlah Pembayaran <span class="text-danger">*</span></label>
                                <input type="number" name="payment_amount" class="form-control"
                                    value="{{ $setting->payment_amount }}" min="0" step="1000" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Custom CSS --}}
    <style>
        .avatar-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .hover-card {
            transition: all 0.3s ease;
            border: 1px solid #e3e6f0;
        }

        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
            border-color: #4e73df;
        }

        .text-sm {
            font-size: 0.875rem;
        }
    </style>

    {{-- SweetAlert2 --}}
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: '{{ session('success') }}',
                timer: 2500,
                showConfirmButton: false
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                timer: 3000,
                showConfirmButton: false
            });
        </script>
    @endif
</body>
