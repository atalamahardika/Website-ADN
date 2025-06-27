@if ($heroTitle && $heroContent && $heroImage)
    <div class="hero-section position-relative d-flex align-items-center min-vh-75 py-5 py-md-5"
        style="background-image: url('{{ asset('images/bg-hero.svg') }}');
        background-size: cover; background-position: center; background-repeat: no-repeat; ">

        <!-- Overlay transparan -->
        <div class="position-absolute top-0 start-0 w-100 h-100"
            style="background-color: rgba(248, 251, 247, 0.4); z-index: 1;">
        </div>

        <!-- Konten di atas background -->
        <div class="container py-5 position-relative" style="z-index: 2;">
            <div class="row align-items-center g-4">
                <!-- Gambar Kiri -->
                <div class="col d-flex justify-content-center">
                    <img src="{{ asset('storage/' . $heroImage->value) }}" alt="image" class="img-fluid" style="max-width: 550px; object-fit: cover;">
                </div>

                <!-- Teks Kanan -->
                <div class="col text-center text-xl-start hero-text">
                    <h2 class="fw-bold display-5" style="color: #14532d;">
                        {{ $heroTitle->value }}
                    </h2>
                    <div class="prose text-justify">
                        {!! $heroContent->value !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
