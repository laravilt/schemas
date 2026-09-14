import { cn } from '@/lib/utils';
import { resolveComponent } from '@laravilt/support/composables/registry';
import { useLocalization } from '@laravilt/support/composables/useLocalization';
import { useState } from 'react';

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
}

// Vue `<component :is="child.component || 'div'" v-bind="child" />`: resolve the registered component, else a plain div
const renderChild = (child: any, childIndex: number) => {
    const Component = resolveComponent(child.component);

    if (!Component) {
        return <div key={childIndex} />;
    }

    return <Component key={childIndex} {...child} />;
};

export default function Wizard({
    steps = [],
    currentStep = 0,
    skippable = false,
    submitButtonLabel = 'Submit',
    nextButtonLabel = 'Next',
    previousButtonLabel = 'Previous',
    skipButtonLabel = 'Skip',
    rtl = false,
}: WizardProps) {
    // Initialize localization
    const { trans } = useLocalization();

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
                {steps.map((step: any, index: number) => (
                    <div
                        key={index}
                        className={cn('wizard-step', {
                            active: currentStepIndex === index,
                            completed: currentStepIndex > index,
                        })}
                    >
                        <div className="wizard-step-indicator">
                            {step.icon ? <span dangerouslySetInnerHTML={{ __html: step.icon }}></span> : <span>{index + 1}</span>}
                        </div>
                        <div className="wizard-step-label">
                            <div className="wizard-step-title">{step.label}</div>
                            {step.description ? <div className="wizard-step-description">{step.description}</div> : null}
                        </div>
                    </div>
                ))}
            </div>

            {/* Step Content */}
            <div className="wizard-content">
                {steps.map((step: any, index: number) => (
                    <div
                        key={index}
                        className="wizard-step-content"
                        style={currentStepIndex === index ? undefined : { display: 'none' }}
                    >
                        {(step.schema || []).map(renderChild)}
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
