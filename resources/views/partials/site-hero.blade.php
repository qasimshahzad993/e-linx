<section class="site-hero">
    <div class="site-hero-orb site-hero-orb-one"></div>
    <div class="site-hero-orb site-hero-orb-two"></div>
    <div class="container position-relative">
        <div class="row align-items-center g-4 g-lg-5">
            <div class="col-lg-5">
                @if(!empty($crumb))
                    <div class="site-hero-crumb"><a href="{{ route('home') }}">Home</a><span aria-hidden="true">/</span>{{ $crumb }}</div>
                @endif
                <span class="site-hero-eyebrow"><i class="bi bi-code-slash" aria-hidden="true"></i>{{ $eyebrow }}</span>
                <h1>
                    {{ $title }}
                    @if(!empty($highlight))
                        <span @if(!empty($rotatePhrases)) data-rotating-phrases='@json($rotatePhrases)' @endif>{{ $highlight }}</span>
                    @endif
                </h1>
                @if(!empty($description))
                    <p class="site-hero-copy">{{ $description }}</p>
                @endif
                @if(!empty($ctaText) && !empty($ctaUrl))
                    <div class="site-hero-actions">
                        <a class="btn btn-primary btn-lg rounded-pill px-4" href="{{ $ctaUrl }}">{{ $ctaText }} <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
                        @if(!empty($secondaryText) && !empty($secondaryUrl))
                            <a class="btn btn-outline-light btn-lg rounded-pill px-4" href="{{ $secondaryUrl }}" @if(!empty($secondaryExternal)) target="_blank" rel="noopener noreferrer" @endif>{{ $secondaryText }}</a>
                        @endif
                    </div>
                @endif
                @if(!empty($points))
                    <ul class="site-hero-points">
                        @foreach($points as $point)
                            <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i>{{ $point }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="col-lg-7">
                <div class="site-hero-art">
                    <div class="carousel slide site-hero-carousel" id="site-hero-carousel" role="region" aria-roledescription="carousel" aria-label="e-Linx services">
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#site-hero-carousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Show slide 1"></button>
                            <button type="button" data-bs-target="#site-hero-carousel" data-bs-slide-to="1" aria-label="Show slide 2"></button>
                            <button type="button" data-bs-target="#site-hero-carousel" data-bs-slide-to="2" aria-label="Show slide 3"></button>
                        </div>
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="{{ asset($image) }}" alt="{{ $imageAlt }}" width="960" height="600" fetchpriority="high">
                                <span class="site-hero-slide-caption"><span></span>{{ $imageCaption }}</span>
                            </div>
                            <div class="carousel-item">
                                <img src="{{ asset('assets/img/stock/mobile-apps.jpg') }}" alt="A person using a smartphone app beside a computer" width="960" height="600" fetchpriority="low" decoding="async">
                                <span class="site-hero-slide-caption"><span></span>Mobile experiences designed around people</span>
                            </div>
                            <div class="carousel-item">
                                <img src="{{ asset('assets/img/stock/product-design.jpg') }}" alt="A team reviewing digital product design work" width="960" height="600" fetchpriority="low" decoding="async">
                                <span class="site-hero-slide-caption"><span></span>Product design from idea to interface</span>
                            </div>
                        </div>
                        <button class="carousel-control-prev site-hero-carousel-control" type="button" data-bs-target="#site-hero-carousel" data-bs-slide="prev" aria-label="Previous image">
                            <span class="carousel-control-icon"><i class="bi bi-arrow-left" aria-hidden="true"></i></span>
                        </button>
                        <button class="carousel-control-next site-hero-carousel-control" type="button" data-bs-target="#site-hero-carousel" data-bs-slide="next" aria-label="Next image">
                            <span class="carousel-control-icon"><i class="bi bi-arrow-right" aria-hidden="true"></i></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@push('scripts')
<script>
    document.querySelectorAll('.site-hero-carousel').forEach((carousel) => {
        const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        new bootstrap.Carousel(carousel, {
            interval: reducedMotion ? false : 5500,
            ride: reducedMotion ? false : 'carousel',
            pause: 'hover',
            touch: true,
        });
    });
</script>
@endpush
