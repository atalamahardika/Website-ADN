<?php

namespace App\Http\Controllers;

use App\Models\CmsLandingSection;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipPayment;
use App\Models\PaymentSetting;
use App\Models\PublicationMember;
use App\Models\PublicationOrganization;
use App\Models\SubDivision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Division;
use App\Models\User;
use App\Models\News;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Models\TriDharma;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MembershipExport; // Nanti akan kita buat


class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();

        // Ambil jumlah data
        $totalMembers = User::where('role', 'member')->count();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalPublikasiMember = PublicationMember::count();
        $totalPublikasiADN = PublicationOrganization::count();
        $totalDivisi = Division::count();
        $totalBerita = News::count();
        $membershipActive = Membership::where('status', 'active')->count();
        $membershipInactive = Membership::where('status', 'inactive')->count();
        $membershipPending = Membership::where('status', 'pending')->count();

        return view('user.super-admin.dashboard', [
            'title' => 'Dashboard ' . ucwords($user->role),
            'subtitle' => 'Halo ' . $user->name . ', selamat datang di Dashboard Aliansi Dosen Nahada (ADN).',
            'user' => $user,
            'totalMembers' => $totalMembers,
            'totalAdmins' => $totalAdmins,
            'totalPublikasiMember' => $totalPublikasiMember,
            'totalPublikasiADN' => $totalPublikasiADN,
            'totalDivisi' => $totalDivisi,
            'totalBerita' => $totalBerita,
            'membershipActive' => $membershipActive,
            'membershipInactive' => $membershipInactive,
            'membershipPending' => $membershipPending,
        ]);
    }

    public function divisionIndex()
    {
        $divisions = Division::with('admin')->get();

        // Semua admin yang belum ditugaskan (untuk TAMBAH divisi)
        $availableAdminsForAdd = User::where('role', 'admin')
            ->whereDoesntHave('division')
            ->get();

        // Siapkan data admin yang tersedia untuk masing-masing divisi saat EDIT
        $availableAdminsForEdit = [];

        foreach ($divisions as $division) {
            $admins = User::where('role', 'admin')
                ->where(function ($query) use ($division) {
                    $query->whereDoesntHave('division')
                        ->orWhere('id', $division->admin_id); // tetap munculkan admin yang sudah assigned ke divisi ini
                })
                ->get();

            $availableAdminsForEdit[$division->id] = $admins;
        }

        return view('user.super-admin.divisi', [
            'title' => 'Divisi ',
            'subtitle' => 'Kelola data divisi',
            'divisions' => $divisions,
            'availableAdminsForAdd' => $availableAdminsForAdd,
            'availableAdminsForEdit' => $availableAdminsForEdit,
        ]);
    }

    public function divisionStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'region' => 'required|string',
            'description' => 'required|string',
            'admin_id' => 'nullable|exists:users,id',
        ]);

        if ($request->admin_id) {
            $isAssigned = Division::where('admin_id', $request->admin_id)->exists();
            if ($isAssigned) {
                return back()->withErrors(['admin_id' => 'Admin ini sudah ditugaskan ke divisi lain.'])->withInput();
            }
        }

        Division::create($request->only('title', 'region', 'description', 'admin_id'));

        return redirect()->back()->with('status', 'Divisi berhasil ditambahkan.');
    }

    public function divisionUpdate(Request $request, Division $division)
    {
        $request->validate([
            'title' => 'required|string',
            'region' => 'required|string',
            'description' => 'required|string',
            'admin_id' => 'nullable|exists:users,id',
        ]);

        if ($request->admin_id && $request->admin_id != $division->admin_id) {
            $isAssigned = Division::where('admin_id', $request->admin_id)->exists();
            if ($isAssigned) {
                return back()->withErrors(['admin_id' => 'Admin ini sudah ditugaskan ke divisi lain.'])->withInput();
            }
        }

        $division->update($request->only('title', 'region', 'description', 'admin_id'));

        return redirect()->back()->with('status', 'Divisi berhasil diperbarui.');
    }

    public function divisionDestroy($id)
    {
        Division::findOrFail($id)->delete();
        return back()->with('status', 'Divisi berhasil dihapus.');
    }

    public function showDivisionDetail($id)
    {
        $division = Division::with('subDivisions', 'admin')->findOrFail($id);
        return view('user.super-admin.detail-divisi', [
            'title' => 'Detail Divisi ',
            'subtitle' => $division->title,
            'division' => $division,
        ]);
    }

    // Simpan Sub Divisi
    public function storeSubDivision(Request $request)
    {
        $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        SubDivision::create([
            'division_id' => $request->division_id,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return back()->with('status', 'Sub Divisi berhasil ditambahkan');
    }

    // Update Sub Divisi
    public function updateSubDivision(Request $request, $id)
    {
        $sub = SubDivision::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_approved' => 'nullable|boolean',
        ]);

        $sub->update($request->only('title', 'description', 'is_approved'));

        return back()->with('status', 'Sub Divisi berhasil diperbarui');
    }

    // Hapus Sub Divisi
    public function deleteSubDivision($id)
    {
        $sub = SubDivision::findOrFail($id);
        $sub->delete();

        return back()->with('status', 'Sub Divisi berhasil dihapus');
    }

    public function toggleSubDivisionApproval($id)
    {
        $sub = SubDivision::findOrFail($id);
        $sub->is_approved = !$sub->is_approved;
        $sub->save();

        return back()->with('status', 'Status persetujuan berhasil diperbarui.');
    }

    public function adminIndex()
    {
        $admins = User::where('role', 'admin')->get();

        return view('user.super-admin.admin', [
            'title' => 'Admin ',
            'subtitle' => 'Kelola akun admin divisi',
            'admins' => $admins,
        ]);
    }

    public function adminStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'profile_photo' => 'nullable|image|max:10240'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => 'admin',
            'password' => bcrypt($request->password),
            'profile_photo' => 'images/profile-user/template_photo_profile.png',
        ]);

        return back()->with('status', 'Admin berhasil ditambahkan.');
    }

    public function adminDestroy($id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);
        $admin->delete();

        return back()->with('status', 'Admin berhasil dihapus.');
    }

    // Tampilkan daftar berita
    public function beritaIndex(Request $request)
    {
        $query = News::query();

        if ($request->has('search') && $request->search !== null) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $news = $query->latest()->paginate(10);

        // Jika ada pencarian dan hasilnya kosong
        if ($request->has('search') && $news->isEmpty()) {
            return redirect()->route('superadmin.berita')->with([
                'status_warning' => 'Hasil pencarian judul berita tidak ditemukan untuk: ' . $request->search
            ]);
        }

        return view('user.super-admin.berita', [
            'title' => 'Berita ',
            'subtitle' => 'Kelola berita organisasi',
            'news' => $news
        ]);
    }

    public function detailBerita($slug)
    {
        $news = News::where('slug', $slug)->firstOrFail();

        Carbon::setLocale('id');

        return view('user.super-admin.detail-berita', [
            'title' => 'Detail Berita ',
            'subtitle' => 'Lihat informasi lengkap berita',
            'news' => $news
        ]);
    }

    public function beritaStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'source_link' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'cropped_image' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('modal', 'modalTambahBerita');
        }

        try {
            $slug = Str::slug($request->title);
            $originalSlug = $slug;
            $counter = 1;
            while (News::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }

            $imagePath = null;
            $croppedImage = $request->cropped_image;

            if (preg_match('/^data:image\/(\w+);base64,/', $croppedImage, $type)) {
                $data = substr($croppedImage, strpos($croppedImage, ',') + 1);
                $imageType = strtolower($type[1]);

                $data = base64_decode($data);

                if ($data === false) {
                    return back()->withErrors(['image' => 'Gagal memproses gambar base64.']);
                }

                $fileName = $slug . '_' . time() . '.' . $imageType;
                $directory = 'images/news';
                $imagePath = $directory . '/' . $fileName;

                Storage::disk('public')->put($imagePath, $data);

            } else {
                return back()->withErrors(['image' => 'Format gambar base64 tidak valid.']);
            }

            News::create([
                'title' => $request->title,
                'slug' => $slug,
                'content' => $request->content,
                'source_link' => $request->source_link,
                'image' => $imagePath,
            ]);

            return redirect()->back()->with('status', 'Berita berhasil ditambahkan.');

        } catch (\Exception $e) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan berita: ' . $e->getMessage());
        }
    }


    public function beritaUpdate(Request $request, $slug)
    {
        // Ambil berita sebelum perubahan slug
        $news = News::where('slug', $slug)->firstOrFail();
        $oldImagePath = $news->image; // Simpan path gambar lama untuk potensi rename/delete

        $validator = Validator::make($request->all(), [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('news', 'slug')->ignore($news->id),
            ],
            'content' => 'required|string',
            'source_link' => 'nullable|string|max:255',
            'cropped_image' => 'nullable|string', // base64 string
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('modal', 'modalEditBerita');
        }

        try {
            $validatedData = $validator->validated();

            // Simpan slug lama untuk perbandingan nanti
            $oldSlug = $news->slug;

            // Handle slug update: buat slug baru, cek keunikan jika berubah
            $newSlug = Str::slug($validatedData['title']);
            if ($newSlug !== $oldSlug) { // Gunakan $oldSlug untuk perbandingan
                $originalNewSlug = $newSlug;
                $counter = 1;
                while (News::where('slug', $newSlug)->where('id', '!=', $news->id)->exists()) {
                    $newSlug = $originalNewSlug . '-' . $counter++;
                }
                $news->slug = $newSlug; // Update slug di model
            }

            // Simpan perubahan judul, konten, dan sumber
            $news->title = $validatedData['title'];
            $news->content = $validatedData['content'];
            $news->source_link = $validatedData['source_link'];

            $imageHasBeenUpdated = false;
            // Tangani gambar baru jika diunggah (base64)
            if (isset($validatedData['cropped_image']) && $validatedData['cropped_image']) {
                $croppedImage = $validatedData['cropped_image'];

                if (preg_match('/^data:image\/(\w+);base64,/', $croppedImage, $type)) {
                    $data = substr($croppedImage, strpos($croppedImage, ',') + 1);
                    $imageType = strtolower($type[1]);

                    $data = base64_decode($data);
                    if ($data === false) {
                        return back()->withErrors(['cropped_image' => 'Gagal memproses gambar base64 yang diperbarui.']);
                    }

                    // Hapus gambar lama dari Storage jika ada
                    if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                        Storage::disk('public')->delete($oldImagePath);
                    }

                    $directory = 'images/news';
                    // Konsisten dengan penamaan: slug_timestamp.type
                    // Gunakan $newSlug yang mungkin sudah diupdate
                    $fileName = $newSlug . '_' . time() . '.' . $imageType;
                    $imagePath = $directory . '/' . $fileName;

                    Storage::disk('public')->put($imagePath, $data);
                    $news->image = $imagePath; // Update path gambar di database
                    $imageHasBeenUpdated = true; // Tandai bahwa gambar telah diupdate
                } else {
                    return back()->withErrors(['cropped_image' => 'Format gambar base64 yang diperbarui tidak valid.']);
                }
            }

            // Jika tidak ada gambar baru yang diunggah, tapi slug berubah, kita perlu me-rename file gambar lama
            if (!$imageHasBeenUpdated && $newSlug !== $oldSlug && $news->image) {
                // Ekstrak nama file dari path lama
                $oldFileName = pathinfo($oldImagePath, PATHINFO_BASENAME);
                // Ekstrak ekstensi dari nama file lama
                $oldExtension = pathinfo($oldFileName, PATHINFO_EXTENSION);

                // Buat nama file baru dengan slug yang baru dan timestamp lama (atau timestamp baru juga bisa)
                // Konsisten dengan format: new_slug_timestamp.ext
                // Kita ambil timestamp dari nama file lama jika ada, atau gunakan yang baru jika tidak ada format yang sama
                $timestampMatch = [];
                preg_match('/_(\d+)\./', $oldFileName, $timestampMatch);
                $timestamp = isset($timestampMatch[1]) ? $timestampMatch[1] : time();

                $directory = 'images/news';
                $newFileName = $newSlug . '_' . $timestamp . '.' . $oldExtension;
                $newImagePath = $directory . '/' . $newFileName;

                // Pastikan file lama ada dan coba rename
                if (Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->move($oldImagePath, $newImagePath);
                    $news->image = $newImagePath; // Update path gambar di database
                }
            }

            $news->save();

            return redirect()->route('superadmin.berita.detail', $news->slug)
                ->with('status', 'Berita berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui berita: ' . $e->getMessage());
        }
    }

    public function deleteBerita($id)
    {
        $news = News::findOrFail($id);

        try {
            if ($news->image && Storage::disk('public')->exists($news->image)) {
                Storage::disk('public')->delete($news->image);
            }

            $news->delete();

            return redirect()->route('superadmin.berita')->with('status', 'Berita berhasil dihapus.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus berita: ' . $e->getMessage());
        }
    }

    public function member(Request $request)
    {
        $search = $request->get('search');

        $members = Member::with('user')
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                    ->orWhere('gelar_depan', 'like', "%{$search}%")
                    ->orWhere('gelar_belakang_1', 'like', "%{$search}%")
                    ->orWhere('gelar_belakang_2', 'like', "%{$search}%")
                    ->orWhere('gelar_belakang_3', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('universitas', 'like', "%{$search}%")
                    ->orWhere('fakultas', 'like', "%{$search}%")
                    ->orWhere('prodi', 'like', "%{$search}%")
                    ->orWhere('provinsi', 'like', "%{$search}%")
                    ->orWhere('kabupaten', 'like', "%{$search}%")
                    ->orWhere('kecamatan', 'like', "%{$search}%")
                    ->orWhere('kelurahan', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('user.super-admin.member', [
            'title' => 'Member ',
            'subtitle' => 'Kelola member yang ada di Aliansi Dosen Nahada (ADN).',
            'user' => auth()->user(),
            'members' => $members,
            'search' => $search
        ]);
    }

    // Method yang sudah ada
    public function membership(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $division = $request->get('division');

        $query = Membership::with(['member.user', 'division', 'payments'])
            ->whereHas('member.user');

        // Filter search
        if ($search) {
            $query->whereHas('member.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('id_member_organization', 'like', "%{$search}%");
        }

        // Filter status
        if ($status) {
            $query->where('status', $status);
        }

        // Filter division
        if ($division) {
            $query->where('division_id', $division);
        }

        $memberships = $query->orderBy('created_at', 'desc')->paginate(20);
        $divisions = Division::all();

        // Ambil data payment settings untuk CRUD
        $paymentSettings = PaymentSetting::orderBy('created_at', 'desc')->get();

        return view('user.super-admin.membership', [
            'title' => 'Membership Keanggotaan',
            'subtitle' => 'Kelola membership yang ada di Aliansi Dosen Nahada (ADN).',
            'user' => auth()->user(),
            'memberships' => $memberships,
            'divisions' => $divisions,
            'paymentSettings' => $paymentSettings, // Tambahkan payment settings data
            'filters' => [
                'search' => $search,
                'status' => $status,
                'division' => $division
            ]
        ]);
    }

    // Method untuk melihat detail membership dan pembayaran
    public function membershipDetail($id)
    {
        $membership = Membership::with(['member.user', 'division', 'payments.approvedBy'])
            ->findOrFail($id);

        // Tambahkan divisions untuk dropdown pada form edit
        $divisions = Division::all();

        return view('user.super-admin.membership-detail', [
            'title' => 'Detail Membership',
            'subtitle' => 'Detail membership ' . $membership->member->user->name,
            'user' => auth()->user(),
            'membership' => $membership,
            'divisions' => $divisions // Tambahkan divisions data
        ]);
    }

    // Method untuk approve payment
    public function approvePayment(Request $request, $paymentId)
    {
        $validated = Validator::make($request->all(), [
            'id_member_organization' => 'required|string|max:255|unique:memberships,id_member_organization,' . $request->membership_id,
            'division_id' => 'nullable|exists:divisions,id',
            'invoice_link' => 'required|url',
        ], [
            'id_member_organization.unique' => 'Maaf id member tersebut sudah digunakan, mohon ganti ke id yang berbeda.',
        ]);

        if ($validated->fails()) {
            return redirect()->back()
                ->withErrors($validated)
                ->withInput()
                ->with('modal', 'approvePaymentModal' . $paymentId);
        }

        try {
            \DB::beginTransaction();

            $payment = MembershipPayment::with('membership')->findOrFail($paymentId);

            if ($payment->status !== 'pending') {
                throw new \Exception('Pembayaran sudah diproses sebelumnya.');
            }

            // Approve payment
            $validatedData = $validated->validated();

            $payment->approve(
                auth()->id(),
                $validatedData['invoice_link'] ?? null
            );

            // Update membership data
            $membership = $payment->membership;

            // Set ID member organization jika belum ada
            if (!$membership->id_member_organization && $validatedData['id_member_organization']) {
                $membership->id_member_organization = $validatedData['id_member_organization'];
            }

            // Set division jika dipilih
            if (!empty($validatedData['division_id'])) {
                $membership->division_id = $validatedData['division_id'];
            }

            $membership->save();

            \DB::commit();

            return redirect()->back()->with('success', 'Pembayaran berhasil disetujui dan membership telah diaktifkan.');

        } catch (\Exception $e) {
            \DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function rejectPayment(Request $request, $paymentId)
    {
        $validated = $request->validate([
            'admin_notes' => 'required|string|max:1000'
        ]);

        try {
            $payment = MembershipPayment::findOrFail($paymentId);

            if ($payment->status !== 'pending') {
                throw new \Exception('Pembayaran sudah diproses sebelumnya.');
            }

            // Update payment status
            $payment->update([
                'status' => 'rejected',
                'processed_by' => auth()->id(),
                'processed_at' => now(),
                'admin_notes' => $validated['admin_notes']
            ]);

            // Update membership status juga menjadi rejected
            $payment->membership->update([
                'status' => 'rejected'
            ]);

            return redirect()->back()->with('success', 'Pembayaran berhasil ditolak dan status membership diubah menjadi rejected.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Method untuk update membership data
    public function updateMembership(Request $request, $id)
    {
        $membership = Membership::findOrFail($id);
        $validator = Validator::make($request->all(), [
            // PERBAIKAN: Gunakan $id dari parameter URL untuk mengabaikan record saat ini
            'id_member_organization' => [
                'required',
                'string',
                'max:255',
                Rule::unique('memberships', 'id_member_organization')->ignore($id),
            ],
            'division_id' => 'nullable|exists:divisions,id',
        ], [
            'id_member_organization.unique' => 'Maaf ID member tersebut sudah digunakan, mohon ganti ke ID yang berbeda.',
        ]);

        try {
            $validatedData = $validator->validated();
            $membership = Membership::findOrFail($id);
            $membership->update($validatedData);

            return redirect()->back()->with('success', 'Data membership berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Method untuk export data ke Excel
    public function exportMembership(Request $request)
    {
        $status = $request->get('status');
        $division = $request->get('division');

        return Excel::download(new MembershipExport($status, $division), 'membership-data-' . now()->format('Y-m-d') . '.xlsx');
    }

    // === PAYMENT SETTINGS CRUD ===
    // Method untuk store payment setting (dipindahkan dari paymentSettings)
    public function storePaymentSetting(Request $request)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'account_holder' => 'required|string|max:255',
            'payment_amount' => 'required|numeric|min:0'
        ]);

        try {
            // Nonaktifkan semua payment setting yang ada
            PaymentSetting::where('is_active', true)->update(['is_active' => false]);

            // Buat payment setting baru sebagai yang aktif
            PaymentSetting::create([
                'bank_name' => $validated['bank_name'],
                'account_number' => $validated['account_number'],
                'account_holder' => $validated['account_holder'],
                'payment_amount' => $validated['payment_amount'],
                'is_active' => true
            ]);

            return redirect()->route('superadmin.membership')->with('success', 'Pengaturan pembayaran berhasil ditambahkan.');

        } catch (\Exception $e) {
            return redirect()->route('superadmin.membership')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Method untuk update payment setting (dipindahkan dari paymentSettings)
    public function updatePaymentSetting(Request $request, $id)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'account_holder' => 'required|string|max:255',
            'payment_amount' => 'required|numeric|min:0'
        ]);

        try {
            $paymentSetting = PaymentSetting::findOrFail($id);
            $paymentSetting->update($validated);

            return redirect()->route('superadmin.membership')->with('success', 'Pengaturan pembayaran berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->route('superadmin.membership')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Method untuk set payment setting sebagai aktif (dipindahkan dari paymentSettings)
    public function activatePaymentSetting($id)
    {
        try {
            \DB::beginTransaction();

            // Nonaktifkan semua payment setting
            PaymentSetting::where('is_active', true)->update(['is_active' => false]);

            // Aktifkan payment setting yang dipilih
            $paymentSetting = PaymentSetting::findOrFail($id);
            $paymentSetting->is_active = true;
            $paymentSetting->save();

            \DB::commit();

            return redirect()->route('superadmin.membership')->with('success', 'Pengaturan pembayaran berhasil diaktifkan.');

        } catch (\Exception $e) {
            \DB::rollback();
            return redirect()->route('superadmin.membership')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Method untuk delete payment setting (dipindahkan dari paymentSettings)
    public function deletePaymentSetting($id)
    {
        try {
            $paymentSetting = PaymentSetting::findOrFail($id);

            if ($paymentSetting->is_active) {
                return redirect()->route('superadmin.membership')->with('error', 'Tidak dapat menghapus pengaturan pembayaran yang sedang aktif.');
            }

            $paymentSetting->delete();

            return redirect()->route('superadmin.membership')->with('success', 'Pengaturan pembayaran berhasil dihapus.');

        } catch (\Exception $e) {
            return redirect()->route('superadmin.membership')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function publikasiIndex()
    {
        $publications = PublicationOrganization::latest()->paginate(10);

        return view('user.super-admin.publikasi_adn', [
            'title' => 'Publikasi ADN ',
            'subtitle' => 'Kelola publikasi organisasi',
            'publications' => $publications
        ]);
    }

    public function detailPublikasi($slug)
    {
        $publications = PublicationOrganization::where('slug', $slug)->firstOrFail();

        return view('user.super-admin.detail-publikasi', [
            'title' => 'Detail Publikasi Organisasi ',
            'subtitle' => 'Lihat informasi lengkap publikasi organisasi',
            'publications' => $publications,
        ]);
    }

    public function publikasiStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('modal', 'modalTambahPublikasi');
        }

        // Buat slug unik
        $slug = Str::slug($request->title);
        $slugExists = PublicationOrganization::where('slug', $slug)->exists();
        if ($slugExists) {
            $slug .= '-' . Str::random(5);
        }

        // Simpan ke database
        PublicationOrganization::create([
            'title' => $request->title,
            'content' => $request->content,
            'slug' => $slug,
        ]);

        return redirect()->back()->with('status', 'Publikasi Organisasi berhasil ditambah');
    }

    public function publikasiUpdate(Request $request, $slug)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $publications = PublicationOrganization::where('slug', $slug)->firstOrFail();

        $publications->title = $request->title;
        $publications->content = $request->content;
        $publications->slug = Str::slug($request->title);
        $publications->save();

        return redirect()->route('superadmin.publikasi-adn.detail', $publications->slug)
            ->with(['status' => 'Publikasi Organisasi berhasil diperbarui!', 'modal' => null]);
    }

    public function publikasiDelete($id)
    {
        $publications = PublicationOrganization::findOrFail($id);
        $publications->delete();
        return redirect()->route('superadmin.publikasi-adn')->with('status', 'Publikasi Organisasi berhasil dihapus.');
    }

    public function triDharmaIndex()
    {
        $tridharma = TriDharma::all();
        return view('user.super-admin.tridharma', [
            'title' => 'Tri Dharma Perguruan Tinggi ',
            'subtitle' => 'Kelola informasi tri dharma perguruan tinggi',
            'tridharma' => $tridharma,
        ]);
    }

    public function tridharmaStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('modal', 'modalTambahTridharma');
        }

        TriDharma::create([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->back()->with('status', 'Tri Dharma berhasil ditambah');
    }

    public function tridharmaUpdate(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $tridharma = TriDharma::where('id', $id)->firstOrFail();
        $tridharma->title = $request->title;
        $tridharma->content = $request->content;
        $tridharma->save();

        return redirect()->route('superadmin.tri-dharma', $tridharma->id)->with(['status' => 'Data berhasil diperbarui.', 'modal' => null]);
    }

    public function tridharmaDelete($id)
    {
        $data = TriDharma::findOrFail($id);
        $data->delete();

        return redirect()->route('superadmin.tri-dharma')->with('status', 'Data berhasil dihapus.');
    }

    public function landingPageIndex(Request $request)
    {
        $sections = CmsLandingSection::orderBy('created_at', 'asc')->get();

        // Ambil ID berita yang dipilih
        $selectedCarouselNews = CmsLandingSection::where('section', 'carousel')
            ->where('key', 'selected_news_ids') // <- ganti dari 'selected_news_titles'
            ->first();

        $selectedIds = [];

        if ($selectedCarouselNews && !is_null($selectedCarouselNews->value)) {
            $decoded = json_decode($selectedCarouselNews->value, true);
            $selectedIds = is_array($decoded) ? $decoded : [];
        }

        // Ambil semua berita dan urutkan berdasarkan created_at DESC
        $allNews = News::orderBy('created_at', 'desc')->get();

        // Ambil dan urutkan berita sesuai ID
        $selectedNews = News::whereIn('id', $selectedIds)
            ->get()
            ->sortBy(function ($item) use ($selectedIds) {
                return array_search($item->id, $selectedIds);
            })->values(); // <-- ini yang membuat iterasi di Blade konsisten dari 0, 1, 2, ...

        // COUNT DATA DINAMIS
        $memberCount = User::where('role', 'member')->count();
        $publicationCount = PublicationMember::count();
        $newsCount = News::count();

        // Berita untuk Portal Berita
        $latestNews = News::orderBy('created_at', 'desc')->take(8)->get();

        return view('user.super-admin.landing', [
            'title' => 'Landing Page ADN',
            'subtitle' => 'Kelola landing page website',
            'sections' => $sections,
            'allNews' => $allNews,
            'selectedNews' => $selectedNews,
            'memberCount' => $memberCount,
            'publicationCount' => $publicationCount,
            'newsCount' => $newsCount,
            'latestNews' => $latestNews,
        ]);
    }


    public function updateCarouselSelection(Request $request)
    {
        $validated = $request->validate([
            'selected_news_ids' => 'nullable|array|max:5',
            'selected_news_ids.*' => 'exists:news,id',
        ], [
            'selected_news_ids.max' => 'Maksimal 5 berita boleh dipilih untuk carousel.',
        ]);

        $selectedNewsIds = $validated['selected_news_ids'] ?? [];

        CmsLandingSection::updateOrCreate(
            [
                'section' => 'carousel',
                'key' => 'selected_news_ids', // <- simpan sebagai ID
            ],
            [
                'value' => json_encode($selectedNewsIds),
            ]
        );

        return redirect()->back()->with('status', 'Berita carousel berhasil diperbarui.');
    }

    public function landingPageStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'section' => 'required|string',
            'key' => 'required|string|in:title,content,image,icon',
            'value' => $request->key === 'image' ? 'required|image|mimes:jpeg,jpg,png|max:2048' : 'required|string',
            // PERBAIKAN: Mengembalikan nama input file menjadi 'icon'
            'icon' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $valueToSave = $request->value;
        $iconPathToSave = null;

        try {
            // Handle image for 'value' if 'key' is 'image'
            if ($request->key === 'image' && $request->hasFile('value')) {
                $file = $request->file('value');
                // PERBAIKAN: Direktori untuk gambar 'value' adalah 'images/landing'
                $directory = 'landing/images';

                // Gunakan nama file unik: section_timestamp_random.ext
                $fileName = Str::slug($request->section) . '_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();

                // Simpan file ke Storage disk 'public'
                $valueToSave = Storage::disk('public')->putFileAs($directory, $file, $fileName);
            }

            // Handle icon upload
            // PERBAIKAN: Menggunakan nama input 'icon'
            if ($request->hasFile('icon')) {
                $file = $request->file('icon');
                // PERBAIKAN: Direktori untuk icon adalah 'icon'
                $directory = 'landing/icon';

                // Gunakan nama file unik untuk icon: section_icon_timestamp_random.ext
                $fileName = Str::slug($request->section) . '_icon_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();

                // Simpan file ke Storage disk 'public'
                $iconPathToSave = Storage::disk('public')->putFileAs($directory, $file, $fileName);
            }

            CmsLandingSection::create([
                'section' => $request->section,
                'key' => $request->key,
                'value' => $valueToSave,
                'icon' => $iconPathToSave,
            ]);

            return redirect()->back()->with('status', 'Section landing page berhasil ditambahkan.');

        } catch (\Exception $e) {
            // Opsional: Hapus file jika ada error setelah disimpan namun sebelum disimpan ke DB
            if ($valueToSave && $request->key === 'image' && Storage::disk('public')->exists($valueToSave)) {
                Storage::disk('public')->delete($valueToSave);
            }
            if ($iconPathToSave && Storage::disk('public')->exists($iconPathToSave)) {
                Storage::disk('public')->delete($iconPathToSave);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan section: ' . $e->getMessage());
        }
    }


    public function landingPageUpdate(Request $request, $id)
    {
        $section = CmsLandingSection::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'section' => 'required|string',
            // Validasi 'value' tergantung pada 'key' yang sudah ada di database ($section->key)
            'value' => $section->key === 'image' ? 'nullable|image|mimes:jpeg,jpg,png|max:2048' : 'nullable|string',
            'icon' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $valueToUpdate = $section->value; // Default: gunakan value lama
        $iconToUpdate = $section->icon;   // Default: gunakan icon lama

        try {
            // Handle 'value' field
            if ($section->key === 'image') {
                // Jika ada file gambar baru diunggah untuk 'value'
                if ($request->hasFile('value')) {
                    // Hapus gambar lama dari Storage jika ada
                    if ($section->value && Storage::disk('public')->exists($section->value)) {
                        Storage::disk('public')->delete($section->value);
                    }

                    $file = $request->file('value');
                    $directory = 'landing/images'; // Direktori yang diminta
                    // Generate nama file unik
                    $fileName = Str::slug($request->section) . '_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();

                    // Simpan file baru
                    $valueToUpdate = Storage::disk('public')->putFileAs($directory, $file, $fileName);
                }
                // Jika tidak ada file baru diunggah, dan request->value adalah string kosong (misal user hapus gambar)
                // atau jika ada gambar lama tapi input file kosong, maka tetap gunakan gambar lama kecuali ada instruksi untuk menghapus
                // Jika ingin user bisa menghapus gambar, tambahkan input hidden (e.g., 'clear_image')
            } else {
                // Jika key bukan 'image', maka value adalah string biasa
                $valueToUpdate = $request->input('value');
            }

            // Handle 'icon' field
            if ($request->hasFile('icon')) {
                // Hapus ikon lama dari Storage jika ada
                if ($section->icon && Storage::disk('public')->exists($section->icon)) {
                    Storage::disk('public')->delete($section->icon);
                }

                $file = $request->file('icon');
                $directory = 'landing/icon'; // Direktori yang diminta
                // Generate nama file unik
                $fileName = Str::slug($request->section) . '_icon_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();

                // Simpan file baru
                $iconToUpdate = Storage::disk('public')->putFileAs($directory, $file, $fileName);
            }

            $section->update([
                'section' => $request->section,
                'value' => $valueToUpdate,
                'icon' => $iconToUpdate,
            ]);

            return redirect()->back()->with('status', 'Section landing page berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui section: ' . $e->getMessage());
        }
    }


    public function landingPageDestroy($id)
    {
        $section = CmsLandingSection::findOrFail($id);

        try {
            // Hapus file gambar jika key adalah 'image' dan value ada
            // Asumsi $section->value sudah menyimpan path relatif dari storage/app/public
            if ($section->key === 'image' && $section->value) {
                if (Storage::disk('public')->exists($section->value)) {
                    Storage::disk('public')->delete($section->value);
                }
            }

            // Hapus file icon jika ada
            // Asumsi $section->icon sudah menyimpan path relatif dari storage/app/public
            if ($section->icon) {
                if (Storage::disk('public')->exists($section->icon)) {
                    Storage::disk('public')->delete($section->icon);
                }
            }

            $section->delete();

            // Menggunakan 'status' untuk SweetAlert
            return redirect()->back()->with('status', 'Section berhasil dihapus.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus section: ' . $e->getMessage());
        }
    }

    public function profile(Request $request)
    {
        return view('user.super-admin.profil', [
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

            return redirect()->route('superadmin.profile.edit')->with('status', 'Profil berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui profil: ' . $e->getMessage());
        }
    }

    public function showChangePassword()
    {
        return view('user.super-admin.ganti_password', [
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

        return redirect()->route('superadmin.ganti-password')->with('status', 'Password berhasil diperbarui.');
    }
}
