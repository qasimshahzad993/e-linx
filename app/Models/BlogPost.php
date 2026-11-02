<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'category',
        'excerpt',
        'body',
        'author',
        'author_title',
        'tags',
        'cover_image',
        'cover_image_alt',
        'seo_title',
        'seo_description',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'tags' => 'array',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function readingTime(): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags($this->body)) / 220));
    }
}
