<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class AdminBlogController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        abort_if($status && ! in_array($status, ['draft', 'published'], true), 404);

        return view('admin.blog.index', [
            'posts' => BlogPost::when($status, fn ($query) => $query->where('status', $status))
                ->orderByDesc('updated_at')
                ->paginate(15)
                ->withQueryString(),
            'edit' => $request->integer('edit') ? BlogPost::findOrFail($request->integer('edit')) : null,
            'status' => $status,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->save($request, new BlogPost);
    }

    public function update(Request $request, BlogPost $post): RedirectResponse
    {
        return $this->save($request, $post);
    }

    public function destroy(BlogPost $post): RedirectResponse
    {
        $image = $post->cover_image;
        $post->delete();

        if ($image && ! Storage::disk('public')->delete($image)) {
            throw new RuntimeException('The blog post was deleted, but its cover image could not be removed.');
        }

        return redirect()->route('admin.blog.index')->with('success', 'Blog post deleted.');
    }

    private function save(Request $request, BlogPost $post): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'alpha_dash', 'max:200'],
            'category' => ['nullable', 'string', 'max:100'],
            'excerpt' => ['required', 'string', 'max:320'],
            'body' => ['required', 'string', 'max:100000'],
            'author' => ['required', 'string', 'max:120'],
            'author_title' => ['nullable', 'string', 'max:120'],
            'tags' => [
                'nullable',
                'string',
                'max:500',
                function (string $attribute, ?string $value, \Closure $fail): void {
                    $tags = collect(explode(',', $value ?? ''))->map(fn (string $tag) => trim($tag))->filter()->unique();

                    if ($tags->count() > 10 || $tags->contains(fn (string $tag) => mb_strlen($tag) > 40)) {
                        $fail('Add up to 10 tags, with no more than 40 characters per tag.');
                    }
                },
            ],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'cover_image_alt' => ['nullable', 'string', 'max:255'],
            'seo_title' => ['nullable', 'string', 'max:70'],
            'seo_description' => ['nullable', 'string', 'max:160'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'published_at' => ['nullable', 'date'],
        ]);

        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['title'], $post);
        $data['category'] = filled($data['category'] ?? null) ? $data['category'] : null;
        $data['author_title'] = filled($data['author_title'] ?? null) ? $data['author_title'] : null;
        $data['seo_title'] = filled($data['seo_title'] ?? null) ? $data['seo_title'] : null;
        $data['seo_description'] = filled($data['seo_description'] ?? null) ? $data['seo_description'] : null;
        $data['cover_image_alt'] = filled($data['cover_image_alt'] ?? null) ? $data['cover_image_alt'] : null;
        $data['tags'] = collect(explode(',', $data['tags'] ?? ''))
            ->map(fn (string $tag) => trim($tag))
            ->filter()
            ->unique(fn (string $tag) => mb_strtolower($tag))
            ->values()
            ->all();

        if ($data['status'] === 'published') {
            $data['published_at'] = $data['published_at'] ?? $post->published_at ?? now();
        } else {
            $data['published_at'] = $data['published_at'] ?? $post->published_at;
        }

        $oldImage = $post->cover_image;
        $newImage = $request->file('cover_image')?->store('blog-covers', 'public');
        if ($request->hasFile('cover_image') && ! $newImage) {
            throw new RuntimeException('The uploaded cover image could not be saved.');
        }

        if ($newImage) {
            $data['cover_image'] = $newImage;
        }

        try {
            $post->fill($data)->save();
        } catch (\Throwable $exception) {
            if ($newImage) {
                Storage::disk('public')->delete($newImage);
            }

            throw $exception;
        }

        if ($newImage && $oldImage && ! Storage::disk('public')->delete($oldImage)) {
            throw new RuntimeException('The blog post was saved, but the previous cover image could not be removed.');
        }

        return redirect()->route('admin.blog.index')->with('success', $post->wasRecentlyCreated ? 'Blog post created.' : 'Blog post updated.');
    }

    private function uniqueSlug(string $title, BlogPost $post): string
    {
        $base = Str::limit(Str::slug($title), 200, '');
        $base = $base !== '' ? $base : 'blog-post';
        $slug = $base;
        $suffix = 2;

        while (BlogPost::where('slug', $slug)
            ->when($post->exists, fn ($query) => $query->whereKeyNot($post->getKey()))
            ->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
