<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\DB; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void {
  Schema::create('messaging.schedules',function(Blueprint $t){$t->id();$t->foreignId('product_id')->constrained('core.products');$t->string('name')->nullable();$t->timestampTz('scheduled_at');$t->string('status',30)->default('scheduled');$t->foreignId('created_by')->nullable()->constrained('core.users')->nullOnDelete();$t->timestampTz('activated_at')->nullable();$t->timestampTz('cancelled_at')->nullable();$t->timestampsTz();$t->index(['status','scheduled_at']);});
  DB::statement("ALTER TABLE messaging.schedules ADD CONSTRAINT schedules_status_check CHECK (status IN ('draft','scheduled','active','completed','cancelled'))");
 } public function down(): void { Schema::dropIfExists('messaging.schedules'); } };
