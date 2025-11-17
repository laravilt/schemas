<x-laravilt-component name="split" :data="$component->toLaraviltProps()">
    <div class="grid md:grid-cols-12 gap-6" :dir="rtl ? 'rtl' : 'ltr'">
        <div :class="startColumnSpan">
            <component
                v-for="(child, index) in startSchema"
                :key="index"
                :is="child.component || 'div'"
                v-bind="child"
            />
        </div>
        <div :class="endColumnSpan">
            <component
                v-for="(child, index) in endSchema"
                :key="index"
                :is="child.component || 'div'"
                v-bind="child"
            />
        </div>
    </div>
</x-laravilt-component>

<script>
export default {
    props: {
        startSchema: Array,
        endSchema: Array,
        startColumnSpan: [String, Number],
        endColumnSpan: [String, Number],
        rtl: Boolean,
        theme: String
    }
}
</script>
