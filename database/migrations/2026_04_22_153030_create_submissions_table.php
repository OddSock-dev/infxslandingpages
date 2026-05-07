<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journey_id')->nullable()->constrained()->nullOnDelete();
            $table->unique('journey_id');
            $table->string('page_key', 100)->index();
            $table->string('product_key', 100)->index();
            $table->text('pii_json');
            $table->json('meta_json');
            $table->string('crm_status', 20)->default('pending');
            $table->dateTime('submitted_at');
            $table->timestamps();

            $table->index(['crm_status', 'submitted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
