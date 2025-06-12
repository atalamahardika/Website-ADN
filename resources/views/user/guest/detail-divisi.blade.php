@include('head')
@include('header')
<style>
    .transition-card {
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .transition-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
</style>
<div class="detail-divisi py-5">
    <div class="container">
        <h2 class="fw-bold mb-3">{{ $division->title }}</h2>
        <p class="text-muted">Wilayah: <strong>{{ $division->region }}</strong></p>

        @if ($division->description)
            <div class="mb-4">
                <h5 class="fw-semibold">Deskripsi</h5>
                <div class="prose">{!! $division->description !!}</div>
            </div>
        @endif

        <hr>

        <h4 class="fw-bold mt-4 mb-3">Sub Divisi</h4>
        @if ($approvedSubDivisions->isEmpty())
            <p class="text-muted">Belum ada sub divisi yang ditambahkan.</p>
        @else
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @foreach ($approvedSubDivisions as $sub)
                    <div class="col">
                        <a href="{{ route('guest.division.subdivision.detail', ['divisionSlug' => $division->slug, 'subdivisionSlug' => $sub->slug]) }}"
                            class="text-decoration-none text-dark">
                            <div class="card h-100 border-0 shadow-sm rounded-4 transition-card">
                                <div class="card-body">
                                    <h5 class="card-title fw-bold text-success mb-2">
                                        {{ $sub->title }}
                                    </h5>
                                    <div class="text-muted small prose" style="min-height: 80px;">
                                        {!! \Illuminate\Support\Str::limit(strip_tags($sub->description), 150) !!}
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</div>

@include('footer')
