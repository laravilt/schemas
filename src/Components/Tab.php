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
     * @param  array<Component>  $components
     *                                        Set tab label.
     */
    public function label(string|Closure $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @param  array<Component>  $components
     *                                        Get label.
     */
    public function getLabel(): ?string
    {
        return $this->evaluate($this->label);
    }

    /**
     * @param  array<Component>  $components
     *                                        Set icon.
     */
    public function icon(string|Closure $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @param  array<Component>  $components
     *                                        Get icon.
     */
    public function getIcon(): ?string
    {
        return $this->evaluate($this->icon);
    }

    /**
     * @param  array<Component>  $components
     *                                        Set badge.
     */
    public function badge(string|Closure $badge): static
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * @param  array<Component>  $components
     *                                        Get badge.
     */
    public function getBadge(): ?string
    {
        return $this->evaluate($this->badge);
    }

    /**
     * @param  array<Component>  $components
     *                                        Set schema.
     */
    public function schema(array $components): static
    {
        $this->schema = $components;

        return $this;
    }

    /**
     * @param  array<Component>  $components
     *                                        Get schema.
     */
    public function getSchema(): array
    {
        return $this->schema;
    }

    /**
     * @param  array<Component>  $components
     *                                        Serialize to Laravilt props.
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
