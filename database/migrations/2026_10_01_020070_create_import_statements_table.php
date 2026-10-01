<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void {
  Schema::create('import.statements',function(Blueprint $t){$t->id();$t->foreignId('statement_batch_id')->constrained('import.statement_batches');$t->foreignId('statement_file_id')->nullable()->constrained('import.statement_files')->nullOnDelete();$t->foreignId('customer_id')->nullable()->constrained('import.customers')->nullOnDelete();$t->string('customer_number',100)->nullable();$t->string('account_number',100);$t->string('customer_name')->nullable();$t->string('original_account_number',100)->nullable();$t->string('account_type',100)->nullable();$t->string('product_description')->nullable();$t->string('branch_code',30)->nullable();$t->string('branch_name')->nullable();$t->string('courier_name',100)->nullable();$t->string('barcode',100)->nullable();$t->date('statement_date')->nullable();$t->date('billing_date')->nullable();$t->date('due_date')->nullable();$t->string('source_file_name')->nullable();$t->unsignedBigInteger('source_row_number')->nullable();$t->jsonb('source_attributes')->nullable();$t->jsonb('raw_data')->nullable();$t->string('status',30)->default('valid');$t->timestampsTz();$t->index(['statement_batch_id','account_number']);$t->index(['statement_batch_id','status']);$t->index('customer_id');$t->index('barcode');});
  DB::statement("ALTER TABLE import.statements ADD CONSTRAINT statements_status_check CHECK (status IN ('valid','invalid','processed','failed'))");
 } public function down(): void { Schema::dropIfExists('import.statements'); } };
