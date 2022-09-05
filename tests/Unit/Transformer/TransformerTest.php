<?php

use Monet\Framework\Transformer\Facades\Transformer;

it('returns the same data if there are no transformers', function () {
    $data = [
        '::key::' => '::value::',
    ];

    expect(Transformer::transform('test', $data))->toEqual($data);
});

it('returns the mutated data if there are transformers', function () {
    $oldData = [
        '::key::' => '::value::',
    ];

    $newData = [
        '::key::' => '::new-value::',
    ];

    Transformer::register('test', fn (array $value): array => $newData);

    expect(Transformer::transform('test', $oldData))
        ->toEqual($newData);
});
