@include('head')

@php
    $editingSkillId = request()->query('edit_skill');
    $editingEducationId = request()->query('edit_edu');
    $editingAwardId = request()->query('edit_award');
    $editingKeilmuanId = request()->query('edit_sci');
    $editingMengajarId = request()->query('edit_mengajar');
@endphp

<body>
    <div class="flex h-screen">
        <x-layout.sidebar />
        <x-layout.content-bar :title="$title" :subtitle="$subtitle">
            {{-- Slot utama di sini, bisa diganti sesuai konten --}}
            @includeWhen($title === 'Biografi', 'components.content.main')

            {{-- Tulis Konten CRUD di bawah ini --}}
            {{-- Biografi --}}
            <div class="biografi">
                <div class="card" style="background-color: #EDF4EA; padding: 24px;">
                    <h5 class="font-semibold mb-2">Biografi Singkat</h5>
                    {{-- Tampilkan keterangan jika belum ada biografi --}}
                    @if (empty($user->member->biografi))
                        <p class="text-gray-500 italic mb-2">Belum ada biografi singkat yang ditulis.</p>
                    @endif
                    <form method="POST" action="{{ route('biografi.update') }}">
                        @csrf
                        @method('PATCH')

                        <textarea name="biografi" rows="6" class="form-control editor">{{ old('biografi', $user->member->biografi ?? '') }}</textarea>
                        @error('biografi')
                            <div class="text-danger mt-1">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="mt-3 text-end">
                            <button type="submit" class="btn btn-primary">Simpan Biografi</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Bidang Keilmuan --}}
            <div class="bidang-keilmuan">
                <div class="card" style="background-color: #EDF4EA; padding: 24px;">
                    <div class="flex justify-between items-center mb-4">
                        <h5 class="font-semibold">Bidang Keilmuan</h5>
                        <button type="button"
                            onclick="document.getElementById('tambahKeilmuanModal').classList.remove('hidden')"
                            class="btn btn-primary">
                            Tambah
                        </button>
                    </div>

                    {{-- Modal Tambah --}}
                    <x-modal id="tambahKeilmuanModal" title="Tambah Bidang Keilmuan">
                        <form method="POST" action="{{ route('keilmuan.store') }}" class="space-y-3">
                            @csrf
                            <div class="mb-4">
                                <label for="name" class="block font-semibold">Bidang Keilmuan</label>
                                <input type="text" name="name" placeholder="Nama bidang keilmuan"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    required>
                                @error('name')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="btn btn-primary">Tambah</button>
                            </div>
                        </form>
                    </x-modal>

                    {{-- List Bidang Keilmuan --}}
                    @if ($user->member->scientificFields->count())
                        <ul class="space-y-3">
                            @foreach ($user->member->scientificFields as $field)
                                <li class="flex justify-between items-center gap-4">
                                    <span>{{ $field->name }}</span>
                                    <div class="flex gap-2">
                                        {{-- Tombol buka modal --}}
                                        <button type="button"
                                            onclick="document.getElementById('editKeilmuanModal-{{ $field->id }}').classList.remove('hidden')"
                                            class="btn btn-warning">Edit</button>

                                        {{-- Form Hapus --}}
                                        <form action="{{ route('keilmuan.destroy', $field->id) }}" method="POST"
                                            onsubmit="return confirmHapus(this)">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger" type="submit">Hapus</button>
                                        </form>
                                    </div>
                                </li>

                                {{-- Modal Edit --}}
                                <x-modal id="editKeilmuanModal-{{ $field->id }}" title="Edit Bidang Keilmuan">
                                    <form action="{{ route('keilmuan.update', $field->id) }}" method="POST"
                                        class="space-y-4">
                                        @csrf
                                        @method('PATCH')
                                        <div class="mb-4">
                                            <label for="name" class="block font-semibold">Bidang Keilmuan</label>
                                            <input type="text" name="name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                value="{{ old('name', $field->name) }}" required>
                                            @error('name')
                                                <div class="text-danger mt-1">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="flex justify-end gap-2">
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                            <button type="button"
                                                onclick="document.getElementById('editKeilmuanModal-{{ $field->id }}').classList.add('hidden')"
                                                class="btn btn-secondary">Batal</button>
                                        </div>
                                    </form>
                                </x-modal>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500">Belum ada bidang keilmuan.</p>
                    @endif
                </div>
            </div>

            {{-- Keahlian/kepakaran --}}
            <div class="keahlian">
                <div class="card" style="background-color: #EDF4EA; padding: 24px;">
                    <div class="flex justify-between items-center mb-4">
                        <h5 class="font-semibold">Keahlian / Kepakaran</h5>
                        <button onclick="document.getElementById('modalAddSkill').classList.remove('hidden')"
                            class="btn btn-primary">Tambah</button>
                    </div>

                    {{-- Modal Tambah --}}
                    <x-modal id="modalAddSkill" title="Tambah Keahlian">
                        <form method="POST" action="{{ route('keahlian.store') }}" class="space-y-3">
                            @csrf
                            <div class="mb-4">
                                <label for="name" class="block font-semibold">Keahlian</label>
                                <input type="text" name="name" placeholder="Nama keahlian"
                                    value="{{ old('name') }}"
                                    class="form-control @error('name') is-invalid @enderror" required>
                                @error('name')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </x-modal>

                    {{-- List Keahlian --}}
                    @if ($user->member->skills->count())
                        <ul class="space-y-3">
                            @foreach ($user->member->skills as $skill)
                                <li class="flex justify-between items-center gap-4">
                                    <span>{{ $skill->name }}</span>
                                    <div class="flex gap-2">
                                        <button
                                            onclick="document.getElementById('edit-skill-{{ $skill->id }}').classList.remove('hidden')"
                                            class="btn btn-warning">Edit</button>
                                        <form action="{{ route('keahlian.destroy', $skill->id) }}" method="POST"
                                            onsubmit="return confirmHapus(this)">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-danger">Hapus</button>
                                        </form>
                                    </div>
                                </li>

                                {{-- Modal Edit Skill --}}
                                <x-modal id="edit-skill-{{ $skill->id }}" title="Edit Keahlian">
                                    <form method="POST" action="{{ route('keahlian.update', $skill->id) }}"
                                        class="space-y-4">
                                        @csrf @method('PATCH')
                                        <div class="mb-4">
                                            <label for="name" class="block font-semibold">Keahlian</label>
                                            <input type="text" name="name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                value="{{ old('name', $skill->name) }}" required>
                                            @error('name')
                                                <div class="text-danger mt-1">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="flex justify-end gap-2">
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                            <button type="button"
                                                onclick="document.getElementById('edit-skill-{{ $skill->id }}').classList.add('hidden')"
                                                class="btn btn-secondary">Batal</button>
                                        </div>
                                    </form>
                                </x-modal>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500">Belum ada keahlian yang ditambahkan.</p>
                    @endif
                </div>
            </div>

            {{-- Riwayat Pendidikan --}}
            <div class="pendidikan">
                <div class="card" style="background-color: #EDF4EA; padding: 24px;">
                    <div class="flex justify-between items-center mb-4">
                        <h5 class="font-semibold">Riwayat Pendidikan</h5>
                        <button onclick="document.getElementById('modalAddEdu').classList.remove('hidden')"
                            class="btn btn-primary">Tambah</button>
                    </div>

                    {{-- Modal Tambah --}}
                    <x-modal id="modalAddEdu" title="Tambah Riwayat Pendidikan">
                        <form method="POST" action="{{ route('pendidikan.store') }}" class="space-y-3">
                            @csrf
                            <div class="mb-4">
                                <label for="jenjang" class="block font-semibold">Jenjang</label>
                                <input type="text" name="jenjang" placeholder="Jenjang"
                                    value="{{ old('jenjang') }}"
                                    class="form-control @error('jenjang') is-invalid @enderror" required>
                                @error('jenjang')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="institusi" class="block font-semibold">Institusi</label>
                                <input type="text" name="institusi" placeholder="Institusi"
                                    value="{{ old('institusi') }}"
                                    class="form-control @error('institusi') is-invalid @enderror" required>
                                @error('institusi')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="program_studi" class="block font-semibold">Program Studi</label>
                                <input type="text" name="program_studi" placeholder="Program Studi"
                                    value="{{ old('program_studi') }}"
                                    class="form-control @error('program_studi') is-invalid @enderror" required>
                                @error('program_studi')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="tahun_masuk" class="block font-semibold">Tahun Masuk</label>
                                <input type="number" name="tahun_masuk" placeholder="Tahun Masuk"
                                    value="{{ old('tahun_masuk') }}"
                                    class="form-control @error('tahun_masuk') is-invalid @enderror" required>
                                @error('tahun_masuk')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="tahun_lulus" class="block font-semibold">Tahun Lulus</label>
                                <input type="number" name="tahun_lulus" placeholder="Tahun Lulus"
                                    value="{{ old('tahun_lulus') }}"
                                    class="form-control @error('tahun_lulus') is-invalid @enderror" required>
                                @error('tahun_lulus')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </x-modal>

                    {{-- Error Handling Untuk Tetap Membuka Modal Tambah Riwayat Pendidikan --}}
                    @if (session('modal') === 'modalAddEdu')
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                document.getElementById('modalAddEdu').classList.remove('hidden');
                            });
                        </script>
                    @endif

                    {{-- List Riwayat Pendidikan --}}
                    @if ($user->member->educationalHistories->count())
                        <ul class="space-y-2">
                            @foreach ($user->member->educationalHistories as $edu)
                                <li class="bg-white p-3 rounded shadow-sm flex justify-between items-center">
                                    <div>
                                        <strong>{{ $edu->jenjang }}</strong> – {{ $edu->program_studi }}
                                        ({{ $edu->institusi }})
                                        <br>
                                        <small class="text-gray-600">{{ $edu->tahun_masuk }} -
                                            {{ $edu->tahun_lulus }}</small>
                                    </div>
                                    <div class="flex gap-2">
                                        <button
                                            onclick="document.getElementById('edit-edu-{{ $edu->id }}').classList.remove('hidden')"
                                            class="btn btn-warning">Edit</button>
                                        <form method="POST" action="{{ route('pendidikan.destroy', $edu->id) }}"
                                            onsubmit="return confirmHapus(this)">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Hapus</button>
                                        </form>
                                    </div>
                                </li>

                                {{-- Modal Edit Pendidikan --}}
                                <x-modal id="edit-edu-{{ $edu->id }}" title="Edit Riwayat Pendidikan">
                                    <form method="POST" action="{{ route('pendidikan.update', $edu->id) }}"
                                        class="grid grid-cols-1 gap-2">
                                        @csrf @method('PATCH')
                                        <div class="mb-4">
                                            <label for="edit_jenjang_{{ $edu->id }}"
                                                class="block font-semibold">Jenjang</label>
                                            <input type="text" id="edit_jenjang_{{ $edu->id }}"
                                                name="jenjang" value="{{ old('jenjang', $edu->jenjang) }}"
                                                class="form-control @error('jenjang') is-invalid @enderror" required>
                                            @error('jenjang')
                                                <div class="text-danger mt-1">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="edit_institusi_{{ $edu->id }}"
                                                class="block font-semibold">Institusi</label>
                                            <input type="text" id="edit_institusi_{{ $edu->id }}"
                                                name="institusi" value="{{ old('institusi', $edu->institusi) }}"
                                                class="form-control @error('institusi') is-invalid @enderror"
                                                required>
                                            @error('institusi')
                                                <div class="text-danger mt-1">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="edit_program_studi_{{ $edu->id }}"
                                                class="block font-semibold">Program Studi</label>
                                            <input type="text" id="edit_program_studi_{{ $edu->id }}"
                                                name="program_studi"
                                                value="{{ old('program_studi', $edu->program_studi) }}"
                                                class="form-control @error('program_studi') is-invalid @enderror"
                                                required>
                                            @error('program_studi')
                                                <div class="text-danger mt-1">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="edit_tahun_masuk_{{ $edu->id }}"
                                                class="block font-semibold">Tahun Masuk</label>
                                            <input type="number" id="edit_tahun_masuk_{{ $edu->id }}"
                                                name="tahun_masuk"
                                                value="{{ old('tahun_masuk', $edu->tahun_masuk) }}"
                                                class="form-control @error('tahun_masuk') is-invalid @enderror"
                                                required>
                                            @error('tahun_masuk')
                                                <div class="text-danger mt-1">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="edit_tahun_lulus_{{ $edu->id }}"
                                                class="block font-semibold">Tahun Lulus</label>
                                            <input type="number" id="edit_tahun_lulus_{{ $edu->id }}"
                                                name="tahun_lulus"
                                                value="{{ old('tahun_lulus', $edu->tahun_lulus) }}"
                                                class="form-control @error('tahun_lulus') is-invalid @enderror"
                                                required>
                                            @error('tahun_lulus')
                                                <div class="text-danger mt-1">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="flex justify-end gap-2">
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                            <button type="button"
                                                onclick="document.getElementById('edit-edu-{{ $edu->id }}').classList.add('hidden')"
                                                class="btn btn-secondary">Batal</button>
                                        </div>
                                    </form>
                                </x-modal>

                                {{-- Error Handling Untuk Tetap Membuka Modal Edit Riwayat Pendidikan --}}
                                @if (session('modal') === 'edit-edu' && session('education_id'))
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            document.getElementById('edit-edu-{{ session('education_id') }}').classList.remove('hidden');
                                        });
                                    </script>
                                @endif
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
                    <div class="flex justify-between items-center mb-4">
                        <h5 class="font-semibold">Penghargaan</h5>
                        <button onclick="document.getElementById('modalAddAward').classList.remove('hidden')"
                            class="btn btn-primary">Tambah</button>
                    </div>

                    {{-- Modal Tambah --}}
                    <x-modal id="modalAddAward" title="Tambah Penghargaan">
                        <form method="POST" action="{{ route('penghargaan.store') }}" class="space-y-3">
                            @csrf
                            <div class="mb-4">
                                <label for="nama" class="block font-semibold">Nama Penghargaan</label>
                                <input type="text" name="nama" placeholder="Nama Penghargaan"
                                    value="{{ old('nama') }}"
                                    class="form-control @error('nama') is-invalid @enderror" required>
                                @error('nama')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="penyelenggara" class="block font-semibold">Penyelenggara</label>
                                <input type="text" name="penyelenggara" placeholder="Penyelenggara"
                                    value="{{ old('penyelenggara') }}"
                                    class="form-control @error('penyelenggara') is-invalid @enderror" required>
                                @error('penyelenggara')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="tahun" class="block font-semibold">Tahun</label>
                                <input type="number" name="tahun" placeholder="Tahun"
                                    value="{{ old('tahun') }}"
                                    class="form-control @error('tahun') is-invalid @enderror" required>
                                @error('tahun')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </x-modal>

                    {{-- Error Handling Untuk Tetap Membuka Modal Tambah Penghargaan --}}
                    @if (session('modal') === 'modalAddAward')
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                document.getElementById('modalAddAward').classList.remove('hidden');
                            });
                        </script>
                    @endif

                    {{-- List Penghargaan --}}
                    @if ($user->member->awards->count())
                        <ul class="space-y-2">
                            @foreach ($user->member->awards as $award)
                                <li class="flex justify-between items-center bg-white p-3 rounded">
                                    <div>
                                        <strong>{{ $award->nama }}</strong>
                                        @if ($award->penyelenggara)
                                            <span>oleh {{ $award->penyelenggara }}</span>
                                        @endif
                                        @if ($award->tahun)
                                            <br><small>{{ $award->tahun }}</small>
                                        @endif
                                    </div>
                                    <div class="flex gap-2">
                                        <button
                                            onclick="document.getElementById('edit-award-{{ $award->id }}').classList.remove('hidden')"
                                            class="btn btn-warning">Edit</button>
                                        <form method="POST" action="{{ route('penghargaan.destroy', $award->id) }}"
                                            onsubmit="return confirmHapus(this)">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Hapus</button>
                                        </form>
                                    </div>
                                </li>

                                {{-- Modal Edit Penghargaan --}}
                                <x-modal id="edit-award-{{ $award->id }}" title="Edit Penghargaan">
                                    <form method="POST" action="{{ route('penghargaan.update', $award->id) }}"
                                        class="grid grid-cols-1 gap-2">
                                        @csrf @method('PATCH')
                                        <div class="mb-4">
                                            <label for="nama" class="block font-semibold">Nama Penghargaan</label>
                                            <input type="text" name="nama"
                                                value="{{ old('nama', $award->nama) }}"
                                                class="form-control @error('nama') is-invalid @enderror" required>
                                            @error('nama')
                                                <div class="text-danger mt-1">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="penyelenggara"
                                                class="block font-semibold">Penyelenggara</label>
                                            <input type="text" name="penyelenggara"
                                                value="{{ old('penyelenggara', $award->penyelenggara) }}"
                                                class="form-control @error('penyelenggara') is-invalid @enderror">
                                            @error('penyelenggara')
                                                <div class="text-danger mt-1">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="tahun" class="block font-semibold">Tahun</label>
                                            <input type="number" name="tahun"
                                                value="{{ old('tahun', $award->tahun) }}"
                                                class="form-control @error('tahun') is-invalid @enderror">
                                            @error('tahun')
                                                <div class="text-danger mt-1">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="flex justify-end gap-2">
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            <button type="button"
                                                onclick="document.getElementById('edit-award-{{ $award->id }}').classList.add('hidden')"
                                                class="btn btn-secondary">Batal</button>
                                        </div>
                                    </form>
                                </x-modal>

                                {{-- Error Handling Untuk Tetap Membuka Modal Edit Penghargaan --}}
                                @if (session('modal') === 'edit-award' && session('award_id'))
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            document.getElementById('edit-award-{{ session('award_id') }}').classList.remove('hidden');
                                        });
                                    </script>
                                @endif
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500">Belum ada penghargaan yang ditambahkan.</p>
                    @endif
                </div>
            </div>

            {{-- Riwayat Mengajar --}}
            <div class="riwayat-mengajar">
                <div class="card" style="background-color: #EDF4EA; padding: 24px;">
                    <div class="flex justify-between items-center mb-4">
                        <h5 class="font-semibold">Riwayat Mengajar</h5>
                        <button onclick="document.getElementById('modalAddMengajar').classList.remove('hidden')"
                            class="btn btn-primary">Tambah</button>
                    </div>

                    {{-- Modal Tambah --}}
                    <x-modal id="modalAddMengajar" title="Tambah Riwayat Mengajar">
                        <form method="POST" action="{{ route('mengajar.store') }}" class="space-y-3">
                            @csrf
                            <div class="mb-4">
                                <label for="mata_kuliah" class="block font-semibold">Mata Kuliah</label>
                                <input type="text" name="mata_kuliah" placeholder="Mata Kuliah"
                                    value="{{ old('mata_kuliah') }}"
                                    class="form-control @error('mata_kuliah') is-invalid @enderror" required>
                                @error('mata_kuliah')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="institusi" class="block font-semibold">Institusi</label>
                                <input type="text" name="institusi" placeholder="Institusi"
                                    value="{{ old('institusi') }}"
                                    class="form-control @error('institusi') is-invalid @enderror" required>
                                @error('institusi')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="tahun_ajar" class="block font-semibold">Tahun Ajar</label>
                                <input type="text" name="tahun_ajar" placeholder="Tahun Ajar (contoh: 2023)"
                                    value="{{ old('tahun_ajar') }}"
                                    class="form-control @error('tahun_ajar') is-invalid @enderror" required>
                                @error('tahun_ajar')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </x-modal>

                    {{-- Error Handling Untuk Tetap Membuka Modal Tambah Riwayat Mengajar --}}
                    @if (session('modal') === 'modalAddMengajar')
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                document.getElementById('modalAddMengajar').classList.remove('hidden');
                            });
                        </script>
                    @endif

                    {{-- List Riwayat Mengajar --}}
                    @if ($user->member->teachingHistories->count())
                        <ul class="space-y-2">
                            @foreach ($user->member->teachingHistories as $teach)
                                <li class="flex justify-between items-center bg-white p-3 rounded">
                                    <div>
                                        <strong>{{ $teach->mata_kuliah }}</strong> – {{ $teach->institusi }}<br>
                                        <small class="text-gray-600">{{ $teach->tahun_ajar }}</small>
                                    </div>
                                    <div class="flex gap-2">
                                        <button
                                            onclick="document.getElementById('edit-mengajar-{{ $teach->id }}').classList.remove('hidden')"
                                            class="btn btn-warning">Edit</button>
                                        <form method="POST" action="{{ route('mengajar.destroy', $teach->id) }}"
                                            onsubmit="return confirmHapus(this)">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Hapus</button>
                                        </form>
                                    </div>
                                </li>

                                {{-- Modal Edit Mengajar --}}
                                <x-modal id="edit-mengajar-{{ $teach->id }}" title="Edit Riwayat Mengajar">
                                    <form method="POST" action="{{ route('mengajar.update', $teach->id) }}"
                                        class="grid grid-cols-1 gap-2">
                                        @csrf @method('PATCH')
                                        <div class="mb-4">
                                            <label for="mata_kuliah" class="block font-semibold">Mata Kuliah</label>
                                            <input type="text" name="mata_kuliah"
                                                value="{{ old('mata_kuliah', $teach->mata_kuliah) }}"
                                                class="form-control @error('mata_kuliah') is-invalid @enderror"
                                                required>
                                            @error('mata_kuliah')
                                                <div class="text-danger mt-1">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="institusi" class="block font-semibold">Institusi</label>
                                            <input type="text" name="institusi"
                                                value="{{ old('institusi', $teach->institusi) }}"
                                                class="form-control @error('institusi') is-invalid @enderror"
                                                required>
                                            @error('institusi')
                                                <div class="text-danger mt-1">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="tahun_ajar" class="block font-semibold">Tahun Ajar</label>
                                            <input type="text" name="tahun_ajar"
                                                value="{{ old('tahun_ajar', $teach->tahun_ajar) }}"
                                                class="form-control @error('tahun_ajar') is-invalid @enderror"
                                                required>
                                            @error('tahun_ajar')
                                                <div class="text-danger mt-1">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="flex justify-end gap-2">
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            <button type="button"
                                                onclick="document.getElementById('edit-mengajar-{{ $teach->id }}').classList.add('hidden')"
                                                class="btn btn-secondary">Batal</button>
                                        </div>
                                    </form>
                                </x-modal>

                                {{-- Error Handling Untuk Tetap Membuka Modal Edit Riwayat Mengajar --}}
                                @if (session('modal') === 'edit-mengajar' && session('teach_id'))
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            document.getElementById('edit-mengajar-{{ session('teach_id') }}').classList.remove('hidden');
                                        });
                                    </script>
                                @endif
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500 italic">Belum ada riwayat mengajar.</p>
                    @endif
                </div>
            </div>
        </x-layout.content-bar>
    </div>

    {{-- SweetAlert2 --}}
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

    {{-- SweetAlert2 untuk error --}}
    @if ($errors->any())
        <script>
            Swal.fire({
                title: 'Gagal!',
                text: 'Terjadi kesalahan saat menyimpan data. Silakan periksa kembali isian Anda.',
                icon: 'error',
                confirmButtonText: 'Oke'
            });
        </script>
    @endif
</body>

<script>
    function confirmHapus(form) {
        event.preventDefault();
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
        return false;
    }
</script>
