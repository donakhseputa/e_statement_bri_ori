<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\DB; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void {
  Schema::create('messaging.templates',function(Blueprint $t){$t->id();$t->foreignId('product_id')->constrained('core.products');$t->foreignId('mail_server_id')->constrained('messaging.mail_servers');$t->string('name',150);$t->text('subject');$t->text('html_body')->nullable();$t->text('text_body')->nullable();$t->boolean('is_active')->default(true);$t->timestampsTz();$t->unique(['product_id','name']);});
 } public function down(): void { Schema::dropIfExists('messaging.templates'); } };
