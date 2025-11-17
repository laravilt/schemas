<?php

namespace Laravilt\Schemas\Tests\Unit;

use Laravilt\Schemas\Components\Section;
use Laravilt\Support\Component;
use Orchestra\Testbench\TestCase;

class SectionTest extends TestCase
{
    public function test_can_create_section(): void
    {
        $section = Section::make('test-section');

        $this->assertInstanceOf(Section::class, $section);
        $this->assertInstanceOf(Component::class, $section);
    }

    public function test_can_set_heading(): void
    {
        $section = Section::make('test-section')
            ->heading('Test Heading');

        $this->assertEquals('Test Heading', $section->getHeading());
    }

    public function test_can_set_description(): void
    {
        $section = Section::make('test-section')
            ->description('Test Description');

        $this->assertEquals('Test Description', $section->getDescription());
    }

    public function test_can_set_icon(): void
    {
        $icon = '<svg>...</svg>';

        $section = Section::make('test-section')
            ->icon($icon);

        $this->assertEquals($icon, $section->getIcon());
    }

    public function test_can_make_collapsible(): void
    {
        $section = Section::make('test-section')
            ->collapsible();

        $this->assertTrue($section->isCollapsible());
    }

    public function test_can_set_collapsed_state(): void
    {
        $section = Section::make('test-section')
            ->collapsible()
            ->collapsed(true);

        $this->assertTrue($section->isCollapsed());
    }

    public function test_cannot_be_collapsed_if_not_collapsible(): void
    {
        $section = Section::make('test-section')
            ->collapsed(true);

        $this->assertFalse($section->isCollapsed());
    }

    public function test_serializes_to_laravilt_props(): void
    {
        $component = $this->createMockComponent();

        $section = Section::make('test-section')
            ->heading('Test')
            ->description('Description')
            ->icon('<svg></svg>')
            ->collapsible()
            ->collapsed(true)
            ->schema([$component]);

        $props = $section->toLaraviltProps();

        $this->assertEquals('Test', $props['heading']);
        $this->assertEquals('Description', $props['description']);
        $this->assertEquals('<svg></svg>', $props['icon']);
        $this->assertTrue($props['collapsible']);
        $this->assertTrue($props['collapsed']);
        $this->assertCount(1, $props['schema']);
    }

    public function test_evaluates_closures(): void
    {
        $section = Section::make('test-section')
            ->heading(fn () => 'Dynamic Heading')
            ->description(fn () => 'Dynamic Description');

        $this->assertEquals('Dynamic Heading', $section->getHeading());
        $this->assertEquals('Dynamic Description', $section->getDescription());
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
