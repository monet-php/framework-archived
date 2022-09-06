<?php

namespace Monet\Framework\Admin\Filament\Pages;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\HtmlString;
use Monet\Framework\Form\Builder\FormBuilder;
use Monet\Framework\Support\Env;
use Monet\Framework\Transformer\Facades\Transformer;

class SiteSettings extends Page
{
    protected static ?string $slug = 'administration/site-settings';

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 9999;

    protected static string $view = 'monet::filament.pages.admin.site-settings';

    public function mount(): void
    {
        $this->form->fill($this->loadConfig());
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        foreach ($data as $key => $value) {
            if (blank($value)) {
                $data[$key] = '';
            }
        }

        $env = Env::make()
            ->put('APP_NAME', $data['app_name'] ?? '')
            ->put('APP_URL', $data['app_url'] ?? '')
            ->put('APP_ENV', $data['app_environment'] ?? '')
            ->put('APP_DEBUG', $data['app_debug'] ?? false)
            ->put('MONET_AUTH_REQUIRE_EMAIL_VERIFICATION', $data['app_require_email_verification'] ?? false)
            ->put('DB_HOST', $data['db_host'] ?? '')
            ->put('DB_PORT', $data['db_port'] ?? '')
            ->put('DB_DATABASE', $data['db_name'] ?? '')
            ->put('DB_USERNAME', $data['db_username'] ?? '')
            ->put('DB_PASSWORD', $data['db_password'] ?? '')
            ->put('MAIL_HOST', $data['mail_host'] ?? '')
            ->put('MAIL_PORT', $data['mail_port'] ?? '')
            ->put('MAIL_USERNAME', $data['mail_username'] ?? '')
            ->put('MAIL_PASSWORD', $data['mail_password'] ?? '')
            ->put('MAIL_ENCRYPTION', $data['mail_encryption'] ?? '')
            ->put('MAIL_FROM_ADDRESS', $data['mail_from_address'] ?? '')
            ->put('MAIL_FROM_NAME', $data['mail_from_name'] ?? '');

        $mailPassword = $data['mail_password'];
        if (! blank($mailPassword)) {
            $env->put('MAIL_PASSWORD', $mailPassword);
        }

        $dbPassword = $data['db_password'];
        if (! blank($dbPassword)) {
            $env->put('DB_PASSWORD', $dbPassword);
        }

        if ($env->save()) {
            Notification::make()
                ->success()
                ->title('Settings have been successfully updated')
                ->send();
        } else {
            Notification::make()
                ->danger()
                ->title('Settings have failed to update')
                ->body('Monet may not have write access to the .env file')
                ->send();
        }
    }

    protected function getFormSchema(): array
    {
        return Transformer::transform(
            'monet.admin.site-settings.form',
            FormBuilder::make(
                [
                    Section::make('Application Settings')
                        ->description('Global application settings')
                        ->collapsible()
                        ->collapsed()
                        ->columns([
                            'sm' => 2,
                        ])
                        ->schema([
                            TextInput::make('app_name')
                                ->label('Application name')
                                ->helperText('The display name of the application')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('app_url')
                                ->label('Application URL')
                                ->hint('HTTPS is recommended')
                                ->helperText('The base URL of the application (E.G - https://example.com)')
                                ->required()
                                ->maxLength(255),
                            Select::make('app_environment')
                                ->label('Environment')
                                ->required()
                                ->options([
                                    'local' => 'Development',
                                    'staging' => 'Staging',
                                    'production' => 'Production',
                                ]),
                            Toggle::make('app_debug')
                                ->label('Verbose logging')
                                ->hint('This should never be enabled in production')
                                ->helperText('Display detailed errors and enable debugging functionality'),
                            Toggle::make('app_require_email_verification')
                                ->label('Require email verification')
                                ->helperText('When a user creates an account, should they be required to confirm their email?'),
                        ]),
                    Section::make('Email Settings')
                        ->description('Your email server settings')
                        ->collapsible()
                        ->collapsed()
                        ->columns([
                            'sm' => 2,
                        ])
                        ->schema([
                            TextInput::make('mail_host')
                                ->label('Host'),
                            TextInput::make('mail_username')
                                ->label('Username')
                                ->hint('This is usually your email address'),
                            TextInput::make('mail_password')
                                ->label('Password')
                                ->password(),
                            TextInput::make('mail_from_address')
                                ->label('From address')
                                ->hint('The sender email address'),
                            TextInput::make('mail_from_name')
                                ->label('From name')
                                ->hint('The sender name')
                                ->helperText(new HtmlString('Use <code>${APP_NAME}</code> to send the application name')),
                            TextInput::make('mail_port')
                                ->label('Port')
                                ->integer()
                                ->minValue(0),
                            Select::make('mail_encryption')
                                ->label('Encryption')
                                ->options([
                                    'tls' => 'TLS',
                                ]),
                        ]),
                    Section::make('Database Settings')
                        ->description('Your database server settings (ensure you know what you\'re doing updating this)')
                        ->collapsible()
                        ->collapsed()
                        ->columns([
                            'sm' => 2,
                        ])
                        ->schema([
                            TextInput::make('db_host')
                                ->label('Host')
                                ->required(),
                            TextInput::make('db_username')
                                ->label('Username')
                                ->required(),
                            TextInput::make('db_password')
                                ->label('Password')
                                ->password()
                                ->rules('confirmed'),
                            TextInput::make('db_password_confirmation')
                                ->label('Confirm password')
                                ->password(),
                            TextInput::make('db_name')
                                ->label('Database name')
                                ->required(),
                            TextInput::make('db_port')
                                ->label('Port')
                                ->default(3306)
                                ->required()
                                ->integer()
                                ->minValue(0),
                        ]),
                ]
            )->build()
        );
    }

    protected function loadConfig(): array
    {
        return Transformer::transform(
            'monet.admin.site-settings.load',
            [
                'app_name' => config('app.name'),
                'app_url' => config('app.url'),
                'app_environment' => config('app.env'),
                'app_debug' => config('app.debug'),

                'app_require_email_verification' => config('monet.auth.require_email_verification'),

                'mail_host' => config('mail.mailers.smtp.host'),
                'mail_username' => config('mail.mailers.smtp.username'),
                'mail_from_address' => config('mail.from.address'),
                'mail_from_name' => config('mail.from.name'),
                'mail_port' => config('mail.mailers.smtp.port'),
                'mail_encryption' => config('mail.mailers.smtp.encryption'),

                'db_host' => config('database.connections.mysql.host'),
                'db_username' => config('database.connections.mysql.username'),
                'db_name' => config('database.connections.mysql.database'),
                'db_port' => config('database.connections.mysql.port'),
            ]
        );
    }
}
