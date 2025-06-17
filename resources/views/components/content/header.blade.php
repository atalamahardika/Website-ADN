@props(['user', 'title', 'subtitle'])

<div class="content-bar-header sticky top-0 bg-white z-10" style="border-bottom: 1px solid #0BAF6A;">
    <div class="container flex justify-content-between items-center py-3">
        <div class="text-wrapper">
            <h5>{{ $title }}</h5>
            <p class="mb-0">{{ $subtitle }}</p>
        </div>

        <div class="relative h-full" x-data="{ open: false }">
            <!-- Tombol Profile -->
            <button @click="open = !open"
                class="flex items-center py-[8px] px-[20px] border-[1.5px] rounded w-full focus:outline-none"
                style="border-color: #0BAF6A;">
                <img class="w-10 h-10 rounded-full object-cover mr-4" src="{{ $user->profile_photo_url }}"
                    alt="{{ $user->name }}" />
                <span>{{ $user->name }}</span>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open" @click.outside="open = false"
                class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-50">

                <!-- Profil tersedia untuk semua role -->
                {{-- <a href="{{ route('profile.edit') }}"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil Saya</a> --}}

                <!-- Tampilkan "Keanggotaan" hanya jika role-nya 'member' -->
                @if ($user->role === 'member')
                    <a href="{{ route('profile.edit') }}"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil Saya</a>
                    <a href="{{ route('keanggotaan') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Keanggotaan</a>
                    <a href="{{ route('ganti-password') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Ubah Kata
                        Sandi</a>
                @elseif ($user->role === 'admin')
                    <a href="{{ route('admin.profile.edit') }}"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil Saya</a>
                    <a href="{{ route('admin.ganti-password') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Ubah Kata
                        Sandi</a>
                @elseif ($user->role === 'super admin')
                    <a href="{{ route('superadmin.profile.edit') }}"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil Saya</a>
                    <a href="{{ route('superadmin.ganti-password') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Ubah Kata
                        Sandi</a>
                @endif

                {{-- <a
                    href="{{ route(Auth::user()->hasRole('admin') ? 'admin.ganti_password' : (Auth::user()->hasRole('super admin') ? 'superadmin.ganti_password' : 'member.ganti_password')) }}">
                    Ganti Password
                </a> --}}
                <!-- Logout tersedia untuk semua role -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Logout</button>
                </form>
            </div>

        </div>
    </div>
</div>
