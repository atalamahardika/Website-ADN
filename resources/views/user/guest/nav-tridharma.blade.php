@include('head')
@include('header')
<div class="tridharma-section mt-5 mb-5">
    <div class="container">
        <h2 class="text-center mb-4">Tridharma Perguruan Tinggi</h2>

        @forelse ($tridharma as $item)
            <div class="text-wrapper mb-4">
                <h5>{{ $loop->iteration }}. {{ $item->title }}</h5>
                <div class="prose">
                    {!! $item->content !!}
                </div>
                {{-- <p>{!! nl2br(e($item->content)) !!}</p> --}}
            </div>
        @empty
            <p class="text-center text-muted">Belum ada data Tridharma tersedia.</p>
        @endforelse
    </div>
</div>

@include('footer')