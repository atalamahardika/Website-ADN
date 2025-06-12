@props(['id', 'title'])

<div id="{{ $id }}" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center overflow-auto">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl mx-4 my-8 relative">
        <div class="p-6 max-h-[90vh] overflow-y-auto">
            <h2 class="text-xl font-bold mb-4 text-center">{{ $title }}</h2>
            
            {{ $slot }}

            <button onclick="document.getElementById('{{ $id }}').classList.add('hidden')" 
                    class="absolute top-2 right-2 text-gray-600 hover:text-black text-xl">
                &times;
            </button>
        </div>
    </div>
</div>
