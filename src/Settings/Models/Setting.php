<?php

namespace Monet\Framework\Settings\Models;

use Illuminate\Database\Eloquent\Model;
use Monet\Framework\Support\Traits\Macroable;
use Monet\Framework\Transformer\Facades\Transformer;

class Setting extends Model
{
    use Macroable;

    public function getTable()
    {
        return config('monet.settings.database.table');
    }

    public function getFillable(): array
    {
        return Transformer::transform(
            'monet.settings.setting.model.fillable',
            array_values(
                config('monet.settings.database.columns')
            )
        );
    }

    public function getHidden(): array
    {
        return Transformer::transform(
            'monet.settings.setting.model.hidden',
            array_values(
                config('monet.settings.database.columns')
            )
        );
    }

    public function getCasts(): array
    {
        return Transformer::transform(
            'monet.settings.setting.model.casts',
            [
                config('monet.settings.database.columns.value') => 'array',
                config('monet.settings.database.columns.autoload') => 'boolean',
            ]
        );
    }
}
