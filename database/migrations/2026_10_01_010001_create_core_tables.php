<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
 public function up(): void {
  Schema::create('core.products', function(Blueprint $t){$t->id();$t->string('code',30)->unique();$t->string('name',100);$t->text('description')->nullable();$t->boolean('is_active')->default(true);$t->timestampsTz();});
  Schema::create('core.users', function(Blueprint $t){$t->id();$t->string('username',100)->unique();$t->string('name');$t->string('email')->nullable()->unique();$t->text('password_hash');$t->boolean('is_active')->default(true);$t->timestampTz('last_login_at')->nullable();$t->timestampsTz();});
  Schema::create('core.roles', function(Blueprint $t){$t->id();$t->string('name',100)->unique();$t->text('description')->nullable();$t->timestampsTz();});
  Schema::create('core.permissions', function(Blueprint $t){$t->id();$t->string('code',150)->unique();$t->string('name',150);$t->text('description')->nullable();$t->timestampsTz();});
  Schema::create('core.user_roles', function(Blueprint $t){$t->foreignId('user_id')->constrained('core.users')->cascadeOnDelete();$t->foreignId('role_id')->constrained('core.roles')->cascadeOnDelete();$t->primary(['user_id','role_id']);});
  Schema::create('core.role_permissions', function(Blueprint $t){$t->foreignId('role_id')->constrained('core.roles')->cascadeOnDelete();$t->foreignId('permission_id')->constrained('core.permissions')->cascadeOnDelete();$t->primary(['role_id','permission_id']);});
  Schema::create('core.customers', function(Blueprint $t){$t->id();$t->string('customer_number',100)->nullable();$t->string('name');$t->string('address_line_1')->nullable();$t->string('address_line_2')->nullable();$t->string('address_line_3')->nullable();$t->string('city',100)->nullable();$t->string('province',100)->nullable();$t->string('postal_code',20)->nullable();$t->timestampsTz();});
  DB::statement('CREATE UNIQUE INDEX uq_customers_customer_number ON core.customers (customer_number) WHERE customer_number IS NOT NULL');
  Schema::create('core.customer_accounts', function(Blueprint $t){$t->id();$t->foreignId('customer_id')->constrained('core.customers');$t->foreignId('product_id')->constrained('core.products');$t->string('account_number',100);$t->string('account_name')->nullable();$t->boolean('is_active')->default(true);$t->timestampsTz();$t->unique(['product_id','account_number']);});
  Schema::create('core.customer_contacts', function(Blueprint $t){$t->id();$t->foreignId('customer_id')->constrained('core.customers')->cascadeOnDelete();$t->string('type',20);$t->string('value');$t->boolean('is_primary')->default(false);$t->boolean('is_active')->default(true);$t->timestampsTz();$t->unique(['customer_id','type','value']);});
  DB::statement("ALTER TABLE core.customer_contacts ADD CONSTRAINT customer_contacts_type_check CHECK (type IN ('email','phone'))");
  DB::statement('CREATE UNIQUE INDEX uq_customer_primary_contact ON core.customer_contacts (customer_id, type) WHERE is_primary AND is_active');
  Schema::create('core.attachments', function(Blueprint $t){$t->id();$t->foreignId('product_id')->nullable()->constrained('core.products');$t->string('name');$t->string('file_name');$t->text('storage_key');$t->string('mime_type',100)->nullable();$t->unsignedBigInteger('size_bytes')->nullable();$t->string('checksum',128)->nullable();$t->boolean('is_active')->default(true);$t->foreignId('created_by')->nullable()->constrained('core.users')->nullOnDelete();$t->timestampsTz();});
  Schema::create('core.settings', function(Blueprint $t){$t->id();$t->string('key',150)->unique();$t->jsonb('value')->nullable();$t->text('description')->nullable();$t->timestampsTz();});
  Schema::create('core.audit_logs', function(Blueprint $t){$t->id();$t->foreignId('user_id')->nullable()->constrained('core.users')->nullOnDelete();$t->string('action',100);$t->string('entity_type',150)->nullable();$t->string('entity_id',100)->nullable();$t->jsonb('metadata')->nullable();$t->ipAddress('ip_address')->nullable();$t->timestampTz('created_at')->useCurrent();$t->index(['entity_type','entity_id']);$t->index('created_at');});
 }
 public function down(): void {foreach(['audit_logs','settings','attachments','customer_contacts','customer_accounts','customers','role_permissions','user_roles','permissions','roles','users','products'] as $table){Schema::dropIfExists("core.$table");}}
};