<x-laravilt-component name="wizard" :data="$component->toLaraviltProps()">
    <div class="wizard" :dir="rtl ? 'rtl' : 'ltr'">
        <!-- Step Indicators -->
        <div class="wizard-steps">
            <div
                v-for="(step, index) in steps"
                :key="index"
                :class="[
                    'wizard-step',
                    {
                        'active': currentStepIndex === index,
                        'completed': currentStepIndex > index
                    }
                ]"
            >
                <div class="wizard-step-indicator">
                    <span v-if="step.icon" v-html="step.icon"></span>
                    <span v-else v-text="index + 1"></span>
                </div>
                <div class="wizard-step-label">
                    <div class="wizard-step-title" v-text="step.label"></div>
                    <div v-if="step.description" class="wizard-step-description" v-text="step.description"></div>
                </div>
            </div>
        </div>

        <!-- Step Content -->
        <div class="wizard-content">
            <div
                v-for="(step, index) in steps"
                :key="index"
                v-show="currentStepIndex === index"
                class="wizard-step-content"
            >
                <component
                    v-for="(child, childIndex) in step.schema"
                    :key="childIndex"
                    :is="child.component || 'div'"
                    v-bind="child"
                />
            </div>
        </div>

        <!-- Navigation -->
        <div class="wizard-navigation">
            <button
                v-if="currentStepIndex > 0"
                type="button"
                @click="previousStep"
                class="wizard-button wizard-button-previous"
                v-text="previousButtonLabel"
            ></button>

            <button
                v-if="skippable && !isLastStep"
                type="button"
                @click="skipStep"
                class="wizard-button wizard-button-skip"
            >
                {{ $t('laravilt-schemas::schemas.wizard.skip') }}
            </button>

            <button
                v-if="!isLastStep"
                type="button"
                @click="nextStep"
                class="wizard-button wizard-button-next"
                v-text="nextButtonLabel"
            ></button>

            <button
                v-if="isLastStep"
                type="submit"
                class="wizard-button wizard-button-submit"
                v-text="submitButtonLabel"
            ></button>
        </div>
    </div>
</x-laravilt-component>

<script>
export default {
    props: {
        steps: Array,
        currentStep: Number,
        skippable: Boolean,
        submitButtonLabel: String,
        nextButtonLabel: String,
        previousButtonLabel: String,
        rtl: Boolean,
        theme: String
    },
    data() {
        return {
            currentStepIndex: this.currentStep
        }
    },
    computed: {
        isLastStep() {
            return this.currentStepIndex === this.steps.length - 1;
        }
    },
    methods: {
        nextStep() {
            if (this.currentStepIndex < this.steps.length - 1) {
                this.currentStepIndex++;
            }
        },
        previousStep() {
            if (this.currentStepIndex > 0) {
                this.currentStepIndex--;
            }
        },
        skipStep() {
            if (this.skippable && this.currentStepIndex < this.steps.length - 1) {
                this.currentStepIndex++;
            }
        }
    }
}
</script>
