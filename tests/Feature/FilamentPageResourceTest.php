<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\PageType;
use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\Pages\ListPages;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentPageResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
        $this->actingAs($this->admin);
    }

    public function test_list_page_renders(): void
    {
        Livewire::test(ListPages::class)->assertOk();
    }

    public function test_create_page_renders(): void
    {
        Livewire::test(CreatePage::class)->assertOk();
    }

    public function test_edit_page_renders(): void
    {
        $page = Page::factory()->create();

        Livewire::test(EditPage::class, ['record' => $page->id])->assertOk();
    }

    public function test_can_create_a_page(): void
    {
        $newPage = Page::factory()->make();

        Livewire::test(CreatePage::class)
            ->fillForm([
                'page_key' => 'new_test_key',
                'page_type' => $newPage->page_type,
                'slug' => '/new-test-slug',
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('pages', ['page_key' => 'new_test_key']);
    }

    public function test_page_key_is_required_on_create(): void
    {
        Livewire::test(CreatePage::class)
            ->fillForm(['page_key' => null, 'slug' => '/some-slug', 'page_type' => PageType::Landing])
            ->call('create')
            ->assertHasFormErrors(['page_key' => 'required']);
    }

    public function test_slug_must_be_unique_across_pages(): void
    {
        Page::factory()->create(['page_key' => 'existing_key', 'slug' => '/taken-slug']);

        Livewire::test(CreatePage::class)
            ->fillForm([
                'page_key' => 'new_unique_key',
                'page_type' => PageType::Landing,
                'slug' => '/taken-slug',
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasFormErrors(['slug']);
    }

    public function test_can_update_a_page(): void
    {
        $page = Page::factory()->create();

        Livewire::test(EditPage::class, ['record' => $page->id])
            ->fillForm(['seo_title' => 'Updated SEO Title'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'seo_title' => 'Updated SEO Title',
        ]);
    }

    public function test_slug_unique_validation_ignores_current_record_on_edit(): void
    {
        $page = Page::factory()->create(['slug' => '/my-slug']);

        Livewire::test(EditPage::class, ['record' => $page->id])
            ->fillForm(['slug' => '/my-slug'])
            ->call('save')
            ->assertHasNoFormErrors();
    }

    public function test_page_key_state_is_set_on_edit(): void
    {
        $page = Page::factory()->create();

        Livewire::test(EditPage::class, ['record' => $page->id])
            ->assertSchemaStateSet(['page_key' => $page->page_key]);
    }
}
