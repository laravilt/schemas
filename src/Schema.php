<?php

namespace Laravilt\Schemas;

use Laravilt\Schemas\Concerns\HasSchema;
use Laravilt\Support\Component;

class Schema extends Component
{
    use HasSchema;

    protected string $view = 'laravilt-schemas::schema';

    protected array $schema = [];

    /**
     * Set the schema components.
     */
    public function schema(array $components): static
    {
        $this->schema = $components;

        return $this;
    }

    /**
     * Get the schema components.
     */
    public function getSchema(): array
    {
        return $this->schema;
    }

    /**
     * Get all visible components in the schema.
     */
    public function getVisibleComponents(): array
    {
        return array_filter($this->schema, fn (Component $component): bool => ! $component->isHidden());
    }

    /**
     * Serialize to Laravilt props.
     */
    public function toLaraviltProps(): array
    {
        return array_merge(parent::toLaraviltProps(), [
            'schema' => array_map(
                fn (Component $component): array => $component->toLaraviltProps(),
                $this->getVisibleComponents()
            ),
        ]);
    }
}
