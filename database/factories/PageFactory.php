<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PageType;
use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    public function definition(): array
    {
        /** @var PageType $type */
        $type = fake()->randomElement(PageType::cases());
        $slug = match ($type) {
            PageType::Landing => '/',
            PageType::ThankYou => '/thanks',
            PageType::Legal => '/'.fake()->word(),
            PageType::Product => '/products/'.fake()->unique()->slug(3),
        };

        return [
            'page_key' => fake()->unique()->lexify('page_????'),
            'page_type' => $type,
            'slug' => $slug,
            'is_active' => true,
            'seo_title' => fake()->sentence(5),
            'meta_description' => fake()->sentence(15),
            'og_title' => fake()->sentence(5),
            'og_description' => fake()->sentence(12),
            'og_image_url' => null,
            'robots' => 'index, follow',
            'cta_text' => fake()->words(3, true),
            'cta_url' => null,
            'trial_url' => null,
            'body_config' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    public function product(): static
    {
        return $this->state([
            'page_type' => PageType::Product,
            'slug' => '/products/'.fake()->unique()->slug(3),
        ]);
    }
}
