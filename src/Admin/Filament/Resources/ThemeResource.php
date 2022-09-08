<?php

namespace Monet\Framework\Admin\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Model;
use Monet\Framework\Admin\Filament\Resources\ThemeResource\Pages\ListThemes;
use Monet\Framework\Admin\Filament\Resources\ThemeResource\Widgets\ThemeStats;
use Monet\Framework\Auth\Models\User;
use Monet\Framework\Theme\Models\Theme;
use Monet\Framework\Transformer\Facades\Transformer;

class ThemeResource extends Resource
{
    protected static ?string $model = Theme::class;

    protected static ?string $slug = 'appearance/themes';

    protected static ?string $navigationGroup = 'Appearance';

    protected static ?string $navigationIcon = 'heroicon-o-color-swatch';

    protected static ?int $navigationSort = -9999;

    public static function table(Table $table): Table
    {
        return Transformer::transform(
            'monet.admin.themes.table',
            $table
                ->columns(
                    Transformer::transform(
                        'monet.admin.themes.table.columns',
                        [
                            TextColumn::make('name')
                                ->label('Name')
                                ->sortable()
                                ->searchable(),
                            TextColumn::make('description')
                                ->label('Description')
                                ->sortable()
                                ->searchable()
                                ->wrap(),
                            BadgeColumn::make('enabled')
                                ->label('Status')
                                ->sortable()
                                ->enum([
                                    true => 'Enabled',
                                    false => 'Disabled'
                                ])
                                ->colors([
                                    'success' => true,
                                    'danger' => false
                                ]),
                        ]
                    )
                )
                ->filters(
                    Transformer::transform(
                        'monet.admin.themes.table.filters',
                        [
                            TernaryFilter::make('enabled')
                                ->label('Enabled'),
                        ]
                    )
                )
                ->bulkActions(
                    Transformer::transform(
                        'monet.admin.themes.table.bulkActions',
                        [
                            BulkAction::make('delete')
                                ->label('Delete selected')
                                ->color('danger')
                                ->icon('heroicon-o-trash')
                                ->requiresConfirmation()
                                ->action('deleteBulk'),
                        ]
                    )
                )
        );
    }

    public static function getPages(): array
    {
        return Transformer::transform(
            'monet.admin.themes.pages',
            [
                'index' => ListThemes::route('/'),
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
            'monet.admin.themes.widgets',
            [
                ThemeStats::class,
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
            'monet.admin.themes.search.title',
            $record->name,
            [
                'theme' => $record
            ]
        );
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return Transformer::transform(
            'monet.admin.themes.search.details',
            [
                'description' => $record->description,
                'version' => $record->version
            ],
            [
                'theme' => $record
            ]
        );
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return static::getUrl('index');
    }
}
