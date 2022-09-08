<?php

namespace Monet\Framework\Admin\Filament\Resources\ThemeResource\Pages;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Monet\Framework\Admin\Filament\Resources\ThemeResource;
use Monet\Framework\Theme\Facades\Themes;
use Monet\Framework\Theme\Installer\ThemeInstaller;
use Monet\Framework\Theme\Models\Theme;
use Monet\Framework\Transformer\Facades\Transformer;

class ListThemes extends ListRecords
{
    protected static string $resource = ThemeResource::class;

    public function enableTheme(Theme $record): void
    {
        $reason = null;

        if (!Themes::enable($record->name, $reason)) {
            Notification::make()
                ->danger()
                ->title(sprintf('Theme "%s" has failed to be enabled', $record->name))
                ->body($reason)
                ->send();

            return;
        }

        $record->forceFill(['enabled' => true])->save();

        Notification::make()
            ->success()
            ->title(sprintf('Theme "%s" has been successfully enabled', $record->name))
            ->body(fn() => $record->parent !== null ? 'This includes the parent theme' : null)
            ->actions([
                \Filament\Notifications\Actions\Action::make('refresh')
                    ->button()
                    ->url(route('filament.resources.appearance/themes.index')),
            ])
            ->send();
    }

    public function disableTheme(Theme $record): void
    {
        Themes::disable();

        $record->forceFill(['enabled' => false])->save();

        Notification::make()
            ->success()
            ->title(sprintf('Theme "%s" has been successfully disabled', $record->name))
            ->body(fn() => $record->parent !== null ? 'This includes the parent theme' : null)
            ->actions([
                \Filament\Notifications\Actions\Action::make('refresh')
                    ->button()
                    ->url(route('filament.resources.appearance/themes.index')),
            ])
            ->send();
    }

    public function publishTheme(Theme $record, array $data): void
    {
        $installer = app(ThemeInstaller::class);

        if (
            $record->parent !== null &&
            ($parent = Themes::find($record->parent))
        ) {
            $installer->publish($parent->getProviders(), $data['run_migrations']);
        }

        if ($theme = Themes::find($record->name)) {
            $installer->publish($theme->getProviders(), $data['run_migrations']);
        }

        Notification::make()
            ->success()
            ->title('Theme assets have been published')
            ->send();
    }

    public function deleteTheme(Theme $record): void
    {
        $reason = null;

        if (!Themes::delete($record->name, $reason)) {
            Notification::make()
                ->danger()
                ->title(sprintf('Theme "%s" has been unsuccessfully deleted', $record->name))
                ->body($reason)
                ->send();

            return;
        }

        $record->delete();

        Notification::make()
            ->success()
            ->title(sprintf('Theme "%s" has been successfully deleted', $record->name))
            ->actions([
                \Filament\Notifications\Actions\Action::make('refresh')
                    ->button()
                    ->url(route('filament.resources.appearance/themes.index')),
            ])
            ->send();
    }

    public function deleteBulk(Collection $records): void
    {
        $count = 0;
        foreach ($records as $theme) {
            $reason = null;

            if (!Themes::delete($theme->name, $reason)) {
                Notification::make()
                    ->danger()
                    ->title(
                        sprintf(
                            'Failed to delete theme "%s"',
                            $theme->name
                        )
                    )
                    ->body($reason)
                    ->send();

                continue;
            }

            $theme->delete();

            $count++;
        }

        Notification::make()
            ->success()
            ->title(
                sprintf(
                    '%s themes have been successfully deleted',
                    number_format($count)
                )
            )
            ->actions([
                \Filament\Notifications\Actions\Action::make('refresh')
                    ->button()
                    ->url(route('filament.resources.appearance/themes.index')),
            ])
            ->send();
    }

    public function installThemes(array $data): void
    {
        $count = 0;
        foreach ($data['themes'] as $path) {
            $file = Storage::disk('local')->path($path);

            $reason = null;

            if (Themes::install($file, $reason)) {
                $count++;
            } else {
                Notification::make()
                    ->danger()
                    ->title(
                        sprintf(
                            'Failed to install theme "%s"',
                            basename($path)
                        )
                    )
                    ->body($reason)
                    ->send();
            }

            Storage::disk('local')->delete($path);
        }

        Notification::make()
            ->success()
            ->title(
                sprintf(
                    '%s %s has been successfully installed',
                    number_format($count),
                    Str::plural('theme', $count)
                )
            )
            ->actions([
                \Filament\Notifications\Actions\Action::make('refresh')
                    ->button()
                    ->url(route('filament.resources.appearance/themes.index')),
            ])
            ->send();
    }

    protected function getTableActions(): array
    {
        return Transformer::transform(
            'monet.admin.themes.list.table.actions',
            [
                ActionGroup::make([
                    Action::make('enable')
                        ->label('Enable')
                        ->hidden(fn(Theme $record): bool => $record->enabled)
                        ->icon('heroicon-o-check')
                        ->requiresConfirmation()
                        ->action('enableTheme'),
                    Action::make('disable')
                        ->label('Disable')
                        ->hidden(fn(Theme $record): bool => !$record->enabled)
                        ->icon('heroicon-o-x')
                        ->requiresConfirmation()
                        ->action('disableTheme'),
                    Action::make('publish')
                        ->label('Publish assets')
                        ->icon('heroicon-o-document-duplicate')
                        ->action('publishTheme')
                        ->form([
                            Checkbox::make('run_migrations')
                                ->label('Run database migrations')
                                ->helperText('This will ensure the database is up-to date')
                        ]),
                    Action::make('delete')
                        ->label('Delete')
                        ->color('danger')
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation()
                        ->action('deleteTheme')
                ])->label('Manage'),
            ]
        );
    }

    protected function getActions(): array
    {
        return Transformer::transform(
            'monet.admin.themes.list.page.actions',
            [
                \Filament\Pages\Actions\Action::make('install')
                    ->label('Install themes')
                    ->action('installThemes')
                    ->visible(fn() => auth()->user()->can('viewAny', Theme::class))
                    ->form([
                        FileUpload::make('themes')
                            ->label('Themes')
                            ->disableLabel()
                            ->disk('local')
                            ->directory('themes-tmp')
                            ->preserveFilenames()
                            ->multiple()
                            ->minFiles(1)
                            ->acceptedFileTypes([
                                'application/zip',
                                'application/x-zip-compressed',
                                'multipart/x-zip',
                            ]),
                    ]),
            ]
        );
    }

    protected function getHeaderWidgets(): array
    {
        return static::$resource::getWidgets();
    }
}
