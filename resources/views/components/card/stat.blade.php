@props(['title', 'count'])

<div class="p-6 rounded-xl text-white shadow-md flex flex-col justify-center items-center" style="background: linear-gradient(to right, #15400E, #006400);">
    <h3 class="text-xl font-semibold mb-2" style="color: white !important;">{{ $title }}</h3>
    <p class="text-3xl font-bold">{{ $count }}</p>
</div>
