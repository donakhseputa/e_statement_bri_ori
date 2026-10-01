<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\DB; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void {
  Schema::create('messaging.recipients',function(Blueprint $t){$t->id();$t->foreignId('delivery_id')->constrained('messaging.deliveries')->cascadeOnDelete();$t->foreignId('customer_contact_id')->nullable()->constrained('import.customer_contacts')->nullOnDelete();$t->string('type',10);$t->string('email');$t->string('name')->nullable();$t->timestampTz('created_at')->useCurrent();$t->unique(['delivery_id','type','email']);$t->index('email');});
  DB::statement("ALTER TABLE messaging.recipients ADD CONSTRAINT recipients_type_check CHECK (type IN ('to','cc','bcc'))");
 } public function down(): void { Schema::dropIfExists('messaging.recipients'); } };
