<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core.attachments', function (Blueprint $table) { $table->id(); $table->foreignId('product_id')->nullable()->constrained('core.products')->nullOnDelete(); $table->string('name'); $table->string('file_name'); $table->text('storage_key'); $table->string('mime_type',100)->nullable(); $table->unsignedBigInteger('size_bytes')->nullable(); $table->string('checksum',128)->nullable(); $table->string('content_id',150)->nullable(); $table->boolean('is_active')->default(true); $table->foreignId('created_by')->nullable()->constrained('core.users')->nullOnDelete(); $table->timestampsTz(); });
    }

    public function down(): void
    {
        Schema::dropIfExists('core.attachments');
    }
};
