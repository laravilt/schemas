import { TabsContent, TabsList, TabsTrigger, Tabs as UiTabs } from '@/components/ui/tabs';
import { useSchemaContext } from '@laravilt/support/composables/contexts';
import { resolveIcon } from '@laravilt/support/lib/icons';
import { useEffect, useState } from 'react';
import Schema from './Schema';

export interface TabsProps {
    tabs: Array<any>;
    activeTab?: number;
    persistTabInQueryString?: boolean;
    modelValue?: Record<string, any>;
    onUpdateModelValue?: (value: Record<string, any>) => void;
}

// persistTabInQueryString(): the active tab is kept in `?tab=` (the tab id, else its index)
const TAB_QUERY_KEY = 'tab';

const getTabKey = (tab: any, index: number): string => (tab && tab.id ? String(tab.id) : String(index));

const readTabFromQueryString = (persist: boolean | undefined, tabs: Array<any>): string | null => {
    if (!persist || typeof window === 'undefined') return null;

    const requested = new URLSearchParams(window.location.search).get(TAB_QUERY_KEY);
    if (requested === null) return null;

    const index = (tabs || []).findIndex((tab, i) => getTabKey(tab, i) === requested);
    return index >= 0 ? String(index) : null;
};

const writeTabToQueryString = (persist: boolean | undefined, tabs: Array<any>, value: string) => {
    if (!persist || typeof window === 'undefined') return;

    const index = Number(value);
    const url = new URL(window.location.href);
    url.searchParams.set(TAB_QUERY_KEY, getTabKey(tabs?.[index], index));
    window.history.replaceState(window.history.state, '', url.toString());
};

export default function Tabs({ tabs, activeTab, persistTabInQueryString, modelValue, onUpdateModelValue }: TabsProps) {
    // Inject parent context for reactive fields
    const { formController, formMethod = 'getSchema' } = useSchemaContext();

    // Evaluated once, like the Vue setup
    const [initialTab] = useState(() => readTabFromQueryString(persistTabInQueryString, tabs) ?? String(activeTab || 0));

    const [isLoading, setIsLoading] = useState(false);
    const [currentTab, setCurrentTab] = useState(initialTab);

    const handleTabChange = (value: string) => {
        if (value !== currentTab) {
            writeTabToQueryString(persistTabInQueryString, tabs, value);
            setIsLoading(true);
            setCurrentTab(value);
            // Small delay to show skeleton
            setTimeout(() => {
                setIsLoading(false);
            }, 150);
        }
    };

    // Reactive document direction for RTL support
    const [dir, setDir] = useState<'ltr' | 'rtl'>('ltr');

    useEffect(() => {
        // Set initial direction
        setDir((document.documentElement.dir as 'ltr' | 'rtl') || 'ltr');

        // Watch for direction changes on the html element
        const observer = new MutationObserver((mutations) => {
            for (const mutation of mutations) {
                if (mutation.attributeName === 'dir') {
                    setDir((document.documentElement.dir as 'ltr' | 'rtl') || 'ltr');
                }
            }
        });

        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['dir'],
        });

        return () => {
            observer.disconnect();
        };
    }, []);

    const tabList = tabs || [];

    return (
        <UiTabs defaultValue={initialTab} dir={dir} className="w-full" onValueChange={handleTabChange}>
            <TabsList className="w-full justify-start">
                {tabList.map((tab: any, index: number) => {
                    const Icon = tab.icon ? resolveIcon(tab.icon) : null;

                    return (
                        <TabsTrigger key={index} value={String(index)} className="gap-2">
                            {Icon ? <Icon className="h-4 w-4" /> : null}
                            <span>{tab.label}</span>
                            {tab.badge ? (
                                <span className="ms-1 inline-flex items-center justify-center px-2 py-0.5 text-xs font-medium rounded-full bg-primary/10 text-primary">
                                    {tab.badge}
                                </span>
                            ) : null}
                        </TabsTrigger>
                    );
                })}
            </TabsList>

            {tabList.map((tab: any, index: number) => (
                <TabsContent key={index} value={String(index)} className="space-y-6 mt-6">
                    {/* Skeleton while loading */}
                    {isLoading && currentTab === String(index) ? (
                        <div key="skeleton" className="space-y-6 animate-in fade-in-0 duration-150 ease-out">
                            <div className="bg-card rounded-xl border shadow-sm p-6 space-y-4">
                                <div className="h-4 bg-muted/60 rounded w-1/4 animate-pulse"></div>
                                <div className="space-y-3">
                                    <div className="h-10 bg-muted/60 rounded animate-pulse" style={{ animationDelay: '0ms' }}></div>
                                    <div className="h-10 bg-muted/60 rounded animate-pulse" style={{ animationDelay: '75ms' }}></div>
                                    <div className="h-10 bg-muted/60 rounded w-3/4 animate-pulse" style={{ animationDelay: '150ms' }}></div>
                                </div>
                            </div>
                        </div>
                    ) : tab.schema ? (
                        // Actual content
                        <Schema
                            key="content"
                            schema={tab.schema}
                            modelValue={modelValue}
                            formController={formController}
                            formMethod={formMethod}
                            onUpdateModelValue={(value) => onUpdateModelValue?.(value)}
                        />
                    ) : null}
                </TabsContent>
            ))}
        </UiTabs>
    );
}
