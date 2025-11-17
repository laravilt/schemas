<?php

namespace Laravilt\Schemas\Tests\Unit;

use Laravilt\Schemas\Components\Step;
use Laravilt\Support\Component;
use Orchestra\Testbench\TestCase;

class StepTest extends TestCase
{
    public function test_can_create_step(): void
    {
        $step = Step::make('test-step');

        $this->assertInstanceOf(Step::class, $step);
        $this->assertInstanceOf(Component::class, $step);
    }

    public function test_can_set_label(): void
    {
        $step = Step::make('test-step')
            ->label('Account Setup');

        $this->assertEquals('Account Setup', $step->getLabel());
    }

    public function test_can_set_description(): void
    {
        $step = Step::make('test-step')
            ->description('Create your account');

        $this->assertEquals('Create your account', $step->getDescription());
    }

    public function test_can_set_icon(): void
    {
        $icon = '<svg>...</svg>';

        $step = Step::make('test-step')
            ->icon($icon);

        $this->assertEquals($icon, $step->getIcon());
    }

    public function test_can_set_schema(): void
    {
        $component = $this->createMockComponent();

        $step = Step::make('test-step')
            ->schema([$component]);

        $this->assertCount(1, $step->getSchema());
    }

    public function test_serializes_to_laravilt_props(): void
    {
        $component = $this->createMockComponent();

        $step = Step::make('test-step')
            ->label('Profile')
            ->description('Set up your profile')
            ->icon('<svg></svg>')
            ->schema([$component]);

        $props = $step->toLaraviltProps();

        $this->assertEquals('Profile', $props['label']);
        $this->assertEquals('Set up your profile', $props['description']);
        $this->assertEquals('<svg></svg>', $props['icon']);
        $this->assertCount(1, $props['schema']);
    }

    public function test_evaluates_closures(): void
    {
        $step = Step::make('test-step')
            ->label(fn () => 'Dynamic Label')
            ->description(fn () => 'Dynamic Description');

        $this->assertEquals('Dynamic Label', $step->getLabel());
        $this->assertEquals('Dynamic Description', $step->getDescription());
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
