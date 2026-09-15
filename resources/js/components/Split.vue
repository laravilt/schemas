<template>
    <div :class="gridClasses" :dir="rtl ? 'rtl' : 'ltr'">
        <div :class="startClasses">
            <Schema
                v-if="startSchema.length > 0"
                :schema="startSchema"
                :model-value="modelValue"
                :form-controller="formController"
                :form-method="formMethod"
                @update:model-value="$emit('update:modelValue', $event)"
            />
        </div>
        <div :class="endClasses">
            <Schema
                v-if="endSchema.length > 0"
                :schema="endSchema"
                :model-value="modelValue"
                :form-controller="formController"
                :form-method="formMethod"
                @update:model-value="$emit('update:modelValue', $event)"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, inject } from 'vue'
import Schema from './Schema.vue'
import { getSplitGridClasses, getSplitSpanClasses } from '../lib/layout'

// Inject parent context for reactive fields
const formController = inject<string | undefined>('formController', undefined)
const formMethod = inject<string | undefined>('formMethod', 'getSchema')

const props = withDefaults(defineProps<{
    startSchema?: Array<any>
    endSchema?: Array<any>
    startColumnSpan?: string | number
    endColumnSpan?: string | number
    fromBreakpoint?: string
    rtl?: boolean
    theme?: string
    modelValue?: Record<string, any>
}>(), {
    startSchema: () => [],
    endSchema: () => [],
    startColumnSpan: 'md:col-span-6',
    endColumnSpan: 'md:col-span-6',
    fromBreakpoint: 'md',
    rtl: false,
    theme: 'light',
})

defineEmits<{
    'update:modelValue': [value: Record<string, any>]
}>()

const gridClasses = computed(() => getSplitGridClasses(props.fromBreakpoint))
const startClasses = computed(() => getSplitSpanClasses(props.startColumnSpan, props.fromBreakpoint))
const endClasses = computed(() => getSplitSpanClasses(props.endColumnSpan, props.fromBreakpoint))
</script>
