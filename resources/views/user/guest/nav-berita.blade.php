@include('head')
@include('header')

<style>
    .berita-card:hover {
        transform: scale(1.01);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        border-color: #2e7d32;
    }
</style>
<div class="nav-berita">
    <div class="container my-5">
        <h2 class="text-center mb-4">Portal Berita</h2>
        {{-- List Berita --}}
        @if ($news->isEmpty())
            <p class="text-gray-500 italic text-center">Belum ada berita yang ditambahkan.</p>
        @else
            <div class="row">
                @foreach ($news as $item)
                    <div class="col-md-12 mb-3">
                        <a href="{{ route('guest.news.show', $item->slug) }}" class="text-decoration-none text-dark">
                            <div class="card d-flex flex-row berita-card">
                                <img src="{{ asset($item->image) }}" class="card-img-left w-25" style="object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title">{{ \Illuminate\Support\Str::limit($item->title, 60) }}
                                    </h5>
                                    <p class="card-text prose">
                                        {{ \Illuminate\Support\Str::limit(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($item->content))), 150, '... (selengkapnya)') }}
                                        <span class="text-primary"></span>
                                    </p>
                                    <small class="text-muted">Sumber: {{ $item->source_link }}</small>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
        {{-- Pagination --}}
        <div class="mt-4">
            {{ $news->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
</div>

@include('footer')