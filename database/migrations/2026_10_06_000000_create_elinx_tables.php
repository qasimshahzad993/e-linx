<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elinx_laravel_created_tables', function (Blueprint $table): void {
            $table->string('table_name', 64)->primary();
        });

        if (! Schema::hasTable('admins')) {
            Schema::create('admins', function (Blueprint $table): void {
                $table->id();
                $table->string('name', 120);
                $table->string('email', 190)->unique();
                $table->string('password');
                $table->timestamp('created_at')->useCurrent();
            });
            DB::table('elinx_laravel_created_tables')->insert(['table_name' => 'admins']);
        }

        if (! Schema::hasTable('services')) {
            Schema::create('services', function (Blueprint $table): void {
                $table->id();
                $table->string('title', 160);
                $table->string('slug', 180)->unique();
                $table->string('short_description')->nullable();
                $table->text('description')->nullable();
                $table->string('icon', 100)->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('status')->default(true);
                $table->timestamps();
            });
            DB::table('elinx_laravel_created_tables')->insert(['table_name' => 'services']);
        }

        if (! Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table): void {
                $table->id();
                $table->string('name', 160);
                $table->string('category', 100)->nullable();
                $table->text('description')->nullable();
                $table->string('icon', 100)->nullable();
                $table->boolean('status')->default(true);
                $table->timestamps();
            });
            DB::table('elinx_laravel_created_tables')->insert(['table_name' => 'products']);
        }

        if (! Schema::hasTable('portfolio')) {
            Schema::create('portfolio', function (Blueprint $table): void {
                $table->id();
                $table->string('title', 180);
                $table->string('category', 100)->nullable();
                $table->text('description')->nullable();
                $table->string('url', 500)->nullable();
                $table->string('icon', 100)->nullable();
                $table->boolean('status')->default(true);
                $table->timestamps();
            });
            DB::table('elinx_laravel_created_tables')->insert(['table_name' => 'portfolio']);
        }

        if (! Schema::hasTable('leads')) {
            Schema::create('leads', function (Blueprint $table): void {
                $table->id();
                $table->string('name', 160);
                $table->string('email', 190);
                $table->string('phone', 80)->nullable();
                $table->string('subject')->nullable();
                $table->text('message');
                $table->string('budget', 100)->nullable();
                $table->string('type', 40)->default('contact');
                $table->string('status', 40)->default('new');
                $table->timestamps();
            });
            DB::table('elinx_laravel_created_tables')->insert(['table_name' => 'leads']);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('elinx_laravel_created_tables')) {
            return;
        }

        $tables = DB::table('elinx_laravel_created_tables')->pluck('table_name')->all();
        foreach (array_reverse($tables) as $table) {
            Schema::dropIfExists($table);
        }

        Schema::dropIfExists('elinx_laravel_created_tables');
    }
};
