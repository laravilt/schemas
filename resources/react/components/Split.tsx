import { useSchemaContext } from '@laravilt/support/composables/contexts';
import { getSplitGridClasses, getSplitSpanClasses } from '../lib/layout';
import Schema from './Schema';

export interface SplitProps {
    startSchema?: Array<any>;
    endSchema?: Array<any>;
    startColumnSpan?: string | number;
    endColumnSpan?: string | number;
    fromBreakpoint?: string;
    rtl?: boolean;
    theme?: string;
    modelValue?: Record<string, any>;
    onUpdateModelValue?: (value: Record<string, any>) => void;
}

const EMPTY: any[] = [];

export default function Split({
    startSchema = EMPTY,
    endSchema = EMPTY,
    // Numeric so getSplitSpanClasses applies it from `fromBreakpoint` (a fixed `md:` class would not)
    startColumnSpan = 6,
    endColumnSpan = 6,
    fromBreakpoint = 'md',
    rtl = false,
    modelValue,
    onUpdateModelValue,
}: SplitProps) {
    // Inject parent context for reactive fields
    const { formController, formMethod = 'getSchema' } = useSchemaContext();

    const renderSide = (sideSchema: any[]) =>
        sideSchema.length > 0 ? (
            <Schema
                schema={sideSchema}
                modelValue={modelValue}
                formController={formController}
                formMethod={formMethod}
                onUpdateModelValue={(value) => onUpdateModelValue?.(value)}
            />
        ) : null;

    return (
        <div className={getSplitGridClasses(fromBreakpoint)} dir={rtl ? 'rtl' : 'ltr'}>
            <div className={getSplitSpanClasses(startColumnSpan, fromBreakpoint)}>{renderSide(startSchema)}</div>
            <div className={getSplitSpanClasses(endColumnSpan, fromBreakpoint)}>{renderSide(endSchema)}</div>
        </div>
    );
}
