<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table): void {
            $table->string('company_name', 180)->nullable();
            $table->string('project_name', 180)->nullable();
            $table->string('project_stage', 80)->nullable();
            $table->string('timeline', 80)->nullable();
            $table->string('target_audience', 500)->nullable();
            $table->json('required_features')->nullable();
            $table->string('reference_url', 500)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table): void {
            $table->dropColumn([
                'company_name',
                'project_name',
                'project_stage',
                'timeline',
                'target_audience',
                'required_features',
                'reference_url',
            ]);
        });
    }
};
