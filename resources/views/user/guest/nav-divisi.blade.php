@include('head')
@include('header')

<style>
    .hover-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        border-color: #2e7d32;
    }

    .card {
        border-radius: 10px;
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: bold;
    }

    .card-text {
        color: #555;
    }
</style>

<div class="divisi">
    <div class="container my-5">
        <h1 class="text-center mb-4" style="color: #1b5e20;">Daftar Divisi</h1>

        <div class="d-flex flex-wrap justify-content-center gap-4">
            @forelse ($divisions as $division)
                <div style="width: 250px;">
                    <a href="{{ route('guest.division.detail', $division->slug) }}" class="text-decoration-none">
                        <div class="card h-100 shadow-sm hover-card p-3">
                            <div class="card-body text-center">
                                <h5 class="card-title text-dark">{{ $division->title }}</h5>
                                <p class="card-text">{{ $division->region }}</p>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <p class="text-center">Belum ada data divisi tersedia.</p>
            @endforelse
        </div>
    </div>
</div>

@include('footer')
