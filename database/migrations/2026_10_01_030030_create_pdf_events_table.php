<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\DB; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void {
  Schema::create('pdf.events',function(Blueprint $t){$t->id();$t->foreignId('document_id')->constrained('pdf.documents')->cascadeOnDelete();$t->foreignId('job_id')->nullable()->constrained('pdf.jobs')->nullOnDelete();$t->string('event_type',100);$t->jsonb('metadata')->nullable();$t->timestampTz('occurred_at')->useCurrent();$t->index(['document_id','occurred_at']);});
 } public function down(): void { Schema::dropIfExists('pdf.events'); } };
