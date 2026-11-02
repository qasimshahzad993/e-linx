<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('services', 'category')) {
            Schema::table('services', function (Blueprint $table): void {
                $table->string('category', 100)->nullable()->index();
            });
        }

        if (! Schema::hasColumn('leads', 'service_details')) {
            Schema::table('leads', function (Blueprint $table): void {
                $table->json('service_details')->nullable();
            });
        }

        DB::table('services')
            ->whereIn('slug', [
                'web-saas-engineering',
                'laravel-php-development',
                'mobile-app-development',
                'ui-ux-product-design',
            ])
            ->update(['category' => 'Website Solutions']);

        DB::table('services')
            ->whereIn('slug', [
                'youtube-automation',
                'seo-digital-growth',
                'social-media-marketing',
                'paid-advertising-ppc',
                'branding-creative-design',
                'content-creation-marketing',
                'local-seo',
                'e-commerce-seo',
                'geo-aeo',
                'email-marketing',
                'digital-pr-outreach',
                'lead-generation-strategy',
                'influencer-creator-marketing',
                'landing-page-conversion-design',
            ])
            ->update(['category' => 'Digital Marketing']);
    }

    public function down(): void
    {
        if (Schema::hasColumn('leads', 'service_details')) {
            Schema::table('leads', function (Blueprint $table): void {
                $table->dropColumn('service_details');
            });
        }

        if (Schema::hasColumn('services', 'category')) {
            Schema::table('services', function (Blueprint $table): void {
                $table->dropIndex(['category']);
                $table->dropColumn('category');
            });
        }
    }
};
