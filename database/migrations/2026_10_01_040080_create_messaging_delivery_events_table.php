<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\DB; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void {
  Schema::create('messaging.delivery_events',function(Blueprint $t){$t->id();$t->foreignId('delivery_id')->constrained('messaging.deliveries')->cascadeOnDelete();$t->foreignId('attempt_id')->nullable()->constrained('messaging.delivery_attempts')->nullOnDelete();$t->string('event_type',30);$t->string('provider_event_id')->nullable();$t->jsonb('metadata')->nullable();$t->timestampTz('occurred_at');$t->timestampTz('created_at')->useCurrent();$t->index(['delivery_id','occurred_at']);$t->index(['event_type','occurred_at']);$t->index('provider_event_id');});
  DB::statement("ALTER TABLE messaging.delivery_events ADD CONSTRAINT delivery_events_type_check CHECK (event_type IN ('queued','processing','sent','delivered','opened','clicked','bounced','complained','failed','cancelled'))");
 } public function down(): void { Schema::dropIfExists('messaging.delivery_events'); } };
