<?php

namespace Monet\Framework\Admin\Filament\Resources\ThemeResource\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Monet\Framework\Theme\Facades\Themes;

class ThemeStats extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        $filter = 'tableFilters[enabled][value]';

        return [
            Card::make(
                'Total themes',
                number_format(count(Themes::all()))
            )
                ->url(
                    route('filament.resources.appearance/themes.index')
                ),
            Card::make(
                'Enabled themes',
                Themes::enabled() !== null ? 1 : 0
            )
                ->url(
                    route(
                        'filament.resources.appearance/themes.index',
                        [
                            $filter => 1,
                        ]
                    )
                ),
            Card::make(
                'Disabled themes',
                number_format(count(Themes::disabled()))
            )
                ->url(
                    route(
                        'filament.resources.appearance/themes.index',
                        [
                            $filter => 0,
                        ]
                    )
                ),
        ];
    }
}
