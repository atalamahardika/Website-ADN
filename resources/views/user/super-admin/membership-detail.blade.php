@include('head')

<body>
    <div class="flex h-screen">
        <x-layout.sidebar />
        <x-layout.content-bar :title="$title" :subtitle="$subtitle">
            {{-- Slot utama di sini, bisa diganti sesuai konten --}}
            @includeWhen($title === 'Membership Detail', 'components.content.main')

            {{-- Back Button --}}
            <div class="mb-4">
                <a href="{{ route('superadmin.membership') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Membership
                </a>
            </div>

            <div class="row">
                {{-- Member Information Card --}}
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary">
                            <h5 class="card-title text-white mb-0">
                                <i class="fas fa-user me-2"></i>Informasi Member
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-4">
                                {{-- Cek apakah ada profile_photo dan file-nya ada --}}
                                @if ($membership->member->user->profile_photo)
                                    {{-- Jika ada profile_photo dan file-nya ada --}}
                                    <img src="{{ $membership->member->user->profile_photo_url }}" alt="Profile Photo"
                                        class="rounded-circle mx-auto mb-3"
                                        style="width: 80px; height: 80px; object-fit: cover;">
                                @else
                                    {{-- Jika tidak ada profile_photo atau file-nya tidak ada, tampilkan avatar inisial --}}
                                    <div class="avatar-circle bg-primary text-white mx-auto mb-3"
                                        style="width: 80px; height: 80px; font-size: 2rem; display: flex; align-items: center; justify-content: center;">
                                        {{ strtoupper(substr($membership->member->user->name, 0, 2)) }}
                                    </div>
                                @endif
                                <h5 class="fw-bold">{{ $membership->member->user->name }}</h5>
                                <p class="text-muted mb-0">{{ $membership->member->user->email }}</p>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Status Membership</label>
                                    <div>
                                        <span
                                            class="badge bg-{{ $membership->status === 'active' ? 'success' : ($membership->status === 'pending' ? 'warning' : ($membership->status === 'rejected' ? 'danger' : 'secondary')) }} fs-6">
                                            {{ $membership->status_label }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">ID Member Organization</label>
                                    <p class="mb-0">{{ $membership->id_member_organization ?? 'Belum diset' }}</p>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Alamat Lengkap</label>
                                    <p class="mb-0">
                                        {{ $membership->member?->full_address ?? 'Belum diisi' }}</p>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Divisi</label>
                                    <p class="mb-0">{{ $membership->division->title ?? 'Belum dipilih' }}</p>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Tanggal Bergabung</label>
                                    <p class="mb-0">{{ $membership->created_at->format('d F Y, H:i') }}</p>
                                </div>
                                @if ($membership->status === 'active')
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Masa Aktif</label>
                                        <p class="mb-0">
                                            {{ $membership->active_until ? $membership->active_until->format('d F Y') : 'Belum diatur' }}</p>
                                    </div>
                                @endif
                                <div class="col-12">
                                    <label class="form-label fw-bold">Total Pembayaran</label>
                                    <p class="mb-0">{{ $membership->payments->count() }} pembayaran</p>
                                </div>
                            </div>

                            @if ($membership->status === 'active' || $membership->status === 'inactive')
                                <hr class="my-4">
                                {{-- Edit Membership Button --}}
                                <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal"
                                    data-bs-target="#editMembershipModal">
                                    <i class="fas fa-edit me-2"></i>Edit Membership
                                </button>
                            @endif

                        </div>
                    </div>
                </div>

                {{-- Payments History --}}
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-credit-card me-2"></i>Riwayat Pembayaran
                            </h5>
                            <span class="badge bg-info">{{ $membership->payments->count() }} Total</span>
                        </div>
                        <div class="card-body">
                            @if ($membership->payments->count() > 0)
                                <div class="timeline">
                                    @foreach ($membership->payments->sortByDesc('created_at') as $payment)
                                        <div
                                            class="timeline-item {{ $payment->status === 'pending' ? 'timeline-pending' : ($payment->status === 'approved' ? 'timeline-success' : 'timeline-danger') }}">
                                            <div class="timeline-marker"></div>
                                            <div class="timeline-content">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        @php
                                                            $totalPayments = $membership->payments->count();
                                                        @endphp
                                                        <h6 class="fw-bold mb-1">
                                                            Pembayaran #{{ $totalPayments - $loop->index }}
                                                            <span
                                                                class="badge bg-{{ $payment->status === 'approved' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger') }} ms-2">
                                                                {{ $payment->status_label }}
                                                            </span>
                                                        </h6>
                                                        <p class="text-muted small mb-0">
                                                            {{ $payment->created_at->format('d F Y, H:i') }}</p>
                                                    </div>
                                                    @if ($payment->status === 'pending')
                                                        <div class="btn-group btn-group-sm">
                                                            <button type="button" class="btn btn-success btn-sm"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#approvePaymentModal{{ $payment->id }}">
                                                                <i class="fas fa-check"></i> Setujui
                                                            </button>
                                                            <button type="button" class="btn btn-danger btn-sm"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#rejectPaymentModal{{ $payment->id }}">
                                                                <i class="fas fa-times"></i> Tolak
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>

                                                @if ($payment->payment_proof_link)
                                                    <div class="mb-2">
                                                        <small class="text-muted">Bukti Pembayaran:</small>
                                                        <div class="mt-1">
                                                            <a href="{{ url($payment->payment_proof_link) }}"
                                                                target="_blank" class="btn btn-outline-primary btn-sm">
                                                                <i class="fas fa-image me-1"></i>Lihat Bukti
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if ($payment->amount)
                                                    <div class="mb-2">
                                                        <small class="text-muted">Jumlah:</small>
                                                        <span class="fw-bold text-primary">Rp
                                                            {{ number_format($payment->amount, 0, ',', '.') }}</span>
                                                    </div>
                                                @endif

                                                @if ($payment->status !== 'pending')
                                                    <div class="row g-2 mt-2">
                                                        @if ($payment->processed_at)
                                                            <div class="col-md-6">
                                                                <small class="text-muted">Diproses:</small>
                                                                <div class="small">
                                                                    {{ $payment->processed_at->format('d F Y, H:i') }}
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if ($payment->approvedBy)
                                                            <div class="col-md-6">
                                                                <small class="text-muted">Oleh:</small>
                                                                <div class="small">{{ $payment->approvedBy->name }}
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if ($payment->invoice_link)
                                                            <div class="col-12">
                                                                <small class="text-muted">Invoice:</small>
                                                                <div class="mt-1">
                                                                    <a href="{{ $payment->invoice_link }}"
                                                                        target="_blank"
                                                                        class="btn btn-outline-success btn-sm">
                                                                        <i
                                                                            class="fas fa-external-link-alt me-1"></i>Lihat
                                                                        Invoice
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if ($payment->admin_notes)
                                                            <div class="col-12">
                                                                <small class="text-muted">Catatan Super Admin:</small>
                                                                <div class="small bg-light p-2 rounded mt-1">
                                                                    {{ $payment->admin_notes }}</div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-credit-card text-muted" style="font-size: 3rem;"></i>
                                    <h5 class="text-muted mt-3">Belum Ada Pembayaran</h5>
                                    <p class="text-muted">Member belum melakukan pembayaran apapun.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Edit Membership Modal --}}
            <div class="modal fade" id="editMembershipModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('superadmin.membership.update', $membership->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Membership</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">ID Member Organization <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="id_member_organization" class="form-control"
                                        value="{{ $membership->id_member_organization }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Divisi</label>
                                    <select name="division_id" class="form-select">
                                        <option value="">Pilih Divisi</option>
                                        @foreach ($divisions as $division)
                                            <option value="{{ $division->id }}"
                                                {{ $membership->division_id == $division->id ? 'selected' : '' }}>
                                                {{ $division->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Approve Payment Modals --}}
            @foreach ($membership->payments->where('status', 'pending') as $payment)
                <div class="modal fade" id="approvePaymentModal{{ $payment->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('superadmin.payment.approve', $payment->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="membership_id" value="{{ $membership->id }}">
                                <div class="modal-header">
                                    <h5 class="modal-title">Setujui Pembayaran</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Anda akan menyetujui pembayaran #{{ $payment->id }} dari
                                        {{ $membership->member->user->name }}
                                    </div>

                                    @if (!$membership->id_member_organization)
                                        <div class="mb-3">
                                            <label class="form-label">ID Member Organization<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="id_member_organization" class="form-control"
                                                placeholder="Masukkan ID member organization">
                                        </div>
                                    @endif

                                    @if (!$membership->division_id)
                                        <div class="mb-3">
                                            <label class="form-label">Divisi</label>
                                            <select name="division_id" class="form-select">
                                                <option value="">Pilih Divisi</option>
                                                @foreach ($divisions as $division)
                                                    <option value="{{ $division->id }}">{{ $division->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">Kosongkan jika tidak ingin mengubah</small>
                                        </div>
                                    @endif

                                    <div class="mb-3">
                                        <label class="form-label">Link Invoice<span
                                                class="text-danger">*</span></label>
                                        <input type="url" name="invoice_link" class="form-control"
                                            placeholder="https://example.com/invoice.pdf">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-success">Setujui Pembayaran</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Reject Payment Modals --}}
            @foreach ($membership->payments->where('status', 'pending') as $payment)
                <div class="modal fade" id="rejectPaymentModal{{ $payment->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('superadmin.payment.reject', $payment->id) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title text-danger">Tolak Pembayaran</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Anda akan menolak pembayaran #{{ $payment->id }} dari
                                        {{ $membership->member->user->name }}
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Alasan Penolakan <span
                                                class="text-danger">*</span></label>
                                        <textarea name="admin_notes" class="form-control" rows="4" required
                                            placeholder="Jelaskan alasan penolakan pembayaran ini..."></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-danger">Tolak Pembayaran</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach

        </x-layout.content-bar>
    </div>

    {{-- Custom CSS --}}
    <style>
        .avatar-circle {
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e3e6f0;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 30px;
        }

        .timeline-marker {
            position: absolute;
            left: -23px;
            top: 5px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 3px solid #e3e6f0;
            background: white;
        }

        .timeline-success .timeline-marker {
            border-color: #28a745;
            background: #28a745;
        }

        .timeline-pending .timeline-marker {
            border-color: #ffc107;
            background: #ffc107;
        }

        .timeline-danger .timeline-marker {
            border-color: #dc3545;
            background: #dc3545;
        }

        .timeline-content {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #e3e6f0;
        }

        .timeline-success .timeline-content {
            border-left-color: #28a745;
        }

        .timeline-pending .timeline-content {
            border-left-color: #ffc107;
        }

        .timeline-danger .timeline-content {
            border-left-color: #dc3545;
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

    @if ($errors->any() && session('modal'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const errorMessages = {!! json_encode($errors->all()) !!};
                const modalId = '{{ session('modal') }}';

                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal!',
                    html: errorMessages.join('<br>'),
                    confirmButtonText: 'OK'
                }).then(() => {
                    const modalElement = document.getElementById(modalId);
                    if (modalElement) {
                        const modalInstance = new bootstrap.Modal(modalElement);
                        modalInstance.show();
                    }
                });
            });
        </script>
    @endif

</body>
