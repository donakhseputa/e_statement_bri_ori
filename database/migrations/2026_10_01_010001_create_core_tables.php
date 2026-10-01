<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core.products', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestampsTz();
        });

        Schema::create('core.users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 100)->unique();
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->text('password_hash');
            $table->boolean('is_active')->default(true);
            $table->timestampTz('last_login_at')->nullable();
            $table->timestampsTz();
        });

        Schema::create('core.roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->text('description')->nullable();
            $table->timestampsTz();
        });

        Schema::create('core.permissions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 150)->unique();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->timestampsTz();
        });

        Schema::create('core.user_roles', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('core.users')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('core.roles')->cascadeOnDelete();
            $table->primary(['user_id', 'role_id']);
        });

        Schema::create('core.role_permissions', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('core.roles')->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained('core.permissions')->cascadeOnDelete();
            $table->primary(['role_id', 'permission_id']);
        });

        Schema::create('core.attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained('core.products')->nullOnDelete();
            $table->string('name');
            $table->string('file_name');
            $table->text('storage_key');
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->string('checksum', 128)->nullable();
            $table->string('content_id', 150)->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('core.users')->nullOnDelete();
            $table->timestampsTz();
        });

        Schema::create('core.settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 150)->unique();
            $table->jsonb('value')->nullable();
            $table->text('description')->nullable();
            $table->timestampsTz();
        });

        Schema::create('core.audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('core.users')->nullOnDelete();
            $table->string('action', 100);
            $table->string('entity_type', 150)->nullable();
            $table->string('entity_id', 100)->nullable();
            $table->jsonb('metadata')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->index(['entity_type', 'entity_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        foreach (['audit_logs', 'settings', 'attachments', 'role_permissions', 'user_roles', 'permissions', 'roles', 'users', 'products'] as $table) {
            Schema::dropIfExists("core.$table");
        }
    }
};
