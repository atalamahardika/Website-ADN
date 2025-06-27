@props(['user', 'title', 'subtitle'])

<div class="content-bar-header sticky top-0 bg-white z-10 border-bottom" style="border-color: #0BAF6A;">
    <div class="container py-3">

        <!-- Baris Pertama (Tablet & Mobile): Hamburger + Profile -->
        <div class="d-flex justify-content-between align-items-center mb-2 d-xl-none">
            <!-- Hamburger -->
            <button class="btn btn-outline-success" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Profile Dropdown (Mobile/Tablet) -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                    class="flex items-center py-[6px] px-[12px] border-[1.5px] rounded"
                    style="border-color: #0BAF6A;">
                    <img class="w-8 h-8 rounded-full object-cover me-2" src="{{ $user->profile_photo_url }}"
                        alt="{{ $user->name }}" />
                    <span>{{ $user->name }}</span>
                </button>

                <div x-show="open" @click.outside="open = false"
                    class="dropdown-menu position-absolute end-0 mt-2 show" style="min-width: 12rem;">
                    <!-- Isi dropdown -->
                    @if ($user->role === 'member')
                        <a href="{{ route('profile.edit') }}" class="dropdown-item">Profil Saya</a>
                        <a href="{{ route('keanggotaan') }}" class="dropdown-item">Keanggotaan</a>
                        <a href="{{ route('ganti-password') }}" class="dropdown-item">Ubah Kata Sandi</a>
                    @elseif ($user->role === 'admin')
                        <a href="{{ route('admin.profile.edit') }}" class="dropdown-item">Profil Saya</a>
                        <a href="{{ route('admin.ganti-password') }}" class="dropdown-item">Ubah Kata Sandi</a>
                    @elseif ($user->role === 'super admin')
                        <a href="{{ route('superadmin.profile.edit') }}" class="dropdown-item">Profil Saya</a>
                        <a href="{{ route('superadmin.ganti-password') }}" class="dropdown-item">Ubah Kata Sandi</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">Logout</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Baris Kedua: Title & Subtitle (Mobile & Tablet) atau Satu baris penuh di Desktop -->
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <!-- Title & Subtitle -->
            <div class="header-title-subtitle">
                <h5 class="mb-1">{{ $title }}</h5>
                <p class="mb-0">{{ $subtitle }}</p>
            </div>

            <!-- Profile Dropdown (Desktop only) -->
            <div class="d-none d-xl-block" x-data="{ open: false }">
                <button @click="open = !open"
                    class="flex items-center py-[8px] px-[20px] border-[1.5px] rounded"
                    style="border-color: #0BAF6A;">
                    <img class="w-10 h-10 rounded-full object-cover me-2" src="{{ $user->profile_photo_url }}"
                        alt="{{ $user->name }}" />
                    <span>{{ $user->name }}</span>
                </button>

                <div x-show="open" @click.outside="open = false"
                    class="dropdown-menu position-absolute end-0 mt-2 show" style="min-width: 12rem;">
                    <!-- Isi dropdown sama -->
                    @if ($user->role === 'member')
                        <a href="{{ route('profile.edit') }}" class="dropdown-item">Profil Saya</a>
                        <a href="{{ route('keanggotaan') }}" class="dropdown-item">Keanggotaan</a>
                        <a href="{{ route('ganti-password') }}" class="dropdown-item">Ubah Kata Sandi</a>
                    @elseif ($user->role === 'admin')
                        <a href="{{ route('admin.profile.edit') }}" class="dropdown-item">Profil Saya</a>
                        <a href="{{ route('admin.ganti-password') }}" class="dropdown-item">Ubah Kata Sandi</a>
                    @elseif ($user->role === 'super admin')
                        <a href="{{ route('superadmin.profile.edit') }}" class="dropdown-item">Profil Saya</a>
                        <a href="{{ route('superadmin.ganti-password') }}" class="dropdown-item">Ubah Kata Sandi</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">Logout</button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
