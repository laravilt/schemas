import { resolveComponent } from '@laravilt/support/composables/registry';

export interface SplitProps {
    startSchema?: Array<any>;
    endSchema?: Array<any>;
    startColumnSpan?: string | number;
    endColumnSpan?: string | number;
    rtl?: boolean;
    theme?: string;
}

// Vue normalizes a non-string class value (e.g. a number) to an empty class
const toClass = (value: string | number): string => (typeof value === 'string' ? value : '');

// Vue `<component :is="child.component || 'div'" v-bind="child" />`: resolve the registered component, else a plain div
const renderChild = (child: any, index: number) => {
    const Component = resolveComponent(child.component);

    if (!Component) {
        return <div key={index} />;
    }

    return <Component key={index} {...child} />;
};

export default function Split({
    startSchema = [],
    endSchema = [],
    startColumnSpan = 'md:col-span-6',
    endColumnSpan = 'md:col-span-6',
    rtl = false,
}: SplitProps) {
    return (
        <div className="grid md:grid-cols-12 gap-6" dir={rtl ? 'rtl' : 'ltr'}>
            <div className={toClass(startColumnSpan)}>{startSchema.map(renderChild)}</div>
            <div className={toClass(endColumnSpan)}>{endSchema.map(renderChild)}</div>
        </div>
    );
}
