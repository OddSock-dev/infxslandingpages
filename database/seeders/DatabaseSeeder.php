<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Page;
use App\Models\User;
use App\Support\PageCatalog;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->seedDefaultPages();
        $this->call(AdminUserSeeder::class);
    }

    private function seedDefaultPages(): void
    {
        foreach (PageCatalog::definitions() as $data) {
            Page::updateOrCreate(
                ['page_key' => $data['page_key']],
                array_merge(['is_active' => true], $data),
            );
        }
    }
}
