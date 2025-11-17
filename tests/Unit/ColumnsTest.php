<?php

namespace Laravilt\Schemas\Tests\Unit;

use Laravilt\Schemas\Components\Columns;
use Laravilt\Support\Component;
use Orchestra\Testbench\TestCase;

class ColumnsTest extends TestCase
{
    public function test_can_create_columns(): void
    {
        $columns = Columns::make('test-columns');

        $this->assertInstanceOf(Columns::class, $columns);
        $this->assertInstanceOf(Component::class, $columns);
    }

    public function test_can_set_column_span_as_integer(): void
    {
        $columns = Columns::make('test-columns')
            ->columnSpan(2);

        $this->assertEquals(2, $columns->getColumnSpan());
    }

    public function test_can_set_column_span_as_string(): void
    {
        $columns = Columns::make('test-columns')
            ->columnSpan('full');

        $this->assertEquals('full', $columns->getColumnSpan());
    }

    public function test_can_set_schema(): void
    {
        $component = $this->createMockComponent();

        $columns = Columns::make('test-columns')
            ->schema([$component]);

        $this->assertCount(1, $columns->getSchema());
    }

    public function test_serializes_to_laravilt_props(): void
    {
        $component = $this->createMockComponent();

        $columns = Columns::make('test-columns')
            ->columnSpan(2)
            ->schema([$component]);

        $props = $columns->toLaraviltProps();

        $this->assertEquals(2, $props['columnSpan']);
        $this->assertArrayHasKey('schema', $props);
        $this->assertCount(1, $props['schema']);
    }

    public function test_serializes_string_column_span(): void
    {
        $columns = Columns::make('test-columns')
            ->columnSpan('full');

        $props = $columns->toLaraviltProps();

        $this->assertEquals('full', $props['columnSpan']);
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
