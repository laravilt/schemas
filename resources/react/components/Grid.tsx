import { useSchemaContext } from '@laravilt/support/composables/contexts';
import Schema from './Schema';

export interface GridProps {
    columns: number | Record<string, number>;
    schema: Array<any>;
    modelValue?: Record<string, any>;
    onUpdateModelValue?: (value: Record<string, any>) => void;
}

// Pre-defined column classes for Tailwind to scan
const mdColumnMap: Record<number, string> = {
    1: 'md:grid-cols-1',
    2: 'md:grid-cols-2',
    3: 'md:grid-cols-3',
    4: 'md:grid-cols-4',
    5: 'md:grid-cols-5',
    6: 'md:grid-cols-6',
    7: 'md:grid-cols-7',
    8: 'md:grid-cols-8',
    9: 'md:grid-cols-9',
    10: 'md:grid-cols-10',
    11: 'md:grid-cols-11',
    12: 'md:grid-cols-12',
};

const defaultColumnMap: Record<number, string> = {
    1: 'grid-cols-1',
    2: 'grid-cols-2',
    3: 'grid-cols-3',
    4: 'grid-cols-4',
    5: 'grid-cols-5',
    6: 'grid-cols-6',
    7: 'grid-cols-7',
    8: 'grid-cols-8',
    9: 'grid-cols-9',
    10: 'grid-cols-10',
    11: 'grid-cols-11',
    12: 'grid-cols-12',
};

const smColumnMap: Record<number, string> = {
    1: 'sm:grid-cols-1',
    2: 'sm:grid-cols-2',
    3: 'sm:grid-cols-3',
    4: 'sm:grid-cols-4',
    5: 'sm:grid-cols-5',
    6: 'sm:grid-cols-6',
};

const lgColumnMap: Record<number, string> = {
    1: 'lg:grid-cols-1',
    2: 'lg:grid-cols-2',
    3: 'lg:grid-cols-3',
    4: 'lg:grid-cols-4',
    5: 'lg:grid-cols-5',
    6: 'lg:grid-cols-6',
};

const xlColumnMap: Record<number, string> = {
    1: 'xl:grid-cols-1',
    2: 'xl:grid-cols-2',
    3: 'xl:grid-cols-3',
    4: 'xl:grid-cols-4',
    5: 'xl:grid-cols-5',
    6: 'xl:grid-cols-6',
};

// Pre-defined column span classes
const colSpanMap: Record<number, string> = {
    1: 'col-span-1',
    2: 'col-span-2',
    3: 'col-span-3',
    4: 'col-span-4',
    5: 'col-span-5',
    6: 'col-span-6',
    7: 'col-span-7',
    8: 'col-span-8',
    9: 'col-span-9',
    10: 'col-span-10',
    11: 'col-span-11',
    12: 'col-span-12',
};

const smColSpanMap: Record<number, string> = {
    1: 'sm:col-span-1',
    2: 'sm:col-span-2',
    3: 'sm:col-span-3',
    4: 'sm:col-span-4',
    5: 'sm:col-span-5',
    6: 'sm:col-span-6',
};

const mdColSpanMap: Record<number, string> = {
    1: 'md:col-span-1',
    2: 'md:col-span-2',
    3: 'md:col-span-3',
    4: 'md:col-span-4',
    5: 'md:col-span-5',
    6: 'md:col-span-6',
};

const lgColSpanMap: Record<number, string> = {
    1: 'lg:col-span-1',
    2: 'lg:col-span-2',
    3: 'lg:col-span-3',
    4: 'lg:col-span-4',
    5: 'lg:col-span-5',
    6: 'lg:col-span-6',
};

const getGridClasses = (columns: number | Record<string, number>): string => {
    const classes = ['grid', 'gap-6'];

    if (typeof columns === 'number') {
        classes.push('grid-cols-1');
        if (columns > 1) {
            classes.push(mdColumnMap[columns] || `md:grid-cols-${columns}`);
        }
    } else if (typeof columns === 'object') {
        if (columns.default) classes.push(defaultColumnMap[columns.default] || `grid-cols-${columns.default}`);
        if (columns.sm) classes.push(smColumnMap[columns.sm] || `sm:grid-cols-${columns.sm}`);
        if (columns.md) classes.push(mdColumnMap[columns.md] || `md:grid-cols-${columns.md}`);
        if (columns.lg) classes.push(lgColumnMap[columns.lg] || `lg:grid-cols-${columns.lg}`);
        if (columns.xl) classes.push(xlColumnMap[columns.xl] || `xl:grid-cols-${columns.xl}`);
    }

    return classes.join(' ');
};

const getColumnSpanClass = (child: any): string => {
    if (!child.columnSpan) return '';

    // Handle 'full' string value from columnSpanFull()
    if (child.columnSpan === 'full') {
        return 'col-span-full';
    }

    if (typeof child.columnSpan === 'number') {
        return colSpanMap[child.columnSpan] || `col-span-${child.columnSpan}`;
    }

    if (typeof child.columnSpan === 'object') {
        const classes: string[] = [];
        if (child.columnSpan.default) classes.push(colSpanMap[child.columnSpan.default] || `col-span-${child.columnSpan.default}`);
        if (child.columnSpan.sm) classes.push(smColSpanMap[child.columnSpan.sm] || `sm:col-span-${child.columnSpan.sm}`);
        if (child.columnSpan.md) classes.push(mdColSpanMap[child.columnSpan.md] || `md:col-span-${child.columnSpan.md}`);
        if (child.columnSpan.lg) classes.push(lgColSpanMap[child.columnSpan.lg] || `lg:col-span-${child.columnSpan.lg}`);
        return classes.join(' ');
    }

    return '';
};

export default function Grid({ columns, schema, modelValue, onUpdateModelValue }: GridProps) {
    // Inject parent context for reactive fields
    const { formController, formMethod = 'getSchema' } = useSchemaContext();

    if (!(schema && schema.length > 0)) {
        return null;
    }

    return (
        <div className={getGridClasses(columns)}>
            {schema.map((child: any, index: number) => (
                <div key={child.name || child.id || index} className={getColumnSpanClass(child)}>
                    <Schema
                        schema={[child]}
                        modelValue={modelValue}
                        formController={formController}
                        formMethod={formMethod}
                        onUpdateModelValue={(value) => onUpdateModelValue?.(value)}
                    />
                </div>
            ))}
        </div>
    );
}
