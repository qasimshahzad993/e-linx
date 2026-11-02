<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faqs', function (Blueprint $table): void {
            $table->id();
            $table->string('question', 255);
            $table->text('answer');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->index(['status', 'sort_order']);
        });

        Schema::create('testimonials', function (Blueprint $table): void {
            $table->id();
            $table->string('client_name', 120);
            $table->string('client_role', 160)->nullable();
            $table->string('company_name', 180)->nullable();
            $table->string('service', 160)->nullable();
            $table->text('quote');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('status')->default(false);
            $table->timestamps();
            $table->index(['status', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('faqs');
    }
};
