<?php

use Laravilt\Schemas\Components\Tab;
use Laravilt\Schemas\Components\Tabs;
use Laravilt\Support\Component;

beforeEach(function () {
    $this->tabs = Tabs::make('test-tabs');
});

it('can be instantiated with make method', function () {
    $tabs = Tabs::make('test');

    expect($tabs)->toBeInstanceOf(Tabs::class)
        ->and($tabs->getName())->toBe('test');
});

it('can set and get tabs via schema method', function () {
    $tab1 = Tab::make('tab-1')->label('First Tab');
    $tab2 = Tab::make('tab-2')->label('Second Tab');

    $this->tabs->schema([$tab1, $tab2]);

    expect($this->tabs->getSchema())->toHaveCount(2)
        ->and($this->tabs->getSchema()[0])->toBe($tab1)
        ->and($this->tabs->getSchema()[1])->toBe($tab2);
});

it('has default active tab as first tab', function () {
    $props = $this->tabs->toLaraviltProps();

    expect($props['activeTab'])->toBe(0);
});

it('can set active tab index', function () {
    $this->tabs->activeTab(2);
    $props = $this->tabs->toLaraviltProps();

    expect($props['activeTab'])->toBe(2);
});

it('can persist tab in query string', function () {
    $this->tabs->persistTabInQueryString();
    $props = $this->tabs->toLaraviltProps();

    expect($props['persistTabInQueryString'])->toBeTrue();
});

it('does not persist tab by default', function () {
    $props = $this->tabs->toLaraviltProps();

    expect($props['persistTabInQueryString'])->toBeFalse();
});

it('serializes tabs to laravilt props via tabs method', function () {
    $tab1 = Tab::make('tab-1')->label('First Tab');
    $tab2 = Tab::make('tab-2')->label('Second Tab');

    // Note: Tabs component has both schema() from HasSchema trait and tabs() method
    // The toLaraviltProps() uses getTabs() which reads from $tabs property, not $schema
    // So we need to use tabs() method to set tabs for serialization
    $this->tabs->tabs([$tab1, $tab2]);
    $props = $this->tabs->toLaraviltProps();

    expect($props)->toHaveKey('tabs')
        ->and($props['tabs'])->toHaveCount(2)
        ->and($props['tabs'][0]['label'])->toBe('First Tab')
        ->and($props['tabs'][1]['label'])->toBe('Second Tab');
});

it('supports method chaining', function () {
    $result = $this->tabs
        ->activeTab(1)
        ->persistTabInQueryString()
        ->schema([]);

    expect($result)->toBe($this->tabs);
});

// Tab component tests
it('tab can be instantiated with make method', function () {
    $tab = Tab::make('test');

    expect($tab)->toBeInstanceOf(Tab::class)
        ->and($tab->getName())->toBe('test');
});

it('tab can set and get label', function () {
    $tab = Tab::make('test')->label('Test Label');

    expect($tab->getLabel())->toBe('Test Label');
});

it('tab can set and get icon', function () {
    $tab = Tab::make('test')->icon('heroicon-o-home');

    expect($tab->getIcon())->toBe('heroicon-o-home');
});

it('tab can set and get badge', function () {
    $tab = Tab::make('test')->badge('5');

    expect($tab->getBadge())->toBe('5');
});

it('tab supports closures for label', function () {
    $tab = Tab::make('test')->label(fn () => 'Dynamic Label');

    expect($tab->getLabel())->toBe('Dynamic Label');
});

it('tab can set and get schema', function () {
    $component = createTestComponent('child');

    $tab = Tab::make('test')->schema([$component]);

    expect($tab->getSchema())->toHaveCount(1)
        ->and($tab->getSchema()[0])->toBe($component);
});

it('tab serializes all properties to laravilt props', function () {
    $component = createTestComponent('child');

    $tab = Tab::make('test')
        ->label('Tab Label')
        ->icon('heroicon-o-user')
        ->badge('3')
        ->schema([$component]);

    $props = $tab->toLaraviltProps();

    expect($props)->toHaveKey('label')
        ->and($props['label'])->toBe('Tab Label')
        ->and($props)->toHaveKey('icon')
        ->and($props['icon'])->toBe('heroicon-o-user')
        ->and($props)->toHaveKey('badge')
        ->and($props['badge'])->toBe('3')
        ->and($props)->toHaveKey('schema')
        ->and($props['schema'])->toHaveCount(1);
});
