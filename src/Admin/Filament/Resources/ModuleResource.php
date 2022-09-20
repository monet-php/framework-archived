<?php

namespace Monet\Framework\Admin\Filament\Resources;

use Filament\Forms;
use Filament\Notifications;
use Filament\Pages;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Monet\Framework\Admin\Filament\Resources\ModuleResource\Pages\ListModules;
use Monet\Framework\Admin\Filament\Resources\ModuleResource\Widgets\ModuleStats;
use Monet\Framework\Module\Models\Module;
use Monet\Framework\Transformer\Facades\Transformer;

class ModuleResource extends Resource
{
    protected static ?string $model = Module::class;

    protected static ?string $slug = 'extend/modules';

    protected static ?string $navigationGroup = 'Extend';

    protected static ?string $navigationIcon = 'heroicon-o-puzzle';

    protected static ?int $navigationSort = -9999;

    public static function table(Table $table): Table
    {
        return Transformer::transform(
            'monet.admin.modules.table',
            $table
                ->columns(
                    Transformer::transform(
                        'monet.admin.modules.table.columns',
                        [
                            Tables\Columns\TextColumn::make('name')
                                ->label('Name')
                                ->sortable()
                                ->searchable(),
                            Tables\Columns\TagsColumn::make('version')
                                ->label('Version')
                                ->sortable()
                                ->searchable()
                                ->separator(),
                            Tables\Columns\TextColumn::make('description')
                                ->label('Description')
                                ->sortable()
                                ->searchable()
                                ->wrap(),
                            Tables\Columns\BadgeColumn::make('status')
                                ->label('Status')
                                ->sortable()
                                ->searchable()
                                ->formatStateUsing(fn(string $state): string => __(ucfirst($state)))
                                ->icons([
                                    'heroicon-o-minus-sm',
                                    'heroicon-o-x' => 'disabled',
                                    'heroicon-o-check' => 'enabled',
                                ])
                                ->colors([
                                    'warning',
                                    'danger' => 'disabled',
                                    'success' => 'enabled',
                                ]),
                        ]
                    )
                )
                ->filters(
                    Transformer::transform(
                        'monet.admin.modules.table.filters',
                        [
                            Tables\Filters\SelectFilter::make('status')
                                ->label('Status')
                                ->options([
                                    'enabled' => 'Enabled',
                                    'disabled' => 'Disabled',
                                ]),
                        ]
                    )
                )
                ->bulkActions(
                    Transformer::transform(
                        'monet.admin.modules.table.bulkActions',
                        [
                            Tables\Actions\BulkAction::make('enable')
                                ->label('Enable selected')
                                ->icon('heroicon-o-check')
                                ->requiresConfirmation()
                                ->action('enableBulk'),
                            Tables\Actions\BulkAction::make('disable')
                                ->label('Disable selected')
                                ->icon('heroicon-o-x')
                                ->requiresConfirmation()
                                ->action('disableBulk'),
                            Tables\Actions\BulkAction::make('delete')
                                ->label('Delete selected')
                                ->color('danger')
                                ->icon('heroicon-o-trash')
                                ->requiresConfirmation()
                                ->action('deleteBulk'),
                        ]
                    )
                )
                ->actions(
                    Transformer::transform(
                        'monet.admin.modules.list.table.actions',
                        [
                            Tables\Actions\ActionGroup::make([
                                Tables\Actions\Action::make('enable')
                                    ->label('Enable')
                                    ->hidden(fn(Module $record): bool => $record->enabled)
                                    ->icon('heroicon-o-check')
                                    ->requiresConfirmation()
                                    ->action('enableModule'),
                                Tables\Actions\Action::make('disable')
                                    ->label('Disable')
                                    ->hidden(fn(Module $record): bool => $record->disabled)
                                    ->icon('heroicon-o-x')
                                    ->requiresConfirmation()
                                    ->action('disableModule'),
                                Tables\Actions\Action::make('publish')
                                    ->label('Publish assets')
                                    ->icon('heroicon-o-document-duplicate')
                                    ->action('publishModule')
                                    ->form([
                                        Forms\Components\Checkbox::make('run_migrations')
                                            ->label('Run database migrations')
                                            ->helperText('This will ensure the database is up-to date')
                                    ]),
                                Tables\Actions\Action::make('delete')
                                    ->label('Delete')
                                    ->color('danger')
                                    ->icon('heroicon-o-trash')
                                    ->requiresConfirmation()
                                    ->action('deleteModule')
                            ])->label('Manage'),
                        ])
                )
        );
    }

    public static function getPages(): array
    {
        return Transformer::transform(
            'monet.admin.modules.pages',
            [
                'index' => ListModules::route('/'),
            ]
        );
    }

    protected static function getNavigationBadge(): ?string
    {
        return __(number_format(static::getModel()::count()) . ' Installed');
    }

    public static function getWidgets(): array
    {
        return Transformer::transform(
            'monet.admin.modules.widgets',
            [
                ModuleStats::class,
            ]
        );
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'description'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return Transformer::transform(
            'monet.admin.modules.search.title',
            $record->name,
            [
                'module' => $record
            ]
        );
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return Transformer::transform(
            'monet.admin.modules.search.details',
            [
                'description' => $record->description,
                'version' => $record->version
            ],
            [
                'module' => $record
            ]
        );
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return static::getUrl();
    }
}
