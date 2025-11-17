<?php

namespace Laravilt\Schemas\Tests\Unit;

use Laravilt\Schemas\Components\Tab;
use Laravilt\Support\Component;
use Orchestra\Testbench\TestCase;

class TabTest extends TestCase
{
    public function test_can_create_tab(): void
    {
        $tab = Tab::make('test-tab');

        $this->assertInstanceOf(Tab::class, $tab);
        $this->assertInstanceOf(Component::class, $tab);
    }

    public function test_can_set_label(): void
    {
        $tab = Tab::make('test-tab')
            ->label('Test Label');

        $this->assertEquals('Test Label', $tab->getLabel());
    }

    public function test_can_set_icon(): void
    {
        $icon = '<svg>...</svg>';

        $tab = Tab::make('test-tab')
            ->icon($icon);

        $this->assertEquals($icon, $tab->getIcon());
    }

    public function test_can_set_badge(): void
    {
        $tab = Tab::make('test-tab')
            ->badge('5');

        $this->assertEquals('5', $tab->getBadge());
    }

    public function test_can_set_schema(): void
    {
        $component = $this->createMockComponent();

        $tab = Tab::make('test-tab')
            ->schema([$component]);

        $this->assertCount(1, $tab->getSchema());
    }

    public function test_serializes_to_laravilt_props(): void
    {
        $component = $this->createMockComponent();

        $tab = Tab::make('test-tab')
            ->label('Profile')
            ->icon('<svg></svg>')
            ->badge('3')
            ->schema([$component]);

        $props = $tab->toLaraviltProps();

        $this->assertEquals('Profile', $props['label']);
        $this->assertEquals('<svg></svg>', $props['icon']);
        $this->assertEquals('3', $props['badge']);
        $this->assertCount(1, $props['schema']);
    }

    public function test_evaluates_closures(): void
    {
        $tab = Tab::make('test-tab')
            ->label(fn () => 'Dynamic Label')
            ->badge(fn () => '10');

        $this->assertEquals('Dynamic Label', $tab->getLabel());
        $this->assertEquals('10', $tab->getBadge());
    }

    protected function createMockComponent(): Component
    {
        $component = $this->getMockBuilder(Component::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['toLaraviltProps'])
            ->getMock();

        $component->method('toLaraviltProps')->willReturn(['name' => 'test']);

        return $component;
    }
}
