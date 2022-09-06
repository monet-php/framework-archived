<?php

namespace Monet\Framework\Admin\Filament\Resources;

use Closure;
use DateTimeInterface;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Monet\Framework\Admin\Filament\Resources\UserResource\Pages\CreateUser;
use Monet\Framework\Admin\Filament\Resources\UserResource\Pages\EditUser;
use Monet\Framework\Admin\Filament\Resources\UserResource\Pages\ListUsers;
use Monet\Framework\Auth\Models\User;
use Monet\Framework\Transformer\Facades\Transformer;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'Users';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        TextInput::make(User::getUsernameIdentifierName())
                            ->label('Username')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email address')
                            ->required()
                            ->email()
                            ->unique(User::class, 'email', fn($record) => $record)
                            ->maxLength(255),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->dehydrateStateUsing(
                                fn(?string $state, Closure $get): string => Hash::make($state, ['user_id' => $get('user_id')])
                            )
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(Page $livewire): bool => $livewire instanceof CreateRecord),
                        TextInput::make('password_confirmation')
                            ->label('Confirm password')
                            ->password(),
                        MultiSelect::make('roles')
                            ->relationship('roles', 'name')
                            ->saveRelationshipsUsing(function (User $record, $state) {
                                $record->syncRoles($state);

                                if (
                                    $record->user_code === auth()->id() &&
                                    !$record->hasPermissionTo('view admin')
                                ) {
                                    $record->assignRole(Role::findById(2));
                                }
                            }),
                    ])
                    ->columns([
                        'sm' => 2,
                    ])
                    ->columnSpan([
                        'sm' => 2,
                    ]),

                Card::make()
                    ->schema([
                        Toggle::make('email_verified_at')
                            ->label('Verified')
                            ->afterStateHydrated(
                                function (Toggle $component, $state): void {
                                    $component->state($state !== null);
                                }
                            )
                            ->dehydrated(function (bool $state, ?User $record): bool {
                                if ($record === null) {
                                    return true;
                                }

                                $verified = $record->hasVerifiedEmail();

                                return $state ? !$verified : $verified;
                            })
                            ->dehydrateStateUsing(fn(bool $state): ?DateTimeInterface => $state ? now() : null),
                        Placeholder::make('created_at')
                            ->label('Created at')
                            ->content(fn(?User $record): string => $record?->created_at?->diffForHumans() ?? '-'),
                        Placeholder::make('update_time')
                            ->label('Modified at')
                            ->content(fn(?User $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
                    ])
                    ->columnSpan(1),
            ])
            ->columns([
                'sm' => 3,
                'lg' => null,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(User::getUsernameIdentifierName())
                    ->label('Username')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->sortable()
                    ->searchable(),
                BadgeColumn::make('email_verified_at')
                    ->label('Verified')
                    ->sortable()
                    ->hidden(!config('monet.auth.require_email_verification'))
                    ->getStateUsing(fn(User $record): bool => $record->hasVerifiedEmail())
                    ->enum([
                        true => 'Verified',
                        false => 'Unverified'
                    ])
                    ->colors([
                        'success' => true,
                        'danger' => false
                    ]),
                TextColumn::make('created_at')
                    ->label('Created at')
                    ->date()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('updated_at')
                    ->label('Modified at')
                    ->date()
                    ->sortable()
                    ->searchable(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->hidden(fn(User $record): bool => $record->user_code === auth()->id()),
            ])
            ->filters([
                TernaryFilter::make('email_verified_at')
                    ->label('Verified')
                    ->nullable(),
            ]);
    }

    public static function getRelations(): array
    {
        return Transformer::transform(
            'monet.admin.user.relations',
            []
        );
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationItems(): array
    {
        return [
            ...parent::getNavigationItems(),
            NavigationItem::make('Your account')
                ->group(static::getNavigationGroup())
                ->icon('heroicon-o-user')
                ->sort(static::getNavigationSort() + 1)
                ->url(route('filament.resources.users.edit', [auth()->id()])),
        ];
    }

    public static function canDelete(Model $record): bool
    {
        return $record->getAuthIdentifier() !== auth()->id() &&
            parent::canDelete($record);
    }
}
