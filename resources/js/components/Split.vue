<template>
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
</template>

<script setup>
const props = defineProps({
    startSchema: {
        type: Array,
        default: () => []
    },
    endSchema: {
        type: Array,
        default: () => []
    },
    startColumnSpan: {
        type: [String, Number],
        default: 'md:col-span-6'
    },
    endColumnSpan: {
        type: [String, Number],
        default: 'md:col-span-6'
    },
    rtl: {
        type: Boolean,
        default: false
    },
    theme: {
        type: String,
        default: 'light'
    }
});
</script>
