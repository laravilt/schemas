<?php

namespace Laravilt\Schemas\Tests\Unit;

use Laravilt\Schemas\Schema;
use Laravilt\Support\Component;
use Orchestra\Testbench\TestCase;

class SchemaTest extends TestCase
{
    public function test_can_create_schema(): void
    {
        $schema = Schema::make('test-schema');

        $this->assertInstanceOf(Schema::class, $schema);
        $this->assertInstanceOf(Component::class, $schema);
    }

    public function test_can_set_schema(): void
    {
        $component1 = $this->createMockComponent('component-1');
        $component2 = $this->createMockComponent('component-2');

        $schema = Schema::make('test-schema')
            ->schema([$component1, $component2]);

        $this->assertCount(2, $schema->getSchema());
    }

    public function test_can_get_visible_components(): void
    {
        $visible = $this->createMockComponent('visible', false);
        $hidden = $this->createMockComponent('hidden', true);

        $schema = Schema::make('test-schema')
            ->schema([$visible, $hidden]);

        $visibleComponents = $schema->getVisibleComponents();

        $this->assertCount(1, $visibleComponents);
    }

    public function test_serializes_to_laravilt_props(): void
    {
        $component = $this->createMockComponent('component-1');

        $schema = Schema::make('test-schema')
            ->schema([$component]);

        $props = $schema->toLaraviltProps();

        $this->assertIsArray($props);
        $this->assertArrayHasKey('schema', $props);
        $this->assertCount(1, $props['schema']);
    }

    protected function createMockComponent(string $name, bool $hidden = false): Component
    {
        $component = $this->getMockBuilder(Component::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isHidden', 'toLaraviltProps'])
            ->getMock();

        $component->method('isHidden')->willReturn($hidden);
        $component->method('toLaraviltProps')->willReturn(['name' => $name]);

        return $component;
    }
}
