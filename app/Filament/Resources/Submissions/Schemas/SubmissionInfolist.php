<?php

declare(strict_types=1);

namespace App\Filament\Resources\Submissions\Schemas;

use App\Enums\CrmStatus;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubmissionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contact Information')
                    ->schema([
                        TextEntry::make('pii_json.name')->label('Name'),
                        TextEntry::make('pii_json.email')->label('Email'),
                        TextEntry::make('pii_json.phone')->label('Phone'),
                        TextEntry::make('pii_json.company')->label('Company'),
                    ])
                    ->columns(2),

                Section::make('Submission Details')
                    ->schema([
                        TextEntry::make('product_key'),
                        TextEntry::make('crm_status')
                            ->badge()
                            ->color(fn (CrmStatus $state): string => match ($state) {
                                CrmStatus::Pending => 'warning',
                                CrmStatus::Synced => 'success',
                                CrmStatus::Failed => 'danger',
                            }),
                        TextEntry::make('submitted_at')
                            ->dateTime(),
                        TextEntry::make('journey_id'),
                    ])
                    ->columns(2),
            ]);
    }
}
