import ActionButton from '@laravilt/actions/components/ActionButton';
import { SchemaContext, type SchemaContextValue } from '@laravilt/support/composables/contexts';
import { useLatest } from '@laravilt/support/composables/hooks';
import { resolveComponent } from '@laravilt/support/composables/registry';
import {
    createContext,
    lazy,
    Suspense,
    useContext,
    useCallback,
    useEffect,
    useImperativeHandle,
    useLayoutEffect,
    useMemo,
    useRef,
    useState,
    type ComponentType,
    type LazyExoticComponent,
    type Ref,
} from 'react';
import { getChildSchemas, isEntryComponent, isSchemaComponent } from '../lib/layout';

/**
 * Set by the outermost Schema. Nested Schemas (rendered by Section, Grid, Tabs, Split, Wizard) find it and
 * delegate reactive-field requests and schema updates to it: the server always returns the ROOT schema.
 * (Vue: provide/inject of `laraviltRootSchemaUpdate`.)
 */
const RootSchemaUpdateContext = createContext<((schema: any[]) => void) | null>(null);

/**
 * Methods exposed to parent components via `ref` (Vue `defineExpose`).
 */
export interface SchemaHandle {
    getFormData(): Record<string, any>;
    validateForm(): boolean;
    updateSchema(schema: any[]): void;
}

export interface SchemaProps {
    schema: Array<any>;
    modelValue?: Record<string, any>;
    schemaId?: string;
    parentHandlesActions?: boolean;
    formController?: string;
    formMethod?: string;
    onUpdateModelValue?: (value: Record<string, any>) => void;
    onUpdateSchema?: (value: any[]) => void;
    ref?: Ref<SchemaHandle | null>;
}

type AnyComponent = ComponentType<any> | LazyExoticComponent<ComponentType<any>>;

const load = (factory: () => Promise<{ default: ComponentType<any> }>): LazyExoticComponent<ComponentType<any>> =>
    lazy(factory);

// Map component types to their React components
const componentMap: Record<string, AnyComponent> = {
    // Schema layout components
    tabs: load(() => import('./Tabs')),
    section: load(() => import('./Section')),
    grid: load(() => import('./Grid')),
    split: load(() => import('./Split')),
    wizard: load(() => import('./Wizard')),

    // Form field components
    text_input: load(() => import('@laravilt/forms/components/fields/TextInput')),
    textarea: load(() => import('@laravilt/forms/components/fields/Textarea')),
    select: load(() => import('@laravilt/forms/components/fields/Select')),
    checkbox: load(() => import('@laravilt/forms/components/fields/Checkbox')),
    radio: load(() => import('@laravilt/forms/components/fields/Radio')),
    toggle: load(() => import('@laravilt/forms/components/fields/Toggle')),
    toggle_buttons: load(() => import('@laravilt/forms/components/fields/ToggleButtons')),
    hidden: load(() => import('@laravilt/forms/components/fields/Hidden')),
    date_picker: load(() => import('@laravilt/forms/components/fields/DatePicker')),
    time_picker: load(() => import('@laravilt/forms/components/fields/TimePicker')),
    date_time_picker: load(() => import('@laravilt/forms/components/fields/DateTimePicker')),
    date_range_picker: load(() => import('@laravilt/forms/components/fields/DateRangePicker')),
    color_picker: load(() => import('@laravilt/forms/components/fields/ColorPicker')),
    file_upload: load(() => import('@laravilt/forms/components/fields/FileUpload')),
    rich_editor: load(() => import('@laravilt/forms/components/fields/RichEditor')),
    markdown_editor: load(() => import('@laravilt/forms/components/fields/MarkdownEditor')),
    tags_input: load(() => import('@laravilt/forms/components/fields/TagsInput')),
    key_value: load(() => import('@laravilt/forms/components/fields/KeyValue')),
    repeater: load(() => import('@laravilt/forms/components/fields/Repeater')),
    builder: load(() => import('@laravilt/forms/components/fields/Builder')),
    icon_picker: load(() => import('@laravilt/forms/components/fields/IconPicker')),
    number_field: load(() => import('@laravilt/forms/components/fields/NumberField')),
    pin_input: load(() => import('@laravilt/forms/components/fields/PinInput')),
    rate_input: load(() => import('@laravilt/forms/components/fields/RateInput')),
    checkbox_list: load(() => import('@laravilt/forms/components/fields/CheckboxList')),
    slider: load(() => import('@laravilt/forms/components/fields/Slider')),
    code_editor: load(() => import('@laravilt/forms/components/fields/CodeEditor')),

    // InfoList Entry components
    text_entry: load(() => import('@laravilt/infolists/components/entries/TextEntry')),
    badge_entry: load(() => import('@laravilt/infolists/components/entries/BadgeEntry')),
    icon_entry: load(() => import('@laravilt/infolists/components/entries/IconEntry')),
    image_entry: load(() => import('@laravilt/infolists/components/entries/ImageEntry')),
    color_entry: load(() => import('@laravilt/infolists/components/entries/ColorEntry')),
    code_entry: load(() => import('@laravilt/infolists/components/entries/CodeEntry')),
    key_value_entry: load(() => import('@laravilt/infolists/components/entries/KeyValueEntry')),
    repeatable_entry: load(() => import('@laravilt/infolists/components/entries/RepeatableEntry')),
};

// Recursively extract all field components and their default values from schema
const extractFieldDefaults = (schema: Array<any>): Record<string, any> => {
    const defaults: Record<string, any> = {};

    // Safety check - ensure schema is an array
    if (!schema || !Array.isArray(schema)) {
        console.warn('extractFieldDefaults: schema is not an array', schema);
        return defaults;
    }

    for (const component of schema) {
        // Skip actions
        if (component.hasAction === true || (component.name && !component.component)) {
            continue;
        }

        // Skip entry components (read-only displays, not form fields)
        if (isEntryComponent(component)) {
            continue;
        }

        // Layout components (tabs, section, grid, split, wizard): recurse into every nested schema
        if (isSchemaComponent(component)) {
            for (const childSchema of getChildSchemas(component)) {
                Object.assign(defaults, extractFieldDefaults(childSchema));
            }
            continue; // Don't add the layout component itself as a field
        }

        // If it has a name and component type (it's an actual field), add its default value
        if (component.name && component.component) {
            // Use defaultValue or default, but NOT value (value could be an object from backend)
            // For hidden fields, also check the 'default' property
            // Use ?? (nullish coalescing) to properly handle false/0 values
            defaults[component.name] = component.defaultValue ?? component.default ?? component.value ?? null;
        }
    }

    return defaults;
};

// Find a field by name in the schema (recursively)
const findFieldInSchema = (schema: any[], fieldName: string): any => {
    for (const component of schema) {
        if (component.name === fieldName) {
            return component;
        }

        // Check nested schemas (schema, tabs[].schema, steps[].schema, split start/end)
        for (const childSchema of getChildSchemas(component)) {
            const found = findFieldInSchema(childSchema, fieldName);
            if (found) return found;
        }
    }
    return null;
};

// Check if an item is an action
const isAction = (item: any) => {
    // Actions have hasAction property or don't have a component property
    return item.hasAction === true || (item.name && !item.component);
};

const getComponent = (component: any): AnyComponent | null => {
    // Get component type from the component object
    const type = component.component || 'div';

    // Return the mapped component, then a globally registered one, or null (div fallback)
    return componentMap[type] || resolveComponent(type) || null;
};

// Get component props, excluding value and modelValue since we set them explicitly
const getComponentProps = (component: any): Record<string, any> => {
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    const { value, modelValue, ...props } = component;
    return props;
};

interface PendingRestore {
    scrollX: number;
    scrollY: number;
    activeElement: HTMLElement | null;
}

export default function Schema({
    schema,
    modelValue,
    schemaId,
    parentHandlesActions = false,
    formController = undefined,
    formMethod = 'getSchema',
    onUpdateModelValue,
    onUpdateSchema,
    ref,
}: SchemaProps) {
    const formRef = useRef<HTMLDivElement | null>(null);

    // Root Schema detection (see RootSchemaUpdateContext)
    const rootUpdateSchema = useContext(RootSchemaUpdateContext);
    const isRootSchema = rootUpdateSchema === null;

    // Only the latest reactive-field response may be applied; older ones that arrive late are dropped
    const reactiveRequestId = useRef(0);

    // Make schema internally reactive so it can be updated by reactive fields
    const [internalSchema, setInternalSchema] = useState<any[]>(schema);
    const [previousSchemaProp, setPreviousSchemaProp] = useState<any[]>(schema);

    // Watch for prop schema changes (from page navigation, etc.)
    if (schema !== previousSchemaProp) {
        setPreviousSchemaProp(schema);
        setInternalSchema(schema);
    }

    const latestSchema = useLatest(internalSchema);

    // Form data: seeded with defaults from schema merged with any provided modelValue.
    // `formDataRef` is the synchronous source of truth so rapid updates always build on the latest data.
    const [internalFormData, setInternalFormData] = useState<Record<string, any>>(() => ({
        ...extractFieldDefaults(schema),
        ...(modelValue || {}),
    }));
    const formDataRef = useRef<Record<string, any>>(internalFormData);

    const latest = useLatest({ formController, formMethod, onUpdateModelValue, onUpdateSchema, rootUpdateSchema });

    const commitFormData = useCallback(
        (update: (previous: Record<string, any>) => Record<string, any>, emit: boolean = true): Record<string, any> => {
            const next = update(formDataRef.current);
            formDataRef.current = next;
            setInternalFormData(next);

            if (emit) {
                latest.current.onUpdateModelValue?.(next);
            }

            return next;
        },
        [latest],
    );

    // Initialize on mount
    useEffect(() => {
        // Form data was initialized from schema defaults + modelValue in the state initializer
        latest.current.onUpdateModelValue?.(formDataRef.current);

        // Handle action-updated data events
        const handleActionUpdatedData = (event: Event) => {
            const updatedData = (event as CustomEvent).detail;

            if (updatedData && typeof updatedData === 'object') {
                // Merge the updated data into internal form data
                commitFormData((previous) => ({
                    ...previous,
                    ...updatedData,
                }));
            }
        };

        // Listen for action-updated-data events from ActionButton
        window.addEventListener('action-updated-data', handleActionUpdatedData);

        // Cleanup on unmount
        return () => {
            window.removeEventListener('action-updated-data', handleActionUpdatedData);
        };
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    // Watch for external modelValue changes and merge them
    useEffect(() => {
        if (!modelValue) {
            return;
        }

        const current = formDataRef.current;
        const hasChanges = Object.keys(modelValue).some((key) => current[key] !== modelValue[key]);

        if (!hasChanges) {
            return;
        }

        commitFormData(
            (previous) => ({
                ...previous,
                ...modelValue,
            }),
            false,
        );
    }, [modelValue, commitFormData]);

    // Save scroll position / focus across schema updates (restored after the DOM update)
    const pendingRestore = useRef<PendingRestore | null>(null);
    const [restoreTick, setRestoreTick] = useState(0);

    useLayoutEffect(() => {
        const restore = pendingRestore.current;

        if (!restore) {
            return;
        }

        pendingRestore.current = null;

        // Restore scroll position
        window.scrollTo({
            top: restore.scrollY,
            left: restore.scrollX,
            behavior: 'instant' as ScrollBehavior,
        });

        // Restore focus if element still exists
        if (restore.activeElement && document.contains(restore.activeElement)) {
            restore.activeElement.focus({ preventScroll: true });
        }
    }, [restoreTick]);

    // Function to update schema (for reactive fields)
    const updateSchema = useCallback(
        (newSchema: any[]) => {
            // Schemas from the server describe the whole form: nested Schemas hand them to the root
            const root = latest.current.rootUpdateSchema;
            if (root) {
                root(newSchema);
                return;
            }

            // Save current scroll position and focused element
            const scrollX = window.scrollX;
            const scrollY = window.scrollY;
            const activeElement = document.activeElement as HTMLElement | null;

            latestSchema.current = newSchema;
            setInternalSchema(newSchema);
            latest.current.onUpdateSchema?.(newSchema);

            // Restore scroll position and focus once the new schema is rendered
            pendingRestore.current = { scrollX, scrollY, activeElement };
            setRestoreTick((tick) => tick + 1);
        },
        [latestSchema, latest],
    );

    // Trigger reactive field update
    const triggerReactiveFieldUpdate = async (fieldName: string, field: any) => {
        // Nested Schemas emit their data up; the root Schema sees the change and sends the request itself
        if (!isRootSchema) {
            return;
        }

        const { formController: controller, formMethod: method } = latest.current;

        // Skip if no form controller is configured
        if (!controller) {
            console.warn('[Schema] No formController configured, skipping reactive field update');
            return;
        }

        // eslint-disable-next-line @typescript-eslint/no-unused-vars
        const debounceMs = field.isLazy ? field.liveDebounce || 500 : field.isLive && field.liveDebounce ? field.liveDebounce : 0;

        // TODO: Implement debouncing if needed
        const requestId = ++reactiveRequestId.current;

        try {
            const payload = {
                controller,
                method: method || 'getSchema',
                data: formDataRef.current,
                changed_field: fieldName,
            };

            const response = await fetch('/reactive-fields/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify(payload),
            });

            if (!response.ok) {
                throw new Error('Failed to update reactive fields');
            }

            const result = await response.json();

            // A newer request was sent meanwhile: this response is stale
            if (requestId !== reactiveRequestId.current) {
                return;
            }

            if (result.schema) {
                updateSchema(result.schema);
            }

            // Update form data if backend modified it (from afterStateUpdated)
            if (result.data) {
                commitFormData((previous) => ({ ...previous, ...result.data }));
            }
        } catch (error) {
            console.error('[FormRenderer] Error updating reactive fields:', error);
        }
    };

    const updateValue = async (name: string, value: any) => {
        // Update internal form data
        commitFormData((previous) => ({
            ...previous,
            [name]: value,
        }));

        // Check if this field is reactive (live/lazy)
        const field = findFieldInSchema(latestSchema.current, name);

        // Skip reactive updates for Repeater fields - they handle their own internal reactivity
        // Triggering a schema update here would cause the Repeater to remount and lose state
        if (field && field.component === 'repeater') {
            return;
        }

        if (field && (field.isLive || field.isLazy)) {
            await triggerReactiveFieldUpdate(name, field);
        }
    };

    // Handle component update events
    const handleComponentUpdate = async (component: any, value: any) => {
        if (isSchemaComponent(component)) {
            // For schema components (Section, Grid, Tabs), value is an object with field updates
            // Update internal form data first
            const oldData = { ...formDataRef.current };

            // Check if any values actually changed before proceeding
            let hasChanges = false;
            for (const [fieldName, fieldValue] of Object.entries(value)) {
                if (oldData[fieldName] !== fieldValue) {
                    hasChanges = true;
                    break;
                }
            }

            // Skip if nothing changed (performance optimization)
            if (!hasChanges) {
                return;
            }

            commitFormData((previous) => ({ ...previous, ...value }));

            // Check each field that changed for reactivity
            for (const [fieldName, fieldValue] of Object.entries(value)) {
                // Only trigger if value actually changed
                if (oldData[fieldName] !== fieldValue) {
                    // Find the field in schema and check if it's reactive
                    const field = findFieldInSchema(latestSchema.current, fieldName);
                    // Repeaters handle their own reactivity (same rule as updateValue)
                    if (field && field.component !== 'repeater' && (field.isLive || field.isLazy)) {
                        await triggerReactiveFieldUpdate(fieldName, field);
                    }
                }
            }
        } else {
            await updateValue(component.name, value);
        }
    };

    // Collect all form data - just return the internal tracked data
    const getFormData = useCallback(() => {
        // Return a copy to avoid mutations
        return { ...formDataRef.current };
    }, []);

    // Validate the form using HTML5 validation
    const validateForm = useCallback(() => {
        if (!formRef.current) {
            return true;
        }

        // Find the closest form element (could be parent or self if we're inside a form)
        const formElement = formRef.current.closest('form') as HTMLFormElement | null;

        if (formElement) {
            const isValid = formElement.checkValidity();

            if (!isValid) {
                // Trigger validation UI (show error messages)
                formElement.reportValidity();
            }

            return isValid;
        }

        // If no parent form, validate all inputs within this container
        const inputs = formRef.current.querySelectorAll('input, select, textarea') as NodeListOf<
            HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement
        >;
        let isValid = true;

        for (const input of inputs) {
            if (!input.checkValidity()) {
                input.reportValidity();
                isValid = false;
                break;
            }
        }

        return isValid;
    }, []);

    // Provide getFormData, validateForm, updateSchema, and schemaId to all child components
    const contextValue = useMemo<SchemaContextValue>(
        () => ({
            getFormData,
            validateForm,
            updateSchema,
            schemaId: schemaId || null,
            formController,
            formMethod,
        }),
        [getFormData, validateForm, updateSchema, schemaId, formController, formMethod],
    );

    // Expose methods to parent component via ref
    useImperativeHandle(
        ref,
        () => ({
            getFormData,
            validateForm,
            updateSchema,
        }),
        [getFormData, validateForm, updateSchema],
    );

    const hasSchema = !!internalSchema && Array.isArray(internalSchema);

    // Separate action components from regular components
    const actionComponents = hasSchema ? internalSchema.filter(isAction) : [];
    const nonActionComponents = hasSchema ? internalSchema.filter((item: any) => !isAction(item)) : [];

    // Determine container spacing based on what we're rendering
    let containerClass = '';
    if (hasSchema && internalSchema.length > 0) {
        // Check if we're rendering sections - if so, use space-y-8 (reduced from space-y-12)
        const hasSections = internalSchema.some((c: any) => c.component === 'section');

        // Otherwise use space-y-6 for general spacing
        containerClass = hasSections ? 'space-y-8' : 'space-y-6';
    }

    return (
        <RootSchemaUpdateContext.Provider value={rootUpdateSchema ?? updateSchema}>
        <SchemaContext.Provider value={contextValue}>
            <div ref={formRef} className={containerClass}>
                {nonActionComponents.map((component: any, index: number) => {
                    const key = component.name || component.id || index;
                    const Component = getComponent(component);

                    if (!Component) {
                        return <div key={key} />;
                    }

                    const schemaComponent = isSchemaComponent(component);
                    const fieldValue = schemaComponent
                        ? undefined
                        : isEntryComponent(component)
                          ? component.state
                          : internalFormData[component.name];

                    // Render regular form components
                    return (
                        <Suspense key={key} fallback={null}>
                            <Component
                                {...getComponentProps(component)}
                                value={fieldValue}
                                state={fieldValue}
                                modelValue={schemaComponent ? internalFormData : internalFormData[component.name]}
                                onUpdateModelValue={(value: any) => handleComponentUpdate(component, value)}
                            />
                        </Suspense>
                    );
                })}

                {/* Render action buttons grouped together with proper gap (only if not handled by parent) */}
                {actionComponents.length > 0 && !parentHandlesActions ? (
                    <div className="flex items-center gap-4">
                        {actionComponents.map((action: any, index: number) => (
                            <ActionButton key={action.name || index} {...action} getFormData={getFormData} />
                        ))}
                    </div>
                ) : null}
            </div>
        </SchemaContext.Provider>
        </RootSchemaUpdateContext.Provider>
    );
}
