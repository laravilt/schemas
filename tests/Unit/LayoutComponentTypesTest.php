<?php

use Laravilt\Schemas\Components\Grid;
use Laravilt\Schemas\Components\Section;
use Laravilt\Schemas\Components\Split;
use Laravilt\Schemas\Components\Step;
use Laravilt\Schemas\Components\Tab;
use Laravilt\Schemas\Components\Tabs;
use Laravilt\Schemas\Components\Wizard;

/*
 * The frontend Schema (Vue and React) maps these serialized component types to its layout
 * components and reads their nested schemas from these keys. Keep them in sync.
 */

it('serializes the layout component types the frontend maps', function () {
    expect(Section::make('Details')->toLaraviltProps()['component'])->toBe('section')
        ->and(Grid::make(2)->toLaraviltProps()['component'])->toBe('grid')
        ->and(Tabs::make('tabs')->toLaraviltProps()['component'])->toBe('tabs')
        ->and(Split::make('split')->toLaraviltProps()['component'])->toBe('split')
        ->and(Wizard::make('wizard')->toLaraviltProps()['component'])->toBe('wizard');
});

it('serializes split nested schemas under startSchema and endSchema', function () {
    $props = Split::make('split')
        ->startSchema([createTestComponent('first')])
        ->endSchema([createTestComponent('second')])
        ->startColumnSpan(8)
        ->endColumnSpan(4)
        ->fromBreakpoint('lg')
        ->toLaraviltProps();

    expect($props['startSchema'])->toHaveCount(1)
        ->and($props['startSchema'][0]['name'])->toBe('first')
        ->and($props['endSchema'][0]['name'])->toBe('second')
        ->and($props['startColumnSpan'])->toBe(8)
        ->and($props['endColumnSpan'])->toBe(4)
        ->and($props['fromBreakpoint'])->toBe('lg');
});

it('serializes wizard steps with their schema and icon name', function () {
    $props = Wizard::make('wizard')
        ->steps([
            Step::make('account')->label('Account')->icon('user')->schema([createTestComponent('email')]),
        ])
        ->toLaraviltProps();

    expect($props['steps'])->toHaveCount(1)
        ->and($props['steps'][0]['icon'])->toBe('user')
        ->and($props['steps'][0]['schema'][0]['name'])->toBe('email');
});

it('serializes persistTabInQueryString and tab ids for the tabs query string', function () {
    $props = Tabs::make('tabs')
        ->tabs([Tab::make('General Info')])
        ->persistTabInQueryString()
        ->toLaraviltProps();

    expect($props['persistTabInQueryString'])->toBeTrue()
        ->and($props['tabs'][0]['id'])->toBe('general_info');

    expect(Tabs::make('tabs')->toLaraviltProps()['persistTabInQueryString'])->toBeFalse();
});
