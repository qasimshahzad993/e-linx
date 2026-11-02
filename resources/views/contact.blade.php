@extends('layouts.site')
@section('title', 'Contact')
@section('description', 'Contact e-Linx to discuss your business objectives, software requirements, or upcoming digital project.')
@section('content')
@include('partials.site-hero', [
    'crumb' => 'Contact',
    'eyebrow' => 'CONTACT E-LINX',
    'title' => 'Discuss your',
    'highlight' => 'digital requirements.',
    'description' => 'Share your objectives and requirements with our team. We’ll review your enquiry and recommend a clear next step.',
    'image' => 'assets/img/stock/mobile-apps.jpg',
    'imageAlt' => 'A person using a smartphone application beside a computer',
    'imageCaption' => 'A clear understanding of your objectives',
    'ctaText' => 'Send us a message',
    'ctaUrl' => '#contact-form',
])
<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5">
                <span class="text-primary fw-bold small">GET IN TOUCH</span>
                <h2 class="section-title mt-2">How can we support your next project?</h2>
                <p class="section-lead mt-3">Provide a brief overview of your requirements. Our team will review your enquiry and get back to you.</p>
                <div class="mt-4">
                    <p><i class="bi bi-envelope me-2 text-primary" aria-hidden="true"></i><a href="mailto:{{ config('mail.notifications.to') }}">{{ config('mail.notifications.to') }}</a></p>
                    @php($whatsAppNumber = preg_replace('/\D+/', '', (string) config('services.whatsapp.number')))
                    @if($whatsAppNumber)
                        <p><i class="bi bi-whatsapp me-2 text-primary" aria-hidden="true"></i><a href="https://wa.me/{{ $whatsAppNumber }}" target="_blank" rel="noopener noreferrer">Chat with us on WhatsApp</a></p>
                    @endif
                    <p><i class="bi bi-globe2 me-2 text-primary" aria-hidden="true"></i>Based in Pakistan · Serving clients worldwide</p>
                </div>
            </div>
            <div class="col-lg-7" id="contact-form">
                <div class="cardx">
                    <form method="post" action="{{ route('contact.store') }}">
                        @csrf
                        <div class="hp-field" aria-hidden="true"><label for="contact-company-website">Leave this field empty</label><input id="contact-company-website" name="company_website" tabindex="-1" autocomplete="off"></div>
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label" for="name">Full name <span class="required-mark">*</span></label><input id="name" name="name" class="form-control" required maxlength="160" autocomplete="name" value="{{ old('name') }}" placeholder="Your full name">@error('name')<div class="quote-field-error">{{ $message }}</div>@enderror</div>
                            <div class="col-md-6"><label class="form-label" for="email">Business email <span class="required-mark">*</span></label><input id="email" type="email" name="email" class="form-control" required maxlength="190" autocomplete="email" value="{{ old('email') }}" placeholder="you@company.com">@error('email')<div class="quote-field-error">{{ $message }}</div>@enderror</div>
                            <div class="col-md-6"><label class="form-label" for="phone">Phone <span class="optional-label">Optional</span></label><input id="phone" name="phone" class="form-control" maxlength="80" autocomplete="tel" value="{{ old('phone') }}" placeholder="+92 300 1234567">@error('phone')<div class="quote-field-error">{{ $message }}</div>@enderror</div>
                            <div class="col-md-6"><label class="form-label" for="subject">Subject or service <span class="optional-label">Optional</span></label><input id="subject" name="subject" class="form-control" maxlength="255" value="{{ old('subject') }}" placeholder="How can we help?">@error('subject')<div class="quote-field-error">{{ $message }}</div>@enderror</div>
                            <div class="col-12"><label class="form-label" for="message">How can we help? <span class="required-mark">*</span></label><textarea id="message" name="message" rows="6" class="form-control" required maxlength="10000" placeholder="Describe your project, requirements, or question.">{{ old('message') }}</textarea>@error('message')<div class="quote-field-error">{{ $message }}</div>@enderror</div>
                            <div class="col-12"><button type="submit" class="btn btn-primary rounded-pill px-4">Submit enquiry <i class="bi bi-send ms-1" aria-hidden="true"></i></button></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
