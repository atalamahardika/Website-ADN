<?php

namespace App\Http\Controllers;

use App\Models\CmsLandingSection;
use App\Models\Division;
use App\Models\News;
use App\Models\PublicationMember;
use App\Models\PublicationOrganization;
use App\Models\TriDharma;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class GuestController extends Controller
{
    public function index()
    {
        // Ambil ID berita carousel dari cms_landing_sections
        $selectedIds = json_decode(
            CmsLandingSection::where('section', 'carousel')
                ->where('key', 'selected_news_ids')
                ->value('value'),
            true
        ) ?? [];

        // Ambil data berita untuk carousel dan urutkan sesuai ID
        $carouselNews = News::whereIn('id', $selectedIds)
            ->get()
            ->sortBy(function ($item) use ($selectedIds) {
                return array_search($item->id, $selectedIds);
            })->values();

        // Hero Section
        $heroTitle = CmsLandingSection::where('section', 'hero')->where('key', 'title')->first();
        $heroContent = CmsLandingSection::where('section', 'hero')->where('key', 'content')->first();
        $heroImage = CmsLandingSection::where('section', 'hero')->where('key', 'image')->first();

        // About Section
        $aboutTitle = CmsLandingSection::where('section', 'about')->where('key', 'title')->first();
        $aboutContent = CmsLandingSection::where('section', 'about')->where('key', 'content')->first();
        $aboutImage = CmsLandingSection::where('section', 'about')->where('key', 'image')->first();

        // Count Section Data
        $memberCount = User::where('role', 'member')->count(); // Atau User::where('role', 'member')->count()
        $publicationCount = PublicationMember::count(); // Sesuaikan model jika berbeda
        $newsCount = News::count();

        // Portal Berita
        $latestNews = News::latest()->take(8)->get();

        // Contact Section
        $contactTitle = CmsLandingSection::where('section', 'contact')->where('key', 'title')->first();
        $contactContents = CmsLandingSection::where('section', 'contact')
            ->where('section', 'contact')
            ->where('key', '!=', 'title')
            ->where('key', '!=', 'icon')
            ->get();

        return view('user.guest.landing', compact(
            'carouselNews',
            'heroTitle',
            'heroContent',
            'heroImage',
            'aboutTitle',
            'aboutContent',
            'aboutImage',
            'memberCount',
            'publicationCount',
            'newsCount',
            'latestNews',
            'contactTitle',
            'contactContents'
        ));
    }


    public function showBerita($slug)
    {
        $news = News::where('slug', $slug)->firstOrFail();
        return view('user.guest.detail-berita', compact('news'));
    }

    public function listBerita()
    {
        $news = News::latest()->paginate(10);
        return view('user.guest.nav-berita', compact('news'));
    }

    public function tridharma()
    {
        $tridharma = TriDharma::orderBy('id')->get(); // diasumsikan urutan berdasarkan ID
        return view('user.guest.nav-tridharma', compact('tridharma'));
    }

    public function divisi()
    {
        $divisions = Division::orderBy('id')->get();
        return view('user.guest.nav-divisi', compact('divisions'));
    }

    public function detailDivisi($slug)
    {
        $division = Division::where('slug', $slug)->firstOrFail();
        // Ambil hanya sub divisi yang disetujui
        $approvedSubDivisions = $division->subDivisions()->where('is_approved', true)->get();

        return view('user.guest.detail-divisi', compact('division', 'approvedSubDivisions'));
    }

    public function detailSubDivisi($divisionSlug, $subdivisionSlug)
    {
        $division = Division::where('slug', $divisionSlug)->firstOrFail();
        $subdivision = $division->subDivisions()
            ->where('is_approved', true)
            ->where('slug', $subdivisionSlug)
            ->firstOrFail();

        return view('user.guest.detail-subdivisi', compact('division', 'subdivision'));
    }



    public function publikasi(Request $request)
    {
        $tab = $request->get('tab');

        $adn = PublicationOrganization::latest()->get();

        // Jika tab adalah mandiri, lakukan pagination
        if ($tab === 'mandiri') {
            $mandiri = PublicationMember::latest()->paginate(10);
        } else {
            // Buat paginator kosong supaya tidak error
            $mandiri = new LengthAwarePaginator([], 0, 10);
        }

        return view('user.guest.nav-publikasi', compact('adn', 'mandiri'));
    }


    public function detailPublikasiAdn($slug)
    {
        $publikasi = PublicationOrganization::where('slug', $slug)->firstOrFail();
        return view('user.guest.detail-publikasi-adn', compact('publikasi'));
    }


    public function detailPublikasiOrganisasi(Request $request)
    {
    }

}
