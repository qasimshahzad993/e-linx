@if($faqs->isNotEmpty())
<section class="section section-soft faq-section" id="faqs">
    <div class="container">
        <div class="faq-section-heading">
            <div><span class="text-primary fw-bold small">COMMON QUESTIONS</span><h2 class="section-title mt-2">A few things you may want to know.</h2><p class="section-lead mb-0">Have another question? Send it to our team and we’ll be happy to help.</p></div>
            <a class="btn btn-soft rounded-pill" href="{{ route('faqs') }}">All FAQs <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="accordion faq-accordion" id="home-faq-accordion">
            @foreach($faqs as $index => $faq)
                <div class="accordion-item">
                    <h3 class="accordion-header" id="home-faq-heading-{{ $index }}">
                        <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#home-faq-answer-{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="home-faq-answer-{{ $index }}">{{ $faq->question }}</button>
                    </h3>
                    <div id="home-faq-answer-{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="home-faq-heading-{{ $index }}" data-bs-parent="#home-faq-accordion">
                        <div class="accordion-body">{!! nl2br(e($faq->answer)) !!}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
