<?php

namespace Monet\Framework\Admin\Filament\Resources\ModuleResource\Pages;

use Filament\Forms;
use Filament\Notifications;
use Filament\Pages;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Monet\Framework\Admin\Filament\Resources\ModuleResource;
use Monet\Framework\Module\Facades\Modules;
use Monet\Framework\Module\Installer\ModuleInstaller;
use Monet\Framework\Module\Models\Module;
use Monet\Framework\Module\Repository\ModuleRepositoryInterface;
use Monet\Framework\Transformer\Facades\Transformer;

class ListModules extends ListRecords
{
    protected static string $resource = ModuleResource::class;

    public function enableModule(ModuleRepositoryInterface $modules, Module $record): void
    {
        $reason = null;

        if (!$modules->enable($record->name, $reason)) {
            Notifications\Notification::make()
                ->danger()
                ->title(sprintf('Module "%s" has failed to be enabled', $record->name))
                ->body($reason)
                ->send();

            return;
        }

        $record->forceFill(['status' => 'enabled'])->save();

        Notifications\Notification::make()
            ->success()
            ->title(sprintf('Module "%s" has been successfully enabled', $record->name))
            ->body('This includes any dependency modules')
            ->actions([
                Notifications\Actions\Action::make('refresh')
                    ->button()
                    ->url(route('filament.resources.extend/modules.index')),
            ])
            ->send();
    }

    public function disableModule(ModuleRepositoryInterface $modules, Module $record): void
    {
        $modules->disable($record->name);

        $record->forceFill(['status' => 'disabled'])->save();

        Notifications\Notification::make()
            ->success()
            ->title(sprintf('Module "%s" has been successfully disabled', $record->name))
            ->body('This includes any dependent modules')
            ->actions([
                Notifications\Actions\Action::make('refresh')
                    ->button()
                    ->url(route('filament.resources.extend/modules.index')),
            ])
            ->send();
    }

    public function publishModule(ModuleRepositoryInterface $modules, Module $record, array $data): void
    {
        $installer = app(ModuleInstaller::class);

        if ($module = $modules->find($record->name)) {
            $installer->publish($module->getProviders(), $data['run_migrations']);
        }

        Notifications\Notification::make()
            ->success()
            ->title('Module assets have been published')
            ->send();
    }

    public function deleteModule(ModuleRepositoryInterface $modules, Module $record): void
    {
        $reason = null;

        if (!$modules->delete($record->name, $reason)) {
            Notifications\Notification::make()
                ->danger()
                ->title(sprintf('Module "%s" has been unsuccessfully deleted', $record->name))
                ->body($reason)
                ->send();

            return;
        }

        $record->delete();

        Notifications\Notification::make()
            ->success()
            ->title(sprintf('Module "%s" has been successfully deleted', $record->name))
            ->actions([
                Notifications\Actions\Action::make('refresh')
                    ->button()
                    ->url(route('filament.resources.extend/modules.index')),
            ])
            ->send();
    }

    public function enableBulk(ModuleRepositoryInterface $modules, Collection $records): void
    {
        $count = 0;
        foreach ($records as $module) {
            if ($module->enabled) {
                return;
            }

            $reason = null;

            if (!$modules->enable($module->name, $reason)) {
                Notifications\Notification::make()
                    ->danger()
                    ->title(sprintf('Module "%s" has failed to be enabled', $module->name))
                    ->body($reason)
                    ->send();

                continue;
            }

            $module->forceFill(['status' => 'enabled'])->save();

            $count++;
        }

        Notifications\Notification::make()
            ->success()
            ->title(
                sprintf(
                    '%s %s have been successfully enabled',
                    number_format($count),
                    Str::plural('module', $count)
                )
            )
            ->body('This includes any dependency modules')
            ->actions([
                Notifications\Actions\Action::make('refresh')
                    ->button()
                    ->url(route('filament.resources.extend/modules.index')),
            ])
            ->send();
    }

    public function disableBulk(ModuleRepositoryInterface $modules, Collection $records): void
    {
        $count = 0;
        foreach ($records as $module) {
            if ($module->disabled) {
                continue;
            }

            $modules->disable($module->name);
            $module->forceFill(['status' => 'disabled'])->save();

            $count++;
        }

        Notifications\Notification::make()
            ->success()
            ->title(
                sprintf(
                    '%s %s have been successfully disabled',
                    number_format($count),
                    Str::plural('count', $count)
                )
            )
            ->body('This includes any dependency modules')
            ->actions([
                Notifications\Actions\Action::make('refresh')
                    ->button()
                    ->url(route('filament.resources.extend/modules.index')),
            ])
            ->send();
    }

    public function deleteBulk(ModuleRepositoryInterface $modules, Collection $records): void
    {
        $count = 0;
        foreach ($records as $module) {
            $reason = null;

            if (!$modules->delete($module->name, $reason)) {
                Notifications\Notification::make()
                    ->danger()
                    ->title(
                        sprintf(
                            'Failed to delete module "%s"',
                            $module->name
                        )
                    )
                    ->body($reason)
                    ->send();

                continue;
            }

            $module->delete();

            $count++;
        }

        Notifications\Notification::make()
            ->success()
            ->title(
                sprintf(
                    '%s %s have been successfully deleted',
                    number_format($count),
                    Str::plural('module', $count)
                )
            )
            ->actions([
                Notifications\Actions\Action::make('refresh')
                    ->button()
                    ->url(route('filament.resources.extend/modules.index')),
            ])
            ->send();
    }

    public function installModules(ModuleRepositoryInterface $modules, array $data): void
    {
        $count = 0;
        foreach ($data['modules'] as $path) {
            $file = Storage::disk('local')->path($path);

            if (!$modules->install($file, $reason)) {
                Notifications\Notification::make()
                    ->danger()
                    ->title(
                        sprintf(
                            'Failed to install module "%s"',
                            basename($path)
                        )
                    )
                    ->body($reason)
                    ->send();

                continue;
            }

            $count++;

            Storage::disk('local')->delete($path);
        }

        Notifications\Notification::make()
            ->success()
            ->title(
                sprintf(
                    '%s %s has been successfully installed',
                    number_format($count),
                    Str::plural('module', $count)
                )
            )
            ->actions([
                Notifications\Actions\Action::make('refresh')
                    ->button()
                    ->url(route('filament.resources.extend/modules.index')),
            ])
            ->send();
    }

    protected function getActions(): array
    {
        return Transformer::transform(
            'monet.admin.modules.list.page.actions',
            [
                Pages\Actions\Action::make('install')
                    ->label('Install modules')
                    ->action('installModules')
                    ->form([
                        Forms\Components\FileUpload::make('modules')
                            ->label('Modules')
                            ->disableLabel()
                            ->disk('local')
                            ->directory('modules-tmp')
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
