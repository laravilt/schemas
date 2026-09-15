import { useSchemaContext } from '@laravilt/support/composables/contexts';
import { getColumnSpanClasses, getGridClasses } from '../lib/layout';
import Schema from './Schema';

export interface GridProps {
    columns: number | Record<string, number>;
    schema: Array<any>;
    modelValue?: Record<string, any>;
    onUpdateModelValue?: (value: Record<string, any>) => void;
}

export default function Grid({ columns, schema, modelValue, onUpdateModelValue }: GridProps) {
    // Inject parent context for reactive fields
    const { formController, formMethod = 'getSchema' } = useSchemaContext();

    if (!(schema && schema.length > 0)) {
        return null;
    }

    // Static class maps (see lib/layout.ts) so Tailwind keeps every class
    return (
        <div className={getGridClasses(columns)}>
            {schema.map((child: any, index: number) => (
                <div key={child.name || child.id || index} className={getColumnSpanClasses(child.columnSpan)}>
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
