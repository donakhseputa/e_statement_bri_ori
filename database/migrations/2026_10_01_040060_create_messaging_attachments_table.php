<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\DB; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void {
  Schema::create('messaging.attachments',function(Blueprint $t){$t->id();$t->foreignId('delivery_id')->constrained('messaging.deliveries')->cascadeOnDelete();$t->foreignId('document_id')->nullable()->constrained('pdf.documents')->nullOnDelete();$t->foreignId('core_attachment_id')->nullable()->constrained('core.attachments')->nullOnDelete();$t->string('file_name');$t->text('storage_key');$t->string('mime_type',100)->nullable();$t->unsignedBigInteger('size_bytes')->nullable();$t->timestampTz('created_at')->useCurrent();});
  DB::statement("ALTER TABLE messaging.attachments ADD CONSTRAINT attachments_source_check CHECK (document_id IS NOT NULL OR core_attachment_id IS NOT NULL)");
 } public function down(): void { Schema::dropIfExists('messaging.attachments'); } };
