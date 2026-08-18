<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Widgets\ZohoConnectionOverviewWidget;
use App\Integrations\Zoho\ZohoCrmClient;
use App\Services\ZohoConnectionSettings;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * @property-read Schema $form
 */
class ZohoConnection extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static ?string $navigationLabel = 'Zoho Connection';

    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    protected string $view = 'filament.pages.zoho-connection';

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(ZohoConnectionSettings $settings): void
    {
        $this->form->fill($settings->formState());

        $message = session('zoho_connection_message');
        $status = session('zoho_connection_status', 'success');

        if (is_string($message) && $message !== '') {
            $notification = Notification::make()->title($message);

            match ($status) {
                'danger' => $notification->danger(),
                'warning' => $notification->warning(),
                default => $notification->success(),
            };

            $notification->send();
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('OAuth application')
                    ->description('Store the Zoho OAuth settings here so CRM sync can run without env-backed secrets.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('client_id')
                                    ->label('Client ID')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('client_secret')
                                    ->label('Client Secret')
                                    ->password()
                                    ->revealable()
                                    ->required()
                                    ->maxLength(255),
                                Placeholder::make('redirect_url')
                                    ->label('OAuth Redirect URL')
                                    ->content(function (): string {
                                        $value = data_get($this->data, 'redirect_url');

                                        return is_string($value) ? $value : '';
                                    }),
                                Placeholder::make('grant_type')
                                    ->label('Grant Type')
                                    ->content('Authorization Code'),
                                TextInput::make('authorization_url')
                                    ->label('Authorization URL')
                                    ->required()
                                    ->url(),
                                TextInput::make('access_token_url')
                                    ->label('Access Token URL')
                                    ->required()
                                    ->url(),
                            ]),
                        Textarea::make('scope')
                            ->label('Scope')
                            ->rows(3)
                            ->required(),
                        TextInput::make('auth_query_parameters')
                            ->label('Auth URI Query Parameters')
                            ->helperText('For example: access_type=offline&prompt=consent'),
                    ]),
                Section::make('Lead Defaults')
                    ->description('Default field values applied to every lead pushed to Zoho CRM.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('lead_owner_id')
                                    ->label('Lead Owner ID')
                                    ->helperText('Zoho user ID assigned as lead owner (e.g. 2960730000120595001).')
                                    ->maxLength(255),
                                TextInput::make('lead_source')
                                    ->label('Lead Source')
                                    ->helperText('Must match a Zoho CRM Lead_Source picklist value.')
                                    ->maxLength(255),
                                TextInput::make('lead_tags')
                                    ->label('Tags')
                                    ->helperText('Comma-separated tag names (e.g. INFXS,INFX).')
                                    ->maxLength(255),
                                TextInput::make('lead_brand')
                                    ->label('Brand')
                                    ->maxLength(255),
                                TextInput::make('lead_franchise')
                                    ->label('Franchise')
                                    ->maxLength(255),
                            ]),
                    ]),

                Section::make('Request handling')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('api_domain')
                                    ->label('API Domain')
                                    ->required()
                                    ->url(),
                                TextInput::make('accounts_url')
                                    ->label('Accounts URL')
                                    ->required()
                                    ->url(),
                                Toggle::make('ignore_ssl_errors')
                                    ->label('Ignore SSL issues (insecure)')
                                    ->inline(false),
                                TextInput::make('token_expired_status_code')
                                    ->label('Token expired status code')
                                    ->numeric()
                                    ->required(),
                            ]),
                        Textarea::make('allowed_domains')
                            ->label('Allowed HTTP request domains')
                            ->rows(3),
                    ]),
            ]);
    }

    /**
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('authorize')
                ->label('Authorize Zoho')
                ->icon('heroicon-o-link')
                ->url(route('admin.zoho.authorize')),
            Action::make('refresh-token')
                ->label('Refresh Token')
                ->icon('heroicon-o-arrow-path')
                ->action('refreshToken')
                ->requiresConfirmation(),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getHeaderWidgets(): array
    {
        return [
            ZohoConnectionOverviewWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }

    public function save(ZohoConnectionSettings $settings): void
    {
        /** @var array<string, mixed> $state */
        $state = $this->form->getState();
        $settings->save($state);
        $this->form->fill($settings->formState());

        Notification::make()
            ->title('Zoho connection settings saved.')
            ->success()
            ->send();
    }

    public function refreshToken(ZohoCrmClient $client, ZohoConnectionSettings $settings): void
    {
        $client->refreshToken();
        $this->form->fill($settings->formState());

        Notification::make()
            ->title('Zoho access token refreshed.')
            ->success()
            ->send();
    }
}
