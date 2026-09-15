<template>
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
                    <!-- Icon names are resolved to components; step.icon is never inserted as HTML -->
                    <component v-if="getIconComponent(step.icon)" :is="getIconComponent(step.icon)" class="h-4 w-4" />
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
                <Schema
                    v-if="Array.isArray(step.schema) && step.schema.length > 0"
                    :schema="step.schema"
                    :model-value="modelValue"
                    :form-controller="formController"
                    :form-method="formMethod"
                    @update:model-value="(value) => emit('update:modelValue', value)"
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
                v-text="translatedPreviousButtonLabel"
            ></button>

            <button
                v-if="skippable && !isLastStep"
                type="button"
                @click="skipStep"
                class="wizard-button wizard-button-skip"
                v-text="translatedSkipButtonLabel"
            ></button>

            <button
                v-if="!isLastStep"
                type="button"
                @click="nextStep"
                class="wizard-button wizard-button-next"
                v-text="translatedNextButtonLabel"
            ></button>

            <button
                v-if="isLastStep"
                type="submit"
                class="wizard-button wizard-button-submit"
                v-text="translatedSubmitButtonLabel"
            ></button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, inject } from 'vue'
import * as LucideIcons from 'lucide-vue-next'
import { useLocalization } from '@laravilt/support/composables'
import Schema from './Schema.vue'

// Initialize localization
const { trans } = useLocalization()

// Inject parent context for reactive fields
const formController = inject<string | undefined>('formController', undefined)
const formMethod = inject<string | undefined>('formMethod', 'getSchema')

const props = withDefaults(defineProps<{
    steps?: Array<any>
    currentStep?: number
    skippable?: boolean
    submitButtonLabel?: string
    nextButtonLabel?: string
    previousButtonLabel?: string
    skipButtonLabel?: string
    rtl?: boolean
    theme?: string
    modelValue?: Record<string, any>
}>(), {
    steps: () => [],
    currentStep: 0,
    skippable: false,
    submitButtonLabel: 'Submit',
    nextButtonLabel: 'Next',
    previousButtonLabel: 'Previous',
    skipButtonLabel: 'Skip',
    rtl: false,
    theme: 'light',
})

const emit = defineEmits<{
    'update:modelValue': [value: Record<string, any>]
}>()

const currentStepIndex = ref(props.currentStep)

// Computed translated labels
const translatedSubmitButtonLabel = computed(() => props.submitButtonLabel !== 'Submit' ? props.submitButtonLabel : trans('wizard.submit_button_label'))
const translatedNextButtonLabel = computed(() => props.nextButtonLabel !== 'Next' ? props.nextButtonLabel : trans('wizard.next_button_label'))
const translatedPreviousButtonLabel = computed(() => props.previousButtonLabel !== 'Previous' ? props.previousButtonLabel : trans('wizard.previous_button_label'))
const translatedSkipButtonLabel = computed(() => props.skipButtonLabel !== 'Skip' ? props.skipButtonLabel : trans('wizard.skip_button_label'))

const isLastStep = computed(() => {
    return currentStepIndex.value === props.steps.length - 1
})

const nextStep = () => {
    if (currentStepIndex.value < props.steps.length - 1) {
        currentStepIndex.value++
    }
}

const previousStep = () => {
    if (currentStepIndex.value > 0) {
        currentStepIndex.value--
    }
}

const skipStep = () => {
    if (props.skippable && currentStepIndex.value < props.steps.length - 1) {
        currentStepIndex.value++
    }
}

// Resolve an icon name ('user', 'file-text', 'heroicon-o-home', ...) to a lucide component
const getIconComponent = (iconName: unknown) => {
    if (!iconName || typeof iconName !== 'string') return null

    const pascalCase = iconName
        .trim()
        .replace(/^heroicon-[osm]-/, '')
        .replace(/^lucide[-:]/i, '')
        .split(/[-_\s.]+/)
        .filter(Boolean)
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join('')

    return (LucideIcons as any)[pascalCase] || null
}
</script>
