<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement('CREATE SCHEMA IF NOT EXISTS core');
        DB::statement('CREATE SCHEMA IF NOT EXISTS import');
        DB::statement('CREATE SCHEMA IF NOT EXISTS pdf');
        DB::statement('CREATE SCHEMA IF NOT EXISTS messaging');
    }

    public function down(): void
    {
        DB::statement('DROP SCHEMA IF EXISTS messaging CASCADE');
        DB::statement('DROP SCHEMA IF EXISTS pdf CASCADE');
        DB::statement('DROP SCHEMA IF EXISTS import CASCADE');
        DB::statement('DROP SCHEMA IF EXISTS core CASCADE');
    }
};
