<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\DB; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void {
  Schema::create('pdf.jobs',function(Blueprint $t){$t->id();$t->foreignId('document_id')->constrained('pdf.documents')->cascadeOnDelete();$t->string('job_type',30);$t->string('status',30)->default('pending');$t->unsignedInteger('attempt_count')->default(0);$t->timestampTz('started_at')->nullable();$t->timestampTz('completed_at')->nullable();$t->timestampTz('failed_at')->nullable();$t->string('error_code',100)->nullable();$t->text('error_message')->nullable();$t->timestampsTz();$t->index(['document_id','status']);});
  DB::statement("ALTER TABLE pdf.jobs ADD CONSTRAINT jobs_type_check CHECK (job_type IN ('generate','merge','encrypt','stamp','validate'))");
  DB::statement("ALTER TABLE pdf.jobs ADD CONSTRAINT jobs_status_check CHECK (status IN ('pending','processing','completed','failed','cancelled'))");
  DB::statement("CREATE INDEX idx_pdf_jobs_pending ON pdf.jobs (created_at) WHERE status = 'pending'");
 } public function down(): void { Schema::dropIfExists('pdf.jobs'); } };
