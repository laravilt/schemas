<template>
    <div ref="formRef" :class="containerClass">
        <template v-for="(component, index) in nonActionComponents" :key="component.name || component.id || index">
            <!-- Render regular form components -->
            <component
                :is="getComponent(component)"
                v-bind="getComponentProps(component)"
                :value="isSchemaComponent(component) ? undefined : (isEntryComponent(component) ? component.state : internalFormData[component.name])"
                :state="isSchemaComponent(component) ? undefined : (isEntryComponent(component) ? component.state : internalFormData[component.name])"
                :model-value="isSchemaComponent(component) ? internalFormData : internalFormData[component.name]"
                @update:model-value="(value) => handleComponentUpdate(component, value)"
            />
        </template>

        <!-- Render action buttons grouped together with proper gap (only if not handled by parent) -->
        <div v-if="actionComponents.length > 0 && !parentHandlesActions" class="flex items-center gap-4">
            <FormActionButton
                v-for="(action, index) in actionComponents"
                :key="action.name || index"
                v-bind="action"
                :getFormData="getFormData"
            />
        </div>
    </div>
</template>

<script lang="ts">
// Module-level (shared by all instances): gives a Schema with no enclosing scope a unique 'laravilt:form-scope' id
let formScopeCounter = 0
</script>

<script setup lang="ts">
import { defineAsyncComponent, getCurrentInstance, onMounted, onUnmounted, computed, h, ref, watch, provide, inject, nextTick } from 'vue'
import ActionButton from '@laravilt/actions/components/ActionButton.vue'
import { getChildSchemas, isEntryComponent, isSchemaComponent } from '../lib/layout'

// Set by the outermost Schema. Nested Schemas (rendered by Section, Grid, Tabs, Split, Wizard) find it and
// delegate reactive-field requests and schema updates to it: the server always returns the ROOT schema.
const ROOT_SCHEMA_KEY = 'laraviltRootSchemaUpdate'

// Form scope id key (plain string, shared with Form and ActionButton without imports)
const FORM_SCOPE_KEY = 'laravilt:form-scope'

const formRef = ref<HTMLFormElement | null>(null)
const internalFormData = ref<Record<string, any>>({})

// Create a wrapper component for form actions that can collect form data
const FormActionButton = {
    props: {
        getFormData: Function,
    },
    setup(props: any, { attrs }: any) {
        return () => h(ActionButton, {
            ...attrs,
            getFormData: props.getFormData,
        })
    },
}

const props = withDefaults(defineProps<{
    schema: Array<any>
    modelValue?: Record<string, any>
    schemaId?: string
    parentHandlesActions?: boolean
    formController?: string
    formMethod?: string
}>(), {
    parentHandlesActions: false,
    formController: undefined,
    formMethod: 'getSchema',
})

const emit = defineEmits<{
    'update:modelValue': [value: Record<string, any>]
    'update:schema': [value: any[]]
}>()

// Make schema internally reactive so it can be updated by reactive fields
const internalSchema = ref(props.schema)

// Watch for prop schema changes (from page navigation, etc.)
watch(() => props.schema, (newSchema) => {
    // Invalidate in-flight reactive requests: their response is for the replaced schema
    reactiveRequestId++
    internalSchema.value = newSchema
})

// Recursively extract all field components and their default values from schema
const extractFieldDefaults = (schema: Array<any>): Record<string, any> => {
    const defaults: Record<string, any> = {}

    // Safety check - ensure schema is an array
    if (!schema || !Array.isArray(schema)) {
        console.warn('extractFieldDefaults: schema is not an array', schema)
        return defaults
    }

    for (const component of schema) {
        // Skip actions
        if (component.hasAction === true || (component.name && !component.component)) {
            continue
        }

        // Skip entry components (read-only displays, not form fields)
        if (isEntryComponent(component)) {
            continue
        }

        // Layout components (tabs, section, grid, split, wizard): recurse into every nested schema
        if (isSchemaComponent(component)) {
            for (const childSchema of getChildSchemas(component)) {
                Object.assign(defaults, extractFieldDefaults(childSchema))
            }
            continue // Don't add the layout component itself as a field
        }

        // If it has a name and component type (it's an actual field), add its default value
        if (component.name && component.component) {
            // Use defaultValue or default, but NOT value (value could be an object from backend)
            // For hidden fields, also check the 'default' property
            // Use ?? (nullish coalescing) to properly handle false/0 values
            defaults[component.name] = component.defaultValue ?? component.default ?? component.value ?? null
        }
    }

    return defaults
}

// Initialize form data with defaults from schema
const initializeFormData = () => {
    const defaults = extractFieldDefaults(internalSchema.value)

    // Merge with any provided modelValue
    internalFormData.value = {
        ...defaults,
        ...(props.modelValue || {})
    }

    emit('update:modelValue', internalFormData.value)
}

// Handle action-updated data events
const handleActionUpdatedData = (event: CustomEvent) => {
    // Ignore data from an action that belongs to another form (unscoped events still apply)
    const eventScope = (event as any).laraviltFormScope;
    if (eventScope && eventScope !== formScope) {
        return;
    }

    const updatedData = event.detail;

    if (updatedData && typeof updatedData === 'object') {
        // Merge the updated data into internal form data
        internalFormData.value = {
            ...internalFormData.value,
            ...updatedData
        };
        emit('update:modelValue', internalFormData.value);
    }
};

// Initialize on mount
onMounted(() => {
    initializeFormData();

    // Listen for action-updated-data events from ActionButton
    window.addEventListener('action-updated-data', handleActionUpdatedData as EventListener);
});

// Cleanup on unmount
onUnmounted(() => {
    window.removeEventListener('action-updated-data', handleActionUpdatedData as EventListener);

    // Drop any reactive response that arrives after unmount
    reactiveRequestId++;
});

// Watch for internal schema changes (from reactive fields)
// Don't re-initialize form data as it will reset user input
// The schema update only changes field options, not the data itself
watch(internalSchema, () => {
    // Intentionally empty - we don't need to do anything when schema changes
    // The reactive update already handles option changes
}, { deep: true })

// Watch for external modelValue changes and merge them
watch(() => props.modelValue, (newValue) => {
    if (newValue) {
        internalFormData.value = {
            ...internalFormData.value,
            ...newValue
        }
    }
}, { deep: true })

// Handle component update events
const handleComponentUpdate = async (component: any, value: any) => {
    if (isSchemaComponent(component)) {
        // For schema components (Section, Grid, Tabs), value is an object with field updates
        // Update internal form data first
        const oldData = { ...internalFormData.value }

        // Check if any values actually changed before proceeding
        let hasChanges = false
        for (const [fieldName, fieldValue] of Object.entries(value)) {
            if (oldData[fieldName] !== fieldValue) {
                hasChanges = true
                break
            }
        }

        // Skip if nothing changed (performance optimization)
        if (!hasChanges) {
            return
        }

        internalFormData.value = { ...internalFormData.value, ...value }
        emit('update:modelValue', internalFormData.value)

        // Check each field that changed for reactivity
        for (const [fieldName, fieldValue] of Object.entries(value)) {
            // Only trigger if value actually changed
            if (oldData[fieldName] !== fieldValue) {
                // Find the field in schema and check if it's reactive
                const field = findFieldInSchema(internalSchema.value, fieldName)
                // Repeaters handle their own reactivity (same rule as updateValue)
                if (field && field.component !== 'repeater' && (field.isLive || field.isLazy)) {
                    await triggerReactiveFieldUpdate(fieldName, field)
                }
            }
        }
    } else {
        await updateValue(component.name, value)
    }
}

const updateValue = async (name: string, value: any) => {
    // Update internal form data
    internalFormData.value = {
        ...internalFormData.value,
        [name]: value
    }

    emit('update:modelValue', internalFormData.value)

    // Check if this field is reactive (live/lazy)
    const field = findFieldInSchema(internalSchema.value, name)

    // Skip reactive updates for Repeater fields - they handle their own internal reactivity
    // Triggering a schema update here would cause the Repeater to remount and lose state
    if (field && field.component === 'repeater') {
        return
    }

    if (field && (field.isLive || field.isLazy)) {
        await triggerReactiveFieldUpdate(name, field)
    }
}

// Find a field by name in the schema (recursively)
const findFieldInSchema = (schema: any[], fieldName: string): any => {
    for (const component of schema) {
        if (component.name === fieldName) {
            return component
        }

        // Check nested schemas (schema, tabs[].schema, steps[].schema, split start/end)
        for (const childSchema of getChildSchemas(component)) {
            const found = findFieldInSchema(childSchema, fieldName)
            if (found) return found
        }
    }
    return null
}

// Root Schema detection (see ROOT_SCHEMA_KEY)
const rootUpdateSchema = inject<((schema: any[]) => void) | null>(ROOT_SCHEMA_KEY, null)
const isRootSchema = rootUpdateSchema === null

// Scope for action-updated-data events (see ActionButton): use the nearest scope an ancestor
// (Form or outer Schema) provides, so actions inside Form > Schema still update the Form.
// Only a Schema with no enclosing scope creates its own.
const formScope: string = inject<string | null>(FORM_SCOPE_KEY, null) ?? `laravilt-schema-${++formScopeCounter}`

// Only the latest reactive-field response may be applied; older ones that arrive late are dropped
let reactiveRequestId = 0

// Trigger reactive field update
const triggerReactiveFieldUpdate = async (fieldName: string, field: any) => {
    // Nested Schemas emit their data up; the root Schema sees the change and sends the request itself
    if (!isRootSchema) {
        return
    }

    // Skip if no form controller is configured
    if (!props.formController) {
        console.warn('[Schema] No formController configured, skipping reactive field update')
        return
    }

    const debounceMs = field.isLazy
        ? (field.liveDebounce || 500)
        : (field.isLive && field.liveDebounce ? field.liveDebounce : 0)

    // TODO: Implement debouncing if needed
    const requestId = ++reactiveRequestId

    try {
        const payload = {
            controller: props.formController,
            method: props.formMethod || 'getSchema',
            data: internalFormData.value,
            changed_field: fieldName,
        }

        const response = await fetch('/reactive-fields/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify(payload),
        })

        if (!response.ok) {
            throw new Error('Failed to update reactive fields')
        }

        const result = await response.json()

        // A newer request was sent meanwhile: this response is stale
        if (requestId !== reactiveRequestId) {
            return
        }

        if (result.schema) {
            updateSchema(result.schema)
        }

        // Update form data if backend modified it (from afterStateUpdated)
        if (result.data) {
            internalFormData.value = { ...internalFormData.value, ...result.data }
            emit('update:modelValue', internalFormData.value)
        }
    } catch (error) {
        console.error('[FormRenderer] Error updating reactive fields:', error)
    }
}

// Collect all form data - just return the internal tracked data
const getFormData = () => {
    // Return a copy to avoid mutations
    return { ...internalFormData.value }
}

// Validate the form using HTML5 validation
const validateForm = () => {
    if (!formRef.value) {
        return true
    }

    // Find the closest form element (could be parent or self if we're inside a form)
    const formElement = formRef.value.closest('form') as HTMLFormElement | null

    if (formElement) {
        const isValid = formElement.checkValidity()

        if (!isValid) {
            // Trigger validation UI (show error messages)
            formElement.reportValidity()
        }

        return isValid
    }

    // If no parent form, validate all inputs within this container
    const inputs = formRef.value.querySelectorAll('input, select, textarea') as NodeListOf<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>
    let isValid = true

    for (const input of inputs) {
        if (!input.checkValidity()) {
            input.reportValidity()
            isValid = false
            break
        }
    }

    return isValid
}

// Function to update schema (for reactive fields)
const updateSchema = (newSchema: any[]) => {
    // Schemas from the server describe the whole form: nested Schemas hand them to the root
    if (rootUpdateSchema) {
        rootUpdateSchema(newSchema)
        return
    }

    // Save current scroll position and focused element
    const scrollX = window.scrollX
    const scrollY = window.scrollY
    const activeElement = document.activeElement as HTMLElement

    // Simply update the schema - Vue's reactivity will handle the updates
    // Since we're using ref(), Vue will detect the change
    internalSchema.value = newSchema
    emit('update:schema', newSchema)

    // Restore scroll position and focus immediately in next tick
    nextTick(() => {
        // Restore scroll position
        window.scrollTo({
            top: scrollY,
            left: scrollX,
            behavior: 'instant' as ScrollBehavior
        })

        // Restore focus if element still exists
        if (activeElement && document.contains(activeElement)) {
            activeElement.focus({ preventScroll: true })
        }
    })
}

// Provide getFormData, validateForm, updateSchema, and schemaId to all child components
provide('getFormData', getFormData)
provide('validateForm', validateForm)
provide('updateSchema', updateSchema)
provide(ROOT_SCHEMA_KEY, rootUpdateSchema ?? updateSchema)
provide(FORM_SCOPE_KEY, formScope)
provide('schemaId', props.schemaId || null)
provide('formController', props.formController)
provide('formMethod', props.formMethod)

// Check if an item is an action
const isAction = (item: any) => {
    // Actions have hasAction property or don't have a component property
    return item.hasAction === true || (item.name && !item.component)
}

// Separate action components from regular components
const actionComponents = computed(() => {
    if (!internalSchema.value || !Array.isArray(internalSchema.value)) return []
    return internalSchema.value.filter(isAction)
})

const nonActionComponents = computed(() => {
    if (!internalSchema.value || !Array.isArray(internalSchema.value)) return []
    return internalSchema.value.filter((item: any) => !isAction(item))
})

// Determine container spacing based on what we're rendering
const containerClass = computed(() => {
    if (!internalSchema.value || !Array.isArray(internalSchema.value) || internalSchema.value.length === 0) return ''

    // Check if we're rendering sections - if so, use space-y-8 (reduced from space-y-12)
    const hasSections = internalSchema.value.some(c => c.component === 'section')
    if (hasSections) return 'space-y-8'

    // Otherwise use space-y-6 for general spacing
    return 'space-y-6'
})


// Map component types to their Vue components
const componentMap: Record<string, any> = {
    // Schema layout components
    tabs: defineAsyncComponent(() => import('./Tabs.vue')),
    section: defineAsyncComponent(() => import('./Section.vue')),
    grid: defineAsyncComponent(() => import('./Grid.vue')),
    split: defineAsyncComponent(() => import('./Split.vue')),
    wizard: defineAsyncComponent(() => import('./Wizard.vue')),

    // Form field components
    text_input: defineAsyncComponent(() => import('@laravilt/forms/components/fields/TextInput.vue')),
    textarea: defineAsyncComponent(() => import('@laravilt/forms/components/fields/Textarea.vue')),
    select: defineAsyncComponent(() => import('@laravilt/forms/components/fields/Select.vue')),
    checkbox: defineAsyncComponent(() => import('@laravilt/forms/components/fields/Checkbox.vue')),
    radio: defineAsyncComponent(() => import('@laravilt/forms/components/fields/Radio.vue')),
    toggle: defineAsyncComponent(() => import('@laravilt/forms/components/fields/Toggle.vue')),
    toggle_buttons: defineAsyncComponent(() => import('@laravilt/forms/components/fields/ToggleButtons.vue')),
    hidden: defineAsyncComponent(() => import('@laravilt/forms/components/fields/Hidden.vue')),
    date_picker: defineAsyncComponent(() => import('@laravilt/forms/components/fields/DatePicker.vue')),
    time_picker: defineAsyncComponent(() => import('@laravilt/forms/components/fields/TimePicker.vue')),
    date_time_picker: defineAsyncComponent(() => import('@laravilt/forms/components/fields/DateTimePicker.vue')),
    date_range_picker: defineAsyncComponent(() => import('@laravilt/forms/components/fields/DateRangePicker.vue')),
    color_picker: defineAsyncComponent(() => import('@laravilt/forms/components/fields/ColorPicker.vue')),
    file_upload: defineAsyncComponent(() => import('@laravilt/forms/components/fields/FileUpload.vue')),
    rich_editor: defineAsyncComponent(() => import('@laravilt/forms/components/fields/RichEditor.vue')),
    markdown_editor: defineAsyncComponent(() => import('@laravilt/forms/components/fields/MarkdownEditor.vue')),
    tags_input: defineAsyncComponent(() => import('@laravilt/forms/components/fields/TagsInput.vue')),
    key_value: defineAsyncComponent(() => import('@laravilt/forms/components/fields/KeyValue.vue')),
    repeater: defineAsyncComponent(() => import('@laravilt/forms/components/fields/Repeater.vue')),
    builder: defineAsyncComponent(() => import('@laravilt/forms/components/fields/Builder.vue')),
    icon_picker: defineAsyncComponent(() => import('@laravilt/forms/components/fields/IconPicker.vue')),
    number_field: defineAsyncComponent(() => import('@laravilt/forms/components/fields/NumberField.vue')),
    pin_input: defineAsyncComponent(() => import('@laravilt/forms/components/fields/PinInput.vue')),
    rate_input: defineAsyncComponent(() => import('@laravilt/forms/components/fields/RateInput.vue')),
    checkbox_list: defineAsyncComponent(() => import('@laravilt/forms/components/fields/CheckboxList.vue')),
    slider: defineAsyncComponent(() => import('@laravilt/forms/components/fields/Slider.vue')),
    code_editor: defineAsyncComponent(() => import('@laravilt/forms/components/fields/CodeEditor.vue')),

    // InfoList Entry components
    text_entry: defineAsyncComponent(() => import('@laravilt/infolists/components/entries/TextEntry.vue')),
    badge_entry: defineAsyncComponent(() => import('@laravilt/infolists/components/entries/BadgeEntry.vue')),
    icon_entry: defineAsyncComponent(() => import('@laravilt/infolists/components/entries/IconEntry.vue')),
    image_entry: defineAsyncComponent(() => import('@laravilt/infolists/components/entries/ImageEntry.vue')),
    color_entry: defineAsyncComponent(() => import('@laravilt/infolists/components/entries/ColorEntry.vue')),
    code_entry: defineAsyncComponent(() => import('@laravilt/infolists/components/entries/CodeEntry.vue')),
    key_value_entry: defineAsyncComponent(() => import('@laravilt/infolists/components/entries/KeyValueEntry.vue')),
    repeatable_entry: defineAsyncComponent(() => import('@laravilt/infolists/components/entries/RepeatableEntry.vue')),
}

// Globally registered components (app.component), so custom fields and entries render here the way
// they do on React, where Schema falls back to the component registry under the same names.
const globalComponents: Record<string, any> = getCurrentInstance()?.appContext.components ?? {}

const getComponent = (component: any) => {
    // Get component type from the component object
    const type = component.component || 'div'

    // The mapped component, then a registered one (as-is or as laravilt-kebab-name), then a div
    return componentMap[type]
        || globalComponents[type]
        || globalComponents[`laravilt-${String(type).replace(/_/g, '-')}`]
        || 'div'
}

// Get component props, excluding value and modelValue since we set them explicitly
const getComponentProps = (component: any) => {
    const { value, modelValue, ...props } = component
    return props
}

// Expose methods to parent component via template refs
defineExpose({
    getFormData,
    validateForm,
    updateSchema,
})
</script>
