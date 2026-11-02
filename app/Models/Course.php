<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Course extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'category',
        'summary',
        'description',
        'curriculum',
        'duration',
        'delivery_mode',
        'price',
        'icon',
        'sort_order',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'status' => 'boolean',
        ];
    }

    public function curriculumItems(): array
    {
        return collect(preg_split('/\r\n|\r|\n/', $this->curriculum ?? ''))
            ->map(fn (string $item): string => trim($item))
            ->filter()
            ->values()
            ->all();
    }

    public static function uniqueSlug(string $title, ?self $course = null): string
    {
        $base = Str::slug($title) ?: 'course';
        $slug = $base;
        $suffix = 2;

        while (self::where('slug', $slug)
            ->when($course?->exists, fn ($query) => $query->whereKeyNot($course->getKey()))
            ->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
