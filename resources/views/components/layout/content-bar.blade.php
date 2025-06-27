@props(['title', 'subtitle'])
<div class="content-bar flex-fill d-flex flex-column h-100 overflow-auto">
    <x-content.header :user="Auth::user()" :title="$title" :subtitle="$subtitle"/>

    <x-content.main>
        {{ $slot }}
    </x-content.main>

    @include('footer')
</div>
