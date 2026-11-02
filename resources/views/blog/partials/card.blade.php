<article class="blog-card">
    <a class="blog-card-cover" href="{{ route('blog.show', $post->slug) }}" aria-label="Read {{ $post->title }}">
        <img src="{{ $post->cover_image ? asset('storage/'.$post->cover_image) : asset('assets/img/stock/insight.jpg') }}" alt="{{ $post->cover_image ? ($post->cover_image_alt ?: $post->title) : 'Business analytics on a laptop screen' }}" loading="lazy">
        @if($post->category)<span class="blog-category">{{ $post->category }}</span>@endif
    </a>
    <div class="blog-card-content">
        <div class="blog-card-meta"><time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('M j, Y') }}</time><span>{{ $post->readingTime() }} min read</span></div>
        <h3><a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a></h3>
        <p>{{ $post->excerpt }}</p>
        <a class="blog-read-link" href="{{ route('blog.show', $post->slug) }}">Read article <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
    </div>
</article>
