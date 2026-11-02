@extends('layouts.site')
@section('title', 'Frequently Asked Questions')
@section('description', 'Answers to common questions about e-Linx services, project proposals, and working with our team.')
@section('content')
@include('partials.site-hero', [
    'crumb' => 'FAQs',
    'eyebrow' => 'HELP & INFORMATION',
    'title' => 'Questions about',
    'highlight' => 'working with e-Linx?',
    'description' => 'Find answers to common questions about our services and project enquiry process. Contact us if you need more information.',
    'image' => 'assets/img/stock/product-design.jpg',
    'imageAlt' => 'A team reviewing a digital product project',
    'imageCaption' => 'Clear answers for your next digital project',
    'ctaText' => 'Ask us a question',
    'ctaUrl' => route('contact'),
])
<section class="section faq-page-section">
    <div class="container">
        @if($faqs->isNotEmpty())
            <div class="accordion faq-accordion" id="faq-page-accordion">
                @foreach($faqs as $index => $faq)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq-page-heading-{{ $index }}">
                            <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq-page-answer-{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="faq-page-answer-{{ $index }}">{{ $faq->question }}</button>
                        </h2>
                        <div id="faq-page-answer-{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="faq-page-heading-{{ $index }}" data-bs-parent="#faq-page-accordion">
                            <div class="accordion-body">{!! nl2br(e($faq->answer)) !!}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="product-empty"><h2>Our FAQs are being updated.</h2><p>Contact our team and we’ll be happy to answer your question.</p><a class="btn btn-primary rounded-pill px-4" href="{{ route('contact') }}">Contact our team <i class="bi bi-arrow-up-right"></i></a></div>
        @endif
        <div class="faq-contact-cta"><div><h2>Still have a question?</h2><p class="mb-0">Tell us what you need to know, or share the outline of a project.</p></div><a class="btn btn-primary rounded-pill px-4" href="{{ route('contact') }}">Get in touch <i class="bi bi-arrow-up-right"></i></a></div>
    </div>
</section>
@if($faqSchema !== [])
<script type="application/ld+json">@json($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)</script>
@endif
@endsection
