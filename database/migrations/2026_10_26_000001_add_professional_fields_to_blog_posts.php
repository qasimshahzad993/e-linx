<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table): void {
            $table->string('author_title', 120)->nullable();
            $table->json('tags')->nullable();
            $table->string('seo_title', 70)->nullable();
            $table->string('seo_description', 160)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table): void {
            $table->dropColumn(['author_title', 'tags', 'seo_title', 'seo_description']);
        });
    }
};
