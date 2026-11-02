@extends('layouts.site')
@section('title', $project->title)
@section('description', $project->description ?: 'A selected e-Linx project.')
@section('content')
@include('partials.site-hero', [
    'crumb' => 'Portfolio',
    'eyebrow' => $project->category ?: 'SELECTED PROJECT',
    'title' => $project->title,
    'highlight' => 'Project story',
    'description' => $project->description ?: 'Project details shared by e-Linx.',
    'image' => $project->artworkPath(),
    'imageAlt' => 'Illustration for '.$project->title,
    'imageCaption' => 'Project information shared by e-Linx',
    'ctaText' => 'Discuss a similar project',
    'ctaUrl' => route('quote'),
])
<section class="section portfolio-case-study"><div class="container"><div class="row justify-content-center"><div class="col-xl-9">
    <img class="case-study-artwork" src="{{ asset($project->artworkPath()) }}" alt="Illustration for {{ $project->title }}" loading="lazy">
    @if($project->challenge)<section class="case-study-section"><span class="text-primary fw-bold small">THE CHALLENGE</span><h2>Understanding the need.</h2><p>{{ $project->challenge }}</p></section>@endif
    @if($project->solution)<section class="case-study-section"><span class="text-primary fw-bold small">OUR APPROACH</span><h2>A considered solution.</h2><p>{{ $project->solution }}</p></section>@endif
    @if($project->outcome)<section class="case-study-section"><span class="text-primary fw-bold small">OUTCOME</span><h2>What was delivered.</h2><p>{{ $project->outcome }}</p></section>@endif
    @if(! $project->challenge && ! $project->solution && ! $project->outcome)
        <div class="product-empty"><h2>More project details are being prepared.</h2><p>Contact our team to discuss a similar requirement.</p><a class="btn btn-primary rounded-pill px-4" href="{{ route('quote') }}">Discuss a similar project <i class="bi bi-arrow-up-right"></i></a></div>
    @endif
    <div class="case-study-actions"><a class="btn btn-soft rounded-pill px-4" href="{{ route('portfolio') }}"><i class="bi bi-arrow-left"></i> All projects</a>@if($project->externalUrl())<a class="btn btn-soft rounded-pill px-4" href="{{ $project->externalUrl() }}" target="_blank" rel="noopener noreferrer">Visit project <i class="bi bi-arrow-up-right"></i></a>@endif</div>
</div></div></div></section>
@endsection
