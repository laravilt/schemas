<x-laravilt-component name="fieldset" :data="$component->toLaraviltProps()">
    <fieldset :dir="rtl ? 'rtl' : 'ltr'">
        <legend v-if="label" v-text="label" class="text-sm font-medium text-gray-900 dark:text-gray-100"></legend>
        <div class="mt-2 space-y-4">
            <component
                v-for="(child, index) in schema"
                :key="index"
                :is="child.component || 'div'"
                v-bind="child"
            />
        </div>
    </fieldset>
</x-laravilt-component>

<script>
export default {
    props: {
        label: String,
        schema: Array,
        rtl: Boolean,
        theme: String
    }
}
</script>
