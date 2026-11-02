@extends('layouts.site')
@section('title', 'Blog & Insights')
@section('description', 'Insights from e-Linx on software engineering, product design, and delivering effective digital experiences.')
@section('content')
<header class="blog-hero">
    <div class="container">
        <span class="blog-eyebrow"><i class="bi bi-journal-richtext" aria-hidden="true"></i> THE E-LINX JOURNAL</span>
        <h1>Perspectives on<br><span>building better digital products.</span></h1>
        <p>Explore insights on software engineering, product design, and technology decisions that help businesses deliver meaningful digital experiences.</p>
    </div>
</header>
<section class="section blog-listing">
    <div class="container">
        <div class="blog-listing-head">
            <div><span class="blog-section-label">INSIGHTS AND UPDATES</span><h2>Latest articles</h2></div>
            <span class="blog-count">{{ $posts->total() }} {{ \Illuminate\Support\Str::plural('article', $posts->total()) }}</span>
        </div>
        @if($posts->isNotEmpty())
            <div class="blog-grid">
                @foreach($posts as $post)
                    @include('blog.partials.card', ['post' => $post])
                @endforeach
            </div>
            <div class="blog-pagination">{{ $posts->links() }}</div>
        @else
            <div class="blog-empty"><span><i class="bi bi-journal-text" aria-hidden="true"></i></span><h2>New articles are on the way.</h2><p>Our team is preparing insights for business leaders and product teams. Please check back soon.</p><a class="btn btn-primary rounded-pill px-4" href="{{ route('contact') }}">Contact our team <i class="bi bi-arrow-up-right"></i></a></div>
        @endif
    </div>
</section>
@endsection
