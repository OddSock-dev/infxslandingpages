<?php

declare(strict_types=1);

namespace App\Filament\Resources\Pages\Schemas;

use App\Enums\PageType;
use App\Models\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identity')
                    ->schema([
                        TextInput::make('page_key')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true)
                            ->disabled(fn (?Page $record): bool => $record !== null)
                            ->dehydrated(fn (?Page $record): bool => $record === null),
                        Select::make('page_type')
                            ->options(PageType::class)
                            ->required()
                            ->native(false),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Toggle::make('is_active')
                            ->default(true)
                            ->inline(false),
                    ])
                    ->columns(2),

                Section::make('SEO')
                    ->schema([
                        TextInput::make('seo_title')->maxLength(255),
                        TextInput::make('og_title')->maxLength(255),
                        Textarea::make('meta_description')->rows(2)->columnSpanFull(),
                        Textarea::make('og_description')->rows(2)->columnSpanFull(),
                        TextInput::make('og_image_url')->maxLength(500)->url(),
                        TextInput::make('robots')->maxLength(100)->default('index, follow'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Call to Action')
                    ->schema([
                        TextInput::make('cta_text')->maxLength(255),
                        TextInput::make('cta_url')->maxLength(500)->url(),
                        TextInput::make('trial_url')->maxLength(500)->url(),
                    ])
                    ->columns(3)
                    ->collapsible(),
            ]);
    }
}
