<x-laravilt-component name="step" :data="$component->toLaraviltProps()">
    <div class="step" :dir="rtl ? 'rtl' : 'ltr'">
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
        label: String,
        description: String,
        icon: String,
        schema: Array,
        rtl: Boolean,
        theme: String
    }
}
</script>
