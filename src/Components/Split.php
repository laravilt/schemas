<?php

namespace Laravilt\Schemas\Components;

use Laravilt\Support\Component;

class Split extends Component
{
    protected string $view = 'laravilt-schemas::components.split';

    protected array $startSchema = [];

    protected array $endSchema = [];

    protected string|int $startColumnSpan = 'md:col-span-6';

    protected string|int $endColumnSpan = 'md:col-span-6';

    protected string $fromBreakpoint = 'md';

    /**
     * Set the breakpoint from which to split.
     */
    public function fromBreakpoint(string $breakpoint): static
    {
        $this->fromBreakpoint = $breakpoint;

        return $this;
    }

    /**
     * Get from breakpoint.
     */
    public function getFromBreakpoint(): string
    {
        return $this->fromBreakpoint;
    }

    /**
     * Set the left schema (alias for startSchema).
     */
    public function leftSchema(array $components): static
    {
        return $this->startSchema($components);
    }

    /**
     * Set the right schema (alias for endSchema).
     */
    public function rightSchema(array $components): static
    {
        return $this->endSchema($components);
    }

    /**
     * Get left schema.
     */
    public function getLeftSchema(): array
    {
        return $this->getStartSchema();
    }

    /**
     * Get right schema.
     */
    public function getRightSchema(): array
    {
        return $this->getEndSchema();
    }

    /**
     * Set the start (left/right) schema.
     */
    public function startSchema(array $components): static
    {
        $this->startSchema = $components;

        return $this;
    }

    /**
     * Get start schema.
     */
    public function getStartSchema(): array
    {
        return $this->startSchema;
    }

    /**
     * Set the end (right/left) schema.
     */
    public function endSchema(array $components): static
    {
        $this->endSchema = $components;

        return $this;
    }

    /**
     * Get end schema.
     */
    public function getEndSchema(): array
    {
        return $this->endSchema;
    }

    /**
     * Set start column span.
     */
    public function startColumnSpan(string|int $span): static
    {
        $this->startColumnSpan = $span;

        return $this;
    }

    /**
     * Set end column span.
     */
    public function endColumnSpan(string|int $span): static
    {
        $this->endColumnSpan = $span;

        return $this;
    }

    /**
     * Serialize to Laravilt props.
     */
    public function toLaraviltProps(): array
    {
        return array_merge(parent::toLaraviltProps(), [
            'fromBreakpoint' => $this->getFromBreakpoint(),
            'leftSchema' => array_map(
                fn (Component $component): array => $component->toLaraviltProps(),
                $this->getLeftSchema()
            ),
            'rightSchema' => array_map(
                fn (Component $component): array => $component->toLaraviltProps(),
                $this->getRightSchema()
            ),
            'startSchema' => array_map(
                fn (Component $component): array => $component->toLaraviltProps(),
                $this->getStartSchema()
            ),
            'endSchema' => array_map(
                fn (Component $component): array => $component->toLaraviltProps(),
                $this->getEndSchema()
            ),
            'startColumnSpan' => $this->startColumnSpan,
            'endColumnSpan' => $this->endColumnSpan,
        ]);
    }
}
