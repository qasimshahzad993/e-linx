@extends('layouts.site')
@section('title', $post->seo_title ?: $post->title)
@section('description', $post->seo_description ?: $post->excerpt)
@section('og_type', 'article')
@section('og_image', $post->cover_image ? asset('storage/'.$post->cover_image) : asset('assets/img/stock/insight.jpg'))
@section('article_meta')
    @php
        $articleSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $post->title,
            'description' => $post->seo_description ?: $post->excerpt,
            'image' => [$post->cover_image ? asset('storage/'.$post->cover_image) : asset('assets/img/stock/insight.jpg')],
            'datePublished' => $post->published_at->toIso8601String(),
            'dateModified' => $post->updated_at->toIso8601String(),
            'author' => ['@type' => 'Organization', 'name' => $post->author],
            'publisher' => ['@type' => 'Organization', 'name' => 'e-Linx', 'logo' => ['@type' => 'ImageObject', 'url' => asset('assets/img/elinxlogo.png')]],
            'keywords' => implode(', ', $post->tags ?? []),
            'wordCount' => str_word_count(strip_tags($post->body)),
            'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => url()->current()],
        ];
    @endphp
    <meta property="article:published_time" content="{{ $post->published_at->toIso8601String() }}">
    <meta property="article:author" content="{{ $post->author }}">
    @if($post->category)<meta property="article:section" content="{{ $post->category }}">@endif
    @foreach($post->tags ?? [] as $tag)<meta property="article:tag" content="{{ $tag }}">@endforeach
    <script type="application/ld+json">{!! \Illuminate\Support\Js::encode($articleSchema) !!}</script>
@endsection
@section('content')
<header class="article-hero">
    <div class="container article-hero-inner">
        <a class="article-back" href="{{ route('blog.index') }}"><i class="bi bi-arrow-left" aria-hidden="true"></i> Browse all articles</a>
        @if($post->category)<span class="blog-category article-category">{{ $post->category }}</span>@endif
        <h1>{{ $post->title }}</h1>
        <p>{{ $post->excerpt }}</p>
        <div class="article-byline"><span class="article-avatar">{{ mb_substr($post->author, 0, 1) }}</span><span><strong>{{ $post->author }}</strong><small>{{ $post->author_title ?: 'e-Linx contributor' }}</small></span><i aria-hidden="true"></i><time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('F j, Y') }}</time><span>{{ $post->readingTime() }} min read</span></div>
    </div>
</header>
<article class="article-layout">
    <div class="container">
        @if($post->cover_image)
            <figure class="article-cover"><img src="{{ asset('storage/'.$post->cover_image) }}" alt="{{ $post->cover_image_alt ?: $post->title }}"></figure>
        @endif
        <div class="article-body">{!! \Illuminate\Support\Str::markdown($post->body, ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}</div>
        @if($post->tags)
            <ul class="article-tags" aria-label="Article topics">
                @foreach($post->tags as $tag)<li>{{ $tag }}</li>@endforeach
            </ul>
        @endif
        <div class="article-end"><span>Continue exploring e-Linx insights</span><a href="{{ route('blog.index') }}">Browse more articles <i class="bi bi-arrow-right" aria-hidden="true"></i></a></div>
    </div>
</article>
@if($relatedPosts->isNotEmpty())
<section class="section section-soft">
    <div class="container"><div class="blog-listing-head"><div><span class="blog-section-label">RELATED INSIGHTS</span><h2>More on {{ $post->category }}</h2></div><a class="blog-all-link" href="{{ route('blog.index') }}">Browse all articles <i class="bi bi-arrow-right"></i></a></div>
        <div class="blog-grid blog-grid-related">@foreach($relatedPosts as $related)@include('blog.partials.card', ['post' => $related])@endforeach</div>
    </div>
</section>
@endif
<section class="article-cta"><div class="container"><div><span>HAVE A PROJECT IN MIND?</span><h2>Let’s build something useful.</h2></div><a class="btn btn-primary rounded-pill px-4" href="{{ route('quote') }}">Discuss your project <i class="bi bi-arrow-up-right"></i></a></div></section>
@endsection
