@if ($aboutTitle && $aboutContent && $aboutImage)
    <div class="about-section py-5 mt-4" id="tentang" style="background-color: rgba(219, 234, 213, 0.2);">
        <div class="container">
            <div class="row align-items-center gy-4">
                <!-- Teks -->
                <div class="col-md-6">
                    <h2 class="display-5 fw-bold">{{ $aboutTitle->value }}</h2>
                    <div class="prose mt-3 text-justify">
                        {!! $aboutContent->value !!}
                    </div>
                </div>

                <!-- Gambar -->
                <div class="col-md-6 text-center">
                    <img src="{{ asset($aboutImage->value) }}" alt="about" class="img-fluid rounded shadow-sm"
                        style="object-fit: cover; max-height: 480px;">
                </div>
            </div>
        </div>
    </div>
@endif
