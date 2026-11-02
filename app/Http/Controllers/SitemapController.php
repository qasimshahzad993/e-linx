<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Course;
use App\Models\Portfolio;
use App\Models\Service;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        return response()
            ->view('sitemap', [
                'urls' => [
                    route('home'),
                    route('about'),
                    route('faqs'),
                    route('services'),
                    ...Service::where('status', true)->orderBy('slug')->get()->map(
                        fn (Service $service): string => route('service.show', $service),
                    )->all(),
                    route('products'),
                    route('portfolio'),
                    ...Portfolio::where('status', true)->orderBy('id')->get()->map(
                        fn (Portfolio $project): string => route('portfolio.show', $project),
                    )->all(),
                    route('courses.index'),
                    ...Course::where('status', true)->orderBy('slug')->get()->map(
                        fn (Course $course): string => route('courses.show', $course),
                    )->all(),
                    route('blog.index'),
                    ...BlogPost::published()->orderBy('slug')->get()->map(
                        fn (BlogPost $post): string => route('blog.show', $post->slug),
                    )->all(),
                    route('contact'),
                    route('quote'),
                ],
            ])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
