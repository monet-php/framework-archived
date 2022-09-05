<?php

namespace Monet\Framework\Theme\Models;

use Illuminate\Database\Eloquent\Model;
use Monet\Framework\Support\Traits\Macroable;
use Monet\Framework\Theme\Facades\Themes;
use Monet\Framework\Transformer\Facades\Transformer;
use Sushi\Sushi;

class Theme extends Model
{
    use Macroable;
    use Sushi;

    public $incrementing = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->macroConstruct();
    }

    public function getKeyType(): string
    {
        return Transformer::transform(
            'monet.themes.theme.model.keyType',
            'string'
        );
    }

    public function getSchema(): array
    {
        return Transformer::transform(
            'monet.themes.theme.model.schema',
            [
                'id' => 'string',
                'name' => 'string',
                'description' => 'string',
                'path' => 'string',
                'parent' => 'string',
                'enabled' => 'integer',
            ]
        );
    }

    public function getCasts(): array
    {
        return Transformer::transform(
            'monet.themes.theme.model.casts',
            [
                'enabled' => 'boolean',
            ]
        );
    }

    public function getRows(): array
    {
        $enabledThemeName = Themes::enabled()?->getName();

        return Transformer::transform(
            'monet.themes.theme.model.rows',
            collect(Themes::all())
                ->map(fn ($theme): array => [
                    'id' => $theme->getName(),
                    'name' => $theme->getName(),
                    'description' => $theme->getDescription(),
                    'path' => $theme->getPath(),
                    'parent' => $theme->getParent(),
                    'enabled' => $enabledThemeName === $theme->getName(),
                ])
                ->values()
                ->all()
        );
    }
}
