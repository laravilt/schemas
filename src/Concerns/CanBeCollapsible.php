<?php

namespace Laravilt\Schemas\Concerns;

trait CanBeCollapsible
{
    protected bool $collapsible = false;

    protected bool $collapsed = false;

    /**
     * Make the component collapsible.
     */
    public function collapsible(bool $condition = true): static
    {
        $this->collapsible = $condition;

        return $this;
    }

    /**
     * Check if the component is collapsible.
     */
    public function isCollapsible(): bool
    {
        return $this->collapsible;
    }

    /**
     * Set the component as collapsed by default.
     */
    public function collapsed(bool $condition = true): static
    {
        $this->collapsed = $condition;

        return $this;
    }

    /**
     * Check if the component is collapsed.
     */
    public function isCollapsed(): bool
    {
        return $this->collapsible && $this->collapsed;
    }
}
