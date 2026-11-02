@extends('layouts.site')
@section('title', $service->title)
@section('description', $service->short_description ?: $service->description)
@section('content')
@include('partials.site-hero', [
    'crumb' => 'Services / '.$service->title,
    'eyebrow' => 'OUR SERVICES',
    'title' => $service->title,
    'highlight' => 'aligned with your goals.',
    'description' => $service->short_description ?: $service->description,
    'image' => $service->artworkPath(),
    'imageAlt' => 'Photo representing '.$service->title,
    'imageCaption' => 'A clear scope, tailored to your requirements',
    'ctaText' => 'Request a quote',
    'ctaUrl' => route('quote', ['service' => $service->title]),
    'secondaryText' => 'All services',
    'secondaryUrl' => route('services'),
])
<section class="section">
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-lg-7">
                <img class="service-detail-art mb-4" src="{{ asset($service->artworkPath()) }}" alt="Photo representing {{ $service->title }}" width="960" height="600" loading="lazy" decoding="async">
                <h2 class="section-title">A well-defined process, from planning to delivery.</h2>
                <p class="section-lead mt-3">{{ $service->description ?: $service->short_description }}</p>
                <div class="service-detail-deliverables mt-4">
                    <h3>Typical deliverables</h3>
                    <ul>@foreach($service->deliverables() as $deliverable)<li><i class="bi bi-check2-circle" aria-hidden="true"></i>{{ $deliverable }}</li>@endforeach</ul>
                </div>
                <div class="row g-3 mt-3">
                    @foreach($service->deliverySteps() as $step)
                        <div class="col-md-6"><div class="cardx"><h3 class="h5">{{ $step['title'] }}</h3><p class="small text-secondary mb-0">{{ $step['description'] }}</p></div></div>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-5"><div class="cardx section-soft"><h2 class="h4">Discuss your requirements</h2><p class="small text-secondary">Share your goals and constraints. We’ll help define a suitable scope and next steps.</p>@if(in_array($service->category, ['Mystery Shopping', 'AI Solutions'], true))<p class="small text-secondary">Availability, required approvals, safeguards, and delivery details will be reviewed with you before any work is agreed.</p>@endif<a class="btn btn-primary w-100 rounded-pill" href="{{ route('quote', ['service' => $service->title]) }}">Request a proposal</a></div></div>
        </div>
    </div>
</section>
@endsection
