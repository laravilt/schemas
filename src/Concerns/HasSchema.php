<?php

namespace Laravilt\Schemas\Concerns;

trait HasSchema
{
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
        return array_filter($this->schema, fn ($component) => ! $component->isHidden());
    }
}
