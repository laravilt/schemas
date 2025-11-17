<?php

namespace Laravilt\Schemas\Components;

use Closure;
use Laravilt\Support\Component;

class Wizard extends Component
{
    protected string $view = 'laravilt-schemas::components.wizard';

    protected array $steps = [];

    protected int $currentStep = 0;

    protected bool $skippable = false;

    protected string|Closure|null $submitButtonLabel = null;

    protected string|Closure|null $nextButtonLabel = null;

    protected string|Closure|null $previousButtonLabel = null;

    /**
     * Set wizard steps.
     */
    public function steps(array $steps): static
    {
        $this->steps = $steps;

        return $this;
    }

    /**
     * Get steps.
     */
    public function getSteps(): array
    {
        return $this->steps;
    }

    /**
     * Allow skipping steps.
     */
    public function skippable(bool $condition = true): static
    {
        $this->skippable = $condition;

        return $this;
    }

    /**
     * Set submit button label.
     */
    public function submitButtonLabel(string|Closure $label): static
    {
        $this->submitButtonLabel = $label;

        return $this;
    }

    /**
     * Get submit button label.
     */
    public function getSubmitButtonLabel(): string
    {
        return $this->evaluate($this->submitButtonLabel) ?? __('laravilt-schemas::schemas.wizard.submit');
    }

    /**
     * Set next button label.
     */
    public function nextButtonLabel(string|Closure $label): static
    {
        $this->nextButtonLabel = $label;

        return $this;
    }

    /**
     * Get next button label.
     */
    public function getNextButtonLabel(): string
    {
        return $this->evaluate($this->nextButtonLabel) ?? __('laravilt-schemas::schemas.wizard.next');
    }

    /**
     * Set previous button label.
     */
    public function previousButtonLabel(string|Closure $label): static
    {
        $this->previousButtonLabel = $label;

        return $this;
    }

    /**
     * Get previous button label.
     */
    public function getPreviousButtonLabel(): string
    {
        return $this->evaluate($this->previousButtonLabel) ?? __('laravilt-schemas::schemas.wizard.previous');
    }

    /**
     * Serialize to Laravilt props.
     */
    public function toLaraviltProps(): array
    {
        return array_merge(parent::toLaraviltProps(), [
            'steps' => array_map(
                fn ($step) => $step->toLaraviltProps(),
                $this->getSteps()
            ),
            'currentStep' => $this->currentStep,
            'skippable' => $this->skippable,
            'submitButtonLabel' => $this->getSubmitButtonLabel(),
            'nextButtonLabel' => $this->getNextButtonLabel(),
            'previousButtonLabel' => $this->getPreviousButtonLabel(),
        ]);
    }
}
