@extends('layouts.site')
@section('title', $course->title)
@section('description', $course->summary)
@section('content')
@include('partials.site-hero', [
    'crumb' => 'Courses',
    'eyebrow' => $course->category ?: 'DIGITAL SKILLS & TRAINING',
    'title' => $course->title,
    'highlight' => 'Course outline',
    'description' => $course->summary,
    'image' => 'assets/img/stock/product-design.jpg',
    'imageAlt' => 'A team learning digital skills',
    'imageCaption' => 'Ask us about course availability',
    'ctaText' => 'Enquire about this course',
    'ctaUrl' => '#course-enquiry',
])
<section class="section"><div class="container"><div class="row g-5">
    <div class="col-lg-7">
        <span class="text-primary fw-bold small">ABOUT THIS COURSE</span>
        <h2 class="section-title mt-2">{{ $course->title }}</h2>
        <p class="section-lead">{{ $course->description }}</p>
        <div class="course-confirmed-details">
            @if($course->duration)<span><i class="bi bi-clock" aria-hidden="true"></i>{{ $course->duration }}</span>@endif
            @if($course->delivery_mode)<span><i class="bi bi-laptop" aria-hidden="true"></i>{{ $course->delivery_mode }}</span>@endif
            @if($course->price)<span><i class="bi bi-tag" aria-hidden="true"></i>{{ $course->price }}</span>@endif
        </div>
        @if($course->curriculumItems() !== [])
            <div class="course-curriculum"><h3>Course outline</h3><ul>@foreach($course->curriculumItems() as $item)<li><i class="bi bi-check2-circle" aria-hidden="true"></i>{{ $item }}</li>@endforeach</ul></div>
        @endif
    </div>
    <div class="col-lg-5" id="course-enquiry"><div class="cardx course-enquiry-card">
        <span class="text-primary fw-bold small">ASK ABOUT AVAILABILITY</span><h2 class="h3 mt-2">Interested in this course?</h2>
        <p class="text-secondary">Send an enquiry and our team will follow up with confirmed details.</p>
        <form method="post" action="{{ route('courses.enquiry', $course) }}">@csrf
            <div class="hp-field" aria-hidden="true"><label for="course-company-website">Leave this field empty</label><input id="course-company-website" name="company_website" tabindex="-1" autocomplete="off"></div>
            <div class="field"><label for="course-name">Full name</label><input class="input form-control" id="course-name" name="name" required maxlength="160" autocomplete="name" value="{{ old('name') }}"></div>
            <div class="field"><label for="course-email">Email</label><input class="input form-control" id="course-email" name="email" type="email" required maxlength="190" autocomplete="email" value="{{ old('email') }}"></div>
            <div class="field"><label for="course-phone">Phone / WhatsApp <span class="optional-label">Optional</span></label><input class="input form-control" id="course-phone" name="phone" maxlength="80" autocomplete="tel" value="{{ old('phone') }}"></div>
            <div class="field"><label for="course-message">What would you like to know?</label><textarea class="input form-control" id="course-message" name="message" required maxlength="10000" rows="4" placeholder="Ask about upcoming dates, course content, or availability.">{{ old('message', 'I would like to ask about availability and details for '.$course->title.'.') }}</textarea></div>
            <button class="btn btn-primary rounded-pill px-4" type="submit">Send course enquiry <i class="bi bi-arrow-up-right" aria-hidden="true"></i></button>
        </form>
    </div></div>
</div></div></section>
@endsection
