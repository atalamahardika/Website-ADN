<div id="guestCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
    <div class="carousel-indicators">
        @foreach ($carouselNews as $index => $news)
            <button type="button" data-bs-target="#guestCarousel" data-bs-slide-to="{{ $index }}"
                class="{{ $loop->first ? 'active' : '' }}" aria-current="{{ $loop->first ? 'true' : 'false' }}"
                aria-label="Slide {{ $index + 1 }}"></button>
        @endforeach
    </div>
    <div class="carousel-inner">
        @foreach ($carouselNews as $index => $news)
            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                <a href="{{ route('guest.news.show', $news->slug ?? $news->id) }}">
                    <!-- Container gambar dengan rasio 3:2 -->
                    <div class="carousel-img-wrapper">
                        <img src="{{ asset('storage/' . $news->image) }}"
                            class="d-block w-100 img-fluid custom-carousel-height" alt="{{ $news->title }}">
                    </div>
                    <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-2">
                        <h5 class="text-white mb-0">{{ $news->title }}</h5>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#guestCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Sebelumnya</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#guestCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Selanjutnya</span>
    </button>
</div>
<style>
    .carousel-img-wrapper {
        aspect-ratio: 3 / 2;
        /* Menjaga rasio 3:2 */
        width: 100%;
        overflow: hidden;
    }

    .carousel-img-wrapper img {
        object-fit: cover;
        width: 100%;
        height: 100%;
    }

    .carousel-inner {
        min-height: 100px;
    }
</style>
