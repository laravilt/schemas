<template>
    <div v-if="schema && schema.length > 0" :class="gridClasses">
        <div
            v-for="(child, index) in schema"
            :key="child.name || child.id || index"
            :class="getColumnSpanClasses(child.columnSpan)"
        >
            <Schema
                :schema="[child]"
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
import { getColumnSpanClasses, getGridClasses } from '../lib/layout'

// Inject parent context for reactive fields
const formController = inject<string | undefined>('formController', undefined)
const formMethod = inject<string | undefined>('formMethod', 'getSchema')

const props = defineProps<{
    columns: number | Record<string, number>
    schema: Array<any>
    modelValue?: Record<string, any>
}>()

defineEmits<{
    'update:modelValue': [value: Record<string, any>]
}>()

// Static class maps (see lib/layout.ts) so Tailwind keeps every class
const gridClasses = computed(() => getGridClasses(props.columns))
</script>
