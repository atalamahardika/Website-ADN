@if ($heroTitle && $heroContent && $heroImage)
    <div class="hero-section d-flex align-items-center min-vh-75 py-5 py-md-5"
        style="background-image: url('{{ asset('images/bg-hero.svg') }}');
        background-size: cover; background-position: center; background-repeat: no-repeat;">
        <div class="container py-5">
            <div class="row align-items-center g-4">
                <!-- Gambar Kiri -->
                <div class="col-md-6 text-center">
                    <img src="{{ asset('storage/' . $heroImage->value) }}" alt="image" class="img-fluid" style="max-width: 550px;">
                </div>

                <!-- Teks Kanan -->
                <div class="col-md-6 text-center text-md-start">
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
