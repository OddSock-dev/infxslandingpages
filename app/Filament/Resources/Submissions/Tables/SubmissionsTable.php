<?php

declare(strict_types=1);

namespace App\Filament\Resources\Submissions\Tables;

use App\Enums\CrmStatus;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubmissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('pii_json.email')
                    ->label('Email')
                    ->searchable(false),
                TextColumn::make('product_key')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('crm_status')
                    ->badge()
                    ->color(fn (CrmStatus $state): string => match ($state) {
                        CrmStatus::Pending => 'warning',
                        CrmStatus::Synced => 'success',
                        CrmStatus::Failed => 'danger',
                    })
                    ->sortable(),
                TextColumn::make('submitted_at')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('submitted_at', 'desc');
    }
}
