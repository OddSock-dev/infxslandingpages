<?php

declare(strict_types=1);

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('zoho_credentials', function (Blueprint $table) {
            // Existing rows may contain plain-text values from the first implementation.
            // Re-save them encrypted before the model begins casting this column.
        });

        DB::table('zoho_credentials')
            ->select(['key', 'value'])
            ->whereNotNull('value')
            ->orderBy('key')
            ->each(function (object $row): void {
                if (! is_string($row->value) || $row->value === '') {
                    return;
                }

                try {
                    Crypt::decryptString($row->value);

                    return;
                } catch (DecryptException) {
                    DB::table('zoho_credentials')
                        ->where('key', $row->key)
                        ->update([
                            'value' => Crypt::encryptString($row->value),
                            'updated_at' => now(),
                        ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zoho_credentials', function (Blueprint $table) {
            // Intentionally left blank: credential values stay encrypted at rest once migrated.
        });
    }
};
