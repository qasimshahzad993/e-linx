<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        return view('blog.index', [
            'posts' => BlogPost::published()->orderByDesc('published_at')->paginate(9),
        ]);
    }

    public function show(string $slug): View
    {
        $post = BlogPost::published()->where('slug', $slug)->firstOrFail();

        return view('blog.show', [
            'post' => $post,
            'relatedPosts' => BlogPost::published()
                ->whereKeyNot($post->id)
                ->when($post->category, fn ($query) => $query->where('category', $post->category))
                ->orderByDesc('published_at')
                ->take(3)
                ->get(),
        ]);
    }
}
