<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table): void {
            $table->id();
            $table->string('title', 180);
            $table->string('slug', 200)->unique();
            $table->string('category', 100)->nullable();
            $table->string('summary', 320);
            $table->text('description');
            $table->text('curriculum')->nullable();
            $table->string('duration', 100)->nullable();
            $table->string('delivery_mode', 100)->nullable();
            $table->string('price', 100)->nullable();
            $table->string('icon', 100)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('status')->default(false);
            $table->timestamps();
            $table->index(['status', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
