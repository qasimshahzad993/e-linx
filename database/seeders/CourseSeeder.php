<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            [
                'title' => 'Digital Marketing Fundamentals',
                'category' => 'Digital Marketing',
                'summary' => 'An introduction to digital channels, campaign planning, and practical measurement.',
                'description' => 'A foundational course outline for learners who want to understand digital marketing concepts and plan a practical campaign.',
                'curriculum' => "Digital channels and customer journeys\nAudience research and campaign objectives\nContent, SEO, and paid media foundations\nAnalytics and responsible campaign measurement",
                'icon' => 'bi bi-megaphone',
            ],
            [
                'title' => 'Search Engine Optimization Essentials',
                'category' => 'SEO',
                'summary' => 'Learn the foundations of technical SEO, useful content, and search measurement.',
                'description' => 'A foundational SEO course outline covering website discoverability, content planning, and responsible performance measurement.',
                'curriculum' => "How search engines discover pages\nTechnical SEO and site structure\nSearch intent and useful content\nSearch analytics and reporting",
                'icon' => 'bi bi-search',
            ],
            [
                'title' => 'Website Development Foundations',
                'category' => 'Web Development',
                'summary' => 'Explore the building blocks of accessible, responsive websites.',
                'description' => 'A beginner-oriented course outline introducing web structure, responsive layout, and practical development workflow.',
                'curriculum' => "HTML page structure and semantics\nCSS styling and responsive layouts\nJavaScript interaction basics\nTesting, accessibility, and publishing",
                'icon' => 'bi bi-code-slash',
            ],
            [
                'title' => 'Graphic Design for Digital Media',
                'category' => 'Design',
                'summary' => 'Learn visual communication principles for everyday digital creative work.',
                'description' => 'A course outline covering core composition, typography, and preparation of visual assets for digital channels.',
                'curriculum' => "Visual hierarchy and composition\nTypography, spacing, and color\nDesigning for digital formats\nExporting and organizing creative assets",
                'icon' => 'bi bi-brush',
            ],
        ];

        foreach ($courses as $index => $attributes) {
            Course::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($attributes['title'])],
                $attributes + [
                    'slug' => \Illuminate\Support\Str::slug($attributes['title']),
                    'sort_order' => $index + 1,
                    'status' => false,
                ],
            );
        }
    }
}
