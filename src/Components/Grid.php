<?php

namespace Laravilt\Schemas\Components;

use Laravilt\Schemas\Concerns\HasSchema;
use Laravilt\Support\Component;

class Grid extends Component
{
    use HasSchema;

    protected string $view = 'laravilt-schemas::components.grid';

    protected int|array $columns = 1;

    protected array $schema = [];

    /**
     * @param  array<Component>  $components
     *                                        Set number of columns.
     * @param  int|array  $columns  Number of columns or responsive array [default => 1, sm => 2, md => 3, lg => 4]
     */
    public function columns(int|array $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @param  array<Component>  $components
     *                                        Get columns configuration.
     */
    public function getColumns(): int|array
    {
        return $this->columns;
    }

    /**
     * @param  array<Component>  $components
     *                                        Set the grid schema.
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
            'columns' => $this->getColumns(),
            'schema' => array_map(
                fn (Component $component): array => $component->toLaraviltProps(),
                $this->getSchema()
            ),
        ]);
    }
}
