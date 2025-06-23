@include('head')
@include('header')
<div class="container mx-auto py-6 px-4">
    <h1 class="text-3xl font-bold text-center mb-4">{{ $news->title }}</h1>
    <p class="text-center text-gray-500 text-sm mb-4">
        {{ \Carbon\Carbon::parse($news->created_at)->translatedFormat('d F Y') }}
    </p>

    <div class="flex justify-center mb-6">
        <img src="{{ asset('storage/' . $news->image) }}" alt="{{ $news->title }}"
            class="rounded shadow w-full max-w-3xl object-cover aspect-[3/2]">
    </div>

    <div class="prose max-w-3xl mx-auto mb-6">
        {!! $news->content !!}
    </div>

    <div class="text-center text-sm text-gray-700 mb-6">
        Sumber:
        @if (Str::startsWith($news->source_link, ['http://', 'https://']))
            <a href="{{ $news->source_link }}" target="_blank" class="text-blue-600 underline">
                {{ $news->source_link }}
            </a>
        @else
            {{ $news->source_link }}
        @endif
    </div>
</div>
@include('footer')