<article class="service-card cardx">
    <div class="service-card-art">
        <img class="service-art" src="{{ asset($service->artworkPath()) }}" alt="" width="960" height="600" @if($i > 2) loading="lazy" decoding="async" @else fetchpriority="{{ $i === 0 ? 'high' : 'auto' }}" @endif>
        <span class="service-card-art-icon"><i class="{{ $service->icon ?: 'bi bi-grid-3x3-gap' }}"></i></span>
    </div>
    <div class="service-card-content">
        <span class="number">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
        <h3>{{ $service->title }}</h3>
        <p>{{ $service->short_description ?: $service->description }}</p>
        <h4>Typical deliverables</h4>
        <ul class="service-deliverables">
            @foreach($service->deliverables() as $deliverable)
                <li><i class="bi bi-check2-circle" aria-hidden="true"></i>{{ $deliverable }}</li>
            @endforeach
        </ul>
        <a href="{{ route('service.show', $service) }}">View service details <i class="bi bi-arrow-up-right"></i></a>
    </div>
</article>
