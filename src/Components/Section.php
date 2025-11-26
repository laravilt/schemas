<?php

namespace Laravilt\Schemas\Components;

use Closure;
use Laravilt\Schemas\Concerns\CanBeCollapsible;
use Laravilt\Schemas\Concerns\HasSchema;
use Laravilt\Support\Component;

class Section extends Component
{
    use CanBeCollapsible;
    use HasSchema;

    protected string $view = 'laravilt-schemas::components.section';

    protected array $schema = [];

    protected string|Closure|null $heading = null;

    protected string|Closure|null $description = null;

    protected string|Closure|null $icon = null;

    /**
     * Set the section heading.
     */
    public function heading(string|Closure $heading): static
    {
        $this->heading = $heading;

        return $this;
    }

    /**
     * Get the heading.
     */
    public function getHeading(): ?string
    {
        return $this->evaluate($this->heading);
    }

    /**
     * Set the description.
     */
    public function description(string|Closure $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the description.
     */
    public function getDescription(): ?string
    {
        return $this->evaluate($this->description);
    }

    /**
     * Set the icon.
     */
    public function icon(string|Closure $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get the icon.
     */
    public function getIcon(): ?string
    {
        return $this->evaluate($this->icon);
    }

    /**
     * Set the schema.
     *
     * @param  array<Component>  $components
     */
    public function schema(array $components): static
    {
        $this->schema = $components;

        return $this;
    }

    /**
     * Get the schema.
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
            'heading' => $this->getHeading(),
            'description' => $this->getDescription(),
            'icon' => $this->getIcon(),
            'collapsible' => $this->isCollapsible(),
            'collapsed' => $this->isCollapsed(),
            'schema' => array_map(
                fn (Component $component): array => $component->toLaraviltProps(),
                $this->getSchema()
            ),
        ]);
    }
}
