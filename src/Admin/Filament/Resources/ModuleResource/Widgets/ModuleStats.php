<?php

namespace Monet\Framework\Admin\Filament\Resources\ModuleResource\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Monet\Framework\Module\Facades\Modules;

class ModuleStats extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        $filter = 'tableFilters[status][values][0]';

        return [
            Card::make(
                'Total modules',
                number_format(count(Modules::all()))
            )
                ->url(
                    route('filament.resources.extend/modules.index')
                ),
            Card::make(
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
            Card::make(
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
