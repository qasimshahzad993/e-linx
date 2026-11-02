@extends('layouts.admin')
@section('title', 'Blog')
@section('content')
<div class="page-head"><div><h1>Blog</h1><p>Write, publish, and manage articles on your website.</p></div><a class="btn btn-primary" href="{{ route('admin.blog.index') }}"><i class="bi bi-plus-lg"></i>New article</a></div>
<div class="blog-admin-grid">
    <section class="card blog-editor">
        <h2>{{ $edit ? 'Edit article' : 'Create an article' }}</h2>
        <form method="post" action="{{ $edit ? route('admin.blog.update', $edit) : route('admin.blog.store') }}" enctype="multipart/form-data">
            @csrf
            @if($edit)@method('PUT')@endif
            <div class="field"><label for="title">Article title</label><input class="input" id="title" name="title" required maxlength="180" value="{{ old('title', $edit?->title) }}" placeholder="A clear, useful title"></div>
            <div class="field"><label for="slug">Article URL <span class="optional">(optional)</span></label><input class="input" id="slug" name="slug" maxlength="200" pattern="[A-Za-z0-9_-]+" value="{{ old('slug', $edit?->slug) }}" placeholder="generated-from-title"><div class="hint">Leave blank to generate it from the title. Changing it changes the public article URL.</div></div>
            <div class="field"><label for="category">Category <span class="optional">(optional)</span></label><input class="input" id="category" name="category" maxlength="100" value="{{ old('category', $edit?->category) }}" placeholder="e.g. Product development"></div>
            <div class="field"><label for="tags">Tags <span class="optional">(optional)</span></label><input class="input" id="tags" name="tags" maxlength="500" value="{{ old('tags', implode(', ', $edit?->tags ?? [])) }}" placeholder="AI, cybersecurity, automation"><div class="hint">Comma-separated, up to 10 tags.</div></div>
            <div class="field"><label for="excerpt">Summary</label><textarea class="textarea" id="excerpt" name="excerpt" required maxlength="320" rows="3" placeholder="A short introduction shown in article listings.">{{ old('excerpt', $edit?->excerpt) }}</textarea><div class="hint">Up to 320 characters.</div></div>
            <div class="field">
                <label for="body">Article content</label>
                <div class="blog-markdown-toolbar" role="group" aria-label="Article formatting">
                    <button type="button" data-markdown-action="heading" title="Add section heading">H2</button>
                    <button type="button" data-markdown-action="subheading" title="Add subheading">H3</button>
                    <button type="button" data-markdown-action="bold" title="Bold selected text"><strong>B</strong></button>
                    <button type="button" data-markdown-action="bullet" title="Add bullet list">• List</button>
                    <button type="button" data-markdown-action="number" title="Add numbered list">1. List</button>
                    <button type="button" data-markdown-action="quote" title="Add a quote">&ldquo; Quote</button>
                    <button type="button" data-markdown-action="link" title="Add a link"><i class="bi bi-link-45deg" aria-hidden="true"></i> Link</button>
                </div>
                <textarea class="textarea blog-content-input" id="body" name="body" required maxlength="100000" rows="14" data-markdown-editor placeholder="Start with a clear introduction, then use section headings to guide the reader.">{{ old('body', $edit?->body) }}</textarea>
                <div class="blog-writing-stats" id="blog-writing-stats" aria-live="polite"></div>
                <div class="hint">Select text and use the toolbar, or write Markdown: ## headings, **bold**, - lists, and [descriptive links](https://example.com). Raw HTML is removed.</div>
                <details class="blog-writing-guide">
                    <summary>Professional writing checklist</summary>
                    <ul>
                        <li>Start with the reader's problem and what they will learn.</li>
                        <li>Use descriptive section headings and short paragraphs.</li>
                        <li>Support recommendations with examples, evidence, or practical steps.</li>
                        <li>Finish with a useful takeaway; remove drafting notes and AI prompts.</li>
                        <li>Check spelling, links, image description, and search preview before publishing.</li>
                    </ul>
                </details>
            </div>
            <div class="field"><label for="author">Author name</label><input class="input" id="author" name="author" required maxlength="120" value="{{ old('author', $edit?->author ?? 'e-Linx Team') }}"></div>
            <div class="field"><label for="author_title">Author role <span class="optional">(optional)</span></label><input class="input" id="author_title" name="author_title" maxlength="120" value="{{ old('author_title', $edit?->author_title) }}" placeholder="e.g. Technology Editor"></div>
            <div class="field"><label for="cover_image">Cover image <span class="optional">(optional, max 5 MB)</span></label><input class="input" id="cover_image" type="file" name="cover_image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">@if($edit?->cover_image)<div class="blog-admin-current-image"><img src="{{ asset('storage/'.$edit->cover_image) }}" alt="{{ $edit->cover_image_alt ?: $edit->title }}"><span>Current cover</span></div>@endif</div>
            <div class="field"><label for="cover_image_alt">Image description <span class="optional">(accessibility)</span></label><input class="input" id="cover_image_alt" name="cover_image_alt" maxlength="255" value="{{ old('cover_image_alt', $edit?->cover_image_alt) }}" placeholder="Describe the image for readers using screen readers"></div>
            <details class="blog-seo-fields">
                <summary>Search and social preview <span class="optional">(optional)</span></summary>
                <div class="field"><label for="seo_title">SEO title</label><input class="input" id="seo_title" name="seo_title" maxlength="70" value="{{ old('seo_title', $edit?->seo_title) }}" placeholder="Defaults to the article title"><div class="hint">Up to 70 characters.</div></div>
                <div class="field"><label for="seo_description">SEO description</label><textarea class="textarea" id="seo_description" name="seo_description" maxlength="160" rows="3" placeholder="Defaults to the article summary.">{{ old('seo_description', $edit?->seo_description) }}</textarea><div class="hint">Up to 160 characters. Used for search and social previews.</div></div>
            </details>
            <div class="blog-publish-fields">
                <div class="field"><label for="status">Status</label><select class="input" id="status" name="status"><option value="draft" @selected(old('status', $edit?->status ?? 'draft') === 'draft')>Draft</option><option value="published" @selected(old('status', $edit?->status ?? 'draft') === 'published')>Published</option></select></div>
                <div class="field"><label for="published_at">Publish date <span class="optional">(optional)</span></label><input class="input" id="published_at" type="datetime-local" name="published_at" value="{{ old('published_at', $edit?->published_at?->format('Y-m-d\TH:i')) }}"><div class="hint">Future dates stay hidden until then.</div></div>
            </div>
            <div class="btn-row"><button class="btn btn-primary" type="submit"><i class="bi bi-check2"></i>{{ $edit ? 'Save changes' : 'Save article' }}</button>@if($edit)<a class="btn btn-ghost" href="{{ route('admin.blog.index') }}">Cancel</a>@endif</div>
        </form>
    </section>
    <section class="card blog-admin-list">
        <div class="blog-admin-list-head"><div><h2>All articles</h2><p>{{ $posts->total() }} total</p></div><form method="get"><label class="visually-hidden" for="blog-status-filter">Filter by status</label><select id="blog-status-filter" name="status" class="input" onchange="this.form.submit()"><option value="">All statuses</option><option value="published" @selected($status === 'published')>Published</option><option value="draft" @selected($status === 'draft')>Drafts</option></select></form></div>
        <div class="blog-admin-items">
            @forelse($posts as $post)
                <article class="blog-admin-item">
                    @if($post->cover_image)<img src="{{ asset('storage/'.$post->cover_image) }}" alt="" loading="lazy">@else<span class="blog-admin-placeholder"><i class="bi bi-journal-text"></i></span>@endif
                    <div class="blog-admin-item-info"><h3>{{ $post->title }}</h3><p>{{ $post->category ?: 'Uncategorized' }} · {{ $post->updated_at->format('M j, Y') }}</p><span class="badge {{ $post->status === 'published' && $post->published_at?->isPast() ? 'badge-on' : 'badge-off' }}">{{ $post->status === 'published' && $post->published_at?->isFuture() ? 'Scheduled' : ucfirst($post->status) }}</span></div>
                    <div class="blog-admin-item-actions"><a class="btn btn-ghost btn-sm" href="{{ route('admin.blog.index', ['edit' => $post->id]) }}"><i class="bi bi-pencil"></i>Edit</a><form method="post" action="{{ route('admin.blog.destroy', $post) }}" data-confirm="Delete “{{ $post->title }}”? This can't be undone.">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm" type="submit" aria-label="Delete {{ $post->title }}"><i class="bi bi-trash"></i></button></form></div>
                </article>
            @empty
                <div class="blog-admin-empty"><i class="bi bi-journal-text"></i><strong>No articles yet</strong><span>Create your first post using the form.</span></div>
            @endforelse
        </div>
        <div class="blog-admin-pagination">{{ $posts->links() }}</div>
    </section>
</div>
@endsection
