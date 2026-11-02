<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ExistingSchemaMigrationTest extends TestCase
{
    public function test_existing_e_linx_tables_and_rows_survive_migration_and_rollback(): void
    {
        Schema::create('leads', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
        });
        DB::table('leads')->insert(['name' => 'Existing lead']);

        $migration = require database_path('migrations/2026_10_06_000000_create_elinx_tables.php');
        $migration->up();

        $this->assertDatabaseHas('leads', ['name' => 'Existing lead']);
        $this->assertTrue(Schema::hasTable('services'));

        $migration->down();

        $this->assertDatabaseHas('leads', ['name' => 'Existing lead']);
        $this->assertFalse(Schema::hasTable('services'));
        $this->assertFalse(Schema::hasTable('elinx_laravel_created_tables'));
    }
}
