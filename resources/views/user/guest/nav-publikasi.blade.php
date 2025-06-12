@include('head')
@include('header')

<style>
    /* Fill the tabs equally */
    .custom-tabs .nav-item {
        margin-bottom: -1px;
    }

    .custom-tabs .nav-link {
        width: 100%;
        border: none;
        border-bottom: 3px solid transparent;
        font-weight: bold;
        color: #000;
        background-color: transparent;
    }

    .custom-tabs .nav-link.active {
        border-bottom: 3px solid #28a745;
        /* hijau */
        color: #28a745;
        background-color: transparent;
    }

    .hover-card:hover {
        transform: translateY(-5px);
        transition: 0.3s;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        border-color: #2e7d32;
    }

    .card {
        border-radius: 10px;
    }
</style>

<div class="container my-5">
    <h1 class="text-center mb-4">Publikasi</h1>

    <ul class="nav nav-tabs custom-tabs justify-content-center" id="myTab" role="tablist">
        <li class="nav-item flex-fill text-center" role="presentation">
            <button class="nav-link active" id="adn-tab" data-bs-toggle="tab" data-bs-target="#adn" type="button"
                role="tab">
                Publikasi ADN
            </button>
        </li>
        <li class="nav-item flex-fill text-center" role="presentation">
            <button class="nav-link" id="mandiri-tab" data-bs-toggle="tab" data-bs-target="#mandiri" type="button"
                role="tab">
                Publikasi Mandiri
            </button>
        </li>
    </ul>

    <div class="tab-content mt-4" id="myTabContent">
        <div class="tab-pane fade show active" id="adn" role="tabpanel">
            <div class="container my-5">
                <div class="row g-4">
                    @forelse ($adn as $item)
                        <div class="col-md-4">
                            <a href="{{ route('guest.publication.adn.detail', $item->slug) }}"
                                class="text-decoration-none">
                                <div class="card h-100 shadow-sm hover-card">
                                    <div class="card-body text-center">
                                        <h5 class="card-title text-dark">{{ $item->title }}</h5>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @empty
                        <p class="text-center mt-4">Belum ada publikasi ADN tersedia.</p>
                    @endforelse
                </div>

            </div>

        </div>

        <div class="tab-pane fade" id="mandiri" role="tabpanel">
            <h4 class="text-center">List Jurnal</h4>

            <ul class="space-y-3">
                @forelse ($mandiri as $pub)
                    <li class="card h-100 shadow-sm hover-card p-4 rounded shadow-sm flex justify-between items-start">
                        <div>
                            {{-- Judul --}}
                            <h5 class="font-semibold mb-1">{{ $pub->title }}</h5>
                            {{-- Format Harvard --}}
                            <p class="text-gray-700">
                                {{ $pub->formatted_authors }} ({{ $pub->year }}), ‘{{ $pub->title }}’,
                                <em>{{ $pub->journal_name }}</em>
                                @if ($pub->volume)
                                    , vol. {{ $pub->volume }}
                                @endif
                                @if ($pub->pages)
                                    , hh. {{ $pub->pages }}
                                @endif.
                            </p>
                            {{-- Link --}}
                            @if ($pub->link)
                                <a href="{{ $pub->link }}" target="_blank" class="text-blue-600 underline">Lihat
                                    publikasi</a>
                            @endif
                        </div>
                    </li>
                @empty
                    <p class="text-center mt-4">Belum ada publikasi member yang tersedia.</p>
                @endforelse
            </ul>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $mandiri->appends(['tab' => 'mandiri'])->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab');

        const tabMandiri = document.querySelector('#mandiri-tab');
        const contentMandiri = document.querySelector('#mandiri');
        const tabAdn = document.querySelector('#adn-tab');
        const contentAdn = document.querySelector('#adn');

        // Handle active tab on reload
        if (activeTab === 'mandiri') {
            tabAdn.classList.remove('active');
            contentAdn.classList.remove('show', 'active');
            tabMandiri.classList.add('active');
            contentMandiri.classList.add('show', 'active');
        }

        // Handle click on mandiri tab
        tabMandiri.addEventListener('click', function() {
            const url = new URL(window.location.href);
            const currentTab = url.searchParams.get('tab');
            const currentPage = url.searchParams.get('page');

            // Jika belum di tab mandiri atau sedang di page > 1, reset
            if (currentTab !== 'mandiri' || currentPage) {
                url.searchParams.set('tab', 'mandiri');
                url.searchParams.delete('page');
                window.location.href = url.toString();
            }
        });

        // Handle click on adn tab
        tabAdn.addEventListener('click', function() {
            const url = new URL(window.location.href);
            const currentTab = url.searchParams.get('tab');

            // Hanya reload jika sedang berada di mandiri
            if (currentTab === 'mandiri') {
                url.searchParams.delete('tab');
                url.searchParams.delete('page');
                window.location.href = url.toString();
            }
        });
    });
</script>


@include('footer')
