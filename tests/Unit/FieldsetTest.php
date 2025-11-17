<?php

namespace Laravilt\Schemas\Tests\Unit;

use Laravilt\Schemas\Components\Fieldset;
use Laravilt\Support\Component;
use Orchestra\Testbench\TestCase;

class FieldsetTest extends TestCase
{
    public function test_can_create_fieldset(): void
    {
        $fieldset = Fieldset::make('test-fieldset');

        $this->assertInstanceOf(Fieldset::class, $fieldset);
        $this->assertInstanceOf(Component::class, $fieldset);
    }

    public function test_can_set_legend(): void
    {
        $fieldset = Fieldset::make('test-fieldset')
            ->legend('Personal Information');

        $this->assertEquals('Personal Information', $fieldset->getLegend());
    }

    public function test_can_set_schema(): void
    {
        $component = $this->createMockComponent();

        $fieldset = Fieldset::make('test-fieldset')
            ->schema([$component]);

        $this->assertCount(1, $fieldset->getSchema());
    }

    public function test_serializes_to_laravilt_props(): void
    {
        $component = $this->createMockComponent();

        $fieldset = Fieldset::make('test-fieldset')
            ->legend('Contact Details')
            ->schema([$component]);

        $props = $fieldset->toLaraviltProps();

        $this->assertEquals('Contact Details', $props['legend']);
        $this->assertArrayHasKey('schema', $props);
        $this->assertCount(1, $props['schema']);
    }

    public function test_evaluates_closures(): void
    {
        $fieldset = Fieldset::make('test-fieldset')
            ->legend(fn () => 'Dynamic Legend');

        $this->assertEquals('Dynamic Legend', $fieldset->getLegend());
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
