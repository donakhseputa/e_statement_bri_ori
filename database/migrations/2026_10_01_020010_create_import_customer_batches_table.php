<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void {
  Schema::create('import.customer_batches',function(Blueprint $t){$t->id();$t->foreignId('product_id')->constrained('core.products');$t->date('statement_period');$t->string('status',30)->default('pending');$t->unsignedBigInteger('total_customers')->default(0);$t->foreignId('created_by')->nullable()->constrained('core.users')->nullOnDelete();$t->timestampTz('started_at')->nullable();$t->timestampTz('completed_at')->nullable();$t->timestampTz('failed_at')->nullable();$t->timestampsTz();$t->index(['product_id','statement_period']);$t->index(['status','created_at']);});
  DB::statement("ALTER TABLE import.customer_batches ADD CONSTRAINT customer_batches_period_check CHECK (statement_period = date_trunc('month', statement_period)::date)");
  DB::statement("ALTER TABLE import.customer_batches ADD CONSTRAINT customer_batches_status_check CHECK (status IN ('pending','processing','completed','partially_completed','failed','cancelled'))");
 } public function down(): void { Schema::dropIfExists('import.customer_batches'); } };
