<?php

namespace Laravilt\Schemas\Tests\Unit;

use Laravilt\Schemas\Components\Step;
use Laravilt\Schemas\Components\Wizard;
use Laravilt\Support\Component;
use Orchestra\Testbench\TestCase;

class WizardTest extends TestCase
{
    public function test_can_create_wizard(): void
    {
        $wizard = Wizard::make('test-wizard');

        $this->assertInstanceOf(Wizard::class, $wizard);
        $this->assertInstanceOf(Component::class, $wizard);
    }

    public function test_can_set_steps(): void
    {
        $step1 = $this->createMockStep('step-1');
        $step2 = $this->createMockStep('step-2');

        $wizard = Wizard::make('test-wizard')
            ->steps([$step1, $step2]);

        $this->assertCount(2, $wizard->getSteps());
    }

    public function test_can_make_skippable(): void
    {
        $wizard = Wizard::make('test-wizard')
            ->skippable();

        $props = $wizard->toLaraviltProps();

        $this->assertTrue($props['skippable']);
    }

    public function test_can_customize_button_labels(): void
    {
        $wizard = Wizard::make('test-wizard')
            ->submitButtonLabel('Complete')
            ->nextButtonLabel('Continue')
            ->previousButtonLabel('Back');

        $this->assertEquals('Complete', $wizard->getSubmitButtonLabel());
        $this->assertEquals('Continue', $wizard->getNextButtonLabel());
        $this->assertEquals('Back', $wizard->getPreviousButtonLabel());
    }

    public function test_uses_default_translations_for_button_labels(): void
    {
        $wizard = Wizard::make('test-wizard');

        // These will use the translation keys
        $this->assertNotEmpty($wizard->getSubmitButtonLabel());
        $this->assertNotEmpty($wizard->getNextButtonLabel());
        $this->assertNotEmpty($wizard->getPreviousButtonLabel());
    }

    public function test_serializes_to_laravilt_props(): void
    {
        $step1 = $this->createMockStep('step-1');
        $step2 = $this->createMockStep('step-2');

        $wizard = Wizard::make('test-wizard')
            ->steps([$step1, $step2])
            ->skippable()
            ->submitButtonLabel('Finish');

        $props = $wizard->toLaraviltProps();

        $this->assertArrayHasKey('steps', $props);
        $this->assertCount(2, $props['steps']);
        $this->assertTrue($props['skippable']);
        $this->assertEquals('Finish', $props['submitButtonLabel']);
    }

    public function test_evaluates_closures_for_button_labels(): void
    {
        $wizard = Wizard::make('test-wizard')
            ->submitButtonLabel(fn () => 'Dynamic Submit')
            ->nextButtonLabel(fn () => 'Dynamic Next');

        $this->assertEquals('Dynamic Submit', $wizard->getSubmitButtonLabel());
        $this->assertEquals('Dynamic Next', $wizard->getNextButtonLabel());
    }

    protected function createMockStep(string $name): Step
    {
        $step = $this->getMockBuilder(Step::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['toLaraviltProps'])
            ->getMock();

        $step->method('toLaraviltProps')->willReturn(['name' => $name]);

        return $step;
    }
}
