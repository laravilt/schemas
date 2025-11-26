<?php

namespace Laravilt\Schemas\Components;

use Laravilt\Schemas\Concerns\HasSchema;
use Laravilt\Support\Component;

class Tabs extends Component
{
    use HasSchema;

    protected string $view = 'laravilt-schemas::components.tabs';

    protected array $tabs = [];

    protected int $activeTab = 0;

    protected bool $persistTabInQueryString = false;

    /**
     * Set tabs (array of Tab components).
     */
    public function tabs(array $tabs): static
    {
        $this->tabs = $tabs;

        return $this;
    }

    /**
     * Get tabs.
     */
    public function getTabs(): array
    {
        return $this->tabs;
    }

    /**
     * Set active tab index.
     */
    public function activeTab(int $index): static
    {
        $this->activeTab = $index;

        return $this;
    }

    /**
     * Persist active tab in query string.
     */
    public function persistTabInQueryString(bool $condition = true): static
    {
        $this->persistTabInQueryString = $condition;

        return $this;
    }

    /**
     * Serialize to Laravilt props.
     */
    public function toLaraviltProps(): array
    {
        return array_merge(parent::toLaraviltProps(), [
            'tabs' => array_map(
                fn ($tab) => $tab->toLaraviltProps(),
                $this->getTabs()
            ),
            'activeTab' => $this->activeTab,
            'persistTabInQueryString' => $this->persistTabInQueryString,
        ]);
    }
}
