@props(['user'])

<div class="sidebar-menu flex-grow p-4">
    <div class="card p-3 rounded-2xl" style="background-color: #EDF4EA; border-color: #0BAF6A; border-width: 1px;">
        <div class="flex flex-col gap-2">
            @if ($user->role === 'member')
                <a href="{{ route('dashboard') }}"
                    class="{{ request()->routeIs('dashboard') ? 'text-green-700' : '' }}">Dashboard</a>
                <a href="{{ route('biografi') }}"
                    class="{{ request()->routeIs('biografi') ? 'text-green-700' : '' }}">Biografi</a>
                <a href="{{ route('publikasi') }}"
                    class="{{ request()->routeIs('publikasi') ? 'text-green-700' : '' }}">Publikasi</a>
            @elseif ($user->role === 'admin')
                <a href="{{ route('admin.dashboard') }}"
                    class="{{ request()->routeIs('admin.dashboard') ? 'text-green-700' : '' }}">Dashboard</a>
                <a href="{{ route('admin.beranda-divisi') }}"
                    class="{{ request()->routeIs('admin.beranda-divisi') ? 'text-green-700' : '' }}">Beranda Divisi</a>
                <a href="{{ route('admin.subdivisi') }}"
                    class="{{ request()->routeIs('admin.subdivisi') ? 'text-green-700' : '' }}">Subdivisi</a>
                <a href="{{ route('admin.anggota') }}"
                    class="{{ request()->routeIs('admin.anggota') ? 'text-green-700' : '' }}">Anggota</a>
            @elseif ($user->role === 'super admin')
                <a href="{{ route('superadmin.dashboard') }}"
                    class="{{ request()->routeIs('superadmin.dashboard') ? 'text-green-700' : '' }}">Dashboard</a>
                <a href="{{ route('superadmin.divisi') }}"
                    class="{{ request()->routeIs('superadmin.divisi') ? 'text-green-700' : '' }}">Divisi</a>
                <a href="{{ route('superadmin.admin') }}"
                    class="{{ request()->routeIs('superadmin.admin') ? 'text-green-700' : '' }}">Admin</a>
                <a href="{{ route('superadmin.member') }}"
                    class="{{ request()->routeIs('superadmin.member') ? 'text-green-700' : '' }}">Member</a>
                <a href="{{ route('superadmin.membership') }}"
                    class="{{ request()->routeIs('superadmin.membership') ? 'text-green-700' : '' }}">Membership Keanggotaan</a>
                <a href="{{ route('superadmin.berita') }}"
                    class="{{ request()->routeIs('superadmin.berita') ? 'text-green-700' : '' }}">Berita</a>
                <a href="{{ route('superadmin.publikasi-adn') }}"
                    class="{{ request()->routeIs('superadmin.publikasi-adn') ? 'text-green-700' : '' }}">Publikasi ADN</a>
                <a href="{{ route('superadmin.tri-dharma') }}"
                    class="{{ request()->routeIs('superadmin.tri-dharma') ? 'text-green-700' : '' }}">Tri Dharma PT</a>
                <a href="{{ route('superadmin.landing') }}"
                    class="{{ request()->routeIs('superadmin.landing') ? 'text-green-700' : '' }}">Landing Page</a>
            @endif
        </div>
    </div>
</div>
