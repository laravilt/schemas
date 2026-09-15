// Laravilt Schemas Package Entry Point
export { default as Grid } from './components/Grid';
export { default as Section } from './components/Section';
export { default as Split } from './components/Split';
export { default as Tabs } from './components/Tabs';
export { default as Wizard } from './components/Wizard';

export default {
    /**
     * The Vue entry registers no global `laravilt-*` component names, so there is nothing to register here.
     * (`laravilt-grid` / `laravilt-section` / `laravilt-tabs` are registered by @laravilt/forms.)
     */
    register(): void {
        // Intentionally empty
    },
};
