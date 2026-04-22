<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PageType;
use Database\Factories\PageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property string $page_key
 * @property PageType $page_type
 * @property string $slug
 * @property bool $is_active
 * @property string|null $seo_title
 * @property string|null $meta_description
 * @property string|null $og_title
 * @property string|null $og_description
 * @property string|null $og_image_url
 * @property string $robots
 * @property string|null $cta_text
 * @property string|null $cta_url
 * @property string|null $trial_url
 * @property array<string, mixed>|null $body_config
 */
#[Fillable([
    'page_key', 'page_type', 'slug', 'is_active',
    'seo_title', 'meta_description', 'og_title', 'og_description', 'og_image_url',
    'robots', 'cta_text', 'cta_url', 'trial_url', 'body_config',
])]
class Page extends Model
{
    /** @use HasFactory<PageFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'page_type' => PageType::class,
            'is_active' => 'boolean',
            'body_config' => 'array',
        ];
    }

    /** @param  Builder<self>  $query */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }
}
