@include('head')

<body>
    <div class="flex h-screen">
        <x-layout.sidebar />
        <x-layout.content-bar :title="$title" :subtitle="$subtitle">
            {{-- Slot utama di sini, bisa diganti sesuai konten --}}
            @includeWhen($title === 'Keanggotaan', 'components.content.main')

            {{-- Tulis kode di bawah ini untuk isi kontennya --}}
            {{-- Status Keanggotaan --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h5 class="card-title mb-0 text-white">
                                <i class="fas fa-id-card me-2"></i>Status Keanggotaan
                            </h5>
                        </div>
                        <div class="card-body">
                            @if ($membership)
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-3">
                                            <strong class="me-3">Status:</strong>
                                            @switch($membership->status)
                                                @case('active')
                                                    <span class="badge bg-success fs-6">
                                                        <i class="fas fa-check-circle me-1"></i>Aktif
                                                    </span>
                                                @break

                                                @case('pending')
                                                    <span class="badge bg-warning fs-6">
                                                        <i class="fas fa-clock me-1"></i>Menunggu Konfirmasi
                                                    </span>
                                                @break

                                                @case('inactive')
                                                    <span class="badge bg-danger fs-6">
                                                        <i class="fas fa-times-circle me-1"></i>Tidak Aktif
                                                    </span>
                                                @break

                                                @case('rejected')
                                                    <span class="badge bg-danger fs-6">
                                                        <i class="fas fa-triangle-exclamation me-1"></i>Ditolak
                                                    </span>
                                                @break
                                            @endswitch
                                        </div>

                                        @if ($membership->status === 'active')
                                            <div class="mb-3">
                                                <strong>Berlaku hingga:</strong>
                                                <span class="text-success">
                                                    {{ $membership->active_until ? $membership->active_until->format('d F Y') : 'Tidak terbatas' }}
                                                </span>
                                            </div>
                                            <div class="mb-3">
                                                <strong>Nomor Anggota:</strong>
                                                <span
                                                    class="text-primary">{{ $membership->id_member_organization ?? 'Belum tersedia' }}</span>
                                            </div>
                                            <div class="mb-3">
                                                <strong>Divisi:</strong>
                                                <span>{{ optional($membership->division)->title ?? 'Belum diberikan admin' }}</span>
                                            </div>
                                            <div class="mb-3">
                                                <strong>Tanggal Bergabung:</strong>
                                                <span>{{ $membership->created_at->format('d F Y') }}</span>
                                            </div>
                                        @elseif ($membership->status === 'inactive')
                                            <div class="mb-3">
                                                <strong>Nomor Anggota:</strong>
                                                <span
                                                    class="text-primary">{{ $membership->id_member_organization ?? 'Belum tersedia' }}</span>
                                            </div>
                                            <div class="mb-3">
                                                <strong>Tanggal Bergabung:</strong>
                                                <span>{{ $membership->created_at->format('d F Y') }}</span>
                                            </div>
                                        @elseif ($membership->status === 'rejected')
                                            <div class="mb-3">
                                                <strong>Alasan Penolakan:</strong>
                                                <span
                                                    class="text-danger">{{ $rejectedPaymentNotes ?? 'Tidak ada' }}</span>
                                            </div>
                                        @elseif ($membership->status === 'pending')
                                            <div class="mb-3">
                                                <strong>Tanggal Bergabung:</strong>
                                                <span>{{ $membership->created_at->format('d F Y') }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-md-6">
                                        @if ($membership->status === 'active')
                                            <div class="alert alert-success">
                                                <i class="fas fa-check-circle me-2"></i>
                                                <strong>Selamat!</strong> Keanggotaan Anda telah aktif.
                                            </div>
                                        @elseif($membership->status === 'pending')
                                            <div class="alert alert-warning">
                                                <i class="fas fa-clock me-2"></i>
                                                <strong>Menunggu Konfirmasi</strong><br>
                                                Pembayaran Anda sedang diverifikasi oleh admin.
                                            </div>
                                        @elseif($membership->status === 'inactive')
                                            <div class="alert alert-danger">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                <strong>Keanggotaan Tidak Aktif</strong><br>
                                                Silakan lakukan perpanjangan untuk mengaktifkan kembali.
                                            </div>
                                        @elseif ($membership->status === 'rejected')
                                            <div class="alert alert-danger">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                <strong>Keanggotaan Ditolak</strong><br>
                                                Silakan melakukan pendaftaran ulang dengan bukti pembayaran yang benar.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <div class="mb-3">
                                        <i class="fas fa-user-plus fa-3x text-muted"></i>
                                    </div>
                                    <h5 class="text-muted">Anda belum terdaftar sebagai anggota</h5>
                                    <p class="text-muted">Silakan lakukan pendaftaran untuk menjadi anggota</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kartu Anggota --}}
            @if ($membership && $membership->status === 'active')
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-primary">
                                <h5 class="card-title mb-0 text-white">
                                    <i class="fas fa-address-card me-2"></i>Kartu Anggota
                                </h5>
                            </div>
                            <div class="card-body d-flex justify-content-center">

                                <div class="card-membership"
                                    style="width: 500px; height: 333px; background-color: #15400E; border-radius: 25px;">
                                    <div class="container" style="padding: 20px">
                                        {{-- Logo Organisasi --}}
                                        <div class="card-logo ms-2"
                                            style="width: 100px; height: 100px; border-radius: 50%; background-color: white;">
                                            <img src="{{ asset('images/adn-hd-removebg-preview.png') }}"
                                                alt="Logo Organisasi" style="width: 100px; height: 100px;">
                                        </div>

                                        {{-- Foto & Profil --}}
                                        <div class="card-profile d-flex align-items-end gap-3 mt-3">
                                            <div class="card-profile-photo">
                                                <img src="{{ auth()->user()->profile_photo_url }}" alt="Foto Profil"
                                                    style="width: 125px; height: 125px; border-radius: 10px;">
                                            </div>
                                            <div class="card-profile-user">
                                                <h4 class="text-white">{{ auth()->user()->name }}</h4>
                                                <p class="text-white mb-2">{{ $membership->id_member_organization }}
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Nama Organisasi --}}
                                        <div class="card-organization mt-3">
                                            <h4 class="text-white">Aliansi Dosen Nahada</h4>
                                        </div>
                                    </div>
                                </div>

                                <style>
                                    .card-membership {
                                        --s: 100px;
                                        /* control the size */
                                        --c: #170409;
                                        /* first color */

                                        --_g: #0000 52%, var(--c) 54% 57%, #0000 59%;
                                        background:
                                            radial-gradient(farthest-side at -33.33% 50%, var(--_g)) 0 calc(var(--s)/2),
                                            radial-gradient(farthest-side at 50% 133.33%, var(--_g)) calc(var(--s)/2) 0,
                                            radial-gradient(farthest-side at 133.33% 50%, var(--_g)),
                                            radial-gradient(farthest-side at 50% -33.33%, var(--_g)),
                                            #67917A;
                                        /* second color */
                                        background-size: calc(var(--s)/4.667) var(--s), var(--s) calc(var(--s)/4.667);
                                    }
                                </style>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Form Pendaftaran atau Perpanjangan --}}
            @if (
                !$membership ||
                    $membership->status === 'inactive' ||
                    $membership->status === 'expired' ||
                    $membership->status === 'rejected')
                @if (!$pendingPayment)
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="card-title text-white mb-0">
                                        {{-- Ubah ikon dan teks judul sesuai skenario --}}
                                        <i
                                            class="fas fa-{{ !$membership || ($membership->status === 'rejected' && !$isLastRejectedPaymentRenewal) ? 'user-plus' : 'refresh' }} me-2"></i>
                                        @if (!$membership)
                                            Pendaftaran Keanggotaan
                                        @elseif ($membership->status === 'rejected' && $isLastRejectedPaymentRenewal)
                                            Perpanjangan Ulang Keanggotaan
                                        @elseif ($membership->status === 'rejected')
                                            {{-- Ini berarti rejected new_registration --}}
                                            Pendaftaran Ulang Keanggotaan
                                        @else
                                            {{-- Ini berarti inactive atau expired --}}
                                            Perpanjangan Keanggotaan
                                        @endif
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if (!$member)
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            <strong>Profil Belum Lengkap</strong><br>
                                            Silakan lengkapi profil Anda terlebih dahulu sebelum mendaftar menjadi
                                            anggota.
                                            <a href="{{ route('profil') }}" class="btn btn-warning btn-sm ms-2">
                                                <i class="fas fa-edit me-1"></i>Lengkapi Profil
                                            </a>
                                        </div>
                                    @else
                                        {{-- Alert untuk rejected status pendaftaran awal --}}
                                        @if ($membership && $membership->status === 'rejected' && !$isLastRejectedPaymentRenewal)
                                            <div class="alert alert-danger mb-3">
                                                <h5 class="alert-heading">
                                                    <i class="fas fa-times-circle me-2"></i>Pendaftaran Sebelumnya
                                                    Ditolak
                                                </h5>
                                                <p class="mb-2">
                                                    Pendaftaran keanggotaan Anda sebelumnya telah ditolak oleh
                                                    administrator.
                                                </p>
                                                @if ($rejectedPaymentNotes)
                                                    <div class="alert alert-warning alert-sm mb-2">
                                                        <strong><i class="fas fa-sticky-note me-1"></i>Catatan
                                                            Admin:</strong><br>
                                                        {{ $rejectedPaymentNotes }}
                                                    </div>
                                                @endif
                                                <hr>
                                                <p class="mb-0">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Silakan perbaiki data yang diperlukan dan isi ulang formulir di
                                                    bawah ini untuk mengajukan pendaftaran kembali.
                                                </p>
                                            </div>
                                        @endif

                                        {{-- Alert untuk rejected status perpanjangan --}}
                                        @if ($membership && $membership->status === 'rejected' && $isLastRejectedPaymentRenewal)
                                            <div class="alert alert-danger mb-3">
                                                <h5 class="alert-heading">
                                                    <i class="fas fa-times-circle me-2"></i>Perpanjangan Sebelumnya
                                                    Ditolak
                                                </h5>
                                                <p class="mb-2">
                                                    Pengajuan perpanjangan keanggotaan Anda sebelumnya telah ditolak
                                                    oleh
                                                    administrator.
                                                </p>
                                                @if ($rejectedPaymentNotes)
                                                    <div class="alert alert-warning alert-sm mb-2">
                                                        <strong><i class="fas fa-sticky-note me-1"></i>Catatan
                                                            Admin:</strong><br>
                                                        {{ $rejectedPaymentNotes }}
                                                    </div>
                                                @endif
                                                <hr>
                                                <p class="mb-0">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Silakan upload bukti pembayaran yang benar untuk mengajukan
                                                    perpanjangan kembali.
                                                </p>
                                            </div>
                                        @endif

                                        <form
                                            action="{{ !$membership
                                                ? route('keanggotaan.register')
                                                : ($membership->status === 'rejected' && $isLastRejectedPaymentRenewal
                                                    ? route('member.reRenewMembership')
                                                    : ($membership->status === 'rejected'
                                                        ? route('member.reRegisterMembership')
                                                        : route('keanggotaan.renew'))) }}"
                                            method="POST">
                                            @csrf

                                            @if (!$membership || ($membership->status === 'rejected' && !$isLastRejectedPaymentRenewal))
                                                {{-- Form Pendaftaran Lengkap --}}
                                                {{-- Pastikan semua field terisi dari $member dan $user untuk kasus rejected --}}
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6 class="border-bottom pb-2 mb-3">
                                                            <i class="fas fa-user me-2"></i>Data Pribadi
                                                        </h6>

                                                        <div class="mb-3">
                                                            <label for="name" class="form-label">Nama Lengkap
                                                                <span class="text-danger">*</span></label>
                                                            <input type="text"
                                                                class="form-control @error('name') is-invalid @enderror"
                                                                id="name" name="name"
                                                                value="{{ old('name', $user->name) }}" required>
                                                            @error('name')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="email" class="form-label">Email</label>
                                                            <input type="email"
                                                                class="form-control @error('email') is-invalid @enderror"
                                                                id="email" name="email"
                                                                value="{{ old('email', $user->email) }}" readonly>
                                                            @error('email')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="gelar_depan" class="block font-semibold">Gelar
                                                                Depan</label>
                                                            <input type="text" name="gelar_depan" id="gelar_depan"
                                                                class="form-control @error('gelar_depan') is-invalid @enderror"
                                                                value="{{ old('gelar_depan', $member->gelar_depan) }}">
                                                            @error('gelar_depan')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        @for ($i = 1; $i <= 3; $i++)
                                                            <div class="mb-3">
                                                                <label for="gelar_belakang_{{ $i }}"
                                                                    class="block font-semibold">Gelar Belakang
                                                                    {{ $i }}</label>
                                                                <input type="text"
                                                                    name="gelar_belakang_{{ $i }}"
                                                                    id="gelar_belakang_{{ $i }}"
                                                                    class="form-control @error('gelar_belakang_' . $i) is-invalid @enderror"
                                                                    value="{{ old('gelar_belakang_' . $i, $member->{'gelar_belakang_' . $i}) }}">
                                                                @error('gelar_belakang_' . $i)
                                                                    <div class="invalid-feedback">{{ $message }}
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        @endfor

                                                        <div class="mb-3">
                                                            <label for="nik" class="form-label">NIK <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text"
                                                                class="form-control @error('nik') is-invalid @enderror"
                                                                id="nik" name="nik"
                                                                value="{{ old('nik', $member->nik) }}" maxlength="16"
                                                                required>
                                                            @error('nik')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="tempat_lahir"
                                                                        class="form-label">Tempat Lahir <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="text"
                                                                        class="form-control @error('tempat_lahir') is-invalid @enderror"
                                                                        id="tempat_lahir" name="tempat_lahir"
                                                                        value="{{ old('tempat_lahir', $member->tempat_lahir) }}"
                                                                        required>
                                                                    @error('tempat_lahir')
                                                                        <div class="invalid-feedback">{{ $message }}
                                                                        </div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="tanggal_lahir"
                                                                        class="form-label">Tanggal Lahir <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="date" class="form-control"
                                                                        id="tanggal_lahir" name="tanggal_lahir"
                                                                        required
                                                                        value="{{ old('tanggal_lahir', $user->member && $user->member->tanggal_lahir ? $user->member->tanggal_lahir->format('Y-m-d') : '') }}">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="no_hp" class="form-label">No.
                                                                        HP <span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control"
                                                                        id="no_hp" name="no_hp"
                                                                        value="{{ old('no_hp', $member->no_hp) }}"
                                                                        required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="no_wa" class="form-label">No.
                                                                        WhatsApp <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control"
                                                                        id="no_wa" name="no_wa"
                                                                        value="{{ old('no_wa', $member->no_wa) }}"
                                                                        required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <h6 class="border-bottom pb-2 mb-3">
                                                            <i class="fas fa-map-marker-alt me-2"></i>Alamat &
                                                            Institusi
                                                        </h6>

                                                        <div class="mb-3">
                                                            <label for="alamat_jalan" class="form-label">Alamat
                                                                Lengkap <span class="text-danger">*</span></label>
                                                            <textarea class="form-control @error('alamat_jalan') is-invalid @enderror" id="alamat_jalan" name="alamat_jalan"
                                                                rows="3" required>{{ old('alamat_jalan', $member->alamat_jalan) }}</textarea>
                                                            @error('alamat_jalan')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="provinsi" class="form-label">Provinsi
                                                                        <span class="text-danger">*</span></label>
                                                                    <select id="provinsi" name="provinsi"
                                                                        class="form-control"
                                                                        data-selected="{{ $user->member->provinsi }}"
                                                                        required>
                                                                        <option value="">Pilih Provinsi</option>
                                                                        {{-- Options dimuat via JavaScript --}}
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="kabupaten"
                                                                        class="form-label">Kabupaten/Kota <span
                                                                            class="text-danger">*</span></label>
                                                                    <select id="kabupaten" name="kabupaten"
                                                                        class="form-control"
                                                                        data-selected="{{ $user->member->kabupaten }}"
                                                                        required>
                                                                        <option value="">Pilih Kabupaten</option>
                                                                        {{-- Options dimuat via JavaScript --}}
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="kecamatan"
                                                                        class="form-label">Kecamatan <span
                                                                            class="text-danger">*</span></label>
                                                                    <select id="kecamatan" name="kecamatan"
                                                                        class="form-control"
                                                                        data-selected="{{ $user->member->kecamatan }}"
                                                                        required>
                                                                        <option value="">Pilih Kecamatan</option>
                                                                        {{-- Options dimuat via JavaScript --}}
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="kelurahan"
                                                                        class="form-label">Kelurahan <span
                                                                            class="text-danger">*</span></label>
                                                                    <select id="kelurahan" name="kelurahan"
                                                                        class="form-control"
                                                                        data-selected="{{ $user->member->kelurahan }}"
                                                                        required>
                                                                        <option value="">Pilih Kelurahan</option>
                                                                        {{-- Options dimuat via JavaScript --}}
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="kode_pos" class="form-label">Kode Pos <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" id="kode_pos"
                                                                name="kode_pos"
                                                                value="{{ old('kode_pos', $member->kode_pos) }}"
                                                                required>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="universitas" class="form-label">Universitas
                                                                <span class="text-danger">*</span></label>
                                                            <input type="text"
                                                                class="form-control @error('universitas') is-invalid @enderror"
                                                                id="universitas" name="universitas"
                                                                value="{{ old('universitas', $member->universitas) }}"
                                                                required>
                                                            @error('universitas')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="fakultas" class="form-label">Fakultas <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text"
                                                                class="form-control @error('fakultas') is-invalid @enderror"
                                                                id="fakultas" name="fakultas"
                                                                value="{{ old('fakultas', $member->fakultas) }}"
                                                                required>
                                                            @error('fakultas')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="prodi" class="form-label">Program Studi
                                                                <span class="text-danger">*</span></label>
                                                            <input type="text"
                                                                class="form-control @error('prodi') is-invalid @enderror"
                                                                id="prodi" name="prodi"
                                                                value="{{ old('prodi', $member->prodi) }}" required>
                                                            @error('prodi')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="email_institusi" class="form-label">Email
                                                                Institusi <span class="text-danger">*</span></label>
                                                            <input type="email" class="form-control"
                                                                id="email_institusi" name="email_institusi"
                                                                value="{{ old('email_institusi', $member->email_institusi) }}"
                                                                required>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- Bukti Pembayaran --}}
                                            <div class="row mt-4">
                                                <div class="col-12">
                                                    <h6 class="border-bottom pb-2 mb-3"><i
                                                            class="fas fa-receipt me-2"></i>Bukti Pembayaran</h6>
                                                    {{-- Informasi Pembayaran --}}
                                                    @if ($paymentSetting)
                                                        <div class="row mb-4">
                                                            <div class="col-12">
                                                                <div class="card">
                                                                    <div class="card-header bg-info">
                                                                        <h5 class="card-title text-white mb-0">
                                                                            <i
                                                                                class="fas fa-credit-card me-2"></i>Informasi
                                                                            Pembayaran
                                                                        </h5>
                                                                    </div>
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <div class="mb-3">
                                                                                    <strong>Biaya Pendaftaran:</strong>
                                                                                    <span class="text-success fs-5">Rp
                                                                                        {{ number_format($paymentSetting->payment_amount, 0, ',', '.') }}</span>
                                                                                </div>
                                                                                <div class="mb-3">
                                                                                    <strong>Bank:</strong>
                                                                                    {{ $paymentSetting->bank_name }}
                                                                                </div>
                                                                                <div class="mb-3">
                                                                                    <strong>Nomor Rekening:</strong>
                                                                                    {{ $paymentSetting->account_number }}
                                                                                </div>
                                                                                <div class="mb-3">
                                                                                    <strong>Atas Nama:</strong>
                                                                                    {{ $paymentSetting->account_holder }}
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="alert alert-info">
                                                                                    <i
                                                                                        class="fas fa-info-circle me-2"></i>
                                                                                    <strong>Petunjuk
                                                                                        Pembayaran:</strong>
                                                                                    <ol class="mb-0 mt-2">
                                                                                        <li>Transfer sesuai nominal yang
                                                                                            tertera</li>
                                                                                        <li>Simpan bukti transfer</li>
                                                                                        <li>Upload bukti pembayaran
                                                                                            melalui link</li>
                                                                                        <li>Tunggu konfirmasi dari admin
                                                                                        </li>
                                                                                    </ol>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="mb-3">
                                                        <label for="payment_proof_link" class="form-label">
                                                            Link Bukti Pembayaran <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="url"
                                                            class="form-control @error('payment_proof_link') is-invalid @enderror"
                                                            id="payment_proof_link" name="payment_proof_link"
                                                            value="{{ old('payment_proof_link') }}"
                                                            placeholder="https://drive.google.com/..." required>
                                                        <div class="form-text">
                                                            Upload bukti transfer ke Google Drive atau layanan cloud
                                                            storage lainnya,
                                                            kemudian paste link-nya di sini.
                                                        </div>
                                                        @error('payment_proof_link')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-end">
                                                <button type="submit" class="btn btn-success btn-lg">
                                                    <i
                                                        class="fas fa-{{ !$membership || ($membership->status === 'rejected' && !$isLastRejectedPaymentRenewal) ? 'user-plus' : 'refresh' }} me-2"></i>
                                                    @if (!$membership)
                                                        Daftar Sekarang
                                                    @elseif ($membership->status === 'rejected' && $isLastRejectedPaymentRenewal)
                                                        Perpanjang Ulang
                                                    @elseif ($membership->status === 'rejected')
                                                        {{-- Ini berarti rejected new_registration --}}
                                                        Daftar Ulang
                                                    @else
                                                        {{-- Ini berarti inactive atau expired --}}
                                                        Perpanjang Membership
                                                    @endif
                                                </button>
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            {{-- Pembayaran Pending --}}
            @if ($pendingPayment)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-clock me-2"></i>Pembayaran Menunggu Konfirmasi
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <p class="mb-3">
                                            <strong>Pembayaran Anda sedang diproses oleh admin.</strong><br>
                                            Silakan tunggu konfirmasi dalam 1-3 hari kerja.
                                        </p>
                                        <div class="mb-2">
                                            <strong>Jumlah:</strong> Rp
                                            {{ number_format($pendingPayment->amount, 0, ',', '.') }}
                                        </div>
                                        <div class="mb-2">
                                            <strong>Tanggal:</strong>
                                            {{ $pendingPayment->created_at->format('d F Y H:i') }}
                                        </div>
                                        <div class="mb-2">
                                            <strong>Tipe:</strong>
                                            {{ $pendingPayment->payment_type === 'new_registration' ? 'Pendaftaran Baru' : 'Perpanjangan' }}
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Status:</strong><br>
                                            <span class="badge bg-warning fs-6 mt-2">Menunggu Konfirmasi</span>
                                        </div>
                                        @if ($pendingPayment->payment_proof_link)
                                            <a href="{{ $pendingPayment->payment_proof_link }}" target="_blank"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-external-link-alt me-1"></i>Lihat Bukti Bayar
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Riwayat Pembayaran --}}
            @if (count($paymentHistory) > 0)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div
                                class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                                <h5 class="card-title text-white mb-0">
                                    <i class="fas fa-history me-2"></i>Riwayat Pembayaran
                                </h5>
                                {{-- <a href="{{ route('keanggotaan.payment-history') }}" class="btn btn-sm btn-light">
                                    <i class="fas fa-eye me-1"></i>Lihat Semua
                                </a> --}}
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Tipe</th>
                                                <th>Jumlah</th>
                                                <th>Status</th>
                                                <th>Periode</th>
                                                <th>Bukti Pembayaran</th>
                                                <th>Invoice</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($paymentHistory as $payment)
                                                <tr>
                                                    <td>{{ $payment->created_at->format('d/m/Y') }}</td>
                                                    <td>
                                                        <span class="badge bg-info">
                                                            {{ $payment->payment_type === 'new_registration' ? 'Pendaftaran' : 'Perpanjangan' }}
                                                        </span>
                                                    </td>
                                                    <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                                    <td>
                                                        <span class="badge bg-success">Disetujui</span>
                                                    </td>
                                                    <td>
                                                        @if ($payment->approved_at && $payment->active_until)
                                                            {{ $payment->approved_at->format('d/m/Y') }} -
                                                            {{ $payment->active_until->format('d/m/Y') }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($payment->payment_proof_link)
                                                            <a href="{{ $payment->payment_proof_link }}"
                                                                target="_blank" class="btn btn-sm btn-outline-info"
                                                                title="Lihat Bukti Pembayaran">
                                                                <i class="fas fa-image"></i>
                                                            </a>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($payment->invoice_link)
                                                            <a href="{{ $payment->invoice_link }}" target="_blank"
                                                                class="btn btn-sm btn-outline-success"
                                                                title="Lihat Invoice">
                                                                <i class="fas fa-file-invoice"></i>
                                                            </a>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </x-layout.content-bar>
    </div>

    {{-- SweetAlert2 --}}
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: '{{ session('success') }}',
                timer: 3000,
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
<script>
    // API untuk memuat data wilayah
    document.addEventListener('DOMContentLoaded', function() {
        const baseUrl = '/api/wilayah';

        const provinsiSelect = document.getElementById('provinsi');
        const kabupatenSelect = document.getElementById('kabupaten');
        const kecamatanSelect = document.getElementById('kecamatan');
        const kelurahanSelect = document.getElementById('kelurahan');
        const kodePosInput = document.getElementById('kode_pos');

        const selectedProvinsi = provinsiSelect.getAttribute('data-selected');
        const selectedKabupaten = kabupatenSelect.getAttribute('data-selected');
        const selectedKecamatan = kecamatanSelect.getAttribute('data-selected');
        const selectedKelurahan = kelurahanSelect.getAttribute('data-selected');

        function clearOptions(select, label) {
            select.innerHTML = `<option value="">Pilih ${label}</option>`;
        }

        function populateSelect(select, data, label) {
            clearOptions(select, label);
            const selectedName = select.getAttribute('data-selected');

            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.name;
                if (item.name === selectedName) option.selected = true;
                select.appendChild(option);
            });
        }

        // Prefill Provinsi -> Kabupaten -> Kecamatan -> Kelurahan -> Kode Pos
        fetch(`${baseUrl}/provinces`)
            .then(res => res.json())
            .then(data => {
                populateSelect(provinsiSelect, data, 'Provinsi');
                const prov = data.find(p => p.name === selectedProvinsi);
                if (prov) {
                    provinsiSelect.value = prov.id;

                    fetch(`${baseUrl}/cities/${prov.id}`)
                        .then(res => res.json())
                        .then(data => {
                            populateSelect(kabupatenSelect, data, 'Kabupaten');
                            const kab = data.find(c => c.name === selectedKabupaten);
                            if (kab) {
                                kabupatenSelect.value = kab.id;

                                fetch(`${baseUrl}/districts/${kab.id}`)
                                    .then(res => res.json())
                                    .then(data => {
                                        populateSelect(kecamatanSelect, data, 'Kecamatan');
                                        const kec = data.find(k => k.name === selectedKecamatan);
                                        if (kec) {
                                            kecamatanSelect.value = kec.id;

                                            fetch(`${baseUrl}/subdistricts/${kec.id}`)
                                                .then(res => res.json())
                                                .then(data => {
                                                    populateSelect(kelurahanSelect, data,
                                                        'Kelurahan');
                                                    const kel = data.find(k => k.name ===
                                                        selectedKelurahan);
                                                    if (kel) {
                                                        kelurahanSelect.value = kel.id;

                                                        fetch(`${baseUrl}/postalcode/${kel.id}`)
                                                            .then(res => res.json())
                                                            .then(data => {
                                                                if (data.postal_code) {
                                                                    kodePosInput.value =
                                                                        data.postal_code;
                                                                }
                                                            });
                                                    }
                                                });
                                        }
                                    });
                            }
                        });
                }
            });

        // Event handler untuk user action
        provinsiSelect.addEventListener('change', function() {
            fetch(`${baseUrl}/cities/${this.value}`)
                .then(res => res.json())
                .then(data => populateSelect(kabupatenSelect, data, 'Kabupaten/Kota'));

            clearOptions(kabupatenSelect, 'Kabupaten/Kota');
            clearOptions(kecamatanSelect, 'Kecamatan');
            clearOptions(kelurahanSelect, 'Kelurahan');
            kodePosInput.value = '';
        });

        kabupatenSelect.addEventListener('change', function() {
            fetch(`${baseUrl}/districts/${this.value}`)
                .then(res => res.json())
                .then(data => populateSelect(kecamatanSelect, data, 'Kecamatan'));

            clearOptions(kecamatanSelect, 'Kecamatan');
            clearOptions(kelurahanSelect, 'Kelurahan');
            kodePosInput.value = '';
        });

        kecamatanSelect.addEventListener('change', function() {
            fetch(`${baseUrl}/subdistricts/${this.value}`)
                .then(res => res.json())
                .then(data => populateSelect(kelurahanSelect, data, 'Kelurahan'));

            clearOptions(kelurahanSelect, 'Kelurahan');
            kodePosInput.value = '';
        });

        kelurahanSelect.addEventListener('change', function() {
            fetch(`${baseUrl}/postalcode/${this.value}`)
                .then(res => res.json())
                .then(data => {
                    kodePosInput.value = data.postal_code || '';
                });
        });
    });
</script>
