<style>
    .news-card {
        transition: transform 0.3s, box-shadow 0.3s;
        border-radius: 10px;
        overflow: hidden;
    }

    .news-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 20px rgba(21, 64, 14, 0.3);
        border: 1px solid #15400E;
    }

    .news-card .card-title {
        font-weight: 600;
        font-size: 1rem;
        color: #15400E;
    }

    .object-fit-cover {
        object-fit: cover;
    }
</style>

<div class="news-section my-5">
    <div class="container">
        <h2 class="text-center fw-bold mb-4">Portal Berita</h2>

        @if ($latestNews->isEmpty())
            <p class="text-center text-muted">Tidak ada berita yang tersedia.</p>
        @else
            <div class="row justify-content-center g-4">
                @foreach ($latestNews as $item)
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex">
                        <div class="card news-card flex-fill shadow-sm border-0">
                            <div class="ratio ratio-4x3">
                                <img src="{{ asset($item->image) }}" class="card-img-top object-fit-cover"
                                    alt="Thumbnail {{ $item->title }}">
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title flex-grow-1">{{ $item->title }}</h6>
                                <a href="{{ route('guest.news.show', ['slug' => $item->slug]) }}"
                                    class="btn btn-sm btn-outline-success mt-2">
                                    Baca Selengkapnya
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('guest.news.list') }}" class="btn btn-outline-success btn-sm">
                    Lihat Semua Berita
                </a>
            </div>
        @endif
    </div>
</div>
