<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\SubDivision;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $division = $user->division;

        // Hitung total sub divisi milik divisinya
        $totalSubDivisi = $division ? $division->subDivisions()->count() : 0;

        // Hitung anggota divisi dari membership
        $totalAnggota = $division ? Membership::where('division_id', $division->id)->count() : 0;

        // Ambil semua sub divisi dari divisinya
        $subDivisions = $division ? $division->subDivisions : [];

        return view('user.admin.dashboard', [
            'title' => 'Dashboard ' . ucwords($user->role),
            'subtitle' => 'Halo ' . $user->name . ', selamat datang di Dashboard Aliansi Dosen Nahada (ADN).',
            'user' => $user,
            'totalSubDivisi' => $totalSubDivisi,
            'totalAnggota' => $totalAnggota,
            'subDivisions' => $subDivisions,
        ]);
    }

    public function profile(Request $request)
    {
        return view('user.admin.profil', [
            'title' => 'Profil Saya',
            'subtitle' => 'Kelola informasi pribadi Anda.',
            'user' => auth()->user()
        ]);
    }

    public function profileUpdate(Request $request)
    {
        $user = Auth::user();

        // Ambil data asli sebelum diisi dengan request
        $originalName = preg_replace('/\s+/', '-', strtolower($user->getOriginal('name')));
        // PERBAIKAN: Ambil role dari user yang login dan slug-kan
        $roleSlug = Str::slug($user->role); 

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'cropped_image' => 'nullable|string', // Pastikan ini ada jika menggunakan Cropper.js
        ]);

        $newName = preg_replace('/\s+/', '-', strtolower($validated['name']));

        // Tentukan direktori dasar di dalam storage/app/public
        // Menggunakan $roleSlug yang dinamis
        $baseDirectory = "images/profile-user/{$roleSlug}";
        $oldUserDirectory = "{$baseDirectory}/{$originalName}";
        $newUserDirectory = "{$baseDirectory}/{$newName}";

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        try {
            // Handle profile photo update (menggunakan cropped_image)
            if ($request->cropped_image) {
                // Hapus foto profil lama dari Storage jika ada
                if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                    Storage::disk('public')->delete($user->profile_photo);
                }

                // Inisialisasi ImageManager
                $imageManager = new ImageManager(new Driver());
                $image = $imageManager->read($request->cropped_image);

                // Ubah format menjadi JPEG dan kompresi (opsional)
                $encodedImage = $image->toJpeg(80); // Kualitas 80%

                // Buat nama file unik
                $filename = uniqid('avatar_') . '.jpg';
                $newPhotoPath = "{$newUserDirectory}/{$filename}"; // Path relatif ke storage/app/public

                // Simpan gambar ke Storage
                Storage::disk('public')->put($newPhotoPath, $encodedImage); // Langsung simpan bytes gambar

                // Update path di database
                $user->profile_photo = $newPhotoPath; // Simpan path relatif dari storage
            }
            // ELSE IF: Nama berubah tapi tidak ada upload foto baru
            elseif ($originalName !== $newName && $user->profile_photo) {
                // Periksa apakah folder lama dan file lama ada di Storage
                if (Storage::disk('public')->exists($user->profile_photo)) {
                    // Dapatkan nama file saja dari path lama
                    $oldFilename = basename($user->profile_photo);
                    $newPhotoPath = "{$newUserDirectory}/{$oldFilename}";

                    // Pindahkan file dari lokasi lama ke lokasi baru di Storage
                    Storage::disk('public')->move($user->profile_photo, $newPhotoPath);

                    // Update path di database
                    $user->profile_photo = $newPhotoPath;
                }
            }

            $user->save();

            // Opsional: Hapus folder lama jika nama berubah dan folder lama menjadi kosong
            if ($originalName !== $newName && Storage::disk('public')->exists($oldUserDirectory)) {
                $filesInOldDirectory = Storage::disk('public')->files($oldUserDirectory);
                if (empty($filesInOldDirectory)) {
                    Storage::disk('public')->deleteDirectory($oldUserDirectory);
                }
            }

            return redirect()->route('admin.profile.edit')->with('status', 'Profil berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui profil: ' . $e->getMessage());
        }
    }

    public function beranda(Request $request)
    {
        return view('user.admin.beranda-divisi', [
            'title' => 'Beranda Divisi ',
            'subtitle' => 'Selamat datang di halaman beranda divisi. Anda bisa mengelola informasi terkait divisi Anda.',
            'user' => auth()->user()
        ]);
    }

    public function updateBeranda(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
        ]);

        $user = auth()->user();

        if (!$user->division) {
            return redirect()->route('admin.beranda-divisi')->with('error', 'Divisi tidak ditemukan.');
        }

        $user->division->update([
            'description' => $request->description,
        ]);

        return redirect()->route('admin.beranda-divisi')->with('status', 'Deskripsi divisi berhasil diperbarui.');
    }

    public function subdivisi(Request $request)
    {
        $user = auth()->user();
        $subDivisions = $user->division
            ? SubDivision::where('division_id', $user->division->id)->get()
            : collect(); // empty jika tidak memegang divisi

        return view('user.admin.subdivisi', [
            'title' => 'Sub Divisi',
            'subtitle' => 'Selamat datang di halaman sub divisi. Anda bisa mengelola informasi terkait sub divisi Anda.',
            'user' => $user,
            'subDivisions' => $subDivisions,
        ]);
    }

    public function storeSubdivisi(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $user = auth()->user();

        if (!$user->division) {
            return back()->with('error', 'Anda belum memiliki divisi untuk menambahkan sub divisi.');
        }

        SubDivision::create([
            'division_id' => $user->division->id,
            'title' => $request->title,
            'description' => $request->description,
            'is_approved' => false,
        ]);

        return back()->with('status', 'Sub divisi berhasil ditambahkan. Menunggu persetujuan Super Admin.');
    }

    public function updateSubdivisi(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $sub = SubDivision::findOrFail($id);

        if ($sub->division_id != auth()->user()->division->id) {
            return back()->with('error', 'Anda tidak memiliki izin untuk mengubah sub divisi ini.');
        }

        // Cek apakah data diubah (title atau deskripsi)
        $isChanged = $sub->title !== $request->title || $sub->description !== $request->description;

        // Jika data diubah dan status sebelumnya belum disetujui, tetap is_approved false
        // Jika sudah disetujui, jangan ubah is_approved
        if ($isChanged) {
            $sub->title = $request->title;
            $sub->description = $request->description;

            // Ubah status approval hanya jika sebelumnya BELUM disetujui
            if (!$sub->is_approved) {
                $sub->is_approved = false;
                $message = 'Sub divisi berhasil diperbarui. Menunggu persetujuan Super Admin.';
            } else {
                $message = 'Sub divisi berhasil diperbarui.';
            }

            $sub->save();
        } else {
            $message = 'Tidak ada perubahan yang dilakukan.';
        }

        return back()->with('status', $message);
    }


    public function destroySubdivisi($id)
    {
        $sub = SubDivision::findOrFail($id);

        if ($sub->division_id != auth()->user()->division->id) {
            return back()->with('error', 'Anda tidak memiliki izin untuk menghapus sub divisi ini.');
        }

        $sub->delete();

        return back()->with('status', 'Sub divisi berhasil dihapus.');
    }


    public function anggota(Request $request)
    {
        $search = $request->get('search');
        // Eager load relasi 'division' pada user yang login.
        // Ini memastikan $user->division akan berisi objek Division atau null.
        $user = auth()->user()->load('division');

        // PERBAIKAN: Hanya cek apakah relasi division ada dan tidak null
        // Karena tidak ada kolom division_id langsung di tabel users
        if (!$user->division) { // Jika user (admin) tidak terhubung ke divisi manapun
            // Membuat instance Paginator kosong agar tampilan tidak error
            $memberships = new LengthAwarePaginator(
                [], // Item kosong
                0,  // Total count 0
                12, // Per page (sesuai pagination yang kamu gunakan)
                1,  // Current page
                ['path' => LengthAwarePaginator::resolveCurrentPath()] // Path untuk pagination links
            );

            return view('user.admin.anggota', [
                'title' => 'Anggota Divisi',
                'subtitle' => 'Daftar anggota dalam divisi Anda',
                'user' => $user, // Kirim objek user (sudah di-load relasinya)
                'memberships' => $memberships,
                'search' => $search
            ]);
        }

        // Dapatkan ID divisi dari admin yang sedang login melalui relasi 'division'
        $adminDivisionId = $user->division->id;

        // Inisialisasi query dengan eager loading yang diperlukan.
        // Relasi `member.user` dan `division` pada Membership diperlukan untuk tampilan card.
        $query = Membership::with(['member.user', 'division']);

        // Filter berdasarkan division_id yang dikelola oleh admin yang login.
        // PERBAIKAN UTAMA: Gunakan $adminDivisionId yang didapat dari relasi.
        $query->where('division_id', $adminDivisionId);

        // Jika ada pencarian, tambahkan kondisi search.
        if ($search) {
            $query->where(function ($q) use ($search) {
                // Search di tabel members (melalui relasi member)
                $q->whereHas('member', function ($memberQuery) use ($search) {
                    $memberQuery->where('email_institusi', 'LIKE', "%{$search}%")
                        ->orWhere('universitas', 'LIKE', "%{$search}%")
                        ->orWhere('fakultas', 'LIKE', "%{$search}%")
                        ->orWhere('prodi', 'LIKE', "%{$search}%")
                        ->orWhere('no_hp', 'LIKE', "%{$search}%")
                        ->orWhere('no_wa', 'LIKE', "%{$search}%")
                        // Grouping untuk kolom alamat
                        ->orWhere(function ($addressQuery) use ($search) {
                            $addressQuery->where('provinsi', 'LIKE', "%{$search}%")
                                ->orWhere('kabupaten', 'LIKE', "%{$search}%")
                                ->orWhere('kecamatan', 'LIKE', "%{$search}%")
                                ->orWhere('kelurahan', 'LIKE', "%{$search}%")
                                ->orWhere('alamat_jalan', 'LIKE', "%{$search}%"); // Tambahkan alamat_jalan
                        })
                        ->orWhere('tempat_lahir', 'LIKE', "%{$search}%")
                        ->orWhere('nik', 'LIKE', "%{$search}%");
                })
                    // Search di tabel users (melalui relasi member.user)
                    ->orWhereHas('member.user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Paginate hasil
        $memberships = $query->orderBy('created_at', 'desc')->paginate(12);

        return view('user.admin.anggota', [
            'title' => 'Anggota Divisi',
            'subtitle' => 'Daftar anggota dalam divisi Anda',
            'user' => $user,
            'memberships' => $memberships,
            'search' => $search
        ]);
    }

    public function showChangePassword()
    {
        return view('user.admin.ganti_password', [
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

        return redirect()->route('admin.ganti-password')->with('status', 'Password berhasil diperbarui.');
    }
}
