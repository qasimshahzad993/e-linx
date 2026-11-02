<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portfolio', function (Blueprint $table): void {
            $table->text('challenge')->nullable();
            $table->text('solution')->nullable();
            $table->text('outcome')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('portfolio', function (Blueprint $table): void {
            $table->dropColumn(['challenge', 'solution', 'outcome']);
        });
    }
};
