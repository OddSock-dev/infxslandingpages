<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trial_clicks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('journey_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable()->index();
            $table->string('page_key', 100)->index();
            $table->string('product_key', 100)->index();
            $table->string('source_page_key', 100)->index();
            $table->string('target_url', 500);
            $table->char('click_fingerprint', 64)->unique();
            $table->json('meta_json')->nullable();
            $table->timestamp('clicked_at')->index();
            $table->timestamps();

            $table->index(['page_key', 'clicked_at']);
            $table->index(['source_page_key', 'clicked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trial_clicks');
    }
};
