@props(['title', 'subtitle'])
<div class="content-bar flex-grow h-full overflow-y-auto">
    <x-content.header :user="Auth::user()" :title="$title" :subtitle="$subtitle"/>

    <x-content.main>
        {{ $slot }}
    </x-content.main>

    @include('footer')
</div>
