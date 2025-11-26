<?php

use Laravilt\Schemas\Schema;
use Laravilt\Support\Component;

beforeEach(function () {
    $this->schema = Schema::make('test-schema');
});

it('can be instantiated with make method', function () {
    $schema = Schema::make('test');

    expect($schema)->toBeInstanceOf(Schema::class)
        ->and($schema->getName())->toBe('test');
});

it('extends Support Component', function () {
    expect($this->schema)->toBeInstanceOf(Component::class);
});

it('can set and get schema', function () {
    $component = createTestComponent('child-component');

    $this->schema->schema([$component]);

    expect($this->schema->getSchema())->toHaveCount(1)
        ->and($this->schema->getSchema()[0])->toBe($component);
});

it('filters hidden components', function () {
    $visible = createTestComponent('visible');

    $hidden = createTestComponent('hidden');
    $hidden->hidden();

    $this->schema->schema([$visible, $hidden]);

    expect($this->schema->getVisibleComponents())->toHaveCount(1)
        ->and($this->schema->getVisibleComponents()[0])->toBe($visible);
});

it('serializes schema to laravilt props', function () {
    $component = createTestComponent('child');
    $component->label('Child Component');

    $this->schema->schema([$component]);

    $props = $this->schema->toLaraviltProps();

    expect($props)->toHaveKey('schema')
        ->and($props['schema'])->toHaveCount(1)
        ->and($props['schema'][0])->toBeArray()
        ->and($props['schema'][0]['name'])->toBe('child')
        ->and($props['schema'][0]['label'])->toBe('Child Component');
});

it('inherits all Support Component traits', function () {
    $this->schema
        ->label('Test Schema')
        ->helperText('Helper text')
        ->disabled()
        ->required();

    expect($this->schema->getLabel())->toBe('Test Schema')
        ->and($this->schema->getHelperText())->toBe('Helper text')
        ->and($this->schema->isDisabled())->toBeTrue()
        ->and($this->schema->isRequired())->toBeTrue();
});

it('converts to array', function () {
    $array = $this->schema->toArray();

    expect($array)->toBeArray()
        ->and($array)->toHaveKey('component')
        ->and($array)->toHaveKey('schema');
});

it('converts to json', function () {
    $json = $this->schema->toJson();

    expect($json)->toBeString();
    $decoded = json_decode($json, true);
    expect($decoded)->toBeArray()
        ->and($decoded)->toHaveKey('schema');
});
