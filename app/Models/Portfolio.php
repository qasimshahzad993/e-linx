<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'portfolio';

    protected $fillable = [
        'title',
        'category',
        'description',
        'url',
        'icon',
        'status',
        'challenge',
        'solution',
        'outcome',
    ];

    public function artworkPath(): string
    {
        $category = strtolower($this->category ?? '');

        return match (true) {
            str_contains($category, 'mobile'), str_contains($category, 'app') => 'assets/img/stock/mobile-apps.jpg',
            str_contains($category, 'commerce'), str_contains($category, 'e-commerce') => 'assets/img/stock/commerce.jpg',
            str_contains($category, 'data'), str_contains($category, 'analytics'), str_contains($category, 'insight') => 'assets/img/stock/insight.jpg',
            str_contains($category, 'design'), str_contains($category, 'ux') => 'assets/img/stock/product-design.jpg',
            str_contains($category, 'youtube'), str_contains($category, 'automation') => 'assets/img/services/youtube-automation.jpg',
            str_contains($category, 'marketing'), str_contains($category, 'seo'), str_contains($category, 'campaign'), str_contains($category, 'growth') => 'assets/img/stock/digital-growth.jpg',
            default => 'assets/img/stock/web-platforms.jpg',
        };
    }

    public function externalUrl(): ?string
    {
        if (! $this->url) {
            return null;
        }

        $parts = parse_url($this->url);

        if (! is_array($parts)
            || ! in_array($parts['scheme'] ?? null, ['http', 'https'], true)
            || empty($parts['host'])) {
            return null;
        }

        return $this->url;
    }
}
