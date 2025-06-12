@include('head')
@include('header')

<div class="detail-subdivisi">
    <div class="container">
        <h2 class="fw-bold my-3">{{ $subdivision->title }}</h2>
        <p class="text-muted">Bagian dari Divisi:
            <a href="{{ route('guest.division.detail', $division->slug) }}"
                class="text-decoration-none text-success fw-semibold">
                {{ $division->title }}
            </a>
        </p>

        <div class="prose">
            {!! $subdivision->description !!}
        </div>
    </div>
</div>

@include('footer')
