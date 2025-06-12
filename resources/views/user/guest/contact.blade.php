@if ($contactTitle && $contactContents)
    <div class="contact-section py-5" id="kontak" style="border-top: 1px solid #30BA7F;">
        <div class="container">
            <h2 class="text-center fw-bold mb-4">{{ $contactTitle->value }}</h2>
            <div class="row justify-content-center">
                <div class="col-md-5">
                    @foreach ($contactContents as $contact)
                        <div class="d-flex align-items-center mb-3">
                            @if ($contact->icon)
                                <img src="{{ asset($contact->icon) }}" alt="icon" style="width: 40px; height: 40px;"
                                    class="me-3">
                            @endif
                            <div class="prose mb-0">
                                {!! $contact->value !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif
