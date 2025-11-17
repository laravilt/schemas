<?php

namespace Laravilt\Schemas\Tests\Feature;

use Laravilt\Schemas\Components\Columns;
use Laravilt\Schemas\Components\Fieldset;
use Laravilt\Schemas\Components\Grid;
use Laravilt\Schemas\Components\Section;
use Laravilt\Schemas\Components\Split;
use Laravilt\Schemas\Components\Step;
use Laravilt\Schemas\Components\Tab;
use Laravilt\Schemas\Components\Tabs;
use Laravilt\Schemas\Components\Wizard;
use Laravilt\Schemas\Schema;
use Orchestra\Testbench\TestCase;

class LayoutTest extends TestCase
{
    public function test_can_create_complex_nested_layout(): void
    {
        $schema = Schema::make('user-form')
            ->schema([
                Grid::make('main-grid')
                    ->columns(['default' => 1, 'lg' => 2])
                    ->schema([
                        Section::make('basic')
                            ->heading('Basic Info')
                            ->collapsible()
                            ->schema([]),

                        Tabs::make('details')
                            ->tabs([
                                Tab::make('profile')
                                    ->label('Profile')
                                    ->schema([]),
                            ]),
                    ]),
            ]);

        $props = $schema->toLaraviltProps();

        $this->assertIsArray($props);
        $this->assertArrayHasKey('schema', $props);
        $this->assertCount(1, $props['schema']);
    }

    public function test_grid_with_responsive_columns(): void
    {
        $grid = Grid::make('responsive-grid')
            ->columns([
                'default' => 1,
                'sm' => 2,
                'md' => 3,
                'lg' => 4,
            ]);

        $props = $grid->toLaraviltProps();

        $this->assertIsArray($props['columns']);
        $this->assertEquals(1, $props['columns']['default']);
        $this->assertEquals(4, $props['columns']['lg']);
    }

    public function test_section_with_full_configuration(): void
    {
        $section = Section::make('settings')
            ->heading('Settings')
            ->description('Configure your preferences')
            ->icon('<svg>icon</svg>')
            ->collapsible()
            ->collapsed(true)
            ->schema([]);

        $props = $section->toLaraviltProps();

        $this->assertEquals('Settings', $props['heading']);
        $this->assertEquals('Configure your preferences', $props['description']);
        $this->assertEquals('<svg>icon</svg>', $props['icon']);
        $this->assertTrue($props['collapsible']);
        $this->assertTrue($props['collapsed']);
    }

    public function test_tabs_with_multiple_tabs(): void
    {
        $tabs = Tabs::make('profile-tabs')
            ->activeTab(1)
            ->persistTabInQueryString()
            ->tabs([
                Tab::make('profile')->label('Profile')->schema([]),
                Tab::make('security')->label('Security')->badge('!')->schema([]),
                Tab::make('billing')->label('Billing')->schema([]),
            ]);

        $props = $tabs->toLaraviltProps();

        $this->assertCount(3, $props['tabs']);
        $this->assertEquals(1, $props['activeTab']);
        $this->assertTrue($props['persistTabInQueryString']);
    }

    public function test_wizard_with_multiple_steps(): void
    {
        $wizard = Wizard::make('onboarding')
            ->skippable()
            ->submitButtonLabel('Complete')
            ->nextButtonLabel('Continue')
            ->previousButtonLabel('Back')
            ->steps([
                Step::make('account')
                    ->label('Account')
                    ->description('Create account')
                    ->schema([]),
                Step::make('profile')
                    ->label('Profile')
                    ->schema([]),
            ]);

        $props = $wizard->toLaraviltProps();

        $this->assertCount(2, $props['steps']);
        $this->assertTrue($props['skippable']);
        $this->assertEquals('Complete', $props['submitButtonLabel']);
    }

    public function test_split_layout(): void
    {
        $split = Split::make('settings-split')
            ->fromBreakpoint('lg')
            ->leftSchema([])
            ->rightSchema([]);

        $props = $split->toLaraviltProps();

        $this->assertEquals('lg', $props['fromBreakpoint']);
        $this->assertIsArray($props['leftSchema']);
        $this->assertIsArray($props['rightSchema']);
    }

    public function test_fieldset_with_legend(): void
    {
        $fieldset = Fieldset::make('contact')
            ->legend('Contact Information')
            ->schema([]);

        $props = $fieldset->toLaraviltProps();

        $this->assertEquals('Contact Information', $props['legend']);
    }

    public function test_columns_wrapper(): void
    {
        $columns = Columns::make('featured')
            ->columnSpan('full')
            ->schema([]);

        $props = $columns->toLaraviltProps();

        $this->assertEquals('full', $props['columnSpan']);
        $this->assertIsArray($props['schema']);
    }

    public function test_all_components_include_rtl_and_theme(): void
    {
        $components = [
            Grid::make('grid'),
            Section::make('section'),
            Tabs::make('tabs'),
            Wizard::make('wizard'),
            Split::make('split'),
        ];

        foreach ($components as $component) {
            $props = $component->toLaraviltProps();

            $this->assertArrayHasKey('rtl', $props);
            $this->assertArrayHasKey('theme', $props);
            $this->assertArrayHasKey('locale', $props);
        }
    }
}
