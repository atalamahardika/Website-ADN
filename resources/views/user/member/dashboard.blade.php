@include('head')

<body>
    <div class="flex h-screen">
        <x-layout.sidebar />
        <x-layout.content-bar :title="$title" :subtitle="$subtitle">
            {{-- Slot utama di sini, bisa diganti sesuai konten --}}
            @includeWhen($title === 'Dashboard', 'components.content.main')

            {{-- Tulis kode di bawah ini untuk isi kontennya --}}
            {{-- Biografi Singkat --}}
            <div class="biography">
                <div class="card" style="background-color: #EDF4EA; padding: 24px;">
                    <h5>Biografi</h5>
                    @if (!empty($user->member->biografi))
                        <p>{{ $user->member->biografi }}</p>
                    @else
                        <p class="text-gray-500 italic">Belum ada biografi yang ditulis.</p>
                    @endif
                </div>
            </div>

            {{-- Bidang Keilmuan --}}
            <div class="keilmuan">
                <div class="card" style="background-color: #EDF4EA; padding: 24px;">
                    <h5>Bidang Keilmuan</h5>
                    @if ($scientificFields->count())
                        <ul class="list-disc pl-5 space-y-1 text-gray-800">
                            @foreach ($scientificFields as $field)
                                <li>{{ $field->name }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500">Belum ada bidang keilmuan yang ditambahkan.</p>
                    @endif
                </div>
            </div>

            {{-- Keahlian/Kepakaran --}}
            <div class="keahlian">
                <div class="card" style="background-color: #EDF4EA; padding: 24px;">
                    <h5>Keahlian/kepakaran</h5>
                    @if ($skills->count())
                        <ul class="list-disc pl-5 space-y-1 text-gray-800">
                            @foreach ($skills as $skill)
                                <li>{{ $skill->name }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500 italic">Belum ada keahlian yang ditambahkan.</p>
                    @endif
                </div>
            </div>

            {{-- Riwayat Pendidikan --}}
            <div class="pendidikan">
                <div class="card" style="background-color: #EDF4EA; padding: 24px;">
                    <h5>Riwayat Pendidikan</h5>
                    @if ($educationalHistories->count())
                        <ul class="list-disc pl-5 space-y-1 text-gray-800">
                            @foreach ($educationalHistories as $edu)
                                <li>
                                    <div>
                                        <strong>{{ $edu->jenjang }}</strong> – {{ $edu->program_studi }}
                                        ({{ $edu->institusi }})
                                        <br>
                                        <small class="text-gray-600">{{ $edu->tahun_masuk }} -
                                            {{ $edu->tahun_lulus }}</small>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500 italic">Belum ada riwayat pendidikan.</p>
                    @endif
                </div>
            </div>

            {{-- Penghargaan --}}
            <div class="penghargaan">
                <div class="card" style="background-color: #EDF4EA; padding: 24px;">
                    <h5>Penghargaan</h5>
                    @if ($awards->count())
                        <ul class="list-disc pl-5 space-y-1 text-gray-800">
                            @foreach ($awards as $award)
                                <li>
                                    <div>
                                        <strong>{{ $award->nama }}</strong>
                                        @if ($award->penyelenggara)
                                            <span>oleh {{ $award->penyelenggara }}</span>
                                        @endif
                                        @if ($award->tahun)
                                            <br><small class="text-gray-600">{{ $award->tahun }}</small>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500 italic">Belum ada penghargaan yang ditambahkan.</p>
                    @endif
                </div>
            </div>

            {{-- Riwayat Mengajar --}}
            <div class="mengajar">
                <div class="card" style="background-color: #EDF4EA; padding: 24px;">
                    <h5>Riwayat Mengajar</h5>
                    @if ($teachingHistories->count())
                        <ul class="list-disc pl-5 space-y-1 text-gray-800">
                            @foreach ($teachingHistories as $teach)
                                <li>
                                    <div>
                                        <strong>{{ $teach->mata_kuliah }}</strong> – {{ $teach->institusi }}
                                        <br>
                                        <small class="text-gray-600">{{ $teach->tahun_ajar }}</small>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500 italic">Belum ada riwayat mengajar.</p>
                    @endif
                </div>
            </div>

            {{-- List Publikasi --}}
            <div class="container">
                <div class="publication-member">
                    <h4 class="font-semibold mb-3">List Publikasi</h4>

                    @if ($publications->count())
                        @foreach ($publications as $pub)
                            <div class="card p-3 mb-3 bg-white rounded shadow-sm">
                                <h5 class="text-lg font-medium">{{ $pub->title }}</h5>
                                <p class="text-gray-700">
                                    {{ $pub->formatted_authors }} ({{ $pub->year }}),
                                    ‘{{ $pub->title }}’,
                                    <em>{{ $pub->journal_name }}</em>
                                    @if ($pub->volume)
                                        , vol. {{ $pub->volume }}
                                    @endif
                                    @if ($pub->pages)
                                        , hh. {{ $pub->pages }}
                                    @endif.
                                </p>
                                @if ($pub->link)
                                    <a href="{{ $pub->link }}" target="_blank" class="text-blue-600 underline">
                                        Lihat publikasi
                                    </a>
                                @endif
                            </div>
                        @endforeach
                        
                        {{-- Pagination --}}
                        <div class="mt-4">
                            {{ $publications->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    @else
                        <p class="text-gray-500 italic">Belum ada publikasi yang ditambahkan.</p>
                    @endif
                </div>
            </div>
        </x-layout.content-bar>
    </div>
</body>
