<x-laravilt-component name="tab" :data="$component->toLaraviltProps()">
    <div class="tab" :dir="rtl ? 'rtl' : 'ltr'">
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
        icon: String,
        badge: String,
        schema: Array,
        rtl: Boolean,
        theme: String
    }
}
</script>
