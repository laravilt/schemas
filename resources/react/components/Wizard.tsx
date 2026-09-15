import { cn } from '@/lib/utils';
import { useSchemaContext } from '@laravilt/support/composables/contexts';
import { useLocalization } from '@laravilt/support/composables/useLocalization';
import { resolveIcon } from '@laravilt/support/lib/icons';
import { useState } from 'react';
import Schema from './Schema';

export interface WizardProps {
    steps?: Array<any>;
    currentStep?: number;
    skippable?: boolean;
    submitButtonLabel?: string;
    nextButtonLabel?: string;
    previousButtonLabel?: string;
    skipButtonLabel?: string;
    rtl?: boolean;
    theme?: string;
    modelValue?: Record<string, any>;
    onUpdateModelValue?: (value: Record<string, any>) => void;
}

const EMPTY: any[] = [];

export default function Wizard({
    steps = EMPTY,
    currentStep = 0,
    skippable = false,
    submitButtonLabel = 'Submit',
    nextButtonLabel = 'Next',
    previousButtonLabel = 'Previous',
    skipButtonLabel = 'Skip',
    rtl = false,
    modelValue,
    onUpdateModelValue,
}: WizardProps) {
    // Initialize localization
    const { trans } = useLocalization();

    // Inject parent context for reactive fields
    const { formController, formMethod = 'getSchema' } = useSchemaContext();

    const [currentStepIndex, setCurrentStepIndex] = useState<number>(currentStep);

    // Translated labels
    const translatedSubmitButtonLabel = submitButtonLabel !== 'Submit' ? submitButtonLabel : trans('wizard.submit_button_label');
    const translatedNextButtonLabel = nextButtonLabel !== 'Next' ? nextButtonLabel : trans('wizard.next_button_label');
    const translatedPreviousButtonLabel =
        previousButtonLabel !== 'Previous' ? previousButtonLabel : trans('wizard.previous_button_label');
    const translatedSkipButtonLabel = skipButtonLabel !== 'Skip' ? skipButtonLabel : trans('wizard.skip_button_label');

    const isLastStep = currentStepIndex === steps.length - 1;

    const nextStep = () => {
        setCurrentStepIndex((index) => (index < steps.length - 1 ? index + 1 : index));
    };

    const previousStep = () => {
        setCurrentStepIndex((index) => (index > 0 ? index - 1 : index));
    };

    const skipStep = () => {
        setCurrentStepIndex((index) => (skippable && index < steps.length - 1 ? index + 1 : index));
    };

    return (
        <div className="wizard" dir={rtl ? 'rtl' : 'ltr'}>
            {/* Step Indicators */}
            <div className="wizard-steps">
                {steps.map((step: any, index: number) => {
                    // Icon names are resolved to components; step.icon is never inserted as HTML
                    const Icon = typeof step.icon === 'string' ? resolveIcon(step.icon) : null;

                    return (
                        <div
                            key={index}
                            className={cn('wizard-step', {
                                active: currentStepIndex === index,
                                completed: currentStepIndex > index,
                            })}
                        >
                            <div className="wizard-step-indicator">{Icon ? <Icon className="h-4 w-4" /> : <span>{index + 1}</span>}</div>
                            <div className="wizard-step-label">
                                <div className="wizard-step-title">{step.label}</div>
                                {step.description ? <div className="wizard-step-description">{step.description}</div> : null}
                            </div>
                        </div>
                    );
                })}
            </div>

            {/* Step Content */}
            <div className="wizard-content">
                {steps.map((step: any, index: number) => (
                    <div
                        key={index}
                        className="wizard-step-content"
                        style={currentStepIndex === index ? undefined : { display: 'none' }}
                    >
                        {Array.isArray(step.schema) && step.schema.length > 0 ? (
                            <Schema
                                schema={step.schema}
                                modelValue={modelValue}
                                formController={formController}
                                formMethod={formMethod}
                                onUpdateModelValue={(value) => onUpdateModelValue?.(value)}
                            />
                        ) : null}
                    </div>
                ))}
            </div>

            {/* Navigation */}
            <div className="wizard-navigation">
                {currentStepIndex > 0 ? (
                    <button type="button" onClick={previousStep} className="wizard-button wizard-button-previous">
                        {translatedPreviousButtonLabel}
                    </button>
                ) : null}

                {skippable && !isLastStep ? (
                    <button type="button" onClick={skipStep} className="wizard-button wizard-button-skip">
                        {translatedSkipButtonLabel}
                    </button>
                ) : null}

                {!isLastStep ? (
                    <button type="button" onClick={nextStep} className="wizard-button wizard-button-next">
                        {translatedNextButtonLabel}
                    </button>
                ) : null}

                {isLastStep ? (
                    <button type="submit" className="wizard-button wizard-button-submit">
                        {translatedSubmitButtonLabel}
                    </button>
                ) : null}
            </div>
        </div>
    );
}
