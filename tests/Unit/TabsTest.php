<?php

namespace Laravilt\Schemas\Tests\Unit;

use Laravilt\Schemas\Components\Tab;
use Laravilt\Schemas\Components\Tabs;
use Laravilt\Support\Component;
use Orchestra\Testbench\TestCase;

class TabsTest extends TestCase
{
    public function test_can_create_tabs(): void
    {
        $tabs = Tabs::make('test-tabs');

        $this->assertInstanceOf(Tabs::class, $tabs);
        $this->assertInstanceOf(Component::class, $tabs);
    }

    public function test_can_set_tabs(): void
    {
        $tab1 = $this->createMockTab('tab-1');
        $tab2 = $this->createMockTab('tab-2');

        $tabs = Tabs::make('test-tabs')
            ->tabs([$tab1, $tab2]);

        $this->assertCount(2, $tabs->getTabs());
    }

    public function test_can_set_active_tab(): void
    {
        $tabs = Tabs::make('test-tabs')
            ->activeTab(1);

        $props = $tabs->toLaraviltProps();

        $this->assertEquals(1, $props['activeTab']);
    }

    public function test_can_persist_tab_in_query_string(): void
    {
        $tabs = Tabs::make('test-tabs')
            ->persistTabInQueryString();

        $props = $tabs->toLaraviltProps();

        $this->assertTrue($props['persistTabInQueryString']);
    }

    public function test_serializes_to_laravilt_props(): void
    {
        $tab1 = $this->createMockTab('tab-1');
        $tab2 = $this->createMockTab('tab-2');

        $tabs = Tabs::make('test-tabs')
            ->tabs([$tab1, $tab2])
            ->activeTab(0)
            ->persistTabInQueryString();

        $props = $tabs->toLaraviltProps();

        $this->assertArrayHasKey('tabs', $props);
        $this->assertCount(2, $props['tabs']);
        $this->assertEquals(0, $props['activeTab']);
        $this->assertTrue($props['persistTabInQueryString']);
    }

    protected function createMockTab(string $name): Tab
    {
        $tab = $this->getMockBuilder(Tab::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['toLaraviltProps'])
            ->getMock();

        $tab->method('toLaraviltProps')->willReturn(['name' => $name]);

        return $tab;
    }
}
