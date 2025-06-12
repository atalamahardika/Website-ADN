@include('head')
@include('header')

<div class="container mx-auto py-6 px-4">
    <h1 class="text-3xl font-bold text-center mb-4">{{ $publikasi->title }}</h1>
    <div class="prose mx-auto mb-6">
        {!! $publikasi->content !!}
    </div>
</div>

@include('footer')
