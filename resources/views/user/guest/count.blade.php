<style>
    .counter {
        font-size: 2.5rem;
        color: #14532d;
        font-weight: bold;
    }

    .count-section img {
        margin-bottom: 10px;
    }
</style>

<div class="count-section py-5 my-4" style="background-color: rgba(219, 234, 213, 0.35); margin-bottom: 40px;">
    <div class="container">
        <div class="row text-center justify-content-center">
            <div class="col-md-4 flex flex-column align-items-center">
                <img src="{{ asset('icon/group.png') }}" alt="Member" style="height: 50px;">
                <h2 class="counter mt-2" data-target="{{ $memberCount }}">0</h2>
                <p>Member</p>
            </div>
            <div class="col-md-4 flex flex-column align-items-center">
                <img src="{{ asset('icon/file.png') }}" alt="Publication" style="height: 50px;">
                <h2 class="counter mt-2" data-target="{{ $publicationCount }}">0</h2>
                <p>Publikasi Member</p>
            </div>
            <div class="col-md-4 flex flex-column align-items-center">
                <img src="{{ asset('icon/newspaper-folded.png') }}" alt="News" style="height: 50px;">
                <h2 class="counter mt-2" data-target="{{ $newsCount }}">0</h2>
                <p>Berita</p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const counters = document.querySelectorAll('.counter');

        const startCounting = (counter) => {
            const target = +counter.getAttribute('data-target');
            let count = 0;

            const updateCount = () => {
                const increment = target / 200;
                if (count < target) {
                    count += increment;
                    counter.innerText = Math.ceil(count);
                    setTimeout(updateCount, 10);
                } else {
                    counter.innerText = target;
                }
            };

            updateCount();
        };

        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counter = entry.target;
                    startCounting(counter);
                    obs.unobserve(counter); // Hanya jalan sekali
                }
            });
        }, {
            threshold: 0.6 // saat 60% elemen terlihat
        });

        counters.forEach(counter => {
            observer.observe(counter);
        });
    });
</script>
