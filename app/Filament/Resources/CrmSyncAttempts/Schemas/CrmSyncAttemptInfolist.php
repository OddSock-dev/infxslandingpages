<?php

declare(strict_types=1);

namespace App\Filament\Resources\CrmSyncAttempts\Schemas;

use App\Enums\CrmStatus;
use App\Enums\SyncAttemptStatus;
use App\Filament\Resources\Submissions\SubmissionResource;
use App\Models\CrmSyncAttempt;
use App\Support\CrmPayloadRedactor;
use Carbon\CarbonImmutable;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CrmSyncAttemptInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Attempt')
                    ->description('Failure metadata stays searchable while admins can inspect the full CRM payload details.')
                    ->schema([
                        TextEntry::make('id')->label('ID'),
                        TextEntry::make('submission_id')
                            ->label('Submission')
                            ->url(fn (CrmSyncAttempt $record): string => SubmissionResource::getUrl('view', ['record' => $record->submission_id])),
                        TextEntry::make('provider'),
                        TextEntry::make('action'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (SyncAttemptStatus $state): string => match ($state) {
                                SyncAttemptStatus::Pending => 'warning',
                                SyncAttemptStatus::Success => 'success',
                                SyncAttemptStatus::Failed => 'danger',
                            }),
                        TextEntry::make('attempted_at')
                            ->dateTime(),
                        TextEntry::make('submission.crm_status')
                            ->label('Submission Status')
                            ->badge()
                            ->color(fn (CrmStatus $state): string => match ($state) {
                                CrmStatus::Pending => 'warning',
                                CrmStatus::Synced => 'success',
                                CrmStatus::Failed => 'danger',
                            }),
                        TextEntry::make('operator_state')
                            ->label('Operator State')
                            ->badge()
                            ->state(fn (CrmSyncAttempt $record): string => self::operatorState($record))
                            ->color(fn (string $state): string => match ($state) {
                                'Ready to requeue' => 'danger',
                                'Resolved by newer attempt' => 'success',
                                default => 'gray',
                            }),
                        TextEntry::make('attempt_count')
                            ->label('Submission Attempts')
                            ->state(fn (CrmSyncAttempt $record): int => $record->submission?->syncAttempts()->count() ?? 0),
                        TextEntry::make('latestSuccessfulSyncAttempt.attempted_at')
                            ->label('Last Successful Push')
                            ->state(fn (CrmSyncAttempt $record) => $record->submission?->latestSuccessfulSyncAttempt?->attempted_at)
                            ->dateTime(),
                    ])
                    ->columns(2),

                Section::make('Submission Context')
                    ->description('Contact details remain encrypted at rest and are shown in full to admins here.')
                    ->schema([
                        TextEntry::make('submission.product_key')
                            ->label('Product'),
                        TextEntry::make('submission.submitted_at')
                            ->label('Submitted')
                            ->dateTime(),
                        KeyValueEntry::make('submission_contact')
                            ->label('Contact Details')
                            ->state(fn (CrmSyncAttempt $record): array => $record->submission === null
                                ? []
                                : CrmPayloadRedactor::flattenForDisplay($record->submission->pii_json, redact: false)),
                    ])
                    ->columns(2),

                Section::make('Error Details')
                    ->schema([
                        TextEntry::make('error_code'),
                        TextEntry::make('error_message')
                            ->columnSpanFull(),
                        TextEntry::make('operator_guidance')
                            ->label('Operator Guidance')
                            ->state(fn (CrmSyncAttempt $record): array => self::operatorGuidance($record))
                            ->bulleted(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Request Payload')
                    ->description('Keys are flattened for readability and shown in full to admins.')
                    ->schema([
                        KeyValueEntry::make('request_payload_display')
                            ->label('Request Payload')
                            ->state(fn (CrmSyncAttempt $record): array => CrmPayloadRedactor::flattenForDisplay($record->request_payload, redact: false)),
                    ])
                    ->collapsible(),

                Section::make('Response Payload')
                    ->description('Zoho responses remain encrypted at rest and are shown in full to admins here.')
                    ->schema([
                        KeyValueEntry::make('response_payload_display')
                            ->label('Response Payload')
                            ->state(fn (CrmSyncAttempt $record): array => CrmPayloadRedactor::flattenForDisplay($record->response_payload, redact: false)),
                    ])
                    ->collapsible(),

                Section::make('Attempt History')
                    ->description('Use the latest failed row for retries. Historical failures stay visible for audit context.')
                    ->schema([
                        RepeatableEntry::make('attempt_history')
                            ->label('Recent Attempts')
                            ->state(fn (CrmSyncAttempt $record): array => self::attemptHistory($record))
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
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }

    /**
     * @return array<int, array{
     *     id: int,
     *     status: SyncAttemptStatus,
     *     attempted_at: CarbonImmutable,
     *     error_message: string|null
     * }>
     */
    private static function attemptHistory(CrmSyncAttempt $record): array
    {
        if ($record->submission === null) {
            return [];
        }

        return $record->submission->syncAttempts()
            ->latest('attempted_at')
            ->limit(8)
            ->get()
            ->map(static fn (CrmSyncAttempt $attempt): array => [
                'id' => $attempt->id,
                'status' => $attempt->status,
                'attempted_at' => $attempt->attempted_at,
                'error_message' => $attempt->error_message,
            ])
            ->all();
    }

    /**
     * @return list<string>
     */
    private static function operatorGuidance(CrmSyncAttempt $record): array
    {
        $guidance = [
            'Only the latest failed attempt can be requeued from Filament.',
            'Payload snapshots below are flattened for readability and shown in full to admins.',
        ];

        $latestAttempt = $record->submission?->latestSyncAttempt;

        if ($latestAttempt !== null && $latestAttempt->is($record) && $record->status === SyncAttemptStatus::Failed) {
            $guidance[] = 'This is the current failed push for the submission and can be requeued safely.';
        }

        if ($record->submission?->crm_status === CrmStatus::Synced) {
            $guidance[] = 'A later attempt already synced this submission, so this failure is historical only.';
        }

        return $guidance;
    }

    private static function operatorState(CrmSyncAttempt $record): string
    {
        $latestAttempt = $record->submission?->latestSyncAttempt;

        if ($record->submission?->crm_status === CrmStatus::Synced && $latestAttempt !== null && ! $latestAttempt->is($record)) {
            return 'Resolved by newer attempt';
        }

        if ($latestAttempt !== null && $latestAttempt->is($record) && $record->status === SyncAttemptStatus::Failed) {
            return 'Ready to requeue';
        }

        return 'Historical attempt';
    }
}
