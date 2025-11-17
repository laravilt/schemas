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
     * @param  array<Component>  $components
     *                                        Set the section heading.
     */
    public function heading(string|Closure $heading): static
    {
        $this->heading = $heading;

        return $this;
    }

    /**
     * @param  array<Component>  $components
     *                                        Get the heading.
     */
    public function getHeading(): ?string
    {
        return $this->evaluate($this->heading);
    }

    /**
     * @param  array<Component>  $components
     *                                        Set the description.
     */
    public function description(string|Closure $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param  array<Component>  $components
     *                                        Get the description.
     */
    public function getDescription(): ?string
    {
        return $this->evaluate($this->description);
    }

    /**
     * @param  array<Component>  $components
     *                                        Set the icon.
     */
    public function icon(string|Closure $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @param  array<Component>  $components
     *                                        Get the icon.
     */
    public function getIcon(): ?string
    {
        return $this->evaluate($this->icon);
    }

    /**
     * @param  array<Component>  $components
     *                                        Set the schema.
     */
    public function schema(array $components): static
    {
        $this->schema = $components;

        return $this;
    }

    /**
     * @param  array<Component>  $components
     *                                        Get the schema.
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
