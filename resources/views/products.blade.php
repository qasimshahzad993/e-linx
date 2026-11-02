@extends('layouts.site')
@section('title', 'Products')
@section('description', 'Explore e-Linx software for customer relationship management, commerce, team operations, and business analytics.')
@section('content')
@include('partials.site-hero', [
    'crumb' => 'Products',
    'eyebrow' => 'THE E-LINX PRODUCT SUITE',
    'title' => 'Business software.',
    'highlight' => 'Designed for the way you work.',
    'description' => 'Explore connected tools for customer relationships, commerce, operations, and analytics—built to support your team’s day-to-day work.',
    'image' => 'assets/img/stock/insight.jpg',
    'imageAlt' => 'Laptop showing a business analytics dashboard with reports and charts',
    'imageCaption' => 'Actionable insight for your business',
    'ctaText' => 'Discuss your requirements',
    'ctaUrl' => route('quote'),
    'points' => ['Designed for growing teams', 'Adaptable to your workflows'],
])
<section class="product-catalog section section-soft" id="catalog">
    <div class="container">
        <div class="row align-items-end g-3 mb-4">
            <div class="col-lg-7"><span class="text-primary fw-bold small">E-LINX PRODUCTS</span><h2 class="section-title mt-2">Software for essential business workflows.</h2><p class="section-lead mb-0">Compare tools for customer management, online commerce, team operations, and business insight.</p></div>
            <div class="col-lg-5"><label class="product-search-label" for="product-search">Search products</label><div class="product-search"><i class="bi bi-search" aria-hidden="true"></i><input id="product-search" type="search" class="form-control" placeholder="Try CRM, commerce, analytics..." autocomplete="off"><button type="button" id="clear-product-search" aria-label="Clear product search" hidden><i class="bi bi-x-circle"></i></button></div></div>
        </div>
        @if($products->isNotEmpty())
            @php
                $categories = $products->pluck('category')->filter()->unique()->values();
            @endphp
            @if($categories->isNotEmpty())
                <div class="product-filters" role="group" aria-label="Filter products by category">
                    <button type="button" class="product-filter active" data-filter="all" aria-pressed="true">All products <span>{{ $products->count() }}</span></button>
                    @foreach($categories as $category)<button type="button" class="product-filter" data-filter="{{ \Illuminate\Support\Str::lower($category) }}" aria-pressed="false">{{ $category }}</button>@endforeach
                </div>
            @endif
        @endif
        <div class="row g-4 product-grid" id="product-grid" aria-live="polite">
            @forelse($products as $product)
                @php
                    $productSlug = \Illuminate\Support\Str::slug($product->name);
                    $productArtwork = match ($productSlug) {
                        'e-linx-crm' => 'crm.jpg',
                        'e-linx-commerce' => 'commerce.jpg',
                        'e-linx-desk' => 'desk.jpg',
                        'e-linx-insight' => 'insight.jpg',
                        default => 'web-platforms.jpg',
                    };
                    $productHighlights = match ($productSlug) {
                        'e-linx-crm' => ['Lead and customer records', 'Sales pipeline visibility', 'Tasks and team follow-up'],
                        'e-linx-commerce' => ['Product and category management', 'Order and customer workflows', 'Flexible storefront foundations'],
                        'e-linx-desk' => ['Track internal requests', 'Organize repeatable workflows', 'See work and ownership clearly'],
                        'e-linx-insight' => ['Bring key measures together', 'Explore operational trends', 'Support decisions with context'],
                        default => ['Adaptable business workflows', 'Role-aware team access', 'Room to grow with your needs'],
                    };
                    $productSearch = \Illuminate\Support\Str::lower(implode(' ', [$product->name, $product->category, $product->description]));
                @endphp
                <div class="col-md-6 col-xl-4 product-item" data-category="{{ \Illuminate\Support\Str::lower($product->category ?? '') }}" data-search="{{ $productSearch }}">
                    <article class="product-card">
                        <div class="product-card-art">
                            <img src="{{ asset('assets/img/stock/'.$productArtwork) }}" alt="Photo illustrating {{ $product->name }}" width="960" height="600" loading="lazy" decoding="async">
                            @if($product->category)<span class="product-category">{{ $product->category }}</span>@endif
                        </div>
                        <div class="product-card-body">
                            <div class="product-card-title"><span class="product-card-icon"><i class="{{ $product->icon ?: 'bi bi-box-seam' }}" aria-hidden="true"></i></span><h3>{{ $product->name }}</h3></div>
                            <p>{{ $product->description ?: 'A flexible software platform shaped around the needs of your team.' }}</p>
                            <ul class="product-highlights">
                                @foreach($productHighlights as $highlight)
                                    <li><i class="bi bi-check2" aria-hidden="true"></i>{{ $highlight }}</li>
                                @endforeach
                            </ul>
                            <a class="product-card-link" href="{{ route('quote', ['product' => $product->name]) }}">Enquire about this product <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12"><div class="product-empty"><span><i class="bi bi-box-seam"></i></span><h3>Our product catalogue is being updated.</h3><p>Contact our team to discuss your requirements and explore a suitable solution.</p><a class="btn btn-primary rounded-pill px-4" href="{{ route('contact') }}">Contact our team <i class="bi bi-arrow-up-right"></i></a></div></div>
            @endforelse
            <div class="col-12 product-no-results" id="product-no-results" hidden><div class="product-empty"><span><i class="bi bi-search"></i></span><h3>No matching products</h3><p>Try another search or choose a different category.</p><button class="btn btn-soft rounded-pill px-4" type="button" id="reset-product-filters">Show all products</button></div></div>
        </div>
    </div>
</section>
<section class="section product-cta"><div class="container"><div class="row align-items-center g-4"><div class="col-lg-8"><span class="products-eyebrow"><i class="bi bi-chat-square-text"></i> LOOKING FOR A TAILORED SOLUTION?</span><h2 class="section-title mt-3">Let’s find the right fit for your requirements.</h2><p class="text-white-50 mb-0">Tell us about your workflows, integration needs, and business objectives. We’ll recommend a practical next step.</p></div><div class="col-lg-4 text-lg-end"><a class="btn btn-primary btn-lg rounded-pill px-4" href="{{ route('quote') }}">Discuss your requirements <i class="bi bi-arrow-up-right"></i></a></div></div></div></section>
@endsection
@push('scripts')
<script src="{{ asset('assets/js/products.js') }}" defer></script>
@endpush
