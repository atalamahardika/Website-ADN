<?php

namespace App\Http\Controllers;

use App\Models\SubDivision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class AdminController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $division = $user->division;

        // Hitung total sub divisi milik divisinya
        $totalSubDivisi = $division ? $division->subDivisions()->count() : 0;

        // Contoh: anggota masih statis dulu
        $totalAnggota = 123; // statis

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
            'profile_photo' => 'nullable|image|max:10240', // <= 10MB
        ]);

        $oldName = preg_replace('/\s+/', '-', strtolower($user->getOriginal('name')));
        $newName = preg_replace('/\s+/', '-', strtolower($validated['name']));
        $role = 'admin';

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

        return redirect()->route('admin.profile.edit')->with('status', 'Profil berhasil diperbarui.');
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
        return view('user.admin.anggota', [
            'title' => 'Anggota Divisi ',
            'subtitle' => 'Selamat datang di halaman anggota. Anda bisa mengelola informasi terkait anggota.',
            'user' => auth()->user()
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
