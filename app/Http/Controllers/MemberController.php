<?php

namespace App\Http\Controllers;

use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\ScientificField;
use App\Models\Skill;
use App\Models\EducationalHistory;
use App\Models\Award;
use App\Models\TeachingHistory;
use App\Models\PublicationMember;

class MemberController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = auth()->user();
        $publications = $user->member ? $user->member->publications()->latest()->paginate(5) : collect();
        return view('user.member.dashboard', [
            'title' => 'Dashboard ' . ucwords($user->role),
            'subtitle' => 'Halo ' . $user->name . ', selamat datang di Dashboard Aliansi Dosen Nahada (ADN).',
            'user' => $user,
            'scientificFields' => $user->member->scientificFields ?? collect(),
            'skills' => $user->member->skills ?? collect(),
            'educationalHistories' => $user->member->educationalHistories ?? collect(),
            'awards' => $user->member->awards ?? collect(),
            'teachingHistories' => $user->member->teachingHistories ?? collect(),
            'publications' => $publications ?? collect(),
        ]);
    }

    public function biografi(Request $request)
    {
        $user = auth()->user();
        return view('user.member.biografi', [
            'title' => 'Biografi Saya ',
            'subtitle' => 'Kelola biografi saya',
            'user' => $user
        ]);
    }

    public function biografiUpdate(Request $request)
    {
        $request->validate([
            'biografi' => 'nullable|string|max:2000',
        ]);

        $user = auth()->user();
        $user->member->update([
            'biografi' => $request->biografi
        ]);

        return redirect()->back()->with('status', 'Biografi berhasil diperbarui.');
    }

    public function storeKeilmuan(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = auth()->user();
        $user->member->scientificFields()->create([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('status', 'Bidang keilmuan berhasil ditambahkan.');
    }

    public function updateKeilmuan(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = auth()->user();
        $field = ScientificField::where('member_id', $user->member->id)->findOrFail($id);
        $field->update(['name' => $request->name]);

        return redirect()->route('biografi')->with([
            'status' => 'Bidang keilmuan berhasil diperbarui.',
        ]);

    }

    public function destroyKeilmuan($id)
    {
        $user = auth()->user();
        $field = ScientificField::where('member_id', $user->member->id)->findOrFail($id);
        $field->delete();

        return redirect()->back()->with('status', 'Bidang keilmuan berhasil dihapus.');
    }

    public function storeKeahlian(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = auth()->user();
        $user->member->skills()->create([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('status', 'Keahlian berhasil ditambahkan.');
    }

    public function updateKeahlian(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = auth()->user();
        $skill = Skill::where('member_id', $user->member->id)->findOrFail($id);
        $skill->update(['name' => $request->name]);

        return redirect()->route('biografi')->with([
            'status' => 'Keahlian berhasil diperbarui.',
            'edited_skill_id' => $id,
        ]);
    }

    public function destroyKeahlian($id)
    {
        $user = auth()->user();
        $skill = Skill::where('member_id', $user->member->id)->findOrFail($id);
        $skill->delete();

        return redirect()->back()->with('status', 'Keahlian berhasil dihapus.');
    }

    public function storePendidikan(Request $request)
    {
        $request->validate([
            'jenjang' => 'required|string|max:255',
            'institusi' => 'required|string|max:255',
            'program_studi' => 'required|string|max:255',
            'tahun_masuk' => 'required|digits:4',
            'tahun_lulus' => 'required|digits:4|gte:tahun_masuk',
        ]);

        $user = auth()->user();
        $user->member->educationalHistories()->create($request->all());

        return redirect()->back()->with('status', 'Riwayat pendidikan berhasil ditambahkan.');
    }

    public function updatePendidikan(Request $request, $id)
    {
        $request->validate([
            'jenjang' => 'required|string|max:255',
            'institusi' => 'required|string|max:255',
            'program_studi' => 'required|string|max:255',
            'tahun_masuk' => 'required|integer|min:1900|max:' . date('Y'),
            'tahun_lulus' => 'required|integer|min:1900|max:' . (date('Y') + 10),
        ]);

        $user = auth()->user();
        $edu = EducationalHistory::where('member_id', $user->member->id)->findOrFail($id);

        $edu->update($request->only([
            'jenjang',
            'institusi',
            'program_studi',
            'tahun_masuk',
            'tahun_lulus'
        ]));

        return redirect()->route('biografi')->with('status', 'Riwayat pendidikan berhasil diperbarui.');
    }

    public function destroyPendidikan($id)
    {
        $user = auth()->user();
        $pendidikan = EducationalHistory::where('member_id', $user->member->id)->findOrFail($id);
        $pendidikan->delete();

        return redirect()->back()->with('status', 'Riwayat pendidikan berhasil dihapus.');
    }

    public function storePenghargaan(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'penyelenggara' => 'nullable|string|max:255',
            'tahun' => 'nullable|numeric|min:1900|max:' . date('Y'),
        ]);

        $user = auth()->user();
        $user->member->awards()->create([
            'nama' => $request->nama,
            'penyelenggara' => $request->penyelenggara,
            'tahun' => $request->tahun,
        ]);

        return redirect()->back()->with('status', 'Penghargaan berhasil ditambahkan.');
    }

    public function updatePenghargaan(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'penyelenggara' => 'nullable|string|max:255',
            'tahun' => 'nullable|numeric|min:1900|max:' . date('Y'),
        ]);

        $user = auth()->user();
        $award = Award::where('member_id', $user->member->id)->findOrFail($id);
        $award->update($request->only('nama', 'penyelenggara', 'tahun'));

        return redirect()->route('biografi')->with('status', 'Penghargaan berhasil diperbarui.');
    }

    public function destroyPenghargaan($id)
    {
        $user = auth()->user();
        $award = Award::where('member_id', $user->member->id)->findOrFail($id);
        $award->delete();

        return redirect()->back()->with('status', 'Penghargaan berhasil dihapus.');
    }

    public function storeMengajar(Request $request)
    {
        $request->validate([
            'mata_kuliah' => 'required|string|max:255',
            'institusi' => 'required|string|max:255',
            'tahun_ajar' => ['required', 'digits:4', 'integer'],
        ], [
            'tahun_ajar.digits' => 'Tahun ajar harus terdiri dari 4 digit.',
            'tahun_ajar.integer' => 'Tahun ajar harus berupa angka.',
        ]);

        $user = auth()->user();
        $user->member->teachingHistories()->create($request->only(['mata_kuliah', 'institusi', 'tahun_ajar']));

        return redirect()->back()->with('status', 'Riwayat mengajar berhasil ditambahkan.');
    }

    public function updateMengajar(Request $request, $id)
    {
        $request->validate([
            'mata_kuliah' => 'required|string|max:255',
            'institusi' => 'required|string|max:255',
            'tahun_ajar' => ['required', 'digits:4', 'integer'],
        ], [
            'tahun_ajar.digits' => 'Tahun ajar harus terdiri dari 4 digit.',
            'tahun_ajar.integer' => 'Tahun ajar harus berupa angka.',
        ]);

        $user = auth()->user();
        $item = TeachingHistory::where('member_id', $user->member->id)->findOrFail($id);
        $item->update($request->only(['mata_kuliah', 'institusi', 'tahun_ajar']));

        return redirect()->route('biografi')->with('status', 'Riwayat mengajar berhasil diperbarui.');
    }

    public function destroyMengajar($id)
    {
        $user = auth()->user();
        $item = TeachingHistory::where('member_id', $user->member->id)->findOrFail($id);
        $item->delete();

        return redirect()->back()->with('status', 'Riwayat mengajar berhasil dihapus.');
    }

    public function publication(Request $request)
    {
        $user = auth()->user();
        $query = $user->member->publications();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhereJsonContains('authors', $search);
            });
        }

        return view('user.member.publikasi', [
            'title' => 'Publikasi Saya ',
            'subtitle' => 'Kelola publikasi saya',
            'user' => $user,
            'publications' => $query->latest()->paginate(5),
        ]);
    }

    private function formatAuthorsToHarvard(array $authors)
    {
        $formatted = [];

        foreach ($authors as $author) {
            // Split nama jadi array kata
            $parts = explode(' ', trim($author));

            if (count($parts) === 0)
                continue;

            // Ambil nama belakang (terakhir)
            $lastName = array_pop($parts);

            // Ambil inisial dari sisanya
            $initials = '';
            foreach ($parts as $part) {
                $initials .= strtoupper(mb_substr($part, 0, 1)) . '. ';
            }

            $formatted[] = "{$lastName}, " . trim($initials);
        }

        // Gabungkan semua dengan koma dan " & " terakhir
        $count = count($formatted);
        if ($count === 1) {
            return $formatted[0];
        } elseif ($count === 2) {
            return $formatted[0] . ' & ' . $formatted[1];
        } else {
            return implode(', ', array_slice($formatted, 0, -1)) . ' & ' . end($formatted);
        }
    }

    public function publicationStore(Request $request)
    {
        $request->validate([
            'authors' => 'required|string', // pisah dengan koma
            'title' => 'required|string',
            'year' => 'required|integer',
            'journal_name' => 'required|string',
            'volume' => 'nullable|string',
            'pages' => 'nullable|string',
            'link' => 'required|string',
        ]);

        $authorsArray = array_map('trim', explode(',', $request->authors));
        $formattedAuthors = $this->formatAuthorsToHarvard($authorsArray);

        auth()->user()->member->publications()->create([
            'authors' => $authorsArray,
            'formatted_authors' => $formattedAuthors,
            'title' => $request->title,
            'year' => $request->year,
            'journal_name' => $request->journal_name,
            'volume' => $request->volume,
            'pages' => $request->pages,
            'link' => $request->link,
        ]);

        return back()->with('status', 'Publikasi berhasil ditambahkan.');
    }

    public function publicationUpdate(Request $request, $id)
    {
        $request->validate([
            'authors' => 'required|string',
            'title' => 'required|string',
            'year' => 'required|integer',
            'journal_name' => 'required|string',
            'volume' => 'nullable|string',
            'pages' => 'nullable|string',
            'link' => 'nullable|url',
        ]);

        $authorsArray = array_map('trim', explode(',', $request->authors));
        $formattedAuthors = $this->formatAuthorsToHarvard($authorsArray);

        $publication = PublicationMember::where('member_id', auth()->user()->member->id)->findOrFail($id);
        $publication->update([
            'authors' => $authorsArray,
            'formatted_authors' => $formattedAuthors,
            'title' => $request->title,
            'year' => $request->year,
            'journal_name' => $request->journal_name,
            'volume' => $request->volume,
            'pages' => $request->pages,
            'link' => $request->link,
        ]);

        return back()->with('status', 'Publikasi berhasil diperbarui.');
    }

    public function publicationDestroy($id)
    {
        $publication = PublicationMember::where('member_id', auth()->user()->member->id)->findOrFail($id);
        $publication->delete();

        return back()->with('status', 'Publikasi berhasil dihapus.');
    }


    public function profile(Request $request)
    {
        return view('user.member.profil', [
            'title' => 'Profil Saya',
            'subtitle' => 'Kelola informasi pribadi Anda.',
            'user' => auth()->user()
        ]);
    }

    public function profileUpdate(Request $request)
    {
        $user = Auth::user();
        $member = $user->member;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'profile_photo' => 'nullable|image|max:10240', // <= 10MB
            'gelar_depan' => 'nullable|string|max:255',
            'gelar_belakang_1' => 'nullable|string|max:255',
            'gelar_belakang_2' => 'nullable|string|max:255',
            'gelar_belakang_3' => 'nullable|string|max:255',
            'nik' => [
                'required',
                'string',
                'max:16',
                'min:16',
                Rule::unique('members', 'nik')->ignore($member?->id),
            ],
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'no_hp' => 'nullable|string|max:20',
            'no_wa' => 'nullable|string|max:20',
            'alamat_jalan' => 'required|string',
            'provinsi' => 'nullable|string',
            'kabupaten' => 'nullable|string',
            'kecamatan' => 'nullable|string',
            'kelurahan' => 'nullable|string',
            'kode_pos' => 'nullable|string|max:10',
            'email_institusi' => 'nullable|email|max:255',
            'universitas' => 'required|string|max:255',
            'fakultas' => 'required|string|max:255',
            'prodi' => 'required|string|max:255',
        ]);

        $oldName = preg_replace('/\s+/', '-', strtolower($user->getOriginal('name')));
        $newName = preg_replace('/\s+/', '-', strtolower($validated['name']));
        $role = 'member';

        $oldPhotoPath = public_path($user->profile_photo);
        $oldFolder = public_path("images/profile-user/{$role}/{$oldName}");
        $newFolder = public_path("images/profile-user/{$role}/{$newName}");

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // CASE 1: Jika user mengirim foto hasil crop (base64)
        if ($request->cropped_image) {
            if ($user->profile_photo && file_exists($oldPhotoPath)) {
                unlink($oldPhotoPath);
            }

            $imageManager = new ImageManager(new Driver());
            $image = $imageManager->read($request->cropped_image)->toJpeg();

            $filename = uniqid('avatar_') . '.jpg';

            if (!file_exists($newFolder)) {
                mkdir($newFolder, 0755, true);
            }

            $image->save($newFolder . '/' . $filename);
            $user->profile_photo = "images/profile-user/{$role}/{$newName}/{$filename}";
        }

        // CASE 2: Nama berubah tapi tidak upload foto
        elseif ($oldName !== $newName && $user->profile_photo && file_exists($oldPhotoPath)) {
            if (!file_exists($newFolder)) {
                mkdir($newFolder, 0755, true);
            }

            $filename = basename($oldPhotoPath);
            $newPhotoPath = $newFolder . '/' . $filename;

            rename($oldPhotoPath, $newPhotoPath);
            $user->profile_photo = "images/profile-user/{$role}/{$newName}/{$filename}";
        }

        $user->save();

        // Hapus folder lama jika nama berubah dan folder lama ada
        if ($oldName !== $newName && is_dir($oldFolder)) {
            @rmdir($oldFolder); // pakai @ untuk suppress error jika folder tidak kosong
        }

        // Ambil nama wilayah berdasarkan ID
        $provinsi = DB::table('provinces')->where('prov_id', $request->provinsi)->value('prov_name');
        $kabupaten = DB::table('cities')->where('city_id', $request->kabupaten)->value('city_name');
        $kecamatan = DB::table('districts')->where('dis_id', $request->kecamatan)->value('dis_name');
        $kelurahan = DB::table('subdistricts')->where('subdis_id', $request->kelurahan)->value('subdis_name');

        $user->member()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'gelar_depan' => $validated['gelar_depan'] ?? null,
                'gelar_belakang_1' => $validated['gelar_belakang_1'] ?? null,
                'gelar_belakang_2' => $validated['gelar_belakang_2'] ?? null,
                'gelar_belakang_3' => $validated['gelar_belakang_3'] ?? null,
                'nik' => $validated['nik'],
                'tempat_lahir' => $validated['tempat_lahir'] ?? null,
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                'no_hp' => $validated['no_hp'] ?? null,
                'no_wa' => $validated['no_wa'] ?? null,
                'alamat_jalan' => $validated['alamat_jalan'] ?? null,
                'provinsi' => $provinsi,
                'kabupaten' => $kabupaten,
                'kecamatan' => $kecamatan,
                'kelurahan' => $kelurahan,
                'kode_pos' => $request->kode_pos,
                'email_institusi' => $validated['email_institusi'] ?? null,
                'universitas' => $validated['universitas'],
                'fakultas' => $validated['fakultas'],
                'prodi' => $validated['prodi'],
            ]
        );

        return redirect()->route('profile.edit')->with('status', 'Profil berhasil diperbarui.');
    }

    public function showChangePassword()
    {
        return view('user.member.ganti_password', [
            'title' => 'Ganti Password ',
            'subtitle' => 'Perbarui password akun Anda',
            'user' => auth()->user(),
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('ganti-password')->with('status', 'Password berhasil diperbarui.');
    }

    // Method yang sudah ada
    public function showKeanggotaan()
    {
        $user = auth()->user();
        $member = $user->member;
        $membership = $member ? $member->membership : null;
        $paymentSetting = PaymentSetting::getActiveSetting();

        // Get pending payment if exists
        $pendingPayment = null;
        if ($membership) {
            $pendingPayment = $membership->getPendingPayment();
        }

        // Get payment history
        $paymentHistory = [];
        if ($membership) {
            $paymentHistory = $membership->getApprovedPayments();
        }

        // Get admin notes from membership_payment for rejected status
        // $rejectedPaymentNotes = null;
        // if ($membership && $membership->payments()->where('status', 'rejected')->exists()) {
        //     $rejectedPayment = $membership->payments()
        //         ->where('status', 'rejected')
        //         ->latest()
        //         ->first();
        //     $rejectedPaymentNotes = $rejectedPayment ? $rejectedPayment->admin_notes : null;
        // }

        // Variabel untuk menyimpan catatan admin dari pembayaran yang ditolak
        $rejectedPaymentNotes = null;
        // Variabel untuk menandai apakah pembayaran terakhir yang ditolak adalah tipe 'renewal'
        $isLastRejectedPaymentRenewal = false;

        // Mengecek apakah ada membership dan statusnya ditolak
        if ($membership && $membership->status === 'rejected') {
            // Mencari pembayaran terakhir yang ditolak untuk mendapatkan catatan dan tipe pembayaran
            $rejectedPayment = $membership->payments()
                ->where('status', 'rejected')
                ->latest() // Ambil yang terbaru
                ->first();

            if ($rejectedPayment) {
                $rejectedPaymentNotes = $rejectedPayment->admin_notes;
                // Jika tipe pembayaran yang ditolak adalah 'renewal', set flag
                if ($rejectedPayment->payment_type === 'renewal') {
                    $isLastRejectedPaymentRenewal = true;
                }
            }
        }

        return view('user.member.keanggotaan', [
            'title' => 'Keanggotaan Saya',
            'subtitle' => 'Kelola keanggotaan saya',
            'user' => $user,
            'member' => $member,
            'membership' => $membership,
            'paymentSetting' => $paymentSetting,
            'pendingPayment' => $pendingPayment,
            'paymentHistory' => $paymentHistory,
            'rejectedPaymentNotes' => $rejectedPaymentNotes,
            'isLastRejectedPaymentRenewal' => $isLastRejectedPaymentRenewal,
        ]);
    }

    // Method untuk proses pendaftaran membership - UPDATED
    public function registerMembership(Request $request)
    {
        $user = auth()->user();
        $member = $user->member;

        // Validasi apakah member sudah ada
        if (!$member) {
            return redirect()->back()->with('error', 'Harap lengkapi profil terlebih dahulu.');
        }

        // PERUBAHAN LOGIKA: Cek apakah sudah ada membership yang pending
        // Hanya tolak jika statusnya pending, bukan rejected
        if ($member->membership && $member->membership->status === 'pending') {
            return redirect()->back()->with('error', 'Anda sudah memiliki pendaftaran yang sedang menunggu konfirmasi.');
        }

        // Validasi form pendaftaran
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'gelar_depan' => 'nullable|string|max:255',
            'gelar_belakang_1' => 'nullable|string|max:255',
            'gelar_belakang_2' => 'nullable|string|max:255',
            'gelar_belakang_3' => 'nullable|string|max:255',
            'nik' => [
                'required',
                'string',
                'max:16',
                'min:16',
                Rule::unique('members', 'nik')->ignore($member->id),
            ],
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'no_hp' => 'nullable|string|max:20',
            'no_wa' => 'nullable|string|max:20',
            'alamat_jalan' => 'required|string',
            'provinsi' => 'nullable|string',
            'kabupaten' => 'nullable|string',
            'kecamatan' => 'nullable|string',
            'kelurahan' => 'nullable|string',
            'kode_pos' => 'nullable|string|max:10',
            'email_institusi' => 'nullable|email|max:255',
            'universitas' => 'required|string|max:255',
            'fakultas' => 'required|string|max:255',
            'prodi' => 'required|string|max:255',
            'payment_proof_link' => 'required|url'
        ]);

        // Ambil nama wilayah berdasarkan ID
        $provinsi = DB::table('provinces')->where('prov_id', $request->provinsi)->value('prov_name');
        $kabupaten = DB::table('cities')->where('city_id', $request->kabupaten)->value('city_name');
        $kecamatan = DB::table('districts')->where('dis_id', $request->kecamatan)->value('dis_name');
        $kelurahan = DB::table('subdistricts')->where('subdis_id', $request->kelurahan)->value('subdis_name');

        try {
            \DB::beginTransaction();

            // Update data user
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email']
            ]);

            // Update data member
            $member->update([
                'gelar_depan' => $validated['gelar_depan'],
                'gelar_belakang_1' => $validated['gelar_belakang_1'],
                'gelar_belakang_2' => $validated['gelar_belakang_2'],
                'gelar_belakang_3' => $validated['gelar_belakang_3'],
                'nik' => $validated['nik'],
                'tempat_lahir' => $validated['tempat_lahir'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'no_hp' => $validated['no_hp'],
                'no_wa' => $validated['no_wa'],
                'alamat_jalan' => $validated['alamat_jalan'],
                'provinsi' => $provinsi,
                'kabupaten' => $kabupaten,
                'kecamatan' => $kecamatan,
                'kelurahan' => $kelurahan,
                'kode_pos' => $validated['kode_pos'],
                'email_institusi' => $validated['email_institusi'],
                'universitas' => $validated['universitas'],
                'fakultas' => $validated['fakultas'],
                'prodi' => $validated['prodi']
            ]);

            // PERUBAHAN LOGIKA MEMBERSHIP
            if (!$member->membership) {
                // Jika belum ada membership, buat baru
                $membership = $member->createMembership();
            } else {
                // Jika sudah ada membership (termasuk yang rejected), 
                // overwrite status menjadi pending
                $membership = $member->membership;
                $membership->status = 'pending';
                $membership->save();
            }

            // Ambil payment setting yang aktif
            $paymentSetting = PaymentSetting::getActiveSetting();
            if (!$paymentSetting) {
                throw new \Exception('Pengaturan pembayaran tidak ditemukan. Silakan hubungi administrator.');
            }

            // Tentukan tipe pembayaran
            $paymentType = $membership->payments()->approved()->exists() ? 'renewal' : 'new_registration';

            // SELALU BUAT RECORD PEMBAYARAN BARU (tidak overwrite)
            // Baik untuk membership baru maupun yang sudah ada (termasuk rejected)
            $membership->payments()->create([
                'payment_type' => $paymentType,
                'amount' => $paymentSetting->payment_amount,
                'bank_name' => $paymentSetting->bank_name,
                'account_number' => $paymentSetting->account_number,
                'account_holder' => $paymentSetting->account_holder,
                'payment_proof_link' => $validated['payment_proof_link'],
                'status' => 'pending'
            ]);

            \DB::commit();

            return redirect()->back()->with('success', 'Pendaftaran membership berhasil dikirim. Menunggu konfirmasi dari Super Admin.');

        } catch (\Exception $e) {
            \DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Method untuk renewal membership - UPDATED
    public function renewMembership(Request $request)
    {
        $user = auth()->user();
        $member = $user->member;
        $membership = $member->membership;

        // PERUBAHAN LOGIKA: Allow renewal jika status inactive ATAU rejected
        if (!$membership || !in_array($membership->status, ['inactive', 'rejected'])) {
            return redirect()->back()->with('error', 'Membership tidak dapat diperpanjang saat ini.');
        }

        // Cek apakah ada payment yang pending
        if ($membership->getPendingPayment()) {
            return redirect()->back()->with('error', 'Masih ada pembayaran yang menunggu konfirmasi.');
        }

        $validated = $request->validate([
            'payment_proof_link' => 'required|url'
        ]);

        try {
            \DB::beginTransaction();

            $paymentSetting = PaymentSetting::getActiveSetting();
            if (!$paymentSetting) {
                throw new \Exception('Pengaturan pembayaran tidak ditemukan.');
            }

            // Update status membership ke pending (overwrite)
            $membership->status = 'pending';
            $membership->save();

            // Buat record pembayaran renewal BARU (tidak overwrite)
            $membership->payments()->create([
                'payment_type' => 'renewal',
                'amount' => $paymentSetting->payment_amount,
                'bank_name' => $paymentSetting->bank_name,
                'account_number' => $paymentSetting->account_number,
                'account_holder' => $paymentSetting->account_holder,
                'payment_proof_link' => $validated['payment_proof_link'],
                'status' => 'pending'
            ]);

            \DB::commit();

            return redirect()->back()->with('success', 'Perpanjangan membership berhasil dikirim. Menunggu konfirmasi dari Super Admin.');

        } catch (\Exception $e) {
            \DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Method baru untuk handle re-registration setelah rejection
    public function reRegisterMembership(Request $request)
    {
        $user = auth()->user();
        $member = $user->member;

        // Validasi apakah member sudah ada
        if (!$member) {
            return redirect()->back()->with('error', 'Harap lengkapi profil terlebih dahulu.');
        }

        // Hanya boleh re-register jika status membership rejected
        if (!$member->membership || $member->membership->status !== 'rejected') {
            return redirect()->back()->with('error', 'Tidak dapat mendaftar ulang. Status membership tidak valid.');
        }

        // Cek apakah pembayaran terakhir yang ditolak adalah 'new_registration'
        $lastRejectedPayment = $member->membership->payments()->where('status', 'rejected')->latest()->first();
        if (!$lastRejectedPayment || $lastRejectedPayment->payment_type !== 'new_registration') {
            return redirect()->back()->with('error', 'Anda tidak dalam skenario pendaftaran ulang yang ditolak.');
        }

        // Validasi form (sama dengan registerMembership)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'gelar_depan' => 'nullable|string|max:255',
            'gelar_belakang_1' => 'nullable|string|max:255',
            'gelar_belakang_2' => 'nullable|string|max:255',
            'gelar_belakang_3' => 'nullable|string|max:255',
            'nik' => [
                'required',
                'string',
                'max:16',
                'min:16',
                Rule::unique('members', 'nik')->ignore($member->id),
            ],
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'no_hp' => 'nullable|string|max:20',
            'no_wa' => 'nullable|string|max:20',
            'alamat_jalan' => 'required|string',
            'provinsi' => 'nullable|string',
            'kabupaten' => 'nullable|string',
            'kecamatan' => 'nullable|string',
            'kelurahan' => 'nullable|string',
            'kode_pos' => 'nullable|string|max:10',
            'email_institusi' => 'nullable|email|max:255',
            'universitas' => 'required|string|max:255',
            'fakultas' => 'required|string|max:255',
            'prodi' => 'required|string|max:255',
            'payment_proof_link' => 'required|url'
        ]);

        // Ambil nama wilayah berdasarkan ID
        $provinsi = DB::table('provinces')->where('prov_id', $request->provinsi)->value('prov_name');
        $kabupaten = DB::table('cities')->where('city_id', $request->kabupaten)->value('city_name');
        $kecamatan = DB::table('districts')->where('dis_id', $request->kecamatan)->value('dis_name');
        $kelurahan = DB::table('subdistricts')->where('subdis_id', $request->kelurahan)->value('subdis_name');

        try {
            \DB::beginTransaction();

            // Update data user
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email']
            ]);

            // Update data member
            $member->update([
                'gelar_depan' => $validated['gelar_depan'],
                'gelar_belakang_1' => $validated['gelar_belakang_1'],
                'gelar_belakang_2' => $validated['gelar_belakang_2'],
                'gelar_belakang_3' => $validated['gelar_belakang_3'],
                'nik' => $validated['nik'],
                'tempat_lahir' => $validated['tempat_lahir'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'no_hp' => $validated['no_hp'],
                'no_wa' => $validated['no_wa'],
                'alamat_jalan' => $validated['alamat_jalan'],
                'provinsi' => $provinsi,
                'kabupaten' => $kabupaten,
                'kecamatan' => $kecamatan,
                'kelurahan' => $kelurahan,
                'kode_pos' => $validated['kode_pos'],
                'email_institusi' => $validated['email_institusi'],
                'universitas' => $validated['universitas'],
                'fakultas' => $validated['fakultas'],
                'prodi' => $validated['prodi']
            ]);

            // Overwrite status membership dari rejected ke pending
            $membership = $member->membership;
            $membership->status = 'pending';
            $membership->save();

            // Ambil payment setting yang aktif
            $paymentSetting = PaymentSetting::getActiveSetting();
            if (!$paymentSetting) {
                throw new \Exception('Pengaturan pembayaran tidak ditemukan. Silakan hubungi administrator.');
            }

            // Tentukan tipe pembayaran
            $paymentType = $membership->payments()->approved()->exists() ? 'renewal' : 'new_registration';

            // Buat record pembayaran BARU (tidak overwrite yang rejected)
            $membership->payments()->create([
                'payment_type' => $paymentType,
                'amount' => $paymentSetting->payment_amount,
                'bank_name' => $paymentSetting->bank_name,
                'account_number' => $paymentSetting->account_number,
                'account_holder' => $paymentSetting->account_holder,
                'payment_proof_link' => $validated['payment_proof_link'],
                'status' => 'pending'
            ]);

            \DB::commit();

            return redirect()->back()->with('success', 'Pendaftaran ulang membership berhasil dikirim. Menunggu konfirmasi dari Super Admin.');

        } catch (\Exception $e) {
            \DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reRenewMembership(Request $request)
    {
        $user = auth()->user();
        $member = $user->member;
        $membership = $member->membership;

        // Hanya boleh re-renew jika member ada dan status membership rejected
        if (!$member || !$membership || $membership->status !== 'rejected') {
            return redirect()->back()->with('error', 'Tidak dapat melakukan perpanjangan ulang. Status membership tidak valid.');
        }

        // Cek apakah pembayaran terakhir yang ditolak adalah 'renewal'
        $lastRejectedPayment = $membership->payments()->where('status', 'rejected')->latest()->first();
        if (!$lastRejectedPayment || $lastRejectedPayment->payment_type !== 'renewal') {
            return redirect()->back()->with('error', 'Anda tidak dalam skenario perpanjangan ulang yang ditolak.');
        }

        // Cek apakah ada pembayaran lain yang sedang pending
        if ($membership->getPendingPayment()) {
            return redirect()->back()->with('error', 'Masih ada pembayaran yang menunggu konfirmasi untuk membership ini.');
        }

        // Validasi hanya untuk link bukti pembayaran
        $validated = $request->validate([
            'payment_proof_link' => 'required|url'
        ]);

        try {
            DB::beginTransaction();

            $paymentSetting = PaymentSetting::getActiveSetting();
            if (!$paymentSetting) {
                throw new \Exception('Pengaturan pembayaran tidak ditemukan.');
            }

            // Update status membership ke pending (mengindikasikan pengajuan perpanjangan ulang baru)
            $membership->status = 'pending';
            $membership->save();

            // Buat record pembayaran renewal BARU
            $membership->payments()->create([
                'payment_type' => 'renewal', // Pastikan tipe ini 'renewal'
                'amount' => $paymentSetting->payment_amount,
                'bank_name' => $paymentSetting->bank_name,
                'account_number' => $paymentSetting->account_number,
                'account_holder' => $paymentSetting->account_holder,
                'payment_proof_link' => $validated['payment_proof_link'],
                'status' => 'pending'
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Perpanjangan ulang membership berhasil dikirim. Menunggu konfirmasi dari Super Admin.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
