<?php

namespace Laravilt\Schemas\Components;

use Laravilt\Schemas\Concerns\HasSchema;
use Laravilt\Support\Component;

class Columns extends Component
{
    use HasSchema;

    protected string $view = 'laravilt-schemas::components.columns';

    protected array $schema = [];

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
            'schema' => array_map(
                fn (Component $component): array => $component->toLaraviltProps(),
                $this->getSchema()
            ),
        ]);
    }
}
