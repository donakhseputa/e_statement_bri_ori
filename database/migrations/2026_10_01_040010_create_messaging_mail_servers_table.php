<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\DB; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void {
  Schema::create('messaging.mail_servers',function(Blueprint $t){$t->id();$t->string('name',100);$t->string('host');$t->unsignedSmallInteger('port');$t->string('username')->nullable();$t->text('secret_reference')->nullable();$t->string('encryption',20)->nullable();$t->string('from_email');$t->string('from_name')->nullable();$t->string('reply_to_email')->nullable();$t->string('bounce_email')->nullable();$t->string('inbox_email')->nullable();$t->unsignedInteger('daily_limit')->nullable();$t->unsignedInteger('hourly_limit')->nullable();$t->boolean('is_active')->default(true);$t->timestampsTz();});
  DB::statement("ALTER TABLE messaging.mail_servers ADD CONSTRAINT mail_servers_encryption_check CHECK (encryption IS NULL OR encryption IN ('none','tls','starttls'))");
 } public function down(): void { Schema::dropIfExists('messaging.mail_servers'); } };
