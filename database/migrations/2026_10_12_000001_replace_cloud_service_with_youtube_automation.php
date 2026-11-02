<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('services')
            ->where(function ($query): void {
                $query->whereIn('slug', ['cloud', 'cloud-devops'])
                    ->orWhere('title', 'Cloud & DevOps');
            })
            ->update([
                'title' => 'YouTube Automation',
                'slug' => 'youtube-automation',
                'short_description' => 'Consistent publishing workflows for growing channels.',
                'description' => 'YouTube channel strategy, video scripting, titles, thumbnails, scheduled publishing and performance analytics to help creators grow their audience.',
                'icon' => 'bi bi-youtube',
            ]);
    }

    public function down(): void
    {
        DB::table('services')
            ->where('slug', 'youtube-automation')
            ->update([
                'title' => 'Cloud & DevOps',
                'slug' => 'cloud',
                'short_description' => 'Reliable infrastructure from dev to production.',
                'description' => 'Deployment pipelines, cloud architecture, backups, monitoring, containers and performance improvements for production systems.',
                'icon' => 'bi bi-cloud-check',
            ]);
    }
};
