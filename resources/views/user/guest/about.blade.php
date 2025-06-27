@if ($aboutTitle && $aboutContent && $aboutImage)
    <div class="about-section py-5 mt-4" id="tentang" style="background-color: rgba(219, 234, 213, 0.2);">
        <div class="container">
            <!-- Gunakan flex-row hanya di xl (desktop), sisanya tetap kolom -->
            <div class="row gy-4 d-flex flex-column flex-xl-row align-items-center">
                <!-- Gambar (default duluan, desktop pindah ke kanan) -->
                <div class="col-12 col-xl-6 text-center order-1 order-xl-2 d-flex justify-content-center">
                    <img src="{{ asset('storage/' . $aboutImage->value) }}" alt="about" class="img-fluid rounded"
                        style="object-fit: cover; max-height: 480px;">
                </div>

                <!-- Teks (default di bawah, desktop pindah ke kiri) -->
                <div class="col-12 col-xl-6 order-2 order-xl-1 text-center text-xl-start">
                    <h2 class="display-5 fw-bold">{{ $aboutTitle->value }}</h2>
                    <div class="prose mt-3 text-justify">
                        {!! $aboutContent->value !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
