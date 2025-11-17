<?php

namespace Laravilt\Schemas\Tests\Unit;

use Laravilt\Schemas\Components\Split;
use Laravilt\Support\Component;
use Orchestra\Testbench\TestCase;

class SplitTest extends TestCase
{
    public function test_can_create_split(): void
    {
        $split = Split::make('test-split');

        $this->assertInstanceOf(Split::class, $split);
        $this->assertInstanceOf(Component::class, $split);
    }

    public function test_can_set_from_breakpoint(): void
    {
        $split = Split::make('test-split')
            ->fromBreakpoint('lg');

        $this->assertEquals('lg', $split->getFromBreakpoint());
    }

    public function test_can_set_left_schema(): void
    {
        $component = $this->createMockComponent();

        $split = Split::make('test-split')
            ->leftSchema([$component]);

        $this->assertCount(1, $split->getLeftSchema());
    }

    public function test_can_set_right_schema(): void
    {
        $component = $this->createMockComponent();

        $split = Split::make('test-split')
            ->rightSchema([$component]);

        $this->assertCount(1, $split->getRightSchema());
    }

    public function test_serializes_to_laravilt_props(): void
    {
        $leftComponent = $this->createMockComponent();
        $rightComponent = $this->createMockComponent();

        $split = Split::make('test-split')
            ->fromBreakpoint('lg')
            ->leftSchema([$leftComponent])
            ->rightSchema([$rightComponent]);

        $props = $split->toLaraviltProps();

        $this->assertEquals('lg', $props['fromBreakpoint']);
        $this->assertArrayHasKey('leftSchema', $props);
        $this->assertArrayHasKey('rightSchema', $props);
        $this->assertCount(1, $props['leftSchema']);
        $this->assertCount(1, $props['rightSchema']);
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
