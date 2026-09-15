/**
 * Layout helpers shared by Schema, Grid, Split and Tabs.
 *
 * Keep this file identical to resources/js/lib/layout.ts (Vue / React parity).
 *
 * Every Tailwind class below is written out literally: Tailwind only generates classes it can find
 * as complete strings in the scanned sources, so classes must never be built with template strings.
 */

/** Layout components that receive the whole form data and render nested schemas. */
export const SCHEMA_COMPONENT_TYPES = ['tabs', 'section', 'grid', 'split', 'wizard']

/** Infolist entries (read-only displays that use `state` from the backend). */
export const ENTRY_COMPONENT_TYPES = [
    'text_entry',
    'badge_entry',
    'icon_entry',
    'image_entry',
    'color_entry',
    'code_entry',
    'key_value_entry',
    'repeatable_entry',
]

export const isSchemaComponent = (component: any): boolean => SCHEMA_COMPONENT_TYPES.includes(component?.component)

export const isEntryComponent = (component: any): boolean => ENTRY_COMPONENT_TYPES.includes(component?.component)

/**
 * Child schemas of a layout component: `schema`, every `tabs[].schema`, every `steps[].schema`
 * (Wizard) and `startSchema` / `endSchema` (Split; `leftSchema` / `rightSchema` are duplicates).
 */
export const getChildSchemas = (component: any): any[][] => {
    const children: any[][] = []

    if (!component || typeof component !== 'object') {
        return children
    }

    if (Array.isArray(component.schema)) {
        children.push(component.schema)
    }

    for (const key of ['tabs', 'steps']) {
        if (Array.isArray(component[key])) {
            for (const item of component[key]) {
                if (item && Array.isArray(item.schema)) {
                    children.push(item.schema)
                }
            }
        }
    }

    if (component.component === 'split') {
        for (const key of ['startSchema', 'endSchema']) {
            if (Array.isArray(component[key])) {
                children.push(component[key])
            }
        }
    }

    return children
}

type Breakpoint = 'default' | 'sm' | 'md' | 'lg' | 'xl' | '2xl'

const BREAKPOINTS: Breakpoint[] = ['default', 'sm', 'md', 'lg', 'xl', '2xl']

const GRID_COLS: Record<Breakpoint, Record<number, string>> = {
    default: {
        1: 'grid-cols-1', 2: 'grid-cols-2', 3: 'grid-cols-3', 4: 'grid-cols-4', 5: 'grid-cols-5', 6: 'grid-cols-6',
        7: 'grid-cols-7', 8: 'grid-cols-8', 9: 'grid-cols-9', 10: 'grid-cols-10', 11: 'grid-cols-11', 12: 'grid-cols-12',
    },
    sm: {
        1: 'sm:grid-cols-1', 2: 'sm:grid-cols-2', 3: 'sm:grid-cols-3', 4: 'sm:grid-cols-4', 5: 'sm:grid-cols-5', 6: 'sm:grid-cols-6',
        7: 'sm:grid-cols-7', 8: 'sm:grid-cols-8', 9: 'sm:grid-cols-9', 10: 'sm:grid-cols-10', 11: 'sm:grid-cols-11', 12: 'sm:grid-cols-12',
    },
    md: {
        1: 'md:grid-cols-1', 2: 'md:grid-cols-2', 3: 'md:grid-cols-3', 4: 'md:grid-cols-4', 5: 'md:grid-cols-5', 6: 'md:grid-cols-6',
        7: 'md:grid-cols-7', 8: 'md:grid-cols-8', 9: 'md:grid-cols-9', 10: 'md:grid-cols-10', 11: 'md:grid-cols-11', 12: 'md:grid-cols-12',
    },
    lg: {
        1: 'lg:grid-cols-1', 2: 'lg:grid-cols-2', 3: 'lg:grid-cols-3', 4: 'lg:grid-cols-4', 5: 'lg:grid-cols-5', 6: 'lg:grid-cols-6',
        7: 'lg:grid-cols-7', 8: 'lg:grid-cols-8', 9: 'lg:grid-cols-9', 10: 'lg:grid-cols-10', 11: 'lg:grid-cols-11', 12: 'lg:grid-cols-12',
    },
    xl: {
        1: 'xl:grid-cols-1', 2: 'xl:grid-cols-2', 3: 'xl:grid-cols-3', 4: 'xl:grid-cols-4', 5: 'xl:grid-cols-5', 6: 'xl:grid-cols-6',
        7: 'xl:grid-cols-7', 8: 'xl:grid-cols-8', 9: 'xl:grid-cols-9', 10: 'xl:grid-cols-10', 11: 'xl:grid-cols-11', 12: 'xl:grid-cols-12',
    },
    '2xl': {
        1: '2xl:grid-cols-1', 2: '2xl:grid-cols-2', 3: '2xl:grid-cols-3', 4: '2xl:grid-cols-4', 5: '2xl:grid-cols-5', 6: '2xl:grid-cols-6',
        7: '2xl:grid-cols-7', 8: '2xl:grid-cols-8', 9: '2xl:grid-cols-9', 10: '2xl:grid-cols-10', 11: '2xl:grid-cols-11', 12: '2xl:grid-cols-12',
    },
}

const COL_SPAN: Record<Breakpoint, Record<string, string>> = {
    default: {
        1: 'col-span-1', 2: 'col-span-2', 3: 'col-span-3', 4: 'col-span-4', 5: 'col-span-5', 6: 'col-span-6',
        7: 'col-span-7', 8: 'col-span-8', 9: 'col-span-9', 10: 'col-span-10', 11: 'col-span-11', 12: 'col-span-12', full: 'col-span-full',
    },
    sm: {
        1: 'sm:col-span-1', 2: 'sm:col-span-2', 3: 'sm:col-span-3', 4: 'sm:col-span-4', 5: 'sm:col-span-5', 6: 'sm:col-span-6',
        7: 'sm:col-span-7', 8: 'sm:col-span-8', 9: 'sm:col-span-9', 10: 'sm:col-span-10', 11: 'sm:col-span-11', 12: 'sm:col-span-12', full: 'sm:col-span-full',
    },
    md: {
        1: 'md:col-span-1', 2: 'md:col-span-2', 3: 'md:col-span-3', 4: 'md:col-span-4', 5: 'md:col-span-5', 6: 'md:col-span-6',
        7: 'md:col-span-7', 8: 'md:col-span-8', 9: 'md:col-span-9', 10: 'md:col-span-10', 11: 'md:col-span-11', 12: 'md:col-span-12', full: 'md:col-span-full',
    },
    lg: {
        1: 'lg:col-span-1', 2: 'lg:col-span-2', 3: 'lg:col-span-3', 4: 'lg:col-span-4', 5: 'lg:col-span-5', 6: 'lg:col-span-6',
        7: 'lg:col-span-7', 8: 'lg:col-span-8', 9: 'lg:col-span-9', 10: 'lg:col-span-10', 11: 'lg:col-span-11', 12: 'lg:col-span-12', full: 'lg:col-span-full',
    },
    xl: {
        1: 'xl:col-span-1', 2: 'xl:col-span-2', 3: 'xl:col-span-3', 4: 'xl:col-span-4', 5: 'xl:col-span-5', 6: 'xl:col-span-6',
        7: 'xl:col-span-7', 8: 'xl:col-span-8', 9: 'xl:col-span-9', 10: 'xl:col-span-10', 11: 'xl:col-span-11', 12: 'xl:col-span-12', full: 'xl:col-span-full',
    },
    '2xl': {
        1: '2xl:col-span-1', 2: '2xl:col-span-2', 3: '2xl:col-span-3', 4: '2xl:col-span-4', 5: '2xl:col-span-5', 6: '2xl:col-span-6',
        7: '2xl:col-span-7', 8: '2xl:col-span-8', 9: '2xl:col-span-9', 10: '2xl:col-span-10', 11: '2xl:col-span-11', 12: '2xl:col-span-12', full: '2xl:col-span-full',
    },
}

/** Twelve-column track used by Split, per breakpoint. */
const SPLIT_GRID: Record<string, string> = {
    default: 'grid-cols-12',
    sm: 'sm:grid-cols-12',
    md: 'md:grid-cols-12',
    lg: 'lg:grid-cols-12',
    xl: 'xl:grid-cols-12',
    '2xl': '2xl:grid-cols-12',
}

const isBreakpoint = (value: string): value is Breakpoint => (BREAKPOINTS as string[]).includes(value)

/** Grid container classes for `columns` (a number, or `{default, sm, md, lg, xl, 2xl}`). */
export const getGridClasses = (columns: unknown): string => {
    const classes = ['grid', 'gap-6']

    if (typeof columns === 'number' || (typeof columns === 'string' && /^\d+$/.test(columns))) {
        classes.push('grid-cols-1')
        const count = Number(columns)
        if (count > 1 && GRID_COLS.md[count]) {
            classes.push(GRID_COLS.md[count])
        }
    } else if (columns && typeof columns === 'object') {
        for (const [breakpoint, count] of Object.entries(columns as Record<string, unknown>)) {
            const cls = isBreakpoint(breakpoint) ? GRID_COLS[breakpoint][Number(count)] : undefined
            if (cls) {
                classes.push(cls)
            }
        }
    }

    return classes.join(' ')
}

/** Column-span classes for `columnSpan` (a number, `'full'`, or `{default, sm, md, lg, xl, 2xl}`). */
export const getColumnSpanClasses = (columnSpan: unknown): string => {
    if (columnSpan === null || columnSpan === undefined || columnSpan === '' || columnSpan === 0) {
        return ''
    }

    if (typeof columnSpan === 'number' || typeof columnSpan === 'string') {
        return COL_SPAN.default[String(columnSpan)] ?? ''
    }

    if (typeof columnSpan === 'object') {
        const classes: string[] = []
        for (const [breakpoint, span] of Object.entries(columnSpan as Record<string, unknown>)) {
            const cls = isBreakpoint(breakpoint) ? COL_SPAN[breakpoint][String(span)] : undefined
            if (cls) {
                classes.push(cls)
            }
        }
        return classes.join(' ')
    }

    return ''
}

/** Split container classes: twelve columns from the `fromBreakpoint` breakpoint up. */
export const getSplitGridClasses = (fromBreakpoint: unknown): string => {
    const breakpoint = typeof fromBreakpoint === 'string' && SPLIT_GRID[fromBreakpoint] ? fromBreakpoint : 'md'

    return `grid gap-6 ${SPLIT_GRID[breakpoint]}`
}

/**
 * Split side span: a number spans that many of the twelve columns from `fromBreakpoint` up;
 * a string is used as given (e.g. the PHP default `md:col-span-6`).
 */
export const getSplitSpanClasses = (span: unknown, fromBreakpoint: unknown): string => {
    if (typeof span === 'string' && !/^\d+$/.test(span)) {
        return span
    }

    const breakpoint = typeof fromBreakpoint === 'string' && isBreakpoint(fromBreakpoint) ? fromBreakpoint : 'md'

    return COL_SPAN[breakpoint][String(span ?? 6)] ?? COL_SPAN[breakpoint][6]
}
