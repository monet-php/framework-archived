<?php

namespace Monet\Framework\Module\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Monet\Framework\Module\Facades\Modules;
use Monet\Framework\Support\Traits\Macroable;
use Monet\Framework\Transformer\Facades\Transformer;
use Sushi\Sushi;

class Module extends Model
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
            'monet.modules.module.model.keyType',
            'string'
        );
    }

    public function getSchema(): array
    {
        return Transformer::transform(
            'monet.modules.module.model.schema',
            [
                'id' => 'string',
                'name' => 'string',
                'description' => 'string',
                'version' => 'string',
                'path' => 'string',
                'status' => 'string',
            ]
        );
    }

    public function getRows(): array
    {
        return Transformer::transform(
            'monet.modules.module.model.rows',
            collect(Modules::all())
                ->map(fn ($module): array => [
                    'id' => $module->getName(),
                    'name' => $module->getName(),
                    'description' => $module->getDescription(),
                    'version' => $module->getVersion(),
                    'path' => $module->getPath(),
                    'status' => $module->getStatus(),
                ])
                ->values()
                ->all()
        );
    }

    public function enabled(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => $this->status === 'enabled'
        );
    }

    public function disabled(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => $this->status === 'disabled'
        );
    }
}
