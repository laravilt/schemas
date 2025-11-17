<?php

namespace Laravilt\Schemas\Components;

use Closure;
use Laravilt\Schemas\Concerns\HasSchema;
use Laravilt\Support\Component;

class Fieldset extends Component
{
    use HasSchema;

    protected string $view = 'laravilt-schemas::components.fieldset';

    protected array $schema = [];

    protected string|Closure|null $label = null;

    protected string|Closure|null $legend = null;

    /**
     * Set the fieldset label.
     */
    public function label(string|Closure $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Set the fieldset legend (alias for label).
     */
    public function legend(string|Closure $legend): static
    {
        $this->legend = $legend;
        $this->label = $legend;

        return $this;
    }

    /**
     * Get the label.
     */
    public function getLabel(): ?string
    {
        return $this->evaluate($this->label);
    }

    /**
     * Get the legend.
     */
    public function getLegend(): ?string
    {
        return $this->evaluate($this->legend ?? $this->label);
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
            'label' => $this->getLabel(),
            'legend' => $this->getLegend(),
            'schema' => array_map(
                fn (Component $component): array => $component->toLaraviltProps(),
                $this->getSchema()
            ),
        ]);
    }
}
