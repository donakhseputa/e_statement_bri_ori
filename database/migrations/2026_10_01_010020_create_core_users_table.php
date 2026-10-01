<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core.users', function (Blueprint $table) {
            $table->id(); $table->string('username',100)->unique(); $table->string('name'); $table->string('email')->nullable()->unique(); $table->text('password_hash'); $table->boolean('is_active')->default(true); $table->timestampTz('last_login_at')->nullable(); $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core.users');
    }
};
