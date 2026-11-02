@extends('layouts.site')
@section('title', 'Request a Quote')
@section('description', 'Share your requirements with e-Linx to discuss a tailored software, website, or digital product proposal.')
@section('content')
@php
    $selectedProduct = old('product', $product);
    $selectedService = old('service', $service);
    $selectedFeatures = old('required_features', []);
    $selectedFeatures = is_array($selectedFeatures) ? $selectedFeatures : [];
@endphp
@include('partials.site-hero', [
    'crumb' => 'Get a quote',
    'eyebrow' => 'PROJECT ENQUIRY',
    'title' => 'Plan your next',
    'highlight' => 'digital project.',
    'description' => $selectedProduct ? 'Share your requirements for '.$selectedProduct.' and our team will help identify the appropriate next steps.' : 'Tell us about your objectives, requirements, and desired outcomes. Our team will review your brief and respond with recommended next steps.',
    'image' => $selectedProduct ? 'assets/img/stock/crm.jpg' : 'assets/img/stock/product-design.jpg',
    'imageAlt' => $selectedProduct ? 'A team discussing customer relationships and CRM workflows' : 'A product team reviewing interface designs',
    'imageCaption' => 'A considered plan for your digital project',
    'ctaText' => 'Complete the project brief',
    'ctaUrl' => '#quote-form',
])
<section class="section quote-form-section"><div class="container"><div class="row justify-content-center"><div class="col-xl-9">
    <div class="quote-form-heading"><span class="quote-form-kicker"><i class="bi bi-clipboard2-check" aria-hidden="true"></i> PROJECT BRIEF</span><h2>Tell us about your project</h2><p>Share your objectives, scope, and priorities. This information helps us prepare a relevant response.</p><div class="quote-form-required"><span>*</span> Required fields</div></div>
    <div class="cardx quote-form-card">
    @if($selectedProduct)
        <div class="selected-product mb-4"><i class="bi bi-box-seam" aria-hidden="true"></i><span><small>PRODUCT ENQUIRY</small><strong>{{ $selectedProduct }}</strong></span><a href="{{ route('products') }}">Choose another</a></div>
    @endif
    <form method="post" action="{{ route('quote.store') }}" id="quote-form">
        @csrf
        <div class="hp-field" aria-hidden="true"><label for="quote-company-website">Leave this field empty</label><input id="quote-company-website" name="company_website" tabindex="-1" autocomplete="off"></div>
        @if($selectedProduct)<input type="hidden" name="product" value="{{ $selectedProduct }}">@endif
        <section class="quote-form-group" aria-labelledby="quote-contact-heading">
            <div class="quote-form-group-heading"><span class="quote-form-step">01</span><div><h3 id="quote-contact-heading">Your contact details</h3><p>How can our team get back to you?</p></div></div>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label" for="name">Full name <span class="required-mark">*</span></label><input id="name" name="name" class="form-control" autocomplete="name" required maxlength="160" value="{{ old('name') }}" placeholder="Your name">@error('name')<div class="quote-field-error">{{ $message }}</div>@enderror</div>
                <div class="col-md-6"><label class="form-label" for="email">Business email <span class="required-mark">*</span></label><input id="email" type="email" name="email" class="form-control" autocomplete="email" required maxlength="190" value="{{ old('email') }}" placeholder="you@company.com">@error('email')<div class="quote-field-error">{{ $message }}</div>@enderror</div>
                <div class="col-md-6"><label class="form-label" for="company_name">Company / organization <span class="optional-label">Optional</span></label><input id="company_name" name="company_name" class="form-control" autocomplete="organization" maxlength="180" value="{{ old('company_name') }}" placeholder="Your organization"></div>
                <div class="col-md-6"><label class="form-label" for="phone">Phone / WhatsApp <span class="optional-label">Optional</span></label><input id="phone" name="phone" class="form-control" autocomplete="tel" maxlength="80" value="{{ old('phone') }}" placeholder="+92 300 1234567"></div>
            </div>
        </section>
        <section class="quote-form-group" aria-labelledby="quote-project-heading">
            <div class="quote-form-group-heading"><span class="quote-form-step">02</span><div><h3 id="quote-project-heading">What would you like us to build?</h3><p>Choose a service and tell us about the outcome you need.</p></div></div>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label" for="service">Service you’re interested in <span class="required-mark">*</span></label><select id="service" name="service" class="form-select" required><option value="">Choose a service</option>@if($selectedProduct)<option value="Product enquiry" @selected($selectedService === 'Product enquiry')>I’m enquiring about {{ $selectedProduct }}</option>@endif @foreach($serviceOptions as $option)<option value="{{ $option->title }}" data-category="{{ $option->category ?? '' }}" @selected($selectedService === $option->title)>{{ $option->title }}</option>@endforeach<option value="Not sure yet — please advise" @selected($selectedService === 'Not sure yet — please advise')>Not sure yet — please advise</option></select><div class="quote-field-hint">Not sure? Choose the last option and we’ll help you decide.</div>@error('service')<div class="quote-field-error">{{ $message }}</div>@enderror</div>
                <div class="col-md-6"><label class="form-label" for="project_name">Project name <span class="optional-label">Optional</span></label><input id="project_name" name="project_name" class="form-control" maxlength="180" value="{{ old('project_name') }}" placeholder="Enter a project name, if available"></div>
                <div class="col-12"><label class="form-label" for="message">Project overview <span class="required-mark">*</span></label><textarea id="message" name="message" rows="5" class="form-control" required maxlength="10000" placeholder="Outline your objectives, requirements, and the challenge you would like to address.">{{ old('message') }}</textarea><div class="quote-field-hint">A brief outline of your objectives and requirements is a useful starting point.</div>@error('message')<div class="quote-field-error">{{ $message }}</div>@enderror</div>
                <div class="col-12"><span class="form-label d-block">What might the project include? <span class="optional-label">Select any that apply</span></span><div class="quote-feature-grid">
                    @foreach($featureOptions as $feature)
                        <label class="quote-feature-option"><input type="checkbox" name="required_features[]" value="{{ $feature }}" @checked(in_array($feature, $selectedFeatures, true))><span class="quote-feature-check"><i class="bi bi-check2" aria-hidden="true"></i></span><span>{{ $feature }}</span></label>
                    @endforeach
                </div>@error('required_features')<div class="quote-field-error">{{ $message }}</div>@enderror @error('required_features.*')<div class="quote-field-error">{{ $message }}</div>@enderror</div>
            </div>
            <section class="service-specific-fields mt-4" data-service-category="Mystery Shopping" hidden aria-labelledby="mystery-scope-heading">
                <div class="quote-form-group-heading"><span class="quote-form-step"><i class="bi bi-binoculars" aria-hidden="true"></i></span><div><h3 id="mystery-scope-heading">Mystery Shopping scope</h3><p>Optional details to help plan locations, channels, and frequency.</p></div></div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="mystery-locations">Locations or branches</label><input id="mystery-locations" class="form-control" name="service_details[locations]" maxlength="500" value="{{ old('service_details.locations') }}" placeholder="Cities, regions, or branch count"></div>
                    <div class="col-md-6"><label class="form-label" for="mystery-frequency">Assessment frequency</label><select id="mystery-frequency" class="form-select" name="service_details[frequency]"><option value="">Choose if known</option>@foreach($mysteryShoppingFrequencies as $frequency)<option value="{{ $frequency }}" @selected(old('service_details.frequency') === $frequency)>{{ $frequency }}</option>@endforeach</select></div>
                    <div class="col-12"><span class="form-label d-block">Customer touchpoints to review</span><div class="quote-feature-grid">@foreach($mysteryShoppingChannels as $channel)<label class="quote-feature-option"><input type="checkbox" name="service_details[channels][]" value="{{ $channel }}" @checked(in_array($channel, old('service_details.channels', []), true))><span class="quote-feature-check"><i class="bi bi-check2" aria-hidden="true"></i></span><span>{{ $channel }}</span></label>@endforeach</div></div>
                </div>
            </section>
            <section class="service-specific-fields mt-4" data-service-category="AI Solutions" hidden aria-labelledby="ai-scope-heading">
                <div class="quote-form-group-heading"><span class="quote-form-step"><i class="bi bi-robot" aria-hidden="true"></i></span><div><h3 id="ai-scope-heading">AI solution scope</h3><p>Tell us about the task, systems, and human oversight you need.</p></div></div>
                <div class="row g-3">
                    <div class="col-12"><label class="form-label" for="ai-workflow">What task or workflow should the solution help with?</label><textarea id="ai-workflow" class="form-control" name="service_details[ai_workflow]" rows="3" maxlength="3000" placeholder="Describe the current process and the intended outcome.">{{ old('service_details.ai_workflow') }}</textarea></div>
                    <div class="col-md-6"><label class="form-label" for="ai-systems">Systems or approved information sources</label><textarea id="ai-systems" class="form-control" name="service_details[ai_systems]" rows="3" maxlength="1000" placeholder="For example, a help centre or internal CRM.">{{ old('service_details.ai_systems') }}</textarea></div>
                    <div class="col-md-6"><label class="form-label" for="ai-human-review">Human review or escalation requirements</label><textarea id="ai-human-review" class="form-control" name="service_details[human_review]" rows="3" maxlength="1000" placeholder="Where should a person review, approve, or take over?">{{ old('service_details.human_review') }}</textarea></div>
                    <div class="col-12"><p class="quote-field-hint">Do not submit passwords, private customer records, or sensitive production data.</p></div>
                </div>
            </section>
            <noscript><p class="quote-field-hint mt-3">JavaScript is disabled. Include any Mystery Shopping or AI-specific requirements in the project overview.</p></noscript>
        </section>
        <section class="quote-form-group" aria-labelledby="quote-planning-heading">
            <div class="quote-form-group-heading"><span class="quote-form-step">03</span><div><h3 id="quote-planning-heading">Planning and scope</h3><p>These details help us recommend a realistic approach.</p></div></div>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label" for="project_stage">Where are you in the process? <span class="required-mark">*</span></label><select id="project_stage" name="project_stage" class="form-select" required><option value="">Choose project stage</option>@foreach($projectStages as $option)<option value="{{ $option }}" @selected(old('project_stage') === $option)>{{ $option }}</option>@endforeach</select>@error('project_stage')<div class="quote-field-error">{{ $message }}</div>@enderror</div>
                <div class="col-md-6"><label class="form-label" for="timeline">When would you like to start? <span class="required-mark">*</span></label><select id="timeline" name="timeline" class="form-select" required><option value="">Choose a timeline</option>@foreach($projectTimelines as $option)<option value="{{ $option }}" @selected(old('timeline') === $option)>{{ $option }}</option>@endforeach</select>@error('timeline')<div class="quote-field-error">{{ $message }}</div>@enderror</div>
                <div class="col-md-6"><label class="form-label" for="budget">Estimated budget <span class="required-mark">*</span></label><select id="budget" name="budget" class="form-select" required><option value="">Choose a range</option>@foreach($budgetOptions as $option)<option value="{{ $option }}" @selected(old('budget', 'Not sure yet') === $option)>{{ $option }}</option>@endforeach</select><div class="quote-field-hint">A rough range helps us suggest the right scope.</div>@error('budget')<div class="quote-field-error">{{ $message }}</div>@enderror</div>
                <div class="col-md-6"><label class="form-label" for="target_audience">Who will use it? <span class="optional-label">Optional</span></label><input id="target_audience" name="target_audience" class="form-control" maxlength="500" value="{{ old('target_audience') }}" placeholder="Customers, staff, students…"></div>
                <div class="col-12"><label class="form-label" for="reference_url">Reference link <span class="optional-label">Optional</span></label><input id="reference_url" type="url" name="reference_url" class="form-control" maxlength="500" value="{{ old('reference_url') }}" placeholder="https://example.com"><div class="quote-field-hint">Add a current website, example, or design link if you have one.</div>@error('reference_url')<div class="quote-field-error">{{ $message }}</div>@enderror</div>
            </div>
        </section>
        <div class="quote-submit-row"><div><strong>Ready to submit your enquiry?</strong><span>Our team will review your brief and follow up with you.</span></div><button type="submit" class="btn btn-primary btn-lg">Submit project brief <i class="bi bi-arrow-up-right" aria-hidden="true"></i></button></div>
    </form>
</div></div></div></div></section>
@endsection
@push('scripts')
<script>
    (() => {
        const serviceSelect = document.getElementById('service');
        const detailGroups = document.querySelectorAll('.service-specific-fields');
        if (!serviceSelect || !detailGroups.length) return;
        const updateServiceFields = () => {
            const category = serviceSelect.selectedOptions[0]?.dataset.category || '';
            detailGroups.forEach((group) => {
                const visible = group.dataset.serviceCategory === category;
                group.hidden = !visible;
                group.querySelectorAll('input, select, textarea').forEach((field) => {
                    field.disabled = !visible;
                });
            });
        };
        serviceSelect.addEventListener('change', updateServiceFields);
        updateServiceFields();
    })();
</script>
@endpush
