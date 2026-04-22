<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PageType;
use App\Models\Page;
use App\Models\User;
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
    }

    private function seedDefaultPages(): void
    {
        $pages = [
            [
                'page_key' => 'landing',
                'page_type' => PageType::Landing,
                'slug' => '/',
                'seo_title' => 'Zoho Solutions Hub | INFX Solutions',
                'robots' => 'index, follow',
            ],
            [
                'page_key' => 'zoho_marketing_plus',
                'page_type' => PageType::Product,
                'slug' => '/products/zoho-marketing-plus',
                'seo_title' => 'Zoho Marketing Plus | INFX Solutions',
                'robots' => 'index, follow',
            ],
            [
                'page_key' => 'zoho_one',
                'page_type' => PageType::Product,
                'slug' => '/products/zoho-one',
                'seo_title' => 'Zoho One | INFX Solutions',
                'robots' => 'index, follow',
            ],
            [
                'page_key' => 'zoho_workplace',
                'page_type' => PageType::Product,
                'slug' => '/products/zoho-workplace',
                'seo_title' => 'Zoho Workplace | INFX Solutions',
                'robots' => 'index, follow',
            ],
        ];

        foreach ($pages as $data) {
            Page::updateOrCreate(
                ['page_key' => $data['page_key']],
                array_merge(['is_active' => true], $data),
            );
        }
    }
}
