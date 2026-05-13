<?php

declare(strict_types=1);

namespace App\Filament\Resources\Submissions\Schemas;

use App\Enums\CrmStatus;
use App\Enums\SyncAttemptStatus;
use App\Models\CrmSyncAttempt;
use App\Models\Submission;
use Carbon\CarbonImmutable;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
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
                        TextEntry::make('contact_name')
                            ->label('Name')
                            ->state(fn (Submission $record): ?string => self::contactValue($record, 'name')),
                        TextEntry::make('contact_email')
                            ->label('Email')
                            ->state(fn (Submission $record): ?string => self::contactValue($record, 'email')),
                        TextEntry::make('contact_phone')
                            ->label('Phone')
                            ->state(fn (Submission $record): ?string => self::contactValue($record, 'phone')),
                        TextEntry::make('contact_company')
                            ->label('Company')
                            ->state(fn (Submission $record): ?string => self::contactValue($record, 'company')),
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

                Section::make('CRM Sync Activity')
                    ->description('Attempt summaries stay visible alongside the full contact and payload details for admins.')
                    ->schema([
                        TextEntry::make('latestSyncAttempt.status')
                            ->label('Latest Attempt Status')
                            ->state(fn (Submission $record) => $record->latestSyncAttempt?->status)
                            ->badge()
                            ->color(fn (?SyncAttemptStatus $state): string => match ($state) {
                                SyncAttemptStatus::Pending => 'warning',
                                SyncAttemptStatus::Success => 'success',
                                SyncAttemptStatus::Failed => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('latestSyncAttempt.attempted_at')
                            ->label('Latest Attempt')
                            ->state(fn (Submission $record) => $record->latestSyncAttempt?->attempted_at)
                            ->dateTime(),
                        TextEntry::make('sync_attempts_count')
                            ->label('Total Attempts')
                            ->state(fn (Submission $record): int => $record->syncAttempts()->count()),
                        TextEntry::make('latestSuccessfulSyncAttempt.attempted_at')
                            ->label('Last Successful Push')
                            ->state(fn (Submission $record) => $record->latestSuccessfulSyncAttempt?->attempted_at)
                            ->dateTime(),
                        RepeatableEntry::make('recent_attempts')
                            ->label('Recent Attempts')
                            ->state(fn (Submission $record): array => self::recentAttempts($record))
                            ->table([
                                TableColumn::make('Attempt'),
                                TableColumn::make('Status'),
                                TableColumn::make('When'),
                                TableColumn::make('Error'),
                            ])
                            ->schema([
                                TextEntry::make('id')
                                    ->label('Attempt'),
                                TextEntry::make('status')
                                    ->badge()
                                    ->color(fn (SyncAttemptStatus $state): string => match ($state) {
                                        SyncAttemptStatus::Pending => 'warning',
                                        SyncAttemptStatus::Success => 'success',
                                        SyncAttemptStatus::Failed => 'danger',
                                    }),
                                TextEntry::make('attempted_at')
                                    ->label('When')
                                    ->dateTime(),
                                TextEntry::make('error_message')
                                    ->label('Error')
                                    ->placeholder('—')
                                    ->limit(100),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    private static function contactValue(Submission $record, string $field): ?string
    {
        $pii = $record->pii_json;

        if ($field === 'name') {
            $fullName = data_get($pii, 'name');

            if (! is_string($fullName) || trim($fullName) === '') {
                $firstName = data_get($pii, 'first_name');
                $lastName = data_get($pii, 'last_name');

                $fullName = trim(implode(' ', array_filter([
                    is_string($firstName) ? $firstName : null,
                    is_string($lastName) ? $lastName : null,
                ])));
            }

            return $fullName === '' ? null : $fullName;
        }

        $value = data_get($pii, $field);

        return is_string($value) ? $value : null;
    }

    /**
     * @return array<int, array{
     *     id: int,
     *     status: SyncAttemptStatus,
     *     attempted_at: CarbonImmutable,
     *     error_message: string|null
     * }>
     */
    private static function recentAttempts(Submission $record): array
    {
        return $record->syncAttempts()
            ->latest('attempted_at')
            ->limit(5)
            ->get()
            ->map(static fn (CrmSyncAttempt $attempt): array => [
                'id' => $attempt->id,
                'status' => $attempt->status,
                'attempted_at' => $attempt->attempted_at,
                'error_message' => $attempt->error_message,
            ])
            ->all();
    }
}
