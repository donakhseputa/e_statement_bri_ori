<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void {
  Schema::create('import.statement_files',function(Blueprint $t){$t->id();$t->foreignId('statement_batch_id')->constrained('import.statement_batches');$t->string('original_name');$t->text('storage_key');$t->unsignedBigInteger('size_bytes')->nullable();$t->string('checksum',128)->nullable();$t->string('status',30)->default('pending');$t->unsignedBigInteger('total_rows')->default(0);$t->unsignedBigInteger('processed_rows')->default(0);$t->unsignedBigInteger('valid_rows')->default(0);$t->unsignedBigInteger('invalid_rows')->default(0);$t->timestampsTz();});
  DB::statement("ALTER TABLE import.statement_files ADD CONSTRAINT statement_files_status_check CHECK (status IN ('pending','processing','completed','failed','quarantined'))");
 } public function down(): void { Schema::dropIfExists('import.statement_files'); } };
