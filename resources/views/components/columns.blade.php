<x-laravilt-component name="columns" :data="$component->toLaraviltProps()">
    <div :class="columnSpanClasses" :dir="rtl ? 'rtl' : 'ltr'">
        <component
            v-for="(child, index) in schema"
            :key="index"
            :is="child.component || 'div'"
            v-bind="child"
        />
    </div>
</x-laravilt-component>

<script>
export default {
    props: {
        columnSpan: [Number, Object],
        schema: Array,
        rtl: Boolean,
        theme: String
    },
    computed: {
        columnSpanClasses() {
            if (typeof this.columnSpan === 'number') {
                return `col-span-${this.columnSpan}`;
            }

            // Responsive column span
            const classes = [];
            if (this.columnSpan.default) classes.push(`col-span-${this.columnSpan.default}`);
            if (this.columnSpan.sm) classes.push(`sm:col-span-${this.columnSpan.sm}`);
            if (this.columnSpan.md) classes.push(`md:col-span-${this.columnSpan.md}`);
            if (this.columnSpan.lg) classes.push(`lg:col-span-${this.columnSpan.lg}`);

            return classes.join(' ');
        }
    }
}
</script>
