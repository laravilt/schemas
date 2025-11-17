<?php

namespace Laravilt\Schemas\Tests\Unit;

use Laravilt\Schemas\Components\Grid;
use Laravilt\Support\Component;
use Orchestra\Testbench\TestCase;

class GridTest extends TestCase
{
    public function test_can_create_grid(): void
    {
        $grid = Grid::make('test-grid');

        $this->assertInstanceOf(Grid::class, $grid);
        $this->assertInstanceOf(Component::class, $grid);
    }

    public function test_can_set_columns_as_integer(): void
    {
        $grid = Grid::make('test-grid')
            ->columns(3);

        $this->assertEquals(3, $grid->getColumns());
    }

    public function test_can_set_columns_as_array(): void
    {
        $columns = [
            'default' => 1,
            'md' => 2,
            'lg' => 3,
        ];

        $grid = Grid::make('test-grid')
            ->columns($columns);

        $this->assertEquals($columns, $grid->getColumns());
    }

    public function test_can_set_schema(): void
    {
        $component = $this->createMockComponent();

        $grid = Grid::make('test-grid')
            ->schema([$component]);

        $this->assertCount(1, $grid->getSchema());
    }

    public function test_serializes_to_laravilt_props(): void
    {
        $component = $this->createMockComponent();

        $grid = Grid::make('test-grid')
            ->columns(2)
            ->schema([$component]);

        $props = $grid->toLaraviltProps();

        $this->assertArrayHasKey('columns', $props);
        $this->assertEquals(2, $props['columns']);
        $this->assertArrayHasKey('schema', $props);
        $this->assertCount(1, $props['schema']);
    }

    public function test_serializes_responsive_columns(): void
    {
        $columns = ['default' => 1, 'md' => 2, 'lg' => 3];

        $grid = Grid::make('test-grid')
            ->columns($columns);

        $props = $grid->toLaraviltProps();

        $this->assertEquals($columns, $props['columns']);
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
