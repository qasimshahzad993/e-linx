<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table): void {
            $table->id();
            $table->string('title', 180);
            $table->string('slug', 200)->unique();
            $table->string('category', 100)->nullable();
            $table->string('excerpt', 320);
            $table->longText('body');
            $table->string('author', 120);
            $table->string('cover_image')->nullable();
            $table->string('cover_image_alt', 255)->nullable();
            $table->string('status', 20)->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
