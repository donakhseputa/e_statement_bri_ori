<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('messaging.test_recipients',function(Blueprint $t){
   $t->id();
   $t->foreignId('product_id')->nullable()->constrained('core.products')->cascadeOnDelete();
   $t->string('email');
   $t->string('name')->nullable();
   $t->boolean('is_default')->default(false);
   $t->boolean('is_active')->default(true);
   $t->foreignId('created_by')->nullable()->constrained('core.users')->nullOnDelete();
   $t->timestampsTz();
   $t->unique(['product_id','email']);
   $t->index(['product_id','is_active']);
  });
 }
 public function down(): void { Schema::dropIfExists('messaging.test_recipients'); }
};
