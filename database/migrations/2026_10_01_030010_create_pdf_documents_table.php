<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\DB; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void {
  Schema::create('pdf.documents',function(Blueprint $t){$t->id();$t->foreignId('statement_id')->unique()->constrained('import.statements');$t->string('status',30)->default('pending');$t->string('file_name')->nullable();$t->text('storage_key')->nullable();$t->string('mime_type',100)->default('application/pdf');$t->unsignedBigInteger('size_bytes')->nullable();$t->unsignedInteger('page_count')->nullable();$t->string('checksum',128)->nullable();$t->boolean('is_encrypted')->default(false);$t->timestampTz('generated_at')->nullable();$t->timestampsTz();$t->index('status');});
  DB::statement("ALTER TABLE pdf.documents ADD CONSTRAINT documents_status_check CHECK (status IN ('pending','processing','generated','failed','archived'))");
 } public function down(): void { Schema::dropIfExists('pdf.documents'); } };
