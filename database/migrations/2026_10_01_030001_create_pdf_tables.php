<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void {
  Schema::create('pdf.documents',function(Blueprint $t){$t->id();$t->foreignId('record_id')->unique()->constrained('import.records');$t->string('status',30)->default('pending');$t->string('file_name')->nullable();$t->text('storage_key')->nullable();$t->string('mime_type',100)->default('application/pdf');$t->unsignedBigInteger('size_bytes')->nullable();$t->unsignedInteger('page_count')->nullable();$t->string('checksum',128)->nullable();$t->boolean('is_encrypted')->default(false);$t->text('password_ciphertext')->nullable();$t->timestampTz('generated_at')->nullable();$t->timestampsTz();$t->index('status');});
  Schema::create('pdf.jobs',function(Blueprint $t){$t->id();$t->foreignId('document_id')->constrained('pdf.documents')->cascadeOnDelete();$t->string('job_type',30);$t->string('status',30)->default('pending');$t->unsignedInteger('attempt_count')->default(0);$t->timestampTz('started_at')->nullable();$t->timestampTz('completed_at')->nullable();$t->timestampTz('failed_at')->nullable();$t->string('error_code',100)->nullable();$t->text('error_message')->nullable();$t->timestampsTz();});
  Schema::create('pdf.events',function(Blueprint $t){$t->id();$t->foreignId('document_id')->constrained('pdf.documents')->cascadeOnDelete();$t->foreignId('job_id')->nullable()->constrained('pdf.jobs')->nullOnDelete();$t->string('event_type',100);$t->jsonb('metadata')->nullable();$t->timestampTz('occurred_at')->useCurrent();});
 }
 public function down():void {foreach(['events','jobs','documents'] as $table){Schema::dropIfExists("pdf.$table");}}
};