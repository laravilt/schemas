<?php

namespace Laravilt\Schemas\Components;

use Closure;
use Laravilt\Schemas\Concerns\HasSchema;
use Laravilt\Support\Component;

class Tab extends Component
{
    use HasSchema;

    protected string $view = 'laravilt-schemas::components.tab';

    protected array $schema = [];

    protected string|Closure|null $label = null;

    protected string|Closure|null $icon = null;

    protected string|Closure|null $badge = null;

    /**
     * Set tab label.
     */
    public function label(string|Closure $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label.
     */
    public function getLabel(): ?string
    {
        return $this->evaluate($this->label);
    }

    /**
     * Set icon.
     */
    public function icon(string|Closure $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon.
     */
    public function getIcon(): ?string
    {
        return $this->evaluate($this->icon);
    }

    /**
     * Set badge.
     */
    public function badge(string|Closure $badge): static
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * Get badge.
     */
    public function getBadge(): ?string
    {
        return $this->evaluate($this->badge);
    }

    /**
     * Set schema.
     *
     * @param  array<Component>  $components
     */
    public function schema(array $components): static
    {
        $this->schema = $components;

        return $this;
    }

    /**
     * Get schema.
     */
    public function getSchema(): array
    {
        return $this->schema;
    }

    /**
     * Serialize to Laravilt props.
     */
    public function toLaraviltProps(): array
    {
        return array_merge(parent::toLaraviltProps(), [
            'label' => $this->getLabel(),
            'icon' => $this->getIcon(),
            'badge' => $this->getBadge(),
            'schema' => array_map(
                fn (Component $component): array => $component->toLaraviltProps(),
                $this->getSchema()
            ),
        ]);
    }
}
