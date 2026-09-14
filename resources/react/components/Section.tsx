import { cn } from '@/lib/utils';
import { useSchemaContext } from '@laravilt/support/composables/contexts';
import { resolveIcon } from '@laravilt/support/lib/icons';
import { ChevronDown } from 'lucide-react';
import { useState } from 'react';
import Schema from './Schema';

export interface SectionProps {
    heading?: string;
    description?: string;
    icon?: string;
    collapsible?: boolean;
    collapsed?: boolean;
    schema?: Array<any>;
    modelValue?: Record<string, any>;
    onUpdateModelValue?: (value: Record<string, any>) => void;
}

export default function Section({
    heading,
    description,
    icon,
    collapsible,
    collapsed,
    schema,
    modelValue,
    onUpdateModelValue,
}: SectionProps) {
    // Inject parent context for reactive fields
    const { formController, formMethod = 'getSchema' } = useSchemaContext();

    const [isCollapsed, setIsCollapsed] = useState<boolean>(collapsed || false);

    const toggleCollapse = () => {
        if (collapsible) {
            setIsCollapsed((value) => !value);
        }
    };

    const Icon = icon ? resolveIcon(icon) : null;

    return (
        <div className="bg-card text-card-foreground rounded-xl border shadow-sm">
            {/* Section Header */}
            {heading ? (
                <header className={cn('px-6 py-4 transition-all duration-200', isCollapsed ? '' : 'border-b')}>
                    <div
                        className={cn('flex items-center gap-3', collapsible ? 'cursor-pointer select-none' : '')}
                        onClick={() => collapsible && toggleCollapse()}
                    >
                        {Icon ? (
                            <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary flex-shrink-0">
                                <Icon className="h-5 w-5" />
                            </div>
                        ) : null}
                        <div className="flex-1 min-w-0">
                            <h3 className="leading-none font-semibold">{heading}</h3>
                            {description ? <p className="mt-1 text-sm text-muted-foreground">{description}</p> : null}
                        </div>
                        {collapsible ? (
                            <ChevronDown
                                className={cn(
                                    'h-4 w-4 text-muted-foreground transition-transform duration-200 ease-out flex-shrink-0',
                                    isCollapsed ? '-rotate-90 rtl:rotate-90' : '',
                                )}
                            />
                        ) : null}
                    </div>
                </header>
            ) : null}

            {/* Section Content with smooth collapse */}
            <div className="grid transition-all duration-200 ease-out" style={{ gridTemplateRows: isCollapsed ? '0fr' : '1fr' }}>
                <div className="overflow-hidden">
                    <div className="p-6 space-y-6">
                        {schema && schema.length > 0 ? (
                            <Schema
                                schema={schema}
                                modelValue={modelValue}
                                formController={formController}
                                formMethod={formMethod}
                                onUpdateModelValue={(value) => onUpdateModelValue?.(value)}
                            />
                        ) : null}
                    </div>
                </div>
            </div>
        </div>
    );
}
