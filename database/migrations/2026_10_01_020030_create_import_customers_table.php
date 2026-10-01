<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void {
  Schema::create('import.customers',function(Blueprint $t){$t->id();$t->foreignId('customer_batch_id')->constrained('import.customer_batches');$t->foreignId('customer_file_id')->nullable()->constrained('import.customer_files')->nullOnDelete();$t->string('customer_number',100)->nullable();$t->string('account_number',100);$t->string('name')->nullable();$t->string('address_line_1')->nullable();$t->string('address_line_2')->nullable();$t->string('address_line_3')->nullable();$t->string('city',100)->nullable();$t->string('province',100)->nullable();$t->string('postal_code',20)->nullable();$t->text('pdf_password_ciphertext')->nullable();$t->unsignedBigInteger('source_row_number')->nullable();$t->jsonb('raw_data')->nullable();$t->timestampsTz();$t->unique(['customer_batch_id','account_number']);$t->index('customer_number');$t->index('account_number');});
 } public function down(): void { Schema::dropIfExists('import.customers'); } };
