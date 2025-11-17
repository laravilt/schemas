<?php

namespace Laravilt\Schemas\Components;

use Closure;
use Laravilt\Schemas\Concerns\HasSchema;
use Laravilt\Support\Component;

class Step extends Component
{
    use HasSchema;

    protected string $view = 'laravilt-schemas::components.step';

    protected array $schema = [];

    protected string|Closure|null $label = null;

    protected string|Closure|null $description = null;

    protected string|Closure|null $icon = null;

    /**
     * @param  array<Component>  $components
     *                                        Set step label.
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
     *                                        Set description.
     */
    public function description(string|Closure $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param  array<Component>  $components
     *                                        Get description.
     */
    public function getDescription(): ?string
    {
        return $this->evaluate($this->description);
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
            'description' => $this->getDescription(),
            'icon' => $this->getIcon(),
            'schema' => array_map(
                fn (Component $component): array => $component->toLaraviltProps(),
                $this->getSchema()
            ),
        ]);
    }
}
