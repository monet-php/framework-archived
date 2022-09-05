<?php

namespace Monet\Framework\Admin\Filament\Resources\ModuleResources\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Monet\Framework\Module\Facades\Modules;

class ModuleStats extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        $filter = 'tableFilters[status][values][0]';

        return [
            StatsOverviewWidget\Card::make(
                'Total modules',
                number_format(count(Modules::all()))
            )
                ->url(
                    route('filament.resources.extend/modules.index')
                ),
            StatsOverviewWidget\Card::make(
                'Enabled modules',
                number_format(count(Modules::enabled()))
            )
                ->url(
                    route(
                        'filament.resources.extend/modules.index',
                        [
                            $filter => 'enabled',
                        ]
                    )
                ),
            StatsOverviewWidget\Card::make(
                'Disabled modules',
                number_format(count(Modules::disabled()))
            )
                ->url(
                    route(
                        'filament.resources.extend/modules.index',
                        [
                            $filter => 'disabled',
                        ]
                    )
                ),
        ];
    }
}
