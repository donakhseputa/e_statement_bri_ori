<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import.customer_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('core.products');
            $table->date('statement_period');
            $table->string('status', 30)->default('pending');
            $table->unsignedBigInteger('total_customers')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('core.users')->nullOnDelete();
            $table->timestampTz('started_at')->nullable();
            $table->timestampTz('completed_at')->nullable();
            $table->timestampTz('failed_at')->nullable();
            $table->timestampsTz();
            $table->index(['product_id', 'statement_period']);
            $table->index(['status', 'created_at']);
        });

        Schema::create('import.customer_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_batch_id')->constrained('import.customer_batches')->cascadeOnDelete();
            $table->string('original_name');
            $table->text('storage_key');
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->string('checksum', 128)->nullable();
            $table->string('status', 30)->default('pending');
            $table->unsignedBigInteger('total_rows')->default(0);
            $table->unsignedBigInteger('valid_rows')->default(0);
            $table->unsignedBigInteger('invalid_rows')->default(0);
            $table->timestampsTz();
        });

        Schema::create('import.customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_batch_id')->constrained('import.customer_batches')->cascadeOnDelete();
            $table->foreignId('customer_file_id')->nullable()->constrained('import.customer_files')->nullOnDelete();
            $table->string('customer_number', 100)->nullable();
            $table->string('account_number', 100);
            $table->string('name')->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('address_line_3')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->text('pdf_password_ciphertext')->nullable();
            $table->unsignedBigInteger('source_row_number')->nullable();
            $table->jsonb('raw_data')->nullable();
            $table->timestampsTz();
            $table->unique(['customer_batch_id', 'account_number']);
            $table->index('customer_number');
            $table->index('account_number');
        });

        Schema::create('import.customer_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('import.customers')->cascadeOnDelete();
            $table->string('type', 20)->default('email');
            $table->string('value');
            $table->unsignedSmallInteger('position')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestampsTz();
            $table->unique(['customer_id', 'type', 'value']);
            $table->index(['type', 'value']);
        });

        Schema::create('import.statement_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('core.products');
            $table->foreignId('customer_batch_id')->nullable()->constrained('import.customer_batches')->nullOnDelete();
            $table->date('statement_period');
            $table->string('status', 30)->default('pending');
            $table->unsignedBigInteger('total_statements')->default(0);
            $table->unsignedBigInteger('processed_statements')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('core.users')->nullOnDelete();
            $table->timestampTz('started_at')->nullable();
            $table->timestampTz('completed_at')->nullable();
            $table->timestampTz('failed_at')->nullable();
            $table->timestampsTz();
            $table->index(['product_id', 'statement_period']);
            $table->index(['status', 'created_at']);
        });

        Schema::create('import.statement_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('statement_batch_id')->constrained('import.statement_batches')->cascadeOnDelete();
            $table->string('original_name');
            $table->text('storage_key');
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->string('checksum', 128)->nullable();
            $table->string('status', 30)->default('pending');
            $table->unsignedBigInteger('total_rows')->default(0);
            $table->unsignedBigInteger('processed_rows')->default(0);
            $table->unsignedBigInteger('valid_rows')->default(0);
            $table->unsignedBigInteger('invalid_rows')->default(0);
            $table->timestampsTz();
        });

        Schema::create('import.statements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('statement_batch_id')->constrained('import.statement_batches')->cascadeOnDelete();
            $table->foreignId('statement_file_id')->nullable()->constrained('import.statement_files')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('import.customers')->nullOnDelete();
            $table->string('customer_number', 100)->nullable();
            $table->string('account_number', 100);
            $table->string('customer_name')->nullable();
            $table->string('original_account_number', 100)->nullable();
            $table->string('account_type', 100)->nullable();
            $table->string('product_description')->nullable();
            $table->string('branch_code', 30)->nullable();
            $table->string('branch_name')->nullable();
            $table->string('courier_name', 100)->nullable();
            $table->string('barcode', 100)->nullable();
            $table->date('statement_date')->nullable();
            $table->date('billing_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('source_file_name')->nullable();
            $table->unsignedBigInteger('source_row_number')->nullable();
            $table->jsonb('source_attributes')->nullable();
            $table->jsonb('raw_data')->nullable();
            $table->string('status', 30)->default('valid');
            $table->timestampsTz();
            $table->index(['statement_batch_id', 'account_number']);
            $table->index(['statement_batch_id', 'status']);
            $table->index('customer_id');
            $table->index('barcode');
        });

        Schema::create('import.errors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_batch_id')->nullable()->constrained('import.customer_batches')->cascadeOnDelete();
            $table->foreignId('customer_file_id')->nullable()->constrained('import.customer_files')->cascadeOnDelete();
            $table->foreignId('statement_batch_id')->nullable()->constrained('import.statement_batches')->cascadeOnDelete();
            $table->foreignId('statement_file_id')->nullable()->constrained('import.statement_files')->cascadeOnDelete();
            $table->unsignedBigInteger('row_number')->nullable();
            $table->string('error_code', 100);
            $table->string('field_name', 100)->nullable();
            $table->text('error_message');
            $table->text('raw_value')->nullable();
            $table->jsonb('raw_data')->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->index(['customer_batch_id', 'created_at']);
            $table->index(['statement_batch_id', 'created_at']);
        });

        Schema::create('import.events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_batch_id')->nullable()->constrained('import.customer_batches')->cascadeOnDelete();
            $table->foreignId('customer_file_id')->nullable()->constrained('import.customer_files')->cascadeOnDelete();
            $table->foreignId('statement_batch_id')->nullable()->constrained('import.statement_batches')->cascadeOnDelete();
            $table->foreignId('statement_file_id')->nullable()->constrained('import.statement_files')->cascadeOnDelete();
            $table->string('event_type', 100);
            $table->jsonb('metadata')->nullable();
            $table->timestampTz('occurred_at')->useCurrent();
            $table->index(['customer_batch_id', 'occurred_at']);
            $table->index(['statement_batch_id', 'occurred_at']);
        });

        DB::statement("ALTER TABLE import.customer_batches ADD CONSTRAINT customer_batches_period_check CHECK (statement_period = date_trunc('month', statement_period)::date)");
        DB::statement("ALTER TABLE import.statement_batches ADD CONSTRAINT statement_batches_period_check CHECK (statement_period = date_trunc('month', statement_period)::date)");
        DB::statement("ALTER TABLE import.customer_files ADD CONSTRAINT customer_files_status_check CHECK (status IN ('pending','processing','completed','failed','quarantined'))");
        DB::statement("ALTER TABLE import.statement_files ADD CONSTRAINT statement_files_status_check CHECK (status IN ('pending','processing','completed','failed','quarantined'))");
        DB::statement("ALTER TABLE import.customer_contacts ADD CONSTRAINT customer_contacts_type_check CHECK (type IN ('email','phone'))");
        DB::statement("ALTER TABLE import.customer_batches ADD CONSTRAINT customer_batches_status_check CHECK (status IN ('pending','processing','completed','partially_completed','failed','cancelled'))");
        DB::statement("ALTER TABLE import.statement_batches ADD CONSTRAINT statement_batches_status_check CHECK (status IN ('pending','processing','completed','partially_completed','failed','cancelled'))");
        DB::statement("ALTER TABLE import.statements ADD CONSTRAINT statements_status_check CHECK (status IN ('valid','invalid','processed','failed'))");
        DB::statement("ALTER TABLE import.errors ADD CONSTRAINT import_errors_owner_check CHECK ((customer_batch_id IS NOT NULL AND statement_batch_id IS NULL) OR (customer_batch_id IS NULL AND statement_batch_id IS NOT NULL))");
        DB::statement("ALTER TABLE import.errors ADD CONSTRAINT import_errors_file_owner_check CHECK ((customer_file_id IS NULL OR customer_batch_id IS NOT NULL) AND (statement_file_id IS NULL OR statement_batch_id IS NOT NULL) AND NOT (customer_file_id IS NOT NULL AND statement_file_id IS NOT NULL))");
        DB::statement("ALTER TABLE import.events ADD CONSTRAINT import_events_owner_check CHECK ((customer_batch_id IS NOT NULL AND statement_batch_id IS NULL) OR (customer_batch_id IS NULL AND statement_batch_id IS NOT NULL))");
        DB::statement("ALTER TABLE import.events ADD CONSTRAINT import_events_file_owner_check CHECK ((customer_file_id IS NULL OR customer_batch_id IS NOT NULL) AND (statement_file_id IS NULL OR statement_batch_id IS NOT NULL) AND NOT (customer_file_id IS NOT NULL AND statement_file_id IS NOT NULL))");
    }

    public function down(): void
    {
        foreach (['events', 'errors', 'statements', 'statement_files', 'statement_batches', 'customer_contacts', 'customers', 'customer_files', 'customer_batches'] as $table) {
            Schema::dropIfExists("import.$table");
        }
    }
};
