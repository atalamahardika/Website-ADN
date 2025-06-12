<?php

namespace App\Http\Controllers;

use App\Models\CmsLandingSection;
use App\Models\PublicationMember;
use App\Models\PublicationOrganization;
use App\Models\SubDivision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use App\Models\Division;
use App\Models\User;
use App\Models\News;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Models\TriDharma;
use App\Models\LandingContent;


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

        // Buat slug unik
        $slug = Str::slug($request->title);
        $slugExists = News::where('slug', $slug)->exists();
        if ($slugExists) {
            $slug .= '-' . Str::random(5);
        }

        // Proses base64 image
        $croppedImage = $request->cropped_image;

        if (preg_match('/^data:image\/(\w+);base64,/', $croppedImage, $type)) {
            $data = substr($croppedImage, strpos($croppedImage, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, etc

            $data = base64_decode($data);

            if ($data === false) {
                return back()->withErrors(['image' => 'Gagal memproses gambar.']);
            }

            $imagePath = "images/news/{$slug}.jpg";
            file_put_contents(public_path($imagePath), $data);
        } else {
            return back()->withErrors(['image' => 'Format gambar tidak valid.']);
        }

        // Simpan ke database
        News::create([
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->content,
            'source_link' => $request->source_link,
            'image' => $imagePath,
        ]);

        return redirect()->back()->with('status', 'Berita berhasil ditambahkan.');
    }


    public function beritaUpdate(Request $request, $slug)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'source_link' => 'nullable|string|max:255',
            'cropped_image' => 'nullable|string', // base64 string
        ]);

        $news = News::where('slug', $slug)->firstOrFail();

        // Simpan perubahan judul, slug, konten, dan sumber
        $news->title = $request->title;
        $news->slug = Str::slug($request->title);
        $news->content = $request->content;
        $news->source_link = $request->source_link;

        // Tangani gambar baru jika diunggah
        if ($request->has('cropped_image') && $request->cropped_image) {
            // Hapus gambar lama
            if (File::exists(public_path($news->image))) {
                File::delete(public_path($news->image));
            }

            // Simpan gambar baru
            $imageData = $request->cropped_image;
            $image = str_replace('data:image/jpeg;base64,', '', $imageData);
            $image = str_replace(' ', '+', $image);
            $imageName = 'images/news/' . $news->slug . '.jpg';

            File::ensureDirectoryExists(public_path('images/news'));
            File::put(public_path($imageName), base64_decode($image));

            $news->image = $imageName;
        }

        $news->save();

        // Redirect kembali ke halaman detail
        return redirect()->route('superadmin.berita.detail', $news->slug)
            ->with(['status' => 'Berita berhasil diperbarui!', 'modal' => null]);
    }

    public function deleteBerita($id)
    {
        $news = News::findOrFail($id);

        // Hapus file gambar
        if ($news->image && file_exists(public_path($news->image))) {
            unlink(public_path($news->image));
        }

        $news->delete();

        return redirect()->route('superadmin.berita')->with('status', 'Berita berhasil dihapus.');
    }

    public function member()
    {
        return view('user.super-admin.member', [
            'title' => 'Member ',
            'subtitle' => 'Kelola member yang ada di Aliansi Dosen Nahada (ADN).',
            'user' => auth()->user()
        ]);
    }

    public function membership()
    {
        return view('user.super-admin.membership', [
            'title' => 'Membership Keanggotaan ',
            'subtitle' => 'Kelola membership yang ada di Aliansi Dosen Nahada (ADN).',
            'user' => auth()->user()
        ]);
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
        $request->validate([
            'section' => 'required|string',
            'key' => 'required|string|in:title,content,image',
            'value' => $request->key === 'image' ? 'required|image|mimes:jpeg,jpg,png|max:2048' : 'required|string',
            'icon' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $value = $request->value;

        // Simpan gambar ke folder images/landing
        if ($request->hasFile('value') && $request->key === 'image') {
            $valueFile = $request->file('value');
            $extension = $valueFile->getClientOriginalExtension();
            $valueName = $request->section . '.' . $extension;
            $path = 'images/landing/' . $valueName;
            $valueFile->move(public_path('images/landing'), $valueName);
            $value = $path; // simpan full path relatif
        }

        // Simpan ikon ke folder icon
        $iconPath = null;
        if ($request->hasFile('icon')) {
            $iconFile = $request->file('icon');
            $iconName = $iconFile->getClientOriginalName();
            $iconFile->move(public_path('icon'), $iconName);
            $iconPath = 'icon/' . $iconName;
        }

        CmsLandingSection::create([
            'section' => $request->section,
            'key' => $request->key,
            'value' => $value,
            'icon' => $iconPath,
        ]);

        return redirect()->back()->with('status', 'Section berhasil ditambahkan.');
    }


    public function landingPageUpdate(Request $request, $id)
    {
        $section = CmsLandingSection::findOrFail($id);

        $request->validate([
            'section' => 'required|string',
            'value' => $section->key === 'image' ? 'nullable|image|mimes:jpeg,jpg,png|max:2048' : 'nullable|string',
            'icon' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $value = $section->value;

        // Ganti gambar jika key == image
        if ($request->hasFile('value') && $section->key === 'image') {
            // Hapus file lama jika ada
            $oldPath = public_path($section->value);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }

            // Simpan file baru
            $file = $request->file('value');
            $extension = $file->getClientOriginalExtension();
            $filename = $request->section . '.' . $extension;
            $file->move(public_path('images/landing'), $filename);
            $value = 'images/landing/' . $filename;
        } elseif ($request->value && $section->key !== 'image') {
            $value = $request->value;
        }

        // Ganti ikon jika ada
        $icon = $section->icon;
        if ($request->hasFile('icon')) {
            // Hapus ikon lama
            if ($icon && file_exists(public_path($icon))) {
                unlink(public_path($icon));
            }

            $iconFile = $request->file('icon');
            $iconName = $iconFile->getClientOriginalName();
            $iconFile->move(public_path('icon'), $iconName);
            $icon = 'icon/' . $iconName;
        }

        $section->update([
            'section' => $request->section,
            'value' => $value,
            'icon' => $icon,
        ]);

        return redirect()->back()->with('status', 'Section berhasil diperbarui.');
    }


    public function landingPageDestroy($id)
    {
        $section = CmsLandingSection::findOrFail($id);

        // Hapus file gambar jika key == image
        if ($section->key === 'image' && $section->value) {
            $imagePath = public_path('images/landing/' . $section->value);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // Hapus file icon jika ada
        if ($section->icon) {
            $iconPath = public_path('icon/' . $section->icon);
            if (file_exists($iconPath)) {
                unlink($iconPath);
            }
        }

        $section->delete();

        return redirect()->back()->with('status', 'Section berhasil dihapus.');
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
        $role = 'super admin';

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

        return redirect()->route('superadmin.profile.edit')->with('status', 'Profil berhasil diperbarui.');
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
