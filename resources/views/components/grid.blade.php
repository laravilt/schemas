<x-laravilt-component name="grid" :data="$component->toLaraviltProps()">
    <div
        class="grid gap-4"
        :class="gridClasses"
        :dir="rtl ? 'rtl' : 'ltr'"
    >
        <div
            v-for="(child, index) in schema"
            :key="index"
            :class="getColumnSpanClass(child)"
        >
            <component
                :is="child.component || 'div'"
                v-bind="child"
            />
        </div>
    </div>
</x-laravilt-component>

<script>
export default {
    props: {
        columns: [Number, Object],
        schema: Array,
        rtl: Boolean,
        theme: String
    },
    computed: {
        gridClasses() {
            if (typeof this.columns === 'number') {
                return `grid-cols-${this.columns}`;
            }

            // Responsive grid
            const classes = [];
            if (this.columns.default) classes.push(`grid-cols-${this.columns.default}`);
            if (this.columns.sm) classes.push(`sm:grid-cols-${this.columns.sm}`);
            if (this.columns.md) classes.push(`md:grid-cols-${this.columns.md}`);
            if (this.columns.lg) classes.push(`lg:grid-cols-${this.columns.lg}`);
            if (this.columns.xl) classes.push(`xl:grid-cols-${this.columns.xl}`);

            return classes.join(' ');
        }
    },
    methods: {
        getColumnSpanClass(child) {
            if (!child.columnSpan) return '';

            if (typeof child.columnSpan === 'number') {
                return `col-span-${child.columnSpan}`;
            }

            // Responsive column span
            const classes = [];
            if (child.columnSpan.default) classes.push(`col-span-${child.columnSpan.default}`);
            if (child.columnSpan.sm) classes.push(`sm:col-span-${child.columnSpan.sm}`);
            if (child.columnSpan.md) classes.push(`md:col-span-${child.columnSpan.md}`);
            if (child.columnSpan.lg) classes.push(`lg:col-span-${child.columnSpan.lg}`);

            return classes.join(' ');
        }
    }
}
</script>
