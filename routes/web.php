<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\WilayahController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('user.guest.landing');
// });

// Guest Routes
Route::prefix('')->name('guest.')->group(function () {
    Route::get('/', [GuestController::class, 'index'])->name('landing');
    Route::get('/berita/{slug}', [GuestController::class, 'showBerita'])->name('news.show');
    Route::get('/berita', [GuestController::class, 'listBerita'])->name('news.list');
    Route::get('/tridharma', [GuestController::class, 'tridharma'])->name('tridharma');
    Route::get('/list-publikasi', [GuestController::class, 'publikasi'])->name('publication');
    Route::get('/list-publikasi/adn/{slug}', [GuestController::class, 'detailPublikasiAdn'])->name('publication.adn.detail');
    Route::get('/divisi', [GuestController::class, 'divisi'])->name('division');
    Route::get('/divisi/{slug}', [GuestController::class, 'detailDivisi'])->name('division.detail');
    Route::get('/divisi/{divisionSlug}/subdivisi/{subdivisionSlug}', [GuestController::class, 'detailSubDivisi'])->name('division.subdivision.detail');

});

require __DIR__ . '/auth.php';

// Member Routes
Route::middleware(['auth', 'verified', 'role:member'])->group(function () {
    Route::get('dashboard', [MemberController::class, 'dashboard'])->name('dashboard');

    // Route untuk navigasi ke halaman biografi (CRUD)
    Route::get('biografi', [MemberController::class, 'biografi'])->name('biografi');
    Route::patch('biografi/update', [MemberController::class, 'biografiUpdate'])->name('biografi.update');

    // Bidang Keilmuan
    Route::post('biografi/keilmuan', [MemberController::class, 'storeKeilmuan'])->name('keilmuan.store');
    Route::patch('biografi/keilmuan/{id}', [MemberController::class, 'updateKeilmuan'])->name('keilmuan.update');
    Route::delete('biografi/keilmuan/{id}', [MemberController::class, 'destroyKeilmuan'])->name('keilmuan.destroy');

    // Keahlian
    Route::post('biografi/keahlian', [MemberController::class, 'storeKeahlian'])->name('keahlian.store');
    Route::patch('biografi/keahlian/{id}', [MemberController::class, 'updateKeahlian'])->name('keahlian.update');
    Route::delete('biografi/keahlian/{id}', [MemberController::class, 'destroyKeahlian'])->name('keahlian.destroy');

    // Riwayat Pendidikan
    Route::post('biografi/pendidikan', [MemberController::class, 'storePendidikan'])->name('pendidikan.store');
    Route::patch('biografi/pendidikan/{id}', [MemberController::class, 'updatePendidikan'])->name('pendidikan.update');
    Route::delete('biografi/pendidikan/{id}', [MemberController::class, 'destroyPendidikan'])->name('pendidikan.destroy');

    // Penghargaan
    Route::post('biografi/penghargaan', [MemberController::class, 'storePenghargaan'])->name('penghargaan.store');
    Route::patch('biografi/penghargaan/{id}', [MemberController::class, 'updatePenghargaan'])->name('penghargaan.update');
    Route::delete('biografi/penghargaan/{id}', [MemberController::class, 'destroyPenghargaan'])->name('penghargaan.destroy');

    // Riwayat Mengajar
    Route::post('biografi/mengajar', [MemberController::class, 'storeMengajar'])->name('mengajar.store');
    Route::patch('biografi/mengajar/{id}', [MemberController::class, 'updateMengajar'])->name('mengajar.update');
    Route::delete('biografi/mengajar/{id}', [MemberController::class, 'destroyMengajar'])->name('mengajar.destroy');

    // Route untuk navigasi ke halaman publikasi (CRUD)
    Route::get('publikasi', [MemberController::class, 'publication'])->name('publikasi');
    Route::post('publikasi', [MemberController::class, 'publicationStore'])->name('publikasi.store');
    Route::patch('publikasi/{id}', [MemberController::class, 'publicationUpdate'])->name('publikasi.update');
    Route::delete('publikasi/{id}', [MemberController::class, 'publicationDestroy'])->name('publikasi.destroy');

    // Route untuk mengakses halaman profil (CRUD)
    Route::get('profile', [MemberController::class, 'profile'])->name('profile.edit');
    Route::patch('profile', [MemberController::class, 'profileUpdate'])->name('profile.update');

    // Route untuk mengakses halaman ganti password
    Route::get('ganti-password', [MemberController::class, 'showChangePassword'])->name('ganti-password');
    Route::put('ganti-password', [MemberController::class, 'updatePassword'])->name('ganti-password.update');
});

// Admin Routes
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // CRUD Konten Beranda Divisi
    Route::get('admin/beranda-divisi', [AdminController::class, 'beranda'])->name('admin.beranda-divisi');
    Route::put('admin/beranda-divisi/update', [AdminController::class, 'updateBeranda'])->name('admin.beranda-divisi.update');

    // CRUD Subdivisi
    Route::get('admin/subdivisi', [AdminController::class, 'subdivisi'])->name('admin.subdivisi');
    Route::post('admin/subdivisi', [AdminController::class, 'storeSubdivisi'])->name('admin.subdivisi.store');
    Route::put('admin/subdivisi/{id}', [AdminController::class, 'updateSubdivisi'])->name('admin.subdivisi.update');
    Route::delete('admin/subdivisi/{id}', [AdminController::class, 'destroySubdivisi'])->name('admin.subdivisi.destroy');


    // CRUD Anggota Divisi
    Route::get('admin/anggota', [AdminController::class, 'anggota'])->name('admin.anggota');

    // Routes untuk mengakses halaman profil
    Route::get('admin/profile', [AdminController::class, 'profile'])->name('admin.profile.edit');
    Route::patch('admin/profile', [AdminController::class, 'profileUpdate'])->name('admin.profile.update');

    // Route untuk mengakses halaman ganti password
    Route::get('admin/ganti-password', [AdminController::class, 'showChangePassword'])->name('admin.ganti-password');
    Route::put('admin/ganti-password', [AdminController::class, 'updatePassword'])->name('admin.ganti-password.update');
    // Route::get('admin/ganti-password', [AdminController::class, 'gantiPassword'])->name('admin.ganti_password');

});

// Super Admin Routes
Route::middleware(['auth', 'verified', 'role:super admin'])->group(function () {
    Route::get('superadmin/dashboard', [SuperAdminController::class, 'dashboard'])->name('superadmin.dashboard');

    // CRUD Divisi
    Route::get('superadmin/divisi', [SuperAdminController::class, 'divisionIndex'])->name('superadmin.divisi');
    Route::post('superadmin/divisi', [SuperAdminController::class, 'divisionStore'])->name('superadmin.divisi.store');
    Route::patch('superadmin/divisi/{division}', [SuperAdminController::class, 'divisionUpdate'])->name('superadmin.divisi.update');
    Route::delete('superadmin/divisi/{id}', [SuperAdminController::class, 'divisionDestroy'])->name('superadmin.divisi.destroy');
    Route::get('/superadmin/divisi/detail/{id}', [SuperAdminController::class, 'showDivisionDetail'])->name('superadmin.divisi.detail');
    Route::post('/superadmin/detail/sub-divisi/store', [SuperAdminController::class, 'storeSubDivision'])->name('superadmin.subdivisi.store');
    Route::post('/superadmin/detail/sub-divisi/update/{id}', [SuperAdminController::class, 'updateSubDivision'])->name('superadmin.subdivisi.update');
    Route::delete('/superadmin/detail/sub-divisi/delete/{id}', [SuperAdminController::class, 'deleteSubDivision'])->name('superadmin.subdivisi.delete');
    Route::patch('/detail/sub-divisi/approval/{id}', [SuperAdminController::class, 'toggleSubDivisionApproval'])->name('superadmin.subdivisi.toggleApproval');


    // CRUD Admin
    Route::get('superadmin/admin', [SuperAdminController::class, 'adminIndex'])->name('superadmin.admin');
    Route::post('superadmin/admin', [SuperAdminController::class, 'adminStore'])->name('superadmin.admin.store');
    Route::delete('superadmin/admin/{id}', [SuperAdminController::class, 'adminDestroy'])->name('superadmin.admin.destroy');

    // CRUD Member
    Route::get('superadmin/member', [SuperAdminController::class, 'member'])->name('superadmin.member');

    // CRUD Membership
    Route::get('superadmin/membership', [SuperAdminController::class, 'membership'])->name('superadmin.membership');

    // CRUD Berita
    Route::get('superadmin/berita', [SuperAdminController::class, 'beritaIndex'])->name('superadmin.berita');
    Route::post('superadmin/berita', [SuperAdminController::class, 'beritaStore'])->name('superadmin.berita.store');
    Route::put('superadmin/berita/detail-berita/{slug}', [SuperAdminController::class, 'beritaUpdate'])->name('superadmin.berita.update');
    Route::delete('superadmin/berita/{id}/delete', [SuperAdminController::class, 'deleteBerita'])->name('superadmin.berita.delete');
    Route::get('superadmin/berita/detail-berita/{slug}', [SuperAdminController::class, 'detailBerita'])->name('superadmin.berita.detail');

    // CRUD Publikasi ADN
    Route::get('superadmin/publikasi-adn', [SuperAdminController::class, 'publikasiIndex'])->name('superadmin.publikasi-adn');
    Route::post('superadmin/publikasi-adn', [SuperAdminController::class, 'publikasiStore'])->name('superadmin.publikasi-adn.store');
    Route::put('superadmin/publikasi-adn/detail-publikasi/{slug}', [SuperAdminController::class, 'publikasiUpdate'])->name('superadmin.publikasi-adn.update');
    Route::delete('superadmin/publikasi-adn/{id}', [SuperAdminController::class, 'publikasiDelete'])->name('superadmin.publikasi-adn.delete');
    Route::get('superadmin/publikasi-adn/detail-publikasi/{slug}', [SuperAdminController::class, 'detailPublikasi'])->name('superadmin.publikasi-adn.detail');

    // CRUD Tri Dharma
    Route::get('superadmin/tridharma', [SuperAdminController::class, 'triDharmaIndex'])->name('superadmin.tri-dharma');
    Route::post('superadmin/tridharma/store', [SuperAdminController::class, 'tridharmaStore'])->name('superadmin.tridharma.store');
    Route::put('superadmin/tridharma/update/{id}', [SuperAdminController::class, 'tridharmaUpdate'])->name('superadmin.tridharma.update');
    Route::delete('superadmin/tridharma/delete/{id}', [SuperAdminController::class, 'tridharmaDelete'])->name('superadmin.tridharma.delete');

    // CRUD Landing Page
    Route::get('superadmin/landing-page', [SuperAdminController::class, 'landingPageIndex'])->name('superadmin.landing');
    Route::post('superadmin/landing-page/store', [SuperAdminController::class, 'landingPageStore'])->name('superadmin.landing.store');
    Route::post('superadmin/landing-page/update/{id}', [SuperAdminController::class, 'landingPageUpdate'])->name('superadmin.landing.update');
    Route::delete('superadmin/landing-page/delete/{id}', [SuperAdminController::class, 'landingPageDestroy'])->name('superadmin.landing.destroy');
    Route::post('superadmin/landing-page/carousel/update', [SuperAdminController::class, 'updateCarouselSelection'])->name('superadmin.landing.carousel.update');

    // Routes untuk mengakses halaman profil
    Route::get('superadmin/profile', [SuperAdminController::class, 'profile'])->name('superadmin.profile.edit');
    Route::patch('superadmin/profile', [SuperAdminController::class, 'profileUpdate'])->name('superadmin.profile.update');

    // Route untuk mengakses halaman ganti password
    Route::get('superadmin/ganti-password', [SuperAdminController::class, 'showChangePassword'])->name('superadmin.ganti-password');
    Route::put('superadmin/ganti-password', [SuperAdminController::class, 'updatePassword'])->name('superadmin.ganti-password.update');
});

// API Routes for Wilayah
Route::prefix('api/wilayah')->group(function () {
    Route::get('/provinces', [WilayahController::class, 'getProvinces']);
    Route::get('/cities/{prov_id}', [WilayahController::class, 'getCities']);
    Route::get('/districts/{city_id}', [WilayahController::class, 'getDistricts']);
    Route::get('/subdistricts/{dis_id}', [WilayahController::class, 'getSubdistricts']);
    Route::get('/postalcode/{subdis_id}', [WilayahController::class, 'getPostalCode']);
});