<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\DB; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void {
  Schema::create('messaging.suppressions',function(Blueprint $t){$t->id();$t->string('email');$t->string('reason',30);$t->string('source',100)->nullable();$t->boolean('is_permanent')->default(true);$t->timestampTz('expires_at')->nullable();$t->timestampsTz();$t->unique(['email','reason']);});
  DB::statement("ALTER TABLE messaging.suppressions ADD CONSTRAINT suppressions_reason_check CHECK (reason IN ('hard_bounce','complaint','unsubscribe','invalid_address','manual'))");
  DB::statement("CREATE INDEX idx_suppressions_permanent_email ON messaging.suppressions (email) WHERE is_permanent");
 } public function down(): void { Schema::dropIfExists('messaging.suppressions'); } };
