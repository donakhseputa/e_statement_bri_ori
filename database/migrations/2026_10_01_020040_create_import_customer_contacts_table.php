<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void {
  Schema::create('import.customer_contacts',function(Blueprint $t){$t->id();$t->foreignId('customer_id')->constrained('import.customers')->cascadeOnDelete();$t->string('type',20)->default('email');$t->string('value');$t->unsignedSmallInteger('position')->default(1);$t->boolean('is_active')->default(true);$t->timestampsTz();$t->unique(['customer_id','type','value']);$t->index(['type','value']);});
  DB::statement("ALTER TABLE import.customer_contacts ADD CONSTRAINT customer_contacts_type_check CHECK (type IN ('email','phone'))");
 } public function down(): void { Schema::dropIfExists('import.customer_contacts'); } };
