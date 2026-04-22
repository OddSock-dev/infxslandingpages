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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('page_key', 100)->unique();
            $table->string('page_type', 20);
            $table->string('slug', 255)->unique();
            $table->boolean('is_active')->default(true);
            $table->string('seo_title', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->string('og_title', 255)->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image_url', 500)->nullable();
            $table->string('robots', 100)->default('index, follow');
            $table->string('cta_text', 255)->nullable();
            $table->string('cta_url', 500)->nullable();
            $table->string('trial_url', 500)->nullable();
            $table->json('body_config')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
