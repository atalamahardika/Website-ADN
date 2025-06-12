@props(['user'])

@php
    $gelarDepan = $user->member->gelar_depan ?? null;
    $gelarBelakang = collect([
        $user->member->gelar_belakang_1 ?? null,
        $user->member->gelar_belakang_2 ?? null,
        $user->member->gelar_belakang_3 ?? null,
    ])
        ->filter()
        ->implode(', ');

    $namaLengkap = trim(
        ($gelarDepan ? $gelarDepan . ' ' : '') . $user->name . ($gelarBelakang ? ', ' . $gelarBelakang : ''),
    );
@endphp

<div class="sidebar-profile flex-grow px-4 pt-4">
    <div class="flex justify-center card py-4" style="background-color: #EDF4EA; border-color: #0BAF6A;">
        <div>
            <div class="flex justify-center mb-4">
                <img src="{{ $user->profile_photo_url }}" alt="" class="rounded-circle"
                    style="width: 100px; height: 100px;">
            </div>

            <div class="text-center mb-4">
                <span class="fw-bold">{{ $namaLengkap }}</span>

                {{-- Keterangan di bawah nama untuk user role admin --}}
                @if ($user->role === 'admin')
                    <div class="mt-2 text-sm text-gray-700">
                        @if ($user->division)
                            <div>{{ $user->division->title }}</div>
                        @else
                            <div class="text-danger">Anda belum mengelola divisi manapun. Hubungi Super Admin.</div>
                        @endif
                    </div>
                @endif
            </div>

            @if ($user->role === 'member')
                <ul class="px-4 mb-0">
                    <li class="flex gap-3 mb-3">
                        <div class="grid grid-cols-[40px_1fr] grid-rows-[auto_auto] gap-x-4">
                            <div class="flex justify-center">
                                <img src="{{ asset('icon/University building.png') }}" alt="Icon"
                                    class="w-[20px] h-[20px]">
                            </div>
                            <div class="flex items-center font-semibold text-sm">
                                Universitas
                            </div>
                            <div></div>
                            <div class="text-sm text-gray-700">
                                {{ $user->member->universitas ?? '-' }}
                            </div>
                        </div>
                    </li>

                    <li class="flex gap-3 mb-3">
                        <div class="grid grid-cols-[40px_1fr] grid-rows-[auto_auto] gap-x-4">
                            <div class="flex justify-center">
                                <img src="{{ asset('icon/Mortarboard.png') }}" alt="Icon" class="w-[20px] h-[20px]">
                            </div>
                            <div class="flex items-center font-semibold text-sm">
                                Fakultas
                            </div>
                            <div></div>
                            <div class="text-sm text-gray-700">
                                {{ $user->member->fakultas ?? '-' }}
                            </div>
                        </div>
                    </li>

                    <li class="flex gap-3 mb-3">
                        <div class="grid grid-cols-[40px_1fr] grid-rows-[auto_auto] gap-x-4">
                            <div class="flex justify-center">
                                <img src="{{ asset('icon/Reading book.png') }}" alt="Icon"
                                    class="w-[20px] h-[20px]">
                            </div>
                            <div class="flex items-center font-semibold text-sm">
                                Program Studi
                            </div>
                            <div></div>
                            <div class="text-sm text-gray-700">
                                {{ $user->member->prodi ?? '-' }}
                            </div>
                        </div>
                    </li>
                </ul>
            @endif
        </div>
    </div>
</div>
